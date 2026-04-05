<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function index()
    {
        $metricas = [
            'ventas' => DB::table('ventas')->whereNull('deleted_at')->count(),
            'compras' => DB::table('compras')->whereNull('deleted_at')->count(),
            'inventario_total' => DB::table('ediciones')->whereNull('deleted_at')->count(),
            'inventario_bajo' => DB::table('ediciones')
                ->whereNull('deleted_at')
                ->whereRaw('existencias <= stock_minimo')
                ->count(),
            'mermas' => DB::table('mermas')->whereNull('deleted_at')->count(),
        ];

        return view('reportes.index', compact('metricas'));
    }

    public function ventas(Request $request)
    {
        $query = DB::table('ventas as v')
            ->leftJoin('usuarios as u', 'u.id', '=', 'v.usuario_id')
            ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
            ->select([
                'v.id',
                'v.folio',
                'v.usuario_id',
                'v.fecha',
                'v.total',
                'u.correo',
                'p.nombre',
                'p.apellido_paterno',
                'p.apellido_materno',
            ])
            ->whereNull('v.deleted_at');

        if ($request->filled('fecha_inicial')) {
            $query->whereRaw("TRUNC(v.fecha) >= TO_DATE(?, 'YYYY-MM-DD')", [$request->string('fecha_inicial')->toString()]);
        }

        if ($request->filled('fecha_final')) {
            $query->whereRaw("TRUNC(v.fecha) <= TO_DATE(?, 'YYYY-MM-DD')", [$request->string('fecha_final')->toString()]);
        }

        if ($request->filled('usuario_id')) {
            $query->where('v.usuario_id', $request->integer('usuario_id'));
        }

        if ($request->filled('folio')) {
            $folio = strtoupper(trim($request->string('folio')->toString()));
            $query->whereRaw('UPPER(v.folio) LIKE ?', ["%{$folio}%"]);
        }

        $query->orderByDesc('v.fecha')->orderByDesc('v.id');

        if ($request->get('export') === 'csv') {
            $registros = $query->get()->map(function ($venta) {
                return [
                    'Folio' => $venta->folio,
                    'Fecha' => $this->formatDate($venta->fecha),
                    'Usuario' => $this->buildFullName($venta, $venta->correo),
                    'Total' => number_format((float) $venta->total, 2, '.', ''),
                ];
            });

            return $this->exportCsv('reporte_ventas.csv', array_keys($registros->first() ?? [
                'Folio' => '',
                'Fecha' => '',
                'Usuario' => '',
                'Total' => '',
            ]), $registros->toArray());
        }

        $ventas = $query->paginate(10)->withQueryString();
        $ventas->setCollection(
            $ventas->getCollection()->map(function ($venta) {
                $venta->usuario_nombre = $this->buildFullName($venta, $venta->correo);
                return $venta;
            })
        );

        $usuarios = DB::table('usuarios as u')
            ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
            ->select([
                'u.id',
                'u.correo',
                'p.nombre',
                'p.apellido_paterno',
                'p.apellido_materno',
            ])
            ->whereNull('u.deleted_at')
            ->orderBy('p.nombre')
            ->orderBy('u.correo')
            ->get()
            ->map(function ($usuario) {
                $usuario->nombre_completo = $this->buildFullName($usuario, $usuario->correo);
                return $usuario;
            });

        return view('reportes.ventas', [
            'ventas' => $ventas,
            'usuarios' => $usuarios,
            'filtros' => $request->only(['fecha_inicial', 'fecha_final', 'usuario_id', 'folio']),
        ]);
    }

    public function compras(Request $request)
    {
        $query = DB::table('compras as c')
            ->leftJoin('proveedores as pr', 'pr.id', '=', 'c.proveedor_id')
            ->select([
                'c.id',
                'c.folio_factura',
                'c.fecha_compra',
                'c.total_compra',
                'c.estado',
                'c.proveedor_id',
                'pr.nombre as proveedor_nombre',
            ])
            ->whereNull('c.deleted_at');

        if ($request->filled('fecha_inicial')) {
            $query->whereRaw("TRUNC(c.fecha_compra) >= TO_DATE(?, 'YYYY-MM-DD')", [$request->string('fecha_inicial')->toString()]);
        }

        if ($request->filled('fecha_final')) {
            $query->whereRaw("TRUNC(c.fecha_compra) <= TO_DATE(?, 'YYYY-MM-DD')", [$request->string('fecha_final')->toString()]);
        }

        if ($request->filled('proveedor_id')) {
            $query->where('c.proveedor_id', $request->integer('proveedor_id'));
        }

        if ($request->filled('folio_factura')) {
            $folioFactura = strtoupper(trim($request->string('folio_factura')->toString()));
            $query->whereRaw('UPPER(c.folio_factura) LIKE ?', ["%{$folioFactura}%"]);
        }

        $query->orderByDesc('c.fecha_compra')->orderByDesc('c.id');

        if ($request->get('export') === 'csv') {
            $registros = $query->get()->map(function ($compra) {
                return [
                    'Factura' => $compra->folio_factura,
                    'Fecha' => $this->formatDate($compra->fecha_compra, false),
                    'Proveedor' => $compra->proveedor_nombre,
                    'Estado' => $compra->estado,
                    'Total' => number_format((float) $compra->total_compra, 2, '.', ''),
                ];
            });

            return $this->exportCsv('reporte_compras.csv', array_keys($registros->first() ?? [
                'Factura' => '',
                'Fecha' => '',
                'Proveedor' => '',
                'Estado' => '',
                'Total' => '',
            ]), $registros->toArray());
        }

        $compras = $query->paginate(10)->withQueryString();

        $proveedores = DB::table('proveedores')
            ->select('id', 'nombre')
            ->whereNull('deleted_at')
            ->orderBy('nombre')
            ->get();

        return view('reportes.compras', [
            'compras' => $compras,
            'proveedores' => $proveedores,
            'filtros' => $request->only(['fecha_inicial', 'fecha_final', 'proveedor_id', 'folio_factura']),
        ]);
    }

    public function inventario(Request $request)
    {
        $query = DB::table('ediciones as e')
            ->join('libros as l', 'l.id', '=', 'e.libro_id')
            ->select([
                'e.id',
                'l.titulo',
                'e.isbn',
                'e.anio_publicacion',
                'e.existencias',
                'e.stock_minimo',
            ])
            ->whereNull('e.deleted_at')
            ->whereNull('l.deleted_at');

        if ($request->filled('titulo')) {
            $titulo = strtoupper(trim($request->string('titulo')->toString()));
            $query->whereRaw('UPPER(l.titulo) LIKE ?', ["%{$titulo}%"]);
        }

        if ($request->filled('isbn')) {
            $isbn = strtoupper(trim($request->string('isbn')->toString()));
            $query->whereRaw('UPPER(e.isbn) LIKE ?', ["%{$isbn}%"]);
        }

        if ($request->filled('anio')) {
            $query->where('e.anio_publicacion', $request->integer('anio'));
        }

        if ($request->filled('estado')) {
            $estado = $request->string('estado')->toString();
            if ($estado === 'sin_stock') {
                $query->where('e.existencias', 0);
            } elseif ($estado === 'stock_minimo') {
                $query->whereRaw('e.existencias > 0 AND e.existencias <= e.stock_minimo');
            } elseif ($estado === 'disponible') {
                $query->whereRaw('e.existencias > e.stock_minimo');
            }
        }

        $query->orderBy('l.titulo');

        if ($request->get('export') === 'csv') {
            $registros = $query->get()->map(function ($item) {
                return [
                    'Libro' => $item->titulo,
                    'ISBN' => $item->isbn,
                    'Año' => $item->anio_publicacion,
                    'Existencias' => $item->existencias,
                    'Stock mínimo' => $item->stock_minimo,
                    'Estado' => $this->resolveInventarioEstado($item->existencias, $item->stock_minimo),
                ];
            });

            return $this->exportCsv('reporte_inventario.csv', array_keys($registros->first() ?? [
                'Libro' => '',
                'ISBN' => '',
                'Año' => '',
                'Existencias' => '',
                'Stock mínimo' => '',
                'Estado' => '',
            ]), $registros->toArray());
        }

        $inventario = $query->paginate(10)->withQueryString();
        $inventario->setCollection(
            $inventario->getCollection()->map(function ($item) {
                $item->estado_inventario = $this->resolveInventarioEstado($item->existencias, $item->stock_minimo);
                return $item;
            })
        );

        $anios = DB::table('ediciones')
            ->whereNull('deleted_at')
            ->whereNotNull('anio_publicacion')
            ->distinct()
            ->orderByDesc('anio_publicacion')
            ->pluck('anio_publicacion');

        return view('reportes.inventario', [
            'inventario' => $inventario,
            'anios' => $anios,
            'filtros' => $request->only(['titulo', 'isbn', 'estado', 'anio']),
        ]);
    }

    public function mermas(Request $request)
    {
        $query = DB::table('mermas as m')
            ->join('lotes as lo', 'lo.id', '=', 'm.lote_id')
            ->join('ediciones as e', 'e.id', '=', 'lo.edicion_id')
            ->join('libros as l', 'l.id', '=', 'e.libro_id')
            ->select([
                'm.id',
                'm.fecha_reporte',
                'm.tipo_merma',
                'm.cantidad',
                'm.estatus',
                'l.titulo',
            ])
            ->whereNull('m.deleted_at')
            ->whereNull('lo.deleted_at')
            ->whereNull('e.deleted_at')
            ->whereNull('l.deleted_at');

        if ($request->filled('fecha_inicial')) {
            $query->whereRaw("TRUNC(m.fecha_reporte) >= TO_DATE(?, 'YYYY-MM-DD')", [$request->string('fecha_inicial')->toString()]);
        }

        if ($request->filled('fecha_final')) {
            $query->whereRaw("TRUNC(m.fecha_reporte) <= TO_DATE(?, 'YYYY-MM-DD')", [$request->string('fecha_final')->toString()]);
        }

        if ($request->filled('tipo_merma')) {
            $query->where('m.tipo_merma', $request->string('tipo_merma')->toString());
        }

        if ($request->filled('titulo')) {
            $titulo = strtoupper(trim($request->string('titulo')->toString()));
            $query->whereRaw('UPPER(l.titulo) LIKE ?', ["%{$titulo}%"]);
        }

        $query->orderByDesc('m.fecha_reporte')->orderByDesc('m.id');

        if ($request->get('export') === 'csv') {
            $registros = $query->get()->map(function ($merma) {
                return [
                    'Fecha' => $this->formatDate($merma->fecha_reporte),
                    'Libro' => $merma->titulo,
                    'Tipo de merma' => $merma->tipo_merma,
                    'Cantidad' => $merma->cantidad,
                    'Estatus' => $merma->estatus,
                ];
            });

            return $this->exportCsv('reporte_mermas.csv', array_keys($registros->first() ?? [
                'Fecha' => '',
                'Libro' => '',
                'Tipo de merma' => '',
                'Cantidad' => '',
                'Estatus' => '',
            ]), $registros->toArray());
        }

        $mermas = $query->paginate(10)->withQueryString();

        return view('reportes.mermas', [
            'mermas' => $mermas,
            'tiposMerma' => [
                'Portada dañada',
                'Hojas rasgadas',
                'Hojas arrugadas',
                'Faltan hojas',
            ],
            'filtros' => $request->only(['fecha_inicial', 'fecha_final', 'tipo_merma', 'titulo']),
        ]);
    }

    private function buildFullName(object $registro, ?string $fallback = null): string
    {
        $partes = array_filter([
            $registro->nombre ?? null,
            $registro->apellido_paterno ?? null,
            $registro->apellido_materno ?? null,
        ], fn($valor) => filled($valor));

        if (! empty($partes)) {
            return implode(' ', $partes);
        }

        return $fallback ?: 'Sin referencia';
    }

    private function resolveInventarioEstado(int|float $existencias, int|float $stockMinimo): string
    {
        if ((int) $existencias === 0) {
            return 'Sin stock';
        }

        if ((int) $existencias <= (int) $stockMinimo) {
            return 'Stock mínimo';
        }

        return 'Disponible';
    }

    private function formatDate(mixed $value, bool $includeTime = true): string
    {
        if (empty($value)) {
            return '';
        }

        $timestamp = strtotime((string) $value);

        if ($timestamp === false) {
            return (string) $value;
        }

        return $includeTime ? date('d/m/Y H:i', $timestamp) : date('d/m/Y', $timestamp);
    }

    private function exportCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($stream, $headers);

            foreach ($rows as $row) {
                fputcsv($stream, $row);
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

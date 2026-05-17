<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Edicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;


class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with(['proveedor', 'usuario'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        $proveedores = Proveedor::where('estado', 'Activo')->get();

        $ediciones = Edicion::with('libro')->get();

        return view('compras.index', compact('compras', 'proveedores', 'ediciones'));
    }
    public function create()
    {
        $proveedores = Proveedor::where('estado', 'Activo')->get();

        $ediciones = Edicion::with('libro')->get();

        return view('compras.create', compact('proveedores', 'ediciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'folio_factura' => 'required|string|max:50',
            'fecha_compra' => 'required|date',
            'items_compra' => 'required|json'
        ]);

        $datosCarrito = json_decode($request->items_compra, true);

        if (empty($datosCarrito)) {
            return back()->with('error', 'Debe añadir al menos un producto a la compra.')->withInput();
        }

        try {
            DB::beginTransaction();

            $totalCalculado = 0;
            foreach ($datosCarrito as $item) {
                $totalCalculado += $item['cantidad'] * $item['precio_costo'];
            }

            $compra = Compra::create([
                'proveedor_id'  => $request->proveedor_id,
                'folio_factura' => strtoupper($request->folio_factura),
                'fecha_compra'  => $request->fecha_compra,
                'total_compra'  => $totalCalculado,
                'usuario_id'    => auth()->id() ?? 1,
                'estado'        => 'Recibida'
            ]);

            foreach ($datosCarrito as $item) {
                DetalleCompra::create([
                    'compra_id'  => $compra->id,
                    'edicion_id' => $item['edicion_id'],
                    'cantidad'   => $item['cantidad'],
                    'subtotal'   => $item['cantidad'] * $item['precio_costo']
                ]);
            }

            DB::commit();

            return redirect()->route('compras.index')
                ->with('success', 'Compra #' . $compra->folio_factura . ' registrada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la compra: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'usuario', 'detalles.edicion.libro'])
            ->findOrFail($id);

        return view('compras.show', compact('compra'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'folio_factura' => 'required|string|max:50',
            'estado' => 'required|in:Recibida,Pendiente,Cancelada'
        ]);

        $compra = Compra::findOrFail($id);
        $compra->update($request->only('folio_factura', 'estado'));

        return redirect()->route('compras.index')->with('success', 'Datos de la compra actualizados.');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $compra = Compra::findOrFail($id);

            if ($compra->lotes()->count() > 0) {
                return back()->with('error', 'No se puede eliminar la compra porque ya tiene lotes de inventario asociados.');
            }

            $compra->detalles()->delete();
            $compra->delete();

            DB::commit();
            return redirect()->route('compras.index')->with('success', 'Compra eliminada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la compra: ' . $e->getMessage());
        }
    }

    public function generarPDF($id)
    {
        // ── Carga completa con relaciones ────────────────────────────────────
        $compra = Compra::with([
            'proveedor',
            'usuario.persona',
            'detalles.edicion.libro',
        ])->find($id);

        if (!$compra) {
            return redirect()->back()->with('error', 'No se pudo generar el PDF. La compra solicitada no existe.');
        }

        // ── Nombre del usuario que registró la compra ───────────────────────
        $persona      = $compra->usuario->persona ?? null;
        $registradoPor = $persona
            ? trim($persona->nombre . ' ' . $persona->apellido_paterno)
            : ($compra->usuario->correo ?? 'N/A');

        // ── Estadísticas de la compra ───────────────────────────────────
        $totalItems   = $compra->detalles->sum('cantidad');
        $totalTitulos = $compra->detalles->count();

        $pdf = Pdf::loadView('pdf.compras.individual', [
            'titulo'        => 'Orden de Compra #' . $compra->folio_factura,
            'compra'        => $compra,
            'registradoPor' => $registradoPor,
            'totalItems'    => $totalItems,
            'totalTitulos'  => $totalTitulos,
            'generadoPor'   => auth()->user()->correo ?? 'Sistema',
        ])->setPaper('letter', 'portrait');

        return $pdf->download('compra_' . $compra->folio_factura . '.pdf');
    }

    public function reporteGeneral(Request $request)
    {
        // ── Validaciones de servidor (sin depender del navegador) ─────────
        $request->validate([
            'fecha' => 'nullable|date',
            'mes'   => 'nullable|integer|between:1,12',
            'anio'  => 'nullable|integer|min:2000|max:2100'
        ], [
            'fecha.date'    => 'El formato de fecha ingresado no es válido.',
            'mes.between'   => 'El mes debe estar entre 1 y 12.',
            'anio.min'      => 'El año debe ser mayor a 2000.',
            'anio.max'      => 'El año debe ser menor a 2100.',
            'anio.integer'  => 'El año debe ser un número válido.'
        ]);

        // ── Query base con relaciones ─────────────────────────────────────
        $query = Compra::with(['proveedor', 'usuario.persona']);

        // ── Filtros Oracle-compatibles ────────────────────────────────────
        if ($request->filled('fecha')) {
            $query->whereRaw("TRUNC(fecha_compra) = TO_DATE(?, 'YYYY-MM-DD')", [$request->fecha]);
        }

        if ($request->filled('mes')) {
            $query->whereRaw('EXTRACT(MONTH FROM fecha_compra) = ?', [$request->mes]);
        }

        if ($request->filled('anio')) {
            $query->whereRaw('EXTRACT(YEAR FROM fecha_compra) = ?', [$request->anio]);
        }

        $compras = $query->orderBy('fecha_compra', 'desc')->get();

        if ($compras->isEmpty()) {
            return redirect()->back()->with('error', 'No hay compras registradas con esos filtros.');
        }

        // ── Estadísticas ──────────────────────────────────────────────────
        $totalInversion   = $compras->sum('total_compra');
        $cantidadCompras  = $compras->count();
        $promedioCompra   = $compras->avg('total_compra');
        $maxCompra        = $compras->max('total_compra');
        $minCompra        = $compras->min('total_compra');

        // ── Datos para la tabla ───────────────────────────────────────────
        $datos = $compras->map(function ($c) {
            return [
                'folio'      => $c->folio_factura,
                'proveedor'  => $c->proveedor->nombre ?? 'N/A',
                'estado'     => $c->estado,
                'total'      => '$' . number_format($c->total_compra, 2),
                'fecha'      => $c->fecha_compra
                    ? \Carbon\Carbon::parse($c->fecha_compra)->format('d/m/Y')
                    : '—',
            ];
        });

        // ── Datos gráfica: compras por mes ────────────────────────────────
        $mesesTexto = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
        ];

        $comprasPorMes = Compra::selectRaw(
            'EXTRACT(MONTH FROM fecha_compra) as mes, SUM(total_compra) as total'
        )
            ->groupByRaw('EXTRACT(MONTH FROM fecha_compra)')
            ->orderByRaw('EXTRACT(MONTH FROM fecha_compra)')
            ->get();

        $labels  = $comprasPorMes->map(fn($i) => $mesesTexto[(int)$i->mes] ?? "Mes {$i->mes}")->toArray();
        $valores = $comprasPorMes->map(fn($i) => round((float)$i->total, 2))->toArray();

        // ── QuickChart → Base64 ───────────────────────────────────────────
        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels'   => $labels,
                'datasets' => [[
                    'label'           => 'Compras por Mes ($)',
                    'data'            => $valores,
                    'backgroundColor' => 'rgba(75, 28, 113, 0.75)',
                    'borderColor'     => '#4b1c71',
                    'borderWidth'     => 1,
                ]],
            ],
            'options' => [
                'scales' => ['y' => ['beginAtZero' => true]],
            ],
        ];

        $chartUrl     = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig));
        $chartBase64  = 'data:image/png;base64,' . base64_encode(file_get_contents($chartUrl));

        // ── Título dinámico ───────────────────────────────────────────────
        $titulo = 'Reporte General de Compras';
        if ($request->filled('fecha')) $titulo .= ' — ' . date('d/m/Y', strtotime($request->fecha));
        if ($request->filled('mes'))   $titulo .= ' — Mes ' . $request->mes;
        if ($request->filled('anio'))  $titulo .= ' — ' . $request->anio;

        $pdf = Pdf::loadView('pdf.compras.reporte_general', [
            'titulo'          => $titulo,
            'datos'           => $datos,
            'totalInversion'  => $totalInversion,
            'cantidadCompras' => $cantidadCompras,
            'promedioCompra'  => $promedioCompra,
            'maxCompra'       => $maxCompra,
            'minCompra'       => $minCompra,
            'chartBase64'     => $chartBase64,
            'generadoPor'     => auth()->user()->correo ?? 'Sistema',
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte_general_compras.pdf');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Lote;
use App\Models\Edicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('usuario.persona')->orderBy('id', 'desc')->paginate(10);
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        return view('ventas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'datos_venta' => 'required|json'
        ]);

        $datos = json_decode($request->datos_venta, true);

        if (!$datos || empty($datos['carrito'])) {
            return back()->with('error', 'El carrito de ventas está vacío.');
        }

        $folio = 'VNT-' . strtoupper(uniqid());
        $fecha = Carbon::now()->format('Y-m-d H:i:s');
        $usuarioId = auth()->id() ?? 1;

        try {
            DB::beginTransaction();

            $venta = Venta::create([
                'folio' => $folio,
                'usuario_id' => $usuarioId,
                'fecha' => $fecha,
                'total' => $datos['total'],
                'monto_recibido' => $datos['monto_recibido'],
                'cambio' => $datos['cambio']
            ]);

            foreach ($datos['carrito'] as $item) {
                $edicionId = $item['edicion_id'];
                $cantidadRequerida = $item['cantidad'];
                $precioUnitario = $item['precio_unitario'];

                // --- SOLUCIÓN PARA ORACLE ORA-00933 ---
                // Obtenemos los lotes con el bloqueo, pero SIN el orderBy en SQL
                // Luego usamos ->sortBy() para ordenarlos en PHP (PEPS)
                $lotesDisponibles = Lote::where('edicion_id', $edicionId)
                    ->where('cantidad', '>', 0)
                    ->lockForUpdate()
                    ->get()
                    ->sortBy('fecha_entrada'); // Orden PEPS en la colección de Laravel

                $cantidadFaltante = $cantidadRequerida;

                foreach ($lotesDisponibles as $lote) {
                    if ($cantidadFaltante <= 0) {
                        break;
                    }

                    $cantidadATomar = min($lote->cantidad, $cantidadFaltante);
                    $subtotalParcial = $cantidadATomar * $precioUnitario;

                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'lote_id' => $lote->id,
                        'cantidad' => $cantidadATomar,
                        'subtotal' => $subtotalParcial
                    ]);

                    // Actualizar el lote
                    $lote->cantidad -= $cantidadATomar;
                    $lote->save();

                    $cantidadFaltante -= $cantidadATomar;
                }

                if ($cantidadFaltante > 0) {
                    throw new Exception("Stock insuficiente en lotes para la edición ID: {$edicionId}.");
                }

                // Actualizar inventario global
                $edicion = Edicion::findOrFail($edicionId);
                $edicion->existencias -= $cantidadRequerida;
                $edicion->save();
            }

            DB::commit();

            // Redirigimos al index con éxito
            return redirect()->route('ventas.index')->with('success', 'Venta registrada correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            // Si quieres ver el error real si vuelve a fallar algo, usa: dd($e->getMessage());
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $venta = Venta::with('usuario', 'detallesVentas.lote.edicion.libro')->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }

    public function edit(string $id)
    {
        $venta = Venta::findOrFail($id);
        return view('ventas.edit', compact('venta'));
    }

    public function update(Request $request, string $id)
    {
        $venta = Venta::findOrFail($id);

        $request->validate([
            'folio' => 'required|max:50|unique:ventas,folio,' . $venta->id,
            'usuario_id' => 'required',
            'fecha' => 'required',
            'total' => 'required|numeric',
            'monto_recibido' => 'nullable|numeric',
            'cambio' => 'nullable|numeric'
        ]);

        $data = $request->all();
        $data['fecha'] = Carbon::parse($request->fecha)->format('Y-m-d H:i:s');

        $venta->update($data);

        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente');
    }

    public function destroy(string $id)
    {
        $venta = Venta::findOrFail($id);
        $venta->delete();

        return redirect()->route('ventas.index')->with('success', 'Venta eliminada correctamente');
    }

    public function buscarLibro(Request $request)
    {
        $termino = strtolower($request->get('q'));

        if (!$termino) {
            return response()->json([]);
        }

        $ediciones = Edicion::with('libro')
            ->where('existencias', '>', 0)
            ->where(function ($query) use ($termino) {
                $query->whereRaw('LOWER(isbn) LIKE ?', ["%{$termino}%"])
                    ->orWhereHas('libro', function ($q) use ($termino) {
                        $q->whereRaw('LOWER(titulo) LIKE ?', ["%{$termino}%"]);
                    });
            })
            ->take(10)
            ->get();

        $resultados = $ediciones->map(function ($edicion) {
            return [
                'edicion_id' => $edicion->id,
                'isbn' => $edicion->isbn,
                'titulo' => $edicion->libro->titulo,
                'precio_venta' => $edicion->precio_venta,
                'existencias' => $edicion->existencias
            ];
        });

        return response()->json($resultados);
    }

    public function generarPDF($id)
    {
        // ── Carga completa con relaciones reales ──────────────────────────
        $venta = Venta::with([
            'usuario.persona',
            'detallesVentas.lote.edicion.libro',
        ])->findOrFail($id);

        // ── Nombre del usuario que realizó la venta ───────────────────────
        $persona   = $venta->usuario->persona ?? null;
        $vendedor  = $persona
            ? trim($persona->nombre . ' ' . $persona->apellido_paterno)
            : ($venta->usuario->correo ?? 'N/A');

        // ── Estadísticas de la venta ──────────────────────────────────────
        $totalItems = $venta->detallesVentas->sum('cantidad');
        $totalTitulos = $venta->detallesVentas->count();

        // ── Usuario autenticado que genera el PDF ─────────────────────────
        $generadoPor = auth()->user()->correo ?? 'Sistema';

        $pdf = Pdf::loadView('pdf.ventas.individual', [
            'titulo'       => 'Comprobante de Venta #' . $venta->folio,
            'venta'        => $venta,
            'vendedor'     => $vendedor,
            'totalItems'   => $totalItems,
            'totalTitulos' => $totalTitulos,
            'generadoPor'  => $generadoPor,
        ])->setPaper('letter', 'portrait');

        return $pdf->download('venta_' . $venta->folio . '.pdf');
    }

    public function reporteGeneral(Request $request)
    {
        // ── Query base con relaciones ─────────────────────────────────────
        $query = Venta::with('usuario.persona');

        // ── Filtros Oracle-compatibles ────────────────────────────────────
        if ($request->filled('fecha')) {
            // TRUNC elimina la parte de hora para comparar solo la fecha
            $query->whereRaw("TRUNC(fecha) = TO_DATE(?, 'YYYY-MM-DD')", [$request->fecha]);
        }

        if ($request->filled('mes')) {
            $query->whereRaw('EXTRACT(MONTH FROM fecha) = ?', [$request->mes]);
        }

        if ($request->filled('anio')) {
            $query->whereRaw('EXTRACT(YEAR FROM fecha) = ?', [$request->anio]);
        }

        $ventas = $query->orderBy('fecha', 'desc')->get();

        if ($ventas->isEmpty()) {
            return redirect()->back()->with('error', 'No hay ventas registradas con esos filtros.');
        }

        // ── Estadísticas ──────────────────────────────────────────────────
        $totalVentas    = $ventas->sum('total');
        $cantidadVentas = $ventas->count();
        $promedioVentas = $ventas->avg('total');
        $maxVenta       = $ventas->max('total');
        $minVenta       = $ventas->min('total');

        // ── Datos para la tabla ───────────────────────────────────────────
        $datos = $ventas->map(function ($v) {
            $persona  = $v->usuario->persona ?? null;
            $vendedor = $persona
                ? trim($persona->nombre . ' ' . $persona->apellido_paterno)
                : ($v->usuario->correo ?? 'N/A');

            return [
                'folio'    => $v->folio,
                'vendedor' => $vendedor,
                'total'    => '$' . number_format($v->total, 2),
                'recibido' => '$' . number_format($v->monto_recibido ?? 0, 2),
                'cambio'   => '$' . number_format($v->cambio ?? 0, 2),
                'fecha'    => $v->fecha
                    ? \Carbon\Carbon::parse($v->fecha)->format('d/m/Y')
                    : '—',
            ];
        });

        // ── Datos gráfica: ventas por mes (año completo) ──────────────────
        $mesesTexto = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
        ];

        $ventasPorMes = Venta::selectRaw(
            'EXTRACT(MONTH FROM fecha) as mes, SUM(total) as total'
        )
            ->groupByRaw('EXTRACT(MONTH FROM fecha)')
            ->orderByRaw('EXTRACT(MONTH FROM fecha)')
            ->get();

        $labels  = $ventasPorMes->map(fn($i) => $mesesTexto[(int)$i->mes] ?? "Mes {$i->mes}")->toArray();
        $valores = $ventasPorMes->map(fn($i) => round((float)$i->total, 2))->toArray();

        // ── QuickChart → Base64 ───────────────────────────────────────────
        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels'   => $labels,
                'datasets' => [[
                    'label'           => 'Ventas por Mes ($)',
                    'data'            => $valores,
                    'backgroundColor' => 'rgba(75, 28, 113, 0.75)',
                    'borderColor'     => '#4b1c71',
                    'borderWidth'     => 1,
                ]],
            ],
            'options' => [
                'plugins' => ['legend' => ['display' => true]],
                'scales'  => ['y' => ['beginAtZero' => true]],
            ],
        ];

        $chartUrl      = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig));
        $imageContent  = file_get_contents($chartUrl);
        $chartBase64   = 'data:image/png;base64,' . base64_encode($imageContent);

        // ── Título dinámico ───────────────────────────────────────────────
        $titulo = 'Reporte General de Ventas';
        if ($request->filled('fecha'))  $titulo .= ' — ' . date('d/m/Y', strtotime($request->fecha));
        if ($request->filled('mes'))    $titulo .= ' — Mes ' . $request->mes;
        if ($request->filled('anio'))   $titulo .= ' — ' . $request->anio;

        $pdf = Pdf::loadView('pdf.ventas.reporte_general', [
            'titulo'         => $titulo,
            'datos'          => $datos,
            'totalVentas'    => $totalVentas,
            'cantidadVentas' => $cantidadVentas,
            'promedioVentas' => $promedioVentas,
            'maxVenta'       => $maxVenta,
            'minVenta'       => $minVenta,
            'chartBase64'    => $chartBase64,
            'generadoPor'    => auth()->user()->correo ?? 'Sistema',
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte_general_ventas.pdf');
    }
}

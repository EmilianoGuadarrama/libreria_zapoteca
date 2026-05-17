<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Merma;
use App\Models\Lote;

class MermaController extends Controller
{
    public function index()
    {
        $mermas = Merma::with([
                'lote.edicion.libro',
                'lote.edicion.editorial',
                'lote.compra.proveedor',
                'usuario.persona',
            ])
            ->orderBy('id', 'desc')
            ->get();

        // ── Resumen financiero (calculado desde accessors) ────────────
        $totalRecuperado = $mermas->sum(fn($m) => $m->monto_recuperado);
        $totalPerdido    = $mermas->sum(fn($m) => $m->monto_perdido);
        $totalMermas     = $mermas->sum(fn($m) => $m->total_merma);
        $balanceNeto     = $totalRecuperado - $totalPerdido;

        $usuariosCatalogo = DB::table('usuarios')
            ->select('id', 'correo')
            ->whereNull('deleted_at')
            ->orderBy('correo', 'asc')
            ->get();

        $lotesDisponibles = Lote::with(['edicion.libro', 'compra.proveedor'])
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->get();

        return view('mermas.index', compact(
            'mermas', 'usuariosCatalogo', 'lotesDisponibles',
            'totalRecuperado', 'totalPerdido', 'totalMermas', 'balanceNeto'
        ));
    }

    public function create()
    {
        return redirect()->route('mermas.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|integer|exists:lotes,id',
            'tipo_merma' => 'required|in:Portada dañada,Hojas rasgadas,Hojas arrugadas,Faltan hojas',
            'fecha_reporte' => 'nullable|date',
            'cantidad' => 'required|integer|min:1',
            'destino' => 'required|in:Devolucion_Proveedor,Destruccion',
            'estatus' => 'required|in:PENDIENTE,PROCESADO',
        ], [
            'tipo_merma.in' => 'El tipo de merma no es válido.',
            'destino.in' => 'El destino no es válido.',
            'estatus.in' => 'El estatus no es válido.',
        ]);

        $fechaReporte = $request->fecha_reporte 
            ? Carbon::parse($request->fecha_reporte)->format('Y-m-d H:i:s') 
            : now();

        $lote = Lote::with('edicion')->findOrFail($request->lote_id);
        $cantidad = abs((int) $request->cantidad);
        $usuarioId = auth()->id() ?? 1;

        $merma = Merma::create([
            'lote_id' => $lote->id,
            'tipo_merma' => $request->tipo_merma,
            'fecha_reporte' => $fechaReporte,
            'cantidad' => $cantidad,
            'usuario_id' => $usuarioId,
            'destino' => $request->destino,
            'estatus' => $request->estatus,
        ]);

        if ($merma->estatus === 'PROCESADO') {
            $lote->cantidad -= $cantidad;
            $lote->save();

            if ($lote->edicion) {
                $lote->edicion->existencias -= $cantidad;
                $lote->edicion->save();
            }
        }

        return redirect()->route('mermas.index')->with('status', 'Merma registrada correctamente.');
    }

    public function show(string $id)
    {
        return redirect()->route('mermas.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('mermas.index');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'lote_id' => 'required|integer|exists:lotes,id',
            'tipo_merma' => 'required|in:Portada dañada,Hojas rasgadas,Hojas arrugadas,Faltan hojas',
            'fecha_reporte' => 'required|date',
            'cantidad' => 'required|integer|min:1',
            'destino' => 'required|in:Devolucion_Proveedor,Destruccion',
            'estatus' => 'required|in:PENDIENTE,PROCESADO',
        ], [
            'tipo_merma.in' => 'El tipo de merma no es válido.',
            'destino.in' => 'El destino no es válido.',
            'estatus.in' => 'El estatus no es válido.',
        ]);

        $merma = Merma::findOrFail($id);
        $estatusAnterior = $merma->estatus;

        $fechaReporte = Carbon::parse($request->fecha_reporte)->format('Y-m-d H:i:s');
        $lote = Lote::with('edicion')->findOrFail($request->lote_id);
        $cantidad = abs((int) $request->cantidad);

        $merma->update([
            'lote_id' => $lote->id,
            'tipo_merma' => $request->tipo_merma,
            'fecha_reporte' => $fechaReporte,
            'cantidad' => $cantidad,
            'destino' => $request->destino,
            'estatus' => $request->estatus,
        ]);

        if ($estatusAnterior !== 'PROCESADO' && $merma->estatus === 'PROCESADO') {
            $lote->cantidad -= $cantidad;
            $lote->save();

            if ($lote->edicion) {
                $lote->edicion->existencias -= $cantidad;
                $lote->edicion->save();
            }
        }

        return redirect()->route('mermas.index')->with('status', 'Merma actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        DB::table('mermas')
            ->where('id', $id)
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('mermas.index')->with('status', 'Merma eliminada correctamente.');
    }


    public function generarPDF($id)
    {
        // ── Solo mermas PROCESADAS ───────────────────────────────────────────────
        $merma = Merma::with([
            'lote.edicion.libro',
            'lote.compra.proveedor',
            'usuario.persona',
        ])->findOrFail($id);

        if ($merma->estatus !== 'PROCESADO') {
            return redirect()->back()->with('error', 'Solo se pueden generar PDFs de mermas con estatus PROCESADO.');
        }

        // ── Nombre del usuario que reportó la merma ───────────────────────
        $persona       = $merma->usuario->persona ?? null;
        $reportadoPor  = $persona
            ? trim($persona->nombre . ' ' . $persona->apellido_paterno)
            : ($merma->usuario->correo ?? 'N/A');

        $pdf = Pdf::loadView('pdf.mermas.individual', [
            'titulo'       => 'Reporte de Merma #' . $merma->id,
            'merma'        => $merma,
            'reportadoPor' => $reportadoPor,
            'generadoPor'  => auth()->user()->correo ?? 'Sistema',
        ])->setPaper('letter', 'portrait');

        return $pdf->download('merma_' . $merma->id . '.pdf');
    }

    public function reporteGeneral(Request $request)
    {
        // ── Query base: solo mermas PROCESADAS ────────────────────────────
        $query = Merma::with(['lote.edicion.libro', 'lote.compra.proveedor', 'usuario.persona'])
            ->where('estatus', 'PROCESADO');

        // ── Filtros Oracle-compatibles sobre fecha_reporte ───────────────────
        if ($request->filled('fecha')) {
            $query->whereRaw("TRUNC(fecha_reporte) = TO_DATE(?, 'YYYY-MM-DD')", [$request->fecha]);
        }

        if ($request->filled('mes')) {
            $query->whereRaw('EXTRACT(MONTH FROM fecha_reporte) = ?', [$request->mes]);
        }

        if ($request->filled('anio')) {
            $query->whereRaw('EXTRACT(YEAR FROM fecha_reporte) = ?', [$request->anio]);
        }

        $mermas = $query->orderBy('fecha_reporte', 'desc')->get();

        if ($mermas->isEmpty()) {
            return redirect()->back()->with('error', 'No hay mermas procesadas con esos filtros.');
        }

        // ── Estadísticas generales ─────────────────────────────────────────
        $totalMermas    = $mermas->count();
        $totalUnidades  = $mermas->sum('cantidad');
        $promedioUnid   = $mermas->avg('cantidad');
        $maxUnidades    = $mermas->max('cantidad');
        $minUnidades    = $mermas->min('cantidad');

        // ── Resumen financiero (calculado desde accessors) ────────────
        $totalRecuperadoPdf = $mermas->sum(fn($m) => $m->monto_recuperado);
        $totalPerdidoPdf    = $mermas->sum(fn($m) => $m->monto_perdido);
        $totalMermaPdf      = $mermas->sum(fn($m) => $m->total_merma);
        $balanceNetoPdf     = $totalRecuperadoPdf - $totalPerdidoPdf;

        // ── Agrupaciones ────────────────────────────────────────────────────
        $porTipo    = $mermas->groupBy('tipo_merma')->map->count();
        $porDestino = $mermas->groupBy('destino')->map->count();

        // ── Datos para la tabla ───────────────────────────────────────────
        $datos = $mermas->map(function ($m) {
            $persona = $m->usuario->persona ?? null;
            $usuario = $persona
                ? trim($persona->nombre . ' ' . $persona->apellido_paterno)
                : ($m->usuario->correo ?? 'N/A');

            return [
                'id'           => $m->id,
                'lote_codigo'  => $m->lote->codigo ?? 'N/A',
                'libro'        => $m->lote->edicion->libro->titulo ?? 'N/A',
                'tipo'         => $m->tipo_merma,
                'cantidad'     => $m->cantidad,
                'precio_unitario' => $m->precio_unitario,
                'total_merma'  => $m->total_merma,
                'monto_recuperado' => $m->monto_recuperado,
                'monto_perdido' => $m->monto_perdido,
                'destino'      => $m->destino,
                'usuario'      => $usuario,
                'fecha'        => $m->fecha_reporte
                    ? \Carbon\Carbon::parse($m->fecha_reporte)->format('d/m/Y')
                    : '—',
            ];
        });

        // ── Gráfica: mermas por tipo ────────────────────────────────────────
        $labels  = $porTipo->keys()->toArray();
        $valores = $porTipo->values()->toArray();

        $chartConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels'   => $labels,
                'datasets' => [[
                    'data'            => $valores,
                    'backgroundColor' => [
                        'rgba(75,28,113,0.8)',
                        'rgba(155,89,182,0.8)',
                        'rgba(215,189,226,0.9)',
                        'rgba(192,57,43,0.8)',
                    ],
                ]],
            ],
            'options' => [
                'plugins' => ['legend' => ['position' => 'right']],
            ],
        ];

        $chartUrl    = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig));
        $chartBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($chartUrl));

        // ── Título dinámico ───────────────────────────────────────────────
        $titulo = 'Reporte General de Mermas';
        if ($request->filled('fecha')) $titulo .= ' — ' . date('d/m/Y', strtotime($request->fecha));
        if ($request->filled('mes'))   $titulo .= ' — Mes ' . $request->mes;
        if ($request->filled('anio'))  $titulo .= ' — ' . $request->anio;

        $pdf = Pdf::loadView('pdf.mermas.reporte_general', [
            'titulo'        => $titulo,
            'datos'         => $datos,
            'totalMermas'   => $totalMermas,
            'totalUnidades'     => $totalUnidades,
            'promedioUnid'      => $promedioUnid,
            'maxUnidades'       => $maxUnidades,
            'minUnidades'       => $minUnidades,
            'totalRecuperado'   => $totalRecuperadoPdf,
            'totalPerdido'      => $totalPerdidoPdf,
            'totalMermaFinanciero' => $totalMermaPdf,
            'balanceNeto'       => $balanceNetoPdf,
            'porTipo'           => $porTipo,
            'porDestino'        => $porDestino,
            'chartBase64'       => $chartBase64,
            'generadoPor'       => auth()->user()->correo ?? 'Sistema',
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte_general_mermas.pdf');
    }
}

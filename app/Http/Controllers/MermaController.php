<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Merma;


class MermaController extends Controller
{
    public function index()
    {
        $mermas = DB::table('mermas')
            ->select('mermas.*')
            ->whereNull('mermas.deleted_at')
            ->orderBy('mermas.id', 'asc')
            ->paginate(10)
            ->withQueryString();

        $usuariosCatalogo = DB::table('usuarios')
            ->select('id', 'correo')
            ->whereNull('deleted_at')
            ->orderBy('correo', 'asc')
            ->get();

        return view('mermas.index', compact('mermas', 'usuariosCatalogo'));
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
            'fecha_reporte' => 'required|date',
            'cantidad' => 'required|integer|min:1',
            'usuario_id' => 'required|integer|exists:usuarios,id',
            'destino' => 'required|in:Devolucion_Proveedor,Destruccion',
            'estatus' => 'required|in:PENDIENTE,PROCESADO',
        ], [
            'tipo_merma.in' => 'El tipo de merma no es válido.',
            'destino.in' => 'El destino no es válido.',
            'estatus.in' => 'El estatus no es válido.',
        ]);

        $fechaReporte = Carbon::parse($request->fecha_reporte)->format('Y-m-d H:i:s');

        DB::statement("
            insert into mermas
            (lote_id, tipo_merma, fecha_reporte, cantidad, usuario_id, destino, estatus, created_at, updated_at)
            values
            (:lote_id, :tipo_merma, to_timestamp(:fecha_reporte, 'YYYY-MM-DD HH24:MI:SS'), :cantidad, :usuario_id, :destino, :estatus, current_timestamp, current_timestamp)
        ", [
            'lote_id' => $request->lote_id,
            'tipo_merma' => $request->tipo_merma,
            'fecha_reporte' => $fechaReporte,
            'cantidad' => $request->cantidad,
            'usuario_id' => $request->usuario_id,
            'destino' => $request->destino,
            'estatus' => $request->estatus,
        ]);

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
            'usuario_id' => 'required|integer|exists:usuarios,id',
            'destino' => 'required|in:Devolucion_Proveedor,Destruccion',
            'estatus' => 'required|in:PENDIENTE,PROCESADO',
        ], [
            'tipo_merma.in' => 'El tipo de merma no es válido.',
            'destino.in' => 'El destino no es válido.',
            'estatus.in' => 'El estatus no es válido.',
        ]);

        $fechaReporte = Carbon::parse($request->fecha_reporte)->format('Y-m-d H:i:s');

        DB::statement("
            update mermas
            set lote_id = :lote_id,
                tipo_merma = :tipo_merma,
                fecha_reporte = to_timestamp(:fecha_reporte, 'YYYY-MM-DD HH24:MI:SS'),
                cantidad = :cantidad,
                usuario_id = :usuario_id,
                destino = :destino,
                estatus = :estatus,
                updated_at = current_timestamp
            where id = :id
        ", [
            'lote_id' => $request->lote_id,
            'tipo_merma' => $request->tipo_merma,
            'fecha_reporte' => $fechaReporte,
            'cantidad' => $request->cantidad,
            'usuario_id' => $request->usuario_id,
            'destino' => $request->destino,
            'estatus' => $request->estatus,
            'id' => $id,
        ]);

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
        $query = Merma::with(['lote.edicion.libro', 'usuario.persona'])
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
            'totalUnidades' => $totalUnidades,
            'promedioUnid'  => $promedioUnid,
            'maxUnidades'   => $maxUnidades,
            'minUnidades'   => $minUnidades,
            'porTipo'       => $porTipo,
            'porDestino'    => $porDestino,
            'chartBase64'   => $chartBase64,
            'generadoPor'   => auth()->user()->correo ?? 'Sistema',
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte_general_mermas.pdf');
    }
}

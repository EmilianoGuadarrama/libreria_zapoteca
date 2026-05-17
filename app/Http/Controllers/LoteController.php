<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Edicion;
use App\Models\Compra;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;


class LoteController extends Controller
{
    public function index()
    {
        // Traemos los lotes con sus relaciones para la tabla
        $lotes = Lote::with(['edicion.libro', 'compra', 'ubicacion', 'usuario'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Traemos catálogos para llenar los <select> del modal
        $ediciones = Edicion::with('libro')->get();
        $compras = Compra::orderBy('id', 'desc')->get();
        $ubicaciones = Ubicacion::all();

        return view('lotes.index', compact('lotes', 'ediciones', 'compras', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'compra_id' => 'required|exists:compras,id',
            'edicion_id' => 'required|exists:ediciones,id',
            'codigo' => 'required|string|max:16|unique:lotes,codigo',
            'cantidad' => 'required|integer|min:1',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear el nuevo lote
            $lote = Lote::create([
                'compra_id' => $request->compra_id,
                'edicion_id' => $request->edicion_id,
                'codigo' => strtoupper($request->codigo),
                'cantidad' => $request->cantidad,
                'usuario_id' => auth()->id() ?? 1, // Cambiar por auth()->id() real
                'ubicacion_id' => $request->ubicacion_id,
            ]);

            // 2. Sumar el stock a la tabla de ediciones (Inventario global)
            $edicion = Edicion::findOrFail($request->edicion_id);
            $edicion->existencias += $request->cantidad;
            $edicion->save();

            DB::commit();

            return redirect()->route('lotes.index')->with('success', 'Lote registrado y stock actualizado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el lote: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'codigo' => 'required|string|max:16|unique:lotes,codigo,' . $id,
            'edicion_id' => 'required|exists:ediciones,id',
            'compra_id' => 'required|exists:compras,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
        ]);

        try {
            DB::beginTransaction();

            $lote = Lote::findOrFail($id);

            $lote->update([
                'codigo' => strtoupper($request->codigo),
                'edicion_id' => $request->edicion_id,
                'compra_id' => $request->compra_id,
                'ubicacion_id' => $request->ubicacion_id,
            ]);

            DB::commit();

            return redirect()->route('lotes.index')->with('success', 'Lote actualizado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el lote: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $lote = Lote::findOrFail($id);

            // Antes de borrar el lote, restamos su cantidad actual de las existencias globales
            if ($lote->cantidad > 0) {
                $edicion = Edicion::findOrFail($lote->edicion_id);
                $edicion->existencias -= $lote->cantidad;
                $edicion->save();
            }

            $lote->delete();

            DB::commit();

            return redirect()->route('lotes.index')->with('success', 'Lote eliminado y stock ajustado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el lote: ' . $e->getMessage());
        }
    }

    public function generarPDF($id)
    {
        // ── Carga completa con relaciones ────────────────────────────────────
        $lote = Lote::with([
            'edicion.libro',
            'compra.proveedor',
            'ubicacion',
            'usuario.persona',
        ])->find($id);

        if (!$lote) {
            return redirect()->back()->with('error', 'No se pudo generar el PDF. El lote solicitado no existe.');
        }

        // ── Nombre del usuario que registró el lote ───────────────────────
        $persona       = $lote->usuario->persona ?? null;
        $registradoPor = $persona
            ? trim($persona->nombre . ' ' . $persona->apellido_paterno)
            : ($lote->usuario->correo ?? 'N/A');

        $pdf = Pdf::loadView('pdf.lotes.individual', [
            'titulo'        => 'Ficha de Lote #' . $lote->codigo,
            'lote'          => $lote,
            'registradoPor' => $registradoPor,
            'generadoPor'   => auth()->user()->correo ?? 'Sistema',
        ])->setPaper('letter', 'portrait');

        return $pdf->download('lote_' . $lote->codigo . '.pdf');
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
        $query = Lote::with(['edicion.libro', 'compra.proveedor', 'ubicacion', 'usuario.persona']);

        // ── Filtros Oracle-compatibles sobre fecha_entrada ─────────────────
        if ($request->filled('fecha')) {
            $query->whereRaw("TRUNC(fecha_entrada) = TO_DATE(?, 'YYYY-MM-DD')", [$request->fecha]);
        }

        if ($request->filled('mes')) {
            $query->whereRaw('EXTRACT(MONTH FROM fecha_entrada) = ?', [$request->mes]);
        }

        if ($request->filled('anio')) {
            $query->whereRaw('EXTRACT(YEAR FROM fecha_entrada) = ?', [$request->anio]);
        }

        $lotes = $query->orderBy('id', 'desc')->get();

        if ($lotes->isEmpty()) {
            return redirect()->back()->with('error', 'No hay lotes registrados con esos filtros.');
        }

        // ── Estadísticas ──────────────────────────────────────────────────
        $totalLotes     = $lotes->count();
        $totalUnidades  = $lotes->sum('cantidad');
        $promedioUnid   = $lotes->avg('cantidad');
        $maxUnidades    = $lotes->max('cantidad');
        $minUnidades    = $lotes->min('cantidad');

        // ── Datos para la tabla ───────────────────────────────────────────
        $datos = $lotes->map(function ($l) {
            $persona = $l->usuario->persona ?? null;
            $usuario = $persona
                ? trim($persona->nombre . ' ' . $persona->apellido_paterno)
                : ($l->usuario->correo ?? 'N/A');

            return [
                'codigo'    => $l->codigo,
                'libro'     => $l->edicion->libro->titulo ?? 'N/A',
                'compra'    => $l->compra->folio_factura ?? 'N/A',
                'proveedor' => $l->compra->proveedor->nombre ?? 'N/A',
                'cantidad'  => $l->cantidad,
                'ubicacion' => $l->ubicacion
                    ? 'P:' . $l->ubicacion->pasillo . ' E:' . $l->ubicacion->estante . ' N:' . $l->ubicacion->nivel
                    : 'N/A',
                'usuario'   => $usuario,
                'fecha'     => $l->fecha_entrada
                    ? \Carbon\Carbon::parse($l->fecha_entrada)->format('d/m/Y')
                    : '—',
            ];
        });

        // ── Datos gráfica: lotes por mes ─────────────────────────────────
        $mesesTexto = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
        ];

        $lotesPorMes = Lote::selectRaw(
            'EXTRACT(MONTH FROM fecha_entrada) as mes, COUNT(*) as total'
        )
            ->groupByRaw('EXTRACT(MONTH FROM fecha_entrada)')
            ->orderByRaw('EXTRACT(MONTH FROM fecha_entrada)')
            ->get();

        $labels  = $lotesPorMes->map(fn($i) => $mesesTexto[(int)$i->mes] ?? "Mes {$i->mes}")->toArray();
        $valores = $lotesPorMes->map(fn($i) => (int)$i->total)->toArray();

        // ── QuickChart → Base64 ───────────────────────────────────────────
        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels'   => $labels,
                'datasets' => [[
                    'label'           => 'Lotes registrados por mes',
                    'data'            => $valores,
                    'backgroundColor' => 'rgba(75, 28, 113, 0.75)',
                    'borderColor'     => '#4b1c71',
                    'borderWidth'     => 1,
                ]],
            ],
            'options' => [
                'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]]],
            ],
        ];

        $chartUrl    = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig));
        $chartBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($chartUrl));

        // ── Título dinámico ───────────────────────────────────────────────
        $titulo = 'Reporte General de Lotes';
        if ($request->filled('fecha')) $titulo .= ' — ' . date('d/m/Y', strtotime($request->fecha));
        if ($request->filled('mes'))   $titulo .= ' — Mes ' . $request->mes;
        if ($request->filled('anio'))  $titulo .= ' — ' . $request->anio;

        $pdf = Pdf::loadView('pdf.lotes.reporte_general', [
            'titulo'        => $titulo,
            'datos'         => $datos,
            'totalLotes'    => $totalLotes,
            'totalUnidades' => $totalUnidades,
            'promedioUnid'  => $promedioUnid,
            'maxUnidades'   => $maxUnidades,
            'minUnidades'   => $minUnidades,
            'chartBase64'   => $chartBase64,
            'generadoPor'   => auth()->user()->correo ?? 'Sistema',
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte_general_lotes.pdf');
    }
}

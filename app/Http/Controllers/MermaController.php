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
        $merma = Merma::findOrFail($id);

        // Validación opcional (como ya usabas)
        if ($merma->estatus !== 'PROCESADO') {
            return redirect()->back()->with('error', 'Solo se pueden generar PDFs de mermas procesadas');
        }

        $columnas = ['ID', 'Lote', 'Tipo', 'Cantidad', 'Destino', 'Fecha'];

        $datos = [
            $merma->id,
            $merma->lote,
            $merma->tipo_merma,
            $merma->cantidad,
            $merma->destino,
            $merma->fecha_reporte
        ];

        $pdf = Pdf::loadView('pdf.individual', [
            'titulo' => 'Reporte de Merma',
            'columnas' => $columnas,
            'datos' => $datos
        ]);

        return $pdf->download('reporte_merma_' . $merma->id . '.pdf');
    }

    public function reporteGeneral()
    {
        $mermas = Merma::where('estatus', 'PROCESADO')->get();

        if ($mermas->isEmpty()) {
            return redirect()->back()->with('error', 'No hay mermas procesadas');
        }

        $columnas = ['ID', 'Lote', 'Tipo', 'Cantidad', 'Destino', 'Fecha'];

        $datos = [];

        foreach ($mermas as $merma) {
            $datos[] = [
                $merma->id,
                $merma->lote,
                $merma->tipo_merma,
                $merma->cantidad,
                $merma->destino,
                $merma->fecha_reporte
            ];
        }

        $pdf = Pdf::loadView('pdf.reporte_general', [
            'titulo' => 'Reporte General de Mermas',
            'columnas' => $columnas,
            'datos' => $datos
        ]);

        return $pdf->download('reporte_general_mermas.pdf');
    }
}

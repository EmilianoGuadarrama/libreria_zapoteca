<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


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


    //Sección para los reportes]
    public function reporte()
    {
        $datos = DB::select("
        SELECT 
            b.titulo as libro,
            COUNT(m.id) as total_mermas,
            SUM(m.cantidad) as cantidad_total
        FROM MERMAS m
        JOIN LOTES l ON m.lote_id = l.id
        JOIN EDICIONES e ON l.edicion_id = e.id
        JOIN LIBROS b ON e.libro_id = b.id
        GROUP BY b.titulo
        ORDER BY b.titulo
    ");

        return view('reportes.mermas', compact('datos'));
    }

    public function reportePDF()
    {
        $datos = DB::select("
        SELECT 
            b.titulo as libro,
            COUNT(m.id) as total_mermas,
            SUM(m.cantidad) as cantidad_total
        FROM MERMAS m
        JOIN LOTES l ON m.lote_id = l.id
        JOIN EDICIONES e ON l.edicion_id = e.id
        JOIN LIBROS b ON e.libro_id = b.id
        GROUP BY b.titulo
        ORDER BY b.titulo
    ");

        $pdf = Pdf::loadView('reportes.mermas_pdf', compact('datos'));

        return $pdf->download('reporte_mermas.pdf');
    }
}

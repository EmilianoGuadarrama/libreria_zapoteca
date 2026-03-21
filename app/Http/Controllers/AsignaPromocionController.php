<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class AsignaPromocionController extends Controller
{
    public function index()
    {
        $promociones = DB::table('promociones')->whereNull('deleted_at')->get();
        $ediciones = DB::table('ediciones')
            ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
            ->select('ediciones.id', 'ediciones.isbn', 'libros.titulo')
            ->whereNull('ediciones.deleted_at')
            ->get();
        $asignaciones = DB::table('asigna_promociones')
            ->join('promociones', 'asigna_promociones.promocion_id', '=', 'promociones.id')
            ->join('ediciones', 'asigna_promociones.edicion_id', '=', 'ediciones.id')
            ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
            ->select(
                'asigna_promociones.id',
                'promociones.nombre as promocion_nombre',
                'promociones.porcentaje_descuento',
                'libros.titulo as libro_titulo',
                'ediciones.isbn'
            )
            ->whereNull('asigna_promociones.deleted_at')
            ->get();

        return view('asigna_promociones.index', compact('promociones', 'ediciones', 'asignaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'promocion_id' => 'required|integer',
            'edicion_id'   => 'required|integer',
        ]);

        try {
            $asignacionActual = DB::table('asigna_promociones')
                ->join('promociones', 'asigna_promociones.promocion_id', '=', 'promociones.id')
                ->select('asigna_promociones.*', 'promociones.nombre as promocion_nombre')
                ->where('asigna_promociones.edicion_id', $request->edicion_id)
                ->whereNull('asigna_promociones.deleted_at')
                ->first();

            if ($asignacionActual && !$request->has('force_replace')) {

                if ($asignacionActual->promocion_id == $request->promocion_id) {
                    return back()->withErrors(['error' => 'Este libro ya tiene esta promoción aplicada actualmente.']);
                }

                $nuevaPromocion = DB::table('promociones')->where('id', $request->promocion_id)->first();
                $libro = DB::table('ediciones')
                    ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
                    ->where('ediciones.id', $request->edicion_id)
                    ->select('libros.titulo')
                    ->first();

                return back()->with('confirm_replace', [
                    'edicion_id'           => $request->edicion_id,
                    'promocion_id'         => $request->promocion_id,
                    'old_promocion_nombre' => $asignacionActual->promocion_nombre,
                    'new_promocion_nombre' => $nuevaPromocion->nombre,
                    'libro_titulo'         => $libro->titulo
                ]);
            }

            if ($asignacionActual && $request->has('force_replace')) {
                // Borrado lógico (Soft Delete) de la promoción vieja
                DB::table('asigna_promociones')
                    ->where('id', $asignacionActual->id)
                    ->update(['deleted_at' => Carbon::now()]);
            }

            DB::table('asigna_promociones')->insert([
                'promocion_id' => $request->promocion_id,
                'edicion_id'   => $request->edicion_id,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]);

            return redirect()->route('asigna_promociones.index')->with('status', '¡Oferta aplicada al libro exitosamente!');

        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Error al asignar la promoción. Verifique los datos.']);
        }
    }

    public function destroy(string $id)
    {
        // (El método destroy se queda igual)
        try {
            DB::table('asigna_promociones')
                ->where('id', $id)
                ->update(['deleted_at' => Carbon::now()]);

            return redirect()->route('asigna_promociones.index')->with('status', 'Oferta removida del libro.');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'No se pudo remover la asignación.']);
        }
    }
}

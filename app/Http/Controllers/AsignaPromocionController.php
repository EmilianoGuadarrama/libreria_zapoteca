<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class AsignaPromocionController extends Controller
{
    public function index()
    {
        $promociones = DB::table('promociones')->whereNull('deleted_at')->get();
        $ediciones = DB::table('ediciones')
            ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
            ->leftJoin('asigna_autores', 'libros.id', '=', 'asigna_autores.libro_id')
            ->leftJoin('autores', 'asigna_autores.autor_id', '=', 'autores.id')
            ->leftJoin('personas', 'autores.persona_id', '=', 'personas.id')
            ->leftJoin('asigna_promociones as ap', function ($join) {
                $join->on('ediciones.id', '=', 'ap.edicion_id')
                    ->whereNull('ap.deleted_at');
            })
            ->leftJoin('promociones as p', 'ap.promocion_id', '=', 'p.id')
            ->select(
                'ediciones.id',
                'ediciones.isbn',
                'ediciones.precio_venta',
                'ediciones.portada',
                'libros.titulo',
                DB::raw("personas.nombre || ' ' || personas.apellido_paterno || ' ' || personas.apellido_materno as autor"),
                'p.nombre as promo_nombre',
                'p.porcentaje_descuento as promo_descuento'
            )
            ->whereNull('ediciones.deleted_at')
            ->get();
        $asignaciones = DB::table('asigna_promociones')
            ->join('promociones', 'asigna_promociones.promocion_id', '=', 'promociones.id')
            ->join('ediciones', 'asigna_promociones.edicion_id', '=', 'ediciones.id')
            ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
            ->leftJoin('editoriales', 'ediciones.editorial_id', '=', 'editoriales.id')
            ->leftJoin('asigna_autores', 'libros.id', '=', 'asigna_autores.libro_id')
            ->leftJoin('autores', 'asigna_autores.autor_id', '=', 'autores.id')
            ->leftJoin('personas', 'autores.persona_id', '=', 'personas.id')
            ->select(
                'asigna_promociones.id',
                'ediciones.id as edicion_id',
                'promociones.nombre as promocion_nombre',
                'promociones.porcentaje_descuento',
                'libros.titulo as libro_titulo',
                'ediciones.isbn',
                'ediciones.portada',
                'ediciones.alt_imagen',
                'ediciones.anio_publicacion',
                'ediciones.numero_edicion',
                'ediciones.numero_paginas',
                'ediciones.precio_venta as precio_venta',
                'ediciones.existencias',
                'ediciones.stock_minimo',
                'editoriales.nombre as editorial',

                DB::raw("personas.nombre || ' ' || personas.apellido_paterno || ' ' || personas.apellido_materno as autor")
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

                $nuevaPromocion = DB::table('promociones')
                    ->where('id', $request->promocion_id)
                    ->first();
                $libro = DB::table('ediciones')
                    ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
                    ->leftJoin('editoriales', 'ediciones.editorial_id', '=', 'editoriales.id')
                    ->leftJoin('asigna_autores', 'libros.id', '=', 'asigna_autores.libro_id')
                    ->leftJoin('autores', 'asigna_autores.autor_id', '=', 'autores.id')
                    ->leftJoin('personas', 'autores.persona_id', '=', 'personas.id')
                    ->where('ediciones.id', $request->edicion_id)
                    ->select(
                        'libros.titulo',
                        'ediciones.isbn',
                        'ediciones.portada',
                        'ediciones.alt_imagen',
                        'ediciones.precio_venta',
                        'editoriales.nombre as editorial',
                        DB::raw("personas.nombre || ' ' || personas.apellido_paterno || ' ' || personas.apellido_materno as autor")
                    )
                    ->first();

                return back()->with('confirm_replace', [
                    'edicion_id'           => $request->edicion_id,
                    'promocion_id'         => $request->promocion_id,
                    'old_promocion_nombre' => $asignacionActual->promocion_nombre,
                    'new_promocion_nombre' => $nuevaPromocion->nombre,
                    'libro_titulo'         => $libro->titulo,
                    'isbn'         => $libro->isbn,
                    'portada'      => $libro->portada,
                    'alt_imagen'   => $libro->alt_imagen,
                    'autor'        => $libro->autor ?? 'Desconocido',
                    'editorial'    => $libro->editorial ?? 'N/A',
                    'precio'       => $libro->precio_venta,
                    'descuento'    => $nuevaPromocion->porcentaje_descuento,
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
    public function updatePortada(Request $request, string $edicionId)
    {
        $request->validate([
            'portada' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $edicion = DB::table('ediciones')
            ->where('id', $edicionId)
            ->whereNull('deleted_at')
            ->first();

        if (!$edicion) {
            return back()->withErrors(['error' => 'La edicion seleccionada no existe.']);
        }

        $path = $request->file('portada')->store('portadas', 'public');

        if (!empty($edicion->portada) && Storage::disk('public')->exists($edicion->portada)) {
            Storage::disk('public')->delete($edicion->portada);
        }

        DB::table('ediciones')
            ->where('id', $edicionId)
            ->update([
                'portada' => $path,
                'updated_at' => Carbon::now(),
            ]);

        return redirect()
            ->route('asigna_promociones.index')
            ->with('status', 'Portada actualizada correctamente.');
    }
}

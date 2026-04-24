<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class PromocionController extends Controller
{
    public function index()
    {
        $todasLasPromociones = DB::table('promociones as p')
            ->leftJoin('usuarios as u', 'p.autorizado_por_id', '=', 'u.id')
            ->leftJoin('personas as per', 'u.persona_id', '=', 'per.id')
            ->select('p.*', 'per.nombre as nombre_autorizado', 'per.apellido_paterno as ape_paterno', 'per.apellido_materno as ape_materno')
            ->whereNull('p.deleted_at')
            ->orderBy('p.nombre', 'asc')
            ->get();

        $hoy = now()->startOfDay();
        $promocionesActivas = collect();
        $promocionesExpiradas = collect();

        foreach ($todasLasPromociones as $promo) {
            $fechaFinal = Carbon::parse($promo->fecha_final)->startOfDay();
            if ($fechaFinal->lt($hoy)) {
                $promocionesExpiradas->push($promo);
            } else {
                $promocionesActivas->push($promo);
            }
        }

        $librosVinculados = DB::table('asigna_promociones as ap')
            ->join('ediciones as e', 'ap.edicion_id', '=', 'e.id')
            ->join('libros as l', 'e.libro_id', '=', 'l.id')
            ->leftJoin('editoriales', 'e.editorial_id', '=', 'editoriales.id')
            ->leftJoin('asigna_autores', 'l.id', '=', 'asigna_autores.libro_id')
            ->leftJoin('autores', 'asigna_autores.autor_id', '=', 'autores.id')
            ->leftJoin('personas', 'autores.persona_id', '=', 'personas.id')
            ->select(
                'ap.promocion_id',
                'e.id as edicion_id',
                'l.titulo as libro_titulo',
                'e.isbn',
                'e.portada',
                'e.alt_imagen',
                'e.anio_publicacion',
                'e.numero_edicion',
                'e.numero_paginas',
                'e.existencias',
                'e.stock_minimo',
                'e.precio_venta',
                'editoriales.nombre as editorial',
                DB::raw("personas.nombre || ' ' || personas.apellido_paterno || ' ' || personas.apellido_materno as autor")
            )
            ->whereNull('ap.deleted_at')
            ->get()
            ->groupBy('promocion_id');

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

        $promociones = $todasLasPromociones;

        return view('promociones.index', compact('promocionesActivas', 'promocionesExpiradas', 'librosVinculados', 'ediciones', 'promociones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'               => 'required|string|max:200',
            'fecha_inicio'         => 'required|date',
            'fecha_final'          => 'required|date|after_or_equal:fecha_inicio',
            'porcentaje_descuento' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $data = $request->all();
            $data['autorizado_por_id'] = auth()->id();

            Promocion::create($data);
            return redirect()->route('promociones.index')->with('status', 'Promoción creada correctamente.');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al guardar.']);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre'               => 'required|string|max:200',
            'fecha_inicio'         => 'required|date',
            'fecha_final'          => 'required|date|after_or_equal:fecha_inicio',
            'porcentaje_descuento' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $promocion = Promocion::findOrFail($id);
            $promocion->update($request->only(['nombre', 'fecha_inicio', 'fecha_final', 'porcentaje_descuento']));
            return redirect()->route('promociones.index')->with('status', 'Promoción actualizada.');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al actualizar la promoción.']);
        }
    }

    public function renovar(Request $request, string $id)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_final'  => 'required|date|after_or_equal:fecha_inicio',
        ]);

        try {
            $promocion = Promocion::findOrFail($id);

            $promocion->update([
                'fecha_inicio'      => $request->fecha_inicio,
                'fecha_final'       => $request->fecha_final,
                'autorizado_por_id' => auth()->id()
            ]);

            return redirect()->route('promociones.index')->with('status', "Promoción '{$promocion->nombre}' reprogramada exitosamente.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al intentar reprogramar la promoción.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $promocion = Promocion::findOrFail($id);
            $promocion->delete();
            return redirect()->route('promociones.index')->with('status', 'Promoción eliminada.');
        } catch (QueryException $e) {
            return redirect()->route('promociones.index')->withErrors(['error' => 'No se puede eliminar: ya está asignada a libros.']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AsignaPromocionController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        $promociones = DB::table('promociones')
            ->whereNull('deleted_at')
            ->whereDate('fecha_final', '>=', $hoy)
            ->orderBy('nombre')
            ->get();

        $ediciones = DB::table('ediciones')
            ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
            ->leftJoin('asigna_autores', 'libros.id', '=', 'asigna_autores.libro_id')
            ->leftJoin('autores', 'asigna_autores.autor_id', '=', 'autores.id')
            ->leftJoin('personas', 'autores.persona_id', '=', 'personas.id')
            ->leftJoin('asigna_promociones as ap', function ($join) {
                $join->on('ediciones.id', '=', 'ap.edicion_id')
                    ->whereNull('ap.deleted_at');
            })
            ->leftJoin('promociones as p', function ($join) use ($hoy) {
                $join->on('ap.promocion_id', '=', 'p.id')
                    ->whereNull('p.deleted_at')
                    ->whereDate('p.fecha_inicio', '<=', $hoy)
                    ->whereDate('p.fecha_final', '>=', $hoy);
            })
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
            ->distinct()
            ->get();

        $todasLasAsignaciones = DB::table('asigna_promociones')
            ->join('promociones', 'asigna_promociones.promocion_id', '=', 'promociones.id')
            ->join('ediciones', 'asigna_promociones.edicion_id', '=', 'ediciones.id')
            ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
            ->leftJoin('editoriales', 'ediciones.editorial_id', '=', 'editoriales.id')
            ->leftJoin('asigna_autores', 'libros.id', '=', 'asigna_autores.libro_id')
            ->leftJoin('autores', 'asigna_autores.autor_id', '=', 'autores.id')
            ->leftJoin('personas', 'autores.persona_id', '=', 'personas.id')
            ->select(
                'asigna_promociones.id',
                'asigna_promociones.created_at as asignada_en',
                'ediciones.id as edicion_id',
                'promociones.id as promocion_id',
                'promociones.nombre as promocion_nombre',
                'promociones.porcentaje_descuento',
                'promociones.fecha_inicio',
                'promociones.fecha_final',
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
            ->orderBy('promociones.fecha_final', 'desc')
            ->orderBy('asigna_promociones.created_at', 'desc')
            ->get();

        $asignacionesActivas = collect();
        $asignacionesExpiradas = collect();

        foreach ($todasLasAsignaciones as $asignacion) {
            $fechaInicio = Carbon::parse($asignacion->fecha_inicio)->startOfDay();
            $fechaFinal = Carbon::parse($asignacion->fecha_final)->startOfDay();

            if ($fechaFinal->lt($hoy)) {
                $asignacionesExpiradas->push($asignacion);
            } else {
                $asignacion->fecha_inicio_carbon = $fechaInicio;
                $asignacion->fecha_final_carbon = $fechaFinal;
                $asignacion->dias_restantes = $hoy->diffInDays($fechaFinal, false);
                $asignacion->es_proxima = $hoy->lt($fechaInicio);
                $asignacionesActivas->push($asignacion);
            }
        }

        $asignacionesActivas = $asignacionesActivas
            ->sort(function ($a, $b) {
                if ($a->es_proxima !== $b->es_proxima) {
                    return $a->es_proxima <=> $b->es_proxima;
                }

                if (!$a->es_proxima && $a->dias_restantes !== $b->dias_restantes) {
                    return $a->dias_restantes <=> $b->dias_restantes;
                }

                if ($a->es_proxima && !$a->fecha_inicio_carbon->equalTo($b->fecha_inicio_carbon)) {
                    return $a->fecha_inicio_carbon->timestamp <=> $b->fecha_inicio_carbon->timestamp;
                }

                return $a->fecha_final_carbon->timestamp <=> $b->fecha_final_carbon->timestamp;
            })
            ->values();

        return view('asigna_promociones.index', compact(
            'promociones',
            'ediciones',
            'asignacionesActivas',
            'asignacionesExpiradas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'promocion_id' => 'required|integer|exists:promociones,id',
            'edicion_id' => 'required|array|min:1',
            'edicion_id.*' => 'required|integer|distinct|exists:ediciones,id',
        ]);

        try {
            $hoy = Carbon::today();
            $edicionIds = collect($request->input('edicion_id', []))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $nuevaPromocion = DB::table('promociones')
                ->where('id', $request->promocion_id)
                ->whereNull('deleted_at')
                ->first();

            if (!$nuevaPromocion) {
                return back()->withErrors(['error' => 'La promoción seleccionada no existe.']);
            }

            $nuevaFechaInicio = Carbon::parse($nuevaPromocion->fecha_inicio)->startOfDay();
            $nuevaFechaFinal = Carbon::parse($nuevaPromocion->fecha_final)->startOfDay();
            $nuevaPromocionActiva = $hoy->betweenIncluded($nuevaFechaInicio, $nuevaFechaFinal);

            if ($nuevaFechaFinal->lt($hoy)) {
                return back()->withErrors(['error' => 'No se puede asignar una promoción que ya terminó.']);
            }

            $libros = $this->obtenerDetallesLibros($edicionIds->all())->keyBy('edicion_id');
            $asignacionesActivas = $this->obtenerAsignacionesActivasPorEdicion($edicionIds->all(), $hoy);

            $duplicadosMismaPromocion = DB::table('asigna_promociones')
                ->where('promocion_id', $request->promocion_id)
                ->whereIn('edicion_id', $edicionIds->all())
                ->whereNull('deleted_at')
                ->pluck('edicion_id')
                ->map(fn ($id) => (int) $id);

            $titulosDuplicados = $duplicadosMismaPromocion
                ->map(fn ($id) => $libros->get($id)?->titulo)
                ->filter()
                ->values();

            if ($titulosDuplicados->isNotEmpty()) {
                return back()->withErrors([
                    'error' => 'Esta promoción ya está registrada para: ' . $titulosDuplicados->join(', ') . '.',
                ]);
            }

            $mismosActivos = $asignacionesActivas
                ->filter(fn ($asignacion) => (int) $asignacion->promocion_id === (int) $request->promocion_id);

            if ($mismosActivos->isNotEmpty()) {
                $titulos = $mismosActivos
                    ->map(fn ($asignacion) => $libros->get((int) $asignacion->edicion_id)?->titulo)
                    ->filter()
                    ->values();

                return back()->withErrors([
                    'error' => 'Los siguientes libros ya tienen esta promoción activa: ' . $titulos->join(', ') . '.',
                ]);
            }

            if ($asignacionesActivas->isNotEmpty() && !$nuevaPromocionActiva) {
                $titulos = $asignacionesActivas
                    ->map(fn ($asignacion) => $libros->get((int) $asignacion->edicion_id)?->titulo)
                    ->filter()
                    ->values();

                return back()->withErrors([
                    'error' => 'Estos libros ya tienen una promoción activa y solo se pueden reemplazar con otra promoción activa: ' . $titulos->join(', ') . '.',
                ]);
            }

            if ($asignacionesActivas->isNotEmpty() && !$request->boolean('force_replace')) {
                $librosParaReemplazo = $asignacionesActivas
                    ->map(function ($asignacion) use ($libros) {
                        $libro = $libros->get((int) $asignacion->edicion_id);

                        return [
                            'edicion_id' => (int) $asignacion->edicion_id,
                            'old_promocion_nombre' => $asignacion->promocion_nombre,
                            'libro_titulo' => $libro->titulo ?? 'Sin título',
                            'isbn' => $libro->isbn ?? 'N/A',
                            'portada' => $libro->portada ?? null,
                            'alt_imagen' => $libro->alt_imagen ?? 'Sin imagen',
                            'autor' => $libro->autor ?? 'Desconocido',
                            'editorial' => $libro->editorial ?? 'N/A',
                            'precio' => $libro->precio_venta ?? 0,
                        ];
                    })
                    ->values()
                    ->all();

                return back()->with('confirm_replace', [
                    'promocion_id' => $request->promocion_id,
                    'edicion_ids' => $edicionIds->all(),
                    'new_promocion_nombre' => $nuevaPromocion->nombre,
                    'descuento' => $nuevaPromocion->porcentaje_descuento,
                    'edicion_id' => $librosParaReemplazo[0]['edicion_id'] ?? null,
                    'old_promocion_nombre' => $librosParaReemplazo[0]['old_promocion_nombre'] ?? null,
                    'libro_titulo' => $librosParaReemplazo[0]['libro_titulo'] ?? null,
                    'isbn' => $librosParaReemplazo[0]['isbn'] ?? null,
                    'portada' => $librosParaReemplazo[0]['portada'] ?? null,
                    'alt_imagen' => $librosParaReemplazo[0]['alt_imagen'] ?? null,
                    'autor' => $librosParaReemplazo[0]['autor'] ?? null,
                    'editorial' => $librosParaReemplazo[0]['editorial'] ?? null,
                    'precio' => $librosParaReemplazo[0]['precio'] ?? 0,
                    'libros' => $librosParaReemplazo,
                    'total_replace' => count($librosParaReemplazo),
                    'total_new' => $edicionIds->count() - count($librosParaReemplazo),
                ]);
            }

            DB::transaction(function () use ($request, $edicionIds, $asignacionesActivas) {
                if ($asignacionesActivas->isNotEmpty() && $request->boolean('force_replace')) {
                    DB::table('asigna_promociones')
                        ->whereIn('id', $asignacionesActivas->pluck('id')->all())
                        ->update([
                            'deleted_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                }

                $now = Carbon::now();
                $rows = $edicionIds->map(fn ($edicionId) => [
                    'promocion_id' => $request->promocion_id,
                    'edicion_id' => $edicionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                DB::table('asigna_promociones')->insert($rows);
            });

            return redirect()->route('asigna_promociones.index')->with('status', 'Oferta aplicada al libro exitosamente.');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Error al asignar la promoción. Verifique los datos.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::table('asigna_promociones')
                ->where('id', $id)
                ->update([
                    'deleted_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

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

    private function obtenerDetallesLibros(array $edicionIds)
    {
        return DB::table('ediciones')
            ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
            ->leftJoin('editoriales', 'ediciones.editorial_id', '=', 'editoriales.id')
            ->leftJoin('asigna_autores', 'libros.id', '=', 'asigna_autores.libro_id')
            ->leftJoin('autores', 'asigna_autores.autor_id', '=', 'autores.id')
            ->leftJoin('personas', 'autores.persona_id', '=', 'personas.id')
            ->whereIn('ediciones.id', $edicionIds)
            ->select(
                'ediciones.id as edicion_id',
                'libros.titulo',
                'ediciones.isbn',
                'ediciones.portada',
                'ediciones.alt_imagen',
                'ediciones.precio_venta',
                'editoriales.nombre as editorial',
                DB::raw("personas.nombre || ' ' || personas.apellido_paterno || ' ' || personas.apellido_materno as autor")
            )
            ->get();
    }

    private function obtenerAsignacionesActivasPorEdicion(array $edicionIds, Carbon $hoy)
    {
        return DB::table('asigna_promociones as ap')
            ->join('promociones as p', 'ap.promocion_id', '=', 'p.id')
            ->select(
                'ap.id',
                'ap.edicion_id',
                'ap.promocion_id',
                'p.nombre as promocion_nombre',
                'p.fecha_inicio',
                'p.fecha_final'
            )
            ->whereIn('ap.edicion_id', $edicionIds)
            ->whereNull('ap.deleted_at')
            ->whereNull('p.deleted_at')
            ->whereDate('p.fecha_inicio', '<=', $hoy)
            ->whereDate('p.fecha_final', '>=', $hoy)
            ->get()
            ->keyBy(fn ($item) => (int) $item->edicion_id);
    }
}

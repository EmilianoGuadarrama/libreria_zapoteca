<?php

namespace App\Http\Controllers;

use App\Models\Clasificacion;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class ClasificacionController extends Controller
{
    public function index()
    {
        $clasificaciones = Clasificacion::withCount('libros')
            ->with([
                'libros' => function ($query) {
                    $query->select('id', 'clasificacion_id', 'titulo', 'anio_publicacion_original')
                        ->with([
                            'autores.persona:id,nombre,apellido_paterno,apellido_materno',
                            'ediciones' => function ($edicionesQuery) {
                                $edicionesQuery->select(
                                        'id',
                                        'libro_id',
                                        'editorial_id',
                                        'isbn',
                                        'anio_publicacion',
                                        'numero_edicion',
                                        'numero_paginas',
                                        'precio_venta',
                                        'portada',
                                        'alt_imagen',
                                        'existencias',
                                        'stock_minimo'
                                    )
                                    ->with('editorial:id,nombre')
                                    ->orderByRaw('CASE WHEN portada IS NULL THEN 1 ELSE 0 END')
                                    ->orderBy('numero_edicion', 'asc')
                                    ->orderBy('id', 'asc');
                            },
                        ])
                        ->orderBy('titulo', 'asc');
                },
            ])
            ->orderBy('nombre', 'asc')
            ->get();

        $librosVinculados = $clasificaciones->mapWithKeys(function ($clasificacion) {
            $libros = $clasificacion->libros->map(function ($libro) {
                $edicionRepresentativa = $libro->ediciones->first();
                $portada = $edicionRepresentativa?->portada;

                $autores = $libro->autores
                    ->map(function ($autor) {
                        if (!$autor->persona) {
                            return null;
                        }

                        return trim(collect([
                            $autor->persona->nombre,
                            $autor->persona->apellido_paterno,
                            $autor->persona->apellido_materno,
                        ])->filter()->implode(' '));
                    })
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode(', ');

                return (object) [
                    'id' => $libro->id,
                    'titulo' => $libro->titulo,
                    'edicion_id' => $edicionRepresentativa?->id,
                    'portada' => $portada,
                    'alt_imagen' => $edicionRepresentativa?->alt_imagen,
                    'isbn' => $edicionRepresentativa?->isbn,
                    'anio_publicacion_original' => $libro->anio_publicacion_original,
                    'anio_publicacion' => $edicionRepresentativa?->anio_publicacion,
                    'numero_edicion' => $edicionRepresentativa?->numero_edicion,
                    'numero_paginas' => $edicionRepresentativa?->numero_paginas,
                    'precio_venta' => $edicionRepresentativa?->precio_venta,
                    'existencias' => $edicionRepresentativa?->existencias,
                    'stock_minimo' => $edicionRepresentativa?->stock_minimo,
                    'editorial' => $edicionRepresentativa?->editorial?->nombre,
                    'autores' => $autores !== '' ? $autores : 'Autor no registrado',
                ];
            });

            return [$clasificacion->id => new Collection($libros)];
        });

        return view('clasificaciones.index', compact('clasificaciones', 'librosVinculados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:clasificaciones,nombre',
        ], [
            'nombre.required' => 'El nombre de la clasificación es obligatorio.',
            'nombre.unique' => 'Esta clasificación ya existe en el sistema.',
        ]);

        try {
            Clasificacion::create($request->all());
            return redirect()->route('clasificaciones.index')
                ->with('status', '¡Clasificación creada con éxito!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al guardar en la base de datos. Intente de nuevo.']);
        }
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:clasificaciones,nombre,' . $id,
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'El nombre ya está en uso por otra clasificación.',
        ]);

        try {
            $clasificacion = Clasificacion::findOrFail($id);
            $clasificacion->update($request->all());

            return redirect()->route('clasificaciones.index')
                ->with('status', '¡Clasificación editada correctamente!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'No se pudo actualizar la clasificación.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $clasificacion = Clasificacion::findOrFail($id);
            $clasificacion->delete();

            return redirect()->route('clasificaciones.index')
                ->with('status', '¡Clasificación eliminada correctamente!');
        } catch (QueryException $e) {
            return redirect()->route('clasificaciones.index')
                ->withErrors(['error' => 'No se puede eliminar: Esta clasificación está siendo utilizada por uno o más libros.']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $hoy = Carbon::now()->toDateString();

        // Primero obtenemos ediciones con su promo activa (sin CLOB en GROUP BY)
        $query = DB::table('ediciones')
            ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
            ->leftJoin('clasificaciones', 'libros.clasificacion_id', '=', 'clasificaciones.id')
            ->leftJoin('generos', 'libros.genero_principal_id', '=', 'generos.id')
            ->leftJoin('editoriales', 'ediciones.editorial_id', '=', 'editoriales.id')
            ->leftJoin('idiomas', 'ediciones.idioma_id', '=', 'idiomas.id')
            ->leftJoin('formatos', 'ediciones.formato_id', '=', 'formatos.id')
            ->leftJoin('asigna_promociones', function ($join) {
                $join->on('asigna_promociones.edicion_id', '=', 'ediciones.id')
                     ->whereNull('asigna_promociones.deleted_at');
            })
            ->leftJoin('promociones', function ($join) use ($hoy) {
                $join->on('promociones.id', '=', 'asigna_promociones.promocion_id')
                     ->whereNull('promociones.deleted_at')
                     ->where('promociones.fecha_inicio', '<=', $hoy)
                     ->where('promociones.fecha_final', '>=', $hoy);
            })
            ->whereNull('ediciones.deleted_at')
            ->whereNull('libros.deleted_at')
            ->where('ediciones.existencias', '>', 0)
            ->select(
                'ediciones.id as edicion_id',
                'ediciones.isbn',
                'ediciones.anio_publicacion',
                'ediciones.numero_edicion',
                'ediciones.numero_paginas',
                'ediciones.precio_venta',
                'ediciones.existencias',
                'ediciones.portada as edicion_portada',
                'ediciones.alt_imagen',
                'libros.id as libro_id',
                'libros.titulo',
                'libros.sinopsis',
                'libros.anio_publicacion_original',
                'libros.portada as libro_portada',
                'libros.clasificacion_id',
                'libros.genero_principal_id',
                'clasificaciones.nombre as clasificacion_nombre',
                'generos.nombre as genero_nombre',
                'editoriales.nombre as editorial_nombre',
                'idiomas.nombre as idioma_nombre',
                'formatos.nombre as formato_nombre',
                'promociones.nombre as promo_nombre',
                'promociones.porcentaje_descuento as promo_descuento'
            );

        // Filtros
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('libros.titulo', 'LIKE', "%{$buscar}%")
                  ->orWhere('ediciones.isbn', 'LIKE', "%{$buscar}%");
            });
        }

        if ($request->filled('genero')) {
            $query->where('libros.genero_principal_id', $request->genero);
        }

        if ($request->filled('clasificacion')) {
            $query->where('libros.clasificacion_id', $request->clasificacion);
        }

        $ediciones = $query->orderBy('libros.titulo', 'asc')->get();

        // Obtener autores por libro
        $libroIds = $ediciones->pluck('libro_id')->unique()->toArray();

        $autoresPorLibro = DB::table('asigna_autores')
            ->join('autores', 'asigna_autores.autor_id', '=', 'autores.id')
            ->join('personas', 'autores.persona_id', '=', 'personas.id')
            ->whereIn('asigna_autores.libro_id', $libroIds)
            ->whereNull('autores.deleted_at')
            ->select(
                'asigna_autores.libro_id',
                DB::raw("personas.nombre || ' ' || personas.apellido_paterno as nombre_completo")
            )
            ->get()
            ->groupBy('libro_id');

        // Subgéneros por libro
        $subgenerosPorLibro = DB::table('asigna_subgenero')
            ->join('subgeneros', 'asigna_subgenero.subgenero_id', '=', 'subgeneros.id')
            ->whereIn('asigna_subgenero.libro_id', $libroIds)
            ->whereNull('asigna_subgenero.deleted_at')
            ->select('asigna_subgenero.libro_id', 'subgeneros.nombre')
            ->get()
            ->groupBy('libro_id');

        // Catálogos para filtros
        $generosCatalogo = DB::table('generos')->whereNull('deleted_at')->orderBy('nombre')->get();
        $clasificacionesCatalogo = DB::table('clasificaciones')->whereNull('deleted_at')->orderBy('nombre')->get();

        return view('catalogo.index', compact(
            'ediciones', 'autoresPorLibro', 'subgenerosPorLibro', 'generosCatalogo', 'clasificacionesCatalogo'
        ));
    }
}

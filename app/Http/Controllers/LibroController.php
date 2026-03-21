<?php

namespace App\Http\Controllers;

use App\Models\libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $libros = DB::table('libros')
            ->leftJoin('clasificaciones', 'libros.clasificacion_id', '=', 'clasificaciones.id')
            ->leftJoin('generos', 'libros.genero_principal_id', '=', 'generos.id')
            ->select(
                'libros.*',
                'clasificaciones.nombre as nombre_clasificacion',
                'generos.nombre as nombre_genero'
            )
            ->orderBy('libros.titulo', 'asc')
            ->paginate(10)->withQueryString();

        return view('libros.index', compact('libros'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(libro $cr)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(libro $cr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, libro $cr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(libro $cr)
    {
        //
    }
}

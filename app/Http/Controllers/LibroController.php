<?php

namespace App\Http\Controllers;

use App\Models\libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibroController extends Controller
{
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
            ->paginate(10)
            ->withQueryString();

        $clasificacionesCatalogo = DB::table('clasificaciones')
            ->select('id', 'nombre')
            ->whereNull('deleted_at')
            ->orderBy('nombre', 'asc')
            ->get();

        $generosCatalogo = DB::table('generos')
            ->select('id', 'nombre')
            ->whereNull('deleted_at')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('libros.index', compact('libros', 'clasificacionesCatalogo', 'generosCatalogo'));
    }

    public function create()
    {
        return redirect()->route('libros.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'clasificacion_id' => 'required|integer|exists:clasificaciones,id',
            'anio_publicacion_original' => 'required|integer',
            'genero_principal_id' => 'required|integer|exists:generos,id',
        ]);

        DB::table('libros')->insert([
            'titulo' => trim($request->titulo),
            'sinopsis' => trim($request->sinopsis),
            'clasificacion_id' => $request->clasificacion_id,
            'anio_publicacion_original' => $request->anio_publicacion_original,
            'genero_principal_id' => $request->genero_principal_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('libros.index')->with('status', 'Libro registrado correctamente.');
    }

    public function show(libro $cr)
    {
        return redirect()->route('libros.index');
    }

    public function edit(libro $cr)
    {
        return redirect()->route('libros.index');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'clasificacion_id' => 'required|integer|exists:clasificaciones,id',
            'anio_publicacion_original' => 'required|integer',
            'genero_principal_id' => 'required|integer|exists:generos,id',
        ]);

        DB::table('libros')
            ->where('id', $id)
            ->update([
                'titulo' => trim($request->titulo),
                'sinopsis' => trim($request->sinopsis),
                'clasificacion_id' => $request->clasificacion_id,
                'anio_publicacion_original' => $request->anio_publicacion_original,
                'genero_principal_id' => $request->genero_principal_id,
                'updated_at' => now(),
            ]);

        return redirect()->route('libros.index')->with('status', 'Libro actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        DB::table('libros')
            ->where('id', $id)
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('libros.index')->with('status', 'Libro eliminado correctamente.');
    }
}

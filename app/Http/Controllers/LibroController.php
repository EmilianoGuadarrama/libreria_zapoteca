<?php

namespace App\Http\Controllers;

use App\Models\Libro;
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
            ->whereNull('libros.deleted_at')
            ->orderBy('libros.titulo', 'asc')
            ->get();

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
        $reglas = [
            'titulo' => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'clasificacion_id' => 'required|integer|exists:clasificaciones,id',
            'anio_publicacion_original' => 'required|integer|min:1000|max:' . date('Y'),
            'genero_principal_id' => 'required|integer|exists:generos,id',
        ];

        $mensajes = [
            'titulo.required' => 'El título del libro es obligatorio.',
            'titulo.max' => 'El título es demasiado largo (máximo 255 caracteres).',
            'sinopsis.required' => 'La sinopsis es obligatoria.',
            'clasificacion_id.required' => 'Debes seleccionar una clasificación de la lista.',
            'clasificacion_id.exists' => 'La clasificación seleccionada no es válida.',
            'anio_publicacion_original.required' => 'El año de publicación es obligatorio.',
            'anio_publicacion_original.integer' => 'El año debe ser un número entero.',
            'anio_publicacion_original.min' => 'El año de publicación debe ser mayor a 1000.',
            'anio_publicacion_original.max' => 'El año de publicación no puede ser en el futuro.',
            'genero_principal_id.required' => 'Debes seleccionar un género principal.',
            'genero_principal_id.exists' => 'El género seleccionado no es válido.',
        ];

        $request->validate($reglas, $mensajes);

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

    public function show(Libro $libro)
    {
        return redirect()->route('libros.index');
    }

    public function edit(Libro $libro)
    {
        return redirect()->route('libros.index');
    }

    public function update(Request $request, string $id)
    {
        $reglas = [
            'titulo' => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'clasificacion_id' => 'required|integer|exists:clasificaciones,id',
            'anio_publicacion_original' => 'required|integer|min:1000|max:' . date('Y'),
            'genero_principal_id' => 'required|integer|exists:generos,id',
        ];

        $mensajes = [
            'titulo.required' => 'El título del libro es obligatorio.',
            'titulo.max' => 'El título es demasiado largo (máximo 255 caracteres).',
            'sinopsis.required' => 'La sinopsis es obligatoria.',
            'clasificacion_id.required' => 'Debes seleccionar una clasificación de la lista.',
            'clasificacion_id.exists' => 'La clasificación seleccionada no es válida.',
            'anio_publicacion_original.required' => 'El año de publicación es obligatorio.',
            'anio_publicacion_original.integer' => 'El año debe ser un número entero.',
            'anio_publicacion_original.min' => 'El año de publicación debe ser mayor a 1000.',
            'anio_publicacion_original.max' => 'El año de publicación no puede ser en el futuro.',
            'genero_principal_id.required' => 'Debes seleccionar un género principal.',
            'genero_principal_id.exists' => 'El género seleccionado no es válido.',
        ];

        $request->validate($reglas, $mensajes);

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

    public function destroy($id)
    {
        $libro = Libro::find($id);

        if ($libro) {
            $libro->delete();
            return redirect()->route('libros.index')->with('status', 'Libro eliminado correctamente.');
        }

        return redirect()->route('libros.index')->with('error', 'No se pudo encontrar el libro.');
    }
}

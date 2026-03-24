<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Autor;
use Illuminate\Http\Request;

class AsignaAutorController extends Controller
{
    public function index()
    {
        $libros = Libro::with('autores.persona')->orderBy('titulo', 'asc')->get();
        $autores = Autor::with('persona')->get();
        return view('asigna_autores.index', compact('libros', 'autores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'libro_id' => 'required|exists:libros,id',
            'autor_ids' => 'required|array',
        ]);

        $libro = Libro::findOrFail($request->libro_id);
        // syncWithoutDetaching suma autores sin borrar los que ya existen
        $libro->autores()->syncWithoutDetaching($request->autor_ids);

        return redirect()->route('asigna_autor.index')->with('status', '¡Autor(es) asignados correctamente!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'old_autor_id' => 'required|exists:autores,id',
            'new_autor_id' => 'required|exists:autores,id',
        ]);

        $libro = Libro::findOrFail($id);
        // Quitamos el viejo y ponemos el nuevo individualmente
        $libro->autores()->detach($request->old_autor_id);
        $libro->autores()->attach($request->new_autor_id);

        return redirect()->route('asigna_autor.index')->with('status', '¡Asignación actualizada!');
    }

    public function destroy(Request $request, $id)
    {
        $libro = Libro::findOrFail($id);
        $libro->autores()->detach($request->autor_id);
        return redirect()->route('asigna_autor.index')->with('status', '¡Autor desvinculado!');
    }
}
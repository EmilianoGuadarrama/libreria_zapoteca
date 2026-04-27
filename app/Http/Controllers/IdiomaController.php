<?php

namespace App\Http\Controllers;

use App\Models\Idioma;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class IdiomaController extends Controller
{
    protected $messages = [
        'nombre.required' => 'El nombre del idioma es obligatorio.',
        'nombre.max'      => 'El nombre es muy largo (máximo 200 caracteres).',
        'nombre.unique'   => 'Este idioma ya se encuentra registrado.',
    ];

    public function index()
    {
        $idiomas = Idioma::orderBy('nombre', 'asc')->get();
        return view('idiomas.index', compact('idiomas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:200|unique:idiomas,nombre',
        ], $this->messages);

        try {
            Idioma::create($request->all());
            return redirect()->route('idiomas.index')
                ->with('status', '¡Idioma registrado con éxito!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al conectar con la base de datos.']);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:200|unique:idiomas,nombre,' . $id,
        ], array_merge($this->messages, [
            'nombre.unique' => 'No puedes usar este nombre porque ya existe en otro idioma.',
        ]));

        try {
            $idioma = Idioma::findOrFail($id);
            $idioma->update($request->all());

            return redirect()->route('idiomas.index')
                ->with('status', '¡Idioma actualizado correctamente!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'No se pudo actualizar el registro.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $idioma = Idioma::findOrFail($id);
            $idioma->delete();

            return redirect()->route('idiomas.index')
                ->with('status', '¡Idioma eliminado correctamente!');
        } catch (QueryException $e) {
            return redirect()->route('idiomas.index')
                ->withErrors(['error' => 'No se puede eliminar: el idioma tiene ediciones asociadas.']);
        }
    }
}

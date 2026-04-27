<?php

namespace App\Http\Controllers;

use App\Models\Formato;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class FormatoController extends Controller
{
    protected $messages = [
        'nombre.required'     => 'El nombre del formato es obligatorio.',
        'nombre.max'          => 'El nombre es muy largo (máximo 200 caracteres).',
        'nombre.unique'       => 'Este formato ya se encuentra registrado.',
        'descripcion.max'     => 'La descripción es muy larga (máximo 500 caracteres).',
    ];

    public function index()
    {
        $formatos = Formato::orderBy('nombre', 'asc')->get();
        return view('formatos.index', compact('formatos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:200|unique:formatos,nombre',
            'descripcion' => 'nullable|string|max:500',
        ], $this->messages);

        try {
            Formato::create($request->all());
            return redirect()->route('formatos.index')
                ->with('status', '¡Formato registrado con éxito!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al conectar con la base de datos.']);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre'      => 'required|string|max:200|unique:formatos,nombre,' . $id,
            'descripcion' => 'nullable|string|max:500',
        ], array_merge($this->messages, [
            'nombre.unique' => 'No puedes usar este nombre porque ya existe en otro formato.',
        ]));

        try {
            $formato = Formato::findOrFail($id);
            $formato->update($request->all());

            return redirect()->route('formatos.index')
                ->with('status', '¡Formato actualizado correctamente!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'No se pudo actualizar el registro.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $formato = Formato::findOrFail($id);
            $formato->delete();

            return redirect()->route('formatos.index')
                ->with('status', '¡Formato eliminado correctamente!');
        } catch (QueryException $e) {
            return redirect()->route('formatos.index')
                ->withErrors(['error' => 'No se puede eliminar: el formato tiene ediciones asociadas.']);
        }
    }
}

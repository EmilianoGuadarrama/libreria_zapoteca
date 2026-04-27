<?php

namespace App\Http\Controllers;

use App\Models\Editorial;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class EditorialController extends Controller
{
    protected $messages = [
        'nombre.required'   => 'El nombre de la editorial es obligatorio.',
        'nombre.max'        => 'El nombre es muy largo (máximo 200 caracteres).',
        'nombre.unique'     => 'Esta editorial ya se encuentra registrada.',
        'pais_id.required'  => 'Debes seleccionar un país.',
        'pais_id.exists'    => 'El país seleccionado no es válido.',
        'correo.email'      => 'El correo electrónico no tiene un formato válido.',
        'correo.max'        => 'El correo es muy largo (máximo 200 caracteres).',
        'telefono.max'      => 'El teléfono es muy largo (máximo 50 caracteres).',
    ];

    public function index()
    {
        $editoriales = Editorial::with('pais')->orderBy('nombre', 'asc')->get();
        $paises = Pais::orderBy('nombre', 'asc')->get();

        return view('editoriales.index', compact('editoriales', 'paises'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:200|unique:editoriales,nombre',
            'pais_id'  => 'required|exists:paises,id',
            'correo'   => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:50',
        ], $this->messages);

        try {
            Editorial::create($request->all());
            return redirect()->route('editoriales.index')
                ->with('status', '¡Editorial registrada con éxito!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al conectar con la base de datos.']);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre'   => 'required|string|max:200|unique:editoriales,nombre,' . $id,
            'pais_id'  => 'required|exists:paises,id',
            'correo'   => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:50',
        ], array_merge($this->messages, [
            'nombre.unique' => 'No puedes usar este nombre porque ya existe en otra editorial.',
        ]));

        try {
            $editorial = Editorial::findOrFail($id);
            $editorial->update($request->all());

            return redirect()->route('editoriales.index')
                ->with('status', '¡Editorial actualizada correctamente!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'No se pudo actualizar el registro.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $editorial = Editorial::findOrFail($id);
            $editorial->delete();

            return redirect()->route('editoriales.index')
                ->with('status', '¡Editorial eliminada correctamente!');
        } catch (QueryException $e) {
            return redirect()->route('editoriales.index')
                ->withErrors(['error' => 'No se puede eliminar: la editorial tiene ediciones asociadas.']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class PaisController extends Controller
{

    public function index()
    {
        $paises = Pais::orderBy('nombre', 'asc')->get();
        return view('paises.index', compact('paises'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:200|unique:paises,nombre',
        ], [
            'nombre.required' => 'El nombre del país es obligatorio.',
            'nombre.unique' => 'Este país ya se encuentra registrado.',
        ]);

        try {
            Pais::create($request->all());
            return redirect()->route('paises.index')
                ->with('status', '¡País registrado con éxito!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al conectar con la base de datos.']);
        }
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:200|unique:paises,nombre,' . $id,
        ], [
            'nombre.required' => 'El nombre es obligatorio para actualizar.',
            'nombre.unique' => 'Ese nombre de país ya está en uso.',
        ]);

        try {
            $pais = Pais::findOrFail($id);
            $pais->update($request->all());

            return redirect()->route('paises.index')
                ->with('status', '¡País actualizado correctamente!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'No se pudo actualizar el registro.']);
        }
    }


    public function destroy(string $id)
    {
        try {
            $pais = Pais::findOrFail($id);
            $pais->delete();

            return redirect()->route('paises.index')
                ->with('status', '¡País eliminado correctamente!');
        } catch (QueryException $e) {
            return redirect()->route('paises.index')
                ->withErrors(['error' => 'No se puede eliminar: El país tiene nacionalidades o autores asociados.']);
        }
    }
}

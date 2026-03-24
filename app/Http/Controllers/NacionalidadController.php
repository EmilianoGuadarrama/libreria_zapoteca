<?php

namespace App\Http\Controllers;

use App\Models\Nacionalidad;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class NacionalidadController extends Controller
{
    public function index()
    {
        $nacionalidades = Nacionalidad::with('pais')->orderBy('nombre', 'asc')->get();
        $paises = Pais::orderBy('nombre', 'asc')->get();
        return view('nacionalidades.index', compact('nacionalidades', 'paises'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:200|unique:nacionalidades,nombre',
            'pais_id' => 'required|exists:paises,id',
        ]);

        try {
            Nacionalidad::create($request->all());
            return redirect()->route('nacionalidades.index')->with('status', '¡Nacionalidad registrada!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al guardar.']);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:200|unique:nacionalidades,nombre,' . $id,
            'pais_id' => 'required|exists:paises,id',
        ]);

        $nacionalidad = Nacionalidad::findOrFail($id);
        $nacionalidad->update($request->all());
        return redirect()->route('nacionalidades.index')->with('status', '¡Nacionalidad actualizada!');
    }

    public function destroy($id)
    {
        Nacionalidad::findOrFail($id)->delete();
        return redirect()->route('nacionalidades.index')->with('status', '¡Nacionalidad eliminada!');
    }
}
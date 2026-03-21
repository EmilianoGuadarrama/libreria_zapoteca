<?php

namespace App\Http\Controllers;

use App\Models\Clasificacion;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ClasificacionController extends Controller
{
    public function index()
    {
        // Trae todos los registros para que DataTables los maneje
        $clasificaciones = Clasificacion::orderBy('nombre', 'asc')->get();
        return view('clasificaciones.index', compact('clasificaciones'));
    }

    // ELIMINAMOS EL METODO create()

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:clasificaciones,nombre',
        ], [
            'nombre.required' => 'El nombre de la clasificación es obligatorio.',
            'nombre.unique' => 'Esta clasificación ya existe en el sistema.',
        ]);

        try {
            Clasificacion::create($request->all());
            return redirect()->route('clasificaciones.index')
                ->with('status', '¡Clasificación creada con éxito!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al guardar en la base de datos. Intente de nuevo.']);
        }
    }

    // ELIMINAMOS EL METODO edit()

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:clasificaciones,nombre,' . $id,
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'El nombre ya está en uso por otra clasificación.',
        ]);

        try {
            $clasificacion = Clasificacion::findOrFail($id);
            $clasificacion->update($request->all());

            return redirect()->route('clasificaciones.index')
                ->with('status', '¡Clasificación editada correctamente!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'No se pudo actualizar la clasificación.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $clasificacion = Clasificacion::findOrFail($id);
            $clasificacion->delete();

            return redirect()->route('clasificaciones.index')
                ->with('status', '¡Clasificación eliminada correctamente!');
        } catch (QueryException $e) {
            return redirect()->route('clasificaciones.index')
                ->withErrors(['error' => 'No se puede eliminar: Esta clasificación está siendo utilizada por uno o más libros.']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PromocionController extends Controller
{
    public function index()
    {
        $promociones = DB::table('promociones as p')
            ->leftJoin('usuarios as u', 'p.autorizado_por_id', '=', 'u.id')
            ->leftJoin('personas as per', 'u.persona_id', '=', 'per.id_persona')
            ->select('p.*', 'per.nombre as nombre_autorizado', 'per.apellido_paterno as ape_paterno')
            ->whereNull('p.deleted_at')
            ->orderBy('p.nombre', 'asc')
            ->get();

        return view('promociones.index', compact('promociones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'               => 'required|string|max:200',
            'fecha_inicio'         => 'required|date',
            'fecha_final'          => 'required|date|after_or_equal:fecha_inicio',
            'porcentaje_descuento' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $data = $request->all();
            $data['autorizado_por_id'] = 1; // Seguridad
            Promocion::create($data);
            return redirect()->route('promociones.index')->with('status', 'Promoción creada correctamente.');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al guardar en la base de datos.']);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre'               => 'required|string|max:200',
            'fecha_inicio'         => 'required|date',
            'fecha_final'          => 'required|date|after_or_equal:fecha_inicio',
            'porcentaje_descuento' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $promocion = Promocion::findOrFail($id);
            $promocion->update($request->only(['nombre', 'fecha_inicio', 'fecha_final', 'porcentaje_descuento']));
            return redirect()->route('promociones.index')->with('status', 'Promoción actualizada.');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Error al actualizar la promoción.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $promocion = Promocion::findOrFail($id);
            $promocion->delete();
            return redirect()->route('promociones.index')->with('status', 'Promoción eliminada.');
        } catch (QueryException $e) {
            return redirect()->route('promociones.index')->withErrors(['error' => 'No se puede eliminar: ya está asignada a libros.']);
        }
    }
}

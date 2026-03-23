<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Edicion;
use App\Models\Compra;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class LoteController extends Controller
{
    public function index()
    {
        // Traemos los lotes con sus relaciones para la tabla
        $lotes = Lote::with(['edicion.libro', 'compra', 'ubicacion', 'usuario'])
                     ->orderBy('id', 'desc')
                     ->paginate(10);
                     
        // Traemos catálogos para llenar los <select> del modal
        $ediciones = Edicion::with('libro')->get();
        $compras = Compra::orderBy('id', 'desc')->get();
        $ubicaciones = Ubicacion::all();

        return view('lotes.index', compact('lotes', 'ediciones', 'compras', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'compra_id' => 'required|exists:compras,id',
            'edicion_id' => 'required|exists:ediciones,id',
            'codigo' => 'required|string|max:16|unique:lotes,codigo',
            'cantidad' => 'required|integer|min:1',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear el nuevo lote
            $lote = Lote::create([
                'compra_id' => $request->compra_id,
                'edicion_id' => $request->edicion_id,
                'codigo' => strtoupper($request->codigo),
                'cantidad' => $request->cantidad,
                'usuario_id' => auth()->id() ?? 1, // Cambiar por auth()->id() real
                'ubicacion_id' => $request->ubicacion_id,
            ]);

            // 2. Sumar el stock a la tabla de ediciones (Inventario global)
            $edicion = Edicion::findOrFail($request->edicion_id);
            $edicion->existencias += $request->cantidad;
            $edicion->save();

            DB::commit();

            return redirect()->route('lotes.index')->with('success', 'Lote registrado y stock actualizado exitosamente.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el lote: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'codigo' => 'required|string|max:16|unique:lotes,codigo,' . $id,
            'cantidad' => 'required|integer|min:0',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
        ]);

        try {
            DB::beginTransaction();

            $lote = Lote::findOrFail($id);

            // Si la cantidad cambió, ajustamos el inventario global de la edición
            if ($lote->cantidad != $request->cantidad) {
                // Calculamos la diferencia (Ej: Si había 10 y ahora 15, la diferencia es +5. Si ahora es 8, la diferencia es -2)
                $diferencia = $request->cantidad - $lote->cantidad;
                
                $edicion = Edicion::findOrFail($lote->edicion_id);
                $edicion->existencias += $diferencia;
                $edicion->save();
            }

            // Actualizamos el lote
            $lote->update([
                'codigo' => strtoupper($request->codigo),
                'cantidad' => $request->cantidad,
                'ubicacion_id' => $request->ubicacion_id,
            ]);

            DB::commit();

            return redirect()->route('lotes.index')->with('success', 'Lote actualizado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el lote: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $lote = Lote::findOrFail($id);

            // Antes de borrar el lote, restamos su cantidad actual de las existencias globales
            if ($lote->cantidad > 0) {
                $edicion = Edicion::findOrFail($lote->edicion_id);
                $edicion->existencias -= $lote->cantidad;
                $edicion->save();
            }

            $lote->delete();

            DB::commit();

            return redirect()->route('lotes.index')->with('success', 'Lote eliminado y stock ajustado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el lote: ' . $e->getMessage());
        }
    }
}
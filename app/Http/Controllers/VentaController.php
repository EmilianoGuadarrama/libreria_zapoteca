<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('usuario')->orderBy('id', 'desc')->paginate(10);
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        return view('ventas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'folio' => 'required|max:50|unique:ventas,folio',
            'usuario_id' => 'required',
            'fecha' => 'required',
            'total' => 'required|numeric',
            'monto_recibido' => 'nullable|numeric',
            'cambio' => 'nullable|numeric'
        ]);

        $data = $request->all();
        $data['fecha'] = Carbon::parse($request->fecha)->format('Y-m-d H:i:s');

        Venta::create($data);

        return redirect()->route('ventas.index')->with('success', 'Venta registrada correctamente');
    }

    public function show(string $id)
    {
        $venta = Venta::with('usuario', 'detallesVentas.lote.edicion.libro')->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }

    public function edit(string $id)
    {
        $venta = Venta::findOrFail($id);
        return view('ventas.edit', compact('venta'));
    }

    public function update(Request $request, string $id)
    {
        $venta = Venta::findOrFail($id);

        $request->validate([
            'folio' => 'required|max:50|unique:ventas,folio,' . $venta->id,
            'usuario_id' => 'required',
            'fecha' => 'required',
            'total' => 'required|numeric',
            'monto_recibido' => 'nullable|numeric',
            'cambio' => 'nullable|numeric'
        ]);

        $data = $request->all();
        $data['fecha'] = Carbon::parse($request->fecha)->format('Y-m-d H:i:s');

        $venta->update($data);

        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente');
    }

    public function destroy(string $id)
    {
        $venta = Venta::findOrFail($id);
        $venta->delete();

        return redirect()->route('ventas.index')->with('success', 'Venta eliminada correctamente');
    }
}
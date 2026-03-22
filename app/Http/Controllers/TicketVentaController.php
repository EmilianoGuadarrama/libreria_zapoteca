<?php

namespace App\Http\Controllers;

use App\Models\Venta;

class TicketVentaController extends Controller
{
    public function show(string $id)
    {
        $venta = Venta::with('usuario', 'detallesVentas.lote.edicion.libro')->findOrFail($id);
        return view('ventas.ticket', compact('venta'));
    }
}
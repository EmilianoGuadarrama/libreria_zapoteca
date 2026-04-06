<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    
    public function indexPendientes()
    {
        $pendientes = User::with(['persona', 'rol'])
            ->where('estado', 'Pendiente')
            ->get();
        return view('admin.pendientes', compact('pendientes'));
    }

    public function activarUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);
            $usuario->estado = 'Activo'; // Valor permitido en tu tabla usuarios
            $usuario->save();

            return redirect()->back()->with('status', 'El usuario ha sido autorizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'No se pudo activar al usuario: ' . $e->getMessage()]);
        }
    }

    public function rechazar($id)
    {
        try {
            $usuario = User::findOrFail($id);
            
            $usuario->delete();

            return redirect()->back()->with('status', 'La solicitud ha sido rechazada y eliminada.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar la solicitud.']);
        }
    }
}
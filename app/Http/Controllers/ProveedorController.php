<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function index()
    {
        // 1. Traemos los proveedores (con el join para el nombre del contacto)
        $proveedores = DB::table('proveedores')
            ->join('personas', 'proveedores.persona_contacto_id', '=', 'personas.id')
            ->select(
                'proveedores.id',
                'proveedores.nombre as empresa',
                'proveedores.correo',
                'proveedores.telefono',
                'proveedores.estado',
                'proveedores.persona_contacto_id', // Necesario para seleccionar el actual en el modal
                DB::raw("personas.nombre || ' ' || personas.apellido_paterno as nombre_contacto")
            )
            ->whereNull('proveedores.deleted_at')
            ->get();

        // 2. Traemos a todas las personas para llenar los <select> de los modales
        $personas = DB::table('personas')
            ->select('id', DB::raw("nombre || ' ' || apellido_paterno as nombre_completo"))
            ->whereNull('deleted_at')
            ->get();

        // 3. NUEVO: Traemos el historial de libros surtidos cruzando las tablas de compras
        // Solo contamos las compras que ya tienen el estado 'Recibida'
        $librosSurtidos = DB::table('compras')
            ->join('detalles_compras', 'compras.id', '=', 'detalles_compras.compra_id')
            ->join('ediciones', 'detalles_compras.edicion_id', '=', 'ediciones.id')
            ->join('libros', 'ediciones.libro_id', '=', 'libros.id')
            ->where('compras.estado', 'Recibida') 
            ->select(
                'compras.proveedor_id',
                'libros.titulo',
                'ediciones.isbn',
                DB::raw('SUM(detalles_compras.cantidad) as total_ejemplares')
            )
            ->groupBy('compras.proveedor_id', 'libros.titulo', 'ediciones.isbn')
            ->get()
            ->groupBy('proveedor_id'); // Agrupamos la colección resultante por proveedor_id para la vista

        // Mandamos las 3 variables a la vista index
        return view('proveedores.index', compact('proveedores', 'personas', 'librosSurtidos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'              => 'required|string|max:200',
            'persona_contacto_id' => 'required|integer',
            'correo'              => 'required|email|max:200',
            'telefono'            => 'nullable|string|max:16',
            'estado'              => 'required|string|max:20'
        ]);

        DB::table('proveedores')->where('id', $id)->update([
            'nombre'              => $request->nombre,
            'persona_contacto_id' => $request->persona_contacto_id,
            'correo'              => $request->correo,
            'telefono'            => $request->telefono,
            'estado'              => $request->estado,
            'updated_at'          => now()
        ]);

        return redirect()->route('proveedores.index')->with('status', '¡Proveedor actualizado correctamente!');
    }

    public function destroy($id)
    {
        // Borrado lógico (Soft Delete)
        DB::table('proveedores')->where('id', $id)->update([
            'deleted_at' => now(),
            'estado'     => 'Inactivo'
        ]);

        return redirect()->route('proveedores.index')->with('status', '¡Proveedor eliminado del sistema!');
    }

    public function store(Request $request)
    {
        // Validamos que la info que manden esté correcta
        $request->validate([
            'nombre'              => 'required|string|max:200',
            'persona_contacto_id' => 'required|integer',
            'correo'              => 'required|email|max:200',
            'telefono'            => 'nullable|string|max:16',
            'estado'              => 'required|string|max:20'
        ]);

        // Insertamos el nuevo registro directo en la base de datos
        DB::table('proveedores')->insert([
            'nombre'              => $request->nombre,
            'persona_contacto_id' => $request->persona_contacto_id,
            'correo'              => $request->correo,
            'telefono'            => $request->telefono,
            'estado'              => $request->estado,
            'created_at'          => now(),
            'updated_at'          => now()
        ]);

        return redirect()->route('proveedores.index')->with('status', '¡Proveedor agregado correctamente!');
    }
}
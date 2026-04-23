<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function index()
    {
        // 1. Traemos los proveedores (Usamos LEFT JOIN para atrapar proveedores huérfanos)
        $proveedores = DB::table('proveedores')
            ->leftJoin('personas', 'proveedores.persona_contacto_id', '=', 'personas.id')
            ->select(
                'proveedores.id',
                'proveedores.nombre as empresa',
                'proveedores.correo',
                'proveedores.telefono',
                'proveedores.estado',
                'proveedores.persona_contacto_id',
                // Si la persona fue borrada a la fuerza, esto regresará NULL y la vista lo pintará de rojo
                DB::raw("personas.nombre || ' ' || personas.apellido_paterno as nombre_contacto")
            )
            ->whereNull('proveedores.deleted_at')
            ->get();

        // 2. Traemos a todas las personas para llenar los <select> de los modales
        $personas = DB::table('personas')
            ->select('id', DB::raw("nombre || ' ' || apellido_paterno as nombre_completo"))
            ->whereNull('deleted_at')
            ->get();

        // 3. Traemos el historial de libros surtidos cruzando las tablas de compras
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
            ->groupBy('proveedor_id'); 

        return view('proveedores.index', compact('proveedores', 'personas', 'librosSurtidos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'              => 'required|string|min:3|max:200',
            'persona_contacto_id' => 'required|integer|exists:personas,id',
            'correo'              => 'required|email|max:200|unique:proveedores,correo,' . $id,
            'telefono'            => ['nullable', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'estado'              => 'required|string|in:Activo,Inactivo'
        ], [
            'telefono.regex'             => 'El teléfono solo debe contener números y tener entre 10 y 15 dígitos.',
            'correo.unique'              => 'Este correo electrónico ya está registrado en otro proveedor.',
            'persona_contacto_id.exists' => 'La persona de contacto seleccionada no es válida o fue eliminada.',
            'estado.in'                  => 'El estado seleccionado no es válido.'
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
        DB::table('proveedores')->where('id', $id)->update([
            'deleted_at' => now(),
            'estado'     => 'Inactivo'
        ]);

        return redirect()->route('proveedores.index')->with('status', '¡Proveedor eliminado del sistema!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'              => 'required|string|min:3|max:200',
            'persona_contacto_id' => 'required|integer|exists:personas,id',
            'correo'              => 'required|email|max:200|unique:proveedores,correo',
            'telefono'            => ['nullable', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'estado'              => 'required|string|in:Activo,Inactivo'
        ], [
            'telefono.regex'             => 'El teléfono solo debe contener números y tener entre 10 y 15 dígitos.',
            'correo.unique'              => 'Este correo electrónico ya está registrado por otro proveedor.',
            'persona_contacto_id.exists' => 'La persona de contacto seleccionada no existe en la base de datos.',
            'estado.in'                  => 'Debes seleccionar un estado válido (Activo o Inactivo).'
        ]);

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
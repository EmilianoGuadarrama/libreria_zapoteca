<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Persona;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        // Cargamos relaciones en minúsculas (asegúrate que así estén en tus modelos)
        $usuarios = User::with(['persona', 'rol'])->get();
        $roles = Rol::all(); 
        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'genero'           => 'required',
            'rol_id'           => 'required|exists:roles,id',
            'correo'           => 'required|email|unique:usuarios,correo', // minúscula
            'contrasena'       => 'required|min:6',
            'estado'           => 'required|in:Activo,Inactivo,Rechasado,Despedido,Pendiente'
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear Persona con llaves en minúsculas
            $persona = Persona::create([
                'nombre'           => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'genero'           => $request->genero,
            ]);

            // 2. Crear Usuario con llaves en minúsculas
            User::create([
                'persona_id' => $persona->id, 
                'rol_id'     => $request->rol_id, 
                'correo'     => $request->correo,
                'contrasena' => Hash::make($request->contrasena),
                'estado'     => $request->estado 
            ]);

            DB::commit();
            return redirect()->back()->with('status', '¡Usuario creado con éxito!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'rol_id'           => 'required',
            'correo'           => 'required|email|unique:usuarios,correo,'.$id,
            'estado'           => 'required|in:Activo,Inactivo,Rechasado,Despedido,Pendiente'
        ]);

        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($id);

            // Actualizar Persona (llaves en minúsculas)
            if ($user->persona) {
                $user->persona->update([
                    'nombre'           => $request->nombre,
                    'apellido_paterno' => $request->apellido_paterno,
                    'apellido_materno' => $request->apellido_materno,
                    'genero'           => $request->genero,
                ]);
            }

            // Actualizar Usuario manualmente para asegurar que Oracle reciba el cambio
            $user->correo = $request->correo;
            $user->rol_id = $request->rol_id;
            $user->estado = $request->estado; // <--- FORZAMOS LA ASIGNACIÓN

            if ($request->filled('contrasena')) {
                $user->contrasena = Hash::make($request->contrasena);
            }

            $user->save();

            DB::commit();
            return redirect()->back()->with('status', 'Actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

   public function destroy($id)
{
    try {
        DB::beginTransaction();
        
        $user = User::findOrFail($id);
        $persona = $user->persona;

        // Intentar borrar el usuario
        $user->delete();

        if ($persona) {
            $persona->delete();
        }

        DB::commit();
        return redirect()->back()->with('status', 'Registro eliminado correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();

        // Verificamos si es el error de integridad de Oracle (Código 2292)
        if (str_contains($e->getMessage(), '2292')) {
            return redirect()->back()->with('error_lila', 'No se puede eliminar: Este usuario tiene historial de VENTAS o registros vinculados. Se recomienda cambiar su estado a "Inactivo" o "Despedido" en lugar de borrarlo.');
        }

        return redirect()->back()->with('error_lila', 'Ocurrió un error inesperado al intentar eliminar.');
    }
}
}
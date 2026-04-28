<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\User;

class AuthController extends Controller
{
    // Muestra el formulario de login con estética Glassmorphism
     
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Maneja la autenticación y redirecciona según el Rol
     
    public function login(Request $request)
    {
        $request->validate([
            'correo'      => 'required|email',
            'contrasena'  => 'required|string',
        ], [
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email'    => 'Ingresa un formato de correo válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        $credenciales = [
            'correo'   => $request->correo,
            'password' => $request->contrasena,
            'estado'   => 'Activo' // Solo permitimos entrar a usuarios activos
        ];

        if (Auth::attempt($credenciales, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Mensaje personalizado según el rol
            $mensaje = ($user->rol_id == 3) 
                ? '¡Qué gusto verte de nuevo! Listo para las ventas del día.' 
                : '¡Bienvenido al panel de administración, ' . $user->persona->nombre . '!';

            return redirect()->route('dashboard')->with('success', $mensaje);
        }

        // Si no entró, revisamos si es porque está pendiente
        $usuarioExistente = User::where('correo', $request->correo)->first();
        if ($usuarioExistente && $usuarioExistente->estado === 'Pendiente') {
            return redirect()->route('login')
                ->withErrors(['correo' => 'Tu cuenta aún requiere la aprobación de un administrador.'])
                ->withInput();
        }

        return redirect()->route('login')
            ->withErrors(['correo' => 'Las credenciales no coinciden o la cuenta está inactiva.'])
            ->withInput($request->only('correo'));
    }

    // Cierra la sesión de forma segura
     
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Muestra el formulario de registro
     
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Registra una nueva persona y su usuario asociado
     
    public function register(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:200',
            'apellido_paterno' => 'required|string|max:200',
            'apellido_materno' => 'required|string|max:200',
            'genero'           => 'required|in:Hombre,Mujer,Otro',
            'correo'           => 'required|email|unique:usuarios,correo',
            'contrasena'       => 'required|string|min:6|confirmed',
            'rol_id'           => 'required|in:1,2,3', // 1:Admin, 2:Gerente, 3:Vendedor
        ], [
            'nombre.required'           => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_materno.required' => 'El apellido materno es obligatorio.',
            'genero.required'           => 'Debes seleccionar un género.',
            'correo.required'           => 'El correo es obligatorio.',
            'correo.unique'             => 'Este correo ya está registrado en Zapoteca.',
            'contrasena.required'       => 'La contraseña es obligatoria.',
            'contrasena.min'            => 'La contraseña debe tener al menos 6 caracteres.',
            'contrasena.confirmed'      => 'Las contraseñas no coinciden.',
            'rol_id.required'           => 'El tipo de cuenta es obligatorio.',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Creamos los datos personales
                $persona = Persona::create([
                    'nombre'           => $request->nombre,
                    'apellido_paterno' => $request->apellido_paterno,
                    'apellido_materno' => $request->apellido_materno,
                    'genero'           => $request->genero,
                ]);

                // 2. Definimos estado: Solo el Vendedor (Rol 3) entra directo
                $estadoInicial = ($request->rol_id == 3) ? 'Activo' : 'Pendiente';

                // 3. Creamos el usuario vinculado
                $usuario = User::create([
                    'persona_id' => $persona->id,
                    'correo'     => $request->correo,
                    'contrasena' => Hash::make($request->contrasena),
                    'estado'     => $estadoInicial,
                    'rol_id'     => $request->rol_id,
                ]);

                if ($usuario->estado === 'Activo') {
                    Auth::login($usuario);
                    return redirect()->route('admin.dashboard')
                        ->with('success', '¡Bienvenida(o) a Zapoteca! Tu cuenta ha sido creada y activada.');
                }

                // Mensaje para Admin/Gerente que deben esperar aprobación
                return redirect('/')->with('info', 'Registro exitoso. Por seguridad, un administrador debe activar tu cuenta de nivel superior.');
            });
        } catch (\Exception $e) {
            return redirect()->route('register')
                ->withErrors(['correo' => 'Hubo un problema al crear tu cuenta: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Métodos de gestión para el Administrador
    
    public function indexPendientes()
    {
        $pendientes = User::with(['persona', 'rol'])
            ->where('estado', 'Pendiente')
            ->get();

        return view('admin.pendientes', compact('pendientes'));
    }

    public function activarUsuario($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['estado' => 'Activo']);
        
        return back()->with('success', 'El usuario ' . $usuario->persona->nombre . ' ha sido activado.');
    }
}
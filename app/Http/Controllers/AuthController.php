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
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo'      => 'required|email',
            'contrasena'  => 'required|string',
        ]);

        if (Auth::attempt([
            'correo'   => $credentials['correo'],
            'password' => $credentials['contrasena'],
            'estado'   => 'Activo'
        ], $request->filled('remember'))) {

            $request->session()->regenerate();
            $user = Auth::user();

            if (in_array($user->rol_id, [1, 2])) {
                return redirect()->intended('/dashboard');
            }

            return redirect('/')->with('success', '¡Qué gusto verte de nuevo en Zapoteca!');
        }

        $usuarioExistente = User::where('correo', $request->correo)->first();
        if ($usuarioExistente && $usuarioExistente->estado === 'Pendiente') {
            return back()->withErrors([
                'correo' => 'Tu cuenta de staff aún requiere la aprobación de un administrador.',
            ])->onlyInput('correo');
        }

        return back()->withErrors([
            'correo' => 'Las credenciales no coinciden o la cuenta está inactiva.',
        ])->onlyInput('correo');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:200',
            'apellido_paterno' => 'required|string|max:200',
            'genero'           => 'required|in:Hombre,Mujer,Otro',
            'correo'           => 'required|email|unique:usuarios,correo',
            'contrasena'       => 'required|string|min:6|confirmed',
            'rol_id'           => 'required|in:1,2,3', // El usuario elige su rol en el form
        ]);

        try {
            return DB::transaction(function () use ($request) {

                $persona = Persona::create([
                    'nombre'           => $request->nombre,
                    'apellido_paterno' => $request->apellido_paterno,
                    'apellido_materno' => $request->apellido_materno,
                    'genero'           => $request->genero,
                ]);

                $estadoInicial = ($request->rol_id == 3) ? 'Activo' : 'Pendiente';

                $usuario = User::create([
                    'persona_id' => $persona->id,
                    'correo'     => $request->correo,
                    'contrasena' => Hash::make($request->contrasena),
                    'estado'     => $estadoInicial,
                    'rol_id'     => $request->rol_id,
                ]);

                if ($usuario->estado === 'Activo') {
                    Auth::login($usuario);
                    return redirect('/')->with('success', '¡Bienvenido a Zapoteca! Ya puedes navegar.');
                }
                return redirect()->route('login')->with('info', 'Registro de personal recibido. Espera a que un Administrador active tu cuenta.');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar: ' . $e->getMessage()])->withInput();
        }
    }
    public function indexPendientes()
    {
        // Buscamos usuarios con estado Pendiente y cargamos su relación con Persona y Rol
        $pendientes = User::with(['persona', 'rol'])
            ->where('estado', 'Pendiente')
            ->get();

        return view('admin.pendientes', compact('pendientes'));
    }

    public function activarUsuario($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['estado' => 'Activo']);
        return back()->with('success', '¡Usuario activado! Ahora puede iniciar sesión.');
    }
}

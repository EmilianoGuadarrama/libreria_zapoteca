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
            'estado'   => 'Activo'
        ];

        if (Auth::attempt($credenciales, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (in_array($user->rol_id, [1, 2])) {
                return redirect()->intended('/dashboard');
            }

            return redirect('/')->with('success', '¡Qué gusto verte de nuevo en Zapoteca!');
        }

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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
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
            'apellido_materno' => 'required|string|max:200', // Validación agregada
            'genero'           => 'required|in:Hombre,Mujer,Otro',
            'correo'           => 'required|email|unique:usuarios,correo',
            'contrasena'       => 'required|string|min:6|confirmed',
            'rol_id'           => 'required|in:1,2,3',
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
                    return redirect('/')->with('success', '¡Bienvenido a Zapoteca! Tu cuenta ha sido creada y activada.');
                }

                // Redirección al welcome (/) con el mensaje de espera para roles 1 y 2
                return redirect('/')->with('info', 'Cuenta creada correctamente. Por favor, espera a que un administrador acepte tu cuenta.');
            });
        } catch (\Exception $e) {
            return redirect()->route('register')
                ->withErrors(['correo' => 'Error al registrar: ' . $e->getMessage()])
                ->withInput();
        }
    }

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
        return back()->with('success', '¡Usuario activado correctamente!');
    }
}
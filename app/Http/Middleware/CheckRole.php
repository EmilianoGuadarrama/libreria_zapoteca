<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles_permitidos)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $usuario = Auth::user();

        if (!$usuario->rol) {
            Auth::logout();
            return redirect('/login')->withErrors(['correo' => 'Tu usuario no tiene un rol válido asignado.']);
        }

        $rol_usuario = $usuario->rol->nombre;

        if (in_array($rol_usuario, $roles_permitidos)) {
            return $next($request);
        }

        abort(403, 'Acceso Denegado. Tu rol de "' . $rol_usuario . '" no tiene permiso para ver esta sección.');
    }
}

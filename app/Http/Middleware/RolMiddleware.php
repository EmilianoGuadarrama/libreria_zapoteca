<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $rolUsuario = auth()->user()->rol->nombre;

        if (in_array($rolUsuario, $roles)) {
            return $next($request);
        }

        // Si no tiene permiso, mandamos un 403 (Prohibido)
        abort(403, 'No tienes permisos para acceder a esta área.');
    }
}

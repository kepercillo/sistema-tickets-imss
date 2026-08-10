<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificar autenticación
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Normalizar el rol del usuario a MAYÚSCULAS y sin espacios
        $userRole = trim(mb_strtoupper(Auth::user()->role, 'UTF-8')); 
        $upperRoles = array_map(fn($r) => trim(mb_strtoupper($r, 'UTF-8')), $roles);

        // 3. Evaluar permisos (ADMINISTRADOR tiene acceso global o según los roles permitidos)
        if ($userRole === 'ADMINISTRADOR' || in_array($userRole, $upperRoles)) {
            return $next($request);
        }

        // 4. Bloquear acceso si no coincide el rol
        abort(403, 'NO TIENE PERMISOS PARA ACCEDER A ESTA SECCIÓN.');
    }
}
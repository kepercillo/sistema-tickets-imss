<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarAdscripcion
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si el usuario no está logueado o le falta la CLUES o el Departamento...
        if (!$user || empty($user->clues) || empty($user->department)) {
            return redirect()->route('dashboard')
                ->with('error', 'ACCESO DENEGADO: DEBE CONFIGURAR SU UNIDAD Y DEPARTAMENTO ANTES DE CONTINUAR.');
        }

        return $next($request);
    }
}
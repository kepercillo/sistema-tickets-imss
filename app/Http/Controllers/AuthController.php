<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Mostrar el formulario de Login
    public function showLogin()
    {
        if (Auth::check()) {
            $role = strtoupper(trim(Auth::user()->role));

            if (in_array($role, ['ADMINISTRADOR', 'SOPORTE'])) {
                return redirect()->route('soporte.tickets.index');
            }

            return redirect()->route('tickets.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'username' => Str::lower(trim($request->username)),
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            session()->forget('url.intended');

            // Recuperamos el rol y lo pasamos a mayúsculas por seguridad
            $rol = strtoupper(trim(Auth::user()->role));

            // Si es EMPLEADO, lo mandamos directo a sus tickets
            if ($rol === 'EMPLEADO') {
                return redirect()->route('tickets.index'); 
            }

            // Si es ADMINISTRADOR o SOPORTE, lo mandamos a su panel de gestión
            if (in_array($rol, ['ADMINISTRADOR', 'SOPORTE'])) {
                return redirect()->route('soporte.tickets.index');
            }

            // Por defecto (si hubiera un rol extraño), al dashboard para redirigir
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'username' => 'LAS CREDENCIALES INTRODUCIDAS NO COINCIDEN CON NUESTROS REGISTROS.',
        ])->withInput([
            'username' => $credentials['username']
        ]);
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
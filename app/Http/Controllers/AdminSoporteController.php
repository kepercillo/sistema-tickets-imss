<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminSoporteController extends Controller
{
    /**
     * Muestra el catálogo de técnicos de SOPORTE.
     */
    public function index()
    {
        // Filtramos únicamente a los usuarios con rol SOPORTE
        $tecnicos = User::where('role', 'SOPORTE')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.soporte.index', compact('tecnicos'));
    }

    /**
     * Registra un nuevo técnico de SOPORTE.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.unique' => 'EL NOMBRE DE USUARIO YA ESTÁ REGISTRADO.',
            'email.unique' => 'EL CORREO ELECTRÓNICO YA ESTÁ REGISTRADO.',
            'password.confirmed' => 'LAS CONTRASEÑAS NO COINCIDEN.',
            'password.min' => 'LA CONTRASEÑA DEBE TENER MÍNIMO 6 CARACTERES.',
        ]);

        // Guardamos transformando absolutamente todo el texto a MAYÚSCULAS (excepto el password)
        User::create([
            'name' => mb_strtoupper($request->input('name'), 'UTF-8'),
            'username' => mb_strtoupper($request->input('username'), 'UTF-8'),
            'email' => mb_strtoupper($request->input('email'), 'UTF-8'),
            'role' => 'SOPORTE', // Rol fijo en mayúsculas
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('admin.soporte.index')
            ->with('success', 'TÉCNICO DE SOPORTE REGISTRADO CORRECTAMENTE.');
    }

    /**
     * Elimina un técnico de SOPORTE.
     */
    public function destroy(User $user)
    {
        // Evitamos que un administrador se borre a sí mismo por error de ruta
        if ($user->role !== 'SOPORTE') {
            return redirect()->back()->with('error', 'SOLO SE PERMITE ELIMINAR CUENTAS CON ROL SOPORTE DESDE ESTE PANEL.');
        }

        $user->delete();

        return redirect()->route('admin.soporte.index')
            ->with('success', 'EL TÉCNICO HA SIDO ELIMINADO DEL SISTEMA CORRECTAMENTE.');
    }

    /**
     * Actualiza los datos del técnico de SOPORTE.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed', // Contraseña opcional al editar
        ], [
            'username.unique' => 'EL NOMBRE DE USUARIO YA ESTÁ REGISTRADO.',
            'email.unique' => 'EL CORREO ELECTRÓNICO YA ESTÁ REGISTRADO.',
            'password.confirmed' => 'LAS CONTRASEÑAS NO COINCIDEN.',
            'password.min' => 'LA CONTRASEÑA DEBE TENER MÍNIMO 6 CARACTERES.',
        ]);

        $data = [
            'name' => mb_strtoupper($request->input('name'), 'UTF-8'),
            'username' => mb_strtoupper($request->input('username'), 'UTF-8'),
            'email' => mb_strtoupper($request->input('email'), 'UTF-8'),
        ];

        // Solo cambiamos la contraseña si el administrador escribió una nueva
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        return redirect()->route('admin.soporte.index')
            ->with('success', 'LOS DATOS DEL TÉCNICO HAN SIDO ACTUALIZADOS CORRECTAMENTE.');
    }
}

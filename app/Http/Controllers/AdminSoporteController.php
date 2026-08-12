<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminSoporteController extends Controller
{
    public function index()
    {
        $tecnicos = User::whereIn('role', ['SOPORTE', 'ADMINISTRADOR'])
            ->orderBy('name')
            ->get();

        return view('admin.soporte.index', compact('tecnicos'));
    }

    /**
     * Almacena un nuevo usuario con rol SOPORTE (creación manual).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'name'     => strtoupper($request->name),
            'username' => strtolower($request->username),
            'email'    => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role'     => 'SOPORTE',
            'clues'    => 'ADMIN',
        ]);

        return redirect()->route('admin.soporte.index')
            ->with('success', 'NUEVO TÉCNICO REGISTRADO CORRECTAMENTE.');
    }

    /**
     * Actualiza los datos de un usuario (sin cambiar rol).
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->name     = strtoupper($request->name);
        $user->username = strtolower($request->username);
        $user->email    = strtolower($request->email);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.soporte.index')
            ->with('success', 'DATOS DEL TÉCNICO ACTUALIZADOS CORRECTAMENTE.');
    }

    /**
     * Elimina un usuario (solo si no es el último administrador).
     */
    public function destroy(User $user)
    {
        // Si el usuario es ADMINISTRADOR, verificar que no sea el único
        if (strtoupper($user->role) === 'ADMINISTRADOR') {
            $adminsCount = User::where('role', 'ADMINISTRADOR')->count();
            if ($adminsCount <= 1) {
                return redirect()->back()
                    ->with('error', 'NO SE PUEDE ELIMINAR AL ÚLTIMO ADMINISTRADOR DEL SISTEMA.');
            }
        }

        $user->delete();

        return redirect()->route('admin.soporte.index')
            ->with('success', 'USUARIO ELIMINADO CORRECTAMENTE.');
    }

    /**
     * Obtiene la lista de empleados disponibles para promoción (rol = EMPLEADO).
     */

    public function empleadosDisponibles()
    {
        $empleados = User::where('role', 'EMPLEADO')
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'email']);
        
        return response()->json($empleados);
    }
    /**
     * Asigna el rol de SOPORTE a un empleado existente.
     */
    public function asignarSoporte(User $user)
    {
        if (strtoupper($user->role) !== 'EMPLEADO') {
            return redirect()->back()
                ->with('error', 'ESTE USUARIO YA TIENE UN ROL DE SOPORTE O ADMINISTRADOR.');
        }

        $user->role = 'SOPORTE';
        $user->save();

        return redirect()->route('admin.soporte.index')
            ->with('success', 'EL EMPLEADO ' . strtoupper($user->name) . ' HA SIDO ASCENDIDO A SOPORTE TÉCNICO.');
    }

    /**
     * Revierte un usuario de SOPORTE a EMPLEADO.
     */
    public function revertirEmpleado(User $user)
    {
        if (!in_array(strtoupper($user->role), ['SOPORTE', 'ADMINISTRADOR'])) {
            return redirect()->back()
                ->with('error', 'ESTE USUARIO NO ES SOPORTE NI ADMINISTRADOR.');
        }

        $user->role = 'EMPLEADO';
        $user->save();

        return redirect()->route('admin.soporte.index')
            ->with('success', 'EL USUARIO ' . strtoupper($user->name) . ' HA SIDO REVERTIDO A EMPLEADO.');
    }

    /**
     * Asigna el rol de ADMINISTRADOR a un empleado o soporte existente.
     */
    public function promoverAdmin(User $user)
    {
        // No permitir si ya es ADMINISTRADOR
        if (strtoupper($user->role) === 'ADMINISTRADOR') {
            return redirect()->back()
                ->with('error', 'ESTE USUARIO YA ES ADMINISTRADOR.');
        }

        $user->role = 'ADMINISTRADOR';
        $user->save();

        return redirect()->route('admin.soporte.index')
            ->with('success', 'EL USUARIO ' . strtoupper($user->name) . ' HA SIDO ASCENDIDO A ADMINISTRADOR.');
    }

    /**
     * Revierte un ADMINISTRADOR a SOPORTE (o EMPLEADO) si hay más de 1 administrador.
     */
    public function revertirAdmin(User $user)
    {
        if (strtoupper($user->role) !== 'ADMINISTRADOR') {
            return redirect()->back()
                ->with('error', 'ESTE USUARIO NO ES ADMINISTRADOR.');
        }

        // Verificar que no sea el único administrador
        $adminsCount = User::where('role', 'ADMINISTRADOR')->count();
        if ($adminsCount <= 1) {
            return redirect()->back()
                ->with('error', 'NO SE PUEDE REVERTIR AL ÚLTIMO ADMINISTRADOR DEL SISTEMA.');
        }

        $user->role = 'SOPORTE'; // O podría ser 'EMPLEADO', según prefieras
        $user->save();

        return redirect()->route('admin.soporte.index')
            ->with('success', 'EL ADMINISTRADOR ' . strtoupper($user->name) . ' HA SIDO REVERTIDO A SOPORTE.');
    }

    public function listarEmpleados(Request $request)
    {
        $tipo = $request->input('tipo', 'activos');
        $buscar = $request->input('buscar', '');

        $query = User::query();

        if ($tipo === 'activos') {
            // Activos: solo EMPLEADO con deleted_at null
            $query->where('role', 'EMPLEADO')->whereNull('deleted_at');
        } else {
            // Inactivos: TODOS los usuarios con deleted_at not null (cualquier rol)
            $query->whereNotNull('deleted_at')->withTrashed();
        }

        if (!empty($buscar)) {
            $query->where(function($q) use ($buscar) {
                $q->where('name', 'LIKE', "%{$buscar}%")
                ->orWhere('username', 'LIKE', "%{$buscar}%")
                ->orWhere('email', 'LIKE', "%{$buscar}%");
            });
        }

        $empleados = $query->orderBy('name')->get(['id', 'name', 'username', 'email', 'role', 'deleted_at']);

        return response()->json($empleados);
    }

    public function desactivarEmpleado(User $user)
    {
        if ($user->role !== 'EMPLEADO') {
            return response()->json(['error' => 'Solo se pueden desactivar empleados.'], 422);
        }
        $user->delete();
        return response()->json(['success' => true]);
    }

    public function reactivarEmpleado(User $user)
    {
        if (!$user->trashed()) {
            return response()->json(['error' => 'Este empleado no está desactivado.'], 422);
        }
        $user->restore();
        return response()->json(['success' => true]);
    }

}
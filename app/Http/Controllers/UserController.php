<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use App\Notifications\BienvenidaUsuarioNotification;

class UserController extends Controller
{
    // Mostrar formulario de registro (sirve para Invitados/Empleados o para el Admin)
    public function showRegister()
    {
        // Traemos los catálogos para los combos
        $cluesDefault = 'CSIMB006731';
        
        // Buscamos el nombre de la unidad por defecto
        $unidadDefault = DB::table('catalogo_clues')
            ->where('clues', $cluesDefault)
            ->value('nombre_unidad') ?? 'UNIDAD NO ENCONTRADA';

        $departamentos = DB::table('catalogo_departaments')->get();
        $cluesLista = DB::table('catalogo_clues')->get(); // Por si a futuro se expande a nivel estatal

        return view('auth.register', compact('cluesDefault', 'unidadDefault', 'departamentos', 'cluesLista'));
    }

    public function index()
    {
        // Obtiene únicamente los tickets creados por el usuario en sesión
        $tickets = Ticket::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('tickets.index', compact('tickets'));
    }
    

    // Procesar el almacenamiento del usuario
    public function register(Request $request)
    {
        // 1. Validaciones estrictas (EMAIL obligatorio, único y formato correcto)
        $request->validate([
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'required_without:apellido_paterno|string|max:100',
            'nombres'          => 'required|string|max:100',
            'password'         => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ],
            'clues'            => 'required|string',
            'department'       => 'required|string',
            'email'            => 'required|email|max:255|unique:users,email' // <-- Cambiado a required
        ]);

        // Recolectamos y limpiamos convirtiendo a MAYÚSCULAS para respetar la regla del sistema
        $paterno = Str::upper(trim($request->apellido_paterno));
        $materno = Str::upper(trim($request->apellido_materno));
        $nombres = Str::upper(trim($request->nombres));
        
        // ─── SOLUCIÓN AL ERROR: Obtenemos el email del request y lo estandarizamos en minúsculas ───
        $email = Str::lower(trim($request->email)); 

        // 2. Formatear el campo 'name' -> "PATERNO MATERNO, NOMBRES"
        $apellidos = trim("$paterno $materno");
        $fullName = Str::upper("$apellidos, $nombres");

        // 3. Generar el 'username' base (en minúsculas por estándar de red: apellido.nombre)
        $primerApellido = !empty($paterno) ? $paterno : $materno;
        $primerNombre = explode(' ', $nombres)[0]; 

        // Forzamos explícitamente a minúsculas limpia
        $usernameBase = Str::lower(Str::slug($primerApellido) . '.' . Str::slug($primerNombre));    
        
        // Validar si el username ya existe y calcular consecutivo numérico
        $username = $usernameBase;
        $contador = 1;
        while (User::where('username', $username)->exists()) {
            $username = $usernameBase . $contador;
            $contador++;
        }

        // 4. Determinar el ROL (Si es Admin logueado creando soportes, o registro libre de empleado)
        $role = 'EMPLEADO';
        if (Auth::check() && Auth::user()->role === 'ADMINISTRADOR') {
            $role = 'SOPORTE'; 
        }

        // ─── CORRECCIÓN: Asignamos el resultado a la variable $user para poder usar la notificación ───
        $user = User::create([
            'username'    => $username, 
            'name'        => $fullName,
            'email'       => $email, // <-- Ahora sí existe la variable
            'clues'       => Str::upper($request->clues),
            'department'  => Str::upper($request->department),
            'password'   => Hash::make($request->password),
            'role'        => $role,
        ]);

        // DISPARAR EL CORREO INSTITUCIONAL
        try {
            $user->notify(new BienvenidaUsuarioNotification($user));
        } catch (\Exception $e) {
            // Loguear el error si el servidor de correos falla, pero permitir que el flujo continúe
            logger('Error enviando correo: ' . $e->getMessage());
        }

        // Si lo creó el admin, regresar al dashboard con mensaje de éxito
        if (Auth::check() && Auth::user()->role === 'ADMINISTRADOR') {
            return redirect()->route('dashboard')->with('success', "USUARIO SOPORTE $username CREADO CON ÉXITO.");
        }

        // Si se registró solo, mandarlo a la pantalla de éxito con el correo de destino en sesión
        return redirect()->route('registro.exitoso')->with('email_enviado', $email);
    }
}
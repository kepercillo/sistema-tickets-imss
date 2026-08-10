<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // 1. Muestra el formulario para pedir el correo
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Procesa el correo y envía el enlace temporal
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'EL CORREO ELECTRÓNICO NO ESTÁ REGISTRADO EN EL SISTEMA.'
        ]);

        $email = $request->email;
        $token = Str::random(64);

        // Guardar o actualizar el token en la tabla nativa
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Generar enlace dinámico (detecta la IP actual de tu red local)
        $actionUrl = route('password.reset', ['token' => $token]) . '?email=' . urlencode($email);

        // Enviar el correo usando una función rápida de Laravel Mail
        try {
            Mail::send([], [], function ($message) use ($email, $actionUrl) {
                $message->to($email)
                    ->subject('RESTABLECER CONTRASEÑA - SISTEMA DE TICKETS IMSS BIENESTAR CHIAPAS')
                    ->html("
                        <div style='font-family: sans-serif; padding: 20px; color: #333;'>
                            <h2 style='color: #1c3d5a;'>SOLICITUD DE RECUPERACIÓN DE CONTRASEÑA</h2>
                            <p>HEMOS RECIBIDO UNA SOLICITUD PARA RESTABLECER LA CONTRASEÑA DE TU CUENTA EN EL SISTEMA DE TICKETS.</p>
                            <p>ESTE ENLACE ES TEMPORAL Y EXPIRARÁ EN 15 MINUTOS.</p>
                            <div style='margin: 30px 0;'>
                                <a href='{$actionUrl}' style='background-color: #1C6046; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 5px;'>RESTABLECER CONTRASEÑA</a>
                            </div>
                            <p style='font-size: 11px; color: #666;'>SI TÚ NO SOLICITASTE ESTE CAMBIO, PUEDES IGNORAR ESTE CORREO DE MANERA SEGURA.</p>
                            <hr style='border: 0; border-top: 1px solid #eee; margin-top: 30px;'>
                            <div style='text-align: center;'>
                            <img src='" . asset('images/imagen-fondo_tecnologia.png') . "' alt='COORDINACIÓN DE TECNOLOGÍAS' style='max-width: 100%; height: auto;'>
                            </div>
                        </div>
                    ");
            });
        DB::table('PASSWORD_RESETS_HISTORY')->insert([
        'EMAIL' => Str::upper($email),
        'STATUS' => 'SOLICITADO',
        'IP_ADDRESS' => $request->ip(),
        'CREATED_AT' => Carbon::now()
        ]);
            return back()->with('status', 'HEMOS ENVIADO UN ENLACE DE RECUPERACIÓN A TU CORREO ELECTRÓNICO.');
        } catch (\Exception $e) {
            logger('Error al enviar correo de recuperación: ' . $e->getMessage());
            return back()->withErrors(['email' => 'HUBO UN PROBLEMA AL ENVIAR EL CORREO. INTÉNTALO MÁS TARDE.']);
        }
    }

    // 3. Muestra el formulario para escribir la nueva contraseña
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    // 4. Actualiza la nueva contraseña en la BD
    public function resetPassword(Request $request)
    {
        $request->validate([
        'token' => 'required',
        'email' => 'required|email|exists:users,email',
        // Aplicamos el esquema estricto de validación
        'password' => [
            'required',
            'string',
            'confirmed',
            Password::min(8)
                ->letters()   // Al menos una letra
                ->mixedCase() // Mayúsculas y minúsculas
                ->numbers()   // Al menos un número
                ->symbols(),  // Al menos un carácter especial
        ],
    ]);

        // Validar que el token exista y sea válido para ese correo
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return back()->withErrors(['email' => 'EL ENLACE DE RECUPERACIÓN ES INVÁLIDO O YA EXPIRÓ.']);
        }

        // Actualizar la contraseña del usuario (Los mutadores del modelo asegurarán las MAYÚSCULAS)
        DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password),
                'updated_at' => Carbon::now()
            ]);

        // Borrar el token para que no se pueda volver a usar
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('PASSWORD_RESETS_HISTORY')->insert([
        'EMAIL' => Str::upper($request->email),
        'STATUS' => 'COMPLETADO',
        'IP_ADDRESS' => $request->ip(),
        'CREATED_AT' => Carbon::now()
        ]);

        return redirect()->route('login')->with('success', 'TU CONTRASEÑA HA SIDO ACTUALIZADA CON ÉXITO. YA PUEDES INICIAR SESIÓN.');
    }
}
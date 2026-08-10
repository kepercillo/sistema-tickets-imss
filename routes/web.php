<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCategoryController;
use App\Http\Controllers\TicketStatusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SoporteTicketController;
use App\Http\Controllers\AdminSoporteController;
use App\Http\Controllers\TicketMessageController;

/*
|--------------------------------------------------------------------------
| REDIRECCIÓN PRINCIPAL
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| RUTAS INVITADOS (NO AUTENTICADOS)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/registro', [UserController::class, 'showRegister'])->name('register');
    Route::post('/registro', [UserController::class, 'register']);

    Route::get('/registro-exitoso', function() {
        if(!session('email_enviado')) return redirect()->route('login');
        return view('auth.register-success');
    })->name('registro.exitoso');

    Route::get('/recuperar-contrasena', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/recuperar-contrasena', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/restablecer-contrasena/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/restablecer-contrasena', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (TODOS LOS USUARIOS AUTENTICADOS)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Cierre de sesión y Dashboard
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil y Utilidades
    Route::get('api/buscar-clues', [PerfilController::class, 'buscarClues'])->name('api.clues.buscar');
    Route::get('perfil', [PerfilController::class, 'edit'])->name('profile.edit');
    Route::put('perfil', [PerfilController::class, 'update'])->name('profile.update');
    Route::get('/directorio', [DirectoryController::class, 'index'])->name('directory.index');

    /*
    |--------------------------------------------------------------------------
    | GESTIÓN Y MENSAJERÍA DE TICKETS (ACCESO GENERAL SEGÚN LÓGICA EN CONTROLADOR)
    |--------------------------------------------------------------------------
    */
    Route::get('/mis-tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/nuevo', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    
    // Rutas compartidas de acción/respuesta sobre tickets
    Route::patch('/tickets/{ticket}/cerrar', [TicketController::class, 'cerrar'])->name('tickets.cerrar');
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');
    
    // Historial y almacenamiento de mensajes
    Route::get('/tickets/{ticket}/mensajes', [TicketMessageController::class, 'index'])->name('tickets.mensajes.index');
    Route::post('/tickets/{ticket}/mensajes', [TicketMessageController::class, 'store'])->name('tickets.mensajes.store');

    /*
    |--------------------------------------------------------------------------
    | RUTAS EXCLUSIVAS PARA ADMINISTRADOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:ADMINISTRADOR')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/soporte', [AdminSoporteController::class, 'index'])->name('soporte.index');
        Route::post('/soporte', [AdminSoporteController::class, 'store'])->name('soporte.store');
        Route::put('/soporte/{user}', [AdminSoporteController::class, 'update'])->name('soporte.update');
        Route::delete('/soporte/{user}', [AdminSoporteController::class, 'destroy'])->name('soporte.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS EXCLUSIVAS PARA SOPORTE Y ADMINISTRADOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:SOPORTE,ADMINISTRADOR')->prefix('soporte')->name('soporte.')->group(function () {
        Route::get('/tickets', [SoporteTicketController::class, 'index'])->name('tickets.index');
        Route::patch('/tickets/{ticket}/atender', [SoporteTicketController::class, 'atender'])->name('tickets.atender');
        Route::patch('/tickets/{ticket}/resolver', [SoporteTicketController::class, 'resolver'])->name('tickets.resolver');
        Route::patch('/tickets/{ticket}/asignar', [SoporteTicketController::class, 'asignar'])->name('tickets.asignar');
        Route::get('/tickets/{id}/chat', [TicketController::class, 'chat'])->name('tickets.chat');
        Route::post('/tickets/{id}/chat/mensaje', [TicketController::class, 'enviarMensaje'])->name('tickets.chat.mensaje');
    });

});
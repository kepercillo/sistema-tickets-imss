<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;                  // <-- IMPORTANTE: Para que reconozca tu modelo Ticket
use Illuminate\Support\Facades\Auth;    // <-- IMPORTANTE: Para saber quién está conectado

class DashboardController extends Controller
{
    /**
     * Carga el panel principal con las métricas dinámicas según el rol.
     */
    public function index()
    {
        $user = Auth::user();
        $role = strtoupper(trim($user->role));

        $totalTickets = 0;
        $pendientes   = 0;
        $enProceso    = 0;
        $resueltos    = 0;

        // Corregido el "if" y el inicio de la condición
        if (in_array($role, ['ADMINISTRADOR', 'SOPORTE'])) {
            $totalTickets = Ticket::count();
            $pendientes   = Ticket::where('status', 'PENDIENTE')->count();
            $enProceso    = Ticket::whereNotNull('attended_at')->whereNull('resolved_at')->count();
            $resueltos    = Ticket::whereNotNull('resolved_at')->count();
            return redirect()->route('soporte.tickets.index');
        } else {
            // Si es un EMPLEADO normal, solo ve sus propios movimientos
            $totalTickets = Ticket::where('user_id', $user->id)->count();
            $pendientes   = Ticket::where('user_id', $user->id)->where('status', 'PENDIENTE')->count();
            $enProceso    = Ticket::where('user_id', $user->id)->whereNotNull('attended_at')->whereNull('resolved_at')->count();
            $resueltos    = Ticket::where('user_id', $user->id)->whereNotNull('resolved_at')->count();
            return redirect()->route('tickets.index');
        }

        return view('dashboard', compact('user', 'totalTickets', 'pendientes', 'enProceso', 'resueltos'));
    }
}
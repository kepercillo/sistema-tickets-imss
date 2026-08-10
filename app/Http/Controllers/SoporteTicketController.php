<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class SoporteTicketController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cargar el listado de técnicos/administradores para los selectores
        $tecnicos = User::whereIn('role', ['SOPORTE', 'ADMINISTRADOR'])
            ->orderBy('name', 'asc')
            ->get();

        $user = auth()->user();

        // 2. Construir la consulta base con conteo de mensajes no leídos por usuario
        $query = Ticket::with(['user', 'category', 'tecnico', 'messages'])
            ->withCount(['messages as unread_messages_count' => function ($q) use ($user) {
                $q->where('user_id', '!=', $user->id)
                  ->where('is_read', 0);
            }]);

        // SI EL USUARIO TIENE ROL DE SOPORTE, FILTRAR SOLO SUS TICKETS ASIGNADOS
        if ($user->role === 'SOPORTE') {
            $query->where('assigned_to', $user->id);
        }   

        // SI EXISTE BÚSQUEDA O FILTRO EN EL REQUEST
        if ($request->filled('search')) {
            $search = strtoupper($request->search);
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 3. ORDENAR POR ESTADO: 1° PENDIENTE, 2° EN PROCESO, 3° RESUELTO (Y LUEGO POR MÁS RECIENTES)
        $tickets = $query->orderByRaw("
                CASE 
                    WHEN UPPER(status) = 'PENDIENTE' THEN 1
                    WHEN UPPER(status) IN ('EN_PROCESO', 'EN PROCESO') THEN 2
                    WHEN UPPER(status) = 'RESUELTO' THEN 3
                    ELSE 4
                END ASC
            ")
            ->latest() // ORDEN SECUNDARIO POR FECHA DE CREACIÓN
            ->paginate(10)
            ->withQueryString();

        // 4. Enviar las variables a la vista
        return view('admin.soporte.tickets.index', compact('tickets', 'tecnicos'));
    }

    // Método para asignar un ticket a un técnico
    public function asignar(Request $request, Ticket $ticket)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'EN_PROCESO',
            'attended_at' => $ticket->attended_at ?? now(),
        ]);

        return back()->with('success', 'TICKET ASIGNADO CORRECTAMENTE.');       
    }

    // Método para marcar como Resuelto/Atendido
    public function resolver(Request $request, Ticket $ticket)
    {
        $request->validate([
            'solucion' => 'required|string'
        ]);

        $ticket->update([
            'solucion' => mb_strtoupper(trim($request->solucion), 'UTF-8'),
            'status' => 'RESUELTO',
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'TICKET MARCADO COMO RESUELTO.');
    }
}
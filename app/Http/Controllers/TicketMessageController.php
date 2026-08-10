<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TicketMessageController extends Controller
{
    /**
     * Devuelve la lista de mensajes de un ticket y los marca como leídos para el usuario actual.
     * GET /tickets/{ticket}/mensajes
     */
    public function index(Ticket $ticket)
    {
        // Validar que el usuario sea el dueño, técnico asignado o administrador
        if (auth()->id() !== $ticket->user_id && 
            auth()->id() !== $ticket->assigned_to && 
            !in_array(auth()->user()->role, ['ADMINISTRADOR', 'SOPORTE', 'TECNICO'])) {
            return response()->json(['error' => 'NO AUTORIZADO.'], 403);
        }

        // Marcar como leídos los mensajes que envió la contraparte
        $ticket->messages()
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $mensajes = $ticket->messages()
            ->with('user:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($mensajes);
    }

    public function getMensajes($ticketId)
{
    // MARCAR COMO LEÍDOS TODOS LOS MENSAJES QUE NO FUERON ENVIADOS POR EL USUARIO AUTENTICADO
    Mensaje::where('ticket_id', $ticketId)
        ->where('user_id', '!=', auth()->id())
        ->where('is_read', 0)
        ->update([
            'is_read' => 1
        ]);

    // OBTENER LA LISTA DE MENSAJES ACTUALIZADA
    $mensajes = Mensaje::where('ticket_id', $ticketId)
        ->with('user')
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json([
        'success' => true,
        'mensajes' => $mensajes
    ]);
}

    /**
     * Guarda un nuevo mensaje enviado en el chat.
     * POST /tickets/{ticket}/mensajes
     */
    public function store(Request $request, Ticket $ticket)
    {
        if (strtoupper($ticket->status) === 'RESUELTO') {
            return response()->json(['error' => 'EL TICKET SE ENCUENTRA RESUELTO.'], 422);
        }

        $contenidoTexto = $request->input('message') ?? $request->input('contenido');

        if (empty(trim($contenidoTexto))) {
            return response()->json(['error' => 'EL MENSAJE NO PUEDE ESTAR VACÍO.'], 422);
        }

        $nuevoMensaje = $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => strtoupper(trim($contenidoTexto)),
            'is_read' => 0,
        ]);

        $nuevoMensaje->load('user:id,name');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $nuevoMensaje  
            ]);
        }

        return back()->with('success', 'MENSAJE ENVIADO CORRECTAMENTE.');
    }

    /**
     * Marca explícitamente los mensajes de un ticket como leídos.
     * POST /tickets/{ticket}/marcar-leidos
     */
    public function markAsRead(Ticket $ticket)
    {
        $ticket->messages()
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }
}
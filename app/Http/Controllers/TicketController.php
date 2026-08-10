<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use App\Events\TicketUpdated;
use App\Models\Mensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Muestra el listado de tickets según el rol del usuario autenticado.
     */
    public function index()
    {
        $user = Auth::user();
        $roleUpper = strtoupper($user->role);

        // Si es ADMINISTRADOR o SOPORTE ve todos los tickets
        if (in_array($roleUpper, ['ADMINISTRADOR', 'SOPORTE'])) {
            $tickets = Ticket::with(['user', 'tecnico', 'category', 'messages.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Si es EMPLEADO solo ve los tickets que creó
            $tickets = Ticket::with(['user', 'tecnico', 'category', 'messages.user'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        // Obtener técnicos para asignación
        $tecnicos = User::whereIn('role', ['SOPORTE', 'soporte', 'ADMINISTRADOR', 'administrador'])
            ->orderBy('name', 'asc')
            ->get();

        return view('tickets.index', compact('tickets', 'tecnicos'));
    }

    /**
     * Guarda un nuevo ticket generado por un usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id'              => Auth::id(),
            'category_id'          => $request->category_id,
            'title'                => strtoupper($request->title),
            'description'          => strtoupper($request->description),
            'status'               => 'PENDIENTE',
            'department_at_report' => strtoupper(Auth::user()->department ?? 'GENERAL'),
        ]);

        // EMITIR ALERTA EN TIEMPO REAL A SOPORTE / ADMIN
        broadcast(new TicketUpdated(
            $ticket,
            'CREADO',
            'NUEVO TICKET REGISTRADO: #' . $ticket->id . ' POR ' . Auth::user()->name
        ))->toOthers();

        return redirect()->route('tickets.index')->with('success', 'TICKET CREADO CORRECTAMENTE.');
    }

    /**
     * Permite a Administrador o Soporte asignar un técnico.
     */
    public function asignar(Request $request, $id)
    {
        $user = Auth::user();
        $roleUpper = strtoupper($user->role);

        if (!in_array($roleUpper, ['ADMINISTRADOR', 'SOPORTE'])) {
            abort(403, 'NO TIENE PERMISOS PARA REALIZAR LA ASIGNACIÓN DE TICKETS.');
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->assigned_to = $request->assigned_to;
        
        if (strtoupper($ticket->status) === 'PENDIENTE') {
            $ticket->status = 'EN_PROCESO';
        }

        $ticket->save();

        // EMITIR ALERTA EN TIEMPO REAL AL USUARIO Y AL TÉCNICO
        broadcast(new TicketUpdated(
            $ticket,
            'ASIGNADO',
            'EL TICKET #' . $ticket->id . ' FUE ASIGNADO A ' . $ticket->tecnico->name
        ));

        return redirect()->back()->with('success', 'TÉCNICO ASIGNADO CORRECTAMENTE.');
    }

    /**
     * Registra un mensaje en el chat del ticket.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();
        $roleUpper = strtoupper($user->role);

        // Validación de permisos de participación en el chat
        $esCreador = ($ticket->user_id === $user->id);
        $esTecnicoAsignado = ($ticket->assigned_to === $user->id);
        $esAdminOSoporte = in_array($roleUpper, ['ADMINISTRADOR', 'SOPORTE']);

        if (!$esCreador && !$esTecnicoAsignado && !$esAdminOSoporte) {
            abort(403, 'NO TIENE PERMISOS PARA ACCEDER A ESTA SECCIÓN.');
        }

        // Crear el mensaje
        $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => strtoupper($request->message),
        ]);

        // Si la respuesta es de soporte/técnico y el ticket estaba pendiente, cambiar estado
        if (($esAdminOSoporte || $esTecnicoAsignado) && strtoupper($ticket->status) === 'PENDIENTE') {
            $ticket->status = 'EN_PROCESO';
            $ticket->save();
        }

        // EMITIR ALERTA EN TIEMPO REAL
        broadcast(new TicketUpdated(
            $ticket,
            'NUEVO_MENSAJE',
            'NUEVO MENSAJE EN TICKET #' . $ticket->id . ' DE ' . $user->name
        ))->toOthers();

        return redirect()->back()->with('success', 'MENSAJE ENVIADO CORRECTAMENTE.');
    }

    /**
     * Resuelve y cierra oficialmente el ticket (Soporte / Administrador).
     */
    public function resolver(Request $request, $id)
    {
        $user = Auth::user();
        $roleUpper = strtoupper($user->role);

        if (!in_array($roleUpper, ['ADMINISTRADOR', 'SOPORTE'])) {
            abort(403, 'NO TIENE PERMISOS PARA RESOLVER ESTE TICKET.');
        }

        $request->validate([
            'solucion' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($id);

        $ticket->status = 'RESUELTO';
        $ticket->solution_notes = strtoupper($request->solucion);
        $ticket->resolved_at = now();
        $ticket->save();

        // Guardar la solución en el chat como un mensaje del sistema
        $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => 'TICKET MARCADO COMO RESUELTO. SOLUCIÓN: ' . strtoupper($request->solucion),
        ]);

       \App\Models\Mensaje::where('ticket_id', $ticketId)
        ->where('is_read', 0)
        ->update([
            'is_read' => 1
        ]);

        return redirect()->back()->with('success', 'EL TICKET HA SIDO MARCADO COMO RESUELTO.');
    }

    /**
     * Cierre de ticket por parte del empleado creador.
     */
    public function cerrar($id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        if ($ticket->user_id !== $user->id && !in_array(strtoupper($user->role), ['ADMINISTRADOR', 'SOPORTE'])) {
            abort(403, 'NO TIENE PERMISOS PARA CANCELAR O CERRAR ESTE TICKET.');
        }

        $ticket->status = 'RESUELTO';
        $ticket->save();

        \App\Models\Mensaje::where('ticket_id', $ticketId)
        ->where('is_read', 0)
        ->update([
            'is_read' => 1
        ]);

        return redirect()->back()->with('success', 'EL TICKET HA SIDO FINALIZADO.');
    }

    public function create()
    {
        // OBTENER EL USUARIO AUTENTICADO Y LAS CATEGORÍAS
        $user = auth()->user();
        $categories = Category::all(); // O Category::orderBy('nombre')->get();

        return view('tickets.create', compact('user', 'categories'));
    }

}

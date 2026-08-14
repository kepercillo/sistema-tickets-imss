<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use App\Models\TicketMessage;
use App\Models\Mensaje;
use App\Events\TicketUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
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
            'clues_at_report'      => strtoupper(Auth::user()->clues ?? 'SIN CLUES'),
            'description'          => strtoupper($request->description),
            'status'               => 'PENDIENTE',
            'department_at_report' => strtoupper(Auth::user()->department ?? 'GENERAL'),
        ]);

        // EMITIR ALERTA EN TIEMPO REAL A SOPORTE / ADMIN

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
        return redirect()->back()->with('success', 'MENSAJE ENVIADO CORRECTAMENTE.');
    }

    /**
 * Resolver ticket por parte de Soporte / Administrador
 */
public function resolver(Request $request, $id)
{
    $user = Auth::user();
    $roleUpper = strtoupper($user->role);

    if (!in_array($roleUpper, ['ADMINISTRADOR', 'SOPORTE'])) {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['error' => 'NO TIENE PERMISOS.'], 403);
        }
        abort(403, 'NO TIENE PERMISOS PARA RESOLVER ESTE TICKET.');
    }

    $request->validate([
        'solucion' => 'required|string',
    ]);

    $ticket = Ticket::findOrFail($id);

    $ticket->status = 'RESUELTO';
    $ticket->solucion = strtoupper($request->solucion);
    $ticket->resolved_at = now();
    $ticket->save();

    // Guardar la solución en el chat como un mensaje del sistema
    $ticket->messages()->create([
        'user_id' => $user->id,
        'message' => 'TICKET MARCADO COMO RESUELTO POR SOPORTE. SOLUCIÓN: ' . strtoupper($request->solucion),
        'is_read' => 0,
    ]);

    // Marcar mensajes como leídos (usando TicketMessage en lugar de Mensaje)
    if (class_exists('App\Models\TicketMessage')) {
        \App\Models\TicketMessage::where('ticket_id', $ticket->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
    } else {
        // Si usas el modelo Mensaje, asegúrate de importarlo
        \App\Models\Mensaje::where('ticket_id', $ticket->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
    }

    // Si es AJAX, devolver JSON
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Ticket resuelto correctamente.']);
    }

    return redirect()->back()->with('success', 'EL TICKET HA SIDO MARCADO COMO RESUELTO.');
}


public function cerrar(Request $request, $id)
{
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        // Solo el creador puede cerrar
        if ($ticket->user_id !== $user->id) {
            abort(403, 'NO TIENE PERMISOS PARA CERRAR ESTE TICKET.');
        }

        // Si ya está resuelto, no hacer nada
        if (strtoupper($ticket->status) === 'RESUELTO') {
            return redirect()->back()->with('info', 'EL TICKET YA ESTÁ RESUELTO.');
        }

        $solucionTexto = $request->filled('solucion') 
            ? strtoupper(trim($request->solucion)) 
            : 'EL USUARIO CONFIRMA LA SOLUCIÓN DEL PROBLEMA.';

        // Asignar valores
        $ticket->status = 'RESUELTO';
        $ticket->solucion = $solucionTexto;  // ← Usa el campo 'solucion'

        // Si existe la columna resolved_at (opcional)
        if (Schema::hasColumn('tickets', 'resolved_at')) {
            $ticket->resolved_at = now();
        }

        $ticket->save();

        // Registrar en el chat usando el modelo TicketMessage (NO Mensaje)
        $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => 'TICKET FINALIZADO POR EL USUARIO. NOTA: ' . $solucionTexto,
            'is_read' => 0,
        ]);

        // Marcar mensajes como leídos (usando TicketMessage)
        if (Schema::hasColumn('ticket_messages', 'is_read')) {  // ← tabla correcta
            TicketMessage::where('ticket_id', $ticket->id)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
        }

        // Si la petición es AJAX (fetch), devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Ticket finalizado correctamente.']);
        }

        return redirect()->back()->with('success', 'EL TICKET HA SIDO FINALIZADO CORRECTAMENTE.');
}

    public function create()
    {
        // OBTENER EL USUARIO AUTENTICADO Y LAS CATEGORÍAS
        $user = auth()->user();
        $categories = Category::all(); // O Category::orderBy('nombre')->get();

        return view('tickets.create', compact('user', 'categories'));
    }

}

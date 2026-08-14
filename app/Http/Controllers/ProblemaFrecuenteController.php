<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class ProblemaFrecuenteController extends Controller
{
    /**
     * Muestra la lista de problemas frecuentes (tickets resueltos con soluciones largas).
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        // Base de la consulta: tickets resueltos con solución de más de 5 palabras
        $query = Ticket::where('status', 'RESUELTO')
            ->whereNotNull('solucion')
            ->whereRaw('LENGTH(solucion) - LENGTH(REPLACE(solucion, " ", "")) + 1 > 5');

        // Aplicar búsqueda si existe
        if (!empty($buscar)) {
            $query->where(function($q) use ($buscar) {
                $q->where('description', 'LIKE', "%{$buscar}%")
                  ->orWhere('title', 'LIKE', "%{$buscar}%")
                  ->orWhere('solucion', 'LIKE', "%{$buscar}%");
            });
        }

        $problemas = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['buscar' => $buscar]);

        return view('problemas-frecuentes.index', compact('problemas', 'buscar'));
    }

    /**
     * Muestra el detalle de un problema frecuente.
     */
    public function show(Ticket $ticket)
    {
        // Validar que sea un ticket resuelto con solución larga
        if ($ticket->status !== 'RESUELTO' || 
            !$ticket->solucion || 
            str_word_count($ticket->solucion) < 5) {
            abort(404, 'Este problema no está disponible en la base de conocimiento.');
        }

        return view('problemas-frecuentes.show', compact('ticket'));
    }
}
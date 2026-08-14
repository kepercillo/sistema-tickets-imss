@extends('layouts.app')

@section('title', 'DETALLE DEL PROBLEMA')
@section('header-title', 'BASE DE CONOCIMIENTO - DETALLE DE SOLUCIÓN')

@section('content')
<div class="container-fluid px-4 py-3">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-uppercase mb-1 text-dark">
                <i class="fa-solid fa-book-open text-success me-2"></i> DETALLE DEL PROBLEMA
            </h4>
            <p class="text-muted text-uppercase small mb-0">
                SOLUCIÓN REGISTRADA EN LA BASE DE CONOCIMIENTO.
            </p>
        </div>
        <a href="{{ route('problemas-frecuentes.index') }}" 
           class="btn btn-sm btn-outline-secondary fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none">
            <i class="fa-solid fa-arrow-left"></i> VOLVER
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-4">
            
            <!-- Título y metadata -->
            <div class="border-bottom pb-3 mb-3">
                <h5 class="fw-bold text-dark text-uppercase mb-2">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>
                    {{ $ticket->title ?? 'SIN TÍTULO' }}
                </h5>
                <div class="d-flex flex-wrap gap-3 text-muted text-uppercase small">
                    <span><i class="fa-regular fa-calendar me-1"></i> RESUELTO: {{ $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : $ticket->updated_at->format('d/m/Y H:i') }}</span>
                    <span><i class="fa-solid fa-user me-1"></i> USUARIO: {{ $ticket->user->name ?? 'N/A' }}</span>
                    @if($ticket->tecnico)
                        <span><i class="fa-solid fa-user-gear me-1"></i> TÉCNICO: {{ $ticket->tecnico->name }}</span>
                    @endif
                </div>
            </div>

            <!-- Descripción del problema -->
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> PROBLEMA DESCRITO:
                </h6>
                <div class="p-3 bg-light rounded-3 text-dark text-uppercase" style="font-size: 0.85rem; line-height: 1.8;">
                    {{ $ticket->description }}
                </div>
            </div>

            <!-- Solución -->
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase text-success mb-2" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-check-circle me-1"></i> SOLUCIÓN APLICADA:
                </h6>
                <div class="p-3 bg-success-subtle border border-success rounded-3 text-dark" style="font-size: 0.85rem; line-height: 1.8;">
                    <i class="fa-solid fa-quote-left text-success me-1 opacity-50"></i>
                    {{ $ticket->solucion }}
                    <i class="fa-solid fa-quote-right text-success ms-1 opacity-50"></i>
                </div>
            </div>

            <!-- Nota de utilidad -->
            <div class="alert alert-info border-0 border-start border-4 border-info rounded-0 text-uppercase small" role="alert">
                <i class="fa-solid fa-lightbulb me-2"></i>
                ESTA SOLUCIÓN FUE REGISTRADA POR SOPORTE TÉCNICO Y PUEDE SER ÚTIL PARA PROBLEMAS SIMILARES.
            </div>

        </div>
    </div>

</div>
@endsection
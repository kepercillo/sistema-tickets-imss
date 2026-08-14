@extends('layouts.app')

@section('title', 'PROBLEMAS FRECUENTES')
@section('header-title', 'BASE DE CONOCIMIENTO - PROBLEMAS RESUELTOS')

@section('content')
<div class="container-fluid px-4 py-3">
    
    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-uppercase mb-1 text-dark">
                <i class="fa-solid fa-book-open text-success me-2"></i> PROBLEMAS FRECUENTES
            </h4>
            <p class="text-muted text-uppercase small mb-0">
                CONSULTA LAS SOLUCIONES A PROBLEMAS COMUNES RESUELTOS POR SOPORTE TÉCNICO.
            </p>
        </div>
        <span class="badge bg-success text-uppercase px-3 py-2">
            {{ $problemas->total() }} SOLUCIONES DISPONIBLES
        </span>
    </div>

    <!-- BUSCADOR -->
    <div class="card border-0 shadow-sm p-3 rounded-3 mb-4">
        <form action="{{ route('problemas-frecuentes.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group input-group-sm flex-grow-1">
                <span class="input-group-text bg-light text-muted border-end-0">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" 
                       class="form-control form-control-sm text-uppercase fw-semibold shadow-none border-start-0" 
                       placeholder="BUSCAR POR PROBLEMA, TÍTULO O SOLUCIÓN...">
            </div>
            <button type="submit" class="btn btn-sm text-white fw-bold text-uppercase px-3 shadow-sm flex-shrink-0" 
                    style="background-color: #047857;">
                <i class="fa-solid fa-filter me-1"></i> FILTRAR
            </button>
            @if(request('buscar'))
                <a href="{{ route('problemas-frecuentes.index') }}" 
                   class="btn btn-sm btn-light fw-bold text-uppercase shadow-none border flex-shrink-0">
                    <i class="fa-solid fa-xmark me-1"></i> LIMPIAR
                </a>
            @endif
        </form>
    </div>

    <!-- LISTADO DE PROBLEMAS -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        @if($problemas->isEmpty())
            <div class="card-body p-5 text-center">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-book-open fs-3"></i>
                </div>
                <h5 class="fw-bold text-secondary text-uppercase fs-6 mb-1">
                    {{ request('buscar') ? 'NO SE ENCONTRARON RESULTADOS' : 'SIN PROBLEMAS REGISTRADOS' }}
                </h5>
                <p class="text-muted text-uppercase mb-0" style="font-size: 0.75rem;">
                    {{ request('buscar') ? 'INTENTE CON OTROS TÉRMINOS DE BÚSQUEDA.' : 'AÚN NO HAY SOLUCIONES REGISTRADAS EN LA BASE DE CONOCIMIENTO.' }}
                </p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            <th class="py-3 px-4">PROBLEMA / TÍTULO</th>
                            <th class="py-3 px-4">FECHA RESOLUCIÓN</th>
                            <th class="py-3 px-4">SOLUCIÓN</th>
                            <th class="py-3 px-4 text-end">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-secondary" style="font-size: 0.8rem;">
                        @foreach($problemas as $problema)
                            <tr>
                                <td class="py-3 px-4">
                                    <span class="d-block fw-bold text-dark text-uppercase">
                                        <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                                        {{ $problema->title ?? 'SIN TÍTULO' }}
                                    </span>
                                    <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">
                                        <i class="fa-regular fa-file-lines me-1"></i>
                                        {{ Str::limit($problema->description, 80) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-muted" style="font-size: 0.75rem;">
                                    {{ $problema->resolved_at ? $problema->resolved_at->format('d/m/Y') : $problema->updated_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-dark text-uppercase" style="font-size: 0.75rem;">
                                        {{ Str::limit($problema->solucion, 60) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <a href="{{ route('problemas-frecuentes.show', $problema) }}" 
                                       class="btn btn-sm btn-outline-success fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                       style="font-size: 0.68rem; padding: 0.3rem 0.6rem;">
                                        <i class="fa-solid fa-eye"></i> VER DETALLE
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- PAGINACIÓN -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <span class="text-muted text-uppercase small">
            MOSTRANDO {{ $problemas->firstItem() ?? 0 }} - {{ $problemas->lastItem() ?? 0 }} DE {{ $problemas->total() }}
        </span>
        {{ $problemas->links() }}
    </div>

</div>
@endsection
@extends('layouts.app')

@section('header-title', 'DIRECTORIO DE PERSONAL IMSS')

@section('content')
<div class="container-fluid p-0">

    <!-- TARJETA CONTENEDORA PRINCIPAL -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        
        <!-- BARRA DE BÚSQUEDA Y FILTRADO -->
        <div class="card-header bg-white py-3 border-bottom">
            <form action="{{ route('directory.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               class="form-control bg-light border-start-0 text-uppercase fw-semibold" 
                               placeholder="BUSCAR POR NOMBRE, PUESTO O COORDINACIÓN..."
                               autocomplete="off">
                    </div>
                </div>

                <div class="col-12 col-md-4 col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn text-white fw-bold text-uppercase px-4 shadow-sm" style="background-color: #1C6046;">
                        BUSCAR
                    </button>

                    @if(!empty($search))
                        <a href="{{ route('directory.index') }}" class="btn btn-secondary fw-bold text-uppercase shadow-sm">
                            LIMPIAR
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TABLA DE INFORMACIÓN -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase border-bottom">
                        <tr class="text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                            <th class="ps-4 py-3">NOMBRE</th>
                            <th class="py-3">CORREO</th>
                            <th class="py-3">PUESTO</th>
                            <th class="py-3">COORDINACIÓN</th>
                            <th class="py-3">TELÉFONO</th>
                            <th class="pe-4 py-3">OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0 text-uppercase" style="font-size: 0.85rem;">
                        @forelse($contactos as $contacto)
                            <tr>
                                <!-- NOMBRE -->
                                <td class="ps-4 py-3 font-semibold fw-bold" style="color: #134230;">
                                    {{ $contacto->NOMBRE }}
                                </td>

                                <!-- CUENTA / CORREO -->
                                <td class="py-3 text-secondary" style="font-size: 0.8rem;">
                                        {{ $contacto->EMAIL ?? 'sin correo registrado' }}
                                </td>

                                <!-- PUESTO -->
                                <td class="py-3 text-secondary" style="font-size: 0.8rem;">
                                    {{ $contacto->PUESTO ?? 'N/A' }}
                                </td>

                                <!-- COORDINACIÓN -->
                                <td class="py-3 text-secondary" style="font-size: 0.8rem;">
                                    {{ $contacto->COORDINACION ?? 'N/A' }}
                                </td>
                                <!-- TELÉFONO -->
                                <td class="py-3 font-monospace text-secondary" style="font-size: 0.8rem;">
                                    {{ $contacto->TELEFONO ?? 'N/A' }}
                                </td>

                                <!-- OBSERVACIONES -->
                                <td class="pe-4 py-3 text-muted text-truncate" style="max-width: 200px; font-size: 0.75rem;" title="{{ $contacto->OBSERVACIONES }}">
                                    {{ $contacto->OBSERVACIONES ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-2 d-block mb-2 opacity-50"></i>
                                    <span class="fw-bold">NO SE ENCONTRARON REGISTROS EN EL DIRECTORIO.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PAGINACIÓN -->
        @if(method_exists($contactos, 'hasPages') && $contactos->hasPages())
        <div class="card-footer bg-white py-3 border-top">
        {{ $contactos->links('pagination::bootstrap-5') }}
        </div>
    @endif

    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'NUEVO TICKET - SISTEMA DE TICKETS IMSS')
@section('header-title', 'LEVANTAR SOLICITUD DE SOPORTE')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            
            <!-- ENCABEZADO DE LA TARJETA -->
            <div class="card-header text-white p-4 border-0" style="background-color: #1C6046;">
                <h5 class="fw-bold text-uppercase fs-6 mb-1">
                    <i class="fa-solid fa-file-circle-plus me-2"></i>FORMATO DE REGISTRO DE INCIDENCIA
                </h5>
                <p class="text-white-50 text-uppercase mb-0" style="font-size: 0.75rem;">
                    Asegúrese de describir detalladamente el problema técnico para agilizar su atención.
                </p>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('tickets.store') }}" method="POST" class="d-flex flex-column gap-3">
                    @csrf

                    <!-- INFORMACIÓN AUTOMÁTICA DE ADSCRIPCIÓN -->
                    <div class="bg-light p-3 rounded-3 border">
                        <div class="row g-3" style="font-size: 0.75rem;">
                            <div class="col-12 col-md-6">
                                <span class="d-block fw-bold text-muted text-uppercase">UNIDAD MÉDICA DE ORIGEN:</span>
                                <span class="d-block fw-bold text-dark text-uppercase fs-6">{{ $user->clues }}</span>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="d-block fw-bold text-muted text-uppercase">DEPARTAMENTO SOLICITANTE:</span>
                                <span class="d-block fw-bold text-dark text-uppercase fs-6">{{ $user->department }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- CATEGORÍA DEL TICKET -->
                    <div>
                        <label for="category_id" class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                            CATEGORÍA DEL PROBLEMA <span class="text-danger">*</span>
                        </label>
                        <select name="category_id" id="category_id" required 
                                class="form-select form-select-sm text-uppercase fw-semibold shadow-none @error('category_id') is-invalid @enderror">
                            <option value="" disabled selected>-- SELECCIONE UNA CATEGORÍA --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback fw-bold text-uppercase" style="font-size: 0.7rem;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- ASUNTO / TÍTULO -->
                    <div>
                        <label for="title" class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                            FALLA PRINCIPAL <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" id="title" required 
                               placeholder="EJ. IMPRESORA NO ENCIENDE / ERROR EN SISTEMA"
                               class="form-control form-control-sm text-uppercase fw-semibold shadow-none @error('title') is-invalid @enderror"
                               value="{{ old('title') }}">
                        @error('title')
                            <div class="invalid-feedback fw-bold text-uppercase" style="font-size: 0.7rem;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div>
                        <label for="description" class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                            DESCRIPCIÓN DETALLADA DE LA PROBLEMÁTICA <span class="text-danger">*</span>
                        </label>
                        <textarea name="description" id="description" rows="5" required 
                                  placeholder="DESCRIBA LOS SÍNTOMAS DEL EQUIPO O SISTEMA, MENSAJES DE ERROR VISIBLES O CUALQUIER DETALLE RELEVANTE..."
                                  class="form-control form-control-sm text-uppercase fw-semibold shadow-none @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback fw-bold text-uppercase" style="font-size: 0.7rem;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top mt-2">
                        <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light text-secondary fw-bold text-uppercase px-3 shadow-none border">
                            CANCELAR
                        </a>
                        <button type="submit" class="btn btn-sm text-white fw-bold text-uppercase px-4 shadow-sm d-inline-flex align-items-center gap-2" style="background-color: #1C6046;">
                            <i class="fa-solid fa-paper-plane"></i> LEVANTAR TICKET
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection
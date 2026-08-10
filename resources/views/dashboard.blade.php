@extends('layouts.app')

@section('title', 'DASHBOARD - SISTEMA DE TICKETS IMSS')
@section('header-title', 'PANEL PRINCIPAL')

@section('content')
<div class="space-y-6">
    
    <!-- VALIDACIÓN DE PERFIL INCOMPLETO -->
    @if(empty(Auth::user()->clues) || empty(Auth::user()->department))
        <div class="p-4 bg-amber-50 border-l-4 border-amber-500 rounded-xl text-amber-900 shadow-sm">
            <h4 class="text-xs font-bold uppercase tracking-wider">⚠️ ATENCIÓN: PERFIL INCOMPLETO DETECTADO</h4>
            <p class="text-[11px] font-semibold uppercase mt-1">
                Para poder LEVANTAR o ATENDER solicitudes de soporte, es estrictamente obligatorio tener asignada una Unidad Médica (CLUES) y un Departamento Operativo.
            </p>
            <div class="mt-3">
                <a href="{{ route('profile.edit') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-[10px] font-bold px-3 py-1.5 rounded uppercase tracking-wider transition-colors">
                ⚙️ COMPLETAR DATOS AHORA
                </a>
            </div>
        </div>
    @endif

    <!-- DATOS DE ADSCRIPCIÓN ACTUAL -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-emerald-900 mb-1 uppercase">¡BIENVENIDO AL SISTEMA DE REPORTES DE INCIDENCIAS!</h3>
        <p class="text-xs text-gray-400 font-bold uppercase mb-6">PANEL DE CONTROL GENERAL DE TICKETS.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-gray-50 border-l-4 border-emerald-700 rounded-lg shadow-sm">
                <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">UNIDAD MÉDICA (CLUES)</h4>
                <p class="text-sm font-bold text-gray-700 mt-1 uppercase">{{ Auth::user()->clues ?? 'NO ASIGNADA' }}</p>
            </div>
            <div class="p-4 bg-gray-50 border-l-4 border-emerald-700 rounded-lg shadow-sm">
                <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">DEPARTAMENTO OPERATIVO</h4>
                <p class="text-sm font-bold text-gray-700 mt-1 uppercase">{{ Auth::user()->department ?? 'NO ASIGNADO' }}</p>
            </div>
            <div class="p-4 bg-gray-50 border-l-4 border-emerald-950 rounded-lg shadow-sm">
                <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">ROL DEL SISTEMA</h4>
                <p class="text-sm font-bold text-gray-700 mt-1 uppercase">{{ Auth::user()->role }}</p>
            </div>
        </div>
    </div>

    <!-- PANEL INDICADORES / MÉTRICAS DINÁMICAS -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Totales -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase">TICKETS TOTALES</p>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $totalTickets }}</p>
        </div>
        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">
            <i class="fa-solid fa-ticket"></i>
        </div>
    </div>

    <!-- Pendientes -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold text-amber-600 uppercase">🛑 ABIERTOS / PENDIENTES</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $pendientes }}</p>
        </div>
        <div class="w-10 h-10 bg-amber-50 rounded-full flex items-center justify-center text-amber-500">
            <i class="fa-solid fa-clock"></i>
        </div>
    </div>

    <!-- En Proceso -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold text-blue-600 uppercase">⚡ EN PROCESO</p>
            <p class="text-2xl font-black text-blue-600 mt-1">{{ $enProceso }}</p>
        </div>
        <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500">
            <i class="fa-solid fa-spinner animate-spin-slow"></i>
        </div>
    </div>

    <!-- Resueltos -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold text-emerald-600 uppercase">✅ RESUELTOS</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $resueltos }}</p>
        </div>
        <div class="w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500">
            <i class="fa-solid fa-circle-check"></i>
        </div>
    </div>
</div>

    <!-- SECCIÓN DE ACCIONES EXCLUSIVAS POR ROL -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">ACCIONES OPERATIVAS DISPONIBLES</h4>

        <div class="flex flex-wrap gap-4">
            
            <!-- ACCIONES PARA EL ROL: USER (Cualquier usuario válido puede levantar reportes) -->
            @if(Auth::user()->role === 'EMPLEADO' || Auth::user()->role === 'ADMINISTRADOR')
                @if(!empty(Auth::user()->clues) && !empty(Auth::user()->department))
                    <a href="{{ route('tickets.create') }}" class="bg-emerald-800 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg uppercase shadow-sm transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle"></i> NUEVO TICKET DE SOPORTE
                    </a>
                    <a href="{{ route('tickets.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-4 py-2.5 rounded-lg uppercase transition-all flex items-center gap-2">
                        <i class="fa-solid fa-list-check"></i> MIS SOLICITUDES LEVANTADAS
                    </a>
                @else
                    <button disabled class="bg-gray-200 text-gray-400 text-xs font-bold px-4 py-2.5 rounded-lg uppercase cursor-not-allowed flex items-center gap-2" title="Debe completar su adscripción primero">
                        <i class="fa-solid fa-lock"></i> NUEVO TICKET (BLOQUEADO)
                    </button>
                @endif
            @endif

            <!-- ACCIONES PARA EL ROL: SOPORTE (Técnicos encargados de resolver incidentes) -->
            @if(Auth::user()->role === 'SOPORTE')
                <a href="#" class="bg-blue-700 hover:bg-blue-600 text-white text-xs font-bold px-4 py-2.5 rounded-lg uppercase shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-headset"></i> ATENDER TICKETS ASIGNADOS
                </a>
                <a href="#" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-4 py-2.5 rounded-lg uppercase transition-all flex items-center gap-2">
                    <i class="fa-solid fa-folder-open"></i> HISTORIAL DE INCIDENCIAS ATENDIDAS
                </a>
            @endif

            <!-- ACCIONES PARA EL ROL: ADMIN (Control total, reasignaciones y eliminaciones masivas) -->
            @if(Auth::user()->role === 'ADMINISTRADOR')
                <div class="w-full my-2 border-t border-gray-100"></div> {{-- Separador visual --}}
                
                <a href="#" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg uppercase shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-users-gear"></i> GESTIONAR PERSONAL / TÉCNICOS
                </a>
                <a href="#" class="bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold px-4 py-2.5 rounded-lg uppercase shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-shuffle"></i> REASIGNAR TICKETS HUÉRFANOS
                </a>
                <a href="#" class="bg-rose-700 hover:bg-rose-600 text-white text-xs font-bold px-4 py-2.5 rounded-lg uppercase shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-trash-can"></i> DEPURAR / ELIMINAR REGISTROS
                </a>
            @endif

        </div>
    </div>
</div>
@endsection
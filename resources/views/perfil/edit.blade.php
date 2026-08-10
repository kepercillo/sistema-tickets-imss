@extends('layouts.app')

@section('title', 'COMPLETAR PERFIL - SISTEMA DE TICKETS IMSS')
@section('header-title', 'COMPLETAR INFORMACIÓN DE ADSCRIPCIÓN')

@section('content')
<div class="max-w-2xl mx-auto">
    
    <!-- Alertas de error de validación -->
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-red-900 shadow-sm text-xs font-bold uppercase tracking-wide">
            <p>POR FAVOR, CORRIJA LOS SIGUIENTES ERRORES:</p>
            <ul class="mt-2 list-disc pl-5 font-semibold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-6">DATOS DE IDENTIFICACIÓN Y ADSCRIPCIÓN</h3>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Campo Nombre -->
            <div>
                <label for="name" class="block text-xs font-bold text-gray-500 uppercase mb-2">NOMBRE COMPLETO:</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold uppercase focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent">
            </div>

            <!-- Campo Correo Electrónico -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-500 uppercase mb-2">CORREO ELECTRÓNICO INSTITUCIONAL:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="cluesAutocompletar()">
    
    <!-- Selector Dinámico de Unidad Médica / CLUES -->
    <div class="relative">
        <label for="buscar_unidad" class="block text-xs font-bold text-gray-500 uppercase mb-2">UNIDAD MÉDICA / ADSCRIPCIÓN:</label>
        
        <!-- Input de Texto visible para buscar por Nombre -->
        <div class="relative">
            <input type="text" id="buscar_unidad" x-model="search" @input.debounce.300ms="fetchResults()" @click.away="open = false" @focus="open = true"
                   placeholder="ESCRIBA EL NOMBRE DE SU UNIDAD O CLUES..."
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold uppercase focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="fa-solid fa-hospital text-xs"></i>
            </div>
        </div>

        <!-- Input Oculto que realmente envía la CLUES al Servidor -->
        <input type="hidden" name="clues" x-model="selectedClues">

        <!-- Lista Desplegable de Resultados -->
        <div x-show="open && results.length > 0" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto divide-y divide-gray-100" x-cloak>
            <template x-for="item in results" :key="item.clues">
                <button type="button" @click="selectItem(item)" class="w-full text-left px-4 py-2.5 hover:bg-emerald-50 text-xs font-bold text-gray-700 uppercase transition duration-100 flex flex-col">
                    <span class="text-emerald-900 font-extrabold" x-text="item.nombre_unidad"></span>
                    <span class="text-[10px] text-gray-400 font-mono tracking-wider mt-0.5" x-text="'CLUES: ' + item.clues"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Campo Departamento (Se mantiene igual) -->
    <div>
        <label for="department" class="block text-xs font-bold text-gray-500 uppercase mb-2">DEPARTAMENTO / ÁREA:</label>
        <input type="text" id="department" name="department" value="{{ old('department', $user->department) }}" required
               placeholder="EJ. TECNOLOGÍAS DE LA INFORMACIÓN"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold uppercase focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent">
    </div>
</div>

            <!-- Botones de Acción -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg uppercase tracking-wide transition">
                    CANCELAR
                </a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-800 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg uppercase tracking-wide shadow-sm transition">
                    💾 GUARDAR CAMBIOS
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function cluesAutocompletar() {
        return {
            search: '{{ old("buscar_unidad", $user->clues ? $user->clues : "") }}',
            selectedClues: '{{ old("clues", $user->clues) }}',
            results: [],
            open: false,

            fetchResults() {
                if (this.search.length < 3) {
                    this.results = [];
                    return;
                }
                fetch(`/api/buscar-clues?q=${encodeURIComponent(this.search)}`)
                    .then(response => response.json())
                    .then(data => {
                        // Si tus columnas en DB están en mayúsculas, adáptalo (item.NOMBRE_UNIDAD / item.CLUES)
                        this.results = data;
                    });
            },

            selectItem(item) {
                // Al dar clic, se muestra el Nombre en la caja de texto pero se guarda la CLUES en el hidden
                this.search = item.nombre_unidad;
                this.selectedClues = item.clues;
                this.open = false;
                this.results = [];
            }
        }
    }
</script>
@endsection


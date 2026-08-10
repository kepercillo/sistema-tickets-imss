<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA DE TICKETS - REGISTRO INSTITUCIONAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: { colors: { imss: { verde: '#1C6046', verdeOscuro: '#134230', oro: '#B19055', grisFondo: '#F4F6F5' } } }
            }
        }
    </script>
</head>
<body class="min-h-full flex flex-col justify-between bg-imss-grisFondo">

    <div class="flex-1 flex items-center justify-center p-6 my-4">
        <div class="w-full max-w-2xl bg-white p-8 rounded-xl shadow-lg border border-gray-100 flex flex-col">
            
            <div class="mb-6 w-full flex justify-center">
                <img src="{{ asset('images/imagen-fondo.png') }}" 
                     alt="Logo IMSS" 
                     class="h-20 md:h-24 w-auto object-contain block mx-auto">
            </div>

            <div class="text-center mb-6">
                <h1 class="text-xl font-bold text-gray-800 uppercase tracking-wide">ALTA DE USUARIO INSTITUCIONAL</h1>
                <p class="text-xs text-imss-oro font-bold uppercase tracking-wider mt-1">SISTEMA DE CONTROL DE INCIDENCIAS</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- SECCIÓN NOMBRE COMPLETO -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">APELLIDO PATERNO:</label>
                        <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-xs font-semibold uppercase focus:outline-none focus:border-imss-oro">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">APELLIDO MATERNO:</label>
                        <input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-xs font-semibold uppercase focus:outline-none focus:border-imss-oro">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">NOMBRE (S):</label>
                        <input type="text" name="nombres" required value="{{ old('nombres') }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-xs font-semibold uppercase focus:outline-none focus:border-imss-oro">
                    </div>
                </div>

                <!-- SECCIÓN DE ADSCRIPCIÓN -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Clues (Default Seleccionado) -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">CLUES DE LA UNIDAD:</label>
                        <select name="clues" id="clues-select" required class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-xs font-semibold focus:outline-none focus:border-imss-oro">
                            @foreach($cluesLista as $c)
                                <option value="{{ $c->clues }}" data-nombre="{{ $c->nombre_unidad }}" {{ $c->clues == $cluesDefault ? 'selected' : '' }}>
                                    {{ $c->clues }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Etiqueta Dinámica del Nombre de la Unidad -->
                        <div class="mt-1 px-2 py-1 bg-imss-grisFondo rounded border border-gray-100">
                            <span class="text-[9px] text-gray-400 font-bold uppercase block">NOMBRE DE LA UNIDAD:</span>
                            <span id="nombre_unidad" class="text-[10px] text-imss-verdeOscuro font-bold uppercase">{{ $unidadDefault }}</span>
                        </div>
                    </div>

                    <!-- Departamento Combo Box -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">DEPARTAMENTO ASIGNADO:</label>
                        <select name="department" id="department" required class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-xs font-semibold focus:outline-none focus:border-imss-oro uppercase">
                            <option value="">-- SELECCIONE DEPARTAMENTO --</option>
                            @foreach($departamentos as $d)
                                <option value="{{ $d->name }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- SECCIÓN CORREO ELECTRÓNICO -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">CORREO ELECTRÓNICO:</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-xs font-semibold focus:outline-none focus:border-imss-oro">
                    <span class="text-[9px] text-gray-400 block mt-0.5">Si se proporciona, se enviará un correo de confirmación.</span>
                </div>

                <!-- SECCIÓN CONTRASEÑAS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">CONTRASEÑA:</label>
                        <input type="password" name="password" required class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-xs focus:outline-none focus:border-imss-oro">
                        <span class="text-[9px] text-gray-400 block mt-0.5">MÍNIMO 8 CARACTERES, 1 MAYÚSCULA, 1 NÚMERO Y 1 ESPECIAL.</span>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">CONFIRMAR CONTRASEÑA:</label>
                        <input type="password" name="password_confirmation" required class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-xs focus:outline-none focus:border-imss-oro">
                    </div>
                </div>

                <!-- ALERTAS DE ERROR -->
                @if($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-[10px] font-bold rounded uppercase">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" class="w-full bg-imss-verde hover:bg-imss-verdeOscuro text-white font-bold text-xs py-2.5 rounded shadow transition-all uppercase tracking-wider">
                    REGISTRAR CUENTA
                </button>
            </form>
        </div>
    </div>

    <footer class="bg-imss-verde text-white text-center py-3 border-t-2 border-imss-oro">
        <p class="text-[10px] font-bold uppercase tracking-wider">
            SISTEMA DESARROLLADO POR IMSS CHIAPAS - ING. JOSÉ EDUARDO ESTRADA GÁLVEZ &copy; {{ date('Y') }}
        </p>
    </footer>

    <!-- Script en caliente para cambiar la etiqueta del nombre de la unidad al elegir otra CLUES -->
    <script>
        const cluesSelect = document.getElementById('clues-select');
        const nombreUnidadLabel = document.getElementById('nombre_unidad');

        cluesSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const nombreUnidad = selectedOption.getAttribute('data-nombre');
            nombreUnidadLabel.textContent = nombreUnidad ? nombreUnidad.toUpperCase() : 'SIN NOMBRE REGISTRADO';
        });
    </script>
</body>
</html>
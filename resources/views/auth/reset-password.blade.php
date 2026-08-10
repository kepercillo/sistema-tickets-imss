<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESTABLECER CONTRASEÑA - SISTEMA DE TICKETS</title>
    @vite('resources/css/app.css')
 
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-slate-200 p-8 m-4">
        <div class="mb-6">
                <img src="{{ asset('images/imagen-fondo.png') }}" alt="Logo IMSS" class="h-24 w-auto object-contain">
            </div>
        
        <div class="text-center mb-6">
            <h1 class="text-xl font-extrabold text-sky-950 tracking-wide uppercase">
                Sistema de Tickets
            </h1>
            <p class="text-xs font-bold text-slate-500 uppercase mt-1">
                Establecer Nueva Contraseña
            </p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs font-bold rounded-lg uppercase">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 mb-4">
                <span class="block text-[10px] font-bold text-slate-500 uppercase">Cuenta a recuperar:</span>
                <span class="text-xs font-bold text-slate-800 break-all">{{ $email }}</span>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase mb-1">
        Nueva Contraseña
    </label>
    <input type="password" name="password" id="password" required minlength="8"
        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-transparent">
    
    <p class="text-[10px] text-slate-500 mt-1 uppercase font-medium leading-tight">
        MÍNIMO 8 CARACTERES, DEBE INCLUIR AL MENOS UNA MAYÚSCULA, UNA MINÚSCULA, UN NÚMERO Y UN SÍMBOLO.
    </p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Confirmar Nueva Contraseña
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-transparent">
            </div>

            <button type="submit" 
    class="w-full py-2.5 bg-[#1C6046] hover:bg-[#134230] text-white font-bold text-xs rounded-lg uppercase tracking-wider transition duration-200">
    Actualizar Contraseña
</button>
        </form>

    </div>

</body>
</html>
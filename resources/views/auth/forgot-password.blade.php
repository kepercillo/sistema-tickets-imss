<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RECUPERAR CONTRASEÑA - SISTEMA DE TICKETS</title>
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
                Recuperación de Contraseña
            </p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs font-bold rounded-lg uppercase">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-xs font-bold rounded-lg uppercase">
                {{ session('status') }}
            </div>
        @endif

        <p class="text-xs text-slate-600 mb-6 uppercase leading-relaxed">
            Ingresa tu dirección de correo electrónico institucional o personal registrado y te enviaremos un enlace temporal para que puedas restablecer tu contraseña.
        </p>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Correo Electrónico
                </label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-transparent uppercase placeholder:normal-case"
                    placeholder="ejemplo@correo.com">
            </div>

            <button type="submit" 
    class="w-full py-2.5 bg-[#1C6046] hover:bg-[#134230] text-white font-bold text-xs rounded-lg uppercase tracking-wider transition duration-200">
    Enviar Enlace de Recuperación
</button>
        </form>

        <div class="text-center mt-6 pt-4 border-t border-slate-100">
            <a href="{{ route('login') }}" class="text-xs font-bold text-sky-800 hover:text-sky-950 uppercase tracking-tight">
                ← Volver al Inicio de Sesión
            </a>
        </div>

    </div>

</body>
</html>
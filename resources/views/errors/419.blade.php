<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SESIÓN EXPIRADA - IMSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-[#F4F6F5] flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-md border border-gray-100 text-center">
        <div class="text-6xl mb-4">⏳</div>
        <h1 class="text-xl font-bold text-[#1C6046] uppercase mb-2">La sesión ha expirado</h1>
        <p class="text-xs text-gray-500 font-bold uppercase mb-6 leading-relaxed">
            Por razones de seguridad del instituto, tu sesión se cierra automáticamente tras un periodo de inactividad.
        </p>
        
        <a href="{{ route('login') }}" 
           class="inline-block w-full bg-[#1C6046] hover:bg-[#134230] text-white font-bold text-xs py-3 px-4 rounded-lg uppercase tracking-wider transition-colors">
            Volver a Iniciar Sesión
        </a>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PÁGINA NO ENCONTRADA (404) - IMSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-[#F4F6F5] flex flex-col justify-between">
    
    <div class="flex-1 flex items-center justify-center p-6">
        
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-md border border-gray-100 text-center">
            
            <div class="text-6xl mb-4">🔍</div>
            <h1 class="text-2xl font-bold text-[#1C6046] uppercase mb-2">ERROR 404</h1>
            <h2 class="text-sm font-bold text-gray-700 uppercase mb-3">PÁGINA NO ENCONTRADA</h2>
            <p class="text-xs text-gray-500 font-bold uppercase mb-6 leading-relaxed">
                La dirección a la que intentas acceder no existe, fue movida o no tienes permisos para verla.
            </p>
            
            <a href="{{ route('login') }}" 
               class="inline-block w-full bg-[#1C6046] hover:bg-[#134230] text-white font-bold text-xs py-3 px-4 rounded-lg uppercase tracking-wider transition-colors shadow-sm">
                Volver al Inicio
            </a>
        </div>
    </div>

    <footer class="bg-[#1C6046] text-white text-center py-3 px-4 border-t-2 border-[#B19055] shadow-inner select-none">
        <p class="text-[10px] md:text-xs font-bold uppercase tracking-wider">
            SISTEMA DESARROLLADO POR IMSS CHIAPAS - ING. JOSÉ EDUARDO ESTRADA GÁLVEZ, TECNOLOGÍAS DE LA INFORMACIÓN &copy; {{ date('Y') }}
        </p>
    </footer>

</body>
</html>
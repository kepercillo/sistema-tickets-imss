<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRO EXITOSO - IMSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { imss: { verde: '#1C6046', verdeOscuro: '#134230', oro: '#B19055', grisFondo: '#F4F6F5' } } } }
        }
    </script>
</head>
<body class="min-h-full flex flex-col justify-between bg-imss-grisFondo">

    <div class="flex-1 flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-md border border-gray-100 text-center">
            
            <div class="mb-6 w-full flex justify-center">
                <img src="{{ asset('images/imagen-fondo.png') }}" alt="Logo IMSS" class="h-20 w-auto object-contain block mx-auto">
            </div>

            <div class="text-5xl mb-4">📧</div>
            <h1 class="text-xl font-bold text-imss-verde uppercase mb-2">¡REGISTRO COMPLETADO!</h1>
            <h2 class="text-xs font-bold text-gray-700 uppercase mb-4">REVISA TU CORREO ELECTRÓNICO</h2>
            
            <p class="text-xs text-gray-500 font-semibold uppercase mb-6 leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100">
                Hemos enviado tus credenciales de acceso oficiales (Nombre de Usuario y Confirmación) a la dirección:<br>
                <span class="text-imss-verde font-bold lowercase text-sm block mt-1">{{ session('email_enviado') }}</span>
            </p>
            
            <a href="{{ route('login') }}" 
               class="inline-block w-full bg-imss-verde hover:bg-imss-verdeOscuro text-white font-bold text-xs py-3 px-4 rounded-lg uppercase tracking-wider transition-colors shadow-sm">
                Ir al Inicio de Sesión
            </a>
        </div>
    </div>

    <footer class="bg-imss-verde text-white text-center py-3 border-t-2 border-imss-oro">
        <p class="text-[10px] font-bold uppercase tracking-wider">
            SISTEMA DESARROLLADO POR IMSS CHIAPAS - TECNOLOGÍAS DE LA INFORMACIÓN &copy; {{ date('Y') }}
        </p>
    </footer>

</body>
</html>
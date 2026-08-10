<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA DE TICKETS - LOGIN</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        imss: {
                            verde: '#1C6046',
                            verdeOscuro: '#134230',
                            oro: '#B19055',
                            oroOscuro: '#93733E',
                            grisFondo: '#F4F6F5',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full flex flex-col justify-between bg-imss-grisFondo">

    <div class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-100 flex flex-col items-center">
            
            <div class="mb-6">
                <img src="{{ asset('images/logo-imss.png') }}" alt="Logo IMSS" class="h-24 w-auto object-contain">
            </div>

            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 tracking-wide uppercase">SISTEMA DE TICKETS</h1>
                <p class="text-xs text-imss-oro font-bold uppercase tracking-wider mt-1">SOPORTE INFORMÁTICO LOCAL</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="w-full space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">USUARIO:</label>
                    <input type="text" name="username" required autocomplete="off"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm font-semibold text-gray-800 focus:outline-none focus:border-imss-oro focus:bg-white lowercase transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">CONTRASEÑA:</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm font-semibold text-gray-800 focus:outline-none focus:border-imss-oro focus:bg-white transition-colors">
                </div>
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-xs font-bold rounded-lg uppercase">
                    {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs font-bold rounded-lg uppercase">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" 
                        class="w-full bg-imss-verde hover:bg-imss-verdeOscuro text-white font-bold text-sm py-3 px-4 rounded-lg shadow-md uppercase tracking-wide transition-all duration-200 transform active:scale-[0.98]">
                    INGRESAR AL SISTEMA
                </button>

                <div class="pt-5 mt-5 border-t border-gray-100 space-y-3">
                    <div class="relative flex py-1 items-center">
                        <div class="flex-grow border-t border-gray-200"></div>
                        <span class="flex-shrink mx-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Opciones de Cuenta</span>
                        <div class="flex-grow border-t border-gray-200"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('password.request') }}" 
                           class="flex items-center justify-center text-center bg-white border border-imss-oro text-imss-oro hover:bg-imss-oro/5 font-bold text-[10px] py-2.5 px-2 rounded-lg uppercase tracking-wider transition-colors">
                            RECUPERAR CLAVE
                        </a>

                        <a href="{{ route('register') }}" 
                           class="flex items-center justify-center text-center bg-imss-oro hover:bg-imss-oroOscuro text-white font-bold text-[10px] py-2.5 px-2 rounded-lg uppercase tracking-wider transition-colors shadow-sm">
                            CREAR USUARIO
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <footer class="bg-imss-verde text-white text-center py-3 px-4 border-t-2 border-imss-oro shadow-inner select-none">
        <p class="text-[10px] md:text-xs font-bold uppercase tracking-wider">
            SISTEMA DESARROLLADO POR IMSS CHIAPAS - ING. JOSÉ EDUARDO ESTRADA GÁLVEZ, TECNOLOGÍAS DE LA INFORMACIÓN &copy; {{ date('Y') }}
        </p>
    </footer>

</body>
</html>
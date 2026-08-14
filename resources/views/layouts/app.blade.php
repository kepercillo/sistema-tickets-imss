<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SISTEMA TICKETS' }}</title>
    
    <!-- Vite (CSS y JS compilados) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Iconos FontAwesome & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Alpine.js (para la funcionalidad del colapso del menú lateral) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-light">

    <div class="d-flex" style="height: 100vh; overflow: hidden;" x-data="{ sidebarOpen: true }">
        
        <!-- PANEL LATERAL RETRÁCTIL (IZQUIERDA) -->
        <aside :style="sidebarOpen ? 'width: 260px;' : 'width: 80px;'" 
               class="bg-dark text-white d-flex flex-column transition-all shadow-lg h-100" 
               style="background-color: #064e3b !important; transition: width 0.3s ease-in-out; flex-shrink: 0;">
            
            <!-- Encabezado del Panel / Identidad -->
            <div class="p-3 d-flex align-items-center justify-content-between border-bottom border-light-subtle" style="background-color: #022c22; height: 64px;">
                <span x-show="sidebarOpen" class="fw-bold tracking-wider fs-6 text-uppercase text-truncate">
                    SISTEMA TICKETS
                </span>
                <button @click="sidebarOpen = !sidebarOpen" class="btn btn-outline-light border-0 p-2 shadow-none">
                    <i class="fa-solid fa-bars fs-5"></i>
                </button>
            </div>

            <!-- Menú de Navegación -->
            <nav class="flex-grow-1 p-2 gap-2 d-flex flex-column overflow-y-auto">
                @php
                    // Obtenemos el rol en mayúsculas y sin espacios para evitar fallos
                    $rolUsuario = Auth::check() ? strtoupper(trim(Auth::user()->role)) : '';
                @endphp

                {{-- 1. MIS TICKETS (Exclusivo para EMPLEADO) --}}
                @if($rolUsuario === 'EMPLEADO')
                    <a href="{{ route('tickets.index') }}" 
                       class="nav-link text-white p-3 rounded d-flex align-items-center gap-3 {{ request()->routeIs('tickets.*') ? 'active bg-success fw-bold' : '' }}"
                       style="background-color: {{ request()->routeIs('tickets.*') ? '#047857' : 'transparent' }};">
                        <i class="fa-solid fa-ticket fs-5 text-center" style="width: 24px;"></i>
                        <span x-show="sidebarOpen" class="text-uppercase small text-nowrap">MIS TICKETS</span>
                    </a>
                @endif

                {{-- 2. GESTIÓN DE SOPORTE (Exclusivo para SOPORTE y ADMINISTRADOR) --}}
                @if(in_array($rolUsuario, ['SOPORTE', 'ADMINISTRADOR']))
                    <a href="{{ route('soporte.tickets.index') }}" 
                       class="nav-link text-white p-3 rounded d-flex align-items-center gap-3 {{ request()->routeIs('soporte.tickets.*') ? 'active bg-success fw-bold' : '' }}"
                       style="background-color: {{ request()->routeIs('soporte.tickets.*') ? '#047857' : 'transparent' }};">
                        <i class="fa-solid fa-headset fs-5 text-center" style="width: 24px;"></i>
                        <span x-show="sidebarOpen" class="text-uppercase small text-nowrap">GESTIÓN DE SOPORTE</span>
                    </a>
                @endif

                {{-- 3. GESTIÓN DE TÉCNICOS / SOPORTE (Exclusivo para ADMINISTRADOR) --}}
                @if($rolUsuario === 'ADMINISTRADOR')
                    <a href="{{ route('admin.soporte.index') }}" 
                       class="nav-link text-white p-3 rounded d-flex align-items-center gap-3 {{ request()->routeIs('admin.soporte.*') ? 'active bg-success fw-bold' : '' }}"
                       style="background-color: {{ request()->routeIs('admin.soporte.*') ? '#047857' : 'transparent' }};">
                        <i class="fa-solid fa-user-gear fs-5 text-center" style="width: 24px;"></i>
                        <span x-show="sidebarOpen" class="text-uppercase small text-nowrap">ALTAS / BAJAS SOPORTE</span>
                    </a>
                @endif

                {{-- 4. DIRECTORIO IMSS (Público para todos los roles) --}}
                <a href="{{ route('directory.index') }}" 
                   class="nav-link text-white p-3 rounded d-flex align-items-center gap-3 {{ request()->routeIs('directory.*') ? 'active bg-success fw-bold' : '' }}"
                   style="background-color: {{ request()->routeIs('directory.*') ? '#047857' : 'transparent' }};">
                    <i class="fa-solid fa-address-book fs-5 text-center" style="width: 24px;"></i>
                    <span x-show="sidebarOpen" class="text-uppercase small text-nowrap">DIRECTORIO IMSS</span>
                </a>

                {{-- 5. PROBLEMAS FRECUENTES (Todos los roles) --}}
                <a href="{{ route('problemas-frecuentes.index') }}" 
                class="nav-link text-white p-3 rounded d-flex align-items-center gap-3 {{ request()->routeIs('problemas-frecuentes.*') ? 'active bg-success fw-bold' : '' }}"
                style="background-color: {{ request()->routeIs('problemas-frecuentes.*') ? '#047857' : 'transparent' }};">
                    <i class="fa-solid fa-book-open fs-5 text-center" style="width: 24px;"></i>
                    <span x-show="sidebarOpen" class="text-uppercase small text-nowrap">PROBLEMAS FRECUENTES</span>
                </a>

            </nav>
            
            <!-- Pie del Panel -->
            <div x-show="sidebarOpen" class="p-3 border-top border-light-subtle text-center text-light opacity-75 text-uppercase" style="background-color: #022c22; font-size: 0.65rem; line-height: 1.4;">
                &copy; {{ date('Y') }} TECNOLOGÍA DE LA INFORMACIÓN IMSS CHIAPAS<br>
                <span class="fw-bold text-white">DESARROLLO: ING. JOSÉ EDUARDO ESTRADA GÁLVEZ</span>
            </div>
        </aside>

        <!-- ÁREA CONTENEDORA PRINCIPAL (DERECHA) -->
        <div class="d-flex flex-column flex-grow-1 h-100 overflow-hidden">
            
            <!-- BARRA SUPERIOR (HEADER) -->
            <header class="bg-white border-bottom px-4 d-flex align-items-center justify-content-between shadow-sm" style="height: 64px;">
                <div class="d-flex align-items-center gap-3">
                    <h1 class="h5 fw-bold text-success text-uppercase mb-0">
                        @yield('header-title', 'CONTROL DE TICKETS')
                    </h1>
                    
                    <!-- Insignia con el Rol del Usuario -->
                    <span class="badge rounded-pill text-bg-success text-uppercase px-2 py-1 font-monospace" style="font-size: 0.65rem;">
                        {{ Auth::user()->role ?? 'USUARIO' }}
                    </span>
                </div>

                <div class="d-flex align-items-center gap-4">
                <!-- Información del Usuario -->
                    <div class="text-end d-none d-sm-block">
                        <span class="d-block fw-bold text-dark text-uppercase small">
                            {{ Auth::user()->name ?? 'USUARIO' }}
                        </span>
                        <span class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">
                            {{ Auth::user()->department ?? 'SIN DEPARTAMENTO' }}
                        </span>
                    </div>

                    <!-- BOTÓN CERRAR SESIÓN -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-link text-danger fw-bold text-decoration-none text-uppercase p-0 small d-flex align-items-center gap-2 shadow-none">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>CERRAR SESIÓN</span>
                        </button>
                    </form>

                    <!-- Imagen institucional -->
                    <div class="d-flex align-items-center border-start ps-3">
                        <img src="/imagen-fondo.png" alt="Logo IMSS" class="img-fluid" style="height: 40px; object-fit: contain;" />
                    </div>
                </div>
            </header>

            <!-- CONTENIDO PRINCIPAL DINÁMICO -->
            <main class="flex-grow-1 overflow-x-hidden overflow-y-auto p-4">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- JS de Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
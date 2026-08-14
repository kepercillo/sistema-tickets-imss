@extends('layouts.app')

@section('title', 'ADMINISTRACIÓN - PERSONAL DE SOPORTE')
@section('header-title', 'CATÁLOGO DE SOPORTE')

@section('content')
<div class="d-flex flex-column gap-4" x-data="soporteApp()" x-init="init()">
    
    <!-- ENCABEZADO Y ACCIONES -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h3 class="h5 fw-bold text-uppercase mb-1" style="color: #064e3b;">PERSONAL DE SOPORTE Y ADMINISTRACIÓN</h3>
            <p class="text-muted fw-bold text-uppercase mb-0" style="font-size: 0.75rem;">
                GESTIÓN DE ROLES Y PERMISOS DEL PERSONAL TÉCNICO Y ADMINISTRATIVO.
            </p>
        </div>
        
        <div class="d-flex gap-2 flex-wrap">
            <!-- Botón para ADMINISTRAR EMPLEADOS -->
            <button @click="openGestionModal = true" 
                    class="btn text-white fw-bold text-uppercase shadow-sm d-inline-flex align-items-center gap-2"
                    style="background-color: #1C6046; font-size: 0.8rem;">
                <i class="fa-solid fa-users-gear"></i> ADMINISTRAR EMPLEADOS
            </button>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 border-start border-4 border-success shadow-sm rounded-3 text-uppercase fw-bold p-3" role="alert" style="font-size: 0.75rem;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 border-start border-4 border-danger shadow-sm rounded-3 text-uppercase fw-bold p-3" role="alert" style="font-size: 0.75rem;">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 border-start border-4 border-danger shadow-sm rounded-3 text-uppercase p-3" role="alert" style="font-size: 0.75rem;">
            <strong class="d-block mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> CORRIJA LOS SIGUIENTES ERRORES:</strong>
            <ul class="mb-0 ps-3 font-semibold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TABLA DE PERSONAL --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        @if($tecnicos->isEmpty())
            <div class="card-body p-5 text-center">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-users-slash fs-3"></i>
                </div>
                <h5 class="fw-bold text-secondary text-uppercase fs-6 mb-1">SIN TÉCNICOS REGISTRADOS</h5>
                <p class="text-muted text-uppercase mb-0" style="font-size: 0.75rem;">AÚN NO HAY CUENTAS DE SOPORTE O ADMINISTRADORES REGISTRADAS.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            <th class="py-3 px-4">ID / NOMBRE</th>
                            <th class="py-3 px-4">USUARIO</th>
                            <th class="py-3 px-4">CORREO</th>
                            <th class="py-3 px-4">ROL</th>
                            <th class="py-3 px-4">FECHA REGISTRO</th>
                            <th class="py-3 px-4 text-end">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-secondary" style="font-size: 0.8rem;">
                        @foreach($tecnicos as $tecnico)
                            @php
                                $rolUpper = strtoupper($tecnico->role);
                                $esAdmin = ($rolUpper === 'ADMINISTRADOR');
                                $esSoporte = ($rolUpper === 'SOPORTE');
                                $adminsCount = \App\Models\User::where('role', 'ADMINISTRADOR')->count();
                            @endphp
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                             style="width: 36px; height: 36px; background-color: {{ $esAdmin ? '#fecaca' : '#d1fae5' }}; color: {{ $esAdmin ? '#991b1b' : '#065f46' }}; font-size: 0.75rem;">
                                            {{ substr($tecnico->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-bold text-dark">{{ $tecnico->name }}</span>
                                            <span class="text-muted d-block" style="font-size: 0.65rem;">
                                                {{ $rolUpper }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-monospace text-dark">
                                    {{ $tecnico->username }}
                                </td>
                                <td class="py-3 px-4">
                                    {{ $tecnico->email }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($esAdmin)
                                        <span class="badge bg-danger text-uppercase px-3 py-2">ADMIN</span>
                                    @elseif($esSoporte)
                                        <span class="badge bg-primary text-uppercase px-3 py-2">SOPORTE</span>
                                    @else
                                        <span class="badge bg-secondary text-uppercase px-3 py-2">EMPLEADO</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-muted" style="font-size: 0.75rem;">
                                    {{ $tecnico->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1 flex-wrap">
                                        <!-- EDITAR -->
                                        <button @click="prepararEdicion({
                                                    id: '{{ $tecnico->id }}',
                                                    name: '{{ $tecnico->name }}',
                                                    username: '{{ $tecnico->username }}',
                                                    email: '{{ $tecnico->email }}'
                                                })"
                                                class="btn btn-sm btn-outline-success fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                            <i class="fa-solid fa-pen"></i> EDITAR
                                        </button>

                                        <!-- ASCENDER A ADMIN -->
                                        @if(!$esAdmin)
                                            <form action="{{ route('admin.soporte.promoverAdmin', $tecnico) }}" 
                                                  method="POST" 
                                                  class="d-inline m-0"
                                                  onsubmit="return confirm('¿ESTÁ SEGURO DE ASCENDER A {{ strtoupper($tecnico->name) }} A ADMINISTRADOR?');">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                        style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                                    <i class="fa-solid fa-crown"></i> ADMIN
                                                </button>
                                            </form>
                                        @endif

                                        <!-- REVERTIR ADMIN -->
                                        @if($esAdmin && $adminsCount > 1)
                                            <form action="{{ route('admin.soporte.revertirAdmin', $tecnico) }}" 
                                                  method="POST" 
                                                  class="d-inline m-0"
                                                  onsubmit="return confirm('¿ESTÁ SEGURO DE REVERTIR A {{ strtoupper($tecnico->name) }} DE ADMINISTRADOR A SOPORTE?');">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-warning fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                        style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                                    <i class="fa-solid fa-user-slash"></i> REVERTIR ADMIN
                                                </button>
                                            </form>
                                        @elseif($esAdmin)
                                            <button disabled 
                                                    class="btn btn-sm btn-outline-secondary fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                    style="font-size: 0.65rem; padding: 0.2rem 0.5rem;"
                                                    title="NO SE PUEDE REVERTIR AL ÚLTIMO ADMINISTRADOR">
                                                <i class="fa-solid fa-lock"></i> ADMIN ÚNICO
                                            </button>
                                        @endif

                                        <!-- REVERTIR A EMPLEADO (para SOPORTE) -->
                                        @if($esSoporte)
                                            <form action="{{ route('admin.soporte.revertir', $tecnico) }}" 
                                                  method="POST" 
                                                  class="d-inline m-0"
                                                  onsubmit="return confirm('¿ESTÁ SEGURO DE REVERTIR A {{ strtoupper($tecnico->name) }} A EMPLEADO?');">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-warning fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                        style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                                    <i class="fa-solid fa-user-slash"></i> REVERTIR
                                                </button>
                                            </form>
                                        @endif

                                        <!-- ELIMINAR -->
                                        <form action="{{ route('admin.soporte.destroy', $tecnico) }}" 
                                              method="POST" 
                                              class="d-inline m-0"
                                              onsubmit="return confirm('¿ESTÁ SEGURO DE ELIMINAR ESTA CUENTA?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                    style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- ============================================================ -->
    <!-- MODAL DE GESTIÓN DE EMPLEADOS (ACTIVOS E INACTIVOS)          -->
    <!-- ============================================================ -->
    <template x-if="openGestionModal">
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
             style="z-index: 1060; background: rgba(0,0,0,0.5);">
            <div class="bg-white rounded-3 shadow-lg p-4" style="width: 95%; max-width: 900px; max-height: 90vh; overflow-y: auto;">
                
                <!-- Encabezado -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-uppercase mb-0">
                        <i class="fa-solid fa-users-gear text-success me-2"></i>
                        GESTIÓN DE EMPLEADOS
                    </h6>
                    <button type="button" class="btn-close shadow-none" @click="openGestionModal = false"></button>
                </div>

                <!-- Buscador global -->
                <div class="mb-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted">
                            <i class="fa-solid fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-sm fw-semibold shadow-none" 
                               placeholder="BUSCAR POR NOMBRE, USUARIO O CORREO..."
                               x-model="buscarEmpleado"
                               @input.debounce="cargarEmpleados()">
                    </div>
                </div>

                <!-- Pestañas -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-uppercase" :class="{ 'active': tabActiva === 'activos' }"
                                @click="cambiarTab('activos')" type="button">
                            <i class="fa-solid fa-user-check me-1"></i> ACTIVOS
                            <span class="badge bg-success ms-1" x-text="empleadosActivos.length"></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-uppercase" :class="{ 'active': tabActiva === 'inactivos' }"
                                @click="cambiarTab('inactivos')" type="button">
                            <i class="fa-solid fa-user-slash me-1"></i> INACTIVOS
                            <span class="badge bg-danger ms-1" x-text="empleadosInactivos.length"></span>
                        </button>
                    </li>
                </ul>

                <!-- Contenido de la pestaña ACTIVOS -->
                <div x-show="tabActiva === 'activos'" x-cloak>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted text-uppercase small">
                                    <th class="py-2">NOMBRE</th>
                                    <th class="py-2">USUARIO</th>
                                    <th class="py-2">CORREO</th>
                                    <th class="py-2">ROL ANTERIOR</th>  
                                    <th class="py-2 text-end">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="emp in empleadosActivos" :key="emp.id">
                                    <tr>
                                        <td class="fw-bold text-dark" x-text="emp.name"></td>
                                        <td  x-text="emp.username"></td>
                                        <td  x-text="emp.email"></td>
                                        <td>
                                            <span class="badge" :class="{
                                                'bg-danger': emp.role === 'ADMINISTRADOR',
                                                'bg-primary': emp.role === 'SOPORTE',
                                                'bg-secondary': emp.role === 'EMPLEADO'
                                            }" x-text="emp.role"></span>
                                        </td>
                                        <td class="text-end">

                                            <div class="d-flex justify-content-end gap-1">
                                                <!-- Ascender a SOPORTE -->
                                                <button @click="ascenderSoporte(emp.id)" 
                                                        class="btn btn-sm btn-success fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                        style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                                    <i class="fa-solid fa-arrow-up"></i> SOPORTE
                                                </button>
                                                <!-- Desactivar -->
                                                <button @click="desactivar(emp.id)" 
                                                        class="btn btn-sm btn-outline-danger fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                        style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                                    <i class="fa-solid fa-user-slash"></i> DESACTIVAR
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="empleadosActivos.length === 0 && !cargando">
                                    <td colspan="4" class="text-center text-muted text-uppercase py-3">
                                        NO HAY EMPLEADOS ACTIVOS
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Contenido de la pestaña INACTIVOS -->
                <div x-show="tabActiva === 'inactivos'" x-cloak>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted text-uppercase small">
                                    <th class="py-2">NOMBRE</th>
                                    <th class="py-2">USUARIO</th>
                                    <th class="py-2">CORREO</th>
                                    <th class="py-2">ROL ANTERIOR</th>
                                    <th class="py-2 text-end">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="emp in empleadosInactivos" :key="emp.id">
                                    <tr>
                                        <td class="fw-bold text-dark" x-text="emp.name"></td>
                                        <td x-text="emp.username"></td>
                                        <td x-text="emp.email"></td>
                                        <td>
                                            <span class="badge" :class="{
                                                'bg-danger': emp.role === 'ADMINISTRADOR',
                                                'bg-primary': emp.role === 'SOPORTE',
                                                'bg-secondary': emp.role === 'EMPLEADO'
                                            }" x-text="emp.role"></span>
                                        </td>
                                        <td class="text-end">
                                            <button @click.prevent="reactivar(emp.id)" 
                                                    class="btn btn-sm btn-warning fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                    style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                                <i class="fa-solid fa-rotate-left"></i> REACTIVAR
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="empleadosInactivos.length === 0 && !cargando">
                                    <td colspan="4" class="text-center text-muted text-uppercase py-3">
                                        NO HAY EMPLEADOS INACTIVOS
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Indicador de carga -->
                <div x-show="cargando" class="text-center py-3">
                    <div class="spinner-border text-success spinner-border-sm" role="status">
                        <span class="visually-hidden">CARGANDO...</span>
                    </div>
                    <span class="text-muted text-uppercase small ms-2">CARGANDO...</span>
                </div>

                <!-- Pie del modal -->
                <div class="d-flex justify-content-end pt-3 border-top mt-3">
                    <button type="button" class="btn btn-sm btn-secondary fw-bold text-uppercase" 
                            @click="openGestionModal = false">
                        CERRAR
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- ============================================================ -->
    <!-- MODAL DE EDICIÓN DE TÉCNICO (CON VALORES ORIGINALES)          -->
    <!-- ============================================================ -->
    <template x-if="openEditModal">
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
             style="z-index: 1060; background: rgba(0,0,0,0.5);">
            <div class="bg-white rounded-3 shadow-lg p-4" style="width: 90%; max-width: 500px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-uppercase mb-0">
                        <i class="fa-solid fa-user-pen text-success me-2"></i>
                        EDITAR TÉCNICO
                    </h6>
                    <button type="button" class="btn-close shadow-none" @click="openEditModal = false"></button>
                </div>

                <form :action="`{{ url('/admin/soporte') }}/${editUser.id}`" method="POST" class="d-flex flex-column gap-3">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">NOMBRE COMPLETO</label>
                        <!-- Quitamos text-uppercase para mostrar el valor original -->
                        <input type="text" name="name" x-model="editUser.name" required class="form-control form-control-sm fw-semibold shadow-none">
                    </div>
                    <div>
                        <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">NOMBRE DE USUARIO</label>
                        <input type="text" name="username" x-model="editUser.username" required class="form-control form-control-sm fw-semibold shadow-none">
                    </div>
                    <div>
                        <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">CORREO ELECTRÓNICO</label>
                        <input type="email" name="email" x-model="editUser.email" required class="form-control form-control-sm fw-semibold shadow-none">
                    </div>
                    <div class="p-3 bg-warning-subtle border border-warning rounded-3 text-warning-emphasis fw-semibold text-uppercase" style="font-size: 0.7rem;">
                        <strong class="d-block mb-1">🔑 ¿CAMBIAR CONTRASEÑA?</strong>
                        <p class="mb-0 font-medium" style="font-size: 0.65rem;">SI NO DESEA CAMBIAR LA CONTRASEÑA ACTUAL, DEJE LOS SIGUIENTES CAMPOS VACÍOS.</p>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">NUEVA CONTRASEÑA</label>
                            <input type="password" name="password" class="form-control form-control-sm fw-semibold shadow-none">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">CONFIRMAR</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-sm fw-semibold shadow-none">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-sm btn-light fw-bold text-uppercase px-3 shadow-none" 
                                @click="openEditModal = false">CANCELAR</button>
                        <button type="submit" class="btn btn-sm text-white fw-bold text-uppercase px-3 shadow-none d-flex align-items-center gap-2" 
                                style="background-color: #047857;">
                            <i class="fa-solid fa-save"></i> GUARDAR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('soporteApp', () => ({
            // Propiedades de modales
            openEditModal: false,
            openGestionModal: false,
            editUser: { id: '', name: '', username: '', email: '' },

            // Gestión de empleados
            tabActiva: 'activos',
            buscarEmpleado: '',
            empleadosActivos: [],
            empleadosInactivos: [],
            cargando: false,

            init() {
                this.$watch('openGestionModal', (value) => {
                    if (value) {
                        this.tabActiva = 'activos';
                        this.buscarEmpleado = '';
                        this.cargarEmpleados();
                    }
                });
            },

            // ===== GESTIÓN DE EMPLEADOS =====
            cambiarTab(tab) {
                this.tabActiva = tab;
                this.cargarEmpleados();
            },

            async cargarEmpleados() {
                this.cargando = true;
                try {
                    const tipo = this.tabActiva;
                    const buscar = encodeURIComponent(this.buscarEmpleado);
                    const response = await fetch(`/admin/soporte/empleados?tipo=${tipo}&buscar=${buscar}`);
                    if (response.ok) {
                        const data = await response.json();
                        if (tipo === 'activos') {
                            this.empleadosActivos = data;
                        } else {
                            this.empleadosInactivos = data;
                        }
                    }
                } catch (error) {
                    console.error('Error al cargar empleados:', error);
                } finally {
                    this.cargando = false;
                }
            },

            async ascenderSoporte(userId) {
                if (!confirm('¿ESTÁ SEGURO DE ASCENDER A ESTE EMPLEADO A SOPORTE TÉCNICO?')) return;
                try {
                    const response = await fetch(`/admin/soporte/asignar/${userId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    });
                    if (response.ok) {
                        this.cargarEmpleados();
                        window.location.reload();
                    } else {
                        const data = await response.json();
                        alert(data.error || 'OCURRIÓ UN ERROR AL ASCENDER AL EMPLEADO.');
                    }
                } catch (error) {
                    console.error('Error al ascender:', error);
                    alert('ERROR DE CONEXIÓN. INTENTE NUEVAMENTE.');
                }
            },

            async desactivar(userId) {
                if (!confirm('¿ESTÁ SEGURO DE DESACTIVAR ESTE EMPLEADO? PODRÁ REACTIVARLO MÁS TARDE.')) return;
                try {
                    const response = await fetch(`/admin/soporte/empleados/desactivar/${userId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    });
                    if (response.ok) {
                        this.cargarEmpleados();
                    } else {
                        const data = await response.json();
                        alert(data.error || 'OCURRIÓ UN ERROR AL DESACTIVAR EL EMPLEADO.');
                    }
                } catch (error) {
                    console.error('Error al desactivar:', error);
                    alert('ERROR DE CONEXIÓN. INTENTE NUEVAMENTE.');
                }
            },

            async reactivar(userId) {
                if (!confirm('¿ESTÁ SEGURO DE REACTIVAR ESTE EMPLEADO?')) return;
                try {
                    const response = await fetch(`/admin/soporte/empleados/reactivar/${userId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    });
                    if (response.ok) {
                        this.cargarEmpleados();
                    } else {
                        const data = await response.json();
                        alert(data.error || 'OCURRIÓ UN ERROR AL REACTIVAR EL EMPLEADO.');
                    }
                } catch (error) {
                    console.error('Error al reactivar:', error);
                    alert('ERROR DE CONEXIÓN. INTENTE NUEVAMENTE.');
                }
            },

            // ===== EDICIÓN =====
            prepararEdicion(tecnico) {
                this.editUser = { ...tecnico };
                this.openEditModal = true;
            }
        }));
    });
</script>

<!-- Estilo para x-cloak -->
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
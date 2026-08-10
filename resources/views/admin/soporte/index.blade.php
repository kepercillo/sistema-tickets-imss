@extends('layouts.app')

@section('title', 'ADMINISTRACIÓN - PERSONAL DE SOPORTE')
@section('header-title', 'CATÁLOGO DE SOPORTE')

@section('content')
<!-- Contenedor principal con variables de Alpine para Crear y Editar -->
 
<div class="d-flex flex-column gap-4" x-data="{ 
    openCreateModal: false, 
    openEditModal: false, 
    editUser: { id: '', name: '', username: '', email: '' },
    prepararEdicion(tecnico) {
        this.editUser = { ...tecnico };
        this.openEditModal = true;
    }
}">

    <!-- ENCABEZADO Y ACCIONES -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h3 class="h5 fw-bold text-uppercase mb-1" style="color: #064e3b;">PERSONAL DE SOPORTE</h3>
            <p class="text-muted fw-bold text-uppercase mb-0" style="font-size: 0.75rem;">
                GESTIÓN DE ALTAS, CAMBIOS Y BAJAS DEL PERSONAL TÉCNICO.
            </p>
        </div>
        
        <div>
            <button @click="openCreateModal = true" 
                    class="btn text-white fw-bold text-uppercase shadow-sm d-inline-flex align-items-center gap-2"
                    style="background-color: #047857; font-size: 0.8rem;">
                <i class="fa-solid fa-user-plus"></i> REGISTRAR TÉCNICO
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
                <p class="text-muted text-uppercase mb-0" style="font-size: 0.75rem;">AÚN NO HAY CUENTAS DE SOPORTE REGISTRADAS.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            <th class="py-3 px-4">ID / NOMBRE</th>
                            <th class="py-3 px-4">USUARIO</th>
                            <th class="py-3 px-4">CORREO ELECTRÓNICO</th>
                            <th class="py-3 px-4">FECHA REGISTRO</th>
                            <th class="py-3 px-4 text-end">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-secondary" style="font-size: 0.8rem;">
                        @foreach($tecnicos as $tecnico)
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase" 
                                             style="width: 36px; height: 36px; background-color: #d1fae5; color: #065f46; font-size: 0.75rem;">
                                            {{ substr($tecnico->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-bold text-dark text-uppercase">{{ $tecnico->name }}</span>
                                            <span class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">SOPORTE TÉCNICO</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-monospace text-dark text-uppercase">
                                    {{ $tecnico->username }}
                                </td>
                                <td class="py-3 px-4 text-uppercase">
                                    {{ $tecnico->email }}
                                </td>
                                <td class="py-3 px-4 text-muted" style="font-size: 0.75rem;">
                                    {{ $tecnico->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <!-- BOTÓN EDITAR -->
                                        <button @click="prepararEdicion({
                                                    id: '{{ $tecnico->id }}',
                                                    name: '{{ $tecnico->name }}',
                                                    username: '{{ $tecnico->username }}',
                                                    email: '{{ $tecnico->email }}'
                                                })"
                                                class="btn btn-sm btn-outline-success fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                style="font-size: 0.68rem; padding: 0.3rem 0.6rem;">
                                            <i class="fa-solid fa-pen"></i> EDITAR
                                        </button>

                                        <!-- BOTÓN ELIMINAR -->
                                        <form action="{{ route('admin.soporte.destroy', $tecnico) }}" 
                                              method="POST" 
                                              class="d-inline m-0"
                                              onsubmit="return confirm('¿ESTÁ COMPLETAMENTE SEGURO DE ELIMINAR ESTA CUENTA DE SOPORTE? ESTA ACCIÓN NO SE PUEDE DESHACER.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                    style="font-size: 0.68rem; padding: 0.3rem 0.6rem;">
                                                <i class="fa-solid fa-trash"></i> ELIMINAR
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

    <!-- MODAL DE REGISTRO (CON ALPINE.JS Y BOOTSTRAP STYLES) -->
    <template x-if="openCreateModal">
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);" @click.self="openCreateModal = false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                    <div class="modal-header text-white px-4 py-3 border-0" style="background-color: #064e3b;">
                        <h5 class="modal-title fs-6 fw-bold text-uppercase d-flex align-items-center gap-2 m-0">
                            <i class="fa-solid fa-user-plus"></i> REGISTRO DE NUEVO TÉCNICO
                        </h5>
                        <button type="button" @click="openCreateModal = false" class="btn-close btn-close-white shadow-none"></button>
                    </div>

                    <form action="{{ route('admin.soporte.store') }}" method="POST" class="p-4 d-flex flex-column gap-3">
                        @csrf
                        <div>
                            <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">NOMBRE COMPLETO</label>
                            <input type="text" name="name" required class="form-class form-control form-control-sm text-uppercase fw-semibold shadow-none">
                        </div>
                        <div>
                            <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">NOMBRE DE USUARIO</label>
                            <input type="text" name="username" required class="form-control form-control-sm text-uppercase fw-semibold shadow-none">
                        </div>
                        <div>
                            <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">CORREO ELECTRÓNICO</label>
                            <input type="email" name="email" required class="form-control form-control-sm text-uppercase fw-semibold shadow-none">
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">CONTRASEÑA</label>
                                <input type="password" name="password" required class="form-control form-control-sm fw-semibold shadow-none">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">CONFIRMAR</label>
                                <input type="password" name="password_confirmation" required class="form-control form-control-sm fw-semibold shadow-none">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button type="button" @click="openCreateModal = false" class="btn btn-sm btn-light fw-bold text-uppercase px-3 shadow-none">CANCELAR</button>
                            <button type="submit" class="btn btn-sm text-white fw-bold text-uppercase px-3 shadow-none d-flex align-items-center gap-2" style="background-color: #047857;">
                                <i class="fa-solid fa-save"></i> GUARDAR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- MODAL DE EDICIÓN (CON ALPINE.JS Y BOOTSTRAP STYLES) -->
    <template x-if="openEditModal">
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);" @click.self="openEditModal = false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                    <div class="modal-header text-white px-4 py-3 border-0" style="background-color: #022c22;">
                        <h5 class="modal-title fs-6 fw-bold text-uppercase d-flex align-items-center gap-2 m-0">
                            <i class="fa-solid fa-user-pen"></i> MODIFICAR DATOS DEL TÉCNICO
                        </h5>
                        <button type="button" @click="openEditModal = false" class="btn-close btn-close-white shadow-none"></button>
                    </div>

                    <!-- Envía el formulario dinámicamente a la ruta con el ID cargado -->
                    <form :action="`{{ url('/admin/soporte') }}/${editUser.id}`" method="POST" class="p-4 d-flex flex-column gap-3">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">NOMBRE COMPLETO</label>
                            <input type="text" name="name" x-model="editUser.name" required class="form-control form-control-sm text-uppercase fw-semibold shadow-none">
                        </div>
                        <div>
                            <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">NOMBRE DE USUARIO</label>
                            <input type="text" name="username" x-model="editUser.username" required class="form-control form-control-sm text-uppercase fw-semibold shadow-none">
                        </div>
                        <div>
                            <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">CORREO ELECTRÓNICO</label>
                            <input type="email" name="email" x-model="editUser.email" required class="form-control form-control-sm text-uppercase fw-semibold shadow-none">
                        </div>
                        <div class="p-3 bg-warning-subtle border border-warning rounded-3 text-warning-emphasis fw-semibold text-uppercase" style="font-size: 0.7rem;">
                            <strong class="d-block mb-1">🔑 ¿CAMBIAR CONTRASEÑA?</strong>
                            <p class="mb-0 font-medium" style="font-size: 0.65rem;">SI NO DESEA CAMBIAR LA CONTRASEÑA ACTUAL, DEJE LOS SIGUIENTES CAMPOS TOTALMENTE VACÍOS.</p>
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
                            <button type="button" @click="openEditModal = false" class="btn btn-sm btn-light fw-bold text-uppercase px-3 shadow-none">CANCELAR</button>
                            <button type="submit" class="btn btn-sm text-white fw-bold text-uppercase px-3 shadow-none d-flex align-items-center gap-2" style="background-color: #047857;">
                                <i class="fa-solid fa-save"></i> GUARDAR CAMBIOS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@push('scripts')
@vite(['resources/js/app.js'])

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const userId = @json(auth()->id());
        const userRole = @json(strtoupper(optional(auth()->user())->role));

        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('soporte-tickets-channel')
                .listen('.ticket.updated', (data) => {
                    console.log('EVENTO RECIBIDO:', data);

                    // 1. Mostrar Notificación Flotante
                    if (data.mensajeNotificacion) {
                        mostrarNotificacion(data.mensajeNotificacion, data.tipoAccion);
                    }

                    // 2. Comunicar evento a Alpine.js usando Eventos Personalizados de JS
                    window.dispatchEvent(new CustomEvent('ticket-updated', { detail: data }));

                    // 3. Recargar página solo para creación/asignación si no hay modal abierto
                    if (['CREADO', 'ASIGNADO'].includes(data.tipoAccion)) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                });
        }
    });

    // Función global para alertas emergentes
    function mostrarNotificacion(mensaje, tipo) {
        let alertBox = document.createElement('div');
        alertBox.className = 'alert alert-info alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg text-uppercase fw-bold';
        alertBox.style.zIndex = '9999';
        alertBox.innerHTML = `
            <i class="fa-solid fa-bell me-2"></i> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertBox);

        setTimeout(() => {
            alertBox.remove();
        }, 5000);
    }
</script>
@endpush
@push('scripts')
@vite(['resources/js/app.js'])

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('soporte-tickets-channel')
                .listen('.ticket.updated', (data) => {
                    console.log('EVENTO RECIBIDO EN WEBSOCKET:', data);

                    // 1. Mostrar notificación emergente si existe mensaje
                    if (data.mensajeNotificacion) {
                        mostrarNotificacionFlotante(data.mensajeNotificacion);
                    }

                    // 2. Re-transmitir a Alpine.js en cualquier blade activo
                    window.dispatchEvent(new CustomEvent('ticket-updated', { detail: data }));
                });
        }
    });

    function mostrarNotificacionFlotante(mensaje) {
        let alertBox = document.createElement('div');
        alertBox.className = 'alert alert-info alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg text-uppercase fw-bold';
        alertBox.style.zIndex = '9999';
        alertBox.innerHTML = `
            <i class="fa-solid fa-bell me-2"></i> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertBox);

        setTimeout(() => alertBox.remove(), 5000);
    }
</script>
@endpush
@endsection
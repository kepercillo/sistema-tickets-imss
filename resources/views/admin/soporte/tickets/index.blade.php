@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3" x-data="ticketsAdminSoporteApp()" x-init="init()">
    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-uppercase mb-1 text-dark">
                <i class="fa-solid fa-headset text-success me-2"></i> GESTIÓN DE TICKETS DE SOPORTE
            </h4>
            <p class="text-muted text-uppercase small mb-0">PANEL DE CONTROL Y SEGUIMIENTO DE INCIDENCIAS</p>
        </div>
    </div>

    <!-- NOTIFICACIONES Y ALERTAS -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-uppercase fw-bold shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-uppercase fw-bold shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="row align-items-center g-2">
                <div class="col-md-6">
                    <h6 class="fw-bold text-uppercase mb-0 text-secondary">
                        <i class="fa-solid fa-list me-1"></i> TICKETS REGISTRADOS ({{ $tickets->total() }})
                    </h6>
                </div>
            </div>
        </div>

        @if($tickets->isEmpty())
            <div class="text-center py-5">
                <i class="fa-solid fa-ticket-simple fa-3x text-muted mb-3 opacity-50"></i>
                <h5 class="fw-bold text-muted text-uppercase">NO HAY TICKETS REGISTRADOS</h5>
                <p class="text-muted text-uppercase small">ACTUALMENTE NO EXISTEN SOLICITUDES DE SOPORTE EN EL SISTEMA.</p>
            </div>
        @else

            <!-- VISTA TABLA (DESKTOP) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted text-uppercase small border-bottom">
                        <tr>
                            <th class="ps-4 py-3" style="width: 100px;">FOLIO</th>
                            <th class="py-3">USUARIO</th>
                            <th class="py-3">ASUNTO / TÍTULO</th>
                            <th class="py-3">ESTADO</th>
                            <th class="py-3">TÉCNICO ASIGNADO</th>
                            <th class="py-3">FECHA CREACIÓN</th>
                            <th class="pe-4 py-3 text-end" style="width: 220px;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($tickets as $ticket)
                            @php
                                $estadoUpper = strtoupper($ticket->status);
                                $estaFinalizado = in_array($estadoUpper, ['RESUELTO', 'CERRADO']);
                                $tecnicoId = $ticket->assigned_to ?? ($ticket->tecnico ? $ticket->tecnico->id : null);
                                $tieneTecnico = !empty($tecnicoId);
                                $nombreUsuario = $ticket->user ? $ticket->user->name : 'N/A';
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <span class="badge bg-secondary text-uppercase">#{{ $ticket->id }}</span>
                                </td>
                                <td class="fw-semibold text-dark text-uppercase">
                                    <i class="fa-solid fa-user text-muted me-1 small"></i> {{ $nombreUsuario }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-uppercase mb-0">
                                        <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                                        {{ $ticket->title ?? 'SIN ASUNTO' }}
                                    </div>
                                </td>
                                <td>
                                    @if($estadoUpper === 'PENDIENTE')
                                        <span class="badge bg-danger text-uppercase px-2 py-1">PENDIENTE</span>
                                    @elseif(in_array($estadoUpper, ['EN_PROCESO', 'EN PROCESO']))
                                        <span class="badge bg-warning text-dark text-uppercase px-2 py-1">EN PROCESO</span>
                                    @elseif($estadoUpper === 'RESUELTO')
                                        <span class="badge bg-success text-uppercase px-2 py-1">RESUELTO</span>
                                    @else
                                        <span class="badge bg-secondary text-uppercase px-2 py-1">{{ $ticket->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(auth()->user()->role === 'ADMINISTRADOR')
                                        @if(!$estaFinalizado)
                                            <form action="{{ route('soporte.tickets.asignar', $ticket->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <select name="assigned_to" class="form-select form-select-sm text-uppercase fw-semibold border-secondary-subtle" onchange="this.form.submit()" style="font-size: 0.78rem; min-width: 160px;">
                                                    <option value="">-- SELECCIONAR --</option>
                                                    @foreach($tecnicos as $tec)
                                                        <option value="{{ $tec->id }}" {{ $tecnicoId == $tec->id ? 'selected' : '' }}>
                                                            {{ strtoupper($tec->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @else
                                            <span class="fw-bold text-dark text-uppercase small">
                                                <i class="fa-solid fa-user-gear me-1"></i> {{ $ticket->tecnico ? strtoupper($ticket->tecnico->name) : 'N/A' }}
                                            </span>
                                        @endif
                                    @else
                                        @if($ticket->tecnico)
                                            <span class="badge bg-light text-dark border border-secondary-subtle fw-bold text-uppercase p-2" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-user-check text-success me-1"></i> {{ strtoupper($ticket->tecnico->name) }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border border-secondary-subtle fw-bold text-uppercase p-2" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-user-clock text-warning me-1"></i> SIN ASIGNAR
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-muted small text-uppercase">
                                    <i class="fa-regular fa-clock me-1"></i> {{ $ticket->created_at->format('d/m/Y H:i A') }}
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        @if(!$estaFinalizado)
                                            <form action="{{ route('tickets.update-status', $ticket->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿ESTÁS SEGURO DE QUE DESEAS FINALIZAR ESTE TICKET?');">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="RESUELTO">
                                                <button type="submit" class="btn btn-sm btn-outline-success fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none" style="font-size: 0.72rem;" {{ !$tieneTecnico ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-check-circle"></i> FINALIZAR
                                                </button>
                                            </form>
                                        @endif

                                        @if($estaFinalizado)
                                            <button disabled class="btn btn-sm btn-light text-muted fw-bold text-uppercase shadow-none border" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-lock"></i> CHAT FINALIZADO
                                            </button>
                                        @elseif($tieneTecnico)
                                            <button type="button" 
                                                    @click="abrirChat({ id: '{{ $ticket->id }}', usuario: '{{ $nombreUsuario }}' })" 
                                                    class="btn btn-sm btn-outline-primary fw-bold text-uppercase position-relative d-inline-flex align-items-center gap-1 shadow-none"
                                                    style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-comments"></i> CHAT
                                                <template x-if="unreadCounts['{{ $ticket->id }}'] > 0">
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.55rem;">
                                                        <span x-text="unreadCounts['{{ $ticket->id }}']"></span>
                                                    </span>
                                                </template>
                                            </button>
                                        @else
                                            <button disabled class="btn btn-sm btn-light text-muted fw-bold text-uppercase shadow-none border" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-lock"></i> CHAT BLOQUEADO
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- VISTA TARJETAS (MÓVIL) -->
            <div class="d-md-none p-3 d-flex flex-column gap-3">
                @foreach($tickets as $ticket)
                    @php
                        $estadoUpper = strtoupper($ticket->status);
                        $estaFinalizado = in_array($estadoUpper, ['RESUELTO', 'CERRADO']);
                        $tecnicoId = $ticket->assigned_to ?? ($ticket->tecnico ? $ticket->tecnico->id : null);
                        $tieneTecnico = !empty($tecnicoId);
                        $nombreUsuario = $ticket->user ? $ticket->user->name : 'N/A';
                    @endphp
                    <div class="card border border-light-subtle shadow-sm p-3 rounded-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-secondary text-uppercase">FOLIO #{{ $ticket->id }}</span>
                            @if($estadoUpper === 'PENDIENTE')
                                <span class="badge bg-danger text-uppercase">PENDIENTE</span>
                            @elseif(in_array($estadoUpper, ['EN_PROCESO', 'EN PROCESO']))
                                <span class="badge bg-warning text-dark text-uppercase">EN PROCESO</span>
                            @elseif($estadoUpper === 'RESUELTO')
                                <span class="badge bg-success text-uppercase">RESUELTO</span>
                            @else
                                <span class="badge bg-secondary text-uppercase">{{ $ticket->status }}</span>
                            @endif
                        </div>

                        <h6 class="fw-bold text-dark text-uppercase mb-2">
                            <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                            {{ $ticket->title ?? 'SIN ASUNTO' }}
                        </h6>

                        <div class="text-uppercase text-muted small mb-3">
                            <div class="mb-1"><i class="fa-solid fa-user me-1"></i> {{ $nombreUsuario }}</div>
                            <div class="mb-2"><i class="fa-regular fa-clock me-1"></i> {{ $ticket->created_at->format('d/m/Y H:i A') }}</div>
                            
                            @if(auth()->user()->role === 'ADMINISTRADOR')
                                @if(!$estaFinalizado)
                                    <form action="{{ route('soporte.tickets.asignar', $ticket->id) }}" method="POST" class="m-0 mt-2">
                                        @csrf
                                        @method('PATCH')
                                        <label class="form-label text-uppercase fw-bold text-muted mb-1" style="font-size: 0.65rem;">ASIGNAR TÉCNICO:</label>
                                        <select name="assigned_to" class="form-select form-select-sm text-uppercase fw-semibold border-secondary-subtle" onchange="this.form.submit()" style="font-size: 0.75rem;">
                                            <option value="">-- SELECCIONAR TÉCNICO --</option>
                                            @foreach($tecnicos as $tec)
                                                <option value="{{ $tec->id }}" {{ $tecnicoId == $tec->id ? 'selected' : '' }}>
                                                    {{ strtoupper($tec->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                @else
                                    <div><i class="fa-solid fa-user-gear me-1"></i> <span class="text-dark fw-bold">{{ $ticket->tecnico ? strtoupper($ticket->tecnico->name) : 'N/A' }}</span></div>
                                @endif
                            @else
                                @if($ticket->tecnico)
                                    <div class="mt-2">
                                        <span class="badge bg-light text-dark border border-secondary-subtle fw-bold text-uppercase p-2" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-user-check text-success me-1"></i> {{ strtoupper($ticket->tecnico->name) }}
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <span class="badge bg-light text-muted border border-secondary-subtle fw-bold text-uppercase p-2" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-user-clock text-warning me-1"></i> SIN ASIGNAR
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="d-flex justify-content-end align-items-center gap-2 pt-2 border-top">
                            @if(!$estaFinalizado)
                                <form action="{{ route('tickets.update-status', $ticket->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿ESTÁS SEGURO DE QUE DESEAS FINALIZAR ESTE TICKET?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="RESUELTO">
                                    <button type="submit" class="btn btn-sm btn-outline-success fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none" style="font-size: 0.7rem;" {{ !$tieneTecnico ? 'disabled' : '' }}>
                                        <i class="fa-solid fa-check-circle"></i> FINALIZAR
                                    </button>
                                </form>
                            @endif

                           @if($estaFinalizado)
                                <button disabled class="btn btn-sm btn-light text-muted fw-bold text-uppercase shadow-none border" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-lock"></i> CHAT FINALIZADO
                                </button>
                                @elseif($tieneTecnico)
                                @php
                                    // SOLO CONTAMOS MENSAJES NO LEÍDOS SI EL TICKET SIGUE ACTIVO
                                    // (SI YA ESTÁ FINALIZADO, $estaFinalizado SE EJECUTA ARRIBA Y ESTA LÓGICA NO SE EVALÚA)
                                    $conteoNoLeidos = $ticket->mensajes()
                                        ->where('user_id', '!=', auth()->id())
                                        ->where('is_read', 0)
                                        ->count();
                                @endphp

                                <button @click="abrirChat({
                                            id: '{{ $ticket->id }}',
                                            usuario: '{{ $ticket->user->name ?? 'N/A' }}'
                                        })" 
                                        x-init="unreadCounts['{{ $ticket->id }}'] = {{ $conteoNoLeidos }}"
                                        class="btn btn-sm btn-outline-primary fw-bold text-uppercase position-relative d-inline-flex align-items-center gap-1 shadow-none"
                                        style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-comments"></i> CHAT
                                    <template x-if="unreadCounts['{{ $ticket->id }}'] > 0">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.55rem;">
                                            <span x-text="unreadCounts['{{ $ticket->id }}']"></span>
                                        </span>
                                    </template>
                                </button>
                            @else
                                <button disabled class="btn btn-sm btn-light text-muted fw-bold text-uppercase shadow-none border" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-lock"></i> CHAT BLOQUEADO
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-3 border-top">
                {{ $tickets->links() }}
            </div>

        @endif
    </div>
<!-- BANNER DE MENSAJES NO LEÍDOS (PERSISTENTE POR 2 MINUTOS) -->
<template x-if="totalUnread > 0 && showUnreadBanner">
    <div class="position-fixed bottom-0 start-0 m-3 p-3 bg-danger text-white rounded-3 shadow-lg border border-light d-flex align-items-center justify-content-between gap-3 text-uppercase fw-bold" style="z-index: 1040; max-width: 380px;">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-envelope-open-text fa-2x animate__animated animate__shakeX animate__infinite"></i>
            <div>
                <div class="small opacity-75">MENSAJES PENDIENTES</div>
                <div style="font-size: 0.85rem;">TIENE <span x-text="totalUnread"></span> MENSAJE(S) SIN LEER</div>
            </div>
        </div>
        <button type="button" @click="cerrarBanner()" class="btn-close btn-close-white shadow-none me-1" aria-label="Cerrar"></button>
    </div>
</template>

<!-- WIDGET DE CHAT FLOTANTE -->
<template x-if="openChatModal && selectedTicket">
    <div class="position-fixed bottom-0 end-0 m-3 shadow-lg rounded-3 border-0 bg-white overflow-hidden d-flex flex-column" 
         style="z-index: 1050; width: 360px; height: 480px; max-width: 95vw;">
        
        <!-- ENCABEZADO DEL CHAT -->
        <div class="p-3 text-white d-flex justify-content-between align-items-center shadow-sm" style="background-color: #064e3b;">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <i class="fa-solid fa-comments text-warning"></i>
                <div class="text-truncate">
                    <h6 class="fw-bold text-uppercase mb-0 text-truncate" style="font-size: 0.82rem;">
                        FOLIO #<span x-text="selectedTicket.id"></span> - <span x-text="selectedTicket.usuario || 'SOPORTE'"></span>
                    </h6>
                </div>
            </div>
            <button type="button" @click="cerrarChat()" class="btn-close btn-close-white shadow-none ms-2" style="font-size: 0.75rem;"></button>
        </div>

        <!-- CUERPO DEL CHAT -->
        <div id="modal-chat-body" class="p-3 bg-light flex-grow-1 overflow-auto d-flex flex-column gap-2">
            <!-- CARGANDO -->
            <template x-if="cargandoMensajes">
                <div class="text-center my-auto py-4">
                    <div class="spinner-border text-success spinner-border-sm" role="status">
                        <span class="visually-hidden">CARGANDO...</span>
                    </div>
                    <p class="text-uppercase fw-bold text-muted small mt-2 mb-0" style="font-size: 0.7rem;">OBTENIENDO CHAT...</p>
                </div>
            </template>

            <!-- LISTADO DE MENSAJES -->
            <template x-if="!cargandoMensajes && selectedTicket.mensajes && selectedTicket.mensajes.length > 0">
                <div class="d-flex flex-column gap-2">
                    <template x-for="msg in selectedTicket.mensajes" :key="msg.id || Math.random()">
                        <div :class="String(msg.user_id) === '{{ auth()->id() }}' ? 'align-self-end bg-success text-white' : 'align-self-start bg-white text-dark border'"
                             class="p-2 rounded-3 shadow-sm text-uppercase" style="max-width: 85%; font-size: 0.75rem;">
                            <div class="fw-bold mb-1 opacity-75" style="font-size: 0.62rem;" x-text="msg.user ? msg.user.name : ''"></div>
                            <p class="mb-1 fw-semibold text-break" x-text="msg.message || msg.contenido"></p>
                            <small class="d-block text-end opacity-75" style="font-size: 0.58rem;" x-text="msg.created_at ? new Date(msg.created_at).toLocaleString('es-MX') : 'AHORA'"></small>
                        </div>
                    </template>
                </div>
            </template>

            <!-- SIN MENSAJES -->
            <template x-if="!cargandoMensajes && (!selectedTicket.mensajes || selectedTicket.mensajes.length === 0)">
                <div class="text-center my-auto text-muted fw-bold text-uppercase p-3" style="font-size: 0.72rem;">
                    <i class="fa-regular fa-comment-dots fa-2x mb-2 text-secondary opacity-50"></i>
                    <div>NO HAY MENSAJES EN ESTE TICKET AÚN.</div>
                </div>
            </template>
        </div>

        <!-- FORMULARIO DE ENVÍO -->
        <form @submit.prevent="enviarMensaje($el)" class="p-2 bg-white border-top">
            <div class="input-group">
                <input type="text" name="message" required placeholder="ESCRIBA SU MENSAJE..." class="form-control form-control-sm text-uppercase fw-semibold shadow-none border-end-0" style="font-size: 0.75rem;" autocomplete="off">
                <button type="submit" class="btn btn-sm text-white fw-bold text-uppercase px-3 shadow-none" style="background-color: #047857;">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    function ticketsAdminSoporteApp() {
        return {
            openChatModal: false,
            selectedTicket: null,
            cargandoMensajes: false,
            unreadCounts: {},
            inactivityTimer: null,
            pollTimer: null,
            showUnreadBanner: true,
            bannerTimer: null,

            init() {
                // Mapear contadores iniciales de mensajes no leídos
                @foreach($tickets as $t)
                    this.unreadCounts['{{ $t->id }}'] = {{ $t->mensajes->where('leido', false)->where('user_id', '!=', auth()->id())->count() }};
                @endforeach

                // Auto-ocultar banner tras 2 minutos (120,000 ms)
                this.iniciarTimerBanner();

                // Recargar página tras 1 minuto de inactividad
                this.resetInactivityTimer();
                ['mousemove', 'keydown', 'click', 'scroll'].forEach(evt => {
                    window.addEventListener(evt, () => this.resetInactivityTimer());
                });

                // Polling cada 10 segundos para refrescar el chat abierto
                this.pollTimer = setInterval(() => {
                    if (this.openChatModal && this.selectedTicket) {
                        this.cargarMensajes(this.selectedTicket.id, false);
                    }
                }, 10000);
            },

            get totalUnread() {
                return Object.values(this.unreadCounts).reduce((a, b) => a + b, 0);
            },

            iniciarTimerBanner() {
                clearTimeout(this.bannerTimer);
                this.bannerTimer = setTimeout(() => {
                    this.showUnreadBanner = false;
                }, 120000); // 2 minutos
            },

            cerrarBanner() {
                this.showUnreadBanner = false;
                clearTimeout(this.bannerTimer);
            },
            
            resetInactivityTimer() {
                clearTimeout(this.inactivityTimer);
                this.inactivityTimer = setTimeout(() => {
                    if (!this.openChatModal) {
                        window.location.reload();
                    }
                }, 60000); // 1 minuto
            },

            abrirChat(ticketData) {
                this.selectedTicket = {
                    id: ticketData.id,
                    usuario: ticketData.usuario,
                    mensajes: []
                };
                this.unreadCounts[ticketData.id] = 0;
                this.openChatModal = true;
                this.cargarMensajes(ticketData.id, true);
            },

            cerrarChat() {
                this.openChatModal = false;
                this.selectedTicket = null;
            },

            async cargarMensajes(ticketId, mostrarSpinner = true) {
                if (mostrarSpinner) this.cargandoMensajes = true;

                try {
                    const response = await fetch(`/tickets/${ticketId}/mensajes`);
                    if (response.ok) {
                        const data = await response.json();
                        if (this.selectedTicket && this.selectedTicket.id === ticketId) {
                            this.selectedTicket.mensajes = data;
                            this.scrollToBottom();
                        }
                    }
                } catch (error) {
                    console.error("ERROR AL CARGAR LOS MENSAJES:", error);
                } finally {
                    if (mostrarSpinner) this.cargandoMensajes = false;
                }
            },

            async enviarMensaje(form) {
                const formData = new FormData(form);
                const input = form.querySelector('input[name="message"]');
                const texto = input.value.trim().toUpperCase();

                if (!texto || !this.selectedTicket) return;

                // Agregar mensaje de forma optimista
                const tempMsg = {
                    id: 'temp_' + Date.now(),
                    user_id: '{{ auth()->id() }}',
                    contenido: texto,
                    user: { name: '{{ auth()->user()->name }}' },
                    created_at: new Date().toISOString()
                };

                this.selectedTicket.mensajes.push(tempMsg);
                input.value = '';
                this.scrollToBottom();

                try {
                    const response = await fetch(`/tickets/${this.selectedTicket.id}/mensajes`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (response.ok) {
                        this.cargarMensajes(this.selectedTicket.id, false);
                    } else {
                        alert('NO SE PUDO ENVIAR EL MENSAJE.');
                    }
                } catch (error) {
                    console.error("ERROR AL ENVIAR MENSAJE:", error);
                }
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const chatBody = document.getElementById('modal-chat-body');
                    if (chatBody) {
                        chatBody.scrollTop = chatBody.scrollHeight;
                    }
                });
            }
        };
    }
</script>
@endsection
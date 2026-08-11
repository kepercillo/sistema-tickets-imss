@extends('layouts.app')

@section('title', 'SISTEMA DE TICKETS')

@section('content')
<div class="container-fluid px-4 py-3" x-data="ticketsApp()" x-init="init()">
    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-uppercase mb-1 text-dark">
                <i class="fa-solid fa-headset text-success me-2"></i> GESTIÓN DE TICKETS DE SOPORTE
            </h4>
            <p class="text-muted text-uppercase small mb-0">PANEL DE CONTROL Y SEGUIMIENTO DE INCIDENCIAS</p>
        </div>
    </div>

    <!-- NOTIFICACIONES -->
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

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show text-uppercase fw-bold shadow-sm" role="alert">
            <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- FILTROS -->
    <div class="card border-0 shadow-sm p-3 rounded-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <form action="{{ route('tickets.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 m-0" style="min-width: 280px;">
                <div class="input-group input-group-sm flex-grow-1">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="form-control form-control-sm text-uppercase fw-semibold shadow-none border-start-0" 
                           placeholder="BUSCAR POR FECHA, PROBLEMA O TÉCNICO">
                </div>
                <button type="submit" class="btn btn-sm text-white fw-bold text-uppercase px-3 shadow-sm flex-shrink-0" style="background-color: #047857;">
                    <i class="fa-solid fa-filter me-1"></i> FILTRAR
                </button>
                @if(request('search'))
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light fw-bold text-uppercase shadow-none border flex-shrink-0">
                        <i class="fa-solid fa-xmark me-1"></i> LIMPIAR
                    </a>
                @endif
            </form>

            <div class="flex-shrink-0">
                <a href="{{ route('tickets.create') }}" class="btn btn-sm text-white fw-bold text-uppercase px-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2" style="background-color: #1C6046;">
                    <i class="fa-solid fa-plus"></i> NUEVO TICKET
                </a>
            </div>
        </div>
    </div>

    <!-- LISTADO DE TICKETS -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        @if($tickets->isEmpty())
            <div class="card-body p-5 text-center">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-ticket-simple fs-3"></i>
                </div>
                <h5 class="fw-bold text-secondary text-uppercase fs-6 mb-1">NO SE ENCONTRARON TICKETS</h5>
                <p class="text-muted text-uppercase mb-3" style="font-size: 0.75rem;">NO HAY TICKETS REGISTRADOS O CON LOS CRITERIOS DE BÚSQUEDA.</p>
                <a href="{{ route('tickets.create') }}" class="btn btn-sm text-white fw-bold text-uppercase px-4 shadow-sm" style="background-color: #1C6046;">
                    <i class="fa-solid fa-plus me-1"></i> CREAR MI PRIMER TICKET
                </a>
            </div>
        @else

            {{-- 1. VISTA TABLA DESKTOP --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            <th class="py-3 px-4">FOLIO / PROBLEMA</th>
                            <th class="py-3 px-4">FECHA CREACIÓN</th>
                            <th class="py-3 px-4">ESTADO</th>
                            <th class="py-3 px-4">TÉCNICO ASIGNADO</th>
                            <th class="py-3 px-4 text-end">ACCIONES / CHAT</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-secondary" style="font-size: 0.8rem;">
                        @foreach($tickets as $ticket)
                            @php
                                $estadoUpper = strtoupper($ticket->status);
                                $estaFinalizado = in_array($estadoUpper, ['RESUELTO', 'CERRADO']);
                                $tieneTecnico = !empty($ticket->assigned_to) || !empty($ticket->tecnico);
                            @endphp
                            <tr>
                                <td class="py-3 px-4">
                                    <span class="badge bg-secondary text-uppercase mb-1">FOLIO #{{ $ticket->id }}</span>
                                    <span class="d-block fw-bold text-dark text-uppercase small text-truncate" style="max-width: 250px;">
                                        <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                                        {{ $ticket->title ?? 'SIN ASUNTO' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-muted" style="font-size: 0.75rem;">
                                    {{ $ticket->created_at->format('d/m/Y H:i A') }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($estadoUpper === 'PENDIENTE')
                                        <span class="badge bg-danger text-uppercase">PENDIENTE</span>
                                    @elseif(in_array($estadoUpper, ['EN_PROCESO', 'EN PROCESO']))
                                        <span class="badge bg-warning text-dark text-uppercase">EN PROCESO</span>
                                    @elseif($estadoUpper === 'RESUELTO')
                                        <span class="badge bg-success text-uppercase">RESUELTO</span>
                                    @else
                                        <span class="badge bg-secondary text-uppercase">{{ $ticket->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-uppercase">
                                    @if($ticket->tecnico)
                                        <span class="text-dark fw-bold"><i class="fa-solid fa-user-gear me-1"></i> {{ $ticket->tecnico->name }}</span>
                                    @else
                                        <span class="text-muted fst-italic">SIN ASIGNAR</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        @if(!$estaFinalizado)
                                            <!-- Botón FINALIZAR (empleado) -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                                    style="font-size: 0.68rem; padding: 0.3rem 0.6rem;"
                                                    @click.prevent="abrirModalFinalizar('{{ $ticket->id }}')"
                                                    {{ !$tieneTecnico ? 'disabled' : '' }}
                                                    title="{{ $tieneTecnico ? 'FINALIZAR TICKET' : 'DEBES ASIGNAR UN TÉCNICO ANTES DE FINALIZAR' }}">
                                                <i class="fa-solid fa-check-circle"></i> FINALIZAR
                                            </button>
                                        @endif

                                        @if($estaFinalizado)
                                            <button disabled class="btn btn-sm btn-light text-muted fw-bold text-uppercase shadow-none border" style="font-size: 0.68rem; padding: 0.3rem 0.6rem;">
                                                <i class="fa-solid fa-lock"></i> CHAT FINALIZADO
                                            </button>
                                        @elseif($tieneTecnico)
                                            <button @click="abrirChat({
                                                        id: '{{ $ticket->id }}',
                                                        usuario: '{{ $ticket->user->name ?? 'N/A' }}'
                                                    })" 
                                                    class="btn btn-sm btn-outline-primary fw-bold text-uppercase position-relative d-inline-flex align-items-center gap-1 shadow-none"
                                                    style="font-size: 0.68rem; padding: 0.3rem 0.6rem;">
                                                <i class="fa-solid fa-comments"></i> CHAT
                                                <template x-if="unreadCounts['{{ $ticket->id }}'] > 0">
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.55rem;">
                                                        <span x-text="unreadCounts['{{ $ticket->id }}']"></span>
                                                    </span>
                                                </template>
                                            </button>
                                        @else
                                            <button disabled class="btn btn-sm btn-light text-muted fw-bold text-uppercase shadow-none border" style="font-size: 0.68rem; padding: 0.3rem 0.6rem;">
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

            {{-- 2. VISTA TARJETAS MÓVIL --}}
            <div class="d-md-none p-3 d-flex flex-column gap-3">
                @foreach($tickets as $ticket)
                    @php
                        $estadoUpper = strtoupper($ticket->status);
                        $estaFinalizado = in_array($estadoUpper, ['RESUELTO', 'CERRADO']);
                        $tieneTecnico = !empty($ticket->assigned_to) || !empty($ticket->tecnico);
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
                            <div class="mb-1"><i class="fa-regular fa-clock me-1"></i> {{ $ticket->created_at->format('d/m/Y H:i A') }}</div>
                            <div>
                                <i class="fa-solid fa-user-gear me-1"></i>
                                @if($ticket->tecnico)
                                    <span class="text-dark fw-bold">{{ $ticket->tecnico->name }}</span>
                                @else
                                    <span class="fst-italic">SIN ASIGNAR</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end align-items-center gap-2 pt-2 border-top">
                            @if(!$estaFinalizado)
                                <!-- Botón FINALIZAR (empleado) -->
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success fw-bold text-uppercase d-inline-flex align-items-center gap-1 shadow-none"
                                        style="font-size: 0.7rem;"
                                        @click.prevent="abrirModalFinalizar('{{ $ticket->id }}')"
                                        {{ !$tieneTecnico ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-check-circle"></i> FINALIZAR
                                </button>
                            @else
                                <button disabled class="btn btn-sm btn-light text-muted fw-bold text-uppercase shadow-none border" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-lock"></i> FINALIZADO
                                </button>
                            @endif

                            {{-- Chat (si no está finalizado y tiene técnico) --}}
                            @if($estaFinalizado)
                                <button disabled class="btn btn-sm btn-light text-muted fw-bold text-uppercase shadow-none border" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-lock"></i> CHAT FINALIZADO
                                </button>
                            @elseif($tieneTecnico)
                                <button @click="abrirChat({
                                            id: '{{ $ticket->id }}',
                                            usuario: '{{ $ticket->user->name ?? 'N/A' }}'
                                        })" 
                                        class="btn btn-sm btn-outline-primary fw-bold text-uppercase position-relative d-inline-flex align-items-center gap-1 shadow-none"
                                        style="font-size: 0.7rem;">
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

            <!-- PAGINACIÓN -->
            <div class="p-3 border-top">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL DE FINALIZACIÓN (Alpine) -->
    <template x-if="showModalFinalizar">
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
             style="z-index: 1060; background: rgba(0,0,0,0.5);">
            <div class="bg-white rounded-3 shadow-lg p-4" style="width: 90%; max-width: 450px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-uppercase mb-0">
                        <i class="fa-solid fa-check-circle text-success me-2"></i>
                        FINALIZAR TICKET #<span x-text="ticketIdFinalizar"></span>
                    </h6>
                    <button type="button" class="btn-close shadow-none" @click="cerrarModalFinalizar()"></button>
                </div>

                <div class="mb-3">
                    <label for="motivoFinalizarInput" class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">
                        ESCRIBA UNA BREVE DESCRIPCIÓN DEL MOTIVO DE CIERRE <span class="text-danger">*</span>
                    </label>
                    <textarea id="motivoFinalizarInput" 
                              class="form-control text-uppercase shadow-none" 
                              rows="4"
                              x-model="motivoFinalizar"
                              placeholder="EJEMPLO: EL PROBLEMA QUEDÓ RESUELTO SATISFACTORIAMENTE..."
                              :disabled="enviandoFinalizar"></textarea>
                    <small class="text-muted text-uppercase" style="font-size: 0.6rem;">
                        MÍNIMO 4 PALABRAS
                    </small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-secondary fw-bold text-uppercase" 
                            @click="cerrarModalFinalizar()" :disabled="enviandoFinalizar">
                        CANCELAR
                    </button>
                    <button type="button" class="btn btn-sm btn-success fw-bold text-uppercase" 
                            @click.prevent="enviarCierre()" :disabled="enviandoFinalizar">
                        <span x-show="!enviandoFinalizar"><i class="fa-solid fa-check me-1"></i> FINALIZAR</span>
                        <span x-show="enviandoFinalizar">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> ENVIANDO...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- BANNER DE MENSAJES NO LEÍDOS -->
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
                <template x-if="cargandoMensajes">
                    <div class="text-center my-auto py-4">
                        <div class="spinner-border text-success spinner-border-sm" role="status">
                            <span class="visually-hidden">CARGANDO...</span>
                        </div>
                        <p class="text-uppercase fw-bold text-muted small mt-2 mb-0" style="font-size: 0.7rem;">OBTENIENDO CHAT...</p>
                    </div>
                </template>

                <template x-if="!cargandoMensajes && selectedTicket.mensajes && selectedTicket.mensajes.length > 0">
                    <div class="d-flex flex-column gap-2">
                        <template x-for="msg in selectedTicket.mensajes" :key="msg.id || Math.random()">
                            <div :class="String(msg.user_id) === '{{ auth()->id() }}' ? 'align-self-end bg-success text-white' : 'align-self-start bg-white text-dark border'"
                                 class="p-2 rounded-3 shadow-sm text-uppercase" style="max-width: 85%; font-size: 0.75rem;">
                                
                                <div class="fw-bold mb-1 opacity-75" style="font-size: 0.62rem;" x-text="msg.user ? msg.user.name : 'USUARIO'"></div>
                                
                                <p class="mb-1 fw-semibold text-break" x-text="msg.message || msg.contenido"></p>
                                
                                <small class="d-block text-end opacity-75" style="font-size: 0.58rem;" 
                                       x-text="msg.created_at ? new Date(msg.created_at).toLocaleString('es-MX', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit' }) : 'AHORA'">
                                </small>
                            </div>
                        </template>
                    </div>
                </template>

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
</div>

<script>
    function ticketsApp() {
        return {
            // Chat
            openChatModal: false,
            selectedTicket: null,
            cargandoMensajes: false,
            unreadCounts: {},
            inactivityTimer: null,
            pollTimer: null,
            showUnreadBanner: true,
            bannerTimer: null,

            // Modal de finalización
            showModalFinalizar: false,
            ticketIdFinalizar: null,
            motivoFinalizar: '',
            enviandoFinalizar: false,

            init() {
                // Cargar contadores de mensajes no leídos desde el backend
                @php
                    $unreadData = [];
                    foreach($tickets as $ticket) {
                        $userId = auth()->id();
                        $count = $ticket->messages->where('is_read', 0)->where('user_id', '!=', $userId)->count();
                        $unreadData[(string)$ticket->id] = $count;
                    }
                @endphp
                this.unreadCounts = @json($unreadData);

                this.iniciarTimerBanner();
                this.resetInactivityTimer();

                ['mousemove', 'keydown', 'click', 'scroll'].forEach(evt => {
                    window.addEventListener(evt, () => this.resetInactivityTimer());
                });

                this.pollTimer = setInterval(() => {
                    if (this.openChatModal && this.selectedTicket) {
                        this.cargarMensajes(this.selectedTicket.id, false);
                    }
                }, 10000);
            },

            get totalUnread() {
                return Object.values(this.unreadCounts).reduce((a, b) => Number(a) + Number(b), 0);
            },

            iniciarTimerBanner() {
                clearTimeout(this.bannerTimer);
                this.bannerTimer = setTimeout(() => {
                    this.showUnreadBanner = false;
                }, 120000);
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
                }, 60000);
            },

            // ===== CHAT =====
            abrirChat(ticketData) {
                const id = String(ticketData.id);
                this.selectedTicket = {
                    id: id,
                    usuario: ticketData.usuario,
                    mensajes: []
                };
                this.unreadCounts[id] = 0;
                this.openChatModal = true;
                this.cargarMensajes(id, true);
            },

            cerrarChat() {
                this.openChatModal = false;
                this.selectedTicket = null;
            },

            async cargarMensajes(ticketId, mostrarSpinner = true) {
                const id = String(ticketId);
                if (mostrarSpinner) this.cargandoMensajes = true;

                try {
                    const response = await fetch(`/tickets/${id}/mensajes`);
                    if (response.ok) {
                        const data = await response.json();
                        const mensajesObtenidos = Array.isArray(data) ? data : (data.mensajes || []);
                        if (this.selectedTicket && String(this.selectedTicket.id) === id) {
                            this.selectedTicket.mensajes = mensajesObtenidos;
                            this.scrollToBottom();
                        }
                        this.unreadCounts[id] = 0;
                    }
                } catch (error) {
                    console.error('ERROR AL CARGAR LOS MENSAJES:', error);
                } finally {
                    if (mostrarSpinner) this.cargandoMensajes = false;
                }
            },

            async enviarMensaje(form) {
                const formData = new FormData(form);
                const input = form.querySelector('input[name="message"]');
                const texto = input.value.trim().toUpperCase();
                if (!texto || !this.selectedTicket) return;

                const tempMsg = {
                    id: 'temp_' + Date.now(),
                    user_id: '{{ auth()->id() }}',
                    message: texto,
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
                    console.error('ERROR AL ENVIAR MENSAJE:', error);
                }
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const chatBody = document.getElementById('modal-chat-body');
                    if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;
                });
            },

            // ===== MODAL DE FINALIZACIÓN =====
            abrirModalFinalizar(ticketId) {
                this.ticketIdFinalizar = ticketId;
                this.motivoFinalizar = '';
                this.showModalFinalizar = true;
            },

            cerrarModalFinalizar() {
                this.showModalFinalizar = false;
                this.ticketIdFinalizar = null;
                this.motivoFinalizar = '';
            },

            async enviarCierre() {
                const palabras = this.motivoFinalizar.trim().split(/\s+/).filter(p => p.length > 0);
                if (palabras.length < 4) {
                    alert('DEBE ESCRIBIR AL MENOS 4 PALABRAS PARA DESCRIBIR EL MOTIVO.');
                    return;
                }
                if (!this.ticketIdFinalizar) return;

                this.enviandoFinalizar = true;

                try {
                    const response = await fetch(`/tickets/${this.ticketIdFinalizar}/cerrar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            solucion: this.motivoFinalizar.toUpperCase().trim(),
                        }),
                    });

                    if (response.ok) {
                        this.cerrarModalFinalizar();
                        window.location.reload();
                    } else {
                        const data = await response.json();
                        alert(data.error || 'OCURRIÓ UN ERROR AL FINALIZAR EL TICKET.');
                    }
                } catch (error) {
                    console.error('Error al finalizar:', error);
                    alert('ERROR DE CONEXIÓN. INTENTE NUEVAMENTE.');
                } finally {
                    this.enviandoFinalizar = false;
                }
            }
        };
    }
</script>
@endsection
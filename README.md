

1. Introducción
El Sistema de Tickets de Soporte Técnico es una aplicación web desarrollada para el IMSS Chiapas que permite gestionar incidencias y solicitudes de soporte técnico de manera eficiente. Está diseñada para tres roles principales: Empleado, Soporte y Administrador, cada uno con funcionalidades específicas.

2. Arquitectura General
Componente	Tecnología
Backend	Laravel 12.x (PHP 8.4+)
Frontend	Blade + Alpine.js + Bootstrap 5
Estilos	CSS con Vite + Bootstrap Icons + FontAwesome
Base de Datos	MySQL / MariaDB
Notificaciones	Laravel Notifications (correo) + WebSockets (Pusher/Echo)
Autenticación	Laravel Auth con roles personalizados
Gestión de Activos	Vite (compilación de CSS/JS)

8. Flujos de Trabajo Clave
8.1 Creación de Ticket (EMPLEADO)
El empleado completa el formulario con categoría, asunto y descripción.

Se guarda el ticket con estado PENDIENTE.

El ADMIN o SOPORTE pueden asignarlo a un técnico (desde la vista de soporte).

El sistema notifica en tiempo real (si está configurado).

8.2 Asignación y Resolución (SOPORTE/ADMIN)
El ADMIN selecciona un técnico en el ticket (desplegable).

El ticket cambia a estado EN_PROCESO.

El técnico o ADMIN pueden resolver el ticket:

Abren el modal "RESOLVER".

Escriben una solución detallada (mínimo 10 palabras).

El estado cambia a RESUELTO y se guarda la solución.

El empleado recibe notificación (si está configurado).

8.3 Cierre de Ticket (EMPLEADO)
El empleado visualiza su ticket en estado EN_PROCESO o PENDIENTE (si ya fue atendido).

Hace clic en "FINALIZAR".

En el modal, escribe un motivo de cierre (mínimo 4 palabras).

El ticket cambia a RESUELTO y el chat se bloquea.

8.4 Chat en Tiempo Real
El usuario abre el chat desde el ticket.

Los mensajes se cargan y se marcan como leídos para el usuario actual.

Se envía un mensaje mediante fetch a la ruta de mensajes.

El polling cada 10 segundos refresca el chat (o WebSockets si está configurado).

8.5 Administración de Empleados (ADMIN)
El ADMIN abre el modal "Administrar Empleados".

Ve la pestaña "Activos" con todos los empleados (role = EMPLEADO).

Puede ascender a SOPORTE (cambia el rol) o desactivar (soft delete).

En la pestaña "Inactivos", ve todos los usuarios con deleted_at NOT NULL (cualquier rol).

Puede reactivar (restaurar) cualquier usuario inactivo.


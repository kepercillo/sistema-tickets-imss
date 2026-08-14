

app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── UserController.php
│   │   ├── TicketController.php
│   │   ├── SoporteTicketController.php
│   │   ├── AdminSoporteController.php
│   │   ├── TicketMessageController.php
│   │   ├── ProblemaFrecuenteController.php
│   │   ├── DirectoryController.php
│   │   ├── PerfilController.php
│   │   └── DashboardController.php
│   └── Middleware/
│       └── RoleMiddleware.php (personalizado)
├── Models/
│   ├── User.php
│   ├── Ticket.php
│   ├── TicketMessage.php
│   ├── Category.php
│   └── Clues.php (si aplica)
├── Notifications/
│   └── BienvenidaUsuarioNotification.php
└── Views/
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── tickets/
    │   ├── index.blade.php (EMPLEADO)
    │   └── create.blade.php
    ├── soporte/
    │   └── tickets/
    │       └── index.blade.php (SOPORTE/ADMIN)
    ├── admin/
    │   └── soporte/
    │       └── index.blade.php (ADMIN)
    ├── problemas-frecuentes/
    │   └── index.blade.php
    ├── directory/
    │   └── index.blade.php
    ├── layouts/
    │   └── app.blade.php
    └── dashboard/
        └── index.blade.php




4. Roles y Permisos
Rol	Descripción	Acceso
EMPLEADO	Usuario final que reporta incidencias	- Mis Tickets (CRUD propio)
- Finalizar sus tickets (con motivo)
- Chat en sus tickets
- Problemas frecuentes
- Directorio IMSS
SOPORTE	Técnico asignado para resolver incidencias	- Gestión de Soporte (todos los tickets)
- Asignar técnicos (si es ADMIN)
- Resolver tickets (mínimo 10 palabras)
- Chat en todos los tickets
- Problemas frecuentes
- Directorio IMSS
ADMINISTRADOR	Gestión completa del sistema	- Gestión de Soporte
- Gestión de personal (altas/bajas, roles)
- Administrar empleados (activos/inactivos)
- Promover/revertir roles
- Ver todos los tickets
- Resolver tickets
- Chat en todos los tickets


5. Módulos Funcionales
5.1 Autenticación y Registro
Login: Formulario con validación de credenciales.

Registro: Formulario con campos personalizados (CLUES, departamento, nombre completo, correo, contraseña).

Recuperación de contraseña: Envío de enlace de restablecimiento por correo.

Registro de soporte: El ADMIN puede crear usuarios con rol SOPORTE.

5.2 Gestión de Tickets (EMPLEADO)
Listado: Tabla con todos los tickets del usuario (filtro por búsqueda).

Creación: Formulario con categoría, asunto y descripción.

Finalización: Modal con campo de motivo (mínimo 4 palabras) que cambia el estado a RESUELTO.

Chat: Ventana flotante con mensajes en tiempo real (polling cada 10s).

5.3 Gestión de Soporte (SOPORTE/ADMIN)
Listado: Tabla con todos los tickets (filtro por búsqueda).

Asignación: El ADMIN puede asignar técnicos a tickets.

Resolución: Modal con campo de solución (mínimo 10 palabras) que cambia el estado a RESUELTO.

Chat: Ventana flotante con mensajes en tiempo real (polling cada 10s).

5.4 Administración de Personal (ADMIN)
Listado de técnicos: Tabla con usuarios SOPORTE y ADMINISTRADOR.

Edición: Modal para modificar datos del técnico (nombre, usuario, correo, contraseña).

Promover a ADMIN: Convierte SOPORTE/EMPLEADO en ADMINISTRADOR.

Revertir ADMIN: Convierte ADMINISTRADOR a SOPORTE (solo si hay más de 1 admin).

Revertir a EMPLEADO: Convierte SOPORTE a EMPLEADO.

Eliminar: Elimina la cuenta (con validación de último admin).

5.5 Gestión de Empleados (ADMIN)
Modal "Administrar Empleados":

Pestañas: Activos (EMPLEADO con deleted_at NULL) e Inactivos (todos los roles con deleted_at NOT NULL).

Buscador: Filtra por nombre, usuario o correo.

Acciones:

Ascender a SOPORTE (en activos).

Desactivar (soft delete) en activos.

Reactivar en inactivos.

Columna de rol en inactivos (para identificar qué perfil tenía).

5.6 Problemas Frecuentes
Listado: Muestra tickets resueltos con soluciones de al menos 5 palabras.

Búsqueda: Filtra por description (problema) y solucion.

Detalle: Vista completa del problema y solución.

5.7 Directorio IMSS
Búsqueda de unidades y departamentos (pendiente de detalle).

5.8 Notificaciones en Tiempo Real (Opcional)
Pusher / Echo: Actualizaciones de tickets (creado, asignado, resuelto).

Banner de mensajes no leídos: Persiste por 2 minutos.

6. Modelos y Relaciones
6.1 User (usuarios)
php
id, name, username, email, password, role, clues, department, deleted_at
Relaciones:

hasMany(Ticket::class, 'user_id') - tickets creados

hasMany(Ticket::class, 'assigned_to') - tickets asignados como técnico

hasMany(TicketMessage::class, 'user_id') - mensajes enviados

6.2 Ticket (incidencias)
php
id, user_id, category_id, title, description, clues_at_report, department_at_report, 
status, assigned_to, solucion, resolved_at, created_at, updated_at
Relaciones:

belongsTo(User::class, 'user_id') - creador

belongsTo(User::class, 'assigned_to') - técnico asignado

belongsTo(Category::class, 'category_id')

hasMany(TicketMessage::class, 'ticket_id') - mensajes del chat

6.3 TicketMessage (mensajes del chat)
php
id, ticket_id, user_id, message, is_read, created_at, updated_at
Relaciones:

belongsTo(Ticket::class, 'ticket_id')

belongsTo(User::class, 'user_id')

6.4 Category (categorías)
php
id, name, description, created_at
Relaciones:

hasMany(Ticket::class, 'category_id')
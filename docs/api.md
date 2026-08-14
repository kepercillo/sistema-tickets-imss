
7. Endpoints Principales
7.1 Públicos
Método	URI	Función
GET	/	Redirige a login
GET	/login	Formulario de login
POST	/login	Procesar login
GET	/registro	Formulario de registro
POST	/registro	Procesar registro
GET	/registro-exitoso	Página de éxito
GET	/recuperar-contrasena	Solicitar restablecimiento
POST	/recuperar-contrasena	Enviar enlace
GET	/restablecer-contrasena/{token}	Formulario de reset
POST	/restablecer-contrasena	Procesar reset


7.2 Autenticados (todos los roles)
Método	URI	Función
POST	/logout	Cerrar sesión
GET	/dashboard	Dashboard
GET	/mis-tickets	Lista de tickets (EMPLEADO)
GET	/tickets/nuevo	Crear ticket (EMPLEADO)
POST	/tickets	Guardar ticket
POST	/tickets/{ticket}/cerrar	Cerrar ticket (EMPLEADO)
GET	/tickets/{ticket}/mensajes	Obtener mensajes
POST	/tickets/{ticket}/mensajes	Enviar mensaje
GET	/problemas-frecuentes	Lista de problemas frecuentes
GET	/problemas-frecuentes/{ticket}	Detalle de problema
GET	/directorio	Directorio IMSS
GET	/perfil	Editar perfil
PUT	/perfil	Actualizar perfil
GET	/api/buscar-clues	Buscar CLUES


7.3 SOPORTE y ADMINISTRADOR
Método	URI	Función
GET	/soporte/tickets	Gestión de soporte
PATCH	/soporte/tickets/{ticket}/atender	Atender ticket
POST	/soporte/tickets/{id}/resolver	Resolver ticket
PATCH	/soporte/tickets/{ticket}/asignar	Asignar técnico (ADMIN)
GET	/soporte/tickets/{id}/chat	Chat específico
POST	/soporte/tickets/{id}/chat/mensaje	Enviar mensaje desde soporte

7.4 ADMINISTRADOR
Método	URI	Función
GET	/admin/soporte	Lista de técnicos
POST	/admin/soporte	Crear técnico
PUT	/admin/soporte/{user}	Editar técnico
DELETE	/admin/soporte/{user}	Eliminar técnico
GET	/admin/soporte/empleados-disponibles	Lista de empleados para ascender
POST	/admin/soporte/asignar/{user}	Ascender a SOPORTE
POST	/admin/soporte/revertir/{user}	Revertir a EMPLEADO
POST	/admin/soporte/promover-admin/{user}	Ascender a ADMIN
POST	/admin/soporte/revertir-admin/{user}	Revertir ADMIN a SOPORTE
GET	/admin/soporte/empleados	Lista de empleados (activos/inactivos)
POST	/admin/soporte/empleados/desactivar/{user}	Desactivar empleado
POST	/admin/soporte/empleados/reactivar/{user}	Reactivar empleado
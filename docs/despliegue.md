9. Guía de Instalación y Configuración
9.1 Requisitos
PHP 8.4 o superior

Composer

MySQL / MariaDB

Node.js + NPM (para Vite y assets)

Servidor web (Apache/Nginx) o Laragon

9.2 Pasos de Instalación

# Clonar repositorio
git clone https://github.com/tu-repo/sistema-tickets.git
cd sistema-tickets

# Instalar dependencias PHP
composer install

# Copiar archivo .env
cp .env.example .env

# Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_tickets
DB_USERNAME=root
DB_PASSWORD=

# Configurar correo (para notificaciones)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-correo@gmail.com
MAIL_PASSWORD=tu-contraseña-de-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-correo@gmail.com
MAIL_FROM_NAME="Sistema Tickets IMSS"

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Instalar dependencias frontend
npm install

# Compilar assets (para desarrollo)
npm run dev

# Para producción
npm run build


9.3 Usuario por Defecto (Seeder)
Admin: admin@admin.com / Admin123!

Soporte: soporte@soporte.com / Soporte123!

Empleado: empleado@empleado.com / Empleado123!

9.4 Configuración de WebSockets (Opcional)
Si usas Pusher/Echo para notificaciones en tiempo real:


# Instalar Pusher
composer require pusher/pusher-php-server
npm install pusher-js laravel-echo

# Configurar .env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=tu-id
PUSHER_APP_KEY=tu-key
PUSHER_APP_SECRET=tu-secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Ejecutar servidor de broadcasting (para desarrollo)
php artisan queue:work


chmod -R 775 storage bootstrap/cache
chmod -R 775 public/uploads  # si usas archivos adjuntos


10. Mantenimiento y Buenas Prácticas
10.1 Limpieza de Caché

php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize

10.2 Backup de Base de Datos
bash
php artisan db:backup  # (si usas spatie/laravel-backup)

10.3 Logs y Monitoreo
Los logs se guardan en storage/logs/laravel.log.

Revisar periódicamente para errores y depuración.

10.4 Seguridad
Roles y permisos: el middleware role protege las rutas.

CSRF: todas las peticiones POST/PUT/DELETE incluyen token.

Contraseñas: se almacenan con Hash::make() y validación de complejidad.

Validación de datos: en todos los controladores con Validator o $request->validate().

10.5 Rendimiento
Eager Loading: usar with() para evitar N+1 en consultas.

Paginación: paginate(10) para listados grandes.

Caché: almacenar configuraciones y rutas en caché para producción.

11. Personalización y Extensiones
11.1 Agregar Nuevo Rol
Actualizar el middleware RoleMiddleware.

Agregar el nuevo rol en el seeder y en la lógica de autenticación.

Crear nuevas vistas y rutas según el rol.

11.2 Agregar Campos a Tablas
Crear una migración: php artisan make:migration add_campo_to_tickets_table.

Actualizar $fillable en el modelo.

Actualizar vistas y controladores para manejar el nuevo campo.

11.3 Integrar WebSockets
Configurar Pusher y Laravel Echo.

Disparar eventos en TicketController y SoporteTicketController.

Escuchar eventos en el frontend y mostrar notificaciones en tiempo real.

11.4 Exportación de Reportes
Instalar barryvdh/laravel-dompdf o maatwebsite/excel.

Agregar rutas y métodos para exportar listados de tickets.

Crear vistas de exportación (PDF/Excel).

12. Solución de Problemas Comunes
Problema	Posible Causa	Solución
Error 500 al enviar formulario	Validación fallida o error en controlador	Revisar logs en storage/logs/laravel.log
Las rutas no funcionan	Caché de rutas desactualizada	php artisan route:clear
Los assets no se cargan	Vite no compilado	npm run build o npm run dev
El correo no se envía	Configuración de mail incorrecta	Verificar .env y ejecutar php artisan config:clear
Los modales no abren	Alpine.js no cargado o error en HTML	Revisar @vite('resources/js/app.js') y consola
El chat no envía mensajes	CSRF token inválido o ruta incorrecta	Verificar X-CSRF-TOKEN en fetch y rutas
13. Créditos y Contacto
Desarrollador: Ing. José Eduardo Estrada Gálvez

Institución: IMSS Chiapas - Tecnología de la Información

Versión: 1.0.0

Fecha: Agosto 2026

14. Apéndice: Estructura de Tablas
Tabla users
sql
id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name        VARCHAR(255),
username    VARCHAR(255) UNIQUE,
email       VARCHAR(255) UNIQUE,
password    VARCHAR(255),
role        VARCHAR(50) DEFAULT 'EMPLEADO',
clues       VARCHAR(50),
department  VARCHAR(255),
created_at  TIMESTAMP,
updated_at  TIMESTAMP,
deleted_at  TIMESTAMP NULL,
Tabla tickets
sql
id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_id            BIGINT UNSIGNED,
category_id        BIGINT UNSIGNED,
title              VARCHAR(255),
description        TEXT,
clues_at_report    VARCHAR(50),
department_at_report VARCHAR(255),
status             VARCHAR(50) DEFAULT 'PENDIENTE',
assigned_to        BIGINT UNSIGNED NULL,
solucion           TEXT NULL,
resolved_at        TIMESTAMP NULL,
created_at         TIMESTAMP,
updated_at         TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id),
FOREIGN KEY (category_id) REFERENCES categories(id),
FOREIGN KEY (assigned_to) REFERENCES users(id)
Tabla ticket_messages
sql
id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
ticket_id  BIGINT UNSIGNED,
user_id    BIGINT UNSIGNED,
message    TEXT,
is_read    BOOLEAN DEFAULT FALSE,
created_at TIMESTAMP,
updated_at TIMESTAMP,
FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
FOREIGN KEY (user_id) REFERENCES users(id)
Tabla categories
sql
id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name        VARCHAR(255),
description TEXT,
created_at  TIMESTAMP,
updated_at  TIMESTAMP
Fin de la Documentación
Última actualización: Agosto 2026
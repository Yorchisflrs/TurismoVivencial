# GUÍA PARA SUBIR HOGARTOURS A LA WEB

## PASO 1: PREPARAR ARCHIVOS PARA PRODUCCIÓN

### Archivos a subir:
- src/ (todos los archivos)
- templates/ (todos los archivos)  
- public/ (CSS, JS, imágenes)
- config/ (modificar database.php)
- index.php
- .htaccess (crear para URLs amigables)

### Archivos a NO subir:
- make_user_host.php
- verify_system.php
- test_*.php
- debug_*.php
- check_*.php
- Archivos temporales

## PASO 2: CONFIGURAR BASE DE DATOS

1. Crear base de datos en el hosting
2. Exportar desde XAMPP: 
   - Ir a phpMyAdmin
   - Seleccionar "hogartours"
   - Exportar → SQL
3. Importar en hosting
4. Actualizar config/database.php con nuevos datos

## PASO 3: CREAR .HTACCESS

RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

## PASO 4: CONFIGURACIÓN DE PRODUCCIÓN

- Cambiar URLs de localhost
- Configurar permisos de carpetas uploads/
- Verificar PHP version (8.0+)
- Activar HTTPS

## HOSTINGS RECOMENDADOS:

### GRATUITO:
1. 000webhost.com
2. infinityfree.net
3. freehostia.com

### PREMIUM:
1. Hostinger ($3/mes)
2. SiteGround ($4/mes)
3. Bluehost ($3/mes)

¿Quieres que te ayude con algún hosting específico?

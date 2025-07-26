# 🚀 GUÍA COMPLETA PARA SUBIR HOGARTOURS A LA WEB

## ✅ OPCIÓN 1: HOSTING GRATUITO (000WEBHOST)

### PASO 1: Crear cuenta
1. Ve a https://www.000webhost.com
2. Crea cuenta gratuita
3. Elige subdominio (ej: miturismo.000webhostapp.com)

### PASO 2: Preparar archivos
En tu carpeta hogartours, crea ZIP con:
```
📁 ARCHIVOS A INCLUIR:
├── src/ (completa)
├── templates/ (completa)
├── public/ (completa)
├── config/ (solo database_production.php renombrado a database.php)
├── uploads/ (crear carpeta vacía)
├── index.php
├── .htaccess
└── hogartours_production.sql
```

📁 ARCHIVOS A EXCLUIR:
- make_user_host.php
- verify_system.php
- test_*.php
- debug_*.php
- check_*.php
- export_database.php

### PASO 3: Subir archivos
1. Panel de control → File Manager
2. Ir a public_html/
3. Subir y extraer ZIP
4. Configurar permisos uploads/ (755)

### PASO 4: Crear base de datos
1. Panel → MySQL Database
2. Crear nueva base de datos
3. Crear usuario y asignar
4. Importar hogartours_production.sql

### PASO 5: Configurar database.php
```php
$host = 'localhost';
$db = 'id######_hogartours';  // Nombre que te dé 000webhost
$user = 'id######_usuario';   // Usuario que creaste
$pass = 'tu_contraseña';      // Tu contraseña
```

### PASO 6: Probar sitio
- Visita: https://tusubdominio.000webhostapp.com
- Login admin: admin@hogartours.com / admin123
- Login host: host@hogartours.com / host123

---

## 💰 OPCIÓN 2: HOSTING PREMIUM (HOSTINGER)

### VENTAJAS:
- ✅ Dominio propio (.com, .net, etc.)
- ✅ Mejor rendimiento
- ✅ Soporte 24/7
- ✅ SSL incluido
- ✅ Email profesional

### PRECIO: ~$3-5 USD/mes

### PASOS:
1. Comprar hosting en https://hostinger.com
2. Configurar dominio
3. Subir archivos vía FTP o File Manager
4. Configurar base de datos
5. Activar SSL

---

## 🔧 CONFIGURACIONES IMPORTANTES

### URLs en producción:
En templates/layouts/main.php cambiar:
```php
// De:
<base href="http://localhost/hogartours/">

// A:
<base href="https://tudominio.com/">
```

### Permisos de carpetas:
```
uploads/ → 755 o 777
uploads/packages/ → 755 o 777
uploads/packages/thumbs/ → 755 o 777
```

### Seguridad:
- Cambiar contraseñas por defecto
- Eliminar archivos de prueba
- Configurar SSL/HTTPS
- Backup regular de base de datos

---

## 📞 ¿NECESITAS AYUDA?

**Hostings recomendados:**
1. 🆓 000webhost.com (gratis)
2. 🆓 infinityfree.net (gratis)
3. 💰 hostinger.com ($3/mes)
4. 💰 siteground.com ($4/mes)

**¿Con cuál quieres que te ayude específicamente?**

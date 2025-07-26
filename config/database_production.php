<?php
// config/database_production.php - Configuración para servidor web

// CAMBIAR ESTOS DATOS POR LOS DE TU HOSTING
$host = 'localhost';              // Generalmente localhost
$db   = 'nombre_de_tu_base_datos'; // El nombre que te dé el hosting
$user = 'tu_usuario_db';          // Usuario de base de datos del hosting
$pass = 'tu_contraseña_db';       // Contraseña de base de datos del hosting

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configuraciones adicionales para producción
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // En producción, no mostrar detalles del error
    error_log("Error de conexión: " . $e->getMessage());
    die("Error de conexión a la base de datos. Contacte al administrador.");
}

// Configuraciones para producción
ini_set('display_errors', 0);     // No mostrar errores en pantalla
ini_set('log_errors', 1);         // Registrar errores en log
error_reporting(E_ALL);           // Reportar todos los errores pero sin mostrarlos

// URL base para producción (cambiar por tu dominio)
define('BASE_URL', 'https://tudominio.com');
?>

<!-- 
INSTRUCCIONES PARA USAR EN PRODUCCIÓN:

1. Cambiar el nombre de este archivo a database.php
2. Reemplazar el archivo config/database.php actual
3. Completar los datos de tu hosting:
   - $host (generalmente 'localhost')
   - $db (nombre de tu base de datos)
   - $user (usuario de base de datos)
   - $pass (contraseña de base de datos)
4. Cambiar BASE_URL por tu dominio real

DATOS TÍPICOS DE HOSTING:
- Host: localhost
- Base de datos: cpanel_nombredb
- Usuario: cpanel_usuario
- Contraseña: la que defines en el hosting
-->

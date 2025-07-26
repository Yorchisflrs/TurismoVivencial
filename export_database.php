<?php
// export_database.php - Exportar base de datos para producción

$host = 'localhost';
$db = 'hogartours';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Generando script SQL para producción...\n\n";
    
    // Exportar estructura y datos principales
    $sql_export = "-- Base de datos HogarTours para producción\n";
    $sql_export .= "-- Generado el " . date('Y-m-d H:i:s') . "\n\n";
    
    // Crear base de datos
    $sql_export .= "CREATE DATABASE IF NOT EXISTS hogartours_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
    $sql_export .= "USE hogartours_prod;\n\n";
    
    // Estructura de tablas
    $tables = ['users', 'hosts', 'packages', 'package_images'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW CREATE TABLE $table");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $sql_export .= "-- Estructura de tabla $table\n";
        $sql_export .= "DROP TABLE IF EXISTS $table;\n";
        $sql_export .= $row['Create Table'] . ";\n\n";
    }
    
    // Datos de usuarios (admin y host)
    $sql_export .= "-- Datos iniciales\n";
    $sql_export .= "INSERT INTO users (name, email, password, role, created_at) VALUES\n";
    $sql_export .= "('Administrador', 'admin@hogartours.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'ADMIN', NOW()),\n";
    $sql_export .= "('Host Demo', 'host@hogartours.com', '" . password_hash('host123', PASSWORD_DEFAULT) . "', 'HOST', NOW());\n\n";
    
    $sql_export .= "INSERT INTO hosts (user_id, status, created_at) VALUES\n";
    $sql_export .= "(2, 'APPROVED', NOW());\n\n";
    
    // Guardar archivo
    file_put_contents('hogartours_production.sql', $sql_export);
    
    echo "✅ Archivo SQL generado: hogartours_production.sql\n";
    echo "📁 Puedes subirlo a tu hosting para crear la base de datos\n\n";
    
    echo "INSTRUCCIONES:\n";
    echo "1. Descarga el archivo hogartours_production.sql\n";
    echo "2. Ve al panel de control de tu hosting (cPanel/phpMyAdmin)\n";
    echo "3. Crea una nueva base de datos\n";
    echo "4. Importa el archivo SQL\n";
    echo "5. Actualiza config/database.php con los nuevos datos\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<?php
// public/create-admin.php - Script temporal para crear usuario admin
require_once __DIR__ . '/../config/database.php';

$admin_email = 'admin@hogartours.com';
$admin_password = 'password';  // Esta es la contraseña correcta según el schema.sql
$admin_name = 'Admin HogarTours';

// Verificar si ya existe
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$admin_email]);

if ($stmt->fetch()) {
    echo "El usuario admin ya existe en la base de datos.<br>";
    echo "Email: $admin_email<br>";
    echo "Password: $admin_password<br>";
    echo "<br><strong>Usa estas credenciales para hacer login!</strong><br>";
} else {
    // Crear usuario admin
    $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
    
    if ($stmt->execute([$admin_name, $admin_email, $password_hash, 'ADMIN'])) {
        echo "Usuario admin creado exitosamente!<br>";
        echo "Email: $admin_email<br>";
        echo "Password: $admin_password<br>";
    } else {
        echo "Error al crear usuario admin.<br>";
    }
}

echo '<br><a href="/hogartours/login">Ir al login</a>';
?>

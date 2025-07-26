<?php
// public/debug-login.php - Script de depuración para login
session_start();
require_once __DIR__ . '/../config/database.php';

echo "<h2>Debug del Login</h2>";

// Probar conexión a BD
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM users');
    echo "✅ Conexión a BD exitosa<br>";
    echo "Total usuarios: " . $stmt->fetchColumn() . "<br><br>";
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    exit;
}

// Probar usuario admin
$admin_email = 'admin@hogartours.com';
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$admin_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "✅ Usuario admin encontrado:<br>";
    echo "ID: " . $user['id'] . "<br>";
    echo "Nombre: " . $user['name'] . "<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Role: " . $user['role'] . "<br>";
    
    // Probar password
    $test_password = 'password';
    if (password_verify($test_password, $user['password_hash'])) {
        echo "✅ Password 'password' es correcta<br>";
    } else {
        echo "❌ Password 'password' NO es correcta<br>";
        echo "Hash almacenado: " . $user['password_hash'] . "<br>";
    }
} else {
    echo "❌ Usuario admin NO encontrado<br>";
}

echo "<br><a href='/hogartours/login'>Ir al login</a>";
?>

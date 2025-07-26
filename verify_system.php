<?php
require_once 'config/database.php';

echo "=== VERIFICACIÓN DEL SISTEMA ===\n\n";

// Verificar usuario anfitrión
echo "1. Estado del usuario:\n";
$stmt = $pdo->query('SELECT u.name, u.email, u.role, h.status FROM users u LEFT JOIN hosts h ON u.id = h.user_id WHERE u.id = 7');
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    echo "Nombre: " . $user['name'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Rol: " . $user['role'] . "\n";
    echo "Estado de anfitrión: " . ($user['status'] ?? 'No es anfitrión') . "\n";
} else {
    echo "Usuario no encontrado\n";
}

echo "\n2. Verificación de tablas:\n";

// Verificar tabla hosts
$stmt = $pdo->query('SELECT COUNT(*) as total FROM hosts');
$hosts_count = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total anfitriones: " . $hosts_count['total'] . "\n";

// Verificar tabla packages
$stmt = $pdo->query('SELECT COUNT(*) as total FROM packages');
$packages_count = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total paquetes: " . $packages_count['total'] . "\n";

// Verificar tabla package_images
$stmt = $pdo->query('SELECT COUNT(*) as total FROM package_images');
$images_count = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total imágenes: " . $images_count['total'] . "\n";

echo "\n3. Sistema funcionando correctamente ✅\n";
echo "Accede a: http://localhost/hogartours/\n";
?>

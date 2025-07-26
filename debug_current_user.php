<?php
session_start();
require_once 'config/database.php';
require_once 'src/lib/helpers.php';

echo "<h2>🔍 DEBUG: Usuario Actual</h2>";

if (!isset($_SESSION['user_id'])) {
    echo "❌ No hay sesión activa<br>";
    echo "<a href='/hogartours/login'>Ir a Login</a>";
    exit;
}

echo "✅ Sesión activa<br>";
echo "- User ID: " . $_SESSION['user_id'] . "<br>";
echo "- User Name: " . ($_SESSION['user_name'] ?? 'No definido') . "<br>";
echo "- User Role: " . ($_SESSION['user_role'] ?? 'No definido') . "<br>";

global $pdo;

// Verificar si es anfitrión
$stmt = $pdo->prepare("SELECT h.*, u.name FROM hosts h JOIN users u ON h.user_id = u.id WHERE h.user_id = ? AND h.status = 'APPROVED'");
$stmt->execute([$_SESSION['user_id']]);
$host = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<br><h3>¿Es Anfitrión?</h3>";
if ($host) {
    echo "✅ SÍ ES ANFITRIÓN APROBADO<br>";
    echo "- Host ID: " . $host['id'] . "<br>";
    echo "- Nombre: " . $host['full_name'] . "<br>";
    echo "- Estado: " . $host['status'] . "<br>";
} else {
    echo "❌ NO ES ANFITRIÓN o NO ESTÁ APROBADO<br>";
}

// Test de función isHost()
echo "<br><h3>Test función isHost():</h3>";
$is_host_result = isHost();
echo $is_host_result ? "✅ isHost() retorna TRUE" : "❌ isHost() retorna FALSE";

echo "<br><br><a href='/hogartours/'>Volver al inicio</a>";
?>

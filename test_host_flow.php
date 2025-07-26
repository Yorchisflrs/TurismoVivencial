<?php
require_once 'config/database.php';
require_once 'src/lib/helpers.php';

// Simular sesión de un anfitrión aprobado
session_start();

// Buscar un anfitrión aprobado con user_id
$stmt = $pdo->query("SELECT h.*, u.name, u.email FROM hosts h 
                     JOIN users u ON h.user_id = u.id 
                     WHERE h.status = 'APPROVED' AND h.user_id IS NOT NULL 
                     LIMIT 1");
$host_data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($host_data) {
    // Configurar sesión como este usuario
    $_SESSION['user_id'] = $host_data['user_id'];
    $_SESSION['user_name'] = $host_data['name'];
    $_SESSION['user_email'] = $host_data['email'];
    
    echo "=== PRUEBA DE FLUJO DE ANFITRIÓN ===\n";
    echo "✅ Usuario logueado como: {$host_data['name']} (ID: {$host_data['user_id']})\n";
    echo "✅ Anfitrión ID: {$host_data['id']}\n";
    echo "✅ Estado del anfitrión: {$host_data['status']}\n";
    
    // Verificar función isHost()
    if (isHost()) {
        echo "✅ Función isHost() devuelve TRUE\n";
    } else {
        echo "❌ Función isHost() devuelve FALSE\n";
    }
    
    // Simular acceso al dashboard
    try {
        $stmt = $pdo->prepare('SELECT * FROM hosts WHERE user_id = ? AND status = "APPROVED"');
        $stmt->execute([$_SESSION['user_id']]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($host) {
            echo "✅ Dashboard accessible: Anfitrión encontrado\n";
            
            // Verificar paquetes
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM packages WHERE host_id = ?');
            $stmt->execute([$host['id']]);
            $package_count = $stmt->fetchColumn();
            echo "✅ Paquetes del anfitrión: $package_count\n";
            
        } else {
            echo "❌ Dashboard NO accessible: Anfitrión no encontrado\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error en dashboard: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ No se encontró ningún anfitrión aprobado con user_id\n";
    
    // Crear un anfitrión de prueba con user_id válido
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'TOURIST' LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $stmt = $pdo->prepare("INSERT INTO hosts (user_id, full_name, email, phone, business_name, description, status, created_at) 
                               VALUES (?, 'Usuario Prueba', 'test@example.com', '123456789', 'Negocio Prueba', 'Descripción', 'APPROVED', NOW())");
        $stmt->execute([$user['id']]);
        
        echo "✅ Anfitrión de prueba creado para user_id: {$user['id']}\n";
    }
}

echo "\n=== PRUEBA COMPLETA ===\n";
?>

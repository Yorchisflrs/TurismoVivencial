<?php
require_once 'config/database.php';

echo "=== CONFIGURACIÓN DEL SISTEMA DE ANFITRIÓN ===\n";

try {
    // 1. Verificar y arreglar relaciones de paquetes
    $stmt = $pdo->query("SELECT * FROM packages WHERE host_id NOT IN (SELECT id FROM hosts)");
    $orphan_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($orphan_packages)) {
        echo "❌ Encontrados " . count($orphan_packages) . " paquetes sin anfitrión válido\n";
        
        // Obtener primer anfitrión aprobado
        $stmt = $pdo->query("SELECT id FROM hosts WHERE status = 'APPROVED' LIMIT 1");
        $host = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($host) {
            foreach ($orphan_packages as $package) {
                $stmt = $pdo->prepare("UPDATE packages SET host_id = ? WHERE id = ?");
                $stmt->execute([$host['id'], $package['id']]);
                echo "✅ Paquete '{$package['title']}' asignado al anfitrión ID: {$host['id']}\n";
            }
        }
    } else {
        echo "✅ Todos los paquetes tienen anfitrión válido\n";
    }
    
    // 2. Verificar que hay un usuario-anfitrión para pruebas
    $stmt = $pdo->query("SELECT u.*, h.id as host_id, h.status as host_status 
                         FROM users u 
                         JOIN hosts h ON u.id = h.user_id 
                         WHERE h.status = 'APPROVED' 
                         LIMIT 1");
    $test_user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$test_user) {
        echo "❌ No hay usuario-anfitrión aprobado para pruebas\n";
        
        // Buscar un usuario normal
        $stmt = $pdo->query("SELECT * FROM users WHERE role = 'TOURIST' LIMIT 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Crear solicitud de anfitrión aprobada
            $stmt = $pdo->prepare("INSERT INTO hosts (user_id, full_name, email, phone, business_name, description, status, created_at) 
                                   VALUES (?, ?, ?, '123456789', 'Mi Negocio Turístico', 'Ofrezco experiencias auténticas', 'APPROVED', NOW())
                                   ON DUPLICATE KEY UPDATE status = 'APPROVED'");
            $stmt->execute([$user['id'], $user['name'], $user['email']]);
            
            echo "✅ Usuario '{$user['name']}' ahora es anfitrión aprobado\n";
            echo "   - Email: {$user['email']}\n";
            echo "   - Puede iniciar sesión y crear paquetes\n";
        }
    } else {
        echo "✅ Usuario-anfitrión de prueba disponible:\n";
        echo "   - Nombre: {$test_user['name']}\n";
        echo "   - Email: {$test_user['email']}\n";
        echo "   - Host ID: {$test_user['host_id']}\n";
        echo "   - Estado: {$test_user['host_status']}\n";
    }
    
    // 3. Verificar estado final
    $stmt = $pdo->query("SELECT COUNT(*) FROM hosts WHERE status = 'APPROVED'");
    $approved_hosts = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM packages WHERE status = 'APPROVED'");
    $approved_packages = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT p.title, h.full_name as host_name 
                         FROM packages p 
                         JOIN hosts h ON p.host_id = h.id 
                         WHERE p.status = 'APPROVED'");
    $package_host_relations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n=== ESTADO FINAL ===\n";
    echo "✅ Anfitriones aprobados: $approved_hosts\n";
    echo "✅ Paquetes aprobados: $approved_packages\n";
    echo "✅ Relaciones paquete-anfitrión:\n";
    
    foreach ($package_host_relations as $relation) {
        echo "   - '{$relation['title']}' por {$relation['host_name']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== CONFIGURACIÓN COMPLETA ===\n";
?>

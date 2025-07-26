<?php
require_once 'config/database.php';

echo "=== VERIFICACIÓN DEL SISTEMA DE ANFITRIÓN ===\n";

// Verificar anfitriones aprobados
try {
    $stmt = $pdo->query("SELECT h.*, u.name as user_name, u.email as user_email 
                         FROM hosts h 
                         LEFT JOIN users u ON h.user_id = u.id 
                         WHERE h.status = 'APPROVED'");
    $hosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Anfitriones aprobados: " . count($hosts) . "\n";
    
    foreach ($hosts as $host) {
        echo "  - ID: {$host['id']}, Usuario: " . ($host['user_name'] ?: 'Sin usuario') . 
             ", Email: " . ($host['user_email'] ?: $host['email']) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error en tabla hosts: " . $e->getMessage() . "\n";
}

// Verificar paquetes
try {
    $stmt = $pdo->query("SELECT p.*, h.full_name as host_name 
                         FROM packages p 
                         LEFT JOIN hosts h ON p.host_id = h.id 
                         ORDER BY p.created_at DESC");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Paquetes totales: " . count($packages) . "\n";
    
    $approved = array_filter($packages, function($p) { return $p['status'] === 'APPROVED'; });
    echo "✅ Paquetes aprobados: " . count($approved) . "\n";
    
    foreach ($packages as $package) {
        echo "  - ID: {$package['id']}, Título: {$package['title']}, Estado: {$package['status']}, Host: " . 
             ($package['host_name'] ?: 'N/A') . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error en tabla packages: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICACIÓN COMPLETA ===\n";
?>

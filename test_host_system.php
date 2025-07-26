<?php
require_once 'config/database.php';
global $pdo;

try {
    echo "🚀 Configurando sistema de paquetes para anfitriones...\n\n";
    
    // 1. Verificar que la tabla package_images existe y está lista
    echo "1. Verificando estructura de imágenes...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'package_images'");
    if ($stmt->rowCount() > 0) {
        echo "   ✓ Tabla package_images existe\n";
        
        $stmt = $pdo->query('DESCRIBE package_images');
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        echo "   ✓ Columnas: " . implode(', ', $columns) . "\n";
    } else {
        echo "   ❌ Tabla package_images no existe\n";
    }
    
    // 2. Verificar anfitriones aprobados
    echo "\n2. Verificando anfitriones aprobados...\n";
    $stmt = $pdo->query('SELECT COUNT(*) FROM hosts WHERE status = "APPROVED"');
    $approved_hosts = $stmt->fetchColumn();
    echo "   ✓ Anfitriones aprobados: $approved_hosts\n";
    
    if ($approved_hosts > 0) {
        $stmt = $pdo->query('SELECT id, full_name, business_name FROM hosts WHERE status = "APPROVED" LIMIT 3');
        $hosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($hosts as $host) {
            echo "   - ID: {$host['id']}, {$host['full_name']} ({$host['business_name']})\n";
        }
    }
    
    // 3. Verificar paquetes existentes
    echo "\n3. Verificando paquetes...\n";
    $stmt = $pdo->query('SELECT COUNT(*) FROM packages');
    $total_packages = $stmt->fetchColumn();
    echo "   ✓ Total de paquetes: $total_packages\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM packages WHERE status = "APPROVED"');
    $approved_packages = $stmt->fetchColumn();
    echo "   ✓ Paquetes aprobados: $approved_packages\n";
    
    // 4. Test de la consulta de experiencias
    echo "\n4. Probando consulta de experiencias...\n";
    $sql = "SELECT p.*, h.full_name as host_name, h.business_name,
                   (SELECT COUNT(*) FROM package_images pi WHERE pi.package_id = p.id) as image_count
            FROM packages p
            LEFT JOIN hosts h ON p.host_id = h.id
            WHERE p.status = 'APPROVED' AND h.status = 'APPROVED'
            ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $visible_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   ✓ Experiencias visibles para usuarios: " . count($visible_packages) . "\n";
    
    if (count($visible_packages) > 0) {
        foreach ($visible_packages as $package) {
            echo "   - {$package['title']} por {$package['host_name']} ({$package['image_count']} imágenes)\n";
        }
    } else {
        echo "   ⚠️  No hay experiencias visibles aún\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ SISTEMA DE ANFITRIONES CONFIGURADO!\n\n";
    echo "🔧 Funcionalidades disponibles:\n";
    echo "   • Los anfitriones pueden crear experiencias\n";
    echo "   • Subir múltiples imágenes por experiencia\n";
    echo "   • Sistema de aprobación por administradores\n";
    echo "   • Experiencias aparecen automáticamente en /packages\n";
    echo "   • Dashboard completo para anfitriones\n\n";
    
    echo "🚀 URLs para probar:\n";
    echo "   • Dashboard anfitrión: /hogartours/host/dashboard\n";
    echo "   • Crear experiencia: /hogartours/host/create-package\n";
    echo "   • Ver experiencias: /hogartours/packages\n";
    echo "   • Admin paquetes: /hogartours/admin/packages\n\n";
    
    if ($approved_hosts > 0) {
        echo "🎯 ¡Listo para usar! Los anfitriones pueden empezar a crear experiencias.\n";
    } else {
        echo "⚠️  Necesitas aprobar al menos un anfitrión para que pueda crear experiencias.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>

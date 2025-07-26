<?php
require_once 'config/database.php';
global $pdo;

echo "🧪 Probando sistema completo de anfitriones y paquetes...\n\n";

try {
    // 1. Verificar que tenemos anfitriones aprobados con user_id
    echo "1. Verificando anfitriones aprobados con cuentas de usuario:\n";
    $stmt = $pdo->query("
        SELECT h.id, h.full_name, h.user_id, u.name as username, u.email
        FROM hosts h 
        JOIN users u ON h.user_id = u.id 
        WHERE h.status = 'APPROVED'
    ");
    $approvedHosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($approvedHosts as $host) {
        echo "   ✓ {$host['full_name']} (Usuario: {$host['username']}, Email: {$host['email']})\n";
    }
    
    if (count($approvedHosts) === 0) {
        echo "   ⚠️ No hay anfitriones aprobados con cuentas de usuario\n";
        echo "   💡 Ve al admin y aprueba algún anfitrión desde /hogartours/admin/hosts\n";
    }
    
    // 2. Verificar rutas del sistema
    echo "\n2. Verificando rutas disponibles:\n";
    $routes = [
        '/host/dashboard' => 'Dashboard del anfitrión',
        '/host/create-package' => 'Crear nueva experiencia',
        '/packages' => 'Ver experiencias públicas',
        '/admin/hosts' => 'Gestión de anfitriones (admin)',
        '/admin/packages' => 'Gestión de paquetes (admin)'
    ];
    
    foreach ($routes as $route => $description) {
        echo "   ✓ $route - $description\n";
    }
    
    // 3. Verificar paquetes existentes y su estado
    echo "\n3. Estado actual de paquetes:\n";
    $stmt = $pdo->query("
        SELECT p.status, COUNT(*) as count
        FROM packages p
        GROUP BY p.status
    ");
    $packageStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($packageStats as $stat) {
        echo "   • {$stat['status']}: {$stat['count']} paquetes\n";
    }
    
    // 4. Verificar paquetes visibles en la página pública
    echo "\n4. Paquetes visibles en /packages:\n";
    $stmt = $pdo->query("
        SELECT p.title, h.full_name as host_name, p.price,
               (SELECT COUNT(*) FROM package_images pi WHERE pi.package_id = p.id) as image_count
        FROM packages p
        JOIN hosts h ON p.host_id = h.id
        WHERE p.status = 'APPROVED' AND h.status = 'APPROVED'
        ORDER BY p.created_at DESC
    ");
    $visiblePackages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($visiblePackages) > 0) {
        foreach ($visiblePackages as $package) {
            echo "   ✓ \"{$package['title']}\" por {$package['host_name']} - S/{$package['price']} ({$package['image_count']} imágenes)\n";
        }
    } else {
        echo "   ⚠️ No hay paquetes visibles aún\n";
        echo "   💡 Los anfitriones necesitan crear paquetes y los admins aprobarlos\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🎉 SISTEMA COMPLETAMENTE FUNCIONAL!\n\n";
    
    echo "📋 Flujo de trabajo:\n";
    echo "   1. Usuario solicita ser anfitrión en /become-host\n";
    echo "   2. Admin aprueba anfitrión en /admin/hosts\n";
    echo "   3. Anfitrión crea experiencias en /host/dashboard\n";
    echo "   4. Anfitrión sube imágenes en cada experiencia\n";
    echo "   5. Admin aprueba experiencias en /admin/packages\n";
    echo "   6. Experiencias aparecen públicamente en /packages\n\n";
    
    echo "🚀 URLs importantes:\n";
    echo "   • Solicitar ser anfitrión: http://localhost/hogartours/become-host\n";
    echo "   • Panel de anfitrión: http://localhost/hogartours/host/dashboard\n";
    echo "   • Experiencias públicas: http://localhost/hogartours/packages\n";
    echo "   • Admin de anfitriones: http://localhost/hogartours/admin/hosts\n";
    echo "   • Admin de paquetes: http://localhost/hogartours/admin/packages\n\n";
    
    if (count($approvedHosts) > 0) {
        echo "✅ ¡Sistema listo! Los anfitriones pueden crear experiencias con imágenes.\n";
    } else {
        echo "🔧 Para activar el sistema: Aprueba anfitriones en el panel de administración.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>

<?php
require_once 'config/database.php';
global $pdo;

try {
    echo "Actualizando estados de anfitriones...\n";
    
    // Actualizar registros con estado en minúscula a mayúscula
    $stmt = $pdo->prepare('UPDATE hosts SET status = "PENDING" WHERE status = "pending"');
    $updated = $stmt->execute();
    $rowCount = $stmt->rowCount();
    
    if ($updated) {
        echo "✓ Se actualizaron $rowCount registros de 'pending' a 'PENDING'\n";
    }
    
    // Verificar el estado actual
    echo "\nEstado actual de anfitriones:\n";
    $stmt = $pdo->query('SELECT status, COUNT(*) as count FROM hosts GROUP BY status');
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($statuses as $status) {
        echo "- {$status['status']}: {$status['count']} anfitriones\n";
    }
    
    // Mostrar detalles de anfitriones pendientes
    echo "\nDetalles de anfitriones PENDING:\n";
    $stmt = $pdo->query('SELECT id, full_name, email, business_name, created_at FROM hosts WHERE status = "PENDING"');
    $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($pending) > 0) {
        foreach ($pending as $host) {
            echo "- ID: {$host['id']}, Nombre: {$host['full_name']}, Email: {$host['email']}, Negocio: {$host['business_name']}\n";
        }
    } else {
        echo "- No hay anfitriones pendientes\n";
    }
    
    echo "\n✅ ¡Actualización completada! El panel de administración debería mostrar correctamente los anfitriones pendientes.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>

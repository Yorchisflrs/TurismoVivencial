<?php
require_once 'config/database.php';
require_once 'src/models/Host.php';

echo "Verificando sistema de anfitriones...\n\n";

try {
    // Verificar estadísticas del dashboard
    echo "=== ESTADÍSTICAS DEL DASHBOARD ===\n";
    $pending_count = $pdo->query('SELECT COUNT(*) FROM hosts WHERE status = "PENDING"')->fetchColumn();
    echo "Anfitriones pendientes (dashboard): $pending_count\n";
    
    // Verificar método getPending() del modelo
    echo "\n=== MÉTODO getPending() ===\n";
    $pending_hosts = Host::getPending();
    echo "Anfitriones pendientes (modelo): " . count($pending_hosts) . "\n";
    
    if (count($pending_hosts) > 0) {
        echo "\nDetalles de anfitriones pendientes:\n";
        foreach ($pending_hosts as $host) {
            echo "- ID: {$host['id']}\n";
            echo "  Nombre: {$host['name']}\n";
            echo "  Email: {$host['email']}\n";
            echo "  Negocio: {$host['business_name']}\n";
            echo "  Estado: {$host['status']}\n";
            echo "  Fecha: {$host['created_at']}\n\n";
        }
    } else {
        echo "No se encontraron anfitriones pendientes.\n";
    }
    
    echo "=== VERIFICACIÓN COMPLETA ===\n";
    if ($pending_count > 0 && count($pending_hosts) > 0) {
        echo "✅ TODO FUNCIONA CORRECTAMENTE!\n";
        echo "   - El dashboard mostrará: $pending_count anfitriones pendientes\n";
        echo "   - La página de gestión mostrará: " . count($pending_hosts) . " anfitriones\n";
        echo "   - Los números coinciden: " . ($pending_count == count($pending_hosts) ? "SÍ" : "NO") . "\n";
    } else {
        echo "⚠️  No hay anfitriones pendientes para mostrar.\n";
        echo "   Crea una nueva solicitud desde el formulario para probar.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>

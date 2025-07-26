<?php
// Convertir usuario en anfitrión aprobado automáticamente
require_once 'config/database.php';

echo "<h2>🔧 CONVIRTIENDO USUARIO EN ANFITRIÓN</h2>";

try {
    global $pdo;
    
    // 1. Obtener información del usuario actual
    $user_id = 8; // YULISSA PERALTA
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "❌ Usuario no encontrado<br>";
        exit;
    }
    
    echo "👤 Usuario encontrado: {$user['name']} ({$user['email']})<br><br>";
    
    // 2. Verificar si ya es anfitrión
    $stmt = $pdo->prepare("SELECT * FROM hosts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existing_host = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_host) {
        echo "📋 Ya existe registro de anfitrión con estado: {$existing_host['status']}<br>";
        
        // Si existe pero no está aprobado, aprobar
        if ($existing_host['status'] !== 'APPROVED') {
            $stmt = $pdo->prepare("UPDATE hosts SET status = 'APPROVED' WHERE user_id = ?");
            $stmt->execute([$user_id]);
            echo "✅ Anfitrión APROBADO exitosamente<br>";
        } else {
            echo "✅ Anfitrión ya estaba APROBADO<br>";
        }
    } else {
        echo "📝 Creando nuevo registro de anfitrión...<br>";
        
        // Crear nuevo anfitrión
        $stmt = $pdo->prepare("
            INSERT INTO hosts (user_id, bio, status, created_at) 
            VALUES (?, ?, 'APPROVED', NOW())
        ");
        
        $stmt->execute([
            $user_id,
            'Anfitrión verificado especializado en experiencias auténticas en el altiplano peruano'
        ]);
        
        echo "✅ Anfitrión creado y APROBADO exitosamente<br>";
    }
    
    // 3. Verificar resultado final
    echo "<br><h3>✅ VERIFICACIÓN FINAL:</h3>";
    
    $stmt = $pdo->prepare("
        SELECT h.*, u.name as user_name 
        FROM hosts h 
        JOIN users u ON h.user_id = u.id 
        WHERE h.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $final_host = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($final_host && $final_host['status'] === 'APPROVED') {
        echo "🎉 <strong>¡ÉXITO!</strong> {$final_host['user_name']} ahora ES ANFITRIÓN APROBADO<br><br>";
        
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
        echo "<strong>✅ Ahora puedes:</strong><br>";
        echo "1. Refrescar la página principal<br>";
        echo "2. Ver 'Panel de Anfitrión' en tu menú de usuario<br>";
        echo "3. Crear experiencias con fotos<br>";
        echo "4. Ya NO verás 'Ser Anfitrión' en el menú<br>";
        echo "</div><br>";
        
        echo "<a href='/' class='btn btn-success' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Ir al Inicio</a>";
        
    } else {
        echo "❌ Error en la verificación final<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

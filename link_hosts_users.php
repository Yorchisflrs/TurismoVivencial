<?php
require_once 'config/database.php';
global $pdo;

echo "Vinculando anfitriones con usuarios...\n\n";

try {
    // Buscar anfitriones que no tienen user_id pero tienen email que coincide con un usuario
    $stmt = $pdo->query("
        SELECT h.id as host_id, h.email as host_email, h.full_name, 
               u.id as user_id, u.name as user_name, u.email as user_email
        FROM hosts h
        LEFT JOIN users u ON h.email = u.email
        WHERE h.user_id IS NULL AND u.id IS NOT NULL
    ");
    
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($matches) > 0) {
        echo "Encontrados " . count($matches) . " anfitriones para vincular:\n";
        
        foreach ($matches as $match) {
            echo "- Vinculando anfitrión {$match['full_name']} con usuario {$match['user_name']}\n";
            
            $updateStmt = $pdo->prepare('UPDATE hosts SET user_id = ? WHERE id = ?');
            $updateStmt->execute([$match['user_id'], $match['host_id']]);
        }
        
        echo "\n✅ Vinculación completada!\n";
    } else {
        echo "No se encontraron anfitriones para vincular.\n";
    }
    
    // Crear usuarios automáticamente para anfitriones que no tienen cuenta
    echo "\nCreando usuarios para anfitriones sin cuenta...\n";
    
    $stmt = $pdo->query("
        SELECT id, full_name, email, phone
        FROM hosts 
        WHERE user_id IS NULL AND status = 'APPROVED'
    ");
    
    $orphanHosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($orphanHosts) > 0) {
        foreach ($orphanHosts as $host) {
            // Crear usuario con password temporal
            $tempPassword = 'hogar' . rand(1000, 9999);
            $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
            
            $userStmt = $pdo->prepare('
                INSERT INTO users (name, email, password_hash, role, created_at) 
                VALUES (?, ?, ?, "HOST", NOW())
            ');
            
            if ($userStmt->execute([$host['full_name'], $host['email'], $hashedPassword])) {
                $userId = $pdo->lastInsertId();
                
                // Vincular anfitrión con nuevo usuario
                $linkStmt = $pdo->prepare('UPDATE hosts SET user_id = ? WHERE id = ?');
                $linkStmt->execute([$userId, $host['id']]);
                
                echo "✓ Creado usuario para {$host['full_name']} (Password temporal: $tempPassword)\n";
            }
        }
    } else {
        echo "Todos los anfitriones aprobados ya tienen cuentas de usuario.\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✅ PROCESO COMPLETADO!\n";
    echo "Los anfitriones ahora pueden acceder a su dashboard.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>

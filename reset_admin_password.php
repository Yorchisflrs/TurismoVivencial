<?php
// reset_admin_password.php - Script para resetear contraseña del admin
require_once 'config/database.php';

$admin_email = 'admin@hogartours.com';
$new_password = 'admin123';

try {
    // Verificar si el admin existe
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = "ADMIN"');
    $stmt->execute([$admin_email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        // Actualizar la contraseña
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
        $stmt->execute([$password_hash, $admin_email]);
        
        echo "<h2>✅ Contraseña del Admin Actualizada</h2>";
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<p><strong>Credenciales de Admin:</strong></p>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> <code>$admin_email</code></li>";
        echo "<li><strong>Password:</strong> <code>$new_password</code></li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<p>🔐 <a href='/hogartours/login' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir a Login</a></p>";
        
    } else {
        echo "<h2>❌ Admin no encontrado</h2>";
        echo "<p>No se encontró usuario admin con email: $admin_email</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p>Error al actualizar contraseña: " . $e->getMessage() . "</p>";
}
?>

<style>
body { 
    font-family: Arial, sans-serif; 
    max-width: 600px; 
    margin: 50px auto; 
    padding: 20px; 
    background: #f8f9fa;
}
h2 { color: #333; }
code { 
    background: #f1f1f1; 
    padding: 2px 5px; 
    border-radius: 3px;
    font-family: monospace;
}
</style>

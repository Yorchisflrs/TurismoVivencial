<?php
// check_admin.php - Script para verificar y crear usuario admin si es necesario
require_once 'config/database.php';
session_start();

echo "<h2>Estado de Autenticación</h2>";
echo "<p><strong>Sesión actual:</strong></p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Verificar Usuario Admin</h2>";

try {
    // Verificar si existe usuario admin
    $stmt = $pdo->prepare('SELECT * FROM users WHERE role = "ADMIN"');
    $stmt->execute();
    $admin_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($admin_users)) {
        echo "<p style='color: red;'>❌ No hay usuarios admin en la base de datos.</p>";
        
        // Crear usuario admin
        $admin_email = 'admin@hogartours.pe';
        $admin_password = 'admin123';
        $admin_name = 'Administrador HogarTours';
        $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$admin_name, $admin_email, $password_hash, 'ADMIN']);
        
        echo "<p style='color: green;'>✅ Usuario admin creado:</p>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> $admin_email</li>";
        echo "<li><strong>Password:</strong> $admin_password</li>";
        echo "<li><strong>Rol:</strong> ADMIN</li>";
        echo "</ul>";
        
    } else {
        echo "<p style='color: green;'>✅ Usuarios admin encontrados:</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Creado</th></tr>";
        
        foreach ($admin_users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>Acciones</h2>";
    
    if (!isset($_SESSION['user_id'])) {
        echo "<p>🔐 <a href='/hogartours/login'>Ir a Login</a></p>";
    } else {
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') {
            echo "<p>✅ Ya estás autenticado como admin</p>";
            echo "<p>🎯 <a href='/hogartours/admin/dashboard'>Ir al Dashboard Admin</a></p>";
        } else {
            echo "<p>⚠️ Estás autenticado pero no como admin</p>";
            echo "<p>🔄 <a href='/hogartours/logout'>Cerrar Sesión</a> y hacer login como admin</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>

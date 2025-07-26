<?php
require_once 'config/database.php';

echo "=== CONFIGURACIÓN DE USUARIO ADMINISTRADOR ===\n";

// Verificar si ya existe un admin
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'ADMIN'");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    echo "✅ Ya existe un administrador:\n";
    echo "   - Nombre: {$admin['name']}\n";
    echo "   - Email: {$admin['email']}\n";
    echo "   - ID: {$admin['id']}\n";
} else {
    echo "❌ No existe ningún administrador\n";
    echo "✅ Creando usuario administrador...\n";
    
    // Crear admin por defecto
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, created_at) 
                               VALUES (?, ?, ?, 'ADMIN', NOW())");
        $stmt->execute(['Administrador', 'admin@hogartours.com', $admin_password]);
        
        echo "✅ Usuario administrador creado:\n";
        echo "   - Email: admin@hogartours.com\n";
        echo "   - Contraseña: admin123\n";
        echo "   - Nombre: Administrador\n";
        
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            // El email ya existe, actualizar rol
            $stmt = $pdo->prepare("UPDATE users SET role = 'ADMIN', password_hash = ? WHERE email = ?");
            $stmt->execute([$admin_password, 'admin@hogartours.com']);
            echo "✅ Usuario existente actualizado a administrador\n";
        } else {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
    }
}

// Verificar usuarios y sus roles
echo "\n=== USUARIOS EN EL SISTEMA ===\n";
$stmt = $pdo->query("SELECT id, name, email, role FROM users ORDER BY role DESC, name");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    $role_icon = $user['role'] === 'ADMIN' ? '🔑' : '👤';
    echo "$role_icon ID: {$user['id']} - {$user['name']} ({$user['email']}) - {$user['role']}\n";
}

echo "\n=== CONFIGURACIÓN COMPLETA ===\n";
echo "Ahora puedes iniciar sesión como administrador con:\n";
echo "📧 Email: admin@hogartours.com\n";
echo "🔒 Contraseña: admin123\n";

?>

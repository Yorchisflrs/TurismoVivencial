<?php
// public/create-test-data.php - Script para crear datos de prueba
require_once __DIR__ . '/../config/database.php';

echo "<h2>Creando datos de prueba para HogarTours</h2>";

try {
    // 1. Crear algunos usuarios turistas
    $usuarios = [
        ['María García', 'maria@example.com', 'TOURIST'],
        ['Juan Pérez', 'juan@example.com', 'HOST'], // Este será anfitrión
        ['Ana López', 'ana@example.com', 'HOST'],   // Este será anfitrión
    ];
    
    echo "<h3>Creando usuarios...</h3>";
    foreach ($usuarios as $user) {
        $password_hash = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
        
        try {
            $stmt->execute([$user[0], $user[1], $password_hash, $user[2]]);
            echo "✅ Usuario creado: {$user[0]} ({$user[1]})<br>";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "⚠️ Usuario ya existe: {$user[0]} ({$user[1]})<br>";
            }
        }
    }
    
    // 2. Obtener IDs de usuarios HOST
    $stmt = $pdo->query('SELECT id, name FROM users WHERE role = "HOST"');
    $hosts_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Creando anfitriones...</h3>";
    foreach ($hosts_users as $host_user) {
        // Crear entrada en tabla hosts
        $bios = [
            'Familia campesina con 20 años de experiencia en turismo rural. Ofrecemos experiencias auténticas.',
            'Comunidad indígena especializada en artesanías tradicionales y gastronomía ancestral.',
        ];
        $bio = $bios[array_rand($bios)];
        
        $stmt = $pdo->prepare('INSERT INTO hosts (user_id, bio, status) VALUES (?, ?, "PENDING")');
        
        try {
            $stmt->execute([$host_user['id'], $bio]);
            $host_id = $pdo->lastInsertId();
            echo "✅ Anfitrión creado: {$host_user['name']} (ID: $host_id)<br>";
            
            // 3. Crear paquetes para este anfitrión
            $paquetes = [
                [
                    'Experiencia de Vida Rural en Puno',
                    'Puno, Perú',
                    'Turismo Rural',
                    150.00,
                    6,
                    'Vive como un lugareño por 3 días. Incluye alojamiento en casa familiar, comidas tradicionales, y participación en actividades agrícolas.'
                ],
                [
                    'Aventura en el Titicaca',
                    'Islas Flotantes, Titicaca',
                    'Aventura',
                    200.00,
                    4,
                    'Navegación por el lago Titicaca, visita a islas flotantes de los Uros, pesca tradicional y noche bajo las estrellas.'
                ],
            ];
            
            $paquete = $paquetes[array_rand($paquetes)];
            $stmt = $pdo->prepare('
                INSERT INTO packages (host_id, title, location, category, price, capacity, description, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, "PENDING")
            ');
            $stmt->execute([$host_id, ...$paquete]);
            echo "✅ Paquete creado: {$paquete[0]}<br>";
            
        } catch (PDOException $e) {
            if ($e->errorInfo[1] != 1062) {
                echo "❌ Error: {$host_user['name']}: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    echo "<br><strong>¡Datos de prueba creados!</strong><br>";
    echo "<a href='/hogartours/admin/dashboard'>Ir al Dashboard Admin</a><br>";
    echo "<a href='/hogartours/admin/hosts'>Ver Anfitriones Pendientes</a><br>";
    echo "<a href='/hogartours/admin/packages'>Ver Paquetes Pendientes</a>";
    
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage();
}
?>

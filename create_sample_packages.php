<?php
// create_sample_packages.php - Crear paquetes de ejemplo
require_once 'config/database.php';

try {
    // Verificar si ya hay paquetes
    $count = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
    
    if ($count > 0) {
        echo "✅ Ya existen $count paquetes en la base de datos.<br>";
    } else {
        echo "📦 Creando paquetes de ejemplo...<br><br>";
        
        $sample_packages = [
            [
                'title' => 'Experiencia Textil en Taquile',
                'description' => 'Aprende las técnicas ancestrales de tejido con una familia tradicional de la Isla Taquile. Participa en todo el proceso desde la preparación de la lana hasta el teñido natural con plantas locales. Una experiencia única que te conectará con 500 años de tradición textil.',
                'price' => 180,
                'duration' => 2,
                'max_participants' => 6,
                'location' => 'Isla Taquile, Puno',
                'category' => 'textiles',
                'status' => 'approved'
            ],
            [
                'title' => 'Cocina Tradicional del Altiplano',
                'description' => 'Descubre los sabores únicos del altiplano peruano. Cocina platos tradicionales como la trucha del Titicaca, quinua orgánica y papas nativas en un ambiente familiar auténtico. Incluye visita al mercado local.',
                'price' => 120,
                'duration' => 1,
                'max_participants' => 8,
                'location' => 'Llachón, Puno',
                'category' => 'gastronomia',
                'status' => 'approved'
            ],
            [
                'title' => 'Pesca Tradicional en Totora',
                'description' => 'Navega en las tradicionales balsas de totora por el lago Titicaca y aprende técnicas de pesca ancestrales. Incluye preparación del pescado recién capturado y almuerzo a orillas del lago.',
                'price' => 200,
                'duration' => 1,
                'max_participants' => 4,
                'location' => 'Capachica, Puno',
                'category' => 'pesca',
                'status' => 'pending'
            ],
            [
                'title' => 'Agricultura en Terrazas Ancestrales',
                'description' => 'Participa en las actividades agrícolas tradicionales del altiplano. Siembra y cosecha papas nativas, quinua y otros cultivos ancestrales en terrazas milenarias. Incluye almuerzo campestre.',
                'price' => 150,
                'duration' => 3,
                'max_participants' => 10,
                'location' => 'Chucuito, Puno',
                'category' => 'agricultura',
                'status' => 'pending'
            ],
            [
                'title' => 'Ceremonia de la Pachamama',
                'description' => 'Participa en una auténtica ceremonia andina de agradecimiento a la Pachamama. Aprende sobre la cosmovisión andina y las tradiciones espirituales del altiplano. Experiencia profundamente transformadora.',
                'price' => 80,
                'duration' => 1,
                'max_participants' => 15,
                'location' => 'Isla Amantaní, Puno',
                'category' => 'ceremonias',
                'status' => 'approved'
            ]
        ];
        
        $sql = "INSERT INTO packages (title, description, price, duration, max_participants, location, category, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        
        foreach ($sample_packages as $package) {
            $stmt->execute([
                $package['title'],
                $package['description'], 
                $package['price'],
                $package['duration'],
                $package['max_participants'],
                $package['location'],
                $package['category'],
                $package['status']
            ]);
            
            echo "✅ Creado: {$package['title']} ({$package['status']})<br>";
        }
        
        echo "<br>🎉 Todos los paquetes de ejemplo han sido creados exitosamente!<br>";
    }
    
    // Mostrar estadísticas actuales
    $stats = [
        'total' => $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn(),
        'approved' => $pdo->query("SELECT COUNT(*) FROM packages WHERE status = 'approved'")->fetchColumn(),
        'pending' => $pdo->query("SELECT COUNT(*) FROM packages WHERE status = 'pending'")->fetchColumn(),
        'rejected' => $pdo->query("SELECT COUNT(*) FROM packages WHERE status = 'rejected'")->fetchColumn()
    ];
    
    echo "<br><h3>📊 Estadísticas Actuales:</h3>";
    echo "<ul>";
    echo "<li><strong>Total de paquetes:</strong> {$stats['total']}</li>";
    echo "<li><strong>Aprobados:</strong> {$stats['approved']}</li>";
    echo "<li><strong>Pendientes:</strong> {$stats['pending']}</li>";
    echo "<li><strong>Rechazados:</strong> {$stats['rejected']}</li>";
    echo "</ul>";
    
    echo "<br><h3>🔗 Enlaces útiles:</h3>";
    echo "<ul>";
    echo "<li><a href='/hogartours/admin/dashboard'>Dashboard Admin</a></li>";
    echo "<li><a href='/hogartours/admin/all-packages'>Ver Todos los Paquetes</a></li>";
    echo "<li><a href='/hogartours/admin/packages'>Paquetes Pendientes</a></li>";
    echo "<li><a href='/hogartours/packages'>Vista Pública de Paquetes</a></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

<style>
body { 
    font-family: Arial, sans-serif; 
    max-width: 800px; 
    margin: 20px auto; 
    padding: 20px; 
    line-height: 1.6;
}
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>

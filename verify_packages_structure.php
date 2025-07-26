<?php
// verify_packages_structure.php - Script para verificar estructura de packages
require_once 'config/database.php';

echo "<h2>Estructura de la tabla packages:</h2>";

try {
    $stmt = $pdo->query("DESCRIBE packages");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Mostrar algunos datos de ejemplo
    echo "<h2>Datos de ejemplo (si existen):</h2>";
    $stmt = $pdo->query("SELECT * FROM packages LIMIT 3");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($packages) {
        echo "<pre>";
        print_r($packages);
        echo "</pre>";
    } else {
        echo "<p>No hay paquetes en la base de datos.</p>";
        
        // Crear un paquete de ejemplo
        echo "<h3>Creando paquete de ejemplo...</h3>";
        $stmt = $pdo->prepare("INSERT INTO packages (title, location, category, price, max_participants, duration, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'Experiencia Textil Ancestral',
            'Isla Taquile',
            'textiles',
            150,
            6,
            2,
            'Aprende las técnicas milenarias de tejido con una familia tradicional de Taquile.',
            'pending'
        ]);
        
        echo "<p>✅ Paquete de ejemplo creado.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
th { background-color: #f2f2f2; }
</style>

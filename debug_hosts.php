<?php
require_once 'config/database.php';

// Mostrar estructura de la tabla hosts
try {
    $stmt = $pdo->query("DESCRIBE hosts");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Estructura de la tabla hosts:</h2>";
    echo "<table border='1'>";
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
    
    // También mostrar algunos datos de ejemplo
    $stmt = $pdo->query("SELECT * FROM hosts LIMIT 3");
    $hosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($hosts) {
        echo "<h2>Datos de ejemplo:</h2>";
        echo "<pre>";
        print_r($hosts);
        echo "</pre>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

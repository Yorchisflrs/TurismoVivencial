<?php
require_once 'config/database.php';
global $pdo;

try {
    echo "Verificando estructura de paquetes...\n\n";
    
    // Verificar si la tabla packages existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'packages'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "✓ Tabla packages existe\n";
        
        // Mostrar estructura
        echo "\nEstructura actual:\n";
        $stmt = $pdo->query('DESCRIBE packages');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
        
        // Mostrar paquetes existentes
        echo "\nPaquetes existentes:\n";
        $stmt = $pdo->query('SELECT COUNT(*) FROM packages');
        $count = $stmt->fetchColumn();
        echo "Total de paquetes: $count\n";
        
        if ($count > 0) {
            $stmt = $pdo->query('SELECT id, title, status, created_at FROM packages ORDER BY created_at DESC LIMIT 5');
            $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($packages as $package) {
                echo "- ID: {$package['id']}, Título: {$package['title']}, Estado: {$package['status']}\n";
            }
        }
        
    } else {
        echo "❌ Tabla packages no existe - necesita ser creada\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

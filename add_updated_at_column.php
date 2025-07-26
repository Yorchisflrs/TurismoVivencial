<?php
// Agregar columna updated_at a la tabla packages
$pdo = new PDO('mysql:host=localhost;dbname=hogartours', 'root', '');

try {
    echo "Agregando columna updated_at a la tabla packages...\n";
    
    // Verificar si la columna ya existe
    $stmt = $pdo->query("SHOW COLUMNS FROM packages LIKE 'updated_at'");
    if ($stmt->rowCount() == 0) {
        // Agregar la columna
        $pdo->exec("ALTER TABLE packages ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL");
        echo "✅ Columna updated_at agregada exitosamente\n";
        
        // Actualizar registros existentes
        $pdo->exec("UPDATE packages SET updated_at = created_at WHERE updated_at IS NULL");
        echo "✅ Registros existentes actualizados\n";
    } else {
        echo "✅ La columna updated_at ya existe\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
 
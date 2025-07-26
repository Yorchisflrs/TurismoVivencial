<?php
require_once 'config/database.php';

echo "=== VERIFICACIÓN Y CORRECCIÓN DE ESTRUCTURA DE BD ===\n";

try {
    // Verificar campos existentes en la tabla packages
    $stmt = $pdo->query("DESCRIBE packages");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Campos actuales en tabla packages:\n";
    foreach ($columns as $col) {
        echo "   - {$col['Field']} ({$col['Type']})\n";
    }
    
    // Verificar si faltan campos importantes
    $existing_fields = array_column($columns, 'Field');
    $required_fields = ['duration', 'max_participants'];
    
    foreach ($required_fields as $field) {
        if (!in_array($field, $existing_fields)) {
            echo "❌ Campo faltante: $field\n";
            
            // Agregar campos faltantes
            if ($field === 'duration') {
                $pdo->exec("ALTER TABLE packages ADD COLUMN duration VARCHAR(50) DEFAULT '1 día'");
                echo "✅ Campo 'duration' agregado\n";
            }
            
            if ($field === 'max_participants') {
                $pdo->exec("ALTER TABLE packages ADD COLUMN max_participants INT DEFAULT 10");
                echo "✅ Campo 'max_participants' agregado\n";
            }
        } else {
            echo "✅ Campo '$field' existe\n";
        }
    }
    
    // Actualizar paquetes existentes con valores por defecto
    $stmt = $pdo->query("UPDATE packages SET duration = '1 día' WHERE duration IS NULL OR duration = ''");
    $affected = $stmt->rowCount();
    if ($affected > 0) {
        echo "✅ Actualizados $affected paquetes con duración por defecto\n";
    }
    
    $stmt = $pdo->query("UPDATE packages SET max_participants = 10 WHERE max_participants IS NULL OR max_participants = 0");
    $affected = $stmt->rowCount();
    if ($affected > 0) {
        echo "✅ Actualizados $affected paquetes con capacidad por defecto\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICACIÓN COMPLETA ===\n";
?>

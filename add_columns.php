<?php
require_once 'config/database.php';
global $pdo;

try {
    echo "Adding missing columns to hosts table...\n";
    
    // Add columns in order without AFTER clause
    $columns_to_add = [
        'full_name' => "VARCHAR(255) NOT NULL DEFAULT ''",
        'email' => "VARCHAR(255) NOT NULL DEFAULT ''",
        'phone' => "VARCHAR(20) DEFAULT NULL", 
        'age' => "INT DEFAULT NULL",
        'business_name' => "VARCHAR(255) NOT NULL DEFAULT ''",
        'location' => "VARCHAR(255) NOT NULL DEFAULT ''",
        'description' => "TEXT NOT NULL",
        'experiences' => "TEXT DEFAULT NULL",
        'max_guests' => "INT DEFAULT NULL",
        'languages' => "VARCHAR(255) DEFAULT NULL",
        'motivation' => "TEXT NOT NULL"
    ];
    
    foreach ($columns_to_add as $column_name => $definition) {
        try {
            $sql = "ALTER TABLE hosts ADD COLUMN $column_name $definition";
            $pdo->exec($sql);
            echo "✓ Added $column_name column\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "- Column $column_name already exists\n";
            } else {
                echo "✗ Error adding $column_name: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nFinal table structure:\n";
    $stmt = $pdo->query('DESCRIBE hosts');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    echo "\n✅ Hosts table is now ready for host registration!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

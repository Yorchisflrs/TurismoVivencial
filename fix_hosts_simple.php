<?php
require_once 'config/database.php';
global $pdo;

try {
    echo "Fixing hosts table structure...\n";
    
    // Get current columns
    $stmt = $pdo->query('DESCRIBE hosts');
    $existing_columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    echo "Current columns: " . implode(', ', $existing_columns) . "\n";
    
    // Add missing columns one by one
    $columns_to_add = [
        'email' => "VARCHAR(255) NOT NULL DEFAULT '' AFTER full_name",
        'phone' => "VARCHAR(20) DEFAULT NULL AFTER email", 
        'age' => "INT DEFAULT NULL AFTER phone",
        'business_name' => "VARCHAR(255) NOT NULL DEFAULT '' AFTER age",
        'location' => "VARCHAR(255) NOT NULL DEFAULT '' AFTER business_name",
        'description' => "TEXT NOT NULL AFTER location",
        'experiences' => "TEXT DEFAULT NULL AFTER description",
        'max_guests' => "INT DEFAULT NULL AFTER experiences",
        'languages' => "VARCHAR(255) DEFAULT NULL AFTER max_guests",
        'motivation' => "TEXT NOT NULL AFTER languages"
    ];
    
    foreach ($columns_to_add as $column_name => $definition) {
        if (!in_array($column_name, $existing_columns)) {
            try {
                $sql = "ALTER TABLE hosts ADD COLUMN $column_name $definition";
                $pdo->exec($sql);
                echo "- Added $column_name column\n";
            } catch (Exception $e) {
                echo "- Error adding $column_name: " . $e->getMessage() . "\n";
            }
        } else {
            echo "- Column $column_name already exists\n";
        }
    }
    
    echo "\nUpdated table structure:\n";
    $stmt = $pdo->query('DESCRIBE hosts');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

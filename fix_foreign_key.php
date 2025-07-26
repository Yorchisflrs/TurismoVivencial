<?php
require_once 'config/database.php';
global $pdo;

try {
    echo "Fixing hosts table foreign key constraint...\n";
    
    // Option 1: Drop the foreign key constraint
    try {
        $pdo->exec("ALTER TABLE hosts DROP FOREIGN KEY hosts_ibfk_1");
        echo "✓ Dropped foreign key constraint hosts_ibfk_1\n";
    } catch (Exception $e) {
        echo "Foreign key constraint may not exist: " . $e->getMessage() . "\n";
    }
    
    // Option 2: Make user_id nullable
    try {
        $pdo->exec("ALTER TABLE hosts MODIFY COLUMN user_id INT NULL");
        echo "✓ Made user_id column nullable\n";
    } catch (Exception $e) {
        echo "Error making user_id nullable: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ Hosts table fixed! Host registration should now work.\n";
    
    // Test the table structure
    echo "\nUpdated table structure:\n";
    $stmt = $pdo->query('DESCRIBE hosts');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ") " . 
             ($column['Null'] == 'YES' ? '[NULL]' : '[NOT NULL]') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

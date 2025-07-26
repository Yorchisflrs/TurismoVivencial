<?php
require_once 'config/database.php';
global $pdo;

try {
    echo "All tables in database:\n";
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- " . $table . "\n";
    }
    
    echo "\nChecking if hosts table exists...\n";
    if (in_array('hosts', $tables)) {
        echo "hosts table exists. Checking structure:\n";
        $stmt = $pdo->query('DESCRIBE hosts');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
    } else {
        echo "hosts table does NOT exist!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

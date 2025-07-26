<?php
require_once 'config/database.php';
global $pdo;

try {
    $stmt = $pdo->query('DESCRIBE hosts');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in hosts table:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

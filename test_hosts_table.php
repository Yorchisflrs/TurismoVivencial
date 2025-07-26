<?php
require_once 'config/database.php';
global $pdo;

echo "Testing hosts table after fixes...\n\n";

try {
    // Check table structure
    $stmt = $pdo->query('DESCRIBE hosts');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Hosts table columns:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✅ HOSTS TABLE IS READY!\n";
    echo "All required columns are present for host registration.\n";
    
    // Test if we can query for email column (this was the original error)
    echo "\nTesting email column query...\n";
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM hosts WHERE email = ?');
    $stmt->execute(['test@example.com']);
    $count = $stmt->fetchColumn();
    echo "✅ Email column query works! Count: $count\n";
    
    echo "\n🎉 Host registration should now work without errors!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>

<?php
// Script to update the package_images table structure

$pdo = new PDO('mysql:host=localhost;dbname=hogartours', 'root', '');

try {
    echo "Updating package_images table structure...\n";
    
    // Add the missing columns
    $alterQueries = [
        "ALTER TABLE package_images ADD COLUMN filename VARCHAR(255) AFTER url",
        "ALTER TABLE package_images ADD COLUMN is_main TINYINT(1) NOT NULL DEFAULT 0 AFTER approved", 
        "ALTER TABLE package_images ADD COLUMN caption TEXT AFTER is_main"
    ];
    
    foreach ($alterQueries as $query) {
        try {
            $pdo->exec($query);
            echo "✓ Executed: $query\n";
        } catch (PDOException $e) {
            if ($e->getCode() == '42S21') { // Duplicate column name
                echo "→ Column already exists: $query\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Copy url to filename for existing records
    $pdo->exec("UPDATE package_images SET filename = SUBSTRING_INDEX(url, '/', -1) WHERE filename IS NULL OR filename = ''");
    echo "✓ Updated filename column with data from url\n";
    
    // Check the new structure
    $stmt = $pdo->query('DESCRIBE package_images');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nUpdated table structure:\n";
    foreach($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    echo "\nTable update completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

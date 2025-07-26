<?php
require_once 'config/database.php';
global $pdo;

try {
    echo "Fixing hosts table structure...\n";
    
    // Add email column if it doesn't exist
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS email VARCHAR(255) NOT NULL AFTER full_name");
    echo "- Email column added/exists\n";
    
    // Add phone column if it doesn't exist  
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER email");
    echo "- Phone column added/exists\n";
    
    // Add age column
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS age INT AFTER phone");
    echo "- Age column added/exists\n";
    
    // Add business_name column
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS business_name VARCHAR(255) NOT NULL DEFAULT '' AFTER age");
    echo "- Business name column added/exists\n";
    
    // Add location column
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS location VARCHAR(255) NOT NULL DEFAULT '' AFTER business_name");
    echo "- Location column added/exists\n";
    
    // Add description column
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS description TEXT NOT NULL AFTER location");
    echo "- Description column added/exists\n";
    
    // Add experiences column
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS experiences TEXT AFTER description");
    echo "- Experiences column added/exists\n";
    
    // Add max_guests column
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS max_guests INT AFTER experiences");
    echo "- Max guests column added/exists\n";
    
    // Add languages column
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS languages VARCHAR(255) AFTER max_guests");
    echo "- Languages column added/exists\n";
    
    // Add motivation column
    $pdo->exec("ALTER TABLE hosts ADD COLUMN IF NOT EXISTS motivation TEXT NOT NULL AFTER languages");
    echo "- Motivation column added/exists\n";
    
    echo "\nAll required columns added successfully!\n";
    
    // Show final structure
    echo "\nFinal table structure:\n";
    $stmt = $pdo->query('DESCRIBE hosts');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

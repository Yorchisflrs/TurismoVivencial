<?php
// Script to add sample images to packages

$pdo = new PDO('mysql:host=localhost;dbname=hogartours', 'root', '');

try {
    echo "Adding sample images to packages...\n";
    
    // Get all packages
    $stmt = $pdo->query("SELECT id FROM packages LIMIT 5");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($packages as $package) {
        $package_id = $package['id'];
        
        // Add 2-3 sample images per package
        $images = [
            ['filename' => 'sample1.jpg', 'url' => '/uploads/images/sample1.jpg', 'is_main' => 1, 'caption' => 'Vista principal'],
            ['filename' => 'sample2.jpg', 'url' => '/uploads/images/sample2.jpg', 'is_main' => 0, 'caption' => 'Interior'],
            ['filename' => 'sample3.jpg', 'url' => '/uploads/images/sample3.jpg', 'is_main' => 0, 'caption' => 'Área común']
        ];
        
        foreach ($images as $index => $image) {
            // Check if image already exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM package_images WHERE package_id = ? AND filename = ?");
            $checkStmt->execute([$package_id, $image['filename']]);
            
            if ($checkStmt->fetchColumn() == 0) {
                $insertStmt = $pdo->prepare("
                    INSERT INTO package_images (package_id, url, filename, approved, is_main, caption, created_at) 
                    VALUES (?, ?, ?, 1, ?, ?, NOW())
                ");
                $insertStmt->execute([
                    $package_id, 
                    $image['url'], 
                    $image['filename'], 
                    $image['is_main'], 
                    $image['caption']
                ]);
                echo "✓ Added {$image['filename']} to package {$package_id}\n";
            } else {
                echo "→ Image {$image['filename']} already exists for package {$package_id}\n";
            }
        }
    }
    
    echo "\nSample images added successfully!\n";
    
    // Show image count per package
    $stmt = $pdo->query("
        SELECT p.title, COUNT(pi.id) as image_count 
        FROM packages p 
        LEFT JOIN package_images pi ON p.id = pi.package_id 
        GROUP BY p.id, p.title
    ");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nImage count per package:\n";
    foreach ($results as $result) {
        echo "- {$result['title']}: {$result['image_count']} images\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

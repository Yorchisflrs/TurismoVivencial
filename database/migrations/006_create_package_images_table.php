<?php
// database/migrations/006_create_package_images_table.php
require_once __DIR__ . '/../../config/database.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS package_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        package_id INT NOT NULL,
        filename VARCHAR(255) NOT NULL,
        is_main BOOLEAN DEFAULT FALSE,
        caption TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
        
        INDEX idx_package_id (package_id),
        INDEX idx_is_main (is_main)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "✅ Tabla 'package_images' creada exitosamente.\n";
    
    // Crear algunas imágenes de ejemplo para los paquetes existentes
    $sample_images = [
        ['package_id' => 1, 'filename' => 'sample_textil_1.jpg', 'is_main' => 1, 'caption' => 'Telar tradicional de la familia Mamani'],
        ['package_id' => 1, 'filename' => 'sample_textil_2.jpg', 'is_main' => 0, 'caption' => 'Proceso de teñido natural'],
        ['package_id' => 2, 'filename' => 'sample_cocina_1.jpg', 'is_main' => 1, 'caption' => 'Preparando trucha del Titicaca'],
        ['package_id' => 2, 'filename' => 'sample_cocina_2.jpg', 'is_main' => 0, 'caption' => 'Papas nativas multicolores'],
        ['package_id' => 3, 'filename' => 'sample_pesca_1.jpg', 'is_main' => 1, 'caption' => 'Balsa de totora en el amanecer'],
    ];
    
    $insert_sql = "INSERT INTO package_images (package_id, filename, is_main, caption) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($insert_sql);
    
    foreach ($sample_images as $image) {
        $stmt->execute([$image['package_id'], $image['filename'], $image['is_main'], $image['caption']]);
    }
    
    echo "✅ Imágenes de ejemplo agregadas.\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>

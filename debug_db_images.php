<?php
// Debug de imágenes en la base de datos
require_once 'config/database.php';

echo "=== DEBUG DE IMÁGENES EN BD ===\n\n";

try {
    // Obtener todas las imágenes con información del paquete
    $sql = "SELECT pi.*, p.title as package_title 
            FROM package_images pi 
            LEFT JOIN packages p ON pi.package_id = p.id 
            ORDER BY pi.package_id, pi.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total de imágenes: " . count($images) . "\n\n";
    
    // Agrupar por paquete
    $by_package = [];
    foreach ($images as $image) {
        $package_id = $image['package_id'];
        if (!isset($by_package[$package_id])) {
            $by_package[$package_id] = [];
        }
        $by_package[$package_id][] = $image;
    }
    
    foreach ($by_package as $package_id => $package_images) {
        $package_title = $package_images[0]['package_title'] ?? 'Sin título';
        echo "PAQUETE $package_id: $package_title\n";
        echo "Cantidad de imágenes: " . count($package_images) . "\n";
        
        foreach ($package_images as $img) {
            echo "  - ID: {$img['id']}, Archivo: {$img['filename']}, Principal: " . ($img['is_main'] ? 'SÍ' : 'NO') . "\n";
        }
        echo "\n";
    }
    
    // Verificar si hay imágenes duplicadas o huérfanas
    echo "=== VERIFICACIONES ===\n";
    
    // Imágenes sin paquete
    $orphaned = $pdo->query("
        SELECT pi.* FROM package_images pi 
        LEFT JOIN packages p ON pi.package_id = p.id 
        WHERE p.id IS NULL
    ")->fetchAll();
    
    if (!empty($orphaned)) {
        echo "⚠️ Imágenes huérfanas (sin paquete): " . count($orphaned) . "\n";
        foreach ($orphaned as $img) {
            echo "  - ID: {$img['id']}, Package ID: {$img['package_id']}, Archivo: {$img['filename']}\n";
        }
    } else {
        echo "✅ No hay imágenes huérfanas\n";
    }
    
    // Múltiples imágenes principales por paquete
    $multiple_main = $pdo->query("
        SELECT package_id, COUNT(*) as count 
        FROM package_images 
        WHERE is_main = 1 
        GROUP BY package_id 
        HAVING count > 1
    ")->fetchAll();
    
    if (!empty($multiple_main)) {
        echo "⚠️ Paquetes con múltiples imágenes principales:\n";
        foreach ($multiple_main as $pkg) {
            echo "  - Paquete {$pkg['package_id']}: {$pkg['count']} imágenes principales\n";
        }
    } else {
        echo "✅ No hay conflictos de imágenes principales\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEBUG ===\n";
?>

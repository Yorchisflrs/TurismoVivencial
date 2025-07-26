<?php
// Fix para el problema de imágenes duplicadas
require_once 'config/database.php';

echo "=== CORRIGIENDO PROBLEMA DE IMÁGENES DUPLICADAS ===\n\n";

try {
    // 1. Limpiar imágenes principales duplicadas
    echo "1. Limpiando imágenes principales duplicadas...\n";
    
    // Obtener paquetes con múltiples imágenes principales
    $multiple_main = $pdo->query("
        SELECT package_id, COUNT(*) as count 
        FROM package_images 
        WHERE is_main = 1 
        GROUP BY package_id 
        HAVING count > 1
    ")->fetchAll();
    
    foreach ($multiple_main as $pkg) {
        echo "   - Paquete {$pkg['package_id']} tiene {$pkg['count']} imágenes principales\n";
        
        // Obtener todas las imágenes principales de este paquete
        $stmt = $pdo->prepare("
            SELECT id FROM package_images 
            WHERE package_id = ? AND is_main = 1 
            ORDER BY created_at ASC
        ");
        $stmt->execute([$pkg['package_id']]);
        $main_images = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Mantener solo la primera como principal
        $first_image = array_shift($main_images);
        echo "     Manteniendo imagen {$first_image} como principal\n";
        
        // Quitar flag principal de las demás
        foreach ($main_images as $img_id) {
            $pdo->prepare("UPDATE package_images SET is_main = 0 WHERE id = ?")
                ->execute([$img_id]);
            echo "     Removiendo flag principal de imagen {$img_id}\n";
        }
    }
    
    // 2. Asegurar que cada paquete tenga al menos una imagen principal
    echo "\n2. Asegurando imagen principal por paquete...\n";
    
    $packages_without_main = $pdo->query("
        SELECT DISTINCT pi.package_id 
        FROM package_images pi 
        LEFT JOIN (
            SELECT package_id 
            FROM package_images 
            WHERE is_main = 1
        ) main_imgs ON pi.package_id = main_imgs.package_id 
        WHERE main_imgs.package_id IS NULL
    ")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($packages_without_main as $package_id) {
        // Obtener la primera imagen de este paquete
        $stmt = $pdo->prepare("
            SELECT id FROM package_images 
            WHERE package_id = ? 
            ORDER BY created_at ASC 
            LIMIT 1
        ");
        $stmt->execute([$package_id]);
        $first_image = $stmt->fetchColumn();
        
        if ($first_image) {
            $pdo->prepare("UPDATE package_images SET is_main = 1 WHERE id = ?")
                ->execute([$first_image]);
            echo "   - Estableciendo imagen {$first_image} como principal para paquete {$package_id}\n";
        }
    }
    
    // 3. Verificar estado final
    echo "\n3. Verificación final...\n";
    
    $final_check = $pdo->query("
        SELECT p.id, p.title, 
               COUNT(pi.id) as total_images,
               SUM(CASE WHEN pi.is_main = 1 THEN 1 ELSE 0 END) as main_images
        FROM packages p
        LEFT JOIN package_images pi ON p.id = pi.package_id
        GROUP BY p.id, p.title
        ORDER BY p.id
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($final_check as $pkg) {
        $status = $pkg['main_images'] == 1 ? "✅" : ($pkg['main_images'] > 1 ? "⚠️" : "❌");
        echo "   {$status} Paquete {$pkg['id']}: {$pkg['total_images']} imágenes, {$pkg['main_images']} principal(es)\n";
    }
    
    echo "\n✅ CORRECCIÓN COMPLETADA!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>

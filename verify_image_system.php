<?php
// Verificación completa del sistema de imágenes
require_once 'config/database.php';

echo "=== VERIFICACIÓN DEL SISTEMA DE IMÁGENES ===\n\n";

try {
    // 1. Estado de las imágenes por paquete
    echo "1. Estado de imágenes por paquete:\n";
    
    $stmt = $pdo->query("
        SELECT p.id, p.title, p.status as package_status,
               COUNT(pi.id) as total_images,
               SUM(CASE WHEN pi.is_main = 1 THEN 1 ELSE 0 END) as main_images,
               (SELECT pi2.filename FROM package_images pi2 
                WHERE pi2.package_id = p.id AND pi2.is_main = 1 LIMIT 1) as main_filename
        FROM packages p
        LEFT JOIN package_images pi ON p.id = pi.package_id
        GROUP BY p.id, p.title, p.status
        ORDER BY p.id
    ");
    
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($packages as $pkg) {
        $status_icon = $pkg['main_images'] == 1 ? "✅" : 
                      ($pkg['main_images'] > 1 ? "⚠️ MÚLTIPLES" : 
                      ($pkg['total_images'] > 0 ? "❌ SIN PRINCIPAL" : "📭 SIN IMÁGENES"));
        
        echo "   {$status_icon} Paquete {$pkg['id']}: \"{$pkg['title']}\"\n";
        echo "      - Estado: {$pkg['package_status']}\n";
        echo "      - Total imágenes: {$pkg['total_images']}\n";
        echo "      - Imágenes principales: {$pkg['main_images']}\n";
        if ($pkg['main_filename']) {
            echo "      - Archivo principal: {$pkg['main_filename']}\n";
        }
        echo "\n";
    }
    
    // 2. Verificar archivos físicos
    echo "2. Verificación de archivos físicos:\n";
    
    $upload_dir = __DIR__ . '/uploads/packages/';
    $thumb_dir = __DIR__ . '/uploads/packages/thumbs/';
    
    echo "   - Directorio upload: " . (is_dir($upload_dir) ? "✅" : "❌") . "\n";
    echo "   - Directorio thumbs: " . (is_dir($thumb_dir) ? "✅" : "❌") . "\n";
    
    if (is_dir($upload_dir)) {
        $files = glob($upload_dir . '*');
        echo "   - Archivos en upload: " . count($files) . "\n";
    }
    
    if (is_dir($thumb_dir)) {
        $thumbs = glob($thumb_dir . '*');
        echo "   - Archivos en thumbs: " . count($thumbs) . "\n";
    }
    
    // 3. Verificar integridad de datos
    echo "\n3. Verificación de integridad:\n";
    
    // Imágenes sin archivo físico
    $stmt = $pdo->query("SELECT filename FROM package_images");
    $db_files = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $missing_files = 0;
    foreach ($db_files as $filename) {
        if (!file_exists($upload_dir . $filename)) {
            $missing_files++;
            echo "   ⚠️ Archivo faltante: {$filename}\n";
        }
    }
    
    if ($missing_files == 0) {
        echo "   ✅ Todos los archivos de BD existen físicamente\n";
    }
    
    // 4. Resumen final
    echo "\n4. Resumen:\n";
    $total_packages = count($packages);
    $packages_with_images = count(array_filter($packages, fn($p) => $p['total_images'] > 0));
    $packages_with_correct_main = count(array_filter($packages, fn($p) => $p['main_images'] == 1));
    
    echo "   - Total paquetes: {$total_packages}\n";
    echo "   - Paquetes con imágenes: {$packages_with_images}\n";
    echo "   - Paquetes con imagen principal correcta: {$packages_with_correct_main}\n";
    
    if ($packages_with_images == $packages_with_correct_main && $packages_with_images > 0) {
        echo "\n🎉 SISTEMA DE IMÁGENES FUNCIONANDO PERFECTAMENTE!\n";
    } else {
        echo "\n⚠️ Hay algunos problemas que necesitan atención.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN VERIFICACIÓN ===\n";
?>

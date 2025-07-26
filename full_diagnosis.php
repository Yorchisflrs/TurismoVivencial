<?php
// Verificación completa del sistema de upload
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DIAGNÓSTICO COMPLETO ===\n\n";

// 1. Verificar que XAMPP esté configurado correctamente
echo "1. Configuración PHP:\n";
echo "- Version: " . phpversion() . "\n";
echo "- Upload max: " . ini_get('upload_max_filesize') . "\n";
echo "- Post max: " . ini_get('post_max_size') . "\n";
echo "- Memory: " . ini_get('memory_limit') . "\n";
echo "- Session status: " . (session_status() === PHP_SESSION_ACTIVE ? "Activa" : "Inactiva") . "\n";

// 2. Verificar directorios
echo "\n2. Directorios de upload:\n";
$upload_dir = __DIR__ . '/uploads/packages';
$thumb_dir = __DIR__ . '/uploads/packages/thumbs';

if (!is_dir($upload_dir)) {
    echo "⚠️ Creando directorio de upload...\n";
    mkdir($upload_dir, 0777, true);
}
if (!is_dir($thumb_dir)) {
    echo "⚠️ Creando directorio de thumbs...\n";
    mkdir($thumb_dir, 0777, true);
}

echo "- Upload dir existe: " . (is_dir($upload_dir) ? "✅" : "❌") . "\n";
echo "- Upload writable: " . (is_writable($upload_dir) ? "✅" : "❌") . "\n";
echo "- Thumb dir existe: " . (is_dir($thumb_dir) ? "✅" : "❌") . "\n";
echo "- Thumb writable: " . (is_writable($thumb_dir) ? "✅" : "❌") . "\n";

// 3. Verificar base de datos
echo "\n3. Base de datos:\n";
try {
    require_once 'config/database.php';
    echo "- Conexión DB: ✅\n";
    
    // Verificar tabla package_images
    $stmt = $pdo->query("DESCRIBE package_images");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "- Tabla package_images: ✅\n";
    echo "- Columnas: " . implode(', ', $columns) . "\n";
    
    // Verificar que existe el paquete 1
    $stmt = $pdo->prepare("SELECT id, title FROM packages WHERE id = 1");
    $stmt->execute();
    $package = $stmt->fetch();
    if ($package) {
        echo "- Paquete 1 existe: ✅ (" . $package['title'] . ")\n";
    } else {
        echo "- Paquete 1: ❌ No existe\n";
    }
    
} catch (Exception $e) {
    echo "- Error DB: ❌ " . $e->getMessage() . "\n";
}

// 4. Test del ImageController paso a paso
echo "\n4. Test del ImageController:\n";
try {
    session_start();
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'ADMIN';
    
    require_once 'src/lib/helpers.php';
    echo "- Helpers: ✅\n";
    
    require_once 'src/models/PackageImage.php';
    echo "- PackageImage model: ✅\n";
    
    require_once 'src/helpers/ImageHelper.php';
    echo "- ImageHelper: ✅\n";
    
    // Test de PackageImage
    $packageImageModel = new PackageImage($pdo);
    echo "- PackageImage instancia: ✅\n";
    
    // Test de ImageHelper paths
    $testPath = ImageHelper::getImageUrl('test.jpg');
    echo "- ImageHelper URL: $testPath\n";
    
} catch (Exception $e) {
    echo "- Error: ❌ " . $e->getMessage() . "\n";
    echo "- File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== FIN DIAGNÓSTICO ===\n";
?>

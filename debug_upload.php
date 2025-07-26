<?php
// Debug específico para el upload de imágenes
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simular una sesión de usuario válida
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'ADMIN';

echo "=== DEBUG UPLOAD DE IMÁGENES ===\n";

// 1. Test de la configuración básica
echo "1. PHP Info:\n";
echo "- Version: " . phpversion() . "\n";
echo "- Upload max size: " . ini_get('upload_max_filesize') . "\n";
echo "- Post max size: " . ini_get('post_max_size') . "\n";
echo "- Memory limit: " . ini_get('memory_limit') . "\n\n";

// 2. Test de directorios
echo "2. Directorios:\n";
$base_dir = __DIR__;
$upload_dir = $base_dir . '/uploads/packages';
$thumb_dir = $base_dir . '/uploads/packages/thumbs';

echo "- Base dir: $base_dir\n";
echo "- Upload dir: $upload_dir\n";
echo "- Upload exists: " . (is_dir($upload_dir) ? "SÍ" : "NO") . "\n";
echo "- Upload writable: " . (is_writable($upload_dir) ? "SÍ" : "NO") . "\n";
echo "- Thumb dir: $thumb_dir\n";
echo "- Thumb exists: " . (is_dir($thumb_dir) ? "SÍ" : "NO") . "\n";
echo "- Thumb writable: " . (is_writable($thumb_dir) ? "SÍ" : "NO") . "\n\n";

// 3. Test del ImageController sin instantiación completa
echo "3. Test de clases:\n";
try {
    require_once 'config/database.php';
    echo "- Database config: OK\n";
    
    require_once 'src/lib/helpers.php';
    echo "- Helpers: OK\n";
    
    require_once 'src/models/PackageImage.php';
    echo "- PackageImage model: OK\n";
    
    require_once 'src/helpers/ImageHelper.php';
    echo "- ImageHelper: OK\n";
    
} catch (Exception $e) {
    echo "- Error: " . $e->getMessage() . "\n";
}

// 4. Test de respuesta JSON limpia
echo "\n4. Test de respuesta JSON:\n";
ob_start();
$test_data = ['success' => true, 'test' => 'OK', 'timestamp' => time()];
header('Content-Type: application/json');
echo json_encode($test_data);
$output = ob_get_clean();
echo "- JSON output: $output\n";
echo "- JSON válido: " . (json_decode($output) ? "SÍ" : "NO") . "\n";

// 5. Simular un upload básico (solo estructura)
echo "\n5. Simulación de upload:\n";
if (!empty($_FILES)) {
    echo "- Archivos recibidos: " . count($_FILES) . "\n";
    var_dump($_FILES);
} else {
    echo "- No hay archivos en \$_FILES\n";
}

echo "\n=== FIN DEBUG ===\n";
?>

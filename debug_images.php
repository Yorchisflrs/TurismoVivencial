<?php
// Debug del sistema de upload de imágenes
session_start();
require_once 'config/database.php';

echo "=== DEBUG DEL SISTEMA DE IMÁGENES ===\n";

// 1. Verificar rutas de upload
$upload_path = __DIR__ . '/public/uploads/packages/';
$thumb_path = __DIR__ . '/public/uploads/packages/thumbs/';

echo "Ruta de upload: $upload_path\n";
echo "Existe upload dir: " . (is_dir($upload_path) ? "SÍ" : "NO") . "\n";
echo "Writable upload dir: " . (is_writable(dirname($upload_path)) ? "SÍ" : "NO") . "\n";

echo "Ruta de thumbs: $thumb_path\n";
echo "Existe thumb dir: " . (is_dir($thumb_path) ? "SÍ" : "NO") . "\n";

// 2. Verificar que las clases necesarias existen
$required_files = [
    'src/models/PackageImage.php',
    'src/helpers/ImageHelper.php',
    'src/controllers/ImageController.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe\n";
    } else {
        echo "❌ $file NO EXISTE\n";
    }
}

// 3. Crear directorios si no existen
if (!is_dir($upload_path)) {
    if (mkdir($upload_path, 0777, true)) {
        echo "✅ Directorio de upload creado\n";
    } else {
        echo "❌ No se pudo crear directorio de upload\n";
    }
}

if (!is_dir($thumb_path)) {
    if (mkdir($thumb_path, 0777, true)) {
        echo "✅ Directorio de thumbs creado\n";
    } else {
        echo "❌ No se pudo crear directorio de thumbs\n";
    }
}

// 4. Verificar modelo PackageImage
try {
    require_once 'src/models/PackageImage.php';
    $packageImageModel = new PackageImage($pdo);
    echo "✅ Modelo PackageImage funciona\n";
} catch (Exception $e) {
    echo "❌ Error en PackageImage: " . $e->getMessage() . "\n";
}

// 5. Probar una respuesta JSON simple
echo "\n=== PRUEBA DE RESPUESTA JSON ===\n";
header('Content-Type: application/json');
$test_response = ['success' => true, 'message' => 'Test OK'];
echo json_encode($test_response) . "\n";

echo "\n=== DEBUG COMPLETO ===\n";
?>

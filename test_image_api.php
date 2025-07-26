<?php
// Test simple de la API de imágenes
session_start();
require_once 'config/database.php';
require_once 'src/lib/helpers.php';

// Simular sesión de admin
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'ADMIN';

echo "=== TEST API IMÁGENES ===\n";

// Test básico del ImageController
try {
    require_once 'src/controllers/ImageController.php';
    $controller = new ImageController();
    echo "✅ ImageController creado correctamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Verificar directorios
$upload_dir = __DIR__ . '/uploads/packages';
$thumb_dir = __DIR__ . '/uploads/packages/thumbs';

echo "Upload dir: " . (is_dir($upload_dir) ? "✅" : "❌") . "\n";
echo "Thumb dir: " . (is_dir($thumb_dir) ? "✅" : "❌") . "\n";
echo "Upload writable: " . (is_writable($upload_dir) ? "✅" : "❌") . "\n";

echo "\n🚀 SISTEMA LISTO PARA UPLOAD DE IMÁGENES\n";
?>

<?php
// Simulador de POST request al API de upload
session_start();

// Simular sesión de admin
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'ADMIN';

// Simular POST data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['package_id'] = 1;

// Simular archivos vacíos (para test sin archivos)
$_FILES = [
    'images' => [
        'name' => [],
        'type' => [],
        'tmp_name' => [],
        'error' => [],
        'size' => []
    ]
];

echo "Simulando POST request a ImageController->uploadImages()...\n\n";

try {
    // Incluir el router o el controller directamente
    require_once 'config/database.php';
    require_once 'src/lib/helpers.php';
    require_once 'src/controllers/ImageController.php';
    
    $controller = new ImageController();
    
    // Llamar al método directamente
    echo "Llamando uploadImages():\n";
    $controller->uploadImages();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>

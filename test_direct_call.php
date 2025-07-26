<?php
// Test directo sin router
session_start();

// Configurar sesión
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'ADMIN';

// Configurar POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['package_id'] = 1;

// Configurar FILES para test sin archivos
$_FILES['images'] = [
    'name' => [],
    'type' => [],
    'tmp_name' => [],
    'error' => [],
    'size' => []
];

// Incluir dependencias
require_once 'config/database.php';
require_once 'src/lib/helpers.php';

// Cargar ImageController
require_once 'src/controllers/ImageController.php';

// Crear instancia y llamar método
$controller = new ImageController();
$controller->uploadImages();
?>

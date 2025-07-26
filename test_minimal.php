<?php
// Test mínimo del endpoint
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'ADMIN';

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['package_id'] = 1;
$_FILES['images'] = ['name' => [], 'type' => [], 'tmp_name' => [], 'error' => [], 'size' => []];

// Incluir solo lo necesario
require_once 'config/database.php';
require_once 'src/lib/helpers.php';
require_once 'src/models/PackageImage.php';
require_once 'src/helpers/ImageHelper.php';

// Limpiar todo output previo
while (ob_get_level()) { ob_end_clean(); }

// Headers JSON
header('Content-Type: application/json; charset=utf-8');

// Respuesta simple
echo json_encode([
    'success' => true, 
    'message' => 'Test endpoint funcionando',
    'data' => [
        'user_id' => $_SESSION['user_id'],
        'package_id' => $_POST['package_id'],
        'files_received' => isset($_FILES['images'])
    ]
]);
exit;
?>

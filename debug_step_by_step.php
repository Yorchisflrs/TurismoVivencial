<?php
// Test de debugging paso a paso del upload
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "INICIO DEL SCRIPT\n";
flush();

session_start();
echo "Session iniciada\n";
flush();

// Simular sesión de admin
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'ADMIN';
echo "Sesión configurada\n";
flush();

// Simular POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['package_id'] = 1;
echo "POST data configurada\n";
flush();

// Incluir archivos uno por uno para detectar output
echo "Incluyendo database.php\n";
flush();
require_once 'config/database.php';

echo "Incluyendo helpers.php\n";
flush();
require_once 'src/lib/helpers.php';

echo "Incluyendo PackageImage.php\n";
flush();
require_once 'src/models/PackageImage.php';

echo "Incluyendo ImageHelper.php\n";
flush();
require_once 'src/helpers/ImageHelper.php';

echo "Incluyendo ImageController.php\n";
flush();
require_once 'src/controllers/ImageController.php';

echo "Creando instancia de ImageController\n";
flush();

// Capturar output antes de crear el controller
ob_start();
$controller = new ImageController();
$output_before = ob_get_clean();

if (trim($output_before)) {
    echo "⚠️ OUTPUT DETECTADO AL CREAR CONTROLLER: '$output_before'\n";
} else {
    echo "✅ Controller creado sin output\n";
}

echo "FIN DEL SCRIPT\n";
?>

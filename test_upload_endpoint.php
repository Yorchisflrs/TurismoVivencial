<?php
// Test directo del endpoint de upload
session_start();

// Simular sesión de admin
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'ADMIN';

echo "=== TEST DIRECTO DEL ENDPOINT UPLOAD ===\n\n";

// Simular POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['package_id'] = 1;

echo "1. Verificando configuración:\n";
echo "- Método: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "- Package ID: " . $_POST['package_id'] . "\n";
echo "- User ID: " . $_SESSION['user_id'] . "\n\n";

echo "2. Intentando cargar ImageController:\n";
try {
    // Capturar cualquier error o output
    ob_start();
    
    require_once 'config/database.php';
    echo "- Database config: OK\n";
    
    require_once 'src/lib/helpers.php';
    echo "- Helpers: OK\n";
    
    require_once 'src/controllers/ImageController.php';
    echo "- ImageController clase: OK\n";
    
    // Intentar crear el controller
    $controller = new ImageController();
    echo "- ImageController instancia: OK\n";
    
    // Verificar que no hay output hasta ahora
    $current_output = ob_get_contents();
    if (trim($current_output)) {
        echo "- ⚠️ ADVERTENCIA: Hay output antes del JSON:\n";
        echo "'" . $current_output . "'\n";
    } else {
        echo "- Output buffer limpio: OK\n";
    }
    
    ob_end_clean();
    
} catch (Exception $e) {
    ob_end_clean();
    echo "- ERROR: " . $e->getMessage() . "\n";
    echo "- File: " . $e->getFile() . "\n";
    echo "- Line: " . $e->getLine() . "\n";
}

echo "\n3. Test de permisos de paquete:\n";
try {
    global $pdo;
    
    // Verificar que el paquete existe
    $stmt = $pdo->prepare("SELECT id, title FROM packages WHERE id = ?");
    $stmt->execute([1]);
    $package = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($package) {
        echo "- Paquete encontrado: " . $package['title'] . "\n";
    } else {
        echo "- ❌ Paquete no encontrado\n";
    }
    
} catch (Exception $e) {
    echo "- Error DB: " . $e->getMessage() . "\n";
}

echo "\n=== FIN TEST ===\n";
?>

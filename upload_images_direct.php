<?php
// Endpoint de upload completamente nuevo y limpio
session_start();

// Limpiar TODO antes de empezar
while (ob_get_level()) {
    ob_end_clean();
}

// Configurar headers inmediatamente
header('Content-Type: application/json; charset=utf-8', true);
header('Cache-Control: no-cache, must-revalidate', true);

// Función para respuesta JSON limpia
function sendJsonResponse($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'error' => 'Método no permitido']);
}

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(['success' => false, 'error' => 'No autorizado']);
}

// Obtener package_id
$package_id = $_POST['package_id'] ?? null;
if (!$package_id) {
    sendJsonResponse(['success' => false, 'error' => 'ID de paquete requerido']);
}

// Verificar archivos
if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
    sendJsonResponse(['success' => false, 'error' => 'No se enviaron archivos']);
}

try {
    // Conectar a base de datos
    require_once 'config/database.php';
    
    // Verificar permisos del usuario (simplificado para admin)
    if ($_SESSION['user_role'] !== 'ADMIN') {
        // Para hosts, verificar que el paquete les pertenece
        $stmt = $pdo->prepare("
            SELECT p.id FROM packages p 
            JOIN hosts h ON p.host_id = h.id 
            WHERE p.id = ? AND h.email = (SELECT email FROM users WHERE id = ?)
        ");
        $stmt->execute([$package_id, $_SESSION['user_id']]);
        if (!$stmt->fetch()) {
            sendJsonResponse(['success' => false, 'error' => 'Sin permisos para este paquete']);
        }
    }
    
    // Procesar archivos
    $files = $_FILES['images'];
    $uploaded_images = [];
    $errors = [];
    
    // Configurar rutas de upload
    $upload_dir = __DIR__ . '/uploads/packages/';
    $thumb_dir = __DIR__ . '/uploads/packages/thumbs/';
    
    // Crear directorios si no existen
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    if (!is_dir($thumb_dir)) mkdir($thumb_dir, 0777, true);
    
    // Procesar cada archivo
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
        
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];
        
        // Validaciones básicas
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error en archivo {$file['name']}";
            continue;
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            $errors[] = "Archivo {$file['name']} muy grande";
            continue;
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Tipo de archivo {$file['name']} no permitido";
            continue;
        }
        
        // Generar nombre único
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'package_' . $package_id . '_' . uniqid() . '.' . $extension;
        
        // Mover archivo
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            
            // Crear thumbnail simple
            $thumb_path = $thumb_dir . $filename;
            copy($target_path, $thumb_path);
            
            // Guardar en base de datos
            $caption = $_POST['captions'][$i] ?? '';
            $is_main = isset($_POST['main_image']) && $_POST['main_image'] == $i ? 1 : 0;
            
            // Si es imagen principal, quitar flag de otras imágenes del mismo paquete
            if ($is_main) {
                $pdo->prepare("UPDATE package_images SET is_main = 0 WHERE package_id = ? AND id != ?")
                    ->execute([$package_id, 0]); // Usar 0 porque aún no tenemos el ID de la nueva imagen
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO package_images (package_id, filename, is_main, caption, approved, created_at) 
                VALUES (?, ?, ?, ?, 1, NOW())
            ");
            $stmt->execute([$package_id, $filename, $is_main, $caption]);
            $image_id = $pdo->lastInsertId();
            
            // Si esta imagen es principal, asegurar que es la única
            if ($is_main) {
                $pdo->prepare("UPDATE package_images SET is_main = 0 WHERE package_id = ? AND id != ?")
                    ->execute([$package_id, $image_id]);
                $pdo->prepare("UPDATE package_images SET is_main = 1 WHERE id = ?")
                    ->execute([$image_id]);
            }
            
            $uploaded_images[] = [
                'id' => $image_id,
                'filename' => $filename,
                'url' => '/hogartours/uploads/packages/' . $filename,
                'thumb_url' => '/hogartours/uploads/packages/thumbs/' . $filename,
                'is_main' => $is_main,
                'caption' => $caption
            ];
        } else {
            $errors[] = "Error al mover archivo {$file['name']}";
        }
    }
    
    // Respuesta final
    $response = ['success' => true, 'images' => $uploaded_images];
    if (!empty($errors)) {
        $response['warnings'] = $errors;
    }
    
    sendJsonResponse($response);
    
} catch (Exception $e) {
    sendJsonResponse(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>

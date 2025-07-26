<?php
// Test simple para diagnóstico de JSON
session_start();

// Simular sesión de admin
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'ADMIN';

// Forzar headers JSON desde el inicio
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Limpiar cualquier output previo
if (ob_get_level()) {
    ob_clean();
}

// Test simple
$response = [
    'success' => true,
    'message' => 'API de imágenes funcionando',
    'timestamp' => date('Y-m-d H:i:s'),
    'debug' => [
        'session_user' => $_SESSION['user_id'] ?? 'No user',
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'No method',
        'upload_max' => ini_get('upload_max_filesize'),
        'post_max' => ini_get('post_max_size')
    ]
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
?>

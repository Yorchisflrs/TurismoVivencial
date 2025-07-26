<?php
// Verificar sistema de usuario
echo "=== VERIFICACIÓN DEL SISTEMA DE USUARIO ===\n";

// Verificar rutas en index.php
$index_content = file_get_contents('index.php');

$rutas = ['/profile', '/my-bookings'];
foreach ($rutas as $ruta) {
    if (strpos($index_content, $ruta) !== false) {
        echo "✅ Ruta $ruta configurada\n";
    } else {
        echo "❌ Ruta $ruta NO configurada\n";
    }
}

// Verificar archivos
$archivos = ['templates/profile.php', 'templates/my-bookings.php'];
foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "✅ Archivo $archivo existe\n";
    } else {
        echo "❌ Archivo $archivo NO existe\n";
    }
}

echo "\n=== VERIFICACIÓN COMPLETA ===\n";
?>

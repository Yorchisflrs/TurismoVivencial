<?php
// src/controllers/HostController.php
require_once __DIR__ . '/../models/Host.php';
require_once __DIR__ . '/../models/Package.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';

class HostController {
    
    public function dashboard() {
        // Verificar si el usuario está logueado y es un anfitrión aprobado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /hogartours/login');
            exit;
        }
        
        global $pdo;
        
        // Buscar el registro de anfitrión del usuario
        $stmt = $pdo->prepare('SELECT * FROM hosts WHERE user_id = ? AND status = "APPROVED"');
        $stmt->execute([$_SESSION['user_id']]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$host) {
            $_SESSION['error'] = 'No tienes permisos de anfitrión o tu solicitud aún no ha sido aprobada.';
            header('Location: /hogartours/');
            exit;
        }
        
        // Obtener estadísticas del anfitrión
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM packages WHERE host_id = ?');
        $stmt->execute([$host['id']]);
        $stats['total_packages'] = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM packages WHERE host_id = ? AND status = "PENDING"');
        $stmt->execute([$host['id']]);
        $stats['pending_packages'] = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM packages WHERE host_id = ? AND status = "APPROVED"');
        $stmt->execute([$host['id']]);
        $stats['approved_packages'] = $stmt->fetchColumn();
        
        // Obtener paquetes del anfitrión
        $stmt = $pdo->prepare('
            SELECT p.*, COUNT(pi.id) as image_count
            FROM packages p
            LEFT JOIN package_images pi ON p.id = pi.package_id
            WHERE p.host_id = ?
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ');
        $stmt->execute([$host['id']]);
        $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require __DIR__ . '/../../templates/host/dashboard.php';
    }
    
    public function createPackage() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /hogartours/login');
            exit;
        }
        
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM hosts WHERE user_id = ? AND status = "APPROVED"');
        $stmt->execute([$_SESSION['user_id']]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$host) {
            $_SESSION['error'] = 'No tienes permisos de anfitrión.';
            header('Location: /hogartours/');
            exit;
        }
        
        require __DIR__ . '/../../templates/host/create-package.php';
    }
    
    public function storePackage() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /hogartours/login');
            exit;
        }
        
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM hosts WHERE user_id = ? AND status = "APPROVED"');
        $stmt->execute([$_SESSION['user_id']]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$host) {
            $_SESSION['error'] = 'No tienes permisos de anfitrión.';
            header('Location: /hogartours/');
            exit;
        }
        
        // Validar campos requeridos
        $required_fields = ['title', 'location', 'category', 'price', 'capacity', 'description'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = 'Por favor, completa todos los campos obligatorios.';
                header('Location: /hogartours/host/create-package');
                exit;
            }
        }
        
        try {
            // Crear el paquete
            $stmt = $pdo->prepare('
                INSERT INTO packages (host_id, title, location, category, price, capacity, description, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, "PENDING", NOW())
            ');
            
            $result = $stmt->execute([
                $host['id'],
                $_POST['title'],
                $_POST['location'],
                $_POST['category'],
                $_POST['price'],
                $_POST['capacity'],
                $_POST['description']
            ]);
            
            if ($result) {
                $package_id = $pdo->lastInsertId();
                
                // Procesar imágenes si se subieron
                if (!empty($_FILES['images']['name'][0])) {
                    require_once __DIR__ . '/../helpers/ImageHelper.php';
                    $uploadResult = ImageHelper::uploadMultipleImages($_FILES['images'], $package_id);
                    
                    if (!$uploadResult['success']) {
                        $_SESSION['warning'] = 'Paquete creado pero hubo problemas con las imágenes: ' . $uploadResult['message'];
                    }
                }
                
                $_SESSION['success'] = '¡Paquete creado exitosamente! Será revisado por un administrador antes de ser publicado.';
                header('Location: /hogartours/host/dashboard');
                exit;
            }
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error al crear el paquete: ' . $e->getMessage();
            header('Location: /hogartours/host/create-package');
            exit;
        }
    }
    
    public function editPackage() {
        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            header('Location: /hogartours/login');
            exit;
        }
        
        global $pdo;
        
        // Verificar que el anfitrión puede editar este paquete
        $stmt = $pdo->prepare('
            SELECT p.*, h.user_id 
            FROM packages p 
            JOIN hosts h ON p.host_id = h.id 
            WHERE p.id = ? AND h.user_id = ? AND h.status = "APPROVED"
        ');
        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
        $package = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$package) {
            $_SESSION['error'] = 'Paquete no encontrado o no tienes permisos para editarlo.';
            header('Location: /hogartours/host/dashboard');
            exit;
        }
        
        // Obtener imágenes del paquete
        $stmt = $pdo->prepare('SELECT * FROM package_images WHERE package_id = ? ORDER BY is_main DESC, id ASC');
        $stmt->execute([$package['id']]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require __DIR__ . '/../../templates/host/edit-package.php';
    }
}
?>

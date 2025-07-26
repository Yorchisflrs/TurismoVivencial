<?php
// src/controllers/AdminController.php
require_once __DIR__ . '/../auth/AdminMiddleware.php';
require_once __DIR__ . '/../models/Host.php';
require_once __DIR__ . '/../models/Package.php';
require_once __DIR__ . '/../models/User.php';

class AdminController {
    public function __construct() {
        AdminMiddleware::handle();
    }

    public function dashboard() {
        global $pdo;
        
        // Obtener estadísticas
        $stats = [
            'pending_hosts' => $pdo->query('SELECT COUNT(*) FROM hosts WHERE status = "PENDING"')->fetchColumn(),
            'pending_packages' => $pdo->query('SELECT COUNT(*) FROM packages WHERE status = "PENDING"')->fetchColumn(),
            'total_users' => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'approved_packages' => $pdo->query('SELECT COUNT(*) FROM packages WHERE status = "APPROVED"')->fetchColumn()
        ];
        
        require __DIR__ . '/../../templates/admin/dashboard.php';
    }
    
    public function hosts() {
        $pending_hosts = Host::getPending();
        require __DIR__ . '/../../templates/admin/hosts.php';
    }
    
    public function approveHost() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            Host::approve($id);
        }
        header('Location: /hogartours/admin/hosts');
        exit;
    }
    
    public function rejectHost() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            Host::reject($id);
        }
        header('Location: /hogartours/admin/hosts');
        exit;
    }
    
    public function packages() {
        $pending_packages = Package::getPending();
        require __DIR__ . '/../../templates/admin/packages.php';
    }
    
    public function approvePackage() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            Package::approve($id);
        }
        header('Location: /hogartours/admin/packages');
        exit;
    }
    
    public function rejectPackage() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            Package::reject($id);
        }
        header('Location: /hogartours/admin/packages');
        exit;
    }
    
    public function allPackages() {
        require __DIR__ . '/../../templates/admin/all-packages.php';
    }
    
    public function packageImages() {
        global $pdo;
        require __DIR__ . '/../../templates/admin/package-images.php';
    }
    
    public function users() {
        global $pdo;
        require __DIR__ . '/../../templates/admin/users.php';
    }
    
    public function reservations() {
        global $pdo;
        require __DIR__ . '/../../templates/admin/reservations.php';
    }
    
    public function editPackage() {
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'ID de paquete no especificado.';
            header('Location: /hogartours/admin/all-packages');
            exit;
        }
        
        global $pdo;
        $package_id = $_GET['id'];
        
        // Obtener datos del paquete
        $stmt = $pdo->prepare('
            SELECT p.*, h.full_name as host_name, h.business_name 
            FROM packages p 
            LEFT JOIN hosts h ON p.host_id = h.id 
            WHERE p.id = ?
        ');
        $stmt->execute([$package_id]);
        $package = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$package) {
            $_SESSION['error'] = 'Paquete no encontrado.';
            header('Location: /hogartours/admin/all-packages');
            exit;
        }
        
        // Obtener imágenes del paquete
        $stmt = $pdo->prepare('SELECT * FROM package_images WHERE package_id = ? ORDER BY is_main DESC, id ASC');
        $stmt->execute([$package_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require __DIR__ . '/../../templates/admin/edit-package.php';
    }
    
    public function updatePackage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['package_id'])) {
            $_SESSION['error'] = 'Método no permitido.';
            header('Location: /hogartours/admin/all-packages');
            exit;
        }
        
        global $pdo;
        $package_id = $_POST['package_id'];
        
        // Validar campos requeridos
        $required_fields = ['title', 'location', 'category', 'price', 'capacity', 'description'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = 'Por favor, completa todos los campos obligatorios.';
                header("Location: /hogartours/admin/edit-package?id=$package_id");
                exit;
            }
        }
        
        try {
            $stmt = $pdo->prepare('
                UPDATE packages 
                SET title = ?, location = ?, category = ?, price = ?, capacity = ?, description = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ');
            
            $result = $stmt->execute([
                $_POST['title'],
                $_POST['location'],
                $_POST['category'],
                $_POST['price'],
                $_POST['capacity'],
                $_POST['description'],
                $_POST['status'],
                $package_id
            ]);
            
            if ($result) {
                $_SESSION['success'] = 'Paquete actualizado exitosamente.';
            } else {
                $_SESSION['error'] = 'Error al actualizar el paquete.';
            }
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
        }
        
        header('Location: /hogartours/admin/all-packages');
        exit;
    }
    
    public function deletePackage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['package_id'])) {
            $_SESSION['error'] = 'Método no permitido.';
            header('Location: /hogartours/admin/all-packages');
            exit;
        }
        
        global $pdo;
        $package_id = $_POST['package_id'];
        
        try {
            // Primero eliminar las imágenes asociadas
            $stmt = $pdo->prepare('SELECT filename FROM package_images WHERE package_id = ?');
            $stmt->execute([$package_id]);
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Eliminar archivos físicos de imágenes
            foreach ($images as $image) {
                $image_path = __DIR__ . '/../../uploads/packages/' . $image['filename'];
                $thumb_path = __DIR__ . '/../../uploads/packages/thumbs/' . $image['filename'];
                
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                if (file_exists($thumb_path)) {
                    unlink($thumb_path);
                }
            }
            
            // Eliminar registros de imágenes
            $stmt = $pdo->prepare('DELETE FROM package_images WHERE package_id = ?');
            $stmt->execute([$package_id]);
            
            // Eliminar el paquete
            $stmt = $pdo->prepare('DELETE FROM packages WHERE id = ?');
            $result = $stmt->execute([$package_id]);
            
            if ($result) {
                $_SESSION['success'] = 'Paquete eliminado exitosamente.';
            } else {
                $_SESSION['error'] = 'Error al eliminar el paquete.';
            }
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error al eliminar: ' . $e->getMessage();
        }
        
        header('Location: /hogartours/admin/all-packages');
        exit;
    }
}

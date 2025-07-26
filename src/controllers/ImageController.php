<?php
// src/controllers/ImageController.php

class ImageController {
    private $packageImageModel;
    
    public function __construct() {
        // Limpiar todos los buffers existentes
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Iniciar nuevo buffer
        ob_start();
        
        global $pdo;
        
        if (!class_exists('PackageImage')) {
            require_once __DIR__ . '/../models/PackageImage.php';
        }
        if (!class_exists('ImageHelper')) {
            require_once __DIR__ . '/../helpers/ImageHelper.php';
        }
        
        $this->packageImageModel = new PackageImage($pdo);
        
        // Limpiar cualquier output generado
        ob_clean();
    }
    
    /**
     * Subir imágenes a un paquete
     */
    public function uploadImages() {
        // Limpiar cualquier output previo inmediatamente
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Configurar headers JSON lo antes posible
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
        }
        
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }
        
        $package_id = $_POST['package_id'] ?? null;
        
        if (!$package_id) {
            echo json_encode(['success' => false, 'error' => 'ID de paquete requerido']);
            exit;
        }
        
        // Verificar que el usuario tiene permisos para este paquete
        if (!$this->canManagePackage($package_id)) {
            echo json_encode(['success' => false, 'error' => 'Sin permisos para este paquete']);
            exit;
        }
        
        if (!isset($_FILES['images'])) {
            echo json_encode(['success' => false, 'error' => 'No se enviaron archivos']);
            exit;
        }
        
        $files = $_FILES['images'];
        
        // Validar archivos
        $validation_errors = ImageHelper::validateMultipleFiles($files);
        if (!empty($validation_errors)) {
            echo json_encode(['success' => false, 'error' => implode(', ', $validation_errors)]);
            exit;
        }
        
        try {
            $results = ImageHelper::uploadMultipleImages($files, $package_id);
            $uploaded_images = [];
            $errors = [];
            
            foreach ($results as $i => $result) {
                if ($result['success']) {
                    // Guardar en base de datos
                    $caption = $_POST['captions'][$i] ?? '';
                    $is_main = isset($_POST['main_image']) && $_POST['main_image'] == $i;
                    
                    $image_id = $this->packageImageModel->create($package_id, $result['filename'], $is_main, $caption);
                    
                    $uploaded_images[] = [
                        'id' => $image_id,
                        'filename' => $result['filename'],
                        'url' => ImageHelper::getImageUrl($result['filename']),
                        'thumb_url' => ImageHelper::getThumbUrl($result['filename']),
                        'is_main' => $is_main,
                        'caption' => $caption
                    ];
                } else {
                    $errors[] = $result['error'];
                }
            }
            
            $response = ['success' => true, 'images' => $uploaded_images];
            if (!empty($errors)) {
                $response['warnings'] = $errors;
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    /**
     * Eliminar imagen
     */
    public function deleteImage() {
        // Limpiar output y configurar headers
        while (ob_get_level()) { ob_end_clean(); }
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }
        
        $image_id = $_POST['image_id'] ?? null;
        
        if (!$image_id) {
            echo json_encode(['success' => false, 'error' => 'ID de imagen requerido']);
            exit;
        }
        
        try {
            // Verificar permisos
            global $pdo;
            $sql = "SELECT pi.*, p.host_id FROM package_images pi 
                    JOIN packages p ON pi.package_id = p.id 
                    WHERE pi.id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$image_id]);
            $image_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$image_data) {
                echo json_encode(['success' => false, 'error' => 'Imagen no encontrada']);
                exit;
            }
            
            if (!$this->canManagePackage($image_data['package_id'])) {
                echo json_encode(['success' => false, 'error' => 'Sin permisos']);
                exit;
            }
            
            $this->packageImageModel->delete($image_id);
            echo json_encode(['success' => true, 'message' => 'Imagen eliminada']);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    /**
     * Establecer imagen como principal
     */
    public function setMainImage() {
        // Limpiar output y configurar headers
        while (ob_get_level()) { ob_end_clean(); }
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }
        
        $image_id = $_POST['image_id'] ?? null;
        $package_id = $_POST['package_id'] ?? null;
        
        if (!$image_id || !$package_id) {
            echo json_encode(['success' => false, 'error' => 'Datos requeridos']);
            exit;
        }
        
        if (!$this->canManagePackage($package_id)) {
            echo json_encode(['success' => false, 'error' => 'Sin permisos']);
            exit;
        }
        
        try {
            $this->packageImageModel->setAsMain($image_id, $package_id);
            echo json_encode(['success' => true, 'message' => 'Imagen principal actualizada']);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    /**
     * Actualizar caption de imagen
     */
    public function updateCaption() {
        // Limpiar output y configurar headers
        while (ob_get_level()) { ob_end_clean(); }
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }
        
        $image_id = $_POST['image_id'] ?? null;
        $caption = $_POST['caption'] ?? '';
        
        if (!$image_id) {
            echo json_encode(['success' => false, 'error' => 'ID de imagen requerido']);
            exit;
        }
        
        try {
            // Verificar permisos
            global $pdo;
            $sql = "SELECT pi.package_id FROM package_images pi WHERE pi.id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$image_id]);
            $package_id = $stmt->fetchColumn();
            
            if (!$package_id || !$this->canManagePackage($package_id)) {
                echo json_encode(['success' => false, 'error' => 'Sin permisos']);
                exit;
            }
            
            $this->packageImageModel->updateCaption($image_id, $caption);
            echo json_encode(['success' => true, 'message' => 'Caption actualizado']);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    /**
     * Obtener imágenes de un paquete
     */
    public function getPackageImages() {
        $package_id = $_GET['package_id'] ?? null;
        
        if (!$package_id) {
            $this->jsonResponse(['success' => false, 'error' => 'ID de paquete requerido']);
            return;
        }
        
        try {
            $images = $this->packageImageModel->getByPackageId($package_id);
            
            // Agregar URLs completas
            foreach ($images as &$image) {
                $image['url'] = ImageHelper::getImageUrl($image['filename']);
                $image['thumb_url'] = ImageHelper::getThumbUrl($image['filename']);
            }
            
            $this->jsonResponse(['success' => true, 'images' => $images]);
            
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Verificar si el usuario puede manejar un paquete
     */
    private function canManagePackage($package_id) {
        global $pdo;
        
        // Los admins pueden manejar cualquier paquete
        if (isAdmin()) {
            return true;
        }
        
        // Los hosts solo pueden manejar sus propios paquetes
        if (isHost()) {
            $sql = "SELECT h.id FROM hosts h 
                    JOIN packages p ON h.id = p.host_id 
                    WHERE p.id = ? AND h.email = (
                        SELECT email FROM users WHERE id = ?
                    )";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$package_id, $_SESSION['user_id']]);
            return $stmt->fetchColumn() !== false;
        }
        
        return false;
    }
    
    /**
     * Enviar respuesta JSON
     */
    private function jsonResponse($data) {
        // Limpiar todos los buffers de salida
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Verificar si los headers ya fueron enviados
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
        }
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>

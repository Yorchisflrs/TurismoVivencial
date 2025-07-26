<?php
// src/models/PackageImage.php

class PackageImage {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Crear nueva imagen de paquete
     */
    public function create($package_id, $filename, $is_main = false, $caption = '') {
        try {
            // Si es imagen principal, remover flag de otras imágenes
            if ($is_main) {
                $this->clearMainImage($package_id);
            }
            
            $sql = "INSERT INTO package_images (package_id, filename, is_main, caption, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$package_id, $filename, $is_main ? 1 : 0, $caption]);
            
            return $this->pdo->lastInsertId();
            
        } catch (PDOException $e) {
            throw new Exception("Error al crear imagen: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener todas las imágenes de un paquete
     */
    public function getByPackageId($package_id) {
        try {
            $sql = "SELECT * FROM package_images 
                    WHERE package_id = ? 
                    ORDER BY is_main DESC, created_at ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$package_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener imágenes: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener imagen principal de un paquete
     */
    public function getMainImage($package_id) {
        try {
            $sql = "SELECT * FROM package_images 
                    WHERE package_id = ? AND is_main = 1 
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$package_id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener imagen principal: " . $e->getMessage());
        }
    }
    
    /**
     * Establecer imagen como principal
     */
    public function setAsMain($image_id, $package_id) {
        try {
            $this->pdo->beginTransaction();
            
            // Quitar flag principal de todas las imágenes del paquete
            $sql1 = "UPDATE package_images SET is_main = 0 WHERE package_id = ?";
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt1->execute([$package_id]);
            
            // Establecer nueva imagen principal
            $sql2 = "UPDATE package_images SET is_main = 1 WHERE id = ? AND package_id = ?";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->execute([$image_id, $package_id]);
            
            $this->pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception("Error al establecer imagen principal: " . $e->getMessage());
        }
    }
    
    /**
     * Eliminar imagen
     */
    public function delete($image_id) {
        try {
            // Obtener información de la imagen antes de eliminar
            $sql = "SELECT * FROM package_images WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$image_id]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$image) {
                throw new Exception("Imagen no encontrada");
            }
            
            // Eliminar de base de datos
            $sql = "DELETE FROM package_images WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$image_id]);
            
            // Eliminar archivos físicos
            require_once __DIR__ . '/../helpers/ImageHelper.php';
            ImageHelper::deleteImage($image['filename']);
            
            return true;
            
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar imagen: " . $e->getMessage());
        }
    }
    
    /**
     * Actualizar caption de imagen
     */
    public function updateCaption($image_id, $caption) {
        try {
            $sql = "UPDATE package_images SET caption = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$caption, $image_id]);
            
            return true;
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar caption: " . $e->getMessage());
        }
    }
    
    /**
     * Limpiar flag de imagen principal
     */
    private function clearMainImage($package_id) {
        $sql = "UPDATE package_images SET is_main = 0 WHERE package_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$package_id]);
    }
    
    /**
     * Contar imágenes de un paquete
     */
    public function countByPackageId($package_id) {
        try {
            $sql = "SELECT COUNT(*) FROM package_images WHERE package_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$package_id]);
            
            return $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            throw new Exception("Error al contar imágenes: " . $e->getMessage());
        }
    }
    
    /**
     * Eliminar todas las imágenes de un paquete
     */
    public function deleteAllByPackageId($package_id) {
        try {
            // Obtener todas las imágenes
            $images = $this->getByPackageId($package_id);
            
            // Eliminar archivos físicos
            require_once __DIR__ . '/../helpers/ImageHelper.php';
            foreach ($images as $image) {
                ImageHelper::deleteImage($image['filename']);
            }
            
            // Eliminar de base de datos
            $sql = "DELETE FROM package_images WHERE package_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$package_id]);
            
            return true;
            
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar todas las imágenes: " . $e->getMessage());
        }
    }
}
?>

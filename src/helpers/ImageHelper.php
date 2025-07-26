<?php
// src/helpers/ImageHelper.php

class ImageHelper {
    
    private static $upload_path = 'uploads/packages/';
    private static $thumb_path = 'uploads/packages/thumbs/';
    private static $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    private static $max_file_size = 5 * 1024 * 1024; // 5MB
    
    /**
     * Subir y procesar una imagen
     */
    public static function uploadImage($file, $package_id) {
        try {
            // Validar archivo
            if (!self::validateFile($file)) {
                return ['success' => false, 'error' => 'Archivo no válido'];
            }
            
            // Generar nombre único
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'package_' . $package_id . '_' . uniqid() . '.' . $file_extension;
            
            // Rutas completas
            $upload_full_path = __DIR__ . '/../../' . self::$upload_path;
            $thumb_full_path = __DIR__ . '/../../' . self::$thumb_path;
            
            // Crear directorios si no existen
            if (!is_dir($upload_full_path)) {
                mkdir($upload_full_path, 0777, true);
            }
            if (!is_dir($thumb_full_path)) {
                mkdir($thumb_full_path, 0777, true);
            }
            
            $target_file = $upload_full_path . $filename;
            $thumb_file = $thumb_full_path . $filename;
            
            // Mover archivo original
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                
                // Crear thumbnail
                self::createThumbnail($target_file, $thumb_file, 400, 300);
                
                // Redimensionar imagen original si es muy grande
                self::resizeImage($target_file, 1200, 800);
                
                return [
                    'success' => true,
                    'filename' => $filename,
                    'path' => self::$upload_path . $filename,
                    'thumb_path' => self::$thumb_path . $filename
                ];
                
            } else {
                return ['success' => false, 'error' => 'Error al subir el archivo'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validar archivo de imagen
     */
    private static function validateFile($file) {
        // Verificar errores de upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Verificar tamaño
        if ($file['size'] > self::$max_file_size) {
            return false;
        }
        
        // Verificar tipo MIME
        if (!in_array($file['type'], self::$allowed_types)) {
            return false;
        }
        
        // Verificar que realmente es una imagen
        $image_info = getimagesize($file['tmp_name']);
        if ($image_info === false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Crear thumbnail de imagen
     */
    private static function createThumbnail($source, $destination, $width, $height) {
        $image_info = getimagesize($source);
        $image_type = $image_info[2];
        
        // Crear imagen desde archivo
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($source);
                break;
            default:
                return false;
        }
        
        if (!$image) return false;
        
        // Dimensiones originales
        $original_width = imagesx($image);
        $original_height = imagesy($image);
        
        // Calcular nuevas dimensiones manteniendo proporción
        $ratio = min($width / $original_width, $height / $original_height);
        $new_width = $original_width * $ratio;
        $new_height = $original_height * $ratio;
        
        // Crear nueva imagen
        $new_image = imagecreatetruecolor($width, $height);
        
        // Preservar transparencia para PNG
        if ($image_type == IMAGETYPE_PNG) {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefill($new_image, 0, 0, $transparent);
        }
        
        // Centrar imagen
        $dst_x = ($width - $new_width) / 2;
        $dst_y = ($height - $new_height) / 2;
        
        // Redimensionar
        imagecopyresampled(
            $new_image, $image,
            $dst_x, $dst_y, 0, 0,
            $new_width, $new_height,
            $original_width, $original_height
        );
        
        // Guardar thumbnail
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($new_image, $destination, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($new_image, $destination, 8);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($new_image, $destination, 90);
                break;
        }
        
        // Limpiar memoria
        imagedestroy($image);
        imagedestroy($new_image);
        
        return true;
    }
    
    /**
     * Redimensionar imagen si es muy grande
     */
    private static function resizeImage($source, $max_width, $max_height) {
        $image_info = getimagesize($source);
        $original_width = $image_info[0];
        $original_height = $image_info[1];
        
        // Si la imagen ya es pequeña, no hacer nada
        if ($original_width <= $max_width && $original_height <= $max_height) {
            return true;
        }
        
        $image_type = $image_info[2];
        
        // Crear imagen desde archivo
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($source);
                break;
            default:
                return false;
        }
        
        if (!$image) return false;
        
        // Calcular nuevas dimensiones
        $ratio = min($max_width / $original_width, $max_height / $original_height);
        $new_width = $original_width * $ratio;
        $new_height = $original_height * $ratio;
        
        // Crear nueva imagen
        $new_image = imagecreatetruecolor($new_width, $new_height);
        
        // Preservar transparencia para PNG
        if ($image_type == IMAGETYPE_PNG) {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefill($new_image, 0, 0, $transparent);
        }
        
        // Redimensionar
        imagecopyresampled(
            $new_image, $image,
            0, 0, 0, 0,
            $new_width, $new_height,
            $original_width, $original_height
        );
        
        // Guardar imagen redimensionada
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($new_image, $source, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($new_image, $source, 8);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($new_image, $source, 90);
                break;
        }
        
        // Limpiar memoria
        imagedestroy($image);
        imagedestroy($new_image);
        
        return true;
    }
    
    /**
     * Eliminar imagen y su thumbnail
     */
    public static function deleteImage($filename) {
        $upload_path = __DIR__ . '/../../' . self::$upload_path . $filename;
        $thumb_path = __DIR__ . '/../../' . self::$thumb_path . $filename;
        
        $deleted = true;
        
        if (file_exists($upload_path)) {
            $deleted = unlink($upload_path) && $deleted;
        }
        
        if (file_exists($thumb_path)) {
            $deleted = unlink($thumb_path) && $deleted;
        }
        
        return $deleted;
    }
    
    /**
     * Obtener URL pública de imagen
     */
    public static function getImageUrl($filename) {
        return '/hogartours/' . self::$upload_path . $filename;
    }
    
    /**
     * Obtener URL pública de thumbnail
     */
    public static function getThumbUrl($filename) {
        return '/hogartours/' . self::$thumb_path . $filename;
    }
    
    /**
     * Validar múltiples archivos
     */
    public static function validateMultipleFiles($files) {
        $errors = [];
        $max_files = 10;
        
        if (count($files['name']) > $max_files) {
            $errors[] = "Máximo {$max_files} archivos permitidos";
            return $errors;
        }
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue; // Skip empty files
            }
            
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            if (!self::validateFile($file)) {
                $errors[] = "Archivo '{$file['name']}' no es válido";
            }
        }
        
        return $errors;
    }
    
    /**
     * Subir múltiples imágenes
     */
    public static function uploadMultipleImages($files, $package_id) {
        $results = [];
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            $result = self::uploadImage($file, $package_id);
            $results[] = $result;
        }
        
        return $results;
    }
}
?>

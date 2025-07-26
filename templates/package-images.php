<?php
// Verificar que el usuario esté autenticado y sea anfitrión o admin
if (!isset($_SESSION['user_id']) || (!isHost() && !isAdmin())) {
    header('Location: /hogartours/login');
    exit;
}

$package_id = $_GET['package_id'] ?? null;
if (!$package_id) {
    header('Location: /hogartours/');
    exit;
}

// Verificar que el paquete existe y el usuario tiene permisos
global $pdo;
if (isAdmin()) {
    $sql = "SELECT * FROM packages WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$package_id]);
} else {
    $sql = "SELECT p.* FROM packages p 
            JOIN hosts h ON p.host_id = h.id 
            WHERE p.id = ? AND h.email = (
                SELECT email FROM users WHERE id = ?
            )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$package_id, $_SESSION['user_id']]);
}

$package = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$package) {
    header('Location: /hogartours/');
    exit;
}

// Obtener imágenes existentes
require_once __DIR__ . '/../src/models/PackageImage.php';
require_once __DIR__ . '/../src/helpers/ImageHelper.php';

$packageImageModel = new PackageImage($pdo);
$existing_images = $packageImageModel->getByPackageId($package_id);

// Agregar URLs a las imágenes
foreach ($existing_images as &$image) {
    $image['url'] = ImageHelper::getImageUrl($image['filename']);
    $image['thumb_url'] = ImageHelper::getThumbUrl($image['filename']);
}
?>

<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/hogartours/" class="text-decoration-none">Inicio</a>
                    </li>
                    <?php if (isAdmin()): ?>
                        <li class="breadcrumb-item">
                            <a href="/hogartours/admin/dashboard" class="text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="/hogartours/admin/packages" class="text-decoration-none">Paquetes</a>
                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a href="/hogartours/host/dashboard" class="text-decoration-none">Mi Dashboard</a>
                        </li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active">Gestionar Imágenes</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Gestionar Imágenes</h1>
                    <p class="text-muted mb-0">
                        <strong><?= htmlspecialchars($package['title']) ?></strong>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-<?= $package['status'] === 'approved' ? 'success' : ($package['status'] === 'pending' ? 'warning' : 'danger') ?>">
                        <?= ucfirst($package['status']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert para mensajes -->
    <div id="alertContainer"></div>

    <!-- Upload Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cloud-upload-alt me-2"></i>
                        Subir Nuevas Imágenes
                    </h5>
                </div>
                <div class="card-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="hidden" name="package_id" value="<?= $package_id ?>">
                        
                        <div class="mb-3">
                            <label for="images" class="form-label">
                                Seleccionar Imágenes 
                                <small class="text-muted">(Máximo 10 archivos, 5MB cada uno)</small>
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="images" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/jpeg,image/jpg,image/png,image/webp"
                                   required>
                            <div class="form-text">
                                Formatos permitidos: JPG, PNG, WebP. Recomendamos imágenes de alta calidad que muestren la experiencia auténtica.
                            </div>
                        </div>
                        
                        <!-- Preview de imágenes seleccionadas -->
                        <div id="imagePreview" class="row g-3 mb-3" style="display: none;"></div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="fas fa-upload me-2"></i>
                                Subir Imágenes
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                                <i class="fas fa-times me-2"></i>
                                Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Images -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-images me-2"></i>
                        Imágenes Actuales (<?= count($existing_images) ?>)
                    </h5>
                    <?php if (empty($existing_images)): ?>
                        <span class="badge bg-warning">Sin imágenes</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($existing_images)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay imágenes</h5>
                            <p class="text-muted">
                                Sube algunas imágenes atractivas para mostrar tu experiencia.
                                Las imágenes de calidad aumentan las reservas significativamente.
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3" id="existingImages">
                            <?php foreach ($existing_images as $image): ?>
                                <div class="col-lg-3 col-md-4 col-sm-6" id="image-<?= $image['id'] ?>">
                                    <div class="card h-100 image-card">
                                        <!-- Imagen -->
                                        <div class="position-relative">
                                            <img src="<?= htmlspecialchars($image['thumb_url']) ?>" 
                                                 class="card-img-top" 
                                                 style="height: 200px; object-fit: cover;" 
                                                 alt="<?= htmlspecialchars($image['caption']) ?>"
                                                 onclick="viewImage('<?= htmlspecialchars($image['url']) ?>')">
                                            
                                            <!-- Badge principal -->
                                            <?php if ($image['is_main']): ?>
                                                <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                                    <i class="fas fa-star me-1"></i>Principal
                                                </span>
                                            <?php endif; ?>
                                            
                                            <!-- Botones de acción -->
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <div class="btn-group-vertical" role="group">
                                                    <?php if (!$image['is_main']): ?>
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="setMainImage(<?= $image['id'] ?>, <?= $package_id ?>)"
                                                                title="Establecer como principal">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="deleteImage(<?= $image['id'] ?>)"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Caption editable -->
                                        <div class="card-body p-2">
                                            <textarea class="form-control form-control-sm" 
                                                      rows="2" 
                                                      placeholder="Descripción de la imagen..."
                                                      onblur="updateCaption(<?= $image['id'] ?>, this.value)"
                                                      data-image-id="<?= $image['id'] ?>"><?= htmlspecialchars($image['caption']) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Consejos -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Consejos para Mejores Imágenes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">📸 <strong>Calidad alta:</strong> Usa imágenes nítidas y bien iluminadas</li>
                                <li class="mb-2">🌅 <strong>Luz natural:</strong> Fotografía durante el día con buena luz</li>
                                <li class="mb-2">👥 <strong>Incluye personas:</strong> Muestra la interacción con los huéspedes</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">🎯 <strong>Actividades:</strong> Captura momentos de la experiencia</li>
                                <li class="mb-2">🏔️ <strong>Paisajes:</strong> Destaca la belleza del altiplano</li>
                                <li class="mb-2">🍽️ <strong>Detalles:</strong> Comida, textiles, artesanías típicas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista de imagen -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista de Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<style>
.image-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.image-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.image-card img {
    cursor: pointer;
}

#imagePreview .preview-item {
    position: relative;
}

#imagePreview .remove-preview {
    position: absolute;
    top: 5px;
    right: 5px;
    z-index: 10;
}
</style>

<script>
// Preview de imágenes seleccionadas
document.getElementById('images').addEventListener('change', function(e) {
    const files = e.target.files;
    const preview = document.getElementById('imagePreview');
    
    if (files.length === 0) {
        preview.style.display = 'none';
        return;
    }
    
    preview.innerHTML = '';
    preview.style.display = 'flex';
    
    Array.from(files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-lg-2 col-md-3 col-4';
                col.innerHTML = `
                    <div class="preview-item">
                        <img src="${e.target.result}" class="img-fluid rounded" style="height: 120px; object-fit: cover; width: 100%;">
                        <button type="button" class="btn btn-sm btn-danger remove-preview" onclick="removePreview(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="mt-1">
                            <input type="text" class="form-control form-control-sm" 
                                   name="captions[]" placeholder="Descripción...">
                        </div>
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="radio" name="main_image" value="${index}">
                            <label class="form-check-label small">Principal</label>
                        </div>
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Upload form
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const uploadBtn = document.getElementById('uploadBtn');
    
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subiendo...';
    
    fetch('/hogartours/upload_images_direct.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Imágenes subidas exitosamente', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error: ' + data.error, 'danger');
        }
    })
    .catch(error => {
        showAlert('Error de conexión: ' + error.message, 'danger');
    })
    .finally(() => {
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Subir Imágenes';
    });
});

// Funciones auxiliares
function clearSelection() {
    document.getElementById('images').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('imagePreview').innerHTML = '';
}

function removePreview(index) {
    // Esta función necesitaría reimplementarse para manejar la eliminación del preview
    showAlert('Función en desarrollo', 'info');
}

function viewImage(url) {
    document.getElementById('modalImage').src = url;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function setMainImage(imageId, packageId) {
    if (!confirm('¿Establecer esta imagen como principal?')) return;
    
    fetch('/hogartours/api/images/set-main', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `image_id=${imageId}&package_id=${packageId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Imagen principal actualizada', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Error: ' + data.error, 'danger');
        }
    });
}

function deleteImage(imageId) {
    if (!confirm('¿Estás seguro de eliminar esta imagen? Esta acción no se puede deshacer.')) return;
    
    fetch('/hogartours/api/images/delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `image_id=${imageId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`image-${imageId}`).remove();
            showAlert('Imagen eliminada', 'success');
        } else {
            showAlert('Error: ' + data.error, 'danger');
        }
    });
}

function updateCaption(imageId, caption) {
    fetch('/hogartours/api/images/update-caption', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `image_id=${imageId}&caption=${encodeURIComponent(caption)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Descripción actualizada', 'success', 2000);
        } else {
            showAlert('Error: ' + data.error, 'danger');
        }
    });
}

function showAlert(message, type, duration = 5000) {
    const alertContainer = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    alertContainer.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, duration);
}
</script>

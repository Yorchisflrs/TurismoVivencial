<?php
$page_title = 'Gestión de Imágenes';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Gestión de Imágenes</h1>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Actualizar
                    </button>
                    <a href="/hogartours/admin" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de imágenes -->
    <div class="row mb-4">
        <?php
        $total_images = $pdo->query("SELECT COUNT(*) FROM package_images")->fetchColumn();
        $approved_images = $pdo->query("SELECT COUNT(*) FROM package_images WHERE approved = 1")->fetchColumn();
        $pending_images = $total_images - $approved_images;
        $packages_with_images = $pdo->query("SELECT COUNT(DISTINCT package_id) FROM package_images")->fetchColumn();
        ?>
        
        <div class="col-md-3 mb-3">
            <div class="card card-stats text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><?php echo $total_images; ?></h5>
                            <p class="card-text">Total Imágenes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-images fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background-color: #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><?php echo $approved_images; ?></h5>
                            <p class="card-text">Aprobadas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background-color: #ffc107; color: #000 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><?php echo $pending_images; ?></h5>
                            <p class="card-text">Pendientes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background-color: #17a2b8;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><?php echo $packages_with_images; ?></h5>
                            <p class="card-text">Paquetes con Imágenes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-box fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de imágenes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Todas las Imágenes</h5>
                </div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT pi.*, p.title as package_title 
                            FROM package_images pi 
                            LEFT JOIN packages p ON pi.package_id = p.id 
                            ORDER BY pi.created_at DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <?php if (empty($images)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-images fs-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay imágenes registradas</h5>
                            <p class="text-muted">Las imágenes aparecerán aquí cuando los anfitriones suban contenido.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vista Previa</th>
                                        <th>Paquete</th>
                                        <th>Archivo</th>
                                        <th>Estado</th>
                                        <th>Principal</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($images as $image): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($image['url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($image['url']); ?>" 
                                                         alt="Preview" class="image-thumbnail" 
                                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yNSAzNUMzMC41MjI4IDM1IDM1IDMwLjUyMjggMzUgMjVDMzUgMTkuNDc3MiAzMC41MjI4IDE1IDI1IDE1QzE5LjQ3NzIgMTUgMTUgMTkuNDc3MiAxNSAyNUMxNSAzMC41MjI4IDE5LjQ3NzIgMzUgMjUgMzVaIiBmaWxsPSIjOTlBM0FFIi8+CjwvcmVnPgo='">
                                                <?php else: ?>
                                                    <div class="image-thumbnail bg-light d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($image['package_title'] ?? 'Sin paquete'); ?></strong>
                                            </td>
                                            <td>
                                                <code><?php echo htmlspecialchars($image['filename'] ?? 'N/A'); ?></code>
                                            </td>
                                            <td>
                                                <?php if ($image['approved']): ?>
                                                    <span class="badge bg-success">Aprobada</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pendiente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($image['is_main']): ?>
                                                    <i class="bi bi-star-fill text-warning" title="Imagen principal"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-star text-muted" title="Imagen secundaria"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', strtotime($image['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <?php if (!$image['approved']): ?>
                                                        <button class="btn btn-outline-success" onclick="approveImage(<?php echo $image['id']; ?>)">
                                                            <i class="bi bi-check"></i> Aprobar
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-outline-danger" onclick="deleteImage(<?php echo $image['id']; ?>)">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function approveImage(imageId) {
    if (confirm('¿Aprobar esta imagen?')) {
        // Implementar llamada AJAX para aprobar imagen
        console.log('Aprobar imagen ID:', imageId);
        // Por ahora solo recarga la página
        location.reload();
    }
}

function deleteImage(imageId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
        // Implementar llamada AJAX para eliminar imagen
        console.log('Eliminar imagen ID:', imageId);
        // Por ahora solo recarga la página
        location.reload();
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

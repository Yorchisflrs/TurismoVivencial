<?php require_once __DIR__ . '/../layouts/admin.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-0">Editar Paquete</h2>
                    <p class="text-muted mb-0">Modificar información del paquete</p>
                </div>
                <a href="/hogartours/admin/all-packages" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Paquetes
                </a>
            </div>

            <!-- Mensajes -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="row">
                <!-- Formulario de edición -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-edit me-2"></i>Información del Paquete
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="/hogartours/admin/update-package" method="POST">
                                <input type="hidden" name="package_id" value="<?= $package['id'] ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Título del Paquete *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="title" 
                                               value="<?= htmlspecialchars($package['title']) ?>" 
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Ubicación *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="location" 
                                               value="<?= htmlspecialchars($package['location']) ?>" 
                                               required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Categoría *</label>
                                        <select class="form-select" name="category" required>
                                            <option value="">Seleccionar categoría</option>
                                            <option value="aventura" <?= $package['category'] === 'aventura' ? 'selected' : '' ?>>Aventura</option>
                                            <option value="cultura" <?= $package['category'] === 'cultura' ? 'selected' : '' ?>>Cultura</option>
                                            <option value="gastronomia" <?= $package['category'] === 'gastronomia' ? 'selected' : '' ?>>Gastronomía</option>
                                            <option value="textiles" <?= $package['category'] === 'textiles' ? 'selected' : '' ?>>Textiles</option>
                                            <option value="agricultura" <?= $package['category'] === 'agricultura' ? 'selected' : '' ?>>Agricultura</option>
                                            <option value="pesca" <?= $package['category'] === 'pesca' ? 'selected' : '' ?>>Pesca</option>
                                            <option value="naturaleza" <?= $package['category'] === 'naturaleza' ? 'selected' : '' ?>>Naturaleza</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Estado *</label>
                                        <select class="form-select" name="status" required>
                                            <option value="PENDING" <?= $package['status'] === 'PENDING' ? 'selected' : '' ?>>Pendiente</option>
                                            <option value="APPROVED" <?= $package['status'] === 'APPROVED' ? 'selected' : '' ?>>Aprobado</option>
                                            <option value="REJECTED" <?= $package['status'] === 'REJECTED' ? 'selected' : '' ?>>Rechazado</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Precio (S/) *</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="price" 
                                               value="<?= $package['price'] ?>" 
                                               min="0" 
                                               step="0.01" 
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Capacidad máxima *</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="capacity" 
                                               value="<?= $package['capacity'] ?>" 
                                               min="1" 
                                               required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Descripción *</label>
                                    <textarea class="form-control" 
                                              name="description" 
                                              rows="4" 
                                              required><?= htmlspecialchars($package['description']) ?></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                    <a href="/hogartours/admin/all-packages" class="btn btn-outline-secondary">
                                        Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Panel lateral con información -->
                <div class="col-lg-4">
                    <!-- Información del anfitrión -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-user me-2"></i>Información del Anfitrión
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Nombre:</strong><br>
                                <?= htmlspecialchars($package['host_name'] ?: 'No especificado') ?>
                            </p>
                            <p class="mb-0">
                                <strong>Negocio:</strong><br>
                                <?= htmlspecialchars($package['business_name'] ?: 'No especificado') ?>
                            </p>
                        </div>
                    </div>

                    <!-- Imágenes del paquete -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-images me-2"></i>Imágenes (<?= count($images) ?>)
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($images)): ?>
                                <div class="row g-2">
                                    <?php foreach ($images as $image): ?>
                                        <div class="col-6">
                                            <div class="position-relative">
                                                <?php 
                                                $image_path = "/hogartours/uploads/packages/thumbs/{$image['filename']}";
                                                ?>
                                                <img src="<?= $image_path ?>" 
                                                     class="img-fluid rounded" 
                                                     alt="<?= htmlspecialchars($image['caption']) ?>"
                                                     style="height: 80px; width: 100%; object-fit: cover;">
                                                <?php if ($image['is_main']): ?>
                                                    <span class="badge bg-success position-absolute top-0 end-0 m-1">
                                                        <i class="fas fa-star"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No hay imágenes subidas</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Acciones de administrador -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-danger text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Zona de Peligro
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Esta acción eliminará permanentemente el paquete y todas sus imágenes.
                            </p>
                            <form action="/hogartours/admin/delete-package" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar este paquete? Esta acción no se puede deshacer.')">
                                <input type="hidden" name="package_id" value="<?= $package['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-trash me-2"></i>Eliminar Paquete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

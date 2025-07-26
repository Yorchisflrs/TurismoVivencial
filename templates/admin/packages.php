<?php
$title = 'Gestión de Paquetes';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestión de Paquetes</h1>
    <a href="/hogartours/admin/dashboard" class="btn btn-secondary">← Volver al Dashboard</a>
</div>

<?php if (empty($pending_packages)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No hay paquetes pendientes de aprobación.
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($pending_packages as $package): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?= htmlspecialchars($package['title']) ?></h5>
                    <small class="text-muted">Por: <?= htmlspecialchars($package['host_name']) ?></small>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <strong>Ubicación:</strong> <?= htmlspecialchars($package['location']) ?><br>
                        <strong>Categoría:</strong> <?= htmlspecialchars($package['category']) ?><br>
                        <strong>Precio:</strong> S/ <?= number_format($package['price'], 0) ?><br>
                        <strong>Capacidad:</strong> <?= $package['max_participants'] ?> personas
                    </p>
                    <p class="card-text"><?= htmlspecialchars(substr($package['description'], 0, 100)) ?>...</p>
                    <small class="text-muted">Creado: <?= date('d/m/Y', strtotime($package['created_at'])) ?></small>
                </div>
                <div class="card-footer">
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="id" value="<?= $package['id'] ?>">
                        <button type="submit" formaction="/hogartours/admin/approve-package" 
                                class="btn btn-success btn-sm flex-fill" 
                                onclick="return confirm('¿Aprobar este paquete?')">
                            ✓ Aprobar
                        </button>
                        <button type="submit" formaction="/hogartours/admin/reject-package" 
                                class="btn btn-danger btn-sm flex-fill" 
                                onclick="return confirm('¿Rechazar este paquete?')">
                            ✗ Rechazar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

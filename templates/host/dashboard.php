<?php
$title = 'Dashboard del Anfitrión';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0 bg-dark">
            <div class="d-flex flex-column min-vh-100">
                <div class="bg-dark text-white p-3">
                    <h5 class="mb-0">🏔️ Panel Anfitrión</h5>
                    <small>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></small>
                </div>
                <nav class="nav flex-column p-3">
                    <a class="nav-link text-white active" href="/hogartours/host/dashboard">
                        <i class="fas fa-chart-line me-2"></i> Dashboard
                    </a>
                    <a class="nav-link text-white" href="/hogartours/host/create-package">
                        <i class="fas fa-plus-circle me-2"></i> Crear Experiencia
                    </a>
                    <a class="nav-link text-white" href="/hogartours/">
                        <i class="fas fa-home me-2"></i> Ver Sitio Web
                    </a>
                    <a class="nav-link text-white" href="/hogartours/logout">
                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Dashboard del Anfitrión</h1>
                    <a href="/hogartours/host/create-package" class="btn btn-success">
                        <i class="fas fa-plus"></i> Nueva Experiencia
                    </a>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5>Total Experiencias</h5>
                                        <h2><?= $stats['total_packages'] ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-map-marked-alt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5>Pendientes</h5>
                                        <h2><?= $stats['pending_packages'] ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5>Aprobadas</h5>
                                        <h2><?= $stats['approved_packages'] ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Experiencias -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Mis Experiencias</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($packages)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-map-marked-alt fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Aún no tienes experiencias</h5>
                                <p class="text-muted">Comienza creando tu primera experiencia para compartir con viajeros.</p>
                                <a href="/hogartours/host/create-package" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Primera Experiencia
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Título</th>
                                            <th>Ubicación</th>
                                            <th>Precio</th>
                                            <th>Estado</th>
                                            <th>Imágenes</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($packages as $package): ?>
                                        <tr>
                                            <td>
                                                <?php if ($package['image_count'] > 0): ?>
                                                    <img src="/hogartours/uploads/packages/<?= $package['id'] ?>/thumb_1.jpg" 
                                                         alt="<?= htmlspecialchars($package['title']) ?>" 
                                                         class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($package['title']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars(substr($package['description'], 0, 50)) ?>...</small>
                                            </td>
                                            <td><?= htmlspecialchars($package['location']) ?></td>
                                            <td>
                                                <strong>S/ <?= number_format($package['price'], 2) ?></strong><br>
                                                <small class="text-muted">Hasta <?= $package['capacity'] ?> personas</small>
                                            </td>
                                            <td>
                                                <?php 
                                                $statusClass = match($package['status']) {
                                                    'APPROVED' => 'bg-success',
                                                    'PENDING' => 'bg-warning',
                                                    'REJECTED' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                $statusText = match($package['status']) {
                                                    'APPROVED' => 'Aprobada',
                                                    'PENDING' => 'Pendiente',
                                                    'REJECTED' => 'Rechazada',
                                                    default => $package['status']
                                                };
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= $package['image_count'] ?> imágenes</span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($package['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="/hogartours/host/edit-package?id=<?= $package['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($package['status'] === 'APPROVED'): ?>
                                                    <a href="/hogartours/packages/<?= $package['id'] ?>" 
                                                       class="btn btn-sm btn-outline-success" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php endif; ?>
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
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

<?php
$title = 'Todos los Paquetes';
ob_start();

// Obtener todos los paquetes con información del anfitrión
global $pdo;
$sql = "SELECT p.*, h.business_name, h.full_name as host_name, h.email as host_email,
               (SELECT COUNT(*) FROM package_images pi WHERE pi.package_id = p.id) as image_count,
               (SELECT pi.filename FROM package_images pi 
                WHERE pi.package_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image
        FROM packages p
        LEFT JOIN hosts h ON p.host_id = h.id
        ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$all_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agregar datos por defecto para campos faltantes
foreach ($all_packages as &$package) {
    $package['host_name'] = $package['host_name'] ?? 'Anfitrión Local';
    $package['business_name'] = $package['business_name'] ?? 'Negocio Local';
    $package['host_email'] = $package['host_email'] ?? 'host@example.com';
    $package['duration'] = $package['duration'] ?? '1 día';
    $package['max_participants'] = $package['max_participants'] ?? 10;
}
?>
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestión de Paquetes</h1>
    <div>
        <a href="/hogartours/admin/packages" class="btn btn-outline-primary me-2">
            <i class="fas fa-clock me-1"></i>Pendientes
        </a>
        <a href="/hogartours/admin/dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Dashboard
        </a>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row mb-4">
    <?php
    $total = count($all_packages);
    $approved = count(array_filter($all_packages, fn($p) => $p['status'] === 'approved'));
    $pending = count(array_filter($all_packages, fn($p) => $p['status'] === 'pending'));
    $with_images = count(array_filter($all_packages, fn($p) => $p['image_count'] > 0));
    ?>
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h3 class="text-primary"><?= $total ?></h3>
                <p class="mb-0">Total Paquetes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <h3 class="text-success"><?= $approved ?></h3>
                <p class="mb-0">Aprobados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h3 class="text-warning"><?= $pending ?></h3>
                <p class="mb-0">Pendientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <h3 class="text-info"><?= $with_images ?></h3>
                <p class="mb-0">Con Imágenes</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select" id="statusFilter" onchange="filterPackages()">
                    <option value="">Todos los estados</option>
                    <option value="pending">Pendientes</option>
                    <option value="approved">Aprobados</option>
                    <option value="rejected">Rechazados</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="imageFilter" onchange="filterPackages()">
                    <option value="">Todas las imágenes</option>
                    <option value="with">Con imágenes</option>
                    <option value="without">Sin imágenes</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchFilter" 
                       placeholder="Buscar por título o anfitrión..." 
                       onkeyup="filterPackages()">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                    <i class="fas fa-eraser me-1"></i>Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Lista de paquetes -->
<?php if (empty($all_packages)): ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>No hay paquetes registrados</h5>
        <p class="mb-0">Los paquetes aparecerán aquí cuando los anfitriones comiencen a registrarlos.</p>
    </div>
<?php else: ?>
    <div class="row" id="packagesContainer">
        <?php foreach ($all_packages as $package): ?>
            <div class="col-lg-6 col-xl-4 mb-4 package-item" 
                 data-status="<?= $package['status'] ?>"
                 data-images="<?= $package['image_count'] > 0 ? 'with' : 'without' ?>"
                 data-search="<?= strtolower($package['title'] . ' ' . ($package['host_name'] ?? '')) ?>">
                
                <div class="card h-100">
                    <!-- Imagen del paquete -->
                    <div class="position-relative">
                        <?php if ($package['main_image']): ?>
                            <?php
                            require_once __DIR__ . '/../../src/helpers/ImageHelper.php';
                            $image_url = ImageHelper::getThumbUrl($package['main_image']);
                            ?>
                            <img src="<?= $image_url ?>" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" 
                                 alt="<?= htmlspecialchars($package['title']) ?>">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px; color: #6c757d;">
                                <div class="text-center">
                                    <i class="fas fa-image fa-3x mb-2"></i>
                                    <div>Sin imagen</div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge de estado -->
                        <span class="badge position-absolute top-0 start-0 m-2 
                                   bg-<?= $package['status'] === 'APPROVED' ? 'success' : 
                                          ($package['status'] === 'PENDING' ? 'warning' : 'danger') ?>">
                            <?= ucfirst(strtolower($package['status'])) ?>
                        </span>
                        
                        <!-- Badge de imágenes -->
                        <span class="badge bg-info position-absolute top-0 end-0 m-2">
                            <i class="fas fa-images me-1"></i><?= $package['image_count'] ?>
                        </span>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <!-- Título y detalles -->
                        <h5 class="card-title"><?= htmlspecialchars($package['title']) ?></h5>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-user me-1"></i>
                            <?= htmlspecialchars($package['host_name'] ?? 'Anfitrión') ?>
                        </p>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?= htmlspecialchars($package['location'] ?? 'No especificado') ?>
                        </p>
                        
                        <!-- Descripción -->
                        <p class="card-text flex-grow-1">
                            <?= htmlspecialchars(substr($package['description'], 0, 120)) ?>
                            <?= strlen($package['description']) > 120 ? '...' : '' ?>
                        </p>
                        
                        <!-- Detalles del paquete -->
                        <div class="row text-center mb-3 small">
                            <div class="col-4">
                                <strong>S/ <?= number_format($package['price'], 0) ?></strong>
                                <div class="text-muted">Precio</div>
                            </div>
                            <div class="col-4">
                                <strong><?= $package['duration'] ?> día<?= $package['duration'] > 1 ? 's' : '' ?></strong>
                                <div class="text-muted">Duración</div>
                            </div>
                            <div class="col-4">
                                <strong><?= $package['max_participants'] ?></strong>
                                <div class="text-muted">Max personas</div>
                            </div>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="mt-auto">
                            <div class="btn-group w-100" role="group">
                                <a href="/hogartours/admin/edit-package?id=<?= $package['id'] ?>" 
                                   class="btn btn-outline-primary btn-sm" 
                                   title="Editar paquete">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/hogartours/package/images?package_id=<?= $package['id'] ?>" 
                                   class="btn btn-outline-secondary btn-sm"
                                   title="Ver imágenes">
                                    <i class="fas fa-images"></i>
                                </a>
                                <button class="btn btn-outline-info btn-sm" 
                                        onclick="viewPackageDetails(<?= $package['id'] ?>)"
                                        title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($package['status'] === 'PENDING'): ?>
                                    <button class="btn btn-outline-success btn-sm" 
                                            onclick="approvePackage(<?= $package['id'] ?>)"
                                            title="Aprobar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="rejectPackage(<?= $package['id'] ?>)"
                                            title="Rechazar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php elseif ($package['status'] === 'APPROVED'): ?>
                                    <button class="btn btn-outline-warning btn-sm" 
                                            onclick="rejectPackage(<?= $package['id'] ?>)"
                                            title="Desaprobar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                <?php elseif ($package['status'] === 'REJECTED'): ?>
                                    <button class="btn btn-outline-success btn-sm" 
                                            onclick="approvePackage(<?= $package['id'] ?>)"
                                            title="Aprobar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Mensaje cuando no hay resultados filtrados -->
    <div id="noResults" class="text-center py-5 d-none">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No se encontraron paquetes</h5>
        <p class="text-muted">Intenta ajustar los filtros para ver más resultados.</p>
        <button class="btn btn-outline-primary" onclick="clearFilters()">
            Limpiar Filtros
        </button>
    </div>
<?php endif; ?>

<!-- Modal para detalles del paquete -->
<div class="modal fade" id="packageDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Paquete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="packageDetailsContent">
                <!-- Contenido cargado dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
function filterPackages() {
    const statusFilter = document.getElementById('statusFilter').value;
    const imageFilter = document.getElementById('imageFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    
    const items = document.querySelectorAll('.package-item');
    let visibleCount = 0;
    
    items.forEach(item => {
        let show = true;
        
        // Filtro por estado
        if (statusFilter && item.dataset.status !== statusFilter) {
            show = false;
        }
        
        // Filtro por imágenes
        if (imageFilter && item.dataset.images !== imageFilter) {
            show = false;
        }
        
        // Filtro por búsqueda
        if (searchFilter && !item.dataset.search.includes(searchFilter)) {
            show = false;
        }
        
        if (show) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Mostrar/ocultar mensaje de no resultados
    const noResults = document.getElementById('noResults');
    if (visibleCount === 0) {
        noResults.classList.remove('d-none');
    } else {
        noResults.classList.add('d-none');
    }
}

function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('imageFilter').value = '';
    document.getElementById('searchFilter').value = '';
    filterPackages();
}

function approvePackage(id) {
    if (confirm('¿Aprobar este paquete?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/hogartours/admin/approve-package';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectPackage(id) {
    if (confirm('¿Rechazar este paquete?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/hogartours/admin/reject-package';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function viewPackageDetails(id) {
    // Aquí podrías cargar detalles vía AJAX
    document.getElementById('packageDetailsContent').innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Cargando detalles...</p>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('packageDetailsModal')).show();
    
    // Simular carga de datos
    setTimeout(() => {
        document.getElementById('packageDetailsContent').innerHTML = `
            <p>Detalles del paquete #${id}</p>
            <p>Esta funcionalidad se puede expandir para mostrar información completa del paquete.</p>
        `;
    }, 1000);
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

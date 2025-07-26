<?php
$page_title = 'Gestión de Usuarios';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Gestión de Usuarios</h1>
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

    <!-- Estadísticas de usuarios -->
    <div class="row mb-4">
        <?php
        $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $admin_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        $regular_users = $total_users - $admin_users;
        $recent_users = $pdo->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
        ?>
        
        <div class="col-md-3 mb-3">
            <div class="card card-stats text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><?php echo $total_users; ?></h5>
                            <p class="card-text">Total Usuarios</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background-color: #dc3545;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><?php echo $admin_users; ?></h5>
                            <p class="card-text">Administradores</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-shield-check fs-1"></i>
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
                            <h5 class="card-title"><?php echo $regular_users; ?></h5>
                            <p class="card-text">Usuarios Regulares</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person fs-1"></i>
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
                            <h5 class="card-title"><?php echo $recent_users; ?></h5>
                            <p class="card-text">Nuevos (30 días)</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-plus fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Todos los Usuarios</h5>
                    <button class="btn btn-primary btn-sm" onclick="createUser()">
                        <i class="bi bi-plus"></i> Nuevo Usuario
                    </button>
                </div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT * FROM users ORDER BY created_at DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <?php if (empty($users)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay usuarios registrados</h5>
                            <p class="text-muted">Los usuarios aparecerán aquí cuando se registren en la plataforma.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Fecha de Registro</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo $user['id']; ?></strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                             style="width: 32px; height: 32px; font-size: 14px;">
                                                            <?php echo strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)); ?>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($user['full_name'] ?? 'Sin nombre'); ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code><?php echo htmlspecialchars($user['email']); ?></code>
                                            </td>
                                            <td>
                                                <?php if ($user['role'] === 'admin'): ?>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-shield-check"></i> Admin
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-person"></i> Usuario
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Activo</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-primary" onclick="editUser(<?php echo $user['id']; ?>)" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <?php if ($user['role'] !== 'admin'): ?>
                                                        <button class="btn btn-outline-warning" onclick="toggleRole(<?php echo $user['id']; ?>)" title="Hacer Admin">
                                                            <i class="bi bi-shield-plus"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($user['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                                                        <button class="btn btn-outline-danger" onclick="deleteUser(<?php echo $user['id']; ?>)" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
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

<script>
function createUser() {
    alert('Función de crear usuario próximamente disponible');
    // Aquí se implementaría un modal o redirigir a una página de creación
}

function editUser(userId) {
    alert('Editar usuario ID: ' + userId);
    // Aquí se implementaría la funcionalidad de edición
}

function toggleRole(userId) {
    if (confirm('¿Convertir este usuario en administrador?')) {
        alert('Función de cambio de rol ID: ' + userId);
        // Aquí se implementaría la funcionalidad de cambio de rol
    }
}

function deleteUser(userId) {
    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
        alert('Eliminar usuario ID: ' + userId);
        // Aquí se implementaría la funcionalidad de eliminación
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

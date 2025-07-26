<?php
// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: /hogartours/login');
    exit;
}

// Obtener información del usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: /hogartours/logout');
        exit;
    }
} catch (PDOException $e) {
    error_log("Error al obtener usuario: " . $e->getMessage());
    $user = [
        'name' => $_SESSION['user_name'] ?? 'Usuario',
        'email' => $_SESSION['user_email'] ?? '',
        'created_at' => date('Y-m-d')
    ];
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                     style="width: 60px; height: 60px;">
                    <i class="fas fa-user fa-2x text-white"></i>
                </div>
                <div>
                    <h2 class="mb-0">Mi Perfil</h2>
                    <p class="text-muted mb-0">Gestiona tu información personal</p>
                </div>
            </div>

            <!-- Información del perfil -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-2"></i>Nombre Completo
                            </label>
                            <p class="form-control-plaintext bg-light p-3 rounded">
                                <?= htmlspecialchars($user['name']) ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope me-2"></i>Correo Electrónico
                            </label>
                            <p class="form-control-plaintext bg-light p-3 rounded">
                                <?= htmlspecialchars($user['email']) ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar me-2"></i>Miembro desde
                            </label>
                            <p class="form-control-plaintext bg-light p-3 rounded">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-shield-alt me-2"></i>Tipo de Usuario
                            </label>
                            <p class="form-control-plaintext bg-light p-3 rounded">
                                <?php if (isAdmin()): ?>
                                    <span class="badge bg-danger">Administrador</span>
                                <?php elseif (isHost()): ?>
                                    <span class="badge bg-success">Anfitrión</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Usuario</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones disponibles -->
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                            <h5>Mis Reservas</h5>
                            <p class="text-muted mb-3">
                                Ver y gestionar tus reservas de experiencias
                            </p>
                            <a href="/hogartours/my-bookings" class="btn btn-primary">
                                Ver Reservas
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php if (!isHost() && !isAdmin()): ?>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-home fa-3x text-success mb-3"></i>
                            <h5>Ser Anfitrión</h5>
                            <p class="text-muted mb-3">
                                Comparte tu cultura y gana dinero extra
                            </p>
                            <a href="/hogartours/become-host" class="btn btn-success">
                                Aplicar Ahora
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (isHost()): ?>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-tachometer-alt fa-3x text-success mb-3"></i>
                            <h5>Panel de Anfitrión</h5>
                            <p class="text-muted mb-3">
                                Gestiona tus experiencias y reservas
                            </p>
                            <a href="/hogartours/host/dashboard" class="btn btn-success">
                                Ir al Panel
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (isAdmin()): ?>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-cogs fa-3x text-danger mb-3"></i>
                            <h5>Panel de Admin</h5>
                            <p class="text-muted mb-3">
                                Administrar plataforma y usuarios
                            </p>
                            <a href="/hogartours/admin/dashboard" class="btn btn-danger">
                                Ir al Panel
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Estadísticas del usuario (si tiene reservas) -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-primary mb-0">0</h3>
                                <small class="text-muted">Experiencias Reservadas</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-success mb-0">0</h3>
                                <small class="text-muted">Experiencias Completadas</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-warning mb-0">0</h3>
                            <small class="text-muted">Reseñas Escritas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

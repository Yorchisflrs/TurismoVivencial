<?php
// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: /hogartours/login');
    exit;
}

// Obtener reservas del usuario (simuladas por ahora)
$bookings = []; // En el futuro aquí consultaremos la tabla de reservas

?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-calendar-check fa-2x text-white"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Mis Reservas</h2>
                        <p class="text-muted mb-0">Gestiona todas tus experiencias reservadas</p>
                    </div>
                </div>
                <a href="/hogartours/packages" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nueva Reserva
                </a>
            </div>

            <!-- Filtros de estado -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <button class="btn btn-outline-primary active" data-filter="all">
                                Todas (0)
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-warning" data-filter="pending">
                                Pendientes (0)
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-success" data-filter="confirmed">
                                Confirmadas (0)
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-info" data-filter="completed">
                                Completadas (0)
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-danger" data-filter="cancelled">
                                Canceladas (0)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de reservas -->
            <?php if (empty($bookings)): ?>
                <div class="text-center py-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No tienes reservas aún</h4>
                            <p class="text-muted mb-4">
                                Explora nuestras experiencias únicas y reserva tu primera aventura en el altiplano peruano.
                            </p>
                            <a href="/hogartours/packages" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i>Explorar Experiencias
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Aquí irían las reservas cuando las implementemos -->
                <div class="row">
                    <!-- Ejemplo de reserva (para futuro desarrollo) -->
                    <div class="col-12 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="https://via.placeholder.com/150x100" 
                                             class="img-fluid rounded" 
                                             alt="Experiencia">
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="mb-1">Aventura en el Titicaca</h5>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            Islas Flotantes, Titicaca
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-calendar me-1"></i>
                                            15 de Agosto, 2024 - 09:00 AM
                                        </p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <span class="badge bg-warning fs-6">Pendiente</span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Ver Detalles</a></li>
                                                <li><a class="dropdown-item" href="#">Contactar Anfitrión</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#">Cancelar</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Script para filtros de estado
document.querySelectorAll('[data-filter]').forEach(button => {
    button.addEventListener('click', function() {
        // Remover clase active de todos los botones
        document.querySelectorAll('[data-filter]').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Agregar clase active al botón clickeado
        this.classList.add('active');
        
        // Aquí implementaremos el filtrado cuando tengamos reservas reales
        const filter = this.dataset.filter;
        console.log('Filtrar por:', filter);
    });
});
</script>

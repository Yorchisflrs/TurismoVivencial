<?php
$page_title = 'Gestión de Reservaciones';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Gestión de Reservaciones</h1>
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

    <!-- Estadísticas de reservaciones -->
    <div class="row mb-4">
        <?php
        $total_reservations = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
        $pending_reservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'PENDING'")->fetchColumn();
        $confirmed_reservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'CONFIRMED'")->fetchColumn();
        $cancelled_reservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'CANCELLED'")->fetchColumn();
        ?>
        
        <div class="col-md-3 mb-3">
            <div class="card card-stats text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><?php echo $total_reservations; ?></h5>
                            <p class="card-text">Total Reservaciones</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-check fs-1"></i>
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
                            <h5 class="card-title"><?php echo $pending_reservations; ?></h5>
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
            <div class="card text-white" style="background-color: #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><?php echo $confirmed_reservations; ?></h5>
                            <p class="card-text">Confirmadas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
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
                            <h5 class="card-title"><?php echo $cancelled_reservations; ?></h5>
                            <p class="card-text">Canceladas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de reservaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Todas las Reservaciones</h5>
                </div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT r.*, u.full_name as user_name, u.email as user_email, 
                                   p.title as package_title, p.price as package_price
                            FROM reservations r 
                            LEFT JOIN users u ON r.user_id = u.id 
                            LEFT JOIN packages p ON r.package_id = p.id 
                            ORDER BY r.created_at DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <?php if (empty($reservations)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay reservaciones registradas</h5>
                            <p class="text-muted">Las reservaciones aparecerán aquí cuando los usuarios hagan reservas.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Paquete</th>
                                        <th>Fechas</th>
                                        <th>Huéspedes</th>
                                        <th>Estado</th>
                                        <th>Total</th>
                                        <th>Fecha Reserva</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo $reservation['id']; ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($reservation['user_name'] ?? 'Usuario desconocido'); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($reservation['user_email'] ?? ''); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($reservation['package_title'] ?? 'Paquete eliminado'); ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <small class="text-muted">Desde:</small> <?php echo date('d/m/Y', strtotime($reservation['date_from'])); ?><br>
                                                    <small class="text-muted">Hasta:</small> <?php echo date('d/m/Y', strtotime($reservation['date_to'])); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $reservation['guests']; ?> personas</span>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                $status_text = '';
                                                switch ($reservation['status']) {
                                                    case 'PENDING':
                                                        $status_class = 'bg-warning';
                                                        $status_text = 'Pendiente';
                                                        break;
                                                    case 'CONFIRMED':
                                                        $status_class = 'bg-success';
                                                        $status_text = 'Confirmada';
                                                        break;
                                                    case 'CANCELLED':
                                                        $status_class = 'bg-danger';
                                                        $status_text = 'Cancelada';
                                                        break;
                                                    default:
                                                        $status_class = 'bg-secondary';
                                                        $status_text = $reservation['status'];
                                                }
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                            </td>
                                            <td>
                                                <strong>S/ <?php echo number_format($reservation['package_price'] * $reservation['guests'], 2); ?></strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', strtotime($reservation['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <?php if ($reservation['status'] === 'PENDING'): ?>
                                                        <button class="btn btn-outline-success" onclick="confirmReservation(<?php echo $reservation['id']; ?>)" title="Confirmar">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" onclick="cancelReservation(<?php echo $reservation['id']; ?>)" title="Cancelar">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-outline-primary" onclick="viewReservation(<?php echo $reservation['id']; ?>)" title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
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
function confirmReservation(reservationId) {
    if (confirm('¿Confirmar esta reservación?')) {
        alert('Confirmar reservación ID: ' + reservationId);
        // Aquí se implementaría la funcionalidad de confirmación
    }
}

function cancelReservation(reservationId) {
    if (confirm('¿Cancelar esta reservación?')) {
        alert('Cancelar reservación ID: ' + reservationId);
        // Aquí se implementaría la funcionalidad de cancelación
    }
}

function viewReservation(reservationId) {
    alert('Ver detalles de reservación ID: ' + reservationId);
    // Aquí se implementaría la vista de detalles
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

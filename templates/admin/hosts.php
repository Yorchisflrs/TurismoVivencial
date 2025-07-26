<?php
$title = 'Gestión de Anfitriones';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestión de Anfitriones</h1>
    <a href="/hogartours/admin/dashboard" class="btn btn-secondary">← Volver al Dashboard</a>
</div>

<?php if (empty($pending_hosts)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No hay anfitriones pendientes de aprobación.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Negocio</th>
                    <th>Ubicación</th>
                    <th>Fecha de Solicitud</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_hosts as $host): ?>
                <tr>
                    <td><?= $host['id'] ?></td>
                    <td><?= htmlspecialchars($host['name']) ?></td>
                    <td><?= htmlspecialchars($host['email']) ?></td>
                    <td><?= htmlspecialchars($host['phone'] ?? 'No disponible') ?></td>
                    <td><?= htmlspecialchars($host['business_name'] ?? 'No especificado') ?></td>
                    <td><?= htmlspecialchars($host['location'] ?? 'No especificada') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($host['created_at'])) ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-info btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#hostModal<?= $host['id'] ?>">
                                👁️ Ver Detalles
                            </button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $host['id'] ?>">
                                <button type="submit" formaction="/hogartours/admin/approve-host" 
                                        class="btn btn-success btn-sm" 
                                        onclick="return confirm('¿Aprobar este anfitrión?')">
                                    ✓ Aprobar
                                </button>
                                <button type="submit" formaction="/hogartours/admin/reject-host" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="return confirm('¿Rechazar este anfitrión?')">
                                    ✗ Rechazar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                
                <!-- Modal para detalles del anfitrión -->
                <div class="modal fade" id="hostModal<?= $host['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detalles del Anfitrión - <?= htmlspecialchars($host['name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Información Personal</h6>
                                        <p><strong>Nombre:</strong> <?= htmlspecialchars($host['name']) ?></p>
                                        <p><strong>Email:</strong> <?= htmlspecialchars($host['email']) ?></p>
                                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($host['phone'] ?? 'No disponible') ?></p>
                                        <p><strong>Edad:</strong> <?= htmlspecialchars($host['age'] ?? 'No especificada') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Información del Negocio</h6>
                                        <p><strong>Nombre del Negocio:</strong> <?= htmlspecialchars($host['business_name'] ?? 'No especificado') ?></p>
                                        <p><strong>Ubicación:</strong> <?= htmlspecialchars($host['location'] ?? 'No especificada') ?></p>
                                        <p><strong>Máximo de huéspedes:</strong> <?= htmlspecialchars($host['max_guests'] ?? 'No especificado') ?></p>
                                        <p><strong>Idiomas:</strong> <?= htmlspecialchars($host['languages'] ?? 'No especificados') ?></p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Descripción</h6>
                                        <p><?= nl2br(htmlspecialchars($host['description'] ?? 'No disponible')) ?></p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Experiencias que Ofrece</h6>
                                        <p><?= nl2br(htmlspecialchars($host['experiences'] ?? 'No especificadas')) ?></p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Motivación</h6>
                                        <p><?= nl2br(htmlspecialchars($host['motivation'] ?? 'No disponible')) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $host['id'] ?>">
                                    <button type="submit" formaction="/hogartours/admin/approve-host" 
                                            class="btn btn-success" 
                                            onclick="return confirm('¿Aprobar este anfitrión?')">
                                        ✓ Aprobar Anfitrión
                                    </button>
                                    <button type="submit" formaction="/hogartours/admin/reject-host" 
                                            class="btn btn-danger" 
                                            onclick="return confirm('¿Rechazar este anfitrión?')">
                                        ✗ Rechazar Anfitrión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

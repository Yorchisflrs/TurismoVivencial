<?php
$title = 'Dashboard Admin';
ob_start();
?>
<h1>Panel de Administración</h1>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning">
            <div class="card-body text-center">
                <h3><?= $stats['pending_hosts'] ?? 0 ?></h3>
                <p>Anfitriones Pendientes</p>
                <a href="/hogartours/admin/hosts" class="btn btn-dark">Gestionar</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info">
            <div class="card-body text-center">
                <h3><?= $stats['pending_packages'] ?? 0 ?></h3>
                <p>Paquetes Pendientes</p>
                <a href="/hogartours/admin/packages" class="btn btn-dark">Gestionar</a>
                <a href="/hogartours/admin/all-packages" class="btn btn-outline-light mt-1">Ver Todos</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success">
            <div class="card-body text-center">
                <h3><?= $stats['total_users'] ?? 0 ?></h3>
                <p>Total Usuarios</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary">
            <div class="card-body text-center">
                <h3><?= $stats['approved_packages'] ?? 0 ?></h3>
                <p>Paquetes Activos</p>
            </div>
        </div>
    </div>
</div>

<div id="charts-container">
    <canvas id="pendingItemsChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const data = {
        labels: ['Anfitriones Pendientes', 'Paquetes Pendientes'],
        datasets: [{
            label: 'Items pendientes de aprobación',
            data: [<?= $stats['pending_hosts'] ?? 0 ?>, <?= $stats['pending_packages'] ?? 0 ?>],
            backgroundColor: [
                'rgba(255, 193, 7, 0.5)',
                'rgba(23, 162, 184, 0.5)'
            ],
            borderColor: [
                'rgba(255, 193, 7, 1)',
                'rgba(23, 162, 184, 1)'
            ],
            borderWidth: 1
        }]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
    };

    new Chart(
        document.getElementById('pendingItemsChart'),
        config
    );
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

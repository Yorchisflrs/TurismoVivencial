<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/hogartours/">
            🏔️ HogarTours
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/hogartours/">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/hogartours/packages">Experiencias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/hogartours/about">Nosotros</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            👋 <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="/hogartours/admin/dashboard">
                                    📊 Dashboard Admin
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <?php if (isHost()): ?>
                                <li><a class="dropdown-item" href="/hogartours/host/dashboard">
                                    🏡 Mi Panel de Anfitrión
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="/hogartours/profile">
                                👤 Mi Perfil
                            </a></li>
                            <li><a class="dropdown-item" href="/hogartours/my-bookings">
                                📅 Mis Reservas
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/hogartours/logout">
                                🚪 Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/hogartours/login">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white px-3 ms-2" href="/hogartours/register">
                            Registrarse
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-success fw-bold" href="/hogartours/become-host">
                            🏡 Ser Anfitrión
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php
$title = 'HogarTours - Turismo Rural Auténtico';
ob_start();
?>
<!-- Hero Section -->
<div class="hero-section bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Descubre el Perú Auténtico</h1>
                <p class="lead mb-4">
                    Vive experiencias únicas con familias locales en el corazón del altiplano peruano. 
                    Turismo rural comunitario que transforma vidas.
                </p>
                <div class="d-flex gap-3">
                    <a href="/hogartours/packages" class="btn btn-light btn-lg">Ver Experiencias</a>
                    <a href="/hogartours/register" class="btn btn-outline-light btn-lg">Únete</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image">
                    <div class="bg-light rounded p-4" style="opacity: 0.9;">
                        <h3 class="text-dark">🏔️ Altiplano Peruano</h3>
                        <p class="text-dark mb-0">Puno • Titicaca • Comunidades Rurales</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container mb-5">
    <div class="row text-center mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-3">¿Por qué elegir HogarTours?</h2>
            <p class="lead text-muted">Turismo responsable que beneficia directamente a las comunidades</p>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <span class="display-4">🏡</span>
                    </div>
                    <h5 class="card-title">Experiencias Auténticas</h5>
                    <p class="card-text">Vive como un local con familias campesinas. Participa en actividades tradicionales y aprende sobre la cultura ancestral.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <span class="display-4">💚</span>
                    </div>
                    <h5 class="card-title">Impacto Social</h5>
                    <p class="card-text">Tus viajes generan ingresos directos para familias rurales y contribuyen al desarrollo sostenible de las comunidades.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <span class="display-4">✨</span>
                    </div>
                    <h5 class="card-title">Calidad Garantizada</h5>
                    <p class="card-text">Todos nuestros anfitriones son cuidadosamente seleccionados y nuestras experiencias están certificadas.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-light py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-3">¿Eres parte de una comunidad rural?</h2>
                <p class="lead mb-4">
                    Únete a nuestra red de anfitriones y comparte tu cultura con viajeros de todo el mundo.
                    Genera ingresos adicionales para tu familia mientras preservas tus tradiciones.
                </p>
                <a href="/hogartours/become-host" class="btn btn-primary btn-lg">Quiero ser anfitrión</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/main.php';
?>

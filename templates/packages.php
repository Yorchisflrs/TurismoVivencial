<?php
// Obtener paquetes con sus imágenes principales
try {
    $sql = "SELECT p.*, h.full_name as host_name, h.business_name,
                   (SELECT pi.filename FROM package_images pi 
                    WHERE pi.package_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image
            FROM packages p
            LEFT JOIN hosts h ON p.host_id = h.id
            WHERE p.status = 'APPROVED' AND h.status = 'APPROVED'
            ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $db_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($db_packages)) {
        // Usar paquetes de la base de datos
        $packages = $db_packages;
        foreach ($packages as &$package) {
            // Usar datos reales del anfitrión
            $package['business_name'] = $package['business_name'] ?: $package['host_name'] ?: 'Anfitrión Local';
            $package['location'] = $package['location'] ?: 'Altiplano Peruano';
            $package['avg_rating'] = 4.5 + (rand(0, 10) / 20); // Rating simulado por ahora
            $package['review_count'] = rand(5, 25); // Reviews simuladas por ahora
            
            // Usar imagen real si está disponible
            if ($package['main_image']) {
                require_once __DIR__ . '/../src/helpers/ImageHelper.php';
                $package['image_url'] = ImageHelper::getThumbUrl($package['main_image']);
            } else {
                $package['image_url'] = '';
            }
        }
    } else {
        // Usar paquetes de ejemplo si no hay en BD
        $packages = [];
    }
} catch (PDOException $e) {
    // Si hay error en BD, usar paquetes de ejemplo
    $packages = [];
}

// Si no hay paquetes, crear algunos de ejemplo para mostrar la funcionalidad
if (empty($packages)) {
    $example_packages = [
        [
            'id' => 1,
            'title' => 'Experiencia Textil en Taquile',
            'description' => 'Aprende las técnicas ancestrales de tejido con una familia tradicional de la Isla Taquile. Participa en todo el proceso desde la preparación de la lana hasta el teñido natural con plantas locales.',
            'price' => 180,
            'duration' => 2,
            'category' => 'textiles',
            'max_participants' => 6,
            'location' => 'Isla Taquile',
            'business_name' => 'Familia Mamani',
            'host_id' => 1,
            'avg_rating' => 4.8,
            'review_count' => 15,
            'image_url' => ''
        ],
        [
            'id' => 2,
            'title' => 'Cocina Tradicional del Altiplano',
            'description' => 'Descubre los sabores únicos del altiplano peruano. Cocina platos tradicionales como la trucha del Titicaca, quinua orgánica y papas nativas en un ambiente familiar auténtico.',
            'price' => 120,
            'duration' => 1,
            'category' => 'gastronomia',
            'max_participants' => 8,
            'location' => 'Llachón',
            'business_name' => 'Casa Quispe',
            'host_id' => 2,
            'avg_rating' => 4.9,
            'review_count' => 23,
            'image_url' => ''
        ],
        [
            'id' => 3,
            'title' => 'Pesca Tradicional en Totora',
            'description' => 'Navega en las tradicionales balsas de totora por el lago Titicaca y aprende técnicas de pesca ancestrales. Incluye preparación del pescado recién capturado.',
            'price' => 200,
            'duration' => 1,
            'category' => 'pesca',
            'max_participants' => 4,
            'location' => 'Capachica',
            'business_name' => 'Familia Condori',
            'host_id' => 3,
            'avg_rating' => 4.7,
            'review_count' => 8,
            'image_url' => ''
        ],
        [
            'id' => 4,
            'title' => 'Agricultura en Terrazas Ancestrales',
            'description' => 'Participa en las actividades agrícolas tradicionales del altiplano. Siembra y cosecha papas nativas, quinua y otros cultivos ancestrales en terrazas milenarias.',
            'price' => 150,
            'duration' => 3,
            'category' => 'agricultura',
            'max_participants' => 10,
            'location' => 'Chucuito',
            'business_name' => 'Comunidad Lupaca',
            'host_id' => 4,
            'avg_rating' => 4.6,
            'review_count' => 12,
            'image_url' => ''
        ],
        [
            'id' => 5,
            'title' => 'Ceremonia de la Pachamama',
            'description' => 'Participa en una auténtica ceremonia andina de agradecimiento a la Pachamama. Aprende sobre la cosmovisión andina y las tradiciones espirituales del altiplano.',
            'price' => 80,
            'duration' => 1,
            'category' => 'ceremonias',
            'max_participants' => 15,
            'location' => 'Isla Amantaní',
            'business_name' => 'Familia Huanca',
            'host_id' => 5,
            'avg_rating' => 5.0,
            'review_count' => 6,
            'image_url' => ''
        ]
    ];
    
    $packages = $example_packages;
}

// Obtener categorías únicas para filtros
$categories = ['aventura', 'gastronomia', 'textiles', 'agricultura', 'pesca', 'ceremonias'];

// Obtener ubicaciones únicas
$locations = ['Isla Amantaní', 'Isla Taquile', 'Llachón', 'Capachica', 'Chucuito'];
?>

<div class="container py-5">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold mb-3">Experiencias Auténticas</h1>
            <p class="lead text-muted">
                Descubre el verdadero altiplano peruano a través de experiencias únicas 
                diseñadas por anfitriones locales apasionados por su cultura.
            </p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3" x-data="{ showFilters: false }">
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category) ?>">
                                        <?= ucfirst(htmlspecialchars($category)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="locationFilter">
                                <option value="">Todas las ubicaciones</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?= htmlspecialchars($location) ?>">
                                        <?= htmlspecialchars($location) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="priceFilter">
                                <option value="">Todos los precios</option>
                                <option value="0-100">S/ 0 - 100</option>
                                <option value="100-300">S/ 100 - 300</option>
                                <option value="300-500">S/ 300 - 500</option>
                                <option value="500+">S/ 500+</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-primary w-100" @click="showFilters = !showFilters">
                                <i class="fas fa-filter me-2"></i>
                                Más Filtros
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filtros adicionales -->
                    <div x-show="showFilters" x-transition class="row mt-3 pt-3 border-top">
                        <div class="col-md-4">
                            <label class="form-label">Duración</label>
                            <select class="form-select" id="durationFilter">
                                <option value="">Cualquier duración</option>
                                <option value="1">1 día</option>
                                <option value="2-3">2-3 días</option>
                                <option value="4+">4+ días</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Calificación mínima</label>
                            <select class="form-select" id="ratingFilter">
                                <option value="">Cualquier calificación</option>
                                <option value="4">4+ estrellas</option>
                                <option value="4.5">4.5+ estrellas</option>
                                <option value="5">5 estrellas</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary" onclick="applyFilters()">
                                Aplicar Filtros
                            </button>
                            <button class="btn btn-outline-secondary ms-2" onclick="clearFilters()">
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de paquetes -->
    <div class="row" id="packagesContainer">
        <?php if (empty($packages)): ?>
            <div class="col-12 text-center py-5">
                <div class="card border-0">
                    <div class="card-body">
                        <h3 class="text-muted mb-3">¡Próximamente!</h3>
                        <p class="text-muted">
                            Estamos trabajando con anfitriones locales para traerte 
                            experiencias increíbles. Vuelve pronto.
                        </p>
                        <a href="/hogartours/become-host" class="btn btn-primary">
                            ¿Quieres ser anfitrión?
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($packages as $package): ?>
                <div class="col-lg-4 col-md-6 mb-4 package-card" 
                     data-category="<?= htmlspecialchars($package['category'] ?? '') ?>"
                     data-location="<?= htmlspecialchars($package['location'] ?? '') ?>"
                     data-price="<?= $package['price'] ?? 0 ?>"
                     data-duration="<?= $package['duration'] ?? 1 ?>"
                     data-rating="<?= number_format($package['avg_rating'] ?? 0, 1) ?>">
                    
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <!-- Imagen del paquete -->
                        <div class="position-relative">
                            <?php if (!empty($package['image_url'])): ?>
                                <img src="<?= htmlspecialchars($package['image_url']) ?>" 
                                     class="card-img-top" 
                                     style="height: 220px; object-fit: cover;" 
                                     alt="<?= htmlspecialchars($package['title']) ?>">
                            <?php else: ?>
                                <div class="card-img-top bg-gradient-primary d-flex align-items-center justify-content-center text-white" 
                                     style="height: 220px;">
                                    <i class="fas fa-mountain fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badge de categoría -->
                            <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                                <?= ucfirst(htmlspecialchars($package['category'] ?? 'experiencia')) ?>
                            </span>
                            
                            <!-- Badge de precio -->
                            <span class="badge bg-success position-absolute bottom-0 start-0 m-2 fs-6">
                                S/ <?= number_format($package['price'] ?? 0, 0) ?>
                            </span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <!-- Título y ubicación -->
                            <div class="mb-3">
                                <h5 class="card-title fw-bold mb-2">
                                    <?= htmlspecialchars($package['title'] ?? 'Experiencia') ?>
                                </h5>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?= htmlspecialchars($package['location'] ?? 'Altiplano Peruano') ?>
                                </p>
                                <p class="text-muted small">
                                    <i class="fas fa-store me-1"></i>
                                    por <?= htmlspecialchars($package['business_name'] ?? 'Anfitrión Local') ?>
                                </p>
                            </div>
                            
                            <!-- Descripción -->
                            <p class="card-text text-muted flex-grow-1">
                                <?= htmlspecialchars(substr($package['description'] ?? 'Descripción no disponible', 0, 120)) ?>
                                <?= strlen($package['description'] ?? '') > 120 ? '...' : '' ?>
                            </p>
                            
                            <!-- Detalles -->
                            <div class="row text-center mb-3 small">
                                <div class="col-4">
                                    <i class="fas fa-clock text-primary"></i>
                                    <div><?= isset($package['duration']) ? $package['duration'] : 1 ?> día<?= (isset($package['duration']) && $package['duration'] > 1) ? 's' : '' ?></div>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-users text-primary"></i>
                                    <div>Max <?= isset($package['max_participants']) ? $package['max_participants'] : 6 ?></div>
                                </div>
                                <div class="col-4">
                                    <?php if (isset($package['avg_rating']) && $package['avg_rating']): ?>
                                        <i class="fas fa-star text-warning"></i>
                                        <div><?= number_format($package['avg_rating'], 1) ?> (<?= isset($package['review_count']) ? $package['review_count'] : 0 ?>)</div>
                                    <?php else: ?>
                                        <i class="fas fa-star-o text-muted"></i>
                                        <div>Nuevo</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Botón de acción -->
                            <div class="mt-auto">
                                <a href="/hogartours/package/<?= $package['id'] ?? '#' ?>" 
                                   class="btn btn-primary w-100">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Mensaje cuando no hay resultados -->
    <div id="noResults" class="col-12 text-center py-5 d-none">
        <div class="card border-0">
            <div class="card-body">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h3 class="text-muted mb-3">No encontramos experiencias</h3>
                <p class="text-muted">
                    Intenta ajustar los filtros para encontrar más opciones.
                </p>
                <button class="btn btn-outline-primary" onclick="clearFilters()">
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS personalizado -->
<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #6f42c1 100%);
}
</style>

<!-- JavaScript para filtros -->
<script>
function applyFilters() {
    const category = document.getElementById('categoryFilter').value;
    const location = document.getElementById('locationFilter').value;
    const price = document.getElementById('priceFilter').value;
    const duration = document.getElementById('durationFilter').value;
    const rating = document.getElementById('ratingFilter').value;
    
    const cards = document.querySelectorAll('.package-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        let show = true;
        
        // Filtro por categoría
        if (category && card.dataset.category !== category) {
            show = false;
        }
        
        // Filtro por ubicación
        if (location && card.dataset.location !== location) {
            show = false;
        }
        
        // Filtro por precio
        if (price) {
            const cardPrice = parseFloat(card.dataset.price);
            const [min, max] = price.split('-');
            if (price === '500+') {
                if (cardPrice < 500) show = false;
            } else {
                const minPrice = parseFloat(min);
                const maxPrice = max ? parseFloat(max) : Infinity;
                if (cardPrice < minPrice || cardPrice > maxPrice) {
                    show = false;
                }
            }
        }
        
        // Filtro por duración
        if (duration) {
            const cardDuration = parseFloat(card.dataset.duration);
            if (duration === '1' && cardDuration !== 1) {
                show = false;
            } else if (duration === '2-3' && (cardDuration < 2 || cardDuration > 3)) {
                show = false;
            } else if (duration === '4+' && cardDuration < 4) {
                show = false;
            }
        }
        
        // Filtro por calificación
        if (rating) {
            const cardRating = parseFloat(card.dataset.rating) || 0;
            if (cardRating < parseFloat(rating)) {
                show = false;
            }
        }
        
        if (show) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Mostrar/ocultar mensaje de "no resultados"
    const noResults = document.getElementById('noResults');
    if (visibleCount === 0) {
        noResults.classList.remove('d-none');
    } else {
        noResults.classList.add('d-none');
    }
}

function clearFilters() {
    document.getElementById('categoryFilter').value = '';
    document.getElementById('locationFilter').value = '';
    document.getElementById('priceFilter').value = '';
    document.getElementById('durationFilter').value = '';
    document.getElementById('ratingFilter').value = '';
    
    document.querySelectorAll('.package-card').forEach(card => {
        card.style.display = 'block';
    });
    
    document.getElementById('noResults').classList.add('d-none');
}

// Aplicar filtros en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    ['categoryFilter', 'locationFilter', 'priceFilter'].forEach(id => {
        document.getElementById(id).addEventListener('change', applyFilters);
    });
});
</script>

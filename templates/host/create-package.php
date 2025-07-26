<?php
$title = 'Crear Nueva Experiencia';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0 bg-dark">
            <div class="d-flex flex-column min-vh-100">
                <div class="bg-dark text-white p-3">
                    <h5 class="mb-0">🏔️ Panel Anfitrión</h5>
                    <small>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></small>
                </div>
                <nav class="nav flex-column p-3">
                    <a class="nav-link text-white" href="/hogartours/host/dashboard">
                        <i class="fas fa-chart-line me-2"></i> Dashboard
                    </a>
                    <a class="nav-link text-white active" href="/hogartours/host/create-package">
                        <i class="fas fa-plus-circle me-2"></i> Crear Experiencia
                    </a>
                    <a class="nav-link text-white" href="/hogartours/">
                        <i class="fas fa-home me-2"></i> Ver Sitio Web
                    </a>
                    <a class="nav-link text-white" href="/hogartours/logout">
                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Crear Nueva Experiencia</h1>
                    <a href="/hogartours/host/dashboard" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Información de la Experiencia</h5>
                            </div>
                            <div class="card-body">
                                <form action="/hogartours/host/store-package" method="POST" enctype="multipart/form-data">
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <label for="title" class="form-label">Título de la Experiencia *</label>
                                            <input type="text" class="form-control" id="title" name="title" required
                                                   placeholder="Ej: Aventura en el Lago Titicaca">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="category" class="form-label">Categoría *</label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="">Seleccionar...</option>
                                                <option value="alojamiento">🏠 Alojamiento Rural</option>
                                                <option value="gastronomia">🍽️ Gastronomía Local</option>
                                                <option value="textileria">🧵 Textilería Tradicional</option>
                                                <option value="agricultura">🌾 Agricultura Tradicional</option>
                                                <option value="pesca">🎣 Pesca en el Titicaca</option>
                                                <option value="ceremonias">🎭 Ceremonias Ancestrales</option>
                                                <option value="trekking">🥾 Trekking y Naturaleza</option>
                                                <option value="artesania">🎨 Artesanía Local</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="location" class="form-label">Ubicación *</label>
                                            <input type="text" class="form-control" id="location" name="location" required
                                                   placeholder="Ej: Isla Amantaní, Puno">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="price" class="form-label">Precio (S/) *</label>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   step="0.01" min="0" required placeholder="0.00">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="capacity" class="form-label">Capacidad *</label>
                                            <input type="number" class="form-control" id="capacity" name="capacity" 
                                                   min="1" required placeholder="Máx. personas">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Descripción de la Experiencia *</label>
                                        <textarea class="form-control" id="description" name="description" rows="6" required
                                                  placeholder="Describe detalladamente la experiencia que ofreces. Incluye actividades, duración, qué incluye, qué no incluye, etc."></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label for="images" class="form-label">Imágenes de la Experiencia</label>
                                        <div class="border rounded p-3 bg-light">
                                            <input type="file" class="form-control" id="images" name="images[]" 
                                                   multiple accept="image/*" onchange="previewImages(this)">
                                            <small class="form-text text-muted">
                                                Puedes subir múltiples imágenes. Formatos aceptados: JPG, PNG, WebP. Tamaño máximo: 5MB por imagen.
                                            </small>
                                            <div id="image-preview" class="mt-3 d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="/hogartours/host/dashboard" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Crear Experiencia
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">💡 Consejos para una buena experiencia</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>📸 Imágenes de calidad</strong>
                                    <p class="small text-muted mb-2">Las primeras impresiones importan. Sube fotos nítidas y atractivas que muestren lo mejor de tu experiencia.</p>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>📝 Descripción detallada</strong>
                                    <p class="small text-muted mb-2">Explica claramente qué incluye la experiencia, cuánto dura, qué aprenderán los visitantes.</p>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>💰 Precio justo</strong>
                                    <p class="small text-muted mb-2">Considera el valor que ofreces, los costos involucrados y los precios del mercado local.</p>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>✅ Proceso de aprobación</strong>
                                    <p class="small text-muted mb-2">Tu experiencia será revisada por nuestro equipo antes de ser publicada. Esto asegura la calidad para los viajeros.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImages(input) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'position-relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <small class="d-block text-center mt-1">${file.name}</small>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

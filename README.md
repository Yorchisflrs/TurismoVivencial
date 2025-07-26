# HogarTours - Turismo Vivencial

Sistema web para gestión de turismo vivencial que conecta anfitriones locales con turistas interesados en experiencias auténticas.

## Características

- **Sistema de Usuarios**: Registro y autenticación para turistas, anfitriones y administradores
- **Gestión de Paquetes**: Los anfitriones pueden crear y gestionar paquetes turísticos
- **Galería de Imágenes**: Sistema completo de carga y gestión de imágenes para paquetes
- **Panel de Administración**: Gestión completa de usuarios, paquetes e imágenes
- **Dashboard de Anfitrión**: Interfaz especializada para anfitriones con estadísticas y gestión
- **Diseño Responsivo**: Compatible con dispositivos móviles y desktop

## Tecnologías

- **Backend**: PHP 8.3 con arquitectura MVC
- **Base de Datos**: MySQL 8
- **Frontend**: Bootstrap 5, Alpine.js
- **Servidor**: Apache (XAMPP)

## Instalación

1. Clona el repositorio en tu directorio de XAMPP:
```bash
git clone https://github.com/Yorchisflrs/TurismoVivencial.git
cd TurismoVivencial
```

2. Configura la base de datos:
   - Crea una base de datos llamada `hogartours`
   - Importa el esquema desde `database/schema.sql`

3. Configura la conexión a la base de datos en `src/lib/database.php`

4. Inicia XAMPP y accede a `http://localhost/TurismoVivencial`

## Estructura del Proyecto

```
hogartours/
├── public/                 # Archivos públicos
│   ├── css/               # Estilos CSS
│   ├── js/                # JavaScript
│   ├── images/            # Imágenes del sitio
│   └── uploads/           # Archivos subidos
├── src/                   # Código fuente
│   ├── controllers/       # Controladores MVC
│   ├── models/           # Modelos de datos
│   ├── lib/              # Librerías y helpers
│   └── middleware/       # Middleware de autenticación
├── templates/            # Plantillas HTML
│   ├── admin/           # Plantillas de administración
│   ├── host/            # Plantillas de anfitrión
│   └── layouts/         # Layouts base
└── uploads/             # Directorio de cargas
```

## Funcionalidades Principales

### Para Turistas
- Explorar paquetes turísticos
- Ver detalles y galerías de imágenes
- Sistema de registro y login

### Para Anfitriones
- Dashboard con estadísticas
- Crear y gestionar paquetes turísticos
- Subir y organizar imágenes
- Gestión de perfil

### Para Administradores
- Panel de control completo
- Gestión de usuarios y roles
- Moderación de contenido
- Estadísticas del sistema

## Contribución

1. Fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para detalles.

## Autor

Desarrollado para el proyecto de Turismo Vivencial.

<?php
// index.php (raíz del proyecto)
session_start();

// Incluir la conexión a la base de datos
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/lib/helpers.php';

// Basic router
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
$route = str_replace('/hogartours', '', $request_uri);

// Controllers
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/AdminController.php';
require_once __DIR__ . '/src/controllers/ImageController.php';
require_once __DIR__ . '/src/controllers/HostController.php';

switch ($route) {
    case '/':
        (new HomeController())->index();
        break;
    case '/login':
        (new AuthController())->showLogin();
        break;
    case '/login-process':
        (new AuthController())->login();
        break;
    case '/register':
        (new AuthController())->showRegister();
        break;
    case '/register-process':
        (new AuthController())->register();
        break;
    case '/logout':
        (new AuthController())->logout();
        break;
    case '/admin':
    case '/admin/dashboard':
        (new AdminController())->dashboard();
        break;
    case '/admin/hosts':
        (new AdminController())->hosts();
        break;
    case '/admin/approve-host':
        (new AdminController())->approveHost();
        break;
    case '/admin/reject-host':
        (new AdminController())->rejectHost();
        break;
    case '/admin/packages':
        (new AdminController())->packages();
        break;
    case '/admin/approve-package':
        (new AdminController())->approvePackage();
        break;
    case '/admin/reject-package':
        (new AdminController())->rejectPackage();
        break;
    case '/admin/all-packages':
        (new AdminController())->allPackages();
        break;
    case '/admin/edit-package':
        (new AdminController())->editPackage();
        break;
    case '/admin/update-package':
        (new AdminController())->updatePackage();
        break;
    case '/admin/delete-package':
        (new AdminController())->deletePackage();
        break;
    case '/admin/package-images':
        (new AdminController())->packageImages();
        break;
    case '/admin/users':
        (new AdminController())->users();
        break;
    case '/admin/reservations':
        (new AdminController())->reservations();
        break;
    case '/packages':
        $page = 'packages';
        include 'templates/layouts/main.php';
        break;
    case '/about':
        $page = 'about';
        include 'templates/layouts/main.php';
        break;
    case '/become-host':
        $page = 'become-host';
        include 'templates/layouts/main.php';
        break;
    case '/register-host':
        (new AuthController())->registerHost();
        break;
    case '/profile':
        $page = 'profile';
        include 'templates/layouts/main.php';
        break;
    case '/my-bookings':
        $page = 'my-bookings';
        include 'templates/layouts/main.php';
        break;
    // Host management routes
    case '/host/dashboard':
        (new HostController())->dashboard();
        break;
    case '/host/create-package':
        (new HostController())->createPackage();
        break;
    case '/host/store-package':
        (new HostController())->storePackage();
        break;
    case '/host/edit-package':
        (new HostController())->editPackage();
        break;
    // Image management routes
    case '/api/images/upload':
        (new ImageController())->uploadImages();
        break;
    case '/api/images/delete':
        (new ImageController())->deleteImage();
        break;
    case '/api/images/set-main':
        (new ImageController())->setMainImage();
        break;
    case '/api/images/update-caption':
        (new ImageController())->updateCaption();
        break;
    case '/api/images/get':
        (new ImageController())->getPackageImages();
        break;
    case '/package/images':
        $page = 'package-images';
        include 'templates/layouts/main.php';
        break;
    // Add more routes here
    default:
        http_response_code(404);
        echo 'Page not found';
        break;
}


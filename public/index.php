<?php
// index.php (movido a la raíz)
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
    // Add more routes here
    default:
        http_response_code(404);
        echo 'Page not found';
        break;
}

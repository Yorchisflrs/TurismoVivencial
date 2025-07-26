<?php
// src/controllers/AuthController.php
require_once __DIR__ . '/../../config/database.php'; // Incluir conexión PDO

class AuthController {
    public function showLogin() {
        require __DIR__ . '/../../templates/auth/login.php';
    }

    public function login() {
        global $pdo;

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Por favor, completa todos los campos.";
            require __DIR__ . '/../../templates/auth/login.php';
            return;
        }

        try {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirigir según el rol
                if ($user['role'] === 'ADMIN') {
                    header('Location: /hogartours/admin/dashboard');
                } else {
                    header('Location: /hogartours/');
                }
                exit;
            } else {
                $error = "Credenciales incorrectas. Inténtalo de nuevo.";
                require __DIR__ . '/../../templates/auth/login.php';
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
            require __DIR__ . '/../../templates/auth/login.php';
        }
    }

    public function showRegister() {
        require __DIR__ . '/../../templates/auth/register.php';
    }

    public function register() {
        global $pdo;

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $error = "Por favor, completa todos los campos.";
            require __DIR__ . '/../../templates/auth/register.php';
            return;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $password_hash, 'TOURIST']);
            
            // Redirigir al login después del registro exitoso
            header('Location: /hogartours/login');
            exit;

        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error = "El correo electrónico ya está registrado.";
            } else {
                $error = "Error al registrar: " . $e->getMessage();
            }
            require __DIR__ . '/../../templates/auth/register.php';
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /hogartours/');
        exit;
    }

    public function registerHost() {
        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Método no permitido';
            header('Location: /hogartours/become-host');
            exit;
        }

        // Validar campos requeridos
        $required_fields = ['full_name', 'email', 'phone', 'business_name', 'location', 'description', 'motivation'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = 'Por favor, completa todos los campos obligatorios.';
                header('Location: /hogartours/become-host');
                exit;
            }
        }

        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $age = $_POST['age'] ?? null;
        $business_name = $_POST['business_name'];
        $location = $_POST['location'];
        $description = $_POST['description'];
        $experiences = isset($_POST['experiences']) ? implode(',', $_POST['experiences']) : '';
        $max_guests = $_POST['max_guests'] ?? null;
        $languages = $_POST['languages'] ?? '';
        $motivation = $_POST['motivation'];

        try {
            // Verificar si el email ya existe
            $stmt = $pdo->prepare('SELECT id FROM hosts WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Ya existe una solicitud con este correo electrónico.';
                header('Location: /hogartours/become-host');
                exit;
            }

            // Insertar nueva solicitud de anfitrión
            $stmt = $pdo->prepare('
                INSERT INTO hosts (
                    user_id, full_name, email, phone, age, business_name, location, 
                    description, experiences, max_guests, languages, motivation, 
                    status, created_at
                ) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "PENDING", NOW())
            ');

            $stmt->execute([
                $full_name, $email, $phone, $age, $business_name, $location,
                $description, $experiences, $max_guests, $languages, $motivation
            ]);

            $_SESSION['success'] = '¡Solicitud enviada exitosamente! Te contactaremos en las próximas 48 horas para continuar con el proceso de verificación.';
            header('Location: /hogartours/become-host');
            exit;

        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error al procesar la solicitud: ' . $e->getMessage();
            header('Location: /hogartours/become-host');
            exit;
        }
    }
}

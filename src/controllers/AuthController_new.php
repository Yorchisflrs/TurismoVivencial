<?php
// src/controllers/AuthController.php
require_once __DIR__ . '/../../config/database.php';

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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
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
            $error = "Error de base de datos: " . $e->getMessage();
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
}

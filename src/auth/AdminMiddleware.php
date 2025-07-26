<?php
// src/auth/AdminMiddleware.php
class AdminMiddleware {
    public static function handle() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
            http_response_code(403);
            die('Forbidden: You do not have access to this page.');
        }
    }
}

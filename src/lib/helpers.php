<?php
// src/lib/helpers.php

function view($template, $data = []) {
    extract($data);
    require __DIR__ . "/../../templates/$template.php";
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN';
}

function isHost() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM hosts WHERE user_id = ? AND status = "APPROVED"');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchColumn() > 0;
}

function isTourist() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'TOURIST';
}

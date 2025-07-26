<?php
// public/testdb.php
require_once __DIR__ . '/../config/database.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

echo 'Conexión exitosa a la base de datos: ' . DB_NAME;

$conn->close();

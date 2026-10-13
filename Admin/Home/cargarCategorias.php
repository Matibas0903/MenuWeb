<?php
// Protege el endpoint: exige sesión de admin (ver config/auth_guard.php)
require_once __DIR__ . "/../../config/auth_guard.php";
require_once __DIR__ . "/../../config/conexion.php";
header("content-type: application/json");
try {
    $sql = "SELECT * FROM categoria";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al cargar categorias']);
    exit;
}

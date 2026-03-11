<?php
require_once ROOT_PATH . "/config/conexion.php";
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

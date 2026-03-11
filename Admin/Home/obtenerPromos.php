<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/conexion.php";

header("Content-Type: Application/json");

try {
    $sql = "
SELECT 
*
FROM promo
";

    $stmt = $conn->query($sql);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "error al obtener promos"
    ]);
    exit;
}

<?php

require_once __DIR__ . "/../../config/conexion.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? null;

if (!$id) {
    echo json_encode(["status" => "error"]);
    exit;
}

try {


    // eliminar subcategoria
    $sqlSub = "DELETE FROM subcategoria WHERE ID_SUBCATEGORIA = :id";
    $stmt = $conn->prepare($sqlSub);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    echo json_encode(["status" => "ok"]);
} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

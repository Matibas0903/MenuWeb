<?php

require_once __DIR__ . "/../../config/conexion.php";


header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["id"])) {

    echo json_encode([
        "status" => "error",
        "message" => "ID no recibido"
    ]);
    exit;
}

$id = intval($data["id"]);

try {

    $sql = "SELECT IMAGEN_URL FROM producto WHERE ID_PRODUCTO = :id_producto";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id_producto", $id, PDO::PARAM_INT);
    $stmt->execute();

    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo json_encode([
            "status" => "error",
            "message" => "Producto no encontrado"
        ]);
        exit;
    }


    if (!empty($producto["IMAGEN_URL"])) {

        $rutaImagen = "../uploads/" . $producto["IMAGEN_URL"];

        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }
    }


    $sql = "DELETE FROM producto WHERE ID_PRODUCTO = :id_producto";
    $stmtDelete = $conn->prepare($sql);
    $stmtDelete->bindParam(":id_producto", $id, PDO::PARAM_INT);
    $stmtDelete->execute();

    echo json_encode([
        "status" => "ok",
        "message" => "Producto eliminado"
    ]);
} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => "Error al eliminar"
    ]);
}

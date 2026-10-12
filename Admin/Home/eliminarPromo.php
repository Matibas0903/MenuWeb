<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/config/conexion.php";


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

    $sql = "SELECT IMAGEN_URL_PROMO FROM promo WHERE ID_PROMO = :id_promo";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id_promo", $id, PDO::PARAM_INT);
    $stmt->execute();

    $promo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$promo) {
        echo json_encode([
            "status" => "error",
            "message" => "promo no encontrada"
        ]);
        exit;
    }


    if (!empty($promo["IMAGEN_URL_PROMO"])) {

        $rutaImagen = "../uploads/" . $promo["IMAGEN_URL_PROMO"];

        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }
    }


    $sql = "DELETE FROM promo WHERE ID_PROMO = :id_promo";
    $stmtDelete = $conn->prepare($sql);
    $stmtDelete->bindParam(":id_promo", $id, PDO::PARAM_INT);
    $stmtDelete->execute();

    echo json_encode([
        "status" => "ok",
        "message" => "promo eliminada"
    ]);
} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => "Error al eliminar"
    ]);
}

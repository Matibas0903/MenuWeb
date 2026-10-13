<?php

// Protege el endpoint: exige sesión de admin (ver config/auth_guard.php)
require_once __DIR__ . "/../../config/auth_guard.php";
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


    // IMAGEN_URL en BD guarda la ruta pública completa (ej: /Resources/img_productos/archivo.png).
    // PROJECT_ROOT apunta a la carpeta real del proyecto (no al docroot), así que
    // el archivo se resuelve aunque la app esté servida desde un subdirectorio.
    if (!empty($producto["IMAGEN_URL"])) {

        $rutaImagen = PROJECT_ROOT . $producto["IMAGEN_URL"];

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

<?php

// Protege el endpoint: exige sesión de admin (ver config/auth_guard.php)
require_once __DIR__ . "/../../config/auth_guard.php";
require_once __DIR__ . "/../../config/conexion.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? null;

if (!$id) {
    echo json_encode(["status" => "error", "message" => "ID no recibido"]);
    exit;
}

try {
    // Borrado en cascada EXPLÍCITO dentro de una transacción.
    // No dependemos de ON DELETE CASCADE en la BD: si la subcategoría
    // tiene productos, primero se borran sus imágenes del disco y
    // los registros de producto, y recién después la subcategoría.
    // Si algo falla, la transacción revierte y no quedan datos a medias.
    $conn->beginTransaction();

    // 1) Traer productos de la subcategoría para limpiar imágenes del disco
    $sqlImgs = "SELECT IMAGEN_URL FROM producto WHERE ID_SUBCATEGORIA = :id";
    $stmtImgs = $conn->prepare($sqlImgs);
    $stmtImgs->bindParam(":id", $id, PDO::PARAM_INT);
    $stmtImgs->execute();
    $imagenes = $stmtImgs->fetchAll(PDO::FETCH_ASSOC);

    foreach ($imagenes as $img) {
        // IMAGEN_URL guarda la ruta pública completa (ej: /Resources/img_productos/x.png)
        // y PROJECT_ROOT la raíz real del proyecto (definida en config/conexion.php).
        if (!empty($img["IMAGEN_URL"])) {
            $rutaImagen = PROJECT_ROOT . $img["IMAGEN_URL"];
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }
    }

    // 2) Borrar productos de la subcategoría
    $sqlProd = "DELETE FROM producto WHERE ID_SUBCATEGORIA = :id";
    $stmtProd = $conn->prepare($sqlProd);
    $stmtProd->bindParam(":id", $id, PDO::PARAM_INT);
    $stmtProd->execute();

    // 3) Borrar la subcategoría
    $sqlSub = "DELETE FROM subcategoria WHERE ID_SUBCATEGORIA = :id";
    $stmtSub = $conn->prepare($sqlSub);
    $stmtSub->bindParam(":id", $id, PDO::PARAM_INT);
    $stmtSub->execute();

    $conn->commit();

    echo json_encode(["status" => "ok"]);
} catch (PDOException $e) {
    // Revertir si algo falló a mitad de camino
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // No exponemos el mensaje interno de la BD al cliente
    error_log("Error al eliminar subcategoría: " . $e->getMessage());
    echo json_encode([
        "status"  => "error",
        "message" => "Error al eliminar la subcategoría"
    ]);
}

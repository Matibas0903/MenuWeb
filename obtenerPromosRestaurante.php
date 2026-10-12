<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/conexion.php";
header("content-type: application/json");

$idCategoria = 1;

try {
    $sql = "SELECT 
        pr.ID_PROMO,
        pr.NOMBRE_PROMO,
        pr.PRECIO,
        pr.IMAGEN_URL_PROMO,
        pr.DESCRIPCION,
        c.NOMBRE_CATEGORIA
    FROM promo pr
    INNER JOIN categoria c ON pr.ID_CATEGORIA = c.ID_CATEGORIA
    WHERE c.ID_CATEGORIA = :id
    ORDER BY c.NOMBRE_CATEGORIA, pr.NOMBRE_PROMO";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $idCategoria, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $menu = [];
    foreach ($rows as $r) {
        $cat = $r["NOMBRE_CATEGORIA"];
        if (!isset($menu[$cat])) {
            $menu[$cat] = ["categoria" => $cat, "promos" => []];
        }
        if ($r["NOMBRE_PROMO"]) {
            $menu[$cat]["promos"][] = [
                "nombre" => $r["NOMBRE_PROMO"],
                "precio" => $r["PRECIO"],
                "imagen" => $r["IMAGEN_URL_PROMO"],
                "descripcion" => $r["DESCRIPCION"]
            ];
        }
    }

    echo json_encode(array_values($menu));
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al cargar promos']);
    exit;
}

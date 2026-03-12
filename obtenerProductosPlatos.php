<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/conexion.php";
header("content-type: application/json");


$idCategoria = 1;

try {
    $sql = "SELECT 
        sc.ID_SUBCATEGORIA,
        sc.NOMBRE_SUBCATEGORIA,
        p.NOMBRE,
        p.PRECIO,
        p.IMAGEN_URL,
        p.DESCRIPCION
    FROM subcategoria sc
    INNER JOIN producto p ON p.ID_SUBCATEGORIA = sc.ID_SUBCATEGORIA 
    WHERE sc.ID_CATEGORIA = :id
    ORDER BY sc.NOMBRE_SUBCATEGORIA, p.NOMBRE"; //inner join para que si una sub categoria esta vacia no la traiga (caso contrario left join)

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $idCategoria, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // agrupar por subcategoria 
    $menu = [];
    foreach ($rows as $r) {
        $sub = $r["NOMBRE_SUBCATEGORIA"];
        if (!isset($menu[$sub])) {
            $menu[$sub] = ["subcategoria" => $sub, "productos" => []];
        }
        if ($r["NOMBRE"]) {
            $menu[$sub]["productos"][] = ["nombre" => $r["NOMBRE"], "precio" => $r["PRECIO"], "imagen" => $r["IMAGEN_URL"], "descripcion" => $r["DESCRIPCION"]];
        }
    }
    echo json_encode(array_values($menu));
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al cargar productos']);
    exit;
}

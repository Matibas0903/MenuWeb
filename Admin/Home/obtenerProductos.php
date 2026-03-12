<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/conexion.php";

header("Content-Type: Application/json");

$sql = "
SELECT 
    c.ID_CATEGORIA,
    c.NOMBRE_CATEGORIA AS categoria,
    s.ID_SUBCATEGORIA,
    s.NOMBRE_SUBCATEGORIA AS subcategoria,
    p.ID_PRODUCTO ,
    p.NOMBRE AS producto,
    p.DESCRIPCION,
    p.PRECIO,
    p.IMAGEN_URL
FROM categoria c
LEFT JOIN subcategoria s ON s.ID_CATEGORIA = c.ID_CATEGORIA
LEFT JOIN producto p ON p.ID_SUBCATEGORIA = s.ID_SUBCATEGORIA
ORDER BY c.NOMBRE_CATEGORIA, s.NOMBRE_SUBCATEGORIA, p.NOMBRE
";

$stmt = $conn->query($sql);

$menu = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $cat = $row["categoria"];
    $sub = $row["subcategoria"];

    if (!isset($menu[$cat])) {
        $menu[$cat] = [
            "categoria" => $cat,
            "subcategorias" => []
        ];
    }

    if ($sub) {

        if (!isset($menu[$cat]["subcategorias"][$sub])) {
            $menu[$cat]["subcategorias"][$sub] = [
                "id" => $row["ID_SUBCATEGORIA"],
                "nombre" => $sub,
                "categoria" => $row["ID_CATEGORIA"],
                "productos" => []
            ];
        }

        if ($row["producto"]) {
            $menu[$cat]["subcategorias"][$sub]["productos"][] = [
                "ID_PRODUCTO" => $row["ID_PRODUCTO"],
                "NOMBRE" => $row["producto"],
                "DESCRIPCION" => $row["DESCRIPCION"],
                "PRECIO" => $row["PRECIO"],
                "IMAGEN_URL" => $row["IMAGEN_URL"],
            ];
        }
    }
}

echo json_encode(array_values($menu));

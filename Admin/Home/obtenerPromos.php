<?php
// Protege el endpoint: exige sesión de admin (ver config/auth_guard.php)
require_once __DIR__ . "/../../config/auth_guard.php";
require_once __DIR__ . "/../../config/conexion.php";

header("Content-Type: Application/json");

try {
    $sql = "
SELECT 
*
FROM promo
";

    $stmt = $conn->query($sql);

    $promos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // La BD guarda /Resources/... (absoluta al dominio); para que la imagen
    // cargue también cuando la app corre en un subdirectorio (Laragon),
    // se le anteponer BASE_URL solo al responder, nunca al guardar.
    foreach ($promos as &$promo) {
        $promo["IMAGEN_URL_PROMO"] = urlImagen($promo["IMAGEN_URL_PROMO"] ?? null);
    }
    unset($promo);

    echo json_encode($promos);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "error al obtener promos"
    ]);
    exit;
}

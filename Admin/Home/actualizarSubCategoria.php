<?php

require_once ROOT_PATH . "/config/conexion.php";

header("Content-Type: application/json");

$id = $_POST["editSubId"] ?? null;
$nombre = trim($_POST["editSubNombre"] ?? "");
$categoria = $_POST["editCategoria"] ?? null;

$errores = [];

if (!$id) {
    $errores["editSubId"] = "ID inválido";
}

if ($nombre === "") {
    $errores["editSubNombre"] = "El nombre es obligatorio";
}

if (!$categoria) {
    $errores["editCategoria"] = "Categoria requerida";
}

/* verificar categoria */
$sql = "SELECT ID_CATEGORIA FROM categoria WHERE ID_CATEGORIA = :id";
$stmtCategoria = $conn->prepare($sql);
$stmtCategoria->bindParam(":id", $categoria, PDO::PARAM_INT);
$stmtCategoria->execute();

$existeCategoria = $stmtCategoria->fetch(PDO::FETCH_ASSOC);

if (!$existeCategoria) {
    $errores["editCategoria"] = "Categoria invalida";
}

if (!empty($errores)) {
    echo json_encode([
        "status" => "error",
        "errors" => $errores
    ]);
    exit;
}

try {

    $sql = "
        UPDATE subcategoria 
        SET 
            NOMBRE_SUBCATEGORIA = :nombre,
            ID_CATEGORIA = :categoria
        WHERE ID_SUBCATEGORIA = :id
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":categoria", $categoria, PDO::PARAM_INT);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    $stmt->execute();

    echo json_encode([
        "status" => "ok"
    ]);
} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

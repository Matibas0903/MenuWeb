<?php

require("C:/xampp\htdocs\Menu_Web\Menu_Web\conexion.php");

header("Content-Type: Application/json");

$nombre =  "";
$error = [];
try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $nombre = $_POST["nombreCategoria"] ?? null;

        if (empty($nombre)) {
            $error["nombreCategoria"] = "El nombre esta vacio";
            echo json_encode([
                "status" => "error",
                "errors" => $error
            ]);
            exit;
        }
        $nombre = ucfirst(trim($nombre));
        $sql = "SELECT NOMBRE_CATEGORIA FROM categoria where NOMBRE_CATEGORIA = :nombre";
        $stmtNombre = $conn->prepare($sql);
        $stmtNombre->bindParam(":nombre", $nombre);
        $stmtNombre->execute();

        $existeNombre = $stmtNombre->fetch(PDO::FETCH_ASSOC);

        if ($existeNombre) {
            $error["nombreCategoria"] = "esa categoria ya existe";
        } else {

            $sql = "INSERT INTO categoria (NOMBRE_CATEGORIA) Values (:nombre)";
            $stmtInsert = $conn->prepare($sql);
            $stmtInsert->bindParam(":nombre", $nombre);
            $stmtInsert->execute();

            echo json_encode(["status" => "ok"]);
            exit;
        }
        echo json_encode([
            "status" => "error",
            "errors" => $error
        ]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "error al insertar la categoria"
    ]);
    exit;
}

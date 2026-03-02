<?php

require("C:/xampp\htdocs\Menu_Web\conexion.php");

header("Content-Type: Application/json");

$nombre = $categoria = "";
$error = [];
try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $nombre = $_POST["nombreSubCategoria"] ?? null;
        $categoria = $_POST["categoria"] ?? null;

        if (empty($categoria) || $categoria === "") {
            $error["categoria"] = "Categoria invalida";
        }
        if (empty($nombre)) {
            $error["nombreSubCategoria"] = "El nombre esta vacio";
            echo json_encode([
                "status" => "error",
                "errors" => $error
            ]);
            exit;
        }



        $sql = "SELECT ID_CATEGORIA  FROM categoria where ID_CATEGORIA = :id";
        $stmtCategoria = $conn->prepare($sql);
        $stmtCategoria->bindParam(":id", $categoria, PDO::PARAM_INT);
        $stmtCategoria->execute();

        $existeCategoria = $stmtCategoria->fetch(PDO::FETCH_ASSOC);

        if (!$existeCategoria) {
            $error["categoria"] = "Categoria invalida";
        }


        $nombre = ucfirst(trim($nombre));
        $sql = "SELECT NOMBRE_SUBCATEGORIA FROM subcategoria where NOMBRE_SUBCATEGORIA = :nombre";
        $stmtNombre = $conn->prepare($sql);
        $stmtNombre->bindParam(":nombre", $nombre);
        $stmtNombre->execute();

        $existeNombre = $stmtNombre->fetch(PDO::FETCH_ASSOC);

        if ($existeNombre) {
            $error["nombreSubCategoria"] = "esa subcategoria ya existe";
        } else {

            $sql = "INSERT INTO subcategoria (ID_CATEGORIA, NOMBRE_SUBCATEGORIA) Values (:idCategoria, :nombre)";
            $stmtInsert = $conn->prepare($sql);
            $stmtInsert->bindParam(":nombre", $nombre);
            $stmtInsert->bindParam(":idCategoria", $categoria, PDO::PARAM_INT);
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
        "message" => "error al insertar la subcategoria"
    ]);
    exit;
}

<?php
require("C:/xampp\htdocs\Menu_Web\Menu_Web\conexion.php");

header("Content-Type: Application/json");

$nombrePromo = $precioPromo = $imagenPromo = "";

$errores = [];

try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombrePromo = $_POST["nombrePromo"] ?? null;
        $precioPromo = $_POST["precioPromo"] ?? null;
        $imagenPromo = $_FILES["imagenPromo"] ?? null;

        $sql = "SELECT NOMBRE_PROMO FROM promo where NOMBRE_PROMO = :nombre";
        $stmtNombre = $conn->prepare($sql);
        $stmtNombre->bindParam(":nombre", $nombrePromo);
        $stmtNombre->execute();

        $existeNombre = $stmtNombre->fetch(PDO::FETCH_ASSOC);


        if (empty($nombrePromo)) {
            $errores["nombrePromo"] = "El nombre esta vacio.";
        }
        if ($existeNombre) {
            $errores["nombrePromo"] = "Ya hay un producto con ese nombre";
            exit;
        }


        if ($precioPromo < 0 && empty($precioPromo) && !is_numeric($precioPromo)) {
            $errores["precioPromo"] = "Precio invalido";
        }

        if ($imagenPromo["error"] !== 0) {
            $errores["imagenPromo"] = "Debe seleccionar una imagen valida";
        } else if ($imagenPromo) {
            $tmp = $imagenPromo["tmp_name"];

            // nombre unico
            $nombreArchivo = time() . "_" . basename($imagenPromo["name"]);

            // carpeta destino absoluta (xampp)
            $carpetaDestino = __DIR__ . "/../../Resources/img_promos/";

            $rutaFinal = $carpetaDestino . $nombreArchivo;

            // mover archivo
            if (!move_uploaded_file($tmp, $rutaFinal)) {
                $errores["imagenPromo"] = "Error al guardar la imagen";
            }
        }

        if (empty($errores)) {
            $sql = "INSERT INTO promo(PRECIO, IMAGEN_URL_PROMO, NOMBRE_PROMO) VALUES (:precio, :imagenURL, :nombre)";
            $stmtInsert = $conn->prepare($sql);

            $stmtInsert->bindParam(":nombre", $nombrePromo);
            $stmtInsert->bindParam(":precio", $precioPromo);
            $stmtInsert->bindParam(":imagenURL", $rutaFinal);

            $stmtInsert->execute();

            //encode
            echo  json_encode(["status" => "ok"]);
            exit;
        }
        echo json_encode([
            "status" => "error",
            "errors" => $errores
        ]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "errors" => "error al insertar la promo"
    ]);
    exit;
}

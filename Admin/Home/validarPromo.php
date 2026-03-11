<?php
require_once ROOT_PATH . "/config/conexion.php";

header("Content-Type: Application/json");

$nombrePromo = $precioPromo = $imagenPromo = $descripcionPromo = "";

$errores = [];

try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombrePromo = $_POST["nombrePromo"] ?? null;
        $precioPromo = $_POST["precioPromo"] ?? null;
        $imagenPromo = $_FILES["imagenPromo"] ?? null;
        $categoriaPromo = $_POST["categoriaPromo"] ?? null;
        $descripcionPromo = $_POST["descripcionPromo"] ?? null;

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
        if (strlen($descripcionPromo) > 120) {
            $errores["descripcion"] = "La descripcion es muy larga";
        }
        if (empty($categoriaPromo) || $categoriaPromo === "") {
            $errores["categoriaPromo"] = "Categoria invalida";
        }

        $sql = "SELECT ID_CATEGORIA  FROM categoria where ID_CATEGORIA = :id";
        $stmtCategoria = $conn->prepare($sql);
        $stmtCategoria->bindParam(":id", $categoriaPromo, PDO::PARAM_INT);
        $stmtCategoria->execute();

        $existeCategoria = $stmtCategoria->fetch(PDO::FETCH_ASSOC);

        if (!$existeCategoria) {
            $errores["categoriaPromo"] = "Categoria invalida";
        }



        if ($precioPromo < 0 && empty($precioPromo) && !is_numeric($precioPromo)) {
            $errores["precioPromo"] = "Precio invalido";
        }



        $rutaFinal = null; // por defecto sin imagen

        if (isset($_FILES["imagenPromo"]) && $_FILES["imagenPromo"]["error"] !== UPLOAD_ERR_NO_FILE) {

            $tiposPermitidos = ["image/jpeg", "image/png", "image/webp", "image/avif"];

            if (!in_array($_FILES["imagenPromo"]["type"], $tiposPermitidos)) {
                $errores["imagenPromo"] = "Formato de imagen no permitido";
            }

            if ($_FILES["imagenPromo"]["size"] > 5 * 1024 * 1024) {
                $errores["imagenPromo"] = "La imagen es demasiado grande";
            }

            if ($_FILES["imagenPromo"]["error"] !== 0) {
                $errores["imagenPromo"] = "Error al subir la imagen";
            } else {

                $imagenPromo = $_FILES["imagenPromo"];
                $tmp = $imagenPromo["tmp_name"];

                // nombre unico
                $nombreArchivo = time() . "_" . basename($imagenPromo["name"]);

                // carpeta destino
                $carpetaDestino = __DIR__ . "/../../Resources/img_promos/";
                $rutaPublica = "/Menu_Web/Resources/img_promos/" . $nombreArchivo;

                $rutaFinal = $carpetaDestino . $nombreArchivo;
            }
        }

        if (empty($errores)) {

            $sql = "INSERT INTO promo(PRECIO, IMAGEN_URL_PROMO, NOMBRE_PROMO, ID_CATEGORIA, DESCRIPCION) 
            VALUES (:precio, :imagenURL, :nombre, :id_categoria, :descripcion)";

            $stmtInsert = $conn->prepare($sql);

            $stmtInsert->bindParam(":nombre", $nombrePromo);
            $stmtInsert->bindParam(":precio", $precioPromo);
            $stmtInsert->bindParam(":imagenURL", $rutaPublica);
            $stmtInsert->bindParam(":id_categoria", $categoriaPromo, PDO::PARAM_INT);
            $stmtInsert->bindParam(":descripcion", $descripcionPromo);

            $stmtInsert->execute();

            // mover archivo
            if ($rutaFinal !== null) {
                if (!move_uploaded_file($tmp, $rutaFinal)) {
                    $errores["imagenPromo"] = "Error al guardar la imagen";
                    echo json_encode([
                        "status" => "error",
                        "errors" => $errores
                    ]);
                    exit;
                }
            }

            echo json_encode(["status" => "ok"]);
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
        "errors" => "error al agregar la promo"
    ]);
    exit;
}

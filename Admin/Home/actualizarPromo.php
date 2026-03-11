<?php

require_once __DIR__ . "/../../config/conexion.php";

header("Content-Type: Application/json");

$nombre = $categoria = $precio = $imagen = $descripcion = $id = "";

$errores = [];

try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $nombre = $_POST["editNombrePromo"] ?? null;
        $categoria = $_POST["editCategoriaPromo"] ?? null;
        $precio = $_POST["editPrecioPromo"] ?? null;
        $imagen = $_FILES["editImagenPromo"] ?? null;
        $descripcion = $_POST["editDescripcionPromo"] ?? null;
        $id = $_POST["editPromoId"] ?? null;

        if (empty($nombre)) {
            $errores["editNombrePromo"] = "El nombre esta vacio";
        }

        if (strlen($descripcion) > 120) {
            $errores["editDescripcionPromo"] = "La descripcion es muy larga";
        }

        if (empty($categoria) || $categoria === "") {
            $errores["editCategoriaPromo"] = "Categoria invalida";
        }

        $sql = "SELECT ID_CATEGORIA FROM categoria WHERE ID_CATEGORIA = :id";
        $stmtCategoria = $conn->prepare($sql);
        $stmtCategoria->bindParam(":id", $categoria, PDO::PARAM_INT);
        $stmtCategoria->execute();

        $existeCategoria = $stmtCategoria->fetch(PDO::FETCH_ASSOC);

        if (!$existeCategoria) {
            $errores["editCategoriaPromo"] = "Categoria invalida";
        }

        if (empty($precio) || !is_numeric($precio) || $precio < 0) {
            $errores["editPrecioPromo"] = "Precio invalido";
        }

        $rutaFinal = null;

        if (isset($_FILES["editImagenPromo"]) && $_FILES["editImagenPromo"]["error"] !== UPLOAD_ERR_NO_FILE) {

            $tiposPermitidos = ["image/jpeg", "image/png", "image/webp", "image/avif"];

            if (!in_array($_FILES["editImagenPromo"]["type"], $tiposPermitidos)) {
                $errores["editImagenPromo"] = "Formato de imagen no permitido";
            }

            if ($_FILES["editImagenPromo"]["size"] > 5 * 1024 * 1024) {
                $errores["editImagenPromo"] = "La imagen es demasiado grande";
            }

            if ($_FILES["editImagenPromo"]["error"] !== 0) {
                $errores["editImagenPromo"] = "Error al subir la imagen";
            } else {

                $imagen = $_FILES["editImagenPromo"];
                $tmp = $imagen["tmp_name"];

                $nombreArchivo = time() . "_" . basename($imagen["name"]);

                $carpetaDestino = __DIR__ . "/../../Resources/img_promos/";
                $rutaPublica = "/Menu_Web/Resources/img_promos/" . $nombreArchivo;

                $rutaFinal = $carpetaDestino . $nombreArchivo;
            }
        }



        if (empty($errores)) {




            if ($rutaFinal !== null) {
                $sql = "UPDATE promo
                SET NOMBRE_PROMO = :nombre,
                    PRECIO = :precio,
                    IMAGEN_URL_PROMO = :imagenURL,
                    ID_CATEGORIA = :id_categoria,
                    DESCRIPCION = :descripcion
                WHERE ID_PROMO = :id_promo";
                $stmtUpdate = $conn->prepare($sql);
                $stmtUpdate->bindParam(":imagenURL", $rutaPublica);
            } else {
                $sql = "UPDATE promo
                SET NOMBRE_PROMO = :nombre,
                    PRECIO = :precio,
                    ID_CATEGORIA = :id_categoria,
                    DESCRIPCION = :descripcion
                WHERE ID_PROMO = :id_promo";
                $stmtUpdate = $conn->prepare($sql);
            }









            $stmtUpdate->bindParam(":id_promo", $id, PDO::PARAM_INT);
            $stmtUpdate->bindParam(":id_categoria", $categoria, PDO::PARAM_INT);
            $stmtUpdate->bindParam(":nombre", $nombre);
            $stmtUpdate->bindParam(":precio", $precio);

            $stmtUpdate->bindParam(":descripcion", $descripcion);


            $stmtUpdate->execute();


            if ($rutaFinal !== null) {


                if (!move_uploaded_file($tmp, $rutaFinal)) {


                    $errores["editImagenPromo"] = "Error al guardar la imagen";


                    echo json_encode([
                        "status" => "error",
                        "errors" => $errores
                    ]);
                    exit;
                }
            }


            echo json_encode([
                "status" => "ok",
                "promo" => $id
            ]);
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
        "errors" => "error al actualizar la promo"
    ]);
    exit;
}

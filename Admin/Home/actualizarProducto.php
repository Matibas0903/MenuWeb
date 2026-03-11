<?php


require_once $_SERVER['DOCUMENT_ROOT'] . "/config/conexion.php";

header("Content-Type: Application/json");

$nombre = $subCategoria = $precio = $imagen = $descripcion = $id = "";

$errores = [];

try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = $_POST["editNombre"] ?? null;
        $subCategoria = $_POST["editSubCategoria"] ?? null;
        $precio = $_POST["editPrecio"] ?? null;
        $imagen = $_FILES["editImagen"] ?? null;
        $descripcion = $_POST["editDescripcion"] ?? null;
        $id = $_POST["editId"] ?? null;



        if (empty($nombre)) {
            $errores["editNombre"] = "El nombre esta vacio";
        }




        if (strlen($descripcion) > 120) {
            $errores["editDescripcion"] = "La descripcion es muy larga";
        }

        if (empty($subCategoria) || $subCategoria === "") {
            $errores["editSubCategoria"] = "Sub-Categoria invalida";
        }

        $sql = "SELECT ID_SUBCATEGORIA FROM subCategoria where ID_SUBCATEGORIA = :id";
        $stmtCategoria = $conn->prepare($sql);
        $stmtCategoria->bindParam(":id", $subCategoria, PDO::PARAM_INT);
        $stmtCategoria->execute();

        $existeCategoria = $stmtCategoria->fetch(PDO::FETCH_ASSOC);

        if (!$existeCategoria) {
            $errores["editSubCategoria"] = "Categoria invalida";
        }
        if (empty($precio) || !is_numeric($precio) || $precio < 0) {
            $errores["editPrecio"] = "Precio invalido";
        }


        $rutaFinal = null; // por defecto sin imagen

        if (isset($_FILES["editImagen"]) && $_FILES["editImagen"]["error"] !== UPLOAD_ERR_NO_FILE) {

            $tiposPermitidos = ["image/jpeg", "image/png", "image/webp", "image/avif"];

            if (!in_array($_FILES["editImagen"]["type"], $tiposPermitidos)) {
                $errores["editImagen"] = "Formato de imagen no permitido";
            }

            if ($_FILES["editImagen"]["size"] > 5 * 1024 * 1024) {
                $errores["editImagen"] = "La imagen es demasiado grande";
            }

            if ($_FILES["editImagen"]["error"] !== 0) {
                $errores["editImagen"] = "Error al subir la imagen";
            } else {

                $imagen = $_FILES["editImagen"];
                $tmp = $imagen["tmp_name"];

                // nombre unico
                $nombreArchivo = time() . "_" . basename($imagen["name"]);

                // carpeta destino
                $carpetaDestino = __DIR__ . "/../../Resources/img_productos/";
                $rutaPublica = "/Menu_Web/Resources/img_productos/" . $nombreArchivo;
                $rutaFinal = $carpetaDestino . $nombreArchivo;
            }
        }

        if (empty($errores)) {
            if ($rutaFinal !== null) {

                $sql = "UPDATE producto
                    SET NOMBRE = :nombre,
                    PRECIO = :precio,
                    IMAGEN_URL = :imagenURL,
                    ID_SUBCATEGORIA = :id_subCategoria,
                    DESCRIPCION = :descripcion
                    WHERE ID_PRODUCTO = :id_producto";

                $stmtUpdate = $conn->prepare($sql);
                $stmtUpdate->bindParam(":imagenURL", $rutaPublica);
            } else {

                $sql = "UPDATE producto
                SET NOMBRE = :nombre,
                PRECIO = :precio,
                ID_SUBCATEGORIA = :id_subCategoria,
                DESCRIPCION = :descripcion
                WHERE ID_PRODUCTO = :id_producto";

                $stmtUpdate = $conn->prepare($sql);
            }




            $stmtUpdate->bindParam(":id_producto", $id, PDO::PARAM_INT);
            $stmtUpdate->bindParam(":id_subCategoria", $subCategoria, PDO::PARAM_INT);
            $stmtUpdate->bindParam(":nombre", $nombre);
            $stmtUpdate->bindParam(":precio", $precio);

            $stmtUpdate->bindParam(":descripcion", $descripcion);

            $stmtUpdate->execute();

            // mover archivo
            if ($rutaFinal !== null) {
                if (!move_uploaded_file($tmp, $rutaFinal)) {
                    $errores["editImagen"] = "Error al guardar la imagen";
                    echo json_encode([
                        "status" => "error",
                        "errors" => $errores
                    ]);
                    exit;
                }
            }

            //encode
            echo  json_encode([
                "status" => "ok",
                "producto" => $id
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
        "errors" => "error al insertar el producto"
    ]);
    exit;
}

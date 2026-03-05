<?php


require("C:/xampp\htdocs\Menu_Web\conexion.php");

header("Content-Type: Application/json");

$nombre = $subCategoria = $precio = $imagen = $descripcion = "";

$errores = [];

try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = $_POST["nombre"] ?? null;
        $subCategoria = $_POST["subCategoria"] ?? null;
        $precio = $_POST["precio"] ?? null;
        $imagen = $_FILES["imagen"] ?? null;
        $descripcion = $_POST["descripcion"] ?? null;


        $sql = "SELECT NOMBRE FROM  producto where NOMBRE = :nombre";
        $stmtNombre = $conn->prepare($sql);
        $stmtNombre->bindParam(":nombre", $nombre);
        $stmtNombre->execute();

        $existeNombre = $stmtNombre->fetch(PDO::FETCH_ASSOC);


        if (empty($nombre)) {
            $errores["nombre"] = "El nombre esta vacio";
        }
        if ($existeNombre) {
            $errores["nombre"] = "Ya hay un producto con ese nombre";
        }

        if (strlen($descripcion) > 120) {
            $errores["descripcion"] = "La descripcion es muy larga";
        }

        if (empty($subCategoria) || $subCategoria === "") {
            $errores["subCategoria"] = "Sub-Categoria invalida";
        }

        $sql = "SELECT ID_SUBCATEGORIA FROM subCategoria where ID_SUBCATEGORIA = :id";
        $stmtCategoria = $conn->prepare($sql);
        $stmtCategoria->bindParam(":id", $subCategoria, PDO::PARAM_INT);
        $stmtCategoria->execute();

        $existeCategoria = $stmtCategoria->fetch(PDO::FETCH_ASSOC);

        if (!$existeCategoria) {
            $errores["subCategoria"] = "Categoria invalida";
        }
        if (empty($precio) || !is_numeric($precio) || $precio < 0) {
            $errores["precio"] = "Precio invalido";
        }


        $rutaFinal = null; // por defecto sin imagen

        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] !== UPLOAD_ERR_NO_FILE) {

            $tiposPermitidos = ["image/jpeg", "image/png", "image/webp", "image/avif"];

            if (!in_array($_FILES["imagen"]["type"], $tiposPermitidos)) {
                $errores["imagen"] = "Formato de imagen no permitido";
            }

            if ($_FILES["imagen"]["size"] > 5 * 1024 * 1024) {
                $errores["imagen"] = "La imagen es demasiado grande";
            }

            if ($_FILES["imagen"]["error"] !== 0) {
                $errores["imagen"] = "Error al subir la imagen";
            } else {

                $imagen = $_FILES["imagen"];
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
            $sql = "INSERT INTO PRODUCTO(NOMBRE, PRECIO, IMAGEN_URL, ID_SUBCATEGORIA, DESCRIPCION) VALUES (:nombre, :precio, :imagenURL, :id_subCategoria, :descripcion)";
            $stmtInsert = $conn->prepare($sql);

            $stmtInsert->bindParam(":id_subCategoria", $subCategoria, PDO::PARAM_INT);
            $stmtInsert->bindParam(":nombre", $nombre);
            $stmtInsert->bindParam(":precio", $precio);
            $stmtInsert->bindParam(":imagenURL", $rutaPublica);
            $stmtInsert->bindParam(":descripcion", $descripcion);

            $stmtInsert->execute();

            // mover archivo
            if ($rutaFinal !== null) {
                if (!move_uploaded_file($tmp, $rutaFinal)) {
                    $errores["imagen"] = "Error al guardar la imagen";
                    echo json_encode([
                        "status" => "error",
                        "errors" => $errores
                    ]);
                    exit;
                }
            }

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
        "errors" => "error al insertar el producto"
    ]);
    exit;
}

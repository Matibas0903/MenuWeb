<?php


require("C:/xampp\htdocs\Menu_Web\conexion.php");

header("Content-Type: Application/json");

$nombre = $categoria = $precio = $imagen = "";

$errores = [];

try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = $_POST["nombre"] ?? null;
        $categoria = $_POST["categoria"] ?? null;
        $precio = $_POST["precio"] ?? null;
        $imagen = $_FILES["imagen"] ?? null;

        $sql = "SELECT NOMBRE FROM  producto where NOMBRE = :nombre";
        $stmtNombre = $conn->prepare($sql);
        $stmtNombre->bindParam(":nombre", $nombre);
        $stmtNombre->execute();

        $existeNombre = $stmtNombre->fetch(PDO::FETCH_ASSOC);


        if (empty($nombre)) {
            $errores["nombre"] = "El nombre esta vacio.";
        }
        if ($existeNombre) {
            $errores["nombre"] = "Ya hay un producto con ese nombre";
            exit;
        }
        if (empty($categoria) || $categoria === "") {
            $errores["categoria"] = "Categoria invalida";
        }

        $sql = "SELECT ID_CATEGORIA FROM categoria where ID_CATEGORIA = :id";
        $stmtCategoria = $conn->prepare($sql);
        $stmtCategoria->bindParam(":id", $categoria, PDO::PARAM_INT);
        $stmtCategoria->execute();

        $existeCategoria = $stmtCategoria->fetch(PDO::FETCH_ASSOC);

        if (!$existeCategoria) {
            $errores["categoria"] = "Categoria invalida";
        }
        if ($precio < 0 && empty($precio) && !is_numeric($precio)) {
            $errores["precio"] = "Precio invalido";
        }

        if (!$imagen || $imagen["error"] !== 0) {
            $errores["imagen"] = "Debe seleccionar una imagen valida";
        } else {
            $tmp = $imagen["tmp_name"];

            // nombre unico
            $nombreArchivo = time() . "_" . basename($imagen["name"]);

            // carpeta destino absoluta (xampp)
            $carpetaDestino = __DIR__ . "/../../Resources/img_productos/";

            $rutaFinal = $carpetaDestino . $nombreArchivo;

            // mover archivo
            if (!move_uploaded_file($tmp, $rutaFinal)) {
                $errores["imagen"] = "Error al guardar la imagen";
            }
        }

        if (empty($errores)) {
            $sql = "INSERT INTO PRODUCTO(ID_CATEGORIA, NOMBRE, PRECIO, IMAGEN_URL) VALUES (:id_categoria, :nombre, :precio, :imagenURL)";
            $stmtCategoria = $conn->prepare($sql);

            $stmtInsert->bindParam(":id_categoria", $categoria, PDO::PARAM_INT);
            $stmtInsert->bindParam(":nombre", $nombre);
            $stmtInsert->bindParam(":precio", $precio);
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
        "errors" => "error al insertar el producto"
    ]);
    exit;
}

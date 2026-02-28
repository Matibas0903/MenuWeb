<?php
session_start();

require("C:/xampp\htdocs\Menu_Web\conexion.php");

$nombre = $mail = $contraseña = "";

try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {


        header("Content-Type: application/json");

        $nombre = $_POST["nombre"] ?? null;
        $mail = $_POST["mail"] ?? null;
        $contraseña = $_POST["contraseña"] ?? null;
        $errores = [];

        //nombre
        if (empty($nombre)) {
            $errores["nombre"] = "El nombre es obligatorio";
        }

        //formato de mail
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errores["mail"] = "Mail inválido";
        }

        //formato de contraseña
        if (strlen($contraseña) < 6) {
            $errores["contraseña"] = "Mínimo 6 caracteres";
        }
        if (empty($errores)) {

            //mail duplicado
            $sqlCheckMail = "SELECT COUNT(*) FROM usuario 
             WHERE MAIL = :mail";

            $stmtCheckMail = $conn->prepare($sqlCheckMail);
            $stmtCheckMail->execute([
                ":mail" => $mail,
            ]);

            //nombre duplicado
            $sqlCheckNombre = "SELECT COUNT(*) FROM usuario 
             WHERE NOMBRE = :nombre";

            $stmtCheckNombre = $conn->prepare($sqlCheckNombre);
            $stmtCheckNombre->execute([
                ":nombre" => $nombre
            ]);


            $existeMail = $stmtCheckMail->fetchColumn();

            $existeNombre = $stmtCheckNombre->fetchColumn();


            if ($existeMail > 0) {
                $errores["mail"] = "El mail ingresado ya esta registrado";
            }

            if ($existeNombre > 0) {
                $errores["nombre"] = "El nombre de usuario ingresado ya esta registrado";
            }
        }

        if (empty($errores)) {

            $hash = password_hash($contraseña, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuario (NOMBRE, MAIL, CONTRASEÑA) VALUES (:nombre, :mail, :hash)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":mail", $mail);
            $stmt->bindParam(":hash", $hash);
            $stmt->execute();


            echo json_encode([
                "status" => "ok"
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
        "message" => "error al insertar usuario"
    ]);
}



?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin-Registro</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <script src="registro.js" defer> </script>
    <link rel="stylesheet" href="/Menu_Web/style.css">

</head>

<body>
    <div class="container vh-100 d-flex flex-column justify-content-center align-items-center">

        <div class="card p-4 shadow-lg login-card">
            <img src="/Menu_Web/Resources/logo.png" class="card-img-top logo-admin">
            <div class="card-body ">
                <form id="registro">
                    <div class="form-group mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingrese nombre de usuario..." class="form-control">
                        <div class="invalid-feedback" id="error-nombre"></div>

                    </div>

                    <div class="form-group mb-3">
                        <label for="mail" class="form-label">Mail</label>
                        <input type="email" id="mail" name="mail" placeholder="example@gmail.com" class=" form-control">
                        <div class="invalid-feedback" id="error-mail"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="contraseña" class="form-label">Contraseña</label>
                        <input type="password" id="contraseña" name="contraseña" placeholder="Ingrese una contraseña..." class="form-control">
                        <div class="invalid-feedback" id="error-contraseña"></div>
                    </div>

                    <div class="text-center mt-5">
                        <button id="btnRegistro" class="btn btn-form">Registrarse</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <a href="\Menu_Web\Admin\Login\login.php">Ya tengo Cuenta</a>
                </div>


            </div>

        </div>

    </div>
</body>
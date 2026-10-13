<?php
session_start();

require_once __DIR__ . "/../../config/conexion.php";

$nombre  = $contraseña = "";

try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {


        header("Content-Type: application/json");

        $nombre = $_POST["nombre"] ?? null;
        $contraseña = $_POST["contraseña"] ?? null;
        $errores = [];

        //nombre
        if (empty($nombre)) {
            $errores["nombre"] = "Nombre no ingresado";
        }
        if (empty($contraseña)) {
            $errores["contraseña"] = "Contraseña no ingresada";
        }
        if (empty($errores)) {
            //check usuario
            $sql = "SELECT * FROM usuario 
             WHERE NOMBRE = :nombre";

            $stmt = $conn->prepare($sql);
            $stmt->execute([":nombre" => $nombre]);

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                //check contraseña
                if (password_verify($contraseña, $usuario["CONTRASEÑA"])) {
                    $_SESSION["idUsuario"] = $usuario["ID_ADMIN"];
                    $_SESSION["nombre"] = $usuario["NOMBRE"];
                    $_SESSION["mail"] = $usuario["MAIL"];


                    //ENVIAR AL MENU DE ADMIN
                    echo json_encode([
                        "status" => "ok"
                    ]);
                    exit;
                } else {
                    $errores["general"] = "El Usuario o contraseña incorrectos";
                }
            } else {
                $errores["general"] = "El Usuario o contraseña incorrectos";
            }
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
    exit;
}

?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin-Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="login.js" defer> </script>
    <!-- Fase 1: CSS por ámbito (CONTEXT.md); rutas relativas al documento -->
    <link rel="stylesheet" href="../../css/theme.css">
    <link rel="stylesheet" href="../../css/admin.css">

</head>

<body>
    <div class="container vh-100 d-flex flex-column justify-content-center align-items-center">

        <div class="card p-4 shadow-lg login-card">
            <!-- BASE_URL (config/conexion.php) evita el 404 cuando la app corre en un subdirectorio -->
            <img src="<?= BASE_URL ?>/Resources/logo.png" class="card-img-top logo-admin">
            <div class="card-body ">
                <form id="login">
                    <div class="form-group mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingrese nombre de usuario..." class="form-control">
                        <div class="invalid-feedback" id="error-nombre"></div>

                    </div>

                    <div class="form-group mb-3">
                        <label for="contraseña" class="form-label">Contraseña</label>
                        <input type="password" id="contraseña" name="contraseña" placeholder="Ingrese una contraseña..." class="form-control">
                        <div class="invalid-feedback" id="error-contraseña"></div>
                    </div>

                    <div class="text-danger text-center mt-2 d-none" id="error-general"></div>
                    <div class="text-center mt-5">
                        <button id="btnLogin" class="btn btn-form">Iniciar sesion</button>
                    </div>
                </form>


            </div>

        </div>



        <!-- Container end -->
    </div>

</body>





<?php  /*

   <div class="text-center mt-3">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalReset">
                        Olvidé mi contraseña
                    </a>
                </div>


    <!-- Modal -->
    <div class="modal fade black-modal" id="modalReset">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Recuperar contraseña</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="formReset">
                        <input type="email" name="email" class="form-control"
                            placeholder="Tu email" required>
                        <button id="btnEnviar" class="btn mini-btn-form mt-3 w-100">
                            Enviar enlace
                        </button>
                    </form>

                    <div id="msgReset" class="mt-2"></div>
                </div>

            </div>
        </div>
    </div>
    */ ?>
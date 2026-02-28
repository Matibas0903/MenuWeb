<?php


require("C:/xampp\htdocs\Menu_Web\conexion.php");

$mail = "";


try {

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $mail = $_POST["email"] ?? null;

        header("Content-Type: application/json");

        if (!$mail) {
            echo json_encode([
                "status" => "error",
                "message" => "Mail no ingresado..."
            ]);
            exit;
        }


        $sql = "SELECT ID_ADMIN FROM usuario WHERE MAIL = :mail";
        $stmt = $conn->prepare($sql);
        $stmt->execute([":mail" => $mail]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            // generar token seguro
            $token = bin2hex(random_bytes(32));

            // expira en 1 hora
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // guardar en BD
            $sql = "UPDATE usuario
            SET RESET_TOKEN = :token,
                RESET_EXPIRE = :expira
            WHERE MAIL = :mail";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ":token" => $token,
                ":expira" => $expira,
                ":mail" => $mail
            ]);

            // link
            $link = "http://localhost/Menu_Web/reset_password.php?token=$token";
            mail(
                $mail,
                "Recuperar contraseña",
                "Haz clic en el enlace para cambiar tu contraseña:\n$link"
            );
        }
        echo json_encode([
            "status" => "ok",
            "message" => "Enlace enviado"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e
    ]);
}

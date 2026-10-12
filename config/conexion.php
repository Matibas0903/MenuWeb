<?php

$host = "sql208.infinityfree.com";
$port = "3306"; 
$db   = "if0_41587812_bar"; 
$user = "if0_41587812"; 
$pass = "Guemes462";
try {
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
        $user,
        $pass
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

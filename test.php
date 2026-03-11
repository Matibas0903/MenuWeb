<?php
require_once __DIR__ . "../config/conexion.php";

$stmt = $conn->query("SHOW TABLES");

$tablas = $stmt->fetchAll();

print_r($tablas);

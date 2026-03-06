<?php
    require("conexion.php");
    header("Content-Type: application/json");

    try{
        $sql = "SELECT * FROM producto WHERE categoria = 11";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($productos);
    }catch(PDOException $e){
        echo json_encode([
            "error" => $e->getMessage()
        ]);
    }
?>
<?php
session_start();
session_unset();   // Eliminar variables de sesión
session_destroy(); // Destruir la sesión
// Ruta relativa al documento: funciona en la raíz (hosting) y en subdirectorio (local)
header("Location: ../index.php");
exit;

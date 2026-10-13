<?php
/**
 * Guard de sesión para endpoints CRUD del panel admin.
 *
 * Uso: require_once __DIR__ . "/../../config/auth_guard.php"; al inicio
 * de cada endpoint protegido (validar*, actualizar*, eliminar*).
 *
 * Si no hay sesión de admin, responde 401 con JSON y corta la ejecución.
 * Los endpoints públicos de lectura del menú NO deben incluir esto.
 */

// Si la sesión ya fue iniciada por el script padre (p. ej. home.php),
// session_start() tiraría warning; por eso solo la iniciamos si no existe.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["idUsuario"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode([
        "status"  => "error",
        "message" => "No autorizado. Iniciá sesión para continuar."
    ]);
    exit;
}

<?php

/**
 * Conexión PDO a MySQL.
 *
 * Cambio 1 (CONTEXT.md): las credenciales ya NO están en texto plano en este
 * archivo. Se leen desde config/env.local.php, que está en .gitignore y por lo
 *  tanto no se sube al repositorio.
 *
 * Orden de carga:
 *   1. config/env.local.php (archivo local / subido al hosting a mano).
 *   2. Variables de entorno DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
 *      (útil en Docker o en el hosting si define vars de entorno).
 *
 * Rutas (fix de DOCUMENT_ROOT): este archivo vive en <proyecto>/config, así que
 * la raíz real del proyecto es dirname(__DIR__) — ej: C:\laragon\www\Menu_Web.
 * Usar $_SERVER['DOCUMENT_ROOT'] para los require/rutas NO sirve cuando el
 * docroot es el padre del proyecto (Laragon sirve desde C:\laragon\www y la app
 * está en www\Menu_Web): el require fallaba con un Fatal error en HTML y el
 * JS no podía parsear la respuesta como JSON.
 * Por eso:
 *   - PROJECT_ROOT  → rutas del filesystem (imágenes, uploads).
 *   - __DIR__       → require de este archivo desde cada endpoint.
 */

define("PROJECT_ROOT", dirname(__DIR__));

/**
 * Calcula la URL bajo la que corre la app dentro del servidor:
 *   - ""            → la app está en la raíz del docroot (InfinityFree, Docker).
 *   - "/Menu_Web"   → el servidor sirve desde el padre (Laragon/XAMPP: docroot
 *                     es www y la app vive en www/Menu_Web).
 * Se usa para armar las URLs de las imágenes, que en la BD están guardadas
 * como absolutas al dominio (/Resources/...): sin este prefijo, en local
 * apuntarían a http://localhost/Resources/... y devolvían 404.
 */
function calcularBaseUrl(): string
{
    $raiz = str_replace("\\", "/", PROJECT_ROOT);

    // 1) Restando el DOCUMENT_ROOT a la raíz del proyecto
    $docRoot = str_replace("\\", "/", rtrim($_SERVER["DOCUMENT_ROOT"] ?? "", "/\\"));
    if ($docRoot !== "" && stripos($raiz, $docRoot) === 0) {
        return rtrim(substr($raiz, strlen($docRoot)), "/");
    }

    // 2) Fallback: restando al SCRIPT_NAME la ruta relativa del script actual
    $scriptFs  = str_replace("\\", "/", $_SERVER["SCRIPT_FILENAME"] ?? "");
    $scriptUrl = str_replace("\\", "/", $_SERVER["SCRIPT_NAME"] ?? "");
    if ($scriptFs !== "" && $scriptUrl !== "" && stripos($scriptFs, $raiz) === 0) {
        $rel = substr($scriptFs, strlen($raiz));
        if (strlen($scriptUrl) > strlen($rel)) {
            return rtrim(substr($scriptUrl, 0, strlen($scriptUrl) - strlen($rel)), "/");
        }
    }

    // 3) Sin contexto web (CLI): se asume raíz
    return "";
}

define("BASE_URL", calcularBaseUrl());

/**
 * Convierte la ruta guardada en la BD (/Resources/img_productos/x.webp) en una
 * URL utilizable por el navegador: BASE_URL + ruta. Vacío si no hay imagen.
 * Ojo: la BD conserva el formato "/Resources/..." porque así lo necesitan los
 * borrados (PROJECT_ROOT . IMAGEN_URL); este helper es solo para el HTML/JS.
 */
function urlImagen(?string $ruta): string
{
    if ($ruta === null || $ruta === "") {
        return "";
    }

    return BASE_URL . "/" . ltrim(str_replace("\\", "/", $ruta), "/");
}

/**
 * Corta la ejecución con una respuesta JSON de error.
 * Todos los consumidores de conexion.php hablan JSON, así que un fallo de
 * credenciales/conexión llega al catch del fetch y la UI muestra el mensaje
 * de error estándar en vez de romper el parseo.
 */
function falloConexion(string $mensaje): void
{
    if (!headers_sent()) {
        http_response_code(500);
        header("Content-Type: application/json");
    }
    echo json_encode(["status" => "error", "message" => $mensaje]);
    exit;
}

$archivoLocal = __DIR__ . "/env.local.php";

if (file_exists($archivoLocal)) {
    $cfg = require $archivoLocal;
} else {
    $cfg = [
        "host" => getenv("DB_HOST") ?: "",
        "port" => getenv("DB_PORT") ?: "3306",
        "db"   => getenv("DB_NAME") ?: "",
        "user" => getenv("DB_USER") ?: "",
        "pass" => getenv("DB_PASS") ?: "",
    ];
}

if (empty($cfg["host"]) || empty($cfg["db"]) || empty($cfg["user"])) {
    error_log("Faltan credenciales de BD: copiar config/env.local.example.php a config/env.local.php");
    falloConexion("Faltan credenciales de base de datos.");
}

$host = $cfg["host"];
$port = $cfg["port"] ?? "3306";
$db   = $cfg["db"];
$user = $cfg["user"];
$pass = $cfg["pass"] ?? "";

try {
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
        $user,
        $pass
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // No exponemos detalles de la conexión al usuario final
    error_log("Error de conexión a la BD: " . $e->getMessage());
    falloConexion("Error de conexión a la base de datos.");
}

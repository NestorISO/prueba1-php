<?php
/**
 * Configuración y funciones comunes de la API REST de la rifa.
 */
session_start();

header('Content-Type: application/json; charset=utf-8');

define('USUARIO_API', 'userupla');
define('CLAVE_API', 'upla123');
define('ARCHIVO_PARTICIPANTES', __DIR__ . '/data/participantes.json');
define('ARCHIVO_PREMIOS', __DIR__ . '/data/premios.json');

/** Envía una respuesta JSON con el código HTTP indicado y termina. */
function responder(int $codigo, array $datos): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/** Verifica que la petición use el método HTTP esperado. */
function exigirMetodo(string $metodo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $metodo) {
        responder(405, ['ok' => false, 'mensaje' => "Método no permitido. Use $metodo."]);
    }
}

/** Corta la petición si no existe una sesión iniciada (servicios protegidos). */
function exigirSesion(): void
{
    if (empty($_SESSION['api_usuario'])) {
        responder(401, ['ok' => false, 'mensaje' => 'Acceso denegado. Debe iniciar sesión.']);
    }
}

/** Lee un archivo JSON y lo devuelve como arreglo. */
function leerJson(string $archivo): array
{
    if (!file_exists($archivo)) {
        return [];
    }
    $datos = json_decode(file_get_contents($archivo), true);
    return is_array($datos) ? $datos : [];
}

/** Guarda un arreglo en un archivo JSON. */
function guardarJson(string $archivo, array $datos): bool
{
    $json = json_encode(array_values($datos), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return file_put_contents($archivo, $json, LOCK_EX) !== false;
}

/**
 * Obtiene los datos enviados por POST. Acepta form-data / x-www-form-urlencoded
 * y también un cuerpo JSON (raw) desde Postman.
 */
function datosPost(): array
{
    if (!empty($_POST)) {
        return $_POST;
    }
    $json = json_decode(file_get_contents('php://input'), true);
    return is_array($json) ? $json : [];
}

/** Valida que el número de rifa sea un entero positivo. */
function numeroRifaValido($numero): bool
{
    return filter_var($numero, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
}

/** Revisa si un número de rifa ya está asignado (opcionalmente ignorando un ID). */
function numeroOcupado(array $participantes, int $numero, ?int $ignorarId = null): bool
{
    foreach ($participantes as $p) {
        if ((int)$p['numero_rifa'] === $numero && (int)$p['id'] !== $ignorarId) {
            return true;
        }
    }
    return false;
}

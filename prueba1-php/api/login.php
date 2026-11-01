<?php
/**
 * 1. Inicio de sesión — POST
 * Parámetros: usuario, clave
 */
require __DIR__ . '/config.php';
exigirMetodo('POST');

$datos   = datosPost();
$usuario = trim($datos['usuario'] ?? '');
$clave   = trim($datos['clave'] ?? '');

if ($usuario === '' || $clave === '') {
    responder(400, ['ok' => false, 'mensaje' => 'Debe enviar usuario y clave.']);
}

if ($usuario === USUARIO_API && $clave === CLAVE_API) {
    session_regenerate_id(true);
    $_SESSION['api_usuario'] = $usuario;
    responder(200, ['ok' => true, 'mensaje' => 'Sesión iniciada correctamente.', 'usuario' => $usuario]);
}

responder(401, ['ok' => false, 'mensaje' => 'Usuario o contraseña incorrectos.']);

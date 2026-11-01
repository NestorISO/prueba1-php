<?php
/**
 * 7. Cerrar sesión — GET
 */
require __DIR__ . '/config.php';
exigirMetodo('GET');

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();

responder(200, ['ok' => true, 'mensaje' => 'Sesión cerrada correctamente.']);

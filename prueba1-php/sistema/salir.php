<?php
/**
 * 7. salir.php — Cierre de sesión
 * Pasos según el manual de PHP (session_destroy):
 *   1) session_start()       para retomar la sesión actual
 *   2) $_SESSION = []        vaciar las variables de sesión
 *   3) setcookie(...)        borrar la cookie de sesión del navegador
 *   4) session_destroy()     destruir la sesión en el servidor
 */
session_start();

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();

// Sesión nueva y vacía solo para mostrar el aviso de cierre en el login
session_id(session_create_id());
session_start();
$_SESSION['flash'] = ['tipo' => 'ok', 'mensaje' => 'Sesión cerrada correctamente.'];

header('Location: index.php');
exit;

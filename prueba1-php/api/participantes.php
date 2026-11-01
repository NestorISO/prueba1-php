<?php
/**
 * 2. Listar participantes — GET (protegido)
 */
require __DIR__ . '/config.php';
exigirMetodo('GET');
exigirSesion();

$participantes = leerJson(ARCHIVO_PARTICIPANTES);

responder(200, [
    'ok'            => true,
    'total'         => count($participantes),
    'participantes' => $participantes,
]);

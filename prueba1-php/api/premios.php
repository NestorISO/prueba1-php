<?php
/**
 * 3. Listar premios — GET (protegido)
 */
require __DIR__ . '/config.php';
exigirMetodo('GET');
exigirSesion();

$premios = leerJson(ARCHIVO_PREMIOS);

responder(200, [
    'ok'      => true,
    'total'   => count($premios),
    'premios' => $premios,
]);

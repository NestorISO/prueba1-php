<?php
/**
 * 6. Eliminar participante — GET (protegido)
 * Uso: eliminar.php?id=3
 */
require __DIR__ . '/config.php';
exigirMetodo('GET');
exigirSesion();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    responder(400, ['ok' => false, 'mensaje' => 'Debe enviar un id válido por GET (?id=).']);
}

$participantes = leerJson(ARCHIVO_PARTICIPANTES);

foreach ($participantes as $i => $p) {
    if ((int)$p['id'] === $id) {
        $eliminado = $p;
        unset($participantes[$i]);
        if (!guardarJson(ARCHIVO_PARTICIPANTES, $participantes)) {
            responder(500, ['ok' => false, 'mensaje' => 'No se pudo guardar el archivo.']);
        }
        responder(200, ['ok' => true, 'mensaje' => 'Participante eliminado.', 'participante' => $eliminado]);
    }
}

responder(404, ['ok' => false, 'mensaje' => "No existe un participante con id $id."]);

<?php
/**
 * 5. Actualizar participante — POST (protegido)
 * Parámetros: id (obligatorio) y uno o más de: nombre, apellido, numero_rifa
 */
require __DIR__ . '/config.php';
exigirMetodo('POST');
exigirSesion();

$datos = datosPost();
$id    = filter_var($datos['id'] ?? '', FILTER_VALIDATE_INT);

if ($id === false) {
    responder(400, ['ok' => false, 'mensaje' => 'Debe enviar un id válido.']);
}

$participantes = leerJson(ARCHIVO_PARTICIPANTES);

foreach ($participantes as $i => $p) {
    if ((int)$p['id'] !== $id) {
        continue;
    }

    if (isset($datos['nombre']) && trim($datos['nombre']) !== '') {
        $participantes[$i]['nombre'] = trim($datos['nombre']);
    }
    if (isset($datos['apellido']) && trim($datos['apellido']) !== '') {
        $participantes[$i]['apellido'] = trim($datos['apellido']);
    }
    if (isset($datos['numero_rifa']) && $datos['numero_rifa'] !== '') {
        if (!numeroRifaValido($datos['numero_rifa'])) {
            responder(400, ['ok' => false, 'mensaje' => 'numero_rifa debe ser un entero mayor a 0.']);
        }
        if (numeroOcupado($participantes, (int)$datos['numero_rifa'], $id)) {
            responder(409, ['ok' => false, 'mensaje' => 'Ese número de rifa ya está asignado.']);
        }
        $participantes[$i]['numero_rifa'] = (int)$datos['numero_rifa'];
    }

    if (!guardarJson(ARCHIVO_PARTICIPANTES, $participantes)) {
        responder(500, ['ok' => false, 'mensaje' => 'No se pudo guardar el archivo.']);
    }
    responder(200, ['ok' => true, 'mensaje' => 'Participante actualizado.', 'participante' => $participantes[$i]]);
}

responder(404, ['ok' => false, 'mensaje' => "No existe un participante con id $id."]);

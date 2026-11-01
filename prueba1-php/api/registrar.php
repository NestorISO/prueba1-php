<?php
/**
 * 4. Registrar participante — POST (protegido)
 * Parámetros: nombre, apellido, numero_rifa
 */
require __DIR__ . '/config.php';
exigirMetodo('POST');
exigirSesion();

$datos      = datosPost();
$nombre     = trim($datos['nombre'] ?? '');
$apellido   = trim($datos['apellido'] ?? '');
$numeroRifa = $datos['numero_rifa'] ?? '';

if ($nombre === '' || $apellido === '' || $numeroRifa === '') {
    responder(400, ['ok' => false, 'mensaje' => 'Debe enviar nombre, apellido y numero_rifa.']);
}
if (!numeroRifaValido($numeroRifa)) {
    responder(400, ['ok' => false, 'mensaje' => 'numero_rifa debe ser un entero mayor a 0.']);
}

$participantes = leerJson(ARCHIVO_PARTICIPANTES);

if (numeroOcupado($participantes, (int)$numeroRifa)) {
    responder(409, ['ok' => false, 'mensaje' => "El número de rifa $numeroRifa ya está asignado."]);
}

// Nuevo ID = mayor ID existente + 1
$ids   = array_column($participantes, 'id');
$nuevo = [
    'id'          => $ids ? max($ids) + 1 : 1,
    'nombre'      => $nombre,
    'apellido'    => $apellido,
    'numero_rifa' => (int)$numeroRifa,
];
$participantes[] = $nuevo;

if (!guardarJson(ARCHIVO_PARTICIPANTES, $participantes)) {
    responder(500, ['ok' => false, 'mensaje' => 'No se pudo guardar el archivo.']);
}

responder(201, ['ok' => true, 'mensaje' => 'Participante registrado.', 'participante' => $nuevo]);

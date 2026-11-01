<?php
/**
 * 4. buscar.php — Búsqueda de participantes (GET)
 * Deben coincidir las 4 condiciones: nombre, apellido, ciudad y categoría.
 */
require __DIR__ . '/includes.php';
protegerPagina();

// Participantes definidos previamente (mínimo 4)
$participantes = [
    ['nombre' => 'Universidad', 'apellido' => 'Playa Ancha', 'ciudad' => 'Valparaiso',   'categoria' => 'Grupo A'],
    ['nombre' => 'Camila',      'apellido' => 'Rojas',       'ciudad' => 'Viña del Mar', 'categoria' => 'Grupo B'],
    ['nombre' => 'Matías',      'apellido' => 'González',    'ciudad' => 'Quilpué',      'categoria' => 'Grupo A'],
    ['nombre' => 'Valentina',   'apellido' => 'Muñoz',       'ciudad' => 'Santiago',     'categoria' => 'Grupo C'],
];

/** Normaliza texto para comparar sin importar mayúsculas ni espacios extra. */
function normalizar(string $texto): string
{
    return mb_strtolower(preg_replace('/\s+/', ' ', trim($texto)), 'UTF-8');
}

$nombre    = $_GET['nombre'] ?? '';
$apellido  = $_GET['apellido'] ?? '';
$ciudad    = $_GET['ciudad'] ?? '';
$categoria = $_GET['categoria'] ?? '';

$seBusco   = isset($_GET['nombre']);
$resultado = null;

if ($seBusco) {
    $encontrado = false;
    foreach ($participantes as $p) {
        if (normalizar($p['nombre'])    === normalizar($nombre)
         && normalizar($p['apellido'])  === normalizar($apellido)
         && normalizar($p['ciudad'])    === normalizar($ciudad)
         && normalizar($p['categoria']) === normalizar($categoria)) {
            $encontrado = true;
            break;
        }
    }
    $resultado = $encontrado ? 'Participante encontrado' : 'Participante no encontrado';
}

encabezado('Búsqueda de participantes');
?>
<form method="GET" action="buscar.php" class="tarjeta">
    <label for="nombre">Nombre</label>
    <input type="text" id="nombre" name="nombre" value="<?= e($nombre) ?>" required>

    <label for="apellido">Apellido</label>
    <input type="text" id="apellido" name="apellido" value="<?= e($apellido) ?>" required>

    <label for="ciudad">Ciudad</label>
    <input type="text" id="ciudad" name="ciudad" value="<?= e($ciudad) ?>" required>

    <label for="categoria">Categoría</label>
    <select id="categoria" name="categoria" required>
        <option value="">-- Seleccione --</option>
        <?php foreach (['Grupo A', 'Grupo B', 'Grupo C'] as $cat): ?>
            <option value="<?= $cat ?>" <?= $categoria === $cat ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Buscar</button>
</form>

<?php if ($resultado !== null): ?>
    <?php $ok = ($resultado === 'Participante encontrado'); ?>
    <div class="resultado <?= $ok ? 'ok' : 'error' ?>" role="status">
        <span class="alerta-icono"><?= $ok ? '✓' : '✕' ?></span>
        <?= e($resultado) ?>
    </div>
<?php endif; ?>
<?php pie(); ?>

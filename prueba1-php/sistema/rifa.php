<?php
/**
 * 3. rifa.php — Reserva de números (POST + sesiones)
 *
 * Los números reservados se guardan en $_SESSION['numeros'].
 * Tras cada POST se redirige con GET (patrón Post/Redirect/Get), así al
 * presionar F5 no se reenvía el formulario y la información no se pierde.
 */
require __DIR__ . '/includes.php';
protegerPagina();

const NUMERO_MIN = 1;
const NUMERO_MAX = 100;

if (!isset($_SESSION['numeros'])) {
    $_SESSION['numeros'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $numero = filter_var($_POST['numero'] ?? '', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => NUMERO_MIN, 'max_range' => NUMERO_MAX],
        ]);

        if ($numero === false) {
            flash('error', 'Ingresa un número válido entre ' . NUMERO_MIN . ' y ' . NUMERO_MAX . '.');
        } elseif (in_array($numero, $_SESSION['numeros'], true)) {
            flash('error', "El número $numero ya está reservado.");
        } else {
            $_SESSION['numeros'][] = $numero;
            sort($_SESSION['numeros']);
            flash('ok', "Número $numero reservado.");
        }
    } elseif ($accion === 'quitar') {
        $numero = filter_var($_POST['numero'] ?? '', FILTER_VALIDATE_INT);
        $pos    = array_search($numero, $_SESSION['numeros'], true);

        if ($pos === false) {
            flash('error', 'Ese número no está en tu reserva.');
        } else {
            unset($_SESSION['numeros'][$pos]);
            $_SESSION['numeros'] = array_values($_SESSION['numeros']);
            flash('ok', "Número $numero quitado.");
        }
    } elseif ($accion === 'vaciar') {
        $_SESSION['numeros'] = [];
        flash('ok', 'Se quitaron todos los números.');
    }

    header('Location: rifa.php');
    exit;
}

// La cantidad es el tamaño del arreglo, por lo que nunca puede ser negativa
$cantidad = max(0, count($_SESSION['numeros']));
$total    = $cantidad * PRECIO_NUMERO;

encabezado('Reserva de números');
?>
<div class="resumen">
    <div><span>Números reservados</span><strong><?= $cantidad ?></strong></div>
    <div><span>Precio por número</span><strong><?= pesos(PRECIO_NUMERO) ?></strong></div>
    <div><span>Total a pagar</span><strong><?= pesos($total) ?></strong></div>
</div>
<p class="formula">Total = <?= $cantidad ?> × <?= pesos(PRECIO_NUMERO) ?> = <?= pesos($total) ?></p>

<form method="POST" action="rifa.php" class="tarjeta en-linea">
    <input type="hidden" name="accion" value="agregar">
    <label for="numero">Número a reservar (<?= NUMERO_MIN ?>–<?= NUMERO_MAX ?>)</label>
    <input type="number" id="numero" name="numero" min="<?= NUMERO_MIN ?>" max="<?= NUMERO_MAX ?>" required>
    <button type="submit">Agregar</button>
</form>

<h2>Mis números</h2>
<?php if ($cantidad === 0): ?>
    <p class="vacio">Aún no has reservado números.</p>
<?php else: ?>
    <div class="numeros">
        <?php foreach ($_SESSION['numeros'] as $n): ?>
            <form method="POST" action="rifa.php" class="ficha">
                <input type="hidden" name="accion" value="quitar">
                <input type="hidden" name="numero" value="<?= $n ?>">
                <span><?= str_pad($n, 3, '0', STR_PAD_LEFT) ?></span>
                <button type="submit" title="Quitar número <?= $n ?>">✕</button>
            </form>
        <?php endforeach; ?>
    </div>
    <form method="POST" action="rifa.php">
        <input type="hidden" name="accion" value="vaciar">
        <button type="submit" class="secundario">Quitar todos</button>
    </form>
<?php endif; ?>
<?php pie(); ?>

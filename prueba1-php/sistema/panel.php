<?php
/**
 * 2. panel.php — Área protegida (página principal)
 */
require __DIR__ . '/includes.php';
protegerPagina();

$reservados = count($_SESSION['numeros'] ?? []);

encabezado('Panel principal');
?>
<p class="bienvenida">Bienvenido, <strong><?= e($_SESSION['usuario']) ?></strong>. Selecciona una opción:</p>

<div class="grilla">
    <a class="tarjeta opcion" href="rifa.php">
        <h2>Reservar números</h2>
        <p>Tienes <?= $reservados ?> número(s) reservado(s).</p>
    </a>
    <a class="tarjeta opcion" href="buscar.php">
        <h2>Buscar participante</h2>
        <p>Busca por nombre, apellido, ciudad y categoría.</p>
    </a>
    <a class="tarjeta opcion" href="subir_comprobante.php">
        <h2>Subir comprobante</h2>
        <p>Carga el comprobante de pago al servidor.</p>
    </a>
    <a class="tarjeta opcion" href="archivos.php">
        <h2>Ver comprobantes</h2>
        <p>Lista de archivos cargados.</p>
    </a>
    <a class="tarjeta opcion salir" href="salir.php">
        <h2>Cerrar sesión</h2>
        <p>Finaliza la sesión actual.</p>
    </a>
</div>
<?php pie(); ?>

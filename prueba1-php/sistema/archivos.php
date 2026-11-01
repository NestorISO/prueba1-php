<?php
/**
 * 6. archivos.php — Visualización de archivos cargados (protegido)
 */
require __DIR__ . '/includes.php';
protegerPagina();

$archivos = [];
if (is_dir(CARPETA_COMPROBANTES)) {
    foreach (scandir(CARPETA_COMPROBANTES) as $nombre) {
        $ruta = CARPETA_COMPROBANTES . $nombre;
        // Se omiten ".", ".." y archivos ocultos como .gitkeep
        if ($nombre[0] === '.' || !is_file($ruta)) {
            continue;
        }
        $archivos[] = [
            'nombre' => $nombre,
            'tamano' => filesize($ruta),
            'fecha'  => filemtime($ruta),
        ];
    }
}

encabezado('Comprobantes cargados');
?>
<?php if (empty($archivos)): ?>
    <p class="vacio">No hay archivos cargados todavía. <a href="subir_comprobante.php">Subir uno</a>.</p>
<?php else: ?>
    <p>Total de archivos: <strong><?= count($archivos) ?></strong></p>
    <div class="tabla-envoltura"><table>
        <thead>
            <tr><th>#</th><th>Nombre del archivo</th><th>Tamaño</th><th>Fecha de carga</th></tr>
        </thead>
        <tbody>
        <?php foreach ($archivos as $i => $a): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><a href="comprobantes/<?= rawurlencode($a['nombre']) ?>" target="_blank"><?= e($a['nombre']) ?></a></td>
                <td><?= number_format($a['tamano'] / 1024, 1, ',', '.') ?> KB</td>
                <td><?= date('d-m-Y H:i', $a['fecha']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
<?php endif; ?>
<?php pie(); ?>

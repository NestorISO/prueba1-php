<?php
/**
 * 5. subir_comprobante.php — Carga de archivos (protegido)
 */
require __DIR__ . '/includes.php';
protegerPagina();

const EXTENSIONES_PERMITIDAS = ['pdf', 'jpg', 'jpeg', 'png'];
const TAMANO_MAXIMO = 5 * 1024 * 1024; // 5 MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archivo = $_FILES['comprobante'] ?? null;

    if (!$archivo || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        flash('error', 'Error: no se seleccionó ningún archivo.');
    } elseif ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores = [
            UPLOAD_ERR_INI_SIZE   => 'el archivo supera el tamaño máximo del servidor.',
            UPLOAD_ERR_FORM_SIZE  => 'el archivo supera el tamaño máximo del formulario.',
            UPLOAD_ERR_PARTIAL    => 'el archivo se subió solo parcialmente.',
            UPLOAD_ERR_NO_TMP_DIR => 'falta la carpeta temporal del servidor.',
            UPLOAD_ERR_CANT_WRITE => 'no se pudo escribir en el disco.',
        ];
        flash('error', 'Error: ' . ($errores[$archivo['error']] ?? 'error desconocido al subir.'));
    } else {
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, EXTENSIONES_PERMITIDAS, true)) {
            flash('error', 'Error: solo se permiten archivos ' . implode(', ', EXTENSIONES_PERMITIDAS) . '.');
        } elseif ($archivo['size'] > TAMANO_MAXIMO) {
            flash('error', 'Error: el archivo supera los 5 MB.');
        } else {
            if (!is_dir(CARPETA_COMPROBANTES)) {
                mkdir(CARPETA_COMPROBANTES, 0755, true);
            }
            // Nombre seguro: se quitan caracteres raros y se agrega fecha para no sobrescribir
            $base   = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($archivo['name'], PATHINFO_FILENAME));
            $nombre = date('Ymd_His') . '_' . $base . '.' . $extension;

            if (move_uploaded_file($archivo['tmp_name'], CARPETA_COMPROBANTES . $nombre)) {
                flash('ok', "Archivo \"$nombre\" cargado correctamente.");
            } else {
                flash('error', 'Error: no se pudo guardar el archivo en el servidor.');
            }
        }
    }

    header('Location: subir_comprobante.php');
    exit;
}

encabezado('Subir comprobante');
?>
<form method="POST" action="subir_comprobante.php" enctype="multipart/form-data" class="tarjeta">
    <label for="comprobante">Selecciona el comprobante (PDF, JPG o PNG, máx. 5 MB)</label>
    <input type="file" id="comprobante" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" required>
    <button type="submit">Subir archivo</button>
</form>
<p><a href="archivos.php">Ver comprobantes cargados →</a></p>
<?php pie(); ?>

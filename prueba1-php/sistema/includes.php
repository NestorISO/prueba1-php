<?php
/**
 * Funciones comunes del sistema web de la rifa.
 */
session_start();

define('USUARIO_VALIDO', 'userupla');
define('CLAVE_VALIDA', 'upla123');
define('PRECIO_NUMERO', 2000);
define('CARPETA_COMPROBANTES', __DIR__ . '/comprobantes/');

/**
 * Si no hay sesión iniciada, deja un aviso y envía al usuario a index.php,
 * donde se muestra el mensaje explicando por qué no pudo entrar.
 */
function protegerPagina(): void
{
    if (empty($_SESSION['usuario'])) {
        $pagina = basename($_SERVER['PHP_SELF']);
        flash('aviso', "Acceso restringido: debes iniciar sesión para ingresar a $pagina.");
        header('Location: index.php');
        exit;
    }
}

/** Escapa texto para mostrarlo de forma segura en HTML. */
function e($texto): string
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

/** Formatea un monto en pesos chilenos. */
function pesos(int $monto): string
{
    return '$' . number_format($monto, 0, ',', '.');
}

/** Guarda un mensaje para mostrarlo una sola vez tras una redirección. */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

/** Muestra (y borra) el mensaje flash pendiente. */
function mostrarFlash(): void
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        $iconos = ['ok' => '✓', 'error' => '✕', 'aviso' => '!'];
        $icono  = $iconos[$f['tipo']] ?? 'i';
        echo '<div class="alerta ' . e($f['tipo']) . '" role="alert">'
           . '<span class="alerta-icono">' . $icono . '</span>'
           . '<span>' . e($f['mensaje']) . '</span></div>';
        unset($_SESSION['flash']);
    }
}

/** Encabezado HTML común. */
function encabezado(string $titulo, bool $conMenu = true): void
{
    $actual = basename($_SERVER['PHP_SELF']);
    $enlaces = [
        'panel.php'             => 'Panel',
        'rifa.php'              => 'Reservar números',
        'buscar.php'            => 'Buscar',
        'subir_comprobante.php' => 'Subir comprobante',
        'archivos.php'          => 'Comprobantes',
    ];
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | Rifa UPLA</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="<?= $conMenu ? 'con-menu' : 'sin-menu' ?>">
<?php if ($conMenu): ?>
<header class="barra">
    <a href="panel.php" class="marca"><span class="logo">R</span>Rifa UPLA</a>
    <nav class="menu">
        <?php foreach ($enlaces as $archivo => $texto): ?>
            <a href="<?= $archivo ?>" class="<?= $archivo === $actual ? 'activo' : '' ?>"><?= $texto ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="usuario">
        <span class="avatar"><?= e(strtoupper(substr($_SESSION['usuario'] ?? '?', 0, 1))) ?></span>
        <span class="nombre-usuario"><?= e($_SESSION['usuario'] ?? '') ?></span>
        <a href="salir.php" class="boton-salir">Salir</a>
    </div>
</header>
<?php endif; ?>
<main class="contenedor">
<?php if ($conMenu): ?>
<h1><?= e($titulo) ?></h1>
<?php mostrarFlash(); ?>
<?php endif; ?>
<?php
}

/** Pie HTML común. */
function pie(): void
{
    echo "</main>\n<footer class=\"pie\">Universidad de Playa Ancha · Prueba 1 PHP y Servicios Web</footer>\n</body>\n</html>";
}

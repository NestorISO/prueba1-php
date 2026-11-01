<?php
/**
 * 1. index.php — Inicio de sesión con CAPTCHA (POST)
 */
require __DIR__ . '/includes.php';

// Si ya hay sesión iniciada, ir directo al panel
if (!empty($_SESSION['usuario'])) {
    header('Location: panel.php');
    exit;
}

/** Genera un nuevo CAPTCHA y guarda el resultado en la sesión. */
function generarCaptcha(): void
{
    $a = random_int(1, 9);
    $b = random_int(1, 9);
    $_SESSION['captcha_pregunta'] = "$a + $b";
    $_SESSION['captcha_resultado'] = $a + $b;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario   = trim($_POST['usuario'] ?? '');
    $clave     = $_POST['clave'] ?? '';
    $respuesta = trim($_POST['captcha'] ?? '');

    $usuarioOk = ($usuario === USUARIO_VALIDO);
    $claveOk   = ($clave === CLAVE_VALIDA);
    $captchaOk = isset($_SESSION['captcha_resultado'])
        && ctype_digit($respuesta)
        && (int)$respuesta === $_SESSION['captcha_resultado'];

    // Después de cada intento se genera un CAPTCHA nuevo
    generarCaptcha();

    if ($usuarioOk && $claveOk && $captchaOk) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario;
        unset($_SESSION['captcha_pregunta'], $_SESSION['captcha_resultado']);
        header('Location: panel.php');
        exit;
    }

    if (!$captchaOk && $usuarioOk && $claveOk) {
        flash('error', 'Acceso denegado: la respuesta del CAPTCHA es incorrecta.');
    } else {
        flash('error', 'Acceso denegado: usuario, contraseña o CAPTCHA incorrectos.');
    }
    header('Location: index.php');
    exit;
}

// Primera visita: crear CAPTCHA si no existe
if (empty($_SESSION['captcha_pregunta'])) {
    generarCaptcha();
}

encabezado('Iniciar sesión', false);
?>
<div class="login">
    <div class="login-cabecera">
        <span class="logo grande">R</span>
        <h1>Rifa UPLA</h1>
        <p>Inicia sesión para administrar la rifa</p>
    </div>

    <?php mostrarFlash(); ?>

    <form method="POST" action="index.php" class="tarjeta">
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="Ingresa tu usuario" required autofocus>

        <label for="clave">Contraseña</label>
        <input type="password" id="clave" name="clave" placeholder="••••••••" required>

        <label for="captcha">Verificación</label>
        <div class="captcha">
            <span class="captcha-pregunta"><?= e($_SESSION['captcha_pregunta']) ?> = ?</span>
            <input type="number" id="captcha" name="captcha" placeholder="Resultado" required>
        </div>

        <button type="submit" class="ancho">Ingresar</button>
    </form>
</div>
<?php pie(); ?>

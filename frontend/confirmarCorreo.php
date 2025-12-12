<?php
// Iniciamos la sesión si aún no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Si ya hay login
if (isset($_SESSION["id_usuario"])) {
    // Usuario YA está logueado → bloquear acceso
    header("Location: ../index.php");
    exit();
}

// ============================================================
// 1. REENVIAR CORREO DE VERIFICACIÓN
// ============================================================
if (isset($_POST["reenviarCorreo"])) {

    require_once("../backend/Correo.php");
    $objetoCorreo = new Correo();

    $email = $_SESSION["correoRegistro"];
    $token = $_SESSION["tokenRegistro"];

    $resultado = $objetoCorreo->enviarCorreoRegistro($email, $token);

    if ($resultado) {
        $_SESSION["MensajeCorreoExitoso"] = "Reenvío del correo exitoso. Revisa tu bandeja.";
        unset($_SESSION["MensajeCorreoFallo"]);
    } else {
        $_SESSION["MensajeCorreoFallo"] = "No se pudo reenviar el correo. Inténtalo más tarde.";
        unset($_SESSION["MensajeCorreoExitoso"]);
    }

    header("location: confirmarCorreo.php");
    exit();
}

// ============================================================
// 2. VOLVER AL LOGIN
// ============================================================
if (isset($_POST["volverAlLogin"])) {

    unset($_SESSION["correoRegistro"]);
    unset($_SESSION["tokenRegistro"]);

    header("location: login.php");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar correo - MoodLoop</title>
    <link rel="icon" type="image/png" href="../assets/logo2.PNG">

    <link rel="stylesheet" href="css/confirmarCorreo.css">
</head>
<body>

<div class="confirm-wrapper">

    <div class="confirm-box">

        <!-- Logo -->
        <div class="logo">
            <img src="../assets/logo2.PNG" alt="MoodLoop">
            <h2>Verifica tu correo</h2>
        </div>

        <!-- Título -->
        <h3>Revisa tu bandeja</h3>

        <!-- ============================
             MENSAJES DE ESTADO
        ============================ -->
        <?php if (isset($_SESSION["MensajeCorreoExitoso"])): ?>
            <div class="alert-success"><?= $_SESSION["MensajeCorreoExitoso"] ?></div>
            <?php unset($_SESSION["MensajeCorreoExitoso"]); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION["MensajeCorreoFallo"])): ?>
            <div class="alert-error"><?= $_SESSION["MensajeCorreoFallo"] ?></div>
            <?php unset($_SESSION["MensajeCorreoFallo"]); ?>
        <?php endif; ?>

        <!-- Texto informativo -->
        <p class="texto-info">
            Te hemos enviado un correo con un enlace para confirmar tu cuenta.
            Si no lo encuentras, revisa la carpeta de spam o vuelve a enviarlo.
        </p>

        <!-- Botón reenviar -->
        <form method="post" action="#">
            <button type="submit" name="reenviarCorreo" class="btn-principal">Reenviar correo</button>
        </form>

        <!-- Botón volver al login -->
        <form method="post" action="#">
            <button type="submit" name="volverAlLogin" class="btn-secundario">Volver al login</button>
        </form>

    </div>

</div>

</body>
</html>

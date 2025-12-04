<?php
session_start();

// ============================================================
// 1. REENVIAR CORREO DE VERIFICACIÓN
// ============================================================
if (isset($_POST["reenviarCorreo"])) {

    // Cargamos la clase que envía correos
    require_once("../backend/Correo.php");

    $objetoCorreo = new Correo();

    // Recuperamos el correo y token guardados en la sesión
    $email = $_SESSION["correoRegistro"];
    $token = $_SESSION["tokenRegistro"];

    // Intentamos reenviar el correo
    $resultado = $objetoCorreo->enviarCorreoRegistro($email, $token);

    // Si se envió correctamente
    if ($resultado) {
        $_SESSION["MensajeCorreoExitoso"] = "Reenvio del correo exitoso. Por favor, verifique la bandeja de email.";
        unset($_SESSION["MensajeCorreoFallo"]);
    }
    // Si falló
    else {
        $_SESSION["MensajeCorreoFallo"] = "No se pudo reenviar el correo. Por favor, vuelva a intentarlo más tarde.";
        unset($_SESSION["MensajeCorreoExitoso"]);
    }

    // Recargamos la página para mostrar el mensaje
    header("location: confirmarCorreo.php");
    exit();
}

// ============================================================
// 2. VOLVER AL LOGIN
// ============================================================
if (isset($_POST["volverAlLogin"])) {

    // Eliminamos los datos temporales del registro
    unset($_SESSION["correoRegistro"]);
    unset($_SESSION["tokenRegistro"]);

    header("location: login.php");
    exit();
}

// ============================================================
// 3. MOSTRAR MENSAJES (si existen)
// ============================================================
if (isset($_SESSION["MensajeCorreoExitoso"])) {
    echo "<h1 style='color:green;'>" . $_SESSION["MensajeCorreoExitoso"] . "</h1>";
}

if (isset($_SESSION["MensajeCorreoFallo"])) {
    echo "<h1 style='color:red;'>" . $_SESSION["MensajeCorreoFallo"] . "</h1>";
}

// Limpiamos posibles errores previos
unset($_SESSION["error"]);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Confirmación del correo.</title>
</head>
<body>

<!-- Formulario para reenviar el correo -->
<form name="formReenviarCorreo" method="post" action="#">
    <p>
        <input type="submit" name="reenviarCorreo" id="reenviarCorreo" value="Reenviar Correo">
    </p>
</form>

<!-- Formulario para volver al login -->
<form name="formVolverAlLogin" method="post" action="#">
    <p>
        <input type="submit" name="volverAlLogin" id="volverAlLogin" value="Volver al Login">
    </p>
</form>

</body>
</html>

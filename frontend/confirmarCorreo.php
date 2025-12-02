<?php
session_start();

if (isset($_POST["reenviarCorreo"])) {
    require_once("../backend/Correo.php");

    $objetoCorreo = new Correo();

    $email = $_SESSION["correoRegistro"];
    $token = $_SESSION["tokenRegistro"];

    $resultado = $objetoCorreo->enviarCorreoRegistro($email, $token);
    if ($resultado) {
        $_SESSION["MensajeCorreoExitoso"] = "Reenvio del correo exitoso. Por favor, verifique la bandeja de email.";
        unset($_SESSION["MensajeCorreoFallo"]);
    }
    else {
        $_SESSION["MensajeCorreoFallo"] = "No se pudo reennviar el correo. Por favor, vuelva intentarlo más tarde.";
        unset($_SESSION["MensajeCorreoExitoso"]);
    }

    header("location: confirmarCorreo.php");
    exit();
}

if (isset($_POST["volverAlLogin"])) {
    unset($_SESSION["correoRegistro"]);
    unset($_SESSION["tokenRegistro"]);
    header("location: login.php");
    exit();
}

if (isset($_SESSION["MensajeCorreoExitoso"])) {
    echo "<h1 style='color:green;'>" . $_SESSION["MensajeCorreoExitoso"] . "</h1>";
}

if (isset($_SESSION["MensajeCorreoFallo"])) {
    echo "<h1 style='color:red;'>" . $_SESSION["MensajeCorreoFallo"] . "</h1>";
}

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

<!-- Formulario de reenviar correo: guarda los datos como POST -->
<form name="formReenviarCorreo" method="post" action="#">
    <!-- Botón de envío -->
    <p>
        <input type="submit" name="reenviarCorreo" id="reenviarCorreo" value="Reenviar Correo">
    </p>
</form>

<form name="formVolverAlLogin" method="post" action="#">
    <!-- Botón de envío -->
    <p>
        <input type="submit" name="volverAlLogin" id="volverAlLogin" value="Volver al Login">
    </p>
</form>


</body>
</html>


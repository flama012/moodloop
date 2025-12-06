<?php
// -------------------------------------------------------------
// verificar.php
// Valida el correo del usuario usando email + token.
// Se accede desde el enlace enviado al correo de registro.
// -------------------------------------------------------------

require_once "UsuarioBBDD.php";

// Estado por defecto
$tipoMensaje = "error";
$tituloMensaje = "Ha ocurrido un problema";
$descripcionMensaje = "No se ha podido verificar tu cuenta.";
$mostrarBotonLogin = true;

// Comprobamos si llega el parámetro 'email' y 'token' en la URL
if (isset($_GET['email']) && isset($_GET['token'])) {

    $usuBD = new UsuarioBBDD();
    $usuario = $usuBD->obtenerUsuario($_GET['email']);

    if ($usuario) {

        // 1. ¿Ya estaba verificado?
        if ($usuario->__get('confirmado') == 1) {
            $tipoMensaje = "info";
            $tituloMensaje = "Tu correo ya está verificado";
            $descripcionMensaje = "Ya puedes iniciar sesión con tu cuenta en MoodLoop.";
        } else {
            // 2. Comprobar token
            $tokenUrl = $_GET['token'];

            if ($usuario->token === $tokenUrl) {

                if ($usuBD->actualizaConfirmacion($usuario)) {
                    $tipoMensaje = "exito";
                    $tituloMensaje = "¡Tu cuenta ha sido verificada!";
                    $descripcionMensaje = "Ahora puedes iniciar sesión y empezar a compartir tus emociones en MoodLoop.";
                } else {
                    $tipoMensaje = "error";
                    $tituloMensaje = "Error al actualizar la verificación";
                    $descripcionMensaje = "Inténtalo más tarde o solicita un nuevo correo de verificación.";
                }

            } else {
                $tipoMensaje = "error";
                $tituloMensaje = "Enlace de verificación no válido";
                $descripcionMensaje = "El token de verificación no coincide. Es posible que el enlace haya expirado o no sea correcto.";
            }
        }

    } else {
        $tipoMensaje = "error";
        $tituloMensaje = "Usuario no encontrado";
        $descripcionMensaje = "No existe ningún usuario asociado a este correo.";
    }

} else {
    $tipoMensaje = "error";
    $tituloMensaje = "Enlace incompleto";
    $descripcionMensaje = "Faltan datos en el enlace de verificación. Revisa el correo o solicita uno nuevo.";
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de cuenta - MoodLoop</title>
    <link rel="stylesheet" href="../frontend/css/verificar.css">
</head>
<body>

<div class="verify-wrapper">
    <div class="verify-box">

        <div class="logo">
            <img src="../assets/logo.PNG" alt="MoodLoop">
            <h2>Verificación de cuenta</h2>
        </div>

        <?php if ($tipoMensaje === "exito"): ?>
            <div class="alert-success"><?= htmlspecialchars($tituloMensaje) ?></div>
        <?php elseif ($tipoMensaje === "info"): ?>
            <div class="alert-info"><?= htmlspecialchars($tituloMensaje) ?></div>
        <?php else: ?>
            <div class="alert-error"><?= htmlspecialchars($tituloMensaje) ?></div>
        <?php endif; ?>

        <p class="texto-info">
            <?= htmlspecialchars($descripcionMensaje) ?>
        </p>

        <?php if ($mostrarBotonLogin): ?>
            <a href="../frontend/login.php" class="btn-principal">Ir al login</a>
        <?php endif; ?>

        <div class="emoji-wall">😜 💌 🔐</div>
    </div>
</div>

</body>
</html>

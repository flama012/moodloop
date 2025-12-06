<?php
// -------------------------------------------------------------
// verificar.php
// Valida el correo del usuario usando email + token.
// Se accede desde el enlace enviado al correo de registro.
// -------------------------------------------------------------

require_once "UsuarioBBDD.php";

// Comprobamos si llega el parámetro 'email' en la URL
if (isset($_GET['email'])) {

    // Creamos el objeto para acceder a la base de datos
    $usuBD = new UsuarioBBDD();

    // Buscamos al usuario por su correo
    $usuario = $usuBD->obtenerUsuario($_GET['email']);

    // ============================================================
    // 1. COMPROBAR SI EL USUARIO YA ESTÁ VERIFICADO
    // ============================================================
    if ($usuario->__get('confirmado') == 1) {

        echo "Error, tu correo ya ha sido verificado";
        echo "<br><a href='../frontend/login.php'>Volver al login</a>";
        exit;
    }

    // ============================================================
    // 2. COMPARAR EL TOKEN DE LA URL CON EL TOKEN DEL USUARIO
    // ============================================================
    if ($usuario->token === $_GET['token']) {

        // Si coinciden, actualizamos la confirmación en la base de datos
        if ($usuBD->actualizaConfirmacion($usuario)) {

            echo "<h2 style='color: green;'>¡Tu cuenta ha sido verificada correctamente!</h2>";
            echo "<p>Ahora puedes iniciar sesión haciendo clic aquí:</p>";
            echo "<br><a href='../frontend/login.php'>Volver al login</a>";

        } else {

            echo "<p style='color:red;'>Error al actualizar tu verificación. Inténtalo de nuevo.</p>";
            echo "<br><a href='../frontend/login.php'>Volver al login</a>";
        }

    } else {

        // Token incorrecto → posible intento inválido
        echo "Error, este token no es válido";
    }

} else {

    // No se recibió el email en la URL
    echo "Error, el email no existe";
}

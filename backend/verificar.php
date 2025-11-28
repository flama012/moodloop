<?php
require_once "UsuarioBBDD.php";
if(isset($_GET['email'])) {
    //recogemos el emial y token
    $usuBD = new UsuarioBBDD();
    //busco al usuario con ese email
    $usuario = $usuBD->obtenerUsuario($_GET['email']);
    //busco al usuario con ese email si tiene confirmado 1 mensaje este proceso ya lo has hecho previamente
    if ($usuario->__get('confirmado') == 1) {
        echo "Error, tu correo  ya ha sido verificado";
        echo "<a href='login.php'>Volver al login</a>";
        exit;
    } else {
        //comparo el token recogido en la URL con el token grabado para ese usuario
        //si coinciden, actualizo el rol del usuario a valor 2 que es confirmado
        //mostrar mensaje usuario confirmado
        if ($usuario->token === $_GET['token']) {
            if ($usuBD->actualizaConfirmacion($usuario)) {
                echo "<h2 style='color: green;'>¡Tu cuenta ha sido verificada correctamente!</h2>";
                echo "<p>Ahora puedes iniciar sesión haciendo clic aquí:</p>";
                echo "<br><a href='login.php'>Volver al login</a>";
            } else {
                echo "<p style='color:red;'>Error al actualizar tu verificación. Inténtalo de nuevo.</p>";
                echo "<br><a href='login.php'>Volver al login</a>";

            }

        } else {
            echo "Error, este token no es valido";
        }
    }
}
else{
    echo "Error, el email no existe";
}

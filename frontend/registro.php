<?php
// Cargamos la clase de conexión desde el backend
require_once "../backend/ConexionDB.php";
require_once "../backend/UsuarioBBDD.php";
require_once "../backend/Correo.php";


// Iniciamos sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Establecemos la conexión con la base de datos 'moodloop'
$db = ConexionDB::getConexion("moodloop");

// Aquí podrías añadir validaciones de email o lógica adicional si lo necesitas
if (isset($_POST['registrarse'])) {
    // recoger datos
    $nombre     = $_POST["nombre"];
    $correo     = $_POST["correo"];
    $password   = $_POST["password"];
    $confirmar  = $_POST["confirmar"];

    //Contraseña es la misma que confirmar contraseña
    if ($password != $confirmar) {
        $_SESSION["error"] = "Las contraseñas no coinciden.";
        header("Location: registro.php");
        exit();
    }

    //verificar si existe ya ese correo en la tabla usuarios
    $usuBD = new UsuarioBBDD();
    if ($usuBD->existeEmail($correo)) {
        $_SESSION["error"] = "Error, el email ya existe"; //guardar datos para que se queden los campos
        // del formulario menos el email y el password
        header('Location: registro.php');
        exit();
    }
    else {
        $token = hash('sha256', rand(1, 15000));
        $passwordHaseada = password_hash($password, PASSWORD_DEFAULT);

        $insertar = $usuBD->insertarUsuario(
                null,                       // id_usuario autoincrement
                $nombre,                     // nombre_usuario
                $correo,                     // correo
                $passwordHaseada,            // contraseña_hash
                "",                          // biografia
                "",                          // estado_emocional
                2,                           // id_rol (usuario)
                0,                           // confirmado
                0,                           // baneado
                date("Y-m-d H:i:s"),         // fecha_registro
                $token                       // token
        );
                if ($insertar) {


//                    $_SESSION["nombreRegistro"] = $nombre;
                    $_SESSION["correoRegistro"] = $correo;
                    $_SESSION["tokenRegistro"] = $token;

                    $objetoCorreo = new Correo();
                    $resultado = $objetoCorreo->enviarCorreoRegistro($correo, $token);

                    if ($resultado) {
                        $_SESSION["MensajeCorreoExitoso"] = "El correo se ha enviado correctamente. Por favor, entra en tu bandeja y haz click en el enlace para confirmarlo.";
                    }
                    else {
                        $_SESSION["MensajeCorreoFallo"] = "No se ha podido enviar el correo. Por favor, vuelve a intentarlo más tarde";
                    }

                    header("location: confirmarCorreo.php");
                    exit();

                }
                else {
                    $_SESSION["error"] = "Error, no se ha podido registrar el usuario. Por favor, vuelve a intentarlo más tarde.";
                }
    }
}

if (isset($_SESSION["error"])) {
    echo "<h1 style='color:red;'>" . $_SESSION["error"] . "</h1>";
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Formulario de registro</title>
</head>
<body>

<h2>Regístrate</h2>

<!-- Formulario de registro: guarda los datos como POST -->
<form name="formRegistro" method="post" action="#">
    <!-- Nombre de usuario -->
    <p>
        <label for="nombre">Nombre de usuario:</label>
        <input type="text" id="nombre" required name="nombre">
    </p>

    <!-- Correo electrónico -->
    <p>
        <label for="correo">Correo:</label>
        <input type="email" id="correo" required name="correo">
    </p>

    <!-- Contraseña -->
    <p>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" required name="password">
    </p>

    <!-- Confirmar contraseña -->
    <p>
        <label for="confirmar">Confirmar contraseña:</label>
        <input type="password" id="confirmar" required name="confirmar">
    </p>

    <!-- Aceptación de términos -->
    <p>
        <a href="terminos.php">Acepto los Términos y Condiciones y la Política de Privacidad.</a>
        <input type="checkbox" required name="terminos" id="terminos" value="Enviar">
    </p>

    <!-- Botón de envío -->
    <p>
        <input type="submit" name="registrarse" id="registrarse" value="Registrarse">
    </p>

    <!-- Enlace para volver al login -->
    <p>
        <a href="login.php">Volver al login</a>
    </p>
</form>

</body>
</html>

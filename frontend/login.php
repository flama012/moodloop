<?php
// Cargamos la clase de conexión desde el backend
require_once "../backend/ConexionDB.php";
require_once "../backend/UsuarioBBDD.php";

// Iniciamos sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Establecemos la conexión con la base de datos 'moodloop'
$db = ConexionDB::getConexion("moodloop");

if (isset($_POST["irARegistro"])) {
    header("location: registro.php");
    exit;
}

if (isset($_POST["iniciar"])) {

    // Recoger datos
    $correo = $_POST["emailLogin"];
    $password = $_POST["passwordLogin"];

    $usuarioBD = new UsuarioBBDD();

    //Comprobar si existe el email
    if (!$usuarioBD->existeEmail($correo)) {
        $_SESSION["error"] = "Este correo no está registrado.";
        header("Location: login.php");
        exit();
    }

    //Obtener datos del usuario
    $usuario = $usuarioBD->obtenerUsuario($correo);

    //Comprobar confirmación del correo
    if ($usuario->__get('confirmado') != 1) {
        $_SESSION["error"] = "Debes confirmar tu correo antes de iniciar sesión.";
        header("location: login.php");
        exit();
        //require_once "./frontend/login.php";
    }

    //Comprobar contraseña
    if (!password_verify($password, $usuario->__get('password'))) {
        $_SESSION["error"] = "La contraseña es incorrecta.";
        header("location: login.php");
        exit();
        //require_once "./frontend/login.php";
    }

    //Login correcto → guardar sesión
    $_SESSION["usuario"] = $usuario->__get("id_usuario");
    $_SESSION["nombre"]  = $usuario->__get("nombre_usuario");
    $_SESSION["correo"] = $usuario->__get("correo");
    $_SESSION["id_usuario"] = $usuario->__get("id_usuario");

    header("Location: pagina_feed.php");
    exit();
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
    <title>Formulario de inicio de sesión</title>
</head>
<body>
<h2>Inicia sesión</h2>

<!-- Formulario de inicio de sesión -->
<form name="formLogin" method="post" action="#">
    <p>
        <label for="emailLogin">Email:</label>
        <input type="email" id="emailLogin" required name="emailLogin">
    </p>
    <p>
        <label for="passwordLogin">Contraseña:</label>
        <input type="password" id="passwordLogin" required name="passwordLogin">
    </p>
    <p>
        <input type="submit" name="iniciar" id="iniciar" value="Iniciar sesión">
    </p>
</form>

<!-- Formulario de redirección al registro -->
<form name="formRegistro" method="post" action="#">
    <p>
        <label>¿No tienes cuenta? Regístrate aquí:</label>
    </p>
    <input type="submit" name="irARegistro" id="registro" value="Registrarse">
</form>

</body>
</html>

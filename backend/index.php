<?php
// index.php
// Este archivo es el punto de inicio del backend.
// Muestra un menú sencillo con enlaces a las funcionalidades básicas.
if (!isset($_SESSION)) {
    session_start();
}
require_once "./backend/UsuarioBBDD.php";
/*
if (isset($_POST["iniciar"])) {

    // Recoger datos
    $correo = $_POST["emailLogin"];
    $password = $_POST["passwordLogin"];

    $usuarioBD = new UsuarioBBDD();

    //Comprobar si existe el email
    if (!$usuarioBD->existeEmail($correo)) {
        $_SESSION["error"] = "Este correo no está registrado.";
        header("Location: index.php");
        exit();
    }

    //Obtener datos del usuario
    $usuario = $usuarioBD->obtenerUsuario($correo);

    //Comprobar confirmación del correo
    if ($usuario->__get('confirmado') != 1) {
        $_SESSION["error"] = "Debes confirmar tu correo antes de iniciar sesión.";
        require_once "login.php";
    }

    //Comprobar contraseña
    if (!password_verify($password, $usuario->__get('password'))) {
        $_SESSION["error"] = "La contraseña es incorrecta.";
        require_once "login.php";
    }

    //Login correcto → guardar sesión
    $_SESSION["usuario"] = $usuario->__get("id_usuario");
    $_SESSION["nombre"]  = $usuario->__get("nombre_usuario");

    header("Location: pagina_feed.php");
    exit();
}
elseif(isset($_SESSION['usuario']) && $_SESSION['usuario'] != null) {
    header("Location: ./frontend/pagina_feed.php");
    exit();
}
elseif(isset($_POST["registro"])){
    require_once "./frontend/registro.php";
}
else {
    require_once "./frontend/login.php";
}

*/

?>

<?php
// index.php
// Punto de inicio del proyecto Moodloop.
// Gestiona el login, registro y redirección al feed.

// Iniciamos sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}
// Incluimos la clase de acceso a usuarios desde el backend
require_once "./backend/UsuarioBBDD.php";

// Si se envía el formulario de inicio de sesión
if (isset($_POST["iniciar"])) {

    // Recoger datos del formulario
    $correo   = $_POST["emailLogin"];
    $password = $_POST["passwordLogin"];

    $usuarioBD = new UsuarioBBDD();

    // Comprobar si existe el email
    if (!$usuarioBD->existeEmail($correo)) {
        $_SESSION["error"] = "Este correo no está registrado.";
        header("Location: index.php");
        exit();
    }

    // Obtener datos del usuario
    $usuario = $usuarioBD->obtenerUsuario($correo);

    // Comprobar confirmación del correo
    if ($usuario->__get('confirmado') != 1) {
        $_SESSION["error"] = "Debes confirmar tu correo antes de iniciar sesión.";
        require_once "./frontend/login.php";
        exit();
    }

    // Comprobar contraseña
    if (!password_verify($password, $usuario->__get('password'))) {
        $_SESSION["error"] = "La contraseña es incorrecta.";
        require_once "./frontend/login.php";
        exit();
    }

    // Login correcto → guardar datos en sesión
    $_SESSION["usuario"] = $usuario->__get("id_usuario");
    $_SESSION["nombre"]  = $usuario->__get("nombre_usuario");

    // Redirigir al feed
    header("Location: ./frontend/pagina_feed.php");
    exit();

} elseif (isset($_SESSION['usuario']) && $_SESSION['usuario'] != null) {
    // Si ya hay sesión activa, redirigir al feed
    header("Location: ./frontend/pagina_feed.php");
    exit();

} elseif (isset($_POST["registro"])) {
    // Si se pulsa el botón de registro, cargar formulario de registro
    require_once "frontend/registro.php";

} else {
    // Si no hay acción, mostrar formulario de login
    //require_once "./frontend/login.php";
    header("Location: ./frontend/login.php");
    exit;
}
?>

<?php
// -------------------------------------------------------------
// index.php
// Punto de inicio del proyecto Moodloop.
// Gestiona el login, registro y redirección al feed.
// -------------------------------------------------------------

// Iniciamos sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Cargamos la clase que maneja usuarios en la base de datos
require_once "./backend/UsuarioBBDD.php";

// ============================================================
// 1. PROCESAR INICIO DE SESIÓN
// ============================================================
if (isset($_POST["iniciar"])) {

    // Recogemos los datos del formulario
    $correo   = $_POST["emailLogin"];
    $password = $_POST["passwordLogin"];

    $usuarioBD = new UsuarioBBDD();

    // 1.1 Comprobar si el correo existe
    if (!$usuarioBD->existeEmail($correo)) {
        $_SESSION["error"] = "Este correo no está registrado.";
        header("Location: index.php");
        exit();
    }

    // 1.2 Obtener datos del usuario
    $usuario = $usuarioBD->obtenerUsuario($correo);

    // 1.3 Comprobar si el correo está verificado
    if ($usuario->__get('confirmado') != 1) {
        $_SESSION["error"] = "Debes confirmar tu correo antes de iniciar sesión.";
        require_once "./frontend/login.php";
        exit();
    }

    // 1.4 Comprobar contraseña
    if (!password_verify($password, $usuario->__get('password'))) {
        $_SESSION["error"] = "La contraseña es incorrecta.";
        require_once "./frontend/login.php";
        exit();
    }

    // 1.5 Login correcto → guardar datos en sesión
    $_SESSION["usuario"] = $usuario->__get("id_usuario");
    $_SESSION["nombre"]  = $usuario->__get("nombre_usuario");

    // Redirigir al feed
    header("Location: ./frontend/pagina_feed.php");
    exit();

}

// ============================================================
// 2. SI YA HAY SESIÓN ACTIVA → IR DIRECTO AL FEED
// ============================================================
elseif (isset($_SESSION['usuario']) && $_SESSION['usuario'] != null) {

    header("Location: ./frontend/pagina_feed.php");
    exit();

}

// ============================================================
// 3. SI SE PULSA EL BOTÓN DE REGISTRO → MOSTRAR FORMULARIO
// ============================================================
elseif (isset($_POST["registro"])) {

    require_once "frontend/registro.php";

}

// ============================================================
// 4. SI NO HAY ACCIÓN → MOSTRAR LOGIN
// ============================================================
else {

    // Antes se incluía directamente el login, ahora se redirige
    header("Location: ./frontend/login.php");
    exit;
}
?>

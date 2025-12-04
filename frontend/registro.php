<?php
// Cargamos las clases necesarias del backend
require_once "../backend/ConexionDB.php";
require_once "../backend/UsuarioBBDD.php";
require_once "../backend/Correo.php";

// Iniciamos sesión si aún no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Obtenemos la conexión a la base de datos
$db = ConexionDB::getConexion("moodloop");

// ============================================================
// PROCESAR FORMULARIO DE REGISTRO
// ============================================================
if (isset($_POST['registrarse'])) {

    // Recogemos los datos del formulario
    $nombre     = trim($_POST["nombre"]);
    $correo     = trim($_POST["correo"]);
    $password   = $_POST["password"];
    $confirmar  = $_POST["confirmar"];

    // 1. Validar que las contraseñas coinciden
    if ($password != $confirmar) {
        $_SESSION["error"] = "Las contraseñas no coinciden.";
        header("Location: registro.php");
        exit();
    }

    // Creamos el objeto para trabajar con usuarios
    $usuBD = new UsuarioBBDD();

    // 2. Comprobar si el correo ya está registrado
    if ($usuBD->existeEmail($correo)) {
        $_SESSION["error"] = "El correo ya está registrado.";
        header("Location: registro.php");
        exit();
    }

    // 3. Preparar datos para insertar
    $token = hash('sha256', rand(1, 15000)); // Token único para confirmar el correo
    $passwordHaseada = password_hash($password, PASSWORD_DEFAULT); // Cifrar contraseña

    // 4. Insertar usuario en la base de datos
    $insertar = $usuBD->insertarUsuario(
            null,                       // id_usuario autoincrement
            $nombre,                    // nombre_usuario
            $correo,                    // correo
            $passwordHaseada,           // contraseña cifrada
            "",                         // biografia
            "",                         // estado_emocional
            2,                          // id_rol (usuario normal)
            0,                          // confirmado (0 = no confirmado)
            0,                          // baneado
            date("Y-m-d H:i:s"),        // fecha_registro
            $token                      // token de verificación
    );

    // 5. Manejo de errores del método insertarUsuario()
    if ($insertar === "duplicado_usuario") {
        $_SESSION["error"] = "El nombre de usuario ya está en uso. Elige otro.";
        header("Location: registro.php");
        exit();
    }

    if ($insertar === false) {
        $_SESSION["error"] = "Error al registrar el usuario. Inténtalo más tarde.";
        header("Location: registro.php");
        exit();
    }

    // 6. Registro correcto → guardar datos para reenviar correo si hace falta
    $_SESSION["correoRegistro"] = $correo;
    $_SESSION["tokenRegistro"] = $token;

    // 7. Enviar correo de verificación
    $objetoCorreo = new Correo();
    $resultado = $objetoCorreo->enviarCorreoRegistro($correo, $token);

    if ($resultado) {
        $_SESSION["MensajeCorreoExitoso"] = "El correo se ha enviado correctamente. Revisa tu bandeja.";
    } else {
        $_SESSION["MensajeCorreoFallo"] = "No se pudo enviar el correo. Inténtalo más tarde.";
    }

    // Redirigimos a la página que explica que debe confirmar el correo
    header("Location: confirmarCorreo.php");
    exit();
}

// ============================================================
// MOSTRAR ERRORES SI EXISTEN
// ============================================================
if (isset($_SESSION["error"])) {
    echo "<h1 style='color:red;'>" . $_SESSION["error"] . "</h1>";
    unset($_SESSION["error"]);
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

<!-- ============================================================
     FORMULARIO DE REGISTRO
============================================================ -->
<form name="formRegistro" method="post" action="#">

    <p>
        <label for="nombre">Nombre de usuario:</label>
        <input type="text" id="nombre" required name="nombre">
    </p>

    <p>
        <label for="correo">Correo:</label>
        <input type="email" id="correo" required name="correo">
    </p>

    <p>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" required name="password">
    </p>

    <p>
        <label for="confirmar">Confirmar contraseña:</label>
        <input type="password" id="confirmar" required name="confirmar">
    </p>

    <p>
        <a href="terminos.php">Acepto los Términos y Condiciones y la Política de Privacidad.</a>
        <input type="checkbox" required name="terminos" id="terminos" value="Enviar">
    </p>

    <p>
        <input type="submit" name="registrarse" id="registrarse" value="Registrarse">
    </p>

    <p>
        <a href="login.php">Volver al login</a>
    </p>

</form>

</body>
</html>

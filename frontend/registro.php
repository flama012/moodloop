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

    $usuBD = new UsuarioBBDD();

    // 2. Comprobar si el correo ya está registrado
    if ($usuBD->existeEmail($correo)) {
        $_SESSION["error"] = "El correo ya está registrado.";
        header("Location: registro.php");
        exit();
    }

    // 3. Preparar datos
    $token = hash('sha256', rand(1, 15000));
    $passwordHaseada = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insertar usuario
    $insertar = $usuBD->insertarUsuario(
            null,
            $nombre,
            $correo,
            $passwordHaseada,
            "",
            "",
            2,
            0,
            0,
            date("Y-m-d H:i:s"),
            $token
    );

    // 5. Manejo de errores
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

    // 6. Guardar datos para reenviar correo
    $_SESSION["correoRegistro"] = $correo;
    $_SESSION["tokenRegistro"] = $token;

    // 7. Enviar correo
    $objetoCorreo = new Correo();
    $resultado = $objetoCorreo->enviarCorreoRegistro($correo, $token);

    if ($resultado) {
        $_SESSION["MensajeCorreoExitoso"] = "El correo se ha enviado correctamente. Revisa tu bandeja.";
    } else {
        $_SESSION["MensajeCorreoFallo"] = "No se pudo enviar el correo. Inténtalo más tarde.";
    }

    header("Location: confirmarCorreo.php");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta - MoodLoop</title>

    <!-- CSS del registro -->
    <link rel="stylesheet" href="css/registro.css">

    <script src="js/validar_registro.js"></script>

</head>
<body>

<div class="login-wrapper">

    <!-- ============================
         COLUMNA IZQUIERDA (FORMULARIO)
    ============================ -->
    <div class="login-left">

        <div class="content-box">

            <div class="logo">
                <img src="../assets/logo2.PNG" alt="MoodLoop">
                <h2>Conecta con tus emociones</h2>
            </div>

            <h3>Crear cuenta</h3>

            <!-- ============================
                 MENSAJES DE ERROR / ÉXITO
            ============================ -->
            <?php if (isset($_SESSION["error"])): ?>
                <div class="alert-error"><?= $_SESSION["error"] ?></div>
                <?php unset($_SESSION["error"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["mensaje"])): ?>
                <div class="alert-success"><?= $_SESSION["mensaje"] ?></div>
                <?php unset($_SESSION["mensaje"]); ?>
            <?php endif; ?>

            <!-- ============================================================
                 FORMULARIO DE REGISTRO
            ============================================================ -->
            <form name="formRegistro" method="post" action="#">

                <label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" required name="nombre" placeholder="Tu nombre">

                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" required name="correo" placeholder="tu@email.com">

                <label for="password">Contraseña</label>
                <input type="password" id="password" required name="password" placeholder="••••••••">

                <label for="confirmar">Confirmar contraseña</label>
                <input type="password" id="confirmar" required name="confirmar" placeholder="••••••••">

                <div class="terminos">
                    <input type="checkbox" required name="terminos" id="terminos" value="Enviar">
                    <label for="terminos">
                        Acepto los <a href="terminos.html">Términos y Condiciones</a> y la <a href="terminos.html">Política de Privacidad</a>.
                    </label>
                </div>

                <button type="submit" name="registrarse" id="btn-login">Crear cuenta</button>
            </form>

            <div id="errorRegistro" class="alert-error" style="display:none;"></div>

            <form method="post" action="login.php">
                <p class="login-register">
                    ¿Ya tienes cuenta?
                    <button type="submit" class="link-registro">Inicia sesión aquí</button>
                </p>
            </form>

        </div>

    </div>

    <!-- ============================
         COLUMNA DERECHA (EMOCIONES)
    ============================ -->
    <div class="login-right">

        <div class="emoji-wall">😊 😢 😡 😍 🤯 😴</div>

        <div class="right-box">
            <h2>Comparte tu estado de ánimo</h2>
            <p>Únete a MoodLoop y conecta con personas que entienden tus emociones. Comparte, descubre y celebra cada emoción.</p>

            <div class="stats-row">
                <div class="stat"><strong>10K+</strong><span>Usuarios activos</span></div>
                <div class="stat"><strong>50K+</strong><span>Momentos compartidos</span></div>
                <div class="stat"><strong>100+</strong><span>Comunidades</span></div>
            </div>
        </div>

    </div>

</div>

</body>
</html>

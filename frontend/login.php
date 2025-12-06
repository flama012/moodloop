<?php
// Cargamos las clases necesarias del backend
require_once "../backend/ConexionDB.php";
require_once "../backend/UsuarioBBDD.php";

// Iniciamos la sesión si aún no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Obtenemos la conexión a la base de datos
$db = ConexionDB::getConexion("moodloop");

// Si el usuario pulsa "Registrarse"
if (isset($_POST["irARegistro"])) {
    header("location: registro.php");
    exit;
}

// Si el usuario pulsa "Iniciar sesión"
if (isset($_POST["iniciar"])) {

    // Recogemos los datos del formulario
    $correo = trim($_POST["emailLogin"]);
    $password = $_POST["passwordLogin"];

    // Clase que maneja los usuarios en la BD
    $usuarioBD = new UsuarioBBDD();

    // 1. Comprobar si el correo existe
    if (!$usuarioBD->existeEmail($correo)) {
        $_SESSION["error"] = "Este correo no está registrado.";
        header("Location: login.php");
        exit();
    }

    // 2. Obtener el usuario
    $usuario = $usuarioBD->obtenerUsuario($correo);

    // 3. Comprobar si está verificado
    if ($usuario->__get('confirmado') != 1) {
        $_SESSION["correoNoVerificado"] = $usuario->__get("correo");
        $_SESSION["tokenNoVerificado"]  = $usuario->__get("token");
        $_SESSION["error"] = "Tu cuenta no está verificada.";
        header("location: login.php");
        exit();
    }

    // 4. Comprobar contraseña
    if (!password_verify($password, $usuario->__get('password'))) {
        $_SESSION["error"] = "La contraseña es incorrecta.";
        header("location: login.php");
        exit();
    }

    // 5. Guardar datos en sesión
    $_SESSION["usuario"] = $usuario->__get("id_usuario");
    $_SESSION["nombre"]  = $usuario->__get("nombre_usuario");
    $_SESSION["correo"]  = $usuario->__get("correo");
    $_SESSION["id_usuario"] = $usuario->__get("id_usuario");

    // Redirigir al feed
    header("Location: pagina_feed.php");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - MoodLoop</title>

    <!-- CSS del login -->
    <link rel="stylesheet" href="css/login.css">

    <!-- JS del login -->
    <script src="js/login.js" defer></script>
</head>
<body>

<div class="login-wrapper">

    <!-- Columna izquierda -->
    <div class="login-left">

        <!-- Contenedor interno para centrar y limitar el ancho -->
        <div class="content-box">

            <!-- Logo y frase -->
            <div class="logo">
                <img src="../assets/logo.PNG" alt="MoodLoop">
                <h2>Conecta con tus emociones</h2>
            </div>

            <h3>Iniciar sesión</h3>

            <!-- Mensajes del sistema -->
            <?php if (isset($_SESSION["error"])): ?>
                <div class="alert-error"><?= $_SESSION["error"] ?></div>
                <?php unset($_SESSION["error"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["mensaje"])): ?>
                <div class="alert-success"><?= $_SESSION["mensaje"] ?></div>
                <?php unset($_SESSION["mensaje"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["correoNoVerificado"])): ?>
                <form method="post" action="reenviar_confirmacion.php">
                    <p class="alert-info">¿No recibiste el correo de verificación?</p>
                    <button type="submit" class="btn-secundario">Reenviar correo</button>
                </form>
            <?php endif; ?>

            <!-- Formulario de login -->
            <form method="post" id="form-login">

                <!-- Campo email -->
                <label for="emailLogin">Correo electrónico</label>
                <input type="email" id="emailLogin" name="emailLogin" placeholder="tu@email.com" required>

                <!-- Campo contraseña -->
                <label for="passwordLogin">Contraseña</label>
                <input type="password" id="passwordLogin" name="passwordLogin" placeholder="••••••••" required>

                <!-- Botón de login -->
                <button type="submit" name="iniciar" id="btn-login">Iniciar sesión</button>
            </form>

            <!-- Botón para ir al registro (formulario separado) -->
            <form method="post">
                <p class="login-register">
                    ¿No tienes cuenta?
                    <button type="submit" name="irARegistro" class="link-registro">Regístrate aquí</button>
                </p>
            </form>

            <footer>
                <p>© 2024 MoodLoop. <a href="terminos.php">Insights</a>. <a href="terminos.php">Términos de Servicio</a> y <a href="terminos.php">Política de Privacidad</a></p>
            </footer>

        </div>

    </div>

    <!-- Columna derecha -->
    <div class="login-right">

        <!-- Emojis primero -->
        <div class="emoji-wall">😊 😢 😡 😍 🤯 😴</div>

        <!-- Contenedor interno igual que en la izquierda -->
        <div class="right-box">

            <h2>Comparte tu estado de ánimo</h2>
            <p>Únete a MoodLoop y conecta con personas que entienden tus emociones. Comparte, descubre y celebra cada emoción.</p>

            <!-- Estadísticas en horizontal -->
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

<?php
// Cargamos la clase de conexión desde el backend
require_once "./backend/ConexionDB.php";

// Iniciamos sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Establecemos la conexión con la base de datos 'moodloop'
$db = ConexionDB::getConexion("moodloop");

// Aquí podrías añadir validaciones de email o lógica adicional si lo necesitas
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
<form name="formRegistro" method="post" action="../backend/send.php">
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
        <input type="submit" name="enviar" id="enviar" value="Enviar">
    </p>

    <!-- Enlace para volver al login -->
    <p>
        <a href="login.php">Volver al login</a>
    </p>

    <!-- Mensaje de error si existe en sesión -->
    <?php
    if (isset($_SESSION["error"])) {
        echo "<p style='color:red;'>" . $_SESSION["error"] . "</p>";
        unset($_SESSION["error"]);
    }
    ?>
</form>

</body>
</html>

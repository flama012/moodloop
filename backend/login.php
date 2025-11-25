<?php
// login.php
// Script para iniciar sesión de usuario

require_once "UsuarioBBDD.php"; // Incluimos la clase
session_start(); // Iniciamos sesión

$conexion = new UsuarioBBDD(); // Creamos el objeto de conexión

// Si se envió el formulario de login
if (isset($_POST["emailLogin"]) && isset($_POST["passwordLogin"])) {
    $email = $_POST["emailLogin"];
    $password = $_POST["passwordLogin"];

    // Validamos credenciales
    if ($conexion->validarUsuario($email, $password)) {
        $usuario = $conexion->getUsuario($email); // Obtenemos datos del usuario
        $_SESSION["usuario"] = $usuario; // Guardamos en sesión
        header("Location: index.php"); // Redirigimos al panel
        exit;
    } else {
        echo "<h4>Email o contraseña incorrectos.</h4>";
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<h2>Inicia sesión</h2>
<form method="post" action="login.php">
    <p><label>Email:</label><input type="email" name="emailLogin" required></p>
    <p><label>Contraseña:</label><input type="password" name="passwordLogin" required></p>
    <p><input type="submit" value="Iniciar sesión"></p>
    <p><a href="registro.php">Registrarse</a></p>
</form>
</body>
</html>

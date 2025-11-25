<?php
// registro.php
// Script para registrar nuevos usuarios

require_once "UsuarioBBDD.php"; // Incluimos la clase
session_start(); // Iniciamos sesión

// Si se envió el formulario de registro
if (isset($_POST["enviar"])) {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $password = $_POST["password"];
    $confirmar = $_POST["confirmar"];

    // Validamos que las contraseñas coincidan
    if ($password !== $confirmar) {
        $_SESSION["mensaje"] = "Las contraseñas no coinciden.";
        header("Location: registro.php");
        exit();
    }

    $conexion = new UsuarioBBDD(); // Creamos el objeto de conexión

    // Comprobamos si el correo ya está registrado
    if ($conexion->getUsuario($correo)) {
        $_SESSION["mensaje"] = "Ese email ya está registrado.";
        header("Location: registro.php");
        exit();
    }

    // Encriptamos la contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertamos el nuevo usuario
    if ($conexion->insertarUsuario($nombre, $correo, $hash)) {
        $_SESSION["mensaje"] = "Usuario registrado correctamente.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION["mensaje"] = "Error al registrar usuario.";
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>
<h2>Regístrate</h2>

<?php
// Mostramos mensaje si existe
if (isset($_SESSION["mensaje"])) {
    echo "<p style='color:red;'>".$_SESSION["mensaje"]."</p>";
    unset($_SESSION["mensaje"]);
}
?>

<form method="post" action="registro.php">
    <p><label>Nombre:</label><input type="text" name="nombre" required></p>
    <p><label>Correo:</label><input type="email" name="correo" required></p>
    <p><label>Contraseña:</label><input type="password" name="password" required></p>
    <p><label>Confirmar:</label><input type="password" name="confirmar" required></p>
    <p><input type="submit" name="enviar" value="Enviar"></p>
    <p><a href="login.php">Volver al login</a></p>
</form>
</body>
</html>

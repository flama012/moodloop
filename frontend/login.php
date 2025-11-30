<?php
// Cargamos la clase de conexión desde el backend
require_once "./backend/ConexionDB.php";

// Iniciamos sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Establecemos la conexión con la base de datos 'moodloop'
$db = ConexionDB::getConexion("moodloop");

// Mensaje de bienvenida
echo "<h2>Inicia sesión</h2>";
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

<!-- Formulario de inicio de sesión -->
<form name="formLogin" method="post" action="../backend/index.php">
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
<form name="formRegistro" method="post" action="../backend/index.php">
    <p>
        <label>¿No tienes cuenta? Regístrate aquí:</label>
    </p>
    <input type="submit" name="registro" id="registro" value="Registro">
</form>

<!-- Mensaje de error si existe en sesión -->
<?php
if (isset($_SESSION["error"])) {
    echo "<p style='color:red;'>" . $_SESSION["error"] . "</p>";
    unset($_SESSION["error"]);
}
?>

</body>
</html>

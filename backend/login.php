<?php
require_once "ConexionDB.php";
if (!isset($_SESSION)) {
    session_start();
}
//guardamos la conexion con base de datos
$db = ConexionDB::getConexion("moodloop");// aquí la conexion que hayais puesto

//si se logea el usuario
echo "<h2>Inicia sesión</h2>";

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Formulario2</title>
</head>
<body>
<!-- El formulario -->
<form name="form2" method="post" action="index.php"><!--Vamos al feed-->
    <p>
        <label for="email">Email:</label>
        <input type="email" id="emailLogin" required name="emailLogin">
    </p>
    <p>
        <label for="passwordLogin"> Contraseña:</label>
        <input type="password" id="passwordLogin" required name="passwordLogin">
    </p>

    <p>
        <input type="submit" name="iniciar" id="iniciar">
    </p>

</form>
<form name="form2" method="post" action="index.php">
    <p>
        <label>Regístrate aquí:</label>

    </p>
    <input type="submit" name="registro" id="registro" value="Registro">
</form>
</body>
</html>
<?php
if (isset($_SESSION["error"])) {
    echo "<p style='color:red;'>".$_SESSION["error"]."</p>";
    unset($_SESSION["error"]);
}
?>



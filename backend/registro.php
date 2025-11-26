<?php
require_once "ConexionDB.php";
session_start();
//si se envia el formulario de registro

// VALIDACIONES
  //email
    // CONEXIÓN BD CON TU SINGLETON
    $db = ConexionDB::getConexion("moodloop");

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Formulario</title>
</head>
<body>
<h2>Regístrate</h2>

<?php
if (isset($_SESSION["mensaje"])) {
    echo "<p style='color:red;'>".$_SESSION["mensaje"]."</p>";
    unset($_SESSION["mensaje"]);
}
?>
<!-- Guarda los datos como post  -->
<form name="form1" method="post" action="send.php"><!--lo llevamos al send para enviarle el correo-->
    <p>
        <label for="nombre"> Nombre de usuario:</label>
        <input type="text" id="nombre" required name="nombre"">
    </p>

    <p>
        <label for="correo"> Correo:</label>
        <input type="email" id="correo" required name="correo">
    </p>
    <p>
        <label for="password"> Contraseña:</label>
        <input type="password" id="password" required name="password">
    </p>
    <p>
        <label for="confirmar"> Confirmar contraseña:</label>
        <input type="password" id="confirmar" required name="confirmar">
    </p>
    <p>
        <a href="terminos.php">Acepta los terminos</a>
        <input type="checkbox" required name="terminos" id="terminos" value="Enviar">
    </p>
    <p>
        <input type="submit" name="enviar" id="enviar" value="Enviar">
        <!--Lo guardamos como enviar-->
    </p>
    <p>
        <a href="login.php">Volver al login</a>
    </p>

    <?php
    if(isset($_SESSION["error"])){
        echo "<p style = 'color: red'>".$_SESSION["error"]."</p>";
    }

    ?>
</form>
</body>
</html>


<?php
require_once "UsuarioBBDD.php";
session_start();
//guardamos la conexion con base de datos
$conexion = new UsuarioBBDD("localhost","root","Ciclo2gs","prueba");// aquí la conexion que hayais puesto

//si se logea el usuario
if (isset($_POST["emailLogin"]) && isset($_POST["passwordLogin"])) {
//guardamos las variables
    $email = $_POST["emailLogin"];
    $password = $_POST["passwordLogin"];

    // Creamos la conexión

    //validamos si esta luego de que ya haya confirmado
    if ($conexion->validarUsuario($email, $password)) {
        //cogemos justo el usuario que ha entrado
        $usuario = $conexion->getUsuario($email);
        // entramos en la tienda
        header("Location: tienda.php");
        exit;
    } else {
        //si no esta en la base de datos
        echo "<h4>Email o contraseña incorrectos.</h4>";
    }
}

//si se ha registrado alguien
if (isset($_POST["enviar"])) {
    //recogemos su informacion
    $apellidos=$_POST["apellidos"];
    $nombre=$_POST["nombre"];
    $email=$_POST["email"];
    $hash= password_hash($_POST["password"], PASSWORD_DEFAULT);//encriptamos

    //se almacena en la clase que esta con los mismos campos que en la base de datos
    $u=new Usuario($dni, $apellidos, $nombre, $email, $hash);
    //insertamos los campos
    $conexion->insertarUsuario($u);
    echo "Usuario registrado correctamente";
}


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
<form name="form2" method="post" action="index.php"><!-- Recargamos página y busca el post de emailLogin y passwordLogin -->
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

    <p>
        <label>Registro:</label>
        <a href="registro.php">Registrate aquí</a>
    </p>
</form>
</body>
</html>



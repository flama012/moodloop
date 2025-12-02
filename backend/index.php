<?php
echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "  <meta charset='UTF-8'>";
echo "  <title>Moodloop - Panel Frontend</title>";
echo "  <style>
          body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
          h1 { color: #333; }
          a { display: block; margin: 10px 0; text-decoration: none; color: #007BFF; }
          a:hover { text-decoration: underline; }
          .menu { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        </style>";
echo "</head>";
echo "<body>";

echo "</body>";
echo "</html>";
// index.php
// Este archivo es el punto de inicio del backend.
// Muestra un menú sencillo con enlaces a las funcionalidades básicas.
if (!isset($_SESSION)) {
    session_start();
}
require_once "./backend/UsuarioBBDD.php";
/*
if (isset($_POST["iniciar"])) {

    // Recoger datos
    $correo = $_POST["emailLogin"];
    $password = $_POST["passwordLogin"];

    $usuarioBD = new UsuarioBBDD();

    //Comprobar si existe el email
    if (!$usuarioBD->existeEmail($correo)) {
        $_SESSION["error"] = "Este correo no está registrado.";
        header("Location: index.php");
        exit();
    }

    //Obtener datos del usuario
    $usuario = $usuarioBD->obtenerUsuario($correo);

    //Comprobar confirmación del correo
    if ($usuario->__get('confirmado') != 1) {
        $_SESSION["error"] = "Debes confirmar tu correo antes de iniciar sesión.";
        require_once "./frontend/login.php";
    }

    //Comprobar contraseña
    if (!password_verify($password, $usuario->__get('password'))) {
        $_SESSION["error"] = "La contraseña es incorrecta.";
        require_once "./frontend/login.php";
    }

    //Login correcto → guardar sesión
    $_SESSION["usuario"] = $usuario->__get("id_usuario");
    $_SESSION["nombre"]  = $usuario->__get("nombre_usuario");

    header("Location: ./frontend/pagina_feed.php");
    exit();
}
elseif(isset($_SESSION['usuario']) && $_SESSION['usuario'] != null) {
    header("Location: ./frontend/pagina_feed.php");
    exit();
}
elseif(isset($_POST["registro"])){
    require_once "./frontend/registro.php";
}
else {
    require_once "./frontend/login.php";
}

*/

?>

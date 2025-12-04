<?php
require_once "../backend/UsuarioBBDD.php";

if (!isset($_SESSION)) {
    session_start();
}

// Si no hay sesión, no dejamos entrar
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "No has iniciado sesión.";
    header("Location: ../index.php");
    exit();
}

$bbdd = new UsuarioBBDD();

// Comprobamos si llega el ID por la URL
if (isset($_GET["id"])) {
    $idUsuario = $_GET["id"];
} else {
    echo "No se ha indicado un usuario.";
    exit();
}

// Obtenemos el usuario como OBJETO para mostrar su nombre
$infoUsuario = $bbdd->obtenerUsuarioObjetoPorId($idUsuario);

// Obtenemos la lista de seguidores (array)
$seguidores = $bbdd->obtenerSeguidores($idUsuario);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguidores</title>
</head>
<body>

<?php require_once "cabecera.php"; ?>

<h1>Seguidores de <?= $infoUsuario->__get("nombre_usuario") ?></h1>

<?php
// Si no tiene seguidores
if (count($seguidores) == 0) {
    echo "<p>Este usuario no tiene seguidores.</p>";
} else {
    // Recorremos la lista de seguidores
    foreach ($seguidores as $seg) {
        echo "<div style='margin-bottom:10px;'>";
        echo "<strong>" . $seg["nombre_usuario"] . "</strong><br>";
        echo "<a href='ver_perfil.php?id=" . $seg["id_usuario"] . "'>Ver perfil</a>";
        echo "</div>";
    }
}
?>

</body>
</html>

<?php
// -------------------------------------------------------------
// ver_seguidos.php
// Muestra la lista de usuarios que un usuario concreto sigue.
// -------------------------------------------------------------

require_once "../backend/UsuarioBBDD.php";

// Iniciar sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Si el usuario no ha iniciado sesión, no puede ver esta página
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "No has iniciado sesión.";
    header("Location: ../index.php");
    exit();
}

// Creamos el objeto para acceder a la base de datos
$bbdd = new UsuarioBBDD();

// ============================================================
// 1. COMPROBAR QUE SE HA RECIBIDO UN ID POR LA URL
// ============================================================
if (isset($_GET["id"])) {
    $idUsuario = $_GET["id"];
} else {
    echo "No se ha indicado un usuario.";
    exit();
}

// ============================================================
// 2. OBTENER INFORMACIÓN DEL USUARIO COMO OBJETO
// ============================================================
// Esto nos permite mostrar su nombre en el título
$infoUsuario = $bbdd->obtenerUsuarioObjetoPorId($idUsuario);

// ============================================================
// 3. OBTENER LISTA DE USUARIOS QUE SIGUE (ARRAY ASOCIATIVO)
// ============================================================
$seguidos = $bbdd->obtenerSeguidos($idUsuario);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguidos</title>
</head>
<body>

<?php require_once "cabecera.php"; ?>

<h1>Usuarios seguidos por <?= $infoUsuario->__get("nombre_usuario") ?></h1>

<?php
// ============================================================
// 4. MOSTRAR LISTA DE SEGUIDOS
// ============================================================

// Si no sigue a nadie
if (count($seguidos) == 0) {

    echo "<p>Este usuario no sigue a nadie.</p>";

} else {

    // Recorremos la lista de seguidos
    foreach ($seguidos as $seg) {

        echo "<div style='margin-bottom:10px;'>";

        // Nombre del usuario seguido
        echo "<strong>" . $seg["nombre_usuario"] . "</strong><br>";

        // Enlace para ver su perfil
        echo "<a href='ver_perfil.php?id=" . $seg["id_usuario"] . "'>Ver perfil</a>";

        echo "</div>";
    }
}
?>

</body>
</html>

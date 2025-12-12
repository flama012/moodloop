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
    <title>Seguidos - MoodLoop</title>
    <link rel="icon" type="image/png" href="../assets/logo2.PNG">

    <link rel="stylesheet" href="css/cabecera.css">
    <link rel="stylesheet" href="css/ver_seguidos.css">
</head>
<body>

<?php require_once "cabecera.php"; ?>

<!-- ============================================================
     TÍTULO PRINCIPAL
============================================================ -->
<h1>Seguidos</h1>

<!-- ============================================================
     LISTA DE USUARIOS SEGUIDOS
============================================================ -->
<div class="lista-seguidos">

    <?php
    // ============================================================
    // 4. MOSTRAR LISTA DE SEGUIDOS
    // ============================================================

    // Si no sigue a nadie
    if (count($seguidos) == 0): ?>

        <p class="sin-seguidos">Este usuario no sigue a nadie.</p>

    <?php else:

        // Recorremos la lista de seguidos
        foreach ($seguidos as $seg): ?>

            <div class="seguido-card">

                <!-- Nombre del usuario seguido -->
                <span class="seguido-nombre"><?= $seg["nombre_usuario"] ?></span>

                <!-- Enlace para ver su perfil -->
                <a href="ver_perfil.php?id=<?= $seg["id_usuario"] ?>" class="btn-ver-perfil">
                    Ver perfil
                </a>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

</body>

</html>

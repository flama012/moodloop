<?php
// -------------------------------------------------------------
// ver_seguidores.php
// Muestra la lista de seguidores de un usuario concreto.
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
// 3. OBTENER LISTA DE SEGUIDORES (ARRAY ASOCIATIVO)
// ============================================================
$seguidores = $bbdd->obtenerSeguidores($idUsuario);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguidores - MoodLoop</title>
    <link rel="icon" type="image/png" href="../assets/logo2.PNG">

    <link rel="stylesheet" href="css/cabecera.css">
    <link rel="stylesheet" href="css/ver_seguidores.css">
</head>
<body>

<?php require_once "cabecera.php"; ?>

<!-- ============================================================
     TÍTULO PRINCIPAL
============================================================ -->
<h1>Seguidores</h1>

<!-- ============================================================
     LISTA DE SEGUIDORES
============================================================ -->
<div class="lista-seguidores">

    <?php
    // ============================================================
    // 4. MOSTRAR LISTA DE SEGUIDORES
    // ============================================================

    // Si no tiene seguidores
    if (count($seguidores) == 0): ?>

        <p class="sin-seguidores">No tienes seguidores.</p>

    <?php else:

        // Recorremos la lista de seguidores
        foreach ($seguidores as $seg): ?>

            <div class="seguidor-card">

                <!-- Nombre del seguidor -->
                <span class="seguidor-nombre"><?= $seg["nombre_usuario"] ?></span>

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

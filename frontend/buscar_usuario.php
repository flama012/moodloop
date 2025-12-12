<?php
// Página que muestra usuarios que coinciden con lo buscado en la cabecera

// Iniciamos la sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Si no hay sesión iniciada, volvemos al inicio
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "No has iniciado sesión.";
    header("Location: ../index.php");
    exit();
}

// Cargamos la clase que consulta la base de datos
require_once "../backend/UsuarioBBDD.php";

// Creamos el objeto para usar sus métodos
$usuarioBBDD = new UsuarioBBDD();

// Texto que el usuario ha escrito en el buscador
$busqueda = $_GET["q"] ?? "";

// Aquí guardaremos los resultados
$resultados = [];

// Si se ha escrito algo, hacemos la búsqueda
if ($busqueda != "") {
    $resultados = $usuarioBBDD->buscarUsuariosPorNombre($busqueda);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar usuario - MoodLoop</title>
    <link rel="icon" type="image/png" href="../assets/logo2.PNG">

    <link rel="stylesheet" href="css/cabecera.css">
    <link rel="stylesheet" href="css/buscar_usuario.css">
</head>
<body>

<?php include "cabecera.php"; ?>

<div class="resultados-contenedor">

    <h1 class="resultados-titulo">Resultados de la búsqueda</h1>
    <hr class="resultados-hr">

    <?php

    // Si no se escribió nada
    if ($busqueda == "") {

        echo "<p class='msg-info'>No has escrito nada en el buscador.</p>";

    }
// Si no hay coincidencias
    else if (empty($resultados)) {

        echo "<p class='msg-error'>No se encontraron usuarios con el nombre <strong>$busqueda</strong>.</p>";

    }
// Si hay resultados
    else {

        echo "<p class='msg-info'>Mostrando resultados para: <strong>$busqueda</strong></p>";

        // Recorremos los usuarios encontrados
        foreach ($resultados as $u) {

            $nombre = $u["nombre_usuario"];
            $id = $u["id_usuario"];

            echo "<div class='usuario-card'>";
            echo "<div class='usuario-nombre'>$nombre</div>";
            echo "<a class='btn-ver-perfil' href='ver_perfil.php?id=$id'>Ver perfil</a>";
            echo "</div>";
        }
    }

    ?>

</div>

</body>

</html>

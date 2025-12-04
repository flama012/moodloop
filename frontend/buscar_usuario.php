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
    <title>Buscar usuario</title>
</head>
<body>

<!-- Cabecera con el buscador -->
<?php include "cabecera.php"; ?>

<h1>Resultados de la búsqueda</h1>
<hr>

<?php

// Si no se escribió nada
if ($busqueda == "") {

    echo "<p>No has escrito nada en el buscador.</p>";

}
// Si no hay coincidencias
else if (empty($resultados)) {

    echo "<p>No se encontraron usuarios con el nombre <strong>$busqueda</strong>.</p>";

}
// Si hay resultados
else {

    echo "<p>Mostrando resultados para: <strong>$busqueda</strong></p><br>";

    // Recorremos los usuarios encontrados
    foreach ($resultados as $u) {

        $nombre = $u["nombre_usuario"];
        $id = $u["id_usuario"];

        echo "<div style='margin-bottom: 10px;'>";
        echo "<strong>$nombre</strong><br>";
        echo "<a href='ver_perfil.php?id=$id'>Ver perfil</a>";
        echo "</div>";
    }
}

?>

</body>
</html>

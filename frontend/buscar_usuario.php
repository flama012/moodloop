<?php
// -------------------------------------------------------------
// buscar_usuario.php
// Esta página muestra los usuarios que coinciden con lo que
// el usuario ha escrito en el buscador de la cabecera.
// -------------------------------------------------------------

// Iniciamos la sesión por si necesitamos datos del usuario
if (!isset($_SESSION)) {
    session_start();
}

// Incluimos la clase que se encarga de hablar con la base de datos
require_once "../backend/UsuarioBBDD.php";

// Creamos el objeto para usar sus funciones
$usuarioBBDD = new UsuarioBBDD();

// Aquí guardamos lo que el usuario ha escrito en el buscador
// Si no ha escrito nada, guardamos un texto vacío ""
$busqueda = $_GET["q"] ?? "";

// Aquí guardaremos los usuarios encontrados
$resultados = [];

// Si el usuario ha escrito algo, hacemos la búsqueda
if ($busqueda != "") {

    // Llamamos al método que busca usuarios por nombre
    // Este método lo tienes en tu clase UsuarioBBDD
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

<!-- Aquí incluimos la cabecera, que ya tiene el buscador -->
<?php include "cabecera.php"; ?>

<h1>Resultados de la búsqueda</h1>
<hr>

<?php

// Si el usuario no escribió nada en el buscador
if ($busqueda == "") {

    echo "<p>No has escrito nada en el buscador.</p>";

}
// Si escribió algo pero no se encontró ningún usuario
else if (empty($resultados)) {

    echo "<p>No se encontraron usuarios con el nombre <strong>$busqueda</strong>.</p>";

}
// Si sí hay resultados, los mostramos
else {

    echo "<p>Mostrando resultados para: <strong>$busqueda</strong></p><br>";

    // Recorremos todos los usuarios encontrados
    foreach ($resultados as $u) {

        // Guardamos los datos en variables para que sea más fácil de entender
        $nombre = $u["nombre_usuario"];
        $id = $u["id_usuario"];

        // Mostramos el nombre del usuario y un enlace para ver su perfil
        echo "<div style='margin-bottom: 10px;'>";
        echo "<strong>$nombre</strong><br>";

        // Este enlace lleva a la página donde se verá el perfil del usuario seleccionado
        echo "<a href='ver_perfil.php?id=$id'>Ver perfil</a>";

        echo "</div>";
    }
}

?>

</body>
</html>

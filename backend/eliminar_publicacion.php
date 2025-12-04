<?php
// Iniciamos la sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

require_once "ConexionDB.php";
require_once "PublicacionBBDD.php";

// Si el usuario no ha iniciado sesión, lo enviamos al inicio
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

// Comprobamos que llega el id de la publicación por POST
if (!isset($_POST["id_publicacion"])) {
    die("Publicación no válida.");
}

// Guardamos el id de la publicación y del usuario
$idPublicacion = intval($_POST["id_publicacion"]);
$idUsuario = $_SESSION["id_usuario"];

// Creamos el objeto para trabajar con publicaciones
$publiBBDD = new PublicacionBBDD();

// Comprobamos que la publicación pertenece al usuario
if (!$publiBBDD->esPublicacionDeUsuario($idPublicacion, $idUsuario)) {
    die("No tienes permiso para eliminar esta publicación.");
}

// Eliminamos la publicación
$publiBBDD->eliminarPublicacion($idPublicacion);

// Volvemos a la página anterior o a la página del usuario
$volver = $_SERVER["HTTP_REFERER"] ?? "../frontend/pagina_usuario.php";
header("Location: $volver");
exit();

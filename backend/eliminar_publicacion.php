<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once "ConexionDB.php";
require_once "PublicacionBBDD.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_POST["id_publicacion"])) {
    die("Publicación no válida.");
}

$idPublicacion = intval($_POST["id_publicacion"]);
$idUsuario = $_SESSION["id_usuario"];

$publiBBDD = new PublicacionBBDD();

// ✅ Comprobar que la publicación pertenece al usuario
if (!$publiBBDD->esPublicacionDeUsuario($idPublicacion, $idUsuario)) {
    die("No tienes permiso para eliminar esta publicación.");
}

// ✅ Eliminar publicación
$publiBBDD->eliminarPublicacion($idPublicacion);

// ✅ Volver a la página anterior
$volver = $_SERVER["HTTP_REFERER"] ?? "../frontend/pagina_usuario.php";
header("Location: $volver");
exit();

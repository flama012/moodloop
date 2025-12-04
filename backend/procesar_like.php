<?php
// Iniciamos la sesión si aún no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

require_once "ConexionDB.php";

// Si el usuario no ha iniciado sesión, lo enviamos al inicio
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

// Comprobamos que llega el id de la publicación
if (!isset($_POST["id_publicacion"])) {
    die("Publicación no válida.");
}

// Guardamos los datos necesarios
$idPublicacion = intval($_POST["id_publicacion"]);
$idUsuario = $_SESSION["id_usuario"];

// Obtenemos la conexión a la base de datos
$conn = ConexionDB::getConexion("moodloop");

// Comprobamos si el usuario ya dio like a esta publicación
$sql = "SELECT 1 FROM megusta WHERE id_usuario = :u AND id_publicacion = :p";
$c = $conn->prepare($sql);
$c->execute([":u" => $idUsuario, ":p" => $idPublicacion]);

// Si ya dio like, lo quitamos
if ($c->fetch()) {
    $sql = "DELETE FROM megusta WHERE id_usuario = :u AND id_publicacion = :p";
    $d = $conn->prepare($sql);
    $d->execute([":u" => $idUsuario, ":p" => $idPublicacion]);

// Si no lo había dado, lo añadimos
} else {
    $sql = "INSERT INTO megusta (id_usuario, id_publicacion) VALUES (:u, :p)";
    $i = $conn->prepare($sql);
    $i->execute([":u" => $idUsuario, ":p" => $idPublicacion]);
}

// Volvemos a la página anterior
$volver = $_SERVER["HTTP_REFERER"] ?? "../frontend/pagina_feed.php";
header("Location: $volver");
exit();

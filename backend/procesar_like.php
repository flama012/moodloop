<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once "ConexionDB.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_POST["id_publicacion"])) {
    die("Publicación no válida.");
}

$idPublicacion = intval($_POST["id_publicacion"]);
$idUsuario = $_SESSION["id_usuario"];

$conn = ConexionDB::getConexion("moodloop");

// Comprobar si ya dio like
$sql = "SELECT 1 FROM megusta WHERE id_usuario = :u AND id_publicacion = :p";
$c = $conn->prepare($sql);
$c->execute([":u" => $idUsuario, ":p" => $idPublicacion]);

if ($c->fetch()) {
    $sql = "DELETE FROM megusta WHERE id_usuario = :u AND id_publicacion = :p";
    $d = $conn->prepare($sql);
    $d->execute([":u" => $idUsuario, ":p" => $idPublicacion]);
} else {
    $sql = "INSERT INTO megusta (id_usuario, id_publicacion) VALUES (:u, :p)";
    $i = $conn->prepare($sql);
    $i->execute([":u" => $idUsuario, ":p" => $idPublicacion]);
}

// ✅ Volver a la página anterior
$volver = $_SERVER["HTTP_REFERER"] ?? "../frontend/pagina_feed.php";
header("Location: $volver");
exit();

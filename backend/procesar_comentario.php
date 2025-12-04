<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once "ConexionDB.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_POST["id_publicacion"]) || !isset($_POST["comentario"])) {
    die("Datos incompletos.");
}

$idPublicacion = intval($_POST["id_publicacion"]);
$idUsuario = $_SESSION["id_usuario"];
$comentario = trim($_POST["comentario"]);

if ($comentario === "") {
    $volver = $_SERVER["HTTP_REFERER"] ?? "../frontend/pagina_feed.php";
    header("Location: $volver");
    exit();
}

$conn = ConexionDB::getConexion("moodloop");

$sql = "INSERT INTO comentarios (id_publicacion, id_usuario, texto, fecha_hora)
        VALUES (:p, :u, :t, NOW())";

$c = $conn->prepare($sql);
$c->execute([
    ":p" => $idPublicacion,
    ":u" => $idUsuario,
    ":t" => $comentario
]);

// ✅ Volver a la página anterior
$volver = $_SERVER["HTTP_REFERER"] ?? "../frontend/pagina_feed.php";
header("Location: $volver");
exit();

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

// Comprobamos que llegan los datos necesarios por POST
if (!isset($_POST["id_publicacion"]) || !isset($_POST["comentario"])) {
    die("Datos incompletos.");
}

// Guardamos los datos recibidos
$idPublicacion = intval($_POST["id_publicacion"]);
$idUsuario = $_SESSION["id_usuario"];
$comentario = trim($_POST["comentario"]);

// Si el comentario está vacío, volvemos a la página anterior
if ($comentario === "") {
    $volver = $_SERVER["HTTP_REFERER"] ?? "../frontend/pagina_feed.php";
    header("Location: $volver");
    exit();
}

// Obtenemos la conexión a la base de datos
$conn = ConexionDB::getConexion("moodloop");

// Consulta para insertar el comentario
$sql = "INSERT INTO comentarios (id_publicacion, id_usuario, texto, fecha_hora)
        VALUES (:p, :u, :t, NOW())";

// Preparamos la consulta
$c = $conn->prepare($sql);

// Ejecutamos la consulta con los valores
$c->execute([
    ":p" => $idPublicacion,
    ":u" => $idUsuario,
    ":t" => $comentario
]);

// Volvemos a la página anterior
$volver = $_SERVER["HTTP_REFERER"] ?? "../frontend/pagina_feed.php";
header("Location: $volver");
exit();

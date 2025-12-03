<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once "../backend/PublicacionBBDD.php";
require_once "../backend/UsuarioBBDD.php";

// Verificar sesión
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "No has iniciado sesión.";
    header("Location: ../index.php");
    exit();
}

$idUsuario = $_SESSION["id_usuario"]; // o $_SESSION["id_usuario"] según cómo lo guardes
$estadoUsuario = $_SESSION["estado_emocional"] ?? null;

$publiBBDD = new PublicacionBBDD();
$usuarioBBDD = new UsuarioBBDD();

// 1. Publicaciones de seguidos
$publicacionesSeguidos = $publiBBDD->obtenerPublicacionesSeguidos($idUsuario);

// 2. Publicaciones por emoción del usuario (si existe)
$publicacionesEmocion = [];
if ($estadoUsuario) {
    $publicacionesEmocion = $publiBBDD->obtenerPublicacionesPorEmocion($estadoUsuario);
}

// 3. Filtros (emocion y etiquetas desde GET)
$filtroEmocion = $_GET["emocion"] ?? null;
$filtroEtiquetas = !empty($_GET["etiquetas"]) ? explode(",", $_GET["etiquetas"]) : [];

$publicacionesFiltro = [];
if ($filtroEmocion) {
    $publicacionesFiltro = $publiBBDD->obtenerPublicacionesPorEmocion($filtroEmocion);
}
if (!empty($filtroEtiquetas)) {
    $publicacionesFiltro = $publiBBDD->obtenerPublicacionesPorEtiquetas($filtroEtiquetas);
}

// 4. Top emociones y etiquetas
$topEmociones = $publiBBDD->obtenerTopEmociones();
$topEtiquetas = $publiBBDD->obtenerTopEtiquetas();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Feed</title>
</head>
<body>
<?php include "cabecera.php"; ?>

<h1>FEED</h1>

<!-- Filtros -->
<form method="get" action="pagina_feed.php">
    <label>Filtrar por emoción:</label>
    <select name="emocion">
        <option value="">Todas</option>
        <option value="Feliz">Feliz</option>
        <option value="Triste">Triste</option>
        <option value="Motivado">Motivado</option>
        <!-- añade más -->
    </select>
    <br>
    <label>Filtrar por etiquetas (máx 5, separadas por coma):</label>
    <input type="text" name="etiquetas" placeholder="ej: motivacion, felicidad">
    <br>
    <button type="submit">Aplicar filtros</button>
</form>

<hr>

<!-- Publicaciones de seguidos -->
<h2>Publicaciones de personas que sigues</h2>
<?php
if (!empty($publicacionesSeguidos)) {
    foreach ($publicacionesSeguidos as $pub) {
        echo "<p>";
        echo "<strong>" . $pub["nombre_usuario"] . "</strong><br>";
        echo nl2br($pub["mensaje"]) . "<br>";
        echo "<em>" . $pub["fecha_hora"] . "</em><br>";

        // Me gusta
        $likes = $publiBBDD->contarMeGustaPorPublicacion($pub["id_publicacion"]);
        echo "Me gusta: $likes<br>";

        // Comentarios
        $comentarios = $publiBBDD->obtenerComentariosPorPublicacion($pub["id_publicacion"]);
        if (!empty($comentarios)) {
            echo "<strong>Comentarios:</strong><br>";
            foreach ($comentarios as $c) {
                echo "- " . $c["texto"] . " <em>por " . $c["nombre_usuario"] . "</em><br>";
            }
        } else {
            echo "Sin comentarios.<br>";
        }
        echo "</p>";
    }
} else {
    echo "<p>No hay publicaciones de tus seguidos.</p>";
}
?>

<hr>

<!-- Publicaciones por emoción del usuario -->
<h2>Publicaciones según tu emoción del día</h2>
<?php
if (!empty($publicacionesEmocion)) {
    foreach ($publicacionesEmocion as $pub) {
        echo "<p><strong>" . $pub["nombre_usuario"] . "</strong>: " . nl2br($pub["mensaje"]) . "</p>";
    }
} else {
    echo "<p>No hay publicaciones con tu emoción actual.</p>";
}
?>

<hr>

<!-- Publicaciones filtradas -->
<h2>Publicaciones filtradas</h2>
<?php
if (!empty($publicacionesFiltro)) {
    foreach ($publicacionesFiltro as $pub) {
        echo "<p><strong>" . $pub["nombre_usuario"] . "</strong>: " . nl2br($pub["mensaje"]) . "</p>";
    }
} else {
    echo "<p>No hay publicaciones con los filtros aplicados.</p>";
}
?>

<hr>

<!-- Top emociones y etiquetas -->
<h2>Emociones populares</h2>
<?php foreach ($topEmociones as $e) echo "<p>".$e["estado_emocional"]." (".$e["total"].")</p>"; ?>

<h2>Etiquetas populares</h2>
<?php foreach ($topEtiquetas as $et) echo "<p>".$et["nombre_etiqueta"]." (".$et["total"].")</p>"; ?>

</body>
</html>

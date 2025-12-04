<?php
// Cargamos las clases necesarias para obtener publicaciones y usuarios
require_once "../backend/PublicacionBBDD.php";
require_once "../backend/UsuarioBBDD.php";

// Iniciamos la sesión si aún no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Si el usuario no ha iniciado sesión, lo enviamos al inicio
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "No has iniciado sesión.";
    header("Location: ../index.php");
    exit();
}

// Guardamos datos del usuario actual
$idUsuario = $_SESSION["id_usuario"];
$estadoUsuario = $_SESSION["estado_emocional"] ?? null;

// Creamos los objetos para acceder a la base de datos
$publiBBDD = new PublicacionBBDD();
$usuarioBBDD = new UsuarioBBDD();

// Lista fija de emociones disponibles
$listaEmociones = [
        "Feliz","Triste","Enojado","Ansioso","Motivado","Agradecido","Cansado","Estresado","Enfadado",
        "Sorprendido","Confundido","Esperanzado","Orgulloso","Relajado","Nostálgico","Melancólico",
        "Entusiasmado","Frustrado","Optimista","Pesimista","Aburrido","Curioso","Apático","Satisfecho",
        "Decepcionado","Inspirado","Resignado","Aliviado","Preocupado"
];

// ============================================================
// 1. LEER PARÁMETROS DE FILTRO (GET)
// ============================================================

// Modo de visualización del feed
$modo = $_GET["modo"] ?? "seguidos";

// Emoción seleccionada por el usuario
$emocionGet = $_GET["emocion"] ?? "";

// Texto de etiquetas introducido por el usuario
$etiquetasTexto = $_GET["etiquetas"] ?? "";

// Convertimos el texto "#ejemplo#prueba" en un array ["ejemplo", "prueba"]
$etiquetasArray = [];
if ($etiquetasTexto !== "") {
    $etiquetasArray = array_filter(array_map('trim', explode('#', $etiquetasTexto)));
    $etiquetasArray = array_slice($etiquetasArray, 0, 5); // Máximo 5 etiquetas
}

// ============================================================
// 2. CARGAR PUBLICACIONES SEGÚN EL MODO ELEGIDO
// ============================================================

$publicaciones = [];

if ($modo === "seguidos") {
    $publicaciones = $publiBBDD->obtenerPublicacionesSeguidos($idUsuario);

} elseif ($modo === "emocion") {
    if (!empty($estadoUsuario)) {
        $publicaciones = $publiBBDD->obtenerPublicacionesPorEmocion($estadoUsuario);
    }

} elseif ($modo === "filtro_emocion") {
    if ($emocionGet !== "") {
        $publicaciones = $publiBBDD->obtenerPublicacionesPorEmocion($emocionGet);
    }

} elseif ($modo === "filtro_etiquetas") {
    if (!empty($etiquetasArray)) {
        $publicaciones = $publiBBDD->obtenerPublicacionesPorEtiquetas($etiquetasArray);
    }

} elseif ($modo === "todas") {
    $publicaciones = $publiBBDD->obtenerPublicaciones(20);
}

// ============================================================
// 3. OBTENER TOP EMOCIONES Y TOP ETIQUETAS
// ============================================================

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

<!-- ============================================================
     FORMULARIO DE FILTROS DEL FEED
============================================================ -->
<form method="get" action="pagina_feed.php">

    <label>Mostrar publicaciones por:</label><br>
    <select name="modo" required>
        <option value="seguidos" <?= ($modo === "seguidos" ? "selected" : "") ?>>Personas que sigo</option>
        <option value="emocion" <?= ($modo === "emocion" ? "selected" : "") ?>>Mi emoción del día</option>
        <option value="filtro_emocion" <?= ($modo === "filtro_emocion" ? "selected" : "") ?>>Emoción específica</option>
        <option value="filtro_etiquetas" <?= ($modo === "filtro_etiquetas" ? "selected" : "") ?>>Etiquetas (#)</option>
        <option value="todas" <?= ($modo === "todas" ? "selected" : "") ?>>Todas las publicaciones</option>
    </select>
    <br><br>

    <!-- Selector de emociones -->
    <label>Emoción:</label><br>
    <select name="emocion">
        <option value="">Todas</option>
        <?php
        foreach ($listaEmociones as $emo) {
            $selected = ($emocionGet === $emo) ? "selected" : "";
            echo "<option value='$emo' $selected>$emo</option>";
        }
        ?>
    </select>
    <br><br>

    <!-- Campo de etiquetas -->
    <label>Etiquetas (máx 5, separadas por #):</label><br>
    <input type="text" name="etiquetas" placeholder="#motivacion#felicidad" value="<?= $etiquetasTexto ?>">
    <br><br>

    <button type="submit">Aplicar</button>
</form>

<hr>

<!-- ============================================================
     TÍTULO SEGÚN EL MODO SELECCIONADO
============================================================ -->
<?php
if ($modo === "seguidos") {
    echo "<h2>Publicaciones de personas que sigues</h2>";
} elseif ($modo === "emocion") {
    echo "<h2>Publicaciones según tu emoción del día</h2>";
} elseif ($modo === "filtro_emocion") {
    echo "<h2>Publicaciones por emoción específica</h2>";
} elseif ($modo === "filtro_etiquetas") {
    echo "<h2>Publicaciones por etiquetas</h2>";
} elseif ($modo === "todas") {
    echo "<h2>Todas las publicaciones</h2>";
}
?>

<!-- ============================================================
     LISTA DE PUBLICACIONES
============================================================ -->
<?php
if (!empty($publicaciones)) {

    foreach ($publicaciones as $pub) {

        echo "<p>";

        // Nombre del autor
        echo "<strong>" . $pub["nombre_usuario"] . "</strong><br>";

        // Emoción
        echo "Emoción: " . $pub["estado_emocional"] . "<br>";

        // Mensaje (nl2br convierte saltos de línea en <br>)
        echo nl2br($pub["mensaje"]) . "<br>";

        // Fecha
        echo "<em>" . $pub["fecha_hora"] . "</em><br>";

        // Me gusta
        $likes = $publiBBDD->contarMeGustaPorPublicacion($pub["id_publicacion"]);
        echo "<strong>Me gusta:</strong> " . $likes . "<br>";

        // Botón de "Me gusta"
        echo '<form action="../backend/procesar_like.php" method="post">
                <input type="hidden" name="id_publicacion" value="' . $pub['id_publicacion'] . '">
                <button type="submit">Me gusta</button>
              </form><br><br>';

        // Etiquetas
        $etis = $publiBBDD->obtenerEtiquetasPorPublicacion($pub["id_publicacion"]);
        if (!empty($etis)) {
            echo "<strong>Etiquetas:</strong> #" . implode(" #", $etis) . "<br>";
        }

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

        // Formulario para comentar
        echo '<form action="../backend/procesar_comentario.php" method="post">
                <input type="hidden" name="id_publicacion" value="' . $pub['id_publicacion'] . '">
                <textarea name="comentario" rows="2" cols="40" placeholder="Escribe un comentario..."></textarea><br>
                <button type="submit">Comentar</button>
              </form>';

        echo "</p>";
    }

} else {
    echo "<p>No hay publicaciones para este modo.</p>";
}
?>

<hr>

<!-- ============================================================
     TOP EMOCIONES
============================================================ -->
<h3>Emociones populares</h3>
<?php
if (!empty($topEmociones)) {
    foreach ($topEmociones as $e) {
        echo "<p>" . $e["estado_emocional"] . " (" . $e["total"] . ")</p>";
    }
} else {
    echo "<p>No hay datos de emociones populares.</p>";
}
?>

<!-- ============================================================
     TOP ETIQUETAS
============================================================ -->
<h3>Etiquetas populares</h3>
<?php
if (!empty($topEtiquetas)) {
    foreach ($topEtiquetas as $et) {
        echo "<p>" . $et["nombre_etiqueta"] . " (" . $et["total"] . ")</p>";
    }
} else {
    echo "<p>No hay datos de etiquetas populares.</p>";
}
?>

</body>
</html>

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
$modo = $_GET["modo"] ?? "todas";

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
    <title>Feed - MoodLoop</title>
    <link rel="icon" type="image/png" href="../assets/logo2.PNG">

    <link rel="stylesheet" href="css/cabecera.css">
    <link rel="stylesheet" href="css/feed.css">
    <link rel="stylesheet" href="css/modal_publicacion.css">
    <link rel="stylesheet" href="css/comentarios.css">

    <script src="js/modal_publicacion.js" defer></script>
    <script src="js/toggle_comentarios.js"></script>

</head>
<body>

<?php include "cabecera.php"; ?>

<div class="feed-layout">

    <!-- ============================================================
         COLUMNA IZQUIERDA: FILTROS
    ============================================================ -->
    <aside class="feed-col filtros">

        <form method="get" action="pagina_feed.php" class="filtros-card">

            <label>Mostrar publicaciones por:</label><br>
            <select name="modo" required>
                <option value="todas" <?= ($modo === "todas" ? "selected" : "") ?>>Todas las publicaciones</option>
                <option value="seguidos" <?= ($modo === "seguidos" ? "selected" : "") ?>>Personas que sigo</option>
                <option value="emocion" <?= ($modo === "emocion" ? "selected" : "") ?>>Mi emoción del día</option>
                <option value="filtro_emocion" <?= ($modo === "filtro_emocion" ? "selected" : "") ?>>Emoción específica</option>
                <option value="filtro_etiquetas" <?= ($modo === "filtro_etiquetas" ? "selected" : "") ?>>Etiquetas (#)</option>
            </select>
            <br><br>

            <label>Filtrar por Emoción:</label><br>
            <select name="emocion">
                <option value="">Seleccionar</option>
                <?php
                foreach ($listaEmociones as $emo) {
                    $selected = ($emocionGet === $emo) ? "selected" : "";
                    echo "<option value='$emo' $selected>$emo</option>";
                }
                ?>
            </select>
            <br><br>

            <label>Filtrar por Etiquetas:</label><br>
            <input type="text" name="etiquetas" placeholder="#motivacion#felicidad" value="<?= $etiquetasTexto ?>">
            <br><br>

            <button type="submit" class="btn-principal">Aplicar</button>
        </form>
    </aside>

    <!-- ============================================================
         COLUMNA CENTRAL: PUBLICACIONES
    ============================================================ -->
    <main class="feed-col publicaciones">

        <?php
        if ($modo === "seguidos") {
            echo "<h2 class='tituloAnimado'>Publicaciones de personas que sigues</h2>";
        } elseif ($modo === "emocion") {
            echo "<h2 class='tituloAnimado'>Publicaciones según tu emoción del día</h2>";
        } elseif ($modo === "filtro_emocion") {
            echo "<h2 class='tituloAnimado'>Publicaciones por emoción específica</h2>";
        } elseif ($modo === "filtro_etiquetas") {
            echo "<h2 class='tituloAnimado'>Publicaciones por etiquetas</h2>";
        } elseif ($modo === "todas") {
            echo "<h2 class='tituloAnimado'>Todas las publicaciones</h2>";
        }
        ?>

        <?php
        if (!empty($publicaciones)) {

            foreach ($publicaciones as $pub) {

                echo "<div class='card-publicacion'>";

                // CABECERA
                echo "<div class='pub-header'>";
                echo '<a href="ver_perfil.php?id=' . $pub["id_usuario"] . '" class="pub-autor-link">';
                echo '<strong>' . $pub["nombre_usuario"] . '</strong>';
                echo '</a>';

                echo "<span class='pub-emocion emocion-animada'>" . $pub["estado_emocional"] . "</span>";
                echo "</div>";

                // FECHA
                echo "<div class='pub-footer'>";
                echo "<em>" . $pub["fecha_hora"] . "</em>";
                echo "</div>";

                // MENSAJE
                echo "<p class='pub-mensaje'>" . nl2br($pub["mensaje"]) . "</p>";

                // ETIQUETAS
                $etis = $publiBBDD->obtenerEtiquetasPorPublicacion($pub["id_publicacion"]);
                if (!empty($etis)) {
                    echo "<div class='pub-etiquetas'>";
                    echo "<strong>Etiquetas:</strong> #" . implode(" #", $etis);
                    echo "</div>";
                }

                // ============================================================
                // BLOQUE DE ME GUSTA
                // ============================================================
                $likes = $publiBBDD->contarMeGustaPorPublicacion($pub["id_publicacion"]);
                $yaLeDioMG = $publiBBDD->usuarioDioMG($_SESSION["id_usuario"], $pub["id_publicacion"]);

                echo "<div class='pub-likes-block'>";

                echo '<form action="../backend/procesar_like.php" method="post" class="like-form">
                        <input type="hidden" name="id_publicacion" value="' . $pub['id_publicacion'] . '">
                        <button type="submit" class="like-button" data-liked="' . ($yaLeDioMG ? "1" : "0") . '">
                            <img src="../assets/' . ($yaLeDioMG ? "like-heart.svg" : "like-heart2.svg") . '" alt="Me gusta">
                        </button>
                      </form>';

                echo "<span class='like-count'>$likes</span>";
                echo "</div>";

                // ============================================================
                // CARGAR COMENTARIOS (IMPORTANTE)
                // ============================================================
                $comentarios = $publiBBDD->obtenerComentariosPorPublicacion($pub["id_publicacion"]);
                ?>

                <!-- BOTÓN MOSTRAR/OCULTAR -->
                <button type="button" class="btn-toggle-comentarios">Mostrar comentarios</button>

                <!-- CONTENEDOR OCULTO -->
                <div class="comentarios-contenedor">

                    <?php if (!empty($comentarios)): ?>
                        <div class="modal-comentarios-scroll">
                            <div class="pub-comentarios">
                                <?php foreach ($comentarios as $c): ?>
                                    <p class="comentario">
                                        <em><strong>@<?= $c["nombre_usuario"] ?>:</strong></em> <?= $c["texto"] ?>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php else: ?>
                        <p class="comentario-vacio">Sin comentarios.</p>
                    <?php endif; ?>

                </div>

                <?php
                // ============================================================
                // FORMULARIO PARA COMENTAR
                // ============================================================
                echo '<form action="../backend/procesar_comentario.php" method="post" class="pub-comentar-flex">
                        <input type="hidden" name="id_publicacion" value="' . $pub['id_publicacion'] . '">
                        <div class="comentar-contenedor">
                            <textarea name="comentario" class="comentario-input" placeholder="Escribe un comentario..."></textarea>
                            <button type="submit" class="btn-principal btn-comentar">Comentar</button>
                        </div>
                      </form>';

                echo "</div>";
            }

        } else {
            echo "<p class='feed-mensaje'>No hay publicaciones para este modo.</p>";
        }
        ?>

    </main>

    <!-- ============================================================
         COLUMNA DERECHA: ESTADÍSTICAS
    ============================================================ -->
    <aside class="feed-col estadisticas">

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

    </aside>

</div>

<!-- ============================================================
     MODAL DE PUBLICACIÓN
============================================================ -->
<div id="modalPublicacion" class="modal">
    <div class="modal-contenido">
        <button class="modal-cerrar" onclick="cerrarModal()">✕</button>
        <div id="modalPublicacionContenido"></div>
    </div>
</div>

</body>

</html>

<?php
// Cargamos las clases necesarias
require_once "../backend/UsuarioBBDD.php";
require_once "../backend/PublicacionBBDD.php";

// Iniciamos sesión si aún no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Si el usuario no ha iniciado sesión, lo enviamos al inicio
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "No has iniciado sesión.";
    header("Location: ../index.php");
    exit();
}

// Creamos el objeto para trabajar con usuarios
$bbdd = new UsuarioBBDD();

// ============================================================
// CARGAR DATOS DEL USUARIO DESDE LA BASE DE DATOS
// ============================================================

$infoUsuario = $bbdd->obtenerUsuario($_SESSION["correo"]);

$nombreUsuario   = $infoUsuario->__get("nombre_usuario");
$biografiaActual = $infoUsuario->__get("biografia");
$idUsuario       = $infoUsuario->__get("id_usuario");
$estadoActual    = $infoUsuario->__get("estado_emocional");

// Contadores de seguidores y seguidos
$seguidores = $bbdd->contarSeguidores($idUsuario);
$seguidos   = $bbdd->contarSeguidos($idUsuario);

// Creamos el objeto para trabajar con publicaciones
$publiBBDD = new PublicacionBBDD();

// Contadores y lista de publicaciones del usuario
$totalPublicaciones = $publiBBDD->contarPublicacionesPorUsuario($idUsuario);
$misPublicaciones   = $publiBBDD->obtenerPublicacionesPorUsuario($idUsuario);

// ============================================================
// LISTA DE EMOCIONES DISPONIBLES
// ============================================================

$emociones = [
        "Feliz","Triste","Enojado","Ansioso","Motivado","Agradecido","Cansado","Estresado","Enfadado",
        "Sorprendido","Confundido","Esperanzado","Orgulloso","Relajado","Nostálgico","Melancólico",
        "Entusiasmado","Frustrado","Optimista","Pesimista","Aburrido","Curioso","Apático","Satisfecho",
        "Decepcionado","Inspirado","Resignado","Aliviado","Preocupado"
];

// ============================================================
// PROCESAR FORMULARIO DE BIOGRAFÍA
// ============================================================

if (isset($_POST["guardar_biografia"])) {

    $nuevaBio = $_POST["biografia"];

    if ($bbdd->actualizarBiografia($idUsuario, $nuevaBio)) {

        $_SESSION["mensaje"] = "Biografía actualizada correctamente.";
        $_SESSION["biografia"] = $nuevaBio;

        header("Location: pagina_usuario.php");
        exit;

    } else {
        $_SESSION["error"] = "Error al actualizar la biografía.";
    }
}

// ============================================================
// PROCESAR FORMULARIO DE ESTADO EMOCIONAL
// ============================================================

if (isset($_POST['submit'])) {

    $estado = $_POST["estado_emocional"];

    if ($bbdd->actualizarEstadoEmocional($idUsuario, $estado)) {

        $_SESSION["estado_emocional"] = $estado;
        $_SESSION["mensaje"] = "Estado emocional actualizado correctamente.";

        header("Location: pagina_usuario.php");
        exit;

    } else {
        $_SESSION["error"] = "No se pudo actualizar el estado emocional.";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario</title>
</head>
<body>

<?php require_once "cabecera.php"; ?>

<!-- Mostrar mensajes de error o éxito -->
<?php
if (isset($_SESSION["error"])) {
    echo "<p style='color:red;'>" . $_SESSION["error"] . "</p>";
    unset($_SESSION["error"]);
}

if (isset($_SESSION["mensaje"])) {
    echo "<h3 style='color:green;'>" . $_SESSION["mensaje"] . "</h3>";
    unset($_SESSION["mensaje"]);
}
?>

<h1>PERFIL DE USUARIO</h1>

<p><strong>Nombre de usuario:</strong> <?= $nombreUsuario ?></p>

<!-- ============================================================
     ESTADÍSTICAS DEL USUARIO
============================================================ -->
<h3>Estadísticas</h3>

<p>
    <strong>Seguidores:</strong>
    <a href="ver_seguidores.php?id=<?= $idUsuario ?>">
        <?= $seguidores ?>
    </a>
</p>

<p>
    <strong>Seguidos:</strong>
    <a href="ver_seguidos.php?id=<?= $idUsuario ?>">
        <?= $seguidos ?>
    </a>
</p>

<p><strong>Publicaciones:</strong> <?= $totalPublicaciones ?></p>

<!-- ============================================================
     FORMULARIO PARA EDITAR BIOGRAFÍA
============================================================ -->
<h3>Biografía</h3>

<form action="pagina_usuario.php" method="post">
    <textarea name="biografia" rows="4" cols="40"><?= $biografiaActual ?></textarea>
    <br><br>
    <button type="submit" name="guardar_biografia">Guardar biografía</button>
</form>

<!-- ============================================================
     FORMULARIO PARA ACTUALIZAR ESTADO EMOCIONAL
============================================================ -->
<h3>Estado emocional</h3>

<form action="pagina_usuario.php" method="post">

    <label for="estado_emocional">Estado emocional</label>

    <select id="estado_emocional" name="estado_emocional" required>

        <!-- Opción por defecto -->
        <option value="" disabled <?= $estadoActual ? "" : "selected" ?>>
            Selecciona tu estado…
        </option>

        <!-- Lista de emociones -->
        <?php foreach ($emociones as $emo): ?>
            <option value="<?= $emo ?>" <?= ($emo == $estadoActual) ? "selected" : "" ?>>
                <?= $emo ?>
            </option>
        <?php endforeach; ?>

    </select>

    <br><br>
    <button type="submit" name="submit">Actualizar estado</button>

</form>

<!-- ============================================================
     LISTA DE PUBLICACIONES DEL USUARIO
============================================================ -->
<h3>Mis publicaciones</h3>

<?php
if (!empty($misPublicaciones)) {

    foreach ($misPublicaciones as $pub) {

        echo "<p>";

        echo "<strong>Estado emocional:</strong> " . $pub["estado_emocional"] . "<br>";
        echo "<strong>Mensaje:</strong> " . nl2br($pub["mensaje"]) . "<br>";
        echo "<em>Publicado el " . $pub["fecha_hora"] . "</em><br>";

        // Me gusta
        $totalLikes = $publiBBDD->contarMeGustaPorPublicacion($pub["id_publicacion"]);
        echo "<strong>Me gusta:</strong> " . $totalLikes . "<br>";

        // Botón de me gusta
        echo '<form action="../backend/procesar_like.php" method="post">
                <input type="hidden" name="id_publicacion" value="' . $pub['id_publicacion'] . '">
                <button type="submit">👍 Me gusta</button>
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
                echo "- " . $c["texto"] . " <em>por "
                        . $c["nombre_usuario"] . " ("
                        . $c["fecha_hora"] . ")</em><br>";
            }
        } else {
            echo "Sin comentarios.<br>";
        }

        // Formulario para comentar
        echo '<form action="../backend/procesar_comentario.php" method="post">
                <input type="hidden" name="id_publicacion" value="' . $pub['id_publicacion'] . '">
                <textarea name="comentario" rows="2" cols="40" placeholder="Escribe un comentario..."></textarea><br>
                <button type="submit">Comentar</button>
              </form><br>';

        // Botón para eliminar publicación
        echo '<form action="../backend/eliminar_publicacion.php" method="post"
                onsubmit="return confirm(\'¿Seguro que quieres eliminar esta publicación? Esta acción no se puede deshacer.\');">
                <input type="hidden" name="id_publicacion" value="' . $pub['id_publicacion'] . '">
                <button type="submit" style="background:red; color:white;">🗑️ Eliminar publicación</button>
              </form>';

        echo "</p>";
    }

} else {
    echo "<p>No tienes publicaciones todavía.</p>";
}
?>

</body>
</html>

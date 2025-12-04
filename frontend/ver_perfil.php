<?php
// -------------------------------------------------------------
// ver_perfil.php
// Página para ver el perfil de OTROS usuarios.
// NO permite editar nada.
// -------------------------------------------------------------

require_once "../backend/UsuarioBBDD.php";
require_once "../backend/PublicacionBBDD.php";

if (!isset($_SESSION)) {
    session_start();
}

// Si no estás logueado, no puedes ver perfiles
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "No has iniciado sesión.";
    header("Location: ../index.php");
    exit();
}

$bbdd = new UsuarioBBDD();
$publiBBDD = new PublicacionBBDD();

// 1. Comprobamos que llega un ID por la URL
if (!isset($_GET["id"])) {
    die("No se ha especificado ningún usuario.");
}

$idPerfil = intval($_GET["id"]); // ID del usuario que queremos ver
$idLogueado = $_SESSION["id_usuario"];   // ID del usuario conectado

// 2. Obtenemos los datos del usuario que queremos ver
$usuario = $bbdd->getUsuarioPorId($idPerfil);

if (!$usuario) {
    die("El usuario no existe.");
}

// 3. Obtenemos estadísticas del usuario
$seguidores = $bbdd->contarSeguidores($idPerfil);
$seguidos = $bbdd->contarSeguidos($idPerfil);

// 4. Obtenemos sus publicaciones
$publicaciones = $publiBBDD->obtenerPublicacionesPorUsuario($idPerfil);

// 5. Saber si YA lo sigo
$yaLoSigo = $bbdd->existeRelacionSeguimiento($idLogueado, $idPerfil);

// 6. Procesar botón de seguir / dejar de seguir
if (isset($_POST["seguir"])) {
    $bbdd->seguirUsuario($idLogueado, $idPerfil);
    header("Location: ver_perfil.php?id=$idPerfil");
    exit();
}

if (isset($_POST["dejar_seguir"])) {
    $bbdd->dejarDeSeguirUsuario($idLogueado, $idPerfil);
    header("Location: ver_perfil.php?id=$idPerfil");
    exit();
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?= $usuario->nombre_usuario ?></title>
</head>
<body>

<?php require_once "cabecera.php"; ?>

<h1>Perfil de <?= $usuario->nombre_usuario ?></h1>

<!-- NO mostramos el email porque no quieres que sea público -->

<h3>Biografía</h3>
<p><?= $usuario->biografia ?></p>

<h3>Estado emocional</h3>
<p><?= $usuario->estado_emocional ?></p>

<h3>Estadísticas</h3>
<p><strong>Seguidores:</strong> <?= $seguidores ?></p>
<p><strong>Seguidos:</strong> <?= $seguidos ?></p>
<p><strong>Publicaciones:</strong> <?= count($publicaciones) ?></p>

<!-- BOTÓN SEGUIR / DEJAR DE SEGUIR -->
<?php if ($idPerfil != $idLogueado): ?>
    <form action="ver_perfil.php?id=<?= $idPerfil ?>" method="post">
        <?php if ($yaLoSigo): ?>
            <button type="submit" name="dejar_seguir">Dejar de seguir</button>
        <?php else: ?>
            <button type="submit" name="seguir">Seguir</button>
        <?php endif; ?>
    </form>
<?php endif; ?>

<hr>

<h3>Publicaciones de <?= $usuario->nombre_usuario ?></h3>

<?php
if (!empty($publicaciones)) {
    foreach ($publicaciones as $pub) {

        echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>";

        echo "<strong>Estado emocional:</strong> " . $pub["estado_emocional"] . "<br>";
        echo "<strong>Mensaje:</strong> " . nl2br($pub["mensaje"]) . "<br>";
        echo "<em>Publicado el " . $pub["fecha_hora"] . "</em><br>";

        // Número de me gusta
        $totalLikes = $publiBBDD->contarMeGustaPorPublicacion($pub["id_publicacion"]);
        echo "<strong>Me gusta:</strong> " . $totalLikes . "<br>";

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

        echo "</div>";
    }
} else {
    echo "<p>Este usuario no tiene publicaciones todavía.</p>";
}
?>

</body>
</html>

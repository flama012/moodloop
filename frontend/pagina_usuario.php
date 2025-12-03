<?php
require_once "../backend/UsuarioBBDD.php";
require_once "../backend/PublicacionBBDD.php";

if (!isset($_SESSION)) {
    session_start();
}


if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "No has iniciado sesión.";
    header("Location: ../index.php");
    exit();
}


$bbdd = new UsuarioBBDD();

// Cargar datos del usuario desde BD
$infoUsuario = $bbdd->obtenerUsuario($_SESSION["correo"]);//hay que pilla la sesion del correo
$nombreUsuario = $infoUsuario->__get("nombre_usuario");
$biografiaActual = $infoUsuario->__get("biografia");
$idUsuario = $infoUsuario->__get("id_usuario");
$seguidores = $bbdd->contarSeguidores($idUsuario);
$seguidos = $bbdd->contarSeguidos($idUsuario);


$publiBBDD = new PublicacionBBDD();
$totalPublicaciones = $publiBBDD->contarPublicacionesPorUsuario($idUsuario);
$misPublicaciones = $publiBBDD->obtenerPublicacionesPorUsuario($idUsuario);


// PROCESAR FORMULARIO DE BIOGRAFÍA
if (isset($_POST["guardar_biografia"])) {
    $nuevaBio = $_POST["biografia"];
    $idUsuario = $infoUsuario->__get("id_usuario");

    if ($bbdd->actualizarBiografia($idUsuario, $nuevaBio)) {
        $_SESSION["mensaje"] = "Biografía actualizada correctamente.";
        $_SESSION["biografia"] = $nuevaBio;
        header("Location: pagina_usuario.php");
        exit;

    } else {
        $_SESSION["error"] = "Error al actualizar la biografía.";
    }
}

// PROCESAR ESTADO EMOCIONAL
if (isset($_POST['submit'])) {
    $estado = $_POST["estado_emocional"];
    $idUsuario = $infoUsuario->__get("id_usuario");
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
<?php
require_once "cabecera.php";


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

<h3>Estadísticas</h3>
<p><strong>Seguidores:</strong> <?= $seguidores ?></p>
<p><strong>Seguidos:</strong> <?= $seguidos ?></p>
<p><strong>Publicaciones:</strong> <?= $totalPublicaciones ?></p>


<!-- FORMULARIO DE BIOGRAFÍA -->
<h3>Biografía</h3>
<form action="pagina_usuario.php" method="post">
    <textarea name="biografia" rows="4" cols="40"><?= $biografiaActual ?></textarea>
    <br><br>
    <button type="submit" name="guardar_biografia">Guardar biografía</button>
</form>

<!-- FORMULARIO ESTADO EMOCIONAL -->
<h3>Estado emocional</h3>
<form action="pagina_usuario.php" method="post">
    <label for="estado_emocional">Estado emocional</label>
    <select id="estado_emocional" name="estado_emocional" required>
        <option value="" disabled selected>Selecciona tu estado…</option>
        <option value="Feliz">Feliz</option>
        <option value="Neutral">Neutral</option>
        <option value="Triste">Triste</option>
        <option value="Ansioso">Ansioso</option>
        <option value="Estresado">Estresado</option>
        <option value="Enfadado">Enfadado</option>
        <option value="Cansado">Cansado</option>
        <option value="Motivado">Motivado</option>
        <option value="Agradecido">Agradecido</option>
    </select>
    <br><br>
    <button type="submit" name="submit">Publicar</button>
</form>
<h3>Mis publicaciones</h3>
<?php
if (!empty($misPublicaciones)) {
    foreach ($misPublicaciones as $pub) {
        echo "<p>";
        echo "<strong>Estado emocional:</strong> " . $pub["estado_emocional"] . "<br>";
        echo "<strong>Mensaje:</strong> " . nl2br($pub["mensaje"]) . "<br>";
        echo "<em>Publicado el " . $pub["fecha_hora"] . "</em><br>";

        // Mostrar número de me gusta
        $totalLikes = $publiBBDD->contarMeGustaPorPublicacion($pub["id_publicacion"]);
        echo "<strong>Me gusta:</strong> " . $totalLikes . "<br>";

        // Mostrar comentarios
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

        echo "</p>";
    }
} else {
    echo "<p>No tienes publicaciones todavía.</p>";
}
?>


</body>
</html>

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

    <link rel="stylesheet" href="css/cabecera.css">
    <link rel="stylesheet" href="css/pagina_usuario.css">
</head>
<body>

<?php require_once "cabecera.php"; ?>

<!-- ============================================================
     MENSAJES DE ÉXITO / ERROR
============================================================ -->
<?php if (isset($_SESSION["error"])): ?>
    <div class="mensaje-error"><?= $_SESSION["error"] ?></div>
    <?php unset($_SESSION["error"]); ?>
<?php endif; ?>

<?php if (isset($_SESSION["mensaje"])): ?>
    <div class="mensaje-exito"><?= $_SESSION["mensaje"] ?></div>
    <?php unset($_SESSION["mensaje"]); ?>
<?php endif; ?>


<!-- ============================================================
     CONTENEDOR PRINCIPAL DEL PERFIL
============================================================ -->
<div class="perfil-contenedor">

    <!-- ============================================================
         CABECERA DEL PERFIL
    ============================================================ -->
    <div class="perfil-header">

        <!-- Nombre del usuario -->
        <h2 class="perfil-nombre"><?= $nombreUsuario ?></h2>

        <!-- ESTADÍSTICAS -->
        <div class="perfil-estadisticas">

            <!-- Publicaciones SIN enlace -->
            <div>
                <strong><?= $totalPublicaciones ?></strong>
                <span>Publicaciones</span>
            </div>

            <!-- Seguidores CON enlace -->
            <div>
                <a href="ver_seguidores.php?id=<?= $_SESSION['id_usuario'] ?>" class="perfil-link-bloque">
                    <strong><?= $seguidores ?></strong>
                    <span>Seguidores</span>
                </a>
            </div>

            <!-- Seguidos CON enlace -->
            <div>
                <a href="ver_seguidos.php?id=<?= $_SESSION['id_usuario'] ?>" class="perfil-link-bloque">
                    <strong><?= $seguidos ?></strong>
                    <span>Seguidos</span>
                </a>
            </div>

        </div>


        <!-- ESTADO EMOCIONAL -->
        <div class="perfil-estado-bloque">
            <h3>Estado emocional</h3>
            <p class="perfil-estado-texto"><?= $estadoActual ?></p>

            <button class="btn-editar" onclick="toggleEstado()">Editar estado</button>

            <form action="pagina_usuario.php" method="post" class="perfil-form oculto" id="formEstado">
                <select name="estado_emocional" class="perfil-select" required>
                    <option value="" disabled>Selecciona tu estado…</option>
                    <?php foreach ($emociones as $emo): ?>
                        <option value="<?= $emo ?>" <?= ($emo == $estadoActual) ? "selected" : "" ?>>
                            <?= $emo ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="submit" class="btn-guardar">Guardar</button>
            </form>
        </div>

        <!-- BIOGRAFÍA -->
        <div class="perfil-bio-bloque">
            <h3>Biografía</h3>
            <p class="perfil-bio-texto"><?= nl2br($biografiaActual) ?></p>

            <button class="btn-editar" onclick="toggleBio()">Editar biografía</button>

            <form action="pagina_usuario.php" method="post" class="perfil-form oculto" id="formBio">
                <textarea name="biografia" class="perfil-textarea" maxlength="255"><?= $biografiaActual ?></textarea>
                <button type="submit" name="guardar_biografia" class="btn-guardar">Guardar</button>
            </form>
        </div>

    </div>


    <!-- ============================================================
         TÍTULO DE PUBLICACIONES
    ============================================================ -->
    <h2 class="titulo-publicaciones">Mis publicaciones</h2>


    <!-- ============================================================
         LISTADO DE PUBLICACIONES
============================================================ -->
    <div class="perfil-publicaciones">

        <?php if (!empty($misPublicaciones)): ?>

            <?php foreach ($misPublicaciones as $pub): ?>
                <div class="card-publicacion">

                    <!-- CABECERA -->
                    <div class="pub-header">

                        <span class="pub-fecha"><?= $pub["fecha_hora"] ?></span>

                        <div class="pub-header-right">
                            <span class="pub-emocion emocion-animada"><?= $pub["estado_emocional"] ?></span>

                            <form action="../backend/eliminar_publicacion.php" method="post" class="btn-eliminar"
                                  onsubmit="return confirm('¿Seguro que quieres eliminar esta publicación?');">
                                <input type="hidden" name="id_publicacion" value="<?= $pub['id_publicacion'] ?>">
                                <button type="submit">
                                    <img src="../assets/delete.svg" alt="Eliminar">
                                </button>
                            </form>
                        </div>

                    </div>

                    <p class="pub-mensaje"><?= nl2br($pub["mensaje"]) ?></p>

                    <?php $etis = $publiBBDD->obtenerEtiquetasPorPublicacion($pub["id_publicacion"]); ?>
                    <?php if (!empty($etis)): ?>
                        <div class="pub-etiquetas">
                            <strong>Etiquetas:</strong> #<?= implode(" #", $etis) ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    $likes = $publiBBDD->contarMeGustaPorPublicacion($pub["id_publicacion"]);
                    $yaLeDioMG = $publiBBDD->usuarioDioMG($_SESSION["id_usuario"], $pub["id_publicacion"]);
                    ?>

                    <div class="pub-likes-block">

                        <form action="../backend/procesar_like.php" method="post">
                            <input type="hidden" name="id_publicacion" value="<?php echo $pub['id_publicacion']; ?>">

                            <button type="submit" class="like-button">
                                <img src="../assets/<?php echo $yaLeDioMG ? 'like-heart.svg' : 'like-heart2.svg'; ?>">
                            </button>
                        </form>

                        <span class="like-count"><?php echo $likes; ?></span>

                    </div>

                    <?php $comentarios = $publiBBDD->obtenerComentariosPorPublicacion($pub["id_publicacion"]); ?>

                    <?php if (!empty($comentarios)): ?>
                        <div class="pub-comentarios">
                            <strong>Comentarios:</strong><br>
                            <?php foreach ($comentarios as $c): ?>
                                <p class="comentario"><em><strong>@<?= $c["nombre_usuario"] ?>:</strong></em> <?= $c["texto"] ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="comentario-vacio">Sin comentarios.</p>
                    <?php endif; ?>

                    <form action="../backend/procesar_comentario.php" method="post" class="pub-comentar-flex">
                        <input type="hidden" name="id_publicacion" value="<?= $pub['id_publicacion'] ?>">
                        <div class="comentar-contenedor">
                            <textarea name="comentario" class="comentario-input" placeholder="Escribe un comentario..."></textarea>
                            <button type="submit" class="btn-principal btn-comentar">Comentar</button>
                        </div>
                    </form>

                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p class="perfil-sin-publicaciones">Todavía no has publicado nada.</p>
        <?php endif; ?>

    </div>

</div>


<!-- ============================================================
     SCRIPTS
============================================================ -->
<script>
    function toggleBio() {
        document.getElementById("formBio").classList.toggle("oculto");
    }

    function toggleEstado() {
        document.getElementById("formEstado").classList.toggle("oculto");
    }
</script>

</body>



</html>

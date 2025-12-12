<?php
// -------------------------------------------------------------
// ver_perfil.php
// Página para ver el perfil de OTROS usuarios.
// -------------------------------------------------------------

require_once "../backend/UsuarioBBDD.php";
require_once "../backend/PublicacionBBDD.php";

// Iniciar sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Si el usuario no ha iniciado sesión, no puede ver perfiles
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "No has iniciado sesión.";
    header("Location: ../index.php");
    exit();
}

// Creamos los objetos para acceder a la base de datos
$bbdd = new UsuarioBBDD();
$publiBBDD = new PublicacionBBDD();

// ============================================================
// 1. COMPROBAR QUE SE HA RECIBIDO UN ID POR LA URL
// ============================================================
if (!isset($_GET["id"])) {
    die("No se ha especificado ningún usuario.");
}

$idPerfil = intval($_GET["id"]);        // ID del usuario que queremos ver
$idLogueado = $_SESSION["id_usuario"];  // ID del usuario conectado

// ============================================================
// 2. OBTENER DATOS DEL USUARIO A VISUALIZAR
// ============================================================
$usuario = $bbdd->getUsuarioPorId($idPerfil);

if (!$usuario) {
    die("El usuario no existe.");
}

// ============================================================
// 3. OBTENER ESTADÍSTICAS DEL PERFIL
// ============================================================
$seguidores = $bbdd->contarSeguidores($idPerfil);
$seguidos   = $bbdd->contarSeguidos($idPerfil);

// ============================================================
// 4. OBTENER PUBLICACIONES DEL USUARIO
// ============================================================
$publicaciones = $publiBBDD->obtenerPublicacionesPorUsuario($idPerfil);

// ============================================================
// 5. COMPROBAR SI EL USUARIO LOGUEADO YA SIGUE A ESTE PERFIL
// ============================================================
$yaLoSigo = $bbdd->existeRelacionSeguimiento($idLogueado, $idPerfil);

// ============================================================
// 6. PROCESAR BOTONES SEGUIR / DEJAR DE SEGUIR
// ============================================================
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

    <link rel="stylesheet" href="css/cabecera.css">
    <link rel="stylesheet" href="css/ver_perfil.css">
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
         CABECERA DEL PERFIL (versión para OTROS usuarios)
    ============================================================= -->
    <div class="perfil-header">

        <!-- Nombre del usuario -->
        <h2 class="perfil-nombre"><?= $usuario->nombre_usuario ?></h2>

        <!-- ESTADÍSTICAS -->
        <div class="perfil-estadisticas">
            <div><strong><?= count($publicaciones) ?></strong><span>Publicaciones</span></div>
            <div><strong><?= $seguidores ?></strong><span>Seguidores</span></div>
            <div><strong><?= $seguidos ?></strong><span>Seguidos</span></div>
        </div>

        <!-- ESTADO EMOCIONAL -->
        <div class="perfil-estado-bloque">
            <h3>Estado emocional</h3>
            <p class="perfil-estado-texto"><?= $usuario->estado_emocional ?></p>
        </div>

        <!-- BIOGRAFÍA -->
        <div class="perfil-bio-bloque">
            <h3>Biografía</h3>
            <p class="perfil-bio-texto"><?= nl2br($usuario->biografia) ?></p>
        </div>

        <!-- BOTÓN SEGUIR / DEJAR DE SEGUIR -->
        <?php if ($idPerfil != $idLogueado): ?>
            <form action="ver_perfil.php?id=<?= $idPerfil ?>" method="post">
                <?php if ($yaLoSigo): ?>
                    <button type="submit" name="dejar_seguir" class="btn-guardar" style="background: linear-gradient(135deg,#e63946,#ff8c42);">
                        Dejar de seguir
                    </button>
                <?php else: ?>
                    <button type="submit" name="seguir" class="btn-guardar">
                        Seguir
                    </button>
                <?php endif; ?>
            </form>
        <?php endif; ?>

    </div>


    <!-- ============================================================
         TÍTULO DE PUBLICACIONES
    ============================================================ -->
    <h2 class="titulo-publicaciones">Publicaciones de <?= $usuario->nombre_usuario ?></h2>


    <!-- ============================================================
         LISTADO DE PUBLICACIONES (idéntico al feed)
    ============================================================ -->
    <div class="perfil-publicaciones">

        <?php if (!empty($publicaciones)): ?>

            <?php foreach ($publicaciones as $pub): ?>
                <div class="card-publicacion">

                    <!-- CABECERA: fecha a la izquierda, emoción a la derecha -->
                    <div class="pub-header">

                        <!-- Fecha -->
                        <span class="pub-fecha"><?= $pub["fecha_hora"] ?></span>

                        <!-- Emoción -->
                        <div class="pub-header-right">
                            <span class="pub-emocion emocion-animada"><?= $pub["estado_emocional"] ?></span>
                        </div>

                    </div>

                    <!-- MENSAJE -->
                    <p class="pub-mensaje"><?= nl2br($pub["mensaje"]) ?></p>

                    <!-- ETIQUETAS -->
                    <?php $etis = $publiBBDD->obtenerEtiquetasPorPublicacion($pub["id_publicacion"]); ?>
                    <?php if (!empty($etis)): ?>
                        <div class="pub-etiquetas">
                            <strong>Etiquetas:</strong> #<?= implode(" #", $etis) ?>
                        </div>
                    <?php endif; ?>

                    <!-- BLOQUE MG -->
                    <!-- BLOQUE MG -->
                    <?php
                    $likes = $publiBBDD->contarMeGustaPorPublicacion($pub["id_publicacion"]);
                    $yaLeDioMG = $publiBBDD->usuarioDioMG($_SESSION["id_usuario"], $pub["id_publicacion"]);
                    ?>

                    <div class="pub-likes-block">

                        <form action="../backend/procesar_like.php" method="post" class="like-form">
                            <input type="hidden" name="id_publicacion" value="<?php echo $pub['id_publicacion']; ?>">

                            <button type="submit" class="like-button">
                                <img src="../assets/<?php echo $yaLeDioMG ? 'like-heart.svg' : 'like-heart2.svg'; ?>" alt="Me gusta">
                            </button>
                        </form>

                        <span class="like-count"><?php echo $likes; ?></span>

                    </div>


                    <!-- COMENTARIOS -->
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

                    <!-- FORMULARIO COMENTAR -->
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
            <p class="perfil-sin-publicaciones">Este usuario no tiene publicaciones todavía.</p>
        <?php endif; ?>

    </div>

</div>

</body>

</html>

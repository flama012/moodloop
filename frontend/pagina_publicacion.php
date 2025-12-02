<?php
require_once "../backend/Publicacion.php";
require_once "../backend/PublicacionBBDD.php";

// Iniciamos sesión
if (!isset($_SESSION)) {
    session_start();
}

// Validamos que el usuario esté logueado
if (!isset($_SESSION["id_usuario"])) {
    echo "Debes iniciar sesión para crear publicaciones.";
    exit();
}

// Incluimos la clase Publicacion z
require_once "Publicacion.php";

// Creamos un objeto de la clase Publicacion
$publicacion = new Publicacion();

// Variables para mensajes
$mensaje_exito = "";
$mensaje_error = "";

// Revisamos si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensaje = $_POST["mensaje"];
    $estado = $_POST["estado_emocional"];

    // Validamos que no estén vacíos
    if ($mensaje != "" && $estado != "") {
        // Llamamos al método crearPublicacion
        $resultado = $publicacion->crearPublicacion($_SESSION["id_usuario"], $mensaje, $estado);

        if ($resultado == true) {
            $mensaje_exito = "¡Publicación creada correctamente!";
        } else {
            $mensaje_error = "Error al crear la publicación.";
        }
    } else {
        $mensaje_error = "Todos los campos son obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear publicación</title>
</head>
<body>
<h1>CREAR NUEVA PUBLICACIÓN</h1>

<!-- Menú simple -->
<p>
    <a href="pagina_feed.php">FEED</a> |
    <a href="pagina_usuario.php">USUARIO</a> |
    <a href="pagina_publicacion.php">CREAR PUBLICACIÓN</a>
</p>

<!-- Mostrar mensajes -->
<?php
if ($mensaje_exito != "") {
    echo "<p style='color:green'>" . $mensaje_exito . "</p>";
}
if ($mensaje_error != "") {
    echo "<p style='color:red'>" . $mensaje_error . "</p>";
}
?>

<!-- Formulario para crear publicación -->
<form method="POST">
    <label>Mensaje:</label><br>
    <textarea name="mensaje" rows="4" cols="50"><?= isset($_POST["mensaje"]) ? $_POST["mensaje"] : "" ?></textarea><br><br>

    <label>Estado emocional:</label><br>
    <select name="estado_emocional">
        <option value="">Selecciona...</option>
        <option value="Feliz" <?= (isset($_POST["estado_emocional"]) && $_POST["estado_emocional"]=="Feliz") ? "selected" : "" ?>>Feliz</option>
        <option value="Triste" <?= (isset($_POST["estado_emocional"]) && $_POST["estado_emocional"]=="Triste") ? "selected" : "" ?>>Triste</option>
        <option value="Enojado" <?= (isset($_POST["estado_emocional"]) && $_POST["estado_emocional"]=="Enojado") ? "selected" : "" ?>>Enojado</option>
        <option value="Ansioso" <?= (isset($_POST["estado_emocional"]) && $_POST["estado_emocional"]=="Ansioso") ? "selected" : "" ?>>Ansioso</option>
        <option value="Motivado" <?= (isset($_POST["estado_emocional"]) && $_POST["estado_emocional"]=="Motivado") ? "selected" : "" ?>>Motivado</option>
    </select><br><br>

    <input type="submit" value="Publicar">
</form>

<hr>

<h2>Últimas publicaciones</h2>
<?php
// Mostrar publicaciones usando el método de la clase
$publicacion->mostrarPublicacionesHTML(10);
?>
</body>
</html>


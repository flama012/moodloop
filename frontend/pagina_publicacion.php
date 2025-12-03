<?php
require_once "../backend/PublicacionBBDD.php";

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["id_usuario"])) {
    echo "Debes iniciar sesión para crear publicaciones.";
    exit();
}

$publicacion = new PublicacionBBDD();

$mensaje_exito = "";
$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensaje = $_POST["mensaje"];
    $estado = $_POST["estado_emocional"];
    $etiquetasTexto = $_POST["etiquetas"] ?? "";

    // Convertir etiquetas separadas por #
    $etiquetas = [];
    if (!empty($etiquetasTexto)) {
        $etiquetas = array_filter(array_map('trim', explode('#', $etiquetasTexto)));
        $etiquetas = array_slice($etiquetas, 0, 5); // máximo 5
    }

    if ($mensaje != "" && $estado != "") {
        // Crear publicación
        $idPublicacion = $publicacion->crearPublicacion($_SESSION["id_usuario"], $mensaje, $estado);

        if ($idPublicacion !== false) {
            // Guardar etiquetas si hay
            if (!empty($etiquetas)) {
                $publicacion->agregarEtiquetasAPublicacion($idPublicacion, $etiquetas);
            }
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

<?php require_once "cabecera.php"; ?>

<?php if ($mensaje_exito != "") echo "<p style='color:green'>$mensaje_exito</p>"; ?>
<?php if ($mensaje_error != "") echo "<p style='color:red'>$mensaje_error</p>"; ?>

<h1>CREAR NUEVA PUBLICACIÓN</h1>

<form method="POST">
    <label>Mensaje:</label><br>
    <textarea name="mensaje" rows="4" cols="50"></textarea><br><br>

    <label>Estado emocional:</label><br>
    <select name="estado_emocional">
        <option value="">Selecciona...</option>
        <option value="Feliz">Feliz</option>
        <option value="Triste">Triste</option>
        <option value="Enojado">Enojado</option>
        <option value="Ansioso">Ansioso</option>
        <option value="Motivado">Motivado</option>
    </select><br><br>

    <label>Etiquetas (máx 5, separadas por #):</label><br>
    <input type="text" name="etiquetas" placeholder="#motivacion#felicidad#programacion"><br><br>

    <input type="submit" value="Publicar">
</form>

</body>
</html>

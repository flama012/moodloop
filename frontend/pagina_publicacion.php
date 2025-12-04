<?php
// Incluimos las clases necesarias
require_once "../backend/PublicacionBBDD.php";

// Iniciamos sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Verificamos que el usuario esté logueado (versión correcta)
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "Debes iniciar sesión para crear publicaciones.";
    header("Location: ../index.php");
    exit();
}

// Creamos el objeto que gestiona las publicaciones
$publicacion = new PublicacionBBDD();

// Mensajes para mostrar al usuario
$mensaje_exito = "";
$mensaje_error = "";

// Lista fija de emociones (coincide con el feed)
$listaEmociones = [
        "Feliz","Triste","Enojado","Ansioso","Motivado","Agradecido","Cansado","Estresado","Enfadado",
        "Sorprendido","Confundido","Esperanzado","Orgulloso","Relajado","Nostálgico","Melancólico",
        "Entusiasmado","Frustrado","Optimista","Pesimista","Aburrido","Curioso","Apático","Satisfecho",
        "Decepcionado","Inspirado","Resignado","Aliviado","Preocupado"
];

// Si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogemos los datos del formulario
    $mensaje = $_POST["mensaje"] ?? "";
    $estado = $_POST["estado_emocional"] ?? "";
    $etiquetasTexto = $_POST["etiquetas"] ?? "";

    // Procesamos las etiquetas separadas por #
    $etiquetas = [];
    if (!empty($etiquetasTexto)) {
        $etiquetas = array_filter(array_map('trim', explode('#', $etiquetasTexto)));
        $etiquetas = array_slice($etiquetas, 0, 5);
    }

    // Validamos que mensaje y emoción no estén vacíos
    if ($mensaje != "" && $estado != "") {
        $idPublicacion = $publicacion->crearPublicacion($_SESSION["id_usuario"], $mensaje, $estado);

        if ($idPublicacion !== false) {
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

<h1>CREAR NUEVA PUBLICACIÓN</h1>

<!-- Mensajes de éxito o error -->
<?php if ($mensaje_exito != "") echo "<p style='color:green'>$mensaje_exito</p>"; ?>
<?php if ($mensaje_error != "") echo "<p style='color:red'>$mensaje_error</p>"; ?>

<!-- Formulario para crear publicación -->
<form method="POST">
    <label>Mensaje:</label><br>
    <textarea name="mensaje" rows="4" cols="50"></textarea><br><br>

    <label>Estado emocional:</label><br>
    <select name="estado_emocional" required>
        <option value="">Selecciona...</option>
        <?php
        foreach ($listaEmociones as $emo) {
            echo "<option value='$emo'>$emo</option>";
        }
        ?>
    </select>
    <br><br>

    <label>Etiquetas (máx 5, separadas por #):</label><br>
    <input type="text" name="etiquetas" placeholder="#motivacion#felicidad"><br><br>

    <button type="submit">Publicar</button>
</form>

</body>
</html>

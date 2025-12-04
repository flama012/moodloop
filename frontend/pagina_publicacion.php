<?php
// Cargamos la clase que maneja las publicaciones en la base de datos
require_once "../backend/PublicacionBBDD.php";

// Iniciamos sesión si aún no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Si el usuario no ha iniciado sesión, no puede crear publicaciones
if (!isset($_SESSION["usuario"])) {
    $_SESSION["error"] = "Debes iniciar sesión para crear publicaciones.";
    header("Location: ../index.php");
    exit();
}

// Creamos el objeto que gestiona las publicaciones
$publicacion = new PublicacionBBDD();

// Variables para mostrar mensajes al usuario
$mensaje_exito = "";
$mensaje_error = "";

// Lista fija de emociones (la misma que en el feed)
$listaEmociones = [
        "Feliz","Triste","Enojado","Ansioso","Motivado","Agradecido","Cansado","Estresado","Enfadado",
        "Sorprendido","Confundido","Esperanzado","Orgulloso","Relajado","Nostálgico","Melancólico",
        "Entusiasmado","Frustrado","Optimista","Pesimista","Aburrido","Curioso","Apático","Satisfecho",
        "Decepcionado","Inspirado","Resignado","Aliviado","Preocupado"
];

// ============================================================
// PROCESAR FORMULARIO CUANDO EL USUARIO ENVÍA UNA PUBLICACIÓN
// ============================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recogemos los datos enviados por el formulario
    $mensaje = $_POST["mensaje"] ?? "";
    $estado = $_POST["estado_emocional"] ?? "";
    $etiquetasTexto = $_POST["etiquetas"] ?? "";

    // Convertimos "#ejemplo#prueba" → ["ejemplo", "prueba"]
    $etiquetas = [];
    if (!empty($etiquetasTexto)) {
        $etiquetas = array_filter(array_map('trim', explode('#', $etiquetasTexto)));
        $etiquetas = array_slice($etiquetas, 0, 5); // Máximo 5 etiquetas
    }

    // Validamos que el mensaje y la emoción no estén vacíos
    if ($mensaje != "" && $estado != "") {

        // Creamos la publicación en la base de datos
        $idPublicacion = $publicacion->crearPublicacion($_SESSION["id_usuario"], $mensaje, $estado);

        // Si se creó correctamente
        if ($idPublicacion !== false) {

            // Si hay etiquetas, las añadimos
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

<!-- ============================================================
     FORMULARIO PARA CREAR UNA PUBLICACIÓN
============================================================ -->
<form method="POST">

    <label>Mensaje:</label><br>
    <textarea name="mensaje" rows="4" cols="50"></textarea><br><br>

    <label>Estado emocional:</label><br>
    <select name="estado_emocional" required>
        <option value="">Selecciona...</option>
        <?php
        // Mostramos todas las emociones disponibles
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

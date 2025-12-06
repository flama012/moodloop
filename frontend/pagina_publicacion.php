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
<link href="css/cabecera.css" rel="stylesheet">
<link href="css/crear_publicacion.css" rel="stylesheet">
<body>

<?php require_once "cabecera.php"; ?>

<div class="crear-publicacion-contenedor">

    <div class="crear-header">
        <h2>Crear publicación</h2>
        <button type="submit" form="form-publicacion" class="btn-publicar">Publicar</button>
    </div>

    <!-- Mensajes de éxito o error -->
    <?php if ($mensaje_exito != "") echo "<p class='mensaje-exito'>$mensaje_exito</p>"; ?>
    <?php if ($mensaje_error != "") echo "<p class='mensaje-error'>$mensaje_error</p>"; ?>

    <form method="POST" id="form-publicacion" class="form-publicacion">

        <!-- Selector de emoción -->
        <label for="estado_emocional">¿Cómo te sientes?</label>
        <select name="estado_emocional" id="estado_emocional" required>
            <option value="">Selecciona...</option>
            <?php foreach ($listaEmociones as $emo) {
                echo "<option value='$emo'>$emo</option>";
            } ?>
        </select>

        <!-- Textarea con contador -->
        <label for="mensaje">Comparte cómo te sientes...</label>
        <textarea name="mensaje" id="mensaje" maxlength="500" placeholder="Comparte cómo te sientes..."></textarea>
        <div class="contador-caracteres"><span id="contador">0</span> / 500</div>

        <!-- Etiquetas -->
        <label for="etiquetas">Añadir etiquetas (máx. 5)</label>
        <input type="text" name="etiquetas" id="etiquetas" placeholder="#motivacion#felicidad">
    </form>
</div>

<script>
    // Contador de caracteres
    const mensaje = document.getElementById("mensaje");
    const contador = document.getElementById("contador");
    mensaje.addEventListener("input", () => {
        contador.textContent = mensaje.value.length;
    });
</script>

</body>

</html>

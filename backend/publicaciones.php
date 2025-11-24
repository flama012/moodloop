<?php
include "db.php";

$sql = "SELECT mensaje, estado_emocional, fecha_hora FROM Publicaciones ORDER BY fecha_hora DESC";
$resultado = mysqli_query($conn, $sql);

if (mysqli_num_rows($resultado) > 0) {
    echo "<h2>Publicaciones recientes</h2>";
    while ($fila = mysqli_fetch_assoc($resultado)) {
        echo "<p><strong>" . $fila["estado_emocional"] . "</strong>: " . $fila["mensaje"] . "<br><em>" . $fila["fecha_hora"] . "</em></p>";
    }
} else {
    echo "No hay publicaciones.";
}
?>

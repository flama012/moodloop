<?php
include "db.php";

$sql = "SELECT id_usuario, nombre_usuario, correo FROM Usuarios";
$resultado = mysqli_query($conn, $sql);

if (mysqli_num_rows($resultado) > 0) {
    echo "<h2>Lista de usuarios</h2>";
    echo "<ul>";
    while ($fila = mysqli_fetch_assoc($resultado)) {
        echo "<li>" . $fila["nombre_usuario"] . " (" . $fila["correo"] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "No hay usuarios registrados.";
}
?>

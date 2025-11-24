<?php
$host = "localhost";
$usuario = "root";
$contrasena = "Ciclo2gs"; // tu contraseña
$base_datos = "moodloop";

// Crear conexión
$conn = mysqli_connect($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if (!$conn) {
    echo "Error de conexión: " . mysqli_connect_error();
    exit;
}
?>

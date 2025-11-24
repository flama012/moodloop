<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; // tu contraseña
$base_datos = "red_social";

// Crear conexión
$conn = mysqli_connect($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if (!$conn) {
    echo "Error de conexión: " . mysqli_connect_error();
    exit;
}
?>

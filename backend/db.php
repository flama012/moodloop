<?php
// db.php
// Este archivo contiene la función para conectar a la base de datos MySQL

function conectar() {
    // Datos de conexión
    $host = "localhost";          // Servidor de la base de datos
    $usuario = "root";            // Usuario de MySQL
    $contrasena = "Ciclo2gs";     // Contraseña de MySQL
    $base_datos = "moodloop";     // Nombre de la base de datos

    // Crear conexión
    $conn = mysqli_connect($host, $usuario, $contrasena, $base_datos);

    // Verificar si la conexión fue exitosa
    if (!$conn) {
        echo "Error de conexión: " . mysqli_connect_error();
        exit; // Detener el programa si falla
    }

    // Si todo va bien, devolvemos la conexión
    return $conn;
}
?>

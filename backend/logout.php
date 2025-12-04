<?php
// Iniciamos la sesión si aún no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Borramos todas las variables de la sesión
$_SESSION = [];

// Cerramos la sesión completamente
session_destroy();

// Enviamos al usuario a la página de inicio
header("Location: ../index.php");
exit();

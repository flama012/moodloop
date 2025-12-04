<?php
// Iniciamos la sesión para acceder a los datos guardados
session_start();

// Cargamos la clase que envía correos
require_once "../backend/Correo.php";

// ============================================================
// 1. COMPROBAR QUE TENEMOS LOS DATOS NECESARIOS
// ============================================================
// Si no tenemos el correo o el token guardados en la sesión,
// significa que el usuario no debería estar aquí.
if (!isset($_SESSION["correoNoVerificado"]) || !isset($_SESSION["tokenNoVerificado"])) {
    header("Location: login.php");
    exit();
}

// Recuperamos el correo y el token que se guardaron cuando el usuario intentó iniciar sesión
$correo = $_SESSION["correoNoVerificado"];
$token  = $_SESSION["tokenNoVerificado"];

// ============================================================
// 2. INTENTAR REENVIAR EL CORREO DE VERIFICACIÓN
// ============================================================

$correoObj = new Correo();
$resultado = $correoObj->enviarCorreoRegistro($correo, $token);

// Guardamos un mensaje para mostrarlo en login.php
if ($resultado) {
    $_SESSION["mensaje"] = "Correo reenviado correctamente. Revisa tu bandeja.";
} else {
    $_SESSION["mensaje"] = "No se pudo reenviar el correo. Inténtalo más tarde.";
}

// ============================================================
// 3. REDIRIGIR AL LOGIN
// ============================================================

header("Location: login.php");
exit();

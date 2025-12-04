<?php
session_start();
require_once "../backend/Correo.php";

if (!isset($_SESSION["correoNoVerificado"]) || !isset($_SESSION["tokenNoVerificado"])) {
    header("Location: login.php");
    exit();
}

$correo = $_SESSION["correoNoVerificado"];
$token  = $_SESSION["tokenNoVerificado"];

$correoObj = new Correo();
$resultado = $correoObj->enviarCorreoRegistro($correo, $token);

if ($resultado) {
    $_SESSION["mensaje"] = "Correo reenviado correctamente. Revisa tu bandeja.";
} else {
    $_SESSION["mensaje"] = "No se pudo reenviar el correo. Inténtalo más tarde.";
}

header("Location: login.php");
exit();

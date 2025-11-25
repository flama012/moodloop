<?php
// usuarios.php
// Script para mostrar usuarios usando la clase Usuario

require_once "Usuario.php"; // Incluimos la clase Usuario

// Creamos un objeto de tipo Usuario
$u = new Usuario();

// Llamamos al método que muestra usuarios en HTML
$u->mostrarUsuariosHTML();
?>

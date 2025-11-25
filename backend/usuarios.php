<?php
// usuarios.php
// Script para mostrar usuarios usando la clase UsuarioBBDD

require_once "UsuarioBBDD.php"; // Incluimos la clase UsuarioBBDD

// Creamos un objeto de tipo UsuarioBBDD
$u = new UsuarioBBDD();

// Llamamos al método que muestra usuarios en HTML
$u->mostrarUsuariosHTML();
?>

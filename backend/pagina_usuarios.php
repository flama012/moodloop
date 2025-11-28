<?php
// pagina_usuarios.php
// Script para mostrar usuarios registrados usando la clase UsuarioBBDD

require_once "UsuarioBBDD.php"; // Incluimos la clase que gestiona usuarios y conexión

// Creamos un objeto de tipo UsuarioBBDD, que internamente usa el Singleton de conexión
$u = new UsuarioBBDD();

// Llamamos al método que muestra los usuarios en formato HTML
$u->mostrarUsuariosHTML();
?>

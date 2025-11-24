<?php
// publicaciones.php
// Script para mostrar publicaciones usando la clase Publicacion

require_once "Publicacion.php"; // Incluimos la clase Publicacion

// Creamos un objeto de tipo Publicacion
$p = new Publicacion();

// Mostramos las últimas 5 publicaciones
$p->mostrarPublicacionesHTML(5);
?>

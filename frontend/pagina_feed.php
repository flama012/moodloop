<?php
// Iniciamos sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Feed</title>
</head>
<body>

<h1>FEED</h1>

<!-- Menú de navegación principal -->
<h3>Menú:</h3>
<nav>
    <a href="pagina_feed.php">FEED</a> <!-- Página actual -->
    <a href="pagina_usuario.php">USUARIO</a> <!-- Perfil del usuario -->
    <a href="pagina_publicacion.php">CREAR PUBLICACIÓN</a> <!-- Formulario para publicar -->
</nav>

</body>
</html>

<?php
// Evita acceso directo
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("Location: ../index.php");
    exit();
}
?>

<!-- CABECERA MOODLOOP -->
<header class="cabecera-moodloop">

    <!-- 1. Logo -->
    <div class="cabecera-logo">
        <a href="pagina_feed.php">
            <img src="../assets/logo.PNG" alt="MoodLoop">
        </a>
    </div>

    <!-- 2. Buscador -->
    <div class="cabecera-buscador">
        <form action="buscar_usuario.php" method="GET">
            <input type="text" name="q" placeholder="Buscar usuarios...">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <!-- 3. Navegación -->
    <nav class="cabecera-nav">
        <a href="pagina_feed.php">Feed</a>
        <a href="pagina_usuario.php">Usuario</a>
        <a href="pagina_publicacion.php">Crear publicación</a>
        <a href="../backend/logout.php" class="btn-salir" onclick="return confirm('¿Seguro que quieres cerrar sesión?');">Salir</a>
    </nav>

</header>

<?php
// Evita que alguien abra este archivo directamente en el navegador
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("Location: ../index.php");
    exit();
}
?>

<!-- CABECERA MOODLOOP -->
<header class="cabecera-moodloop">

    <!-- LOGO: clic para volver al feed -->
    <div class="cabecera-logo">
        <a href="pagina_feed.php">
            <img src="../assets/logo.PNG" alt="MoodLoop">
        </a>
    </div>

    <!-- BUSCADOR DE USUARIOS -->
    <div class="cabecera-buscador">
        <form action="buscar_usuario.php" method="GET">
            <input type="text" name="q" placeholder="Buscar usuarios...">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <!-- MENÚ DE NAVEGACIÓN CON ICONOS -->
    <nav class="cabecera-nav">
        <a href="pagina_feed.php" title="Feed">
            <img src="../assets/house.svg" alt="Feed">
        </a>
        <a href="pagina_publicacion.php" title="Crear publicación">
            <img src="../assets/create.svg" alt="Crear">
        </a>
        <a href="pagina_usuario.php" title="Usuario">
            <img src="../assets/user.svg" alt="Usuario">
        </a>
        <a href="../backend/logout.php" title="Salir" onclick="return confirm('¿Seguro que quieres cerrar sesión?');">
            <img src="../assets/exit.svg" alt="Salir">
        </a>
    </nav>

</header>

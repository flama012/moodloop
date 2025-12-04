<!-- cabecera.php -->

<!-- Contenedor principal de la cabecera -->
<div style="display:flex; align-items:center; justify-content:space-between; padding:10px;">

    <!-- 1. LOGO -->
    <div>
        <a href="pagina_feed.php">
            <img src="logo_moodloop.png" alt="MoodLoop" style="height:50px;">
        </a>
    </div>

    <!-- 2. BUSCADOR DE USUARIOS -->
    <div>
        <form action="buscar_usuario.php" method="GET" style="display:flex; gap:5px;">
            <input type="text" name="q" placeholder="Buscar usuarios..." style="padding:5px;">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <!-- 3. MENÚ -->
    <nav style="display:flex; gap:15px;">
        <a href="pagina_feed.php">FEED</a>
        <a href="pagina_usuario.php">USUARIO</a>
        <a href="pagina_publicacion.php">CREAR PUBLICACIÓN</a>

        <!-- ✅ SALIR con confirmación -->
        <a href="../backend/logout.php"
           style="color:red;"
           onclick="return confirm('¿Seguro que quieres cerrar sesión?');">
            SALIR
        </a>
    </nav>

</div>

<hr>

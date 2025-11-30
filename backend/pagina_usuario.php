<?php
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
<h1>USUARIO</h1>
<h3>Menú:</h3>
<a href="pagina_feed.php">FEED</a>
<a href="pagina_usuario.php">USUARIO</a>
<a href="pagina_publicacion.php">CREAR PUBLICACION</a>

<form action="/publicar.php" method="post">
    <div class="field">
        <label for="estado_emocional">Estado emocional</label>
        <select id="estado_emocional" name="estado_emocional" required>
            <option value="" disabled selected>Selecciona tu estado…</option>
            <option value="Feliz">Feliz</option>
            <option value="Neutral">Neutral</option>
            <option value="Triste">Triste</option>
            <option value="Ansioso">Ansioso</option>
            <option value="Estresado">Estresado</option>
            <option value="Enfadado">Enfadado</option>
            <option value="Cansado">Cansado</option>
            <option value="Motivado">Motivado</option>
            <option value="Agradecido">Agradecido</option>
        </select>
    </div>

    <button type="submit">Publicar</button>
</form>
</body>
</html>

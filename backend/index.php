<?php
// index.php
// Este archivo es el punto de inicio del backend.
// Muestra un menú sencillo con enlaces a las funcionalidades básicas.

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "  <meta charset='UTF-8'>";
echo "  <title>Moodloop - Panel Backend</title>";
echo "  <style>
          body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
          h1 { color: #333; }
          a { display: block; margin: 10px 0; text-decoration: none; color: #007BFF; }
          a:hover { text-decoration: underline; }
          .menu { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        </style>";
echo "</head>";
echo "<body>";

echo "<div class='menu'>";
echo "  <h1>Moodloop - Panel Backend</h1>";
echo "  <p>Selecciona una opción:</p>";
echo "  <a href='usuarios.php'>Ver usuarios registrados</a>";
echo "  <a href='publicaciones.php'>Ver publicaciones recientes</a>";
echo "</div>";

echo "</body>";
echo "</html>";
?>

<?php
// Publicacion.php
// Clase para manejar publicaciones en la base de datos

require_once "db.php"; // Incluimos la conexión

class Publicacion {
    private $conn; // Guardará la conexión

    public function __construct() {
        $this->conn = conectar(); // Conectamos a la base de datos
    }

    // Método para obtener publicaciones (con límite opcional)
    public function obtenerPublicaciones($limite = 10) {
        // Consulta SQL: selecciona las publicaciones más recientes
        $sql = "SELECT mensaje, estado_emocional, fecha_hora 
            FROM Publicaciones 
            ORDER BY fecha_hora DESC 
            LIMIT " . intval($limite);

        $resultado = mysqli_query($this->conn, $sql);

        $publicaciones = []; // Array vacío

        // Guardamos cada fila en el array usando un bucle while
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $publicaciones[] = $fila;
        }

        return $publicaciones; // Devolvemos el array
    }

    // Método para mostrar publicaciones en HTML
    public function mostrarPublicacionesHTML($limite = 10) {
        $publicaciones = $this->obtenerPublicaciones($limite);

        if (count($publicaciones) > 0) {
            echo "<h2>Últimas publicaciones</h2>";
            foreach ($publicaciones as $p) {
                echo "<p><strong>" . $p["estado_emocional"] . "</strong>: "
                    . $p["mensaje"] . "<br><em>" . $p["fecha_hora"] . "</em></p>";
            }
        } else {
            echo "No hay publicaciones.";
        }
    }
}
?>

<?php
// Publicacion.php
// Clase para manejar publicaciones en la base de datos con PDO

require_once "ConexionDB.php"; // Incluimos la conexión (Singleton)

class Publicacion {
    private $conn; // Guardará la conexión PDO

    public function __construct() {
        // Obtenemos la conexión única desde el Singleton
        $this->conn = ConexionDB::getConexion("moodloop");
    }

    // Método para obtener publicaciones (con límite opcional)
    public function obtenerPublicaciones($limite = 10) {
        $publicaciones = []; // Array vacío
        try {
            // Consulta SQL con LIMIT parametrizado
            $sql = "SELECT mensaje, estado_emocional, fecha_hora 
                    FROM publicaciones 
                    ORDER BY fecha_hora DESC 
                    LIMIT :limite";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
            $consulta->execute();

            // Obtenemos todas las filas como array asociativo
            $publicaciones = $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener publicaciones: " . $e->getMessage();
        }

        return $publicaciones; // Devolvemos el array
    }

    // Método para mostrar publicaciones en HTML
    public function mostrarPublicacionesHTML($limite = 10) {
        $publicaciones = $this->obtenerPublicaciones($limite);

        if (count($publicaciones) > 0) {
            echo "<h2>Últimas publicaciones</h2>";
            foreach ($publicaciones as $p) {
                echo "<p><strong>" . htmlspecialchars($p["estado_emocional"]) . "</strong>: "
                    . htmlspecialchars($p["mensaje"]) . "<br><em>"
                    . htmlspecialchars($p["fecha_hora"]) . "</em></p>";
            }
        } else {
            echo "No hay publicaciones.";
        }
    }
}
?>

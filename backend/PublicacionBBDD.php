<?php

require_once "ConexionDB.php"; // Incluimos la conexión (Singleton)
require_once "Publicacion.php";
class PublicacionBBDD {
    private $conn;

    public function __construct() {
        // Obtenemos la conexión única desde el Singleton
        $this->conn = ConexionDB::getConexion("moodloop");
    }

    public function crearPublicacion($id_usuario, $mensaje, $estado_emocional) {
        try {
            $sql = "INSERT INTO publicaciones (id_usuario, mensaje, estado_emocional, fecha_hora)
                    VALUES (:id_usuario, :mensaje, :estado_emocional, NOW())";

            $consulta = $this->conn->prepare($sql);

            $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $consulta->bindParam(":mensaje", $mensaje, PDO::PARAM_STR);
            $consulta->bindParam(":estado_emocional", $estado_emocional, PDO::PARAM_STR);

            return $consulta->execute();
        } catch (PDOException $e) {
            echo "Error al crear publicación: " . $e->getMessage();
            return false;
        }
    }



    // Obtener todas las publicaciones
    public function obtenerPublicaciones($limite = 10) {
        try {
            $sql = "SELECT * FROM publicaciones ORDER BY fecha_hora DESC LIMIT :limite";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener publicaciones: " . $e->getMessage();
            return [];
        }
    }

    // Obtener una publicación por su ID
    public function obtenerPublicacionPorID($id_publicacion) {
        try {
            $sql = "SELECT * FROM publicaciones WHERE id_publicacion = :id";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener la publicación: " . $e->getMessage();
            return null;
        }
    }


    public function actualizarPublicacion($id_publicacion, $mensaje, $estado_emocional) {
        try {
            $sql = "UPDATE publicaciones 
                    SET mensaje = :mensaje, estado_emocional = :estado 
                    WHERE id_publicacion = :id";

            $consulta = $this->conn->prepare($sql);

            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->bindParam(":mensaje", $mensaje, PDO::PARAM_STR);
            $consulta->bindParam(":estado", $estado_emocional, PDO::PARAM_STR);

            return $consulta->execute();
        } catch (PDOException $e) {
            echo "Error al actualizar publicación: " . $e->getMessage();
            return false;
        }
    }


    public function eliminarPublicacion($id_publicacion) {
        try {
            $sql = "DELETE FROM publicaciones WHERE id_publicacion = :id";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);

            return $consulta->execute();
        } catch (PDOException $e) {
            echo "Error al eliminar publicación: " . $e->getMessage();
            return false;
        }
    }

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
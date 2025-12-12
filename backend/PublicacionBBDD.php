<?php
// Clase que gestiona las publicaciones en la base de datos

require_once "ConexionDB.php";
require_once "Publicacion.php";

class PublicacionBBDD {

    // Guardamos la conexión para usarla en todos los métodos
    private $conexion;

    public function __construct() {
        // Obtenemos la conexión desde el Singleton
        $this->conexion = ConexionDB::getConexion("moodloop");
    }

    // ============================================================
    // CREAR PUBLICACIÓN
    // ============================================================
    // Inserta una publicación y devuelve su ID
    public function crearPublicacion($id_usuario, $mensaje, $estado_emocional) {
        try {
            $sql = "INSERT INTO publicaciones (id_usuario, mensaje, estado_emocional, fecha_hora)
                    VALUES (:id_usuario, :mensaje, :estado_emocional, NOW())";

            $consulta = $this->conexion->prepare($sql);

            $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $consulta->bindParam(":mensaje", $mensaje, PDO::PARAM_STR);
            $consulta->bindParam(":estado_emocional", $estado_emocional, PDO::PARAM_STR);

            if ($consulta->execute()) {
                return $this->conexion->lastInsertId();
            }
            return false;

        } catch (PDOException $e) {
            echo "Error al crear publicación: " . $e->getMessage();
            return false;
        }
    }

    // ============================================================
    // AGREGAR ETIQUETAS
    // ============================================================
    // Crea etiquetas si no existen y las relaciona con la publicación
    public function agregarEtiquetasAPublicacion($id_publicacion, $etiquetas) {
        try {
            foreach ($etiquetas as $etiqueta) {

                $etiqueta = trim($etiqueta);
                if ($etiqueta === "") continue;

                // Buscar etiqueta
                $sqlBuscar = "SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = :nombre";
                $consultaBuscar = $this->conexion->prepare($sqlBuscar);
                $consultaBuscar->bindParam(":nombre", $etiqueta, PDO::PARAM_STR);
                $consultaBuscar->execute();
                $fila = $consultaBuscar->fetch(PDO::FETCH_ASSOC);

                // Crear si no existe
                if ($fila) {
                    $id_etiqueta = (int)$fila["id_etiqueta"];
                } else {
                    $sqlInsert = "INSERT INTO etiquetas (nombre_etiqueta) VALUES (:nombre)";
                    $consultaInsert = $this->conexion->prepare($sqlInsert);
                    $consultaInsert->bindParam(":nombre", $etiqueta, PDO::PARAM_STR);
                    $consultaInsert->execute();
                    $id_etiqueta = (int)$this->conexion->lastInsertId();
                }

                // Comprobar relación
                $sqlExisteRelacion = "SELECT 1 FROM publicacion_etiqueta 
                                      WHERE id_publicacion = :id_pub AND id_etiqueta = :id_et";
                $cExiste = $this->conexion->prepare($sqlExisteRelacion);
                $cExiste->bindParam(":id_pub", $id_publicacion, PDO::PARAM_INT);
                $cExiste->bindParam(":id_et", $id_etiqueta, PDO::PARAM_INT);
                $cExiste->execute();

                // Insertar relación si no existe
                if (!$cExiste->fetch()) {
                    $sqlRelacion = "INSERT INTO publicacion_etiqueta (id_publicacion, id_etiqueta)
                                    VALUES (:id_pub, :id_et)";
                    $consultaRelacion = $this->conexion->prepare($sqlRelacion);
                    $consultaRelacion->bindParam(":id_pub", $id_publicacion, PDO::PARAM_INT);
                    $consultaRelacion->bindParam(":id_et", $id_etiqueta, PDO::PARAM_INT);
                    $consultaRelacion->execute();
                }
            }
            return true;

        } catch (PDOException $e) {
            echo "Error al agregar etiquetas: " . $e->getMessage();
            return false;
        }
    }

    // ============================================================
    // OBTENER ETIQUETAS DE UNA PUBLICACIÓN
    // ============================================================
    public function obtenerEtiquetasPorPublicacion($id_publicacion) {
        try {
            $sql = "SELECT e.nombre_etiqueta
                    FROM publicacion_etiqueta pe
                    JOIN etiquetas e ON pe.id_etiqueta = e.id_etiqueta
                    WHERE pe.id_publicacion = :id";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_COLUMN);

        } catch (PDOException $e) {
            echo "Error al obtener etiquetas: " . $e->getMessage();
            return [];
        }
    }

    // ============================================================
    // OBTENER PUBLICACIONES (GENERALES)
    // ============================================================
    public function obtenerPublicaciones($limite = 10) {
        try {
            $sql = "SELECT p.*, u.nombre_usuario
                    FROM publicaciones p
                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                    ORDER BY p.fecha_hora DESC
                    LIMIT :limite";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error al obtener publicaciones: " . $e->getMessage();
            return [];
        }
    }

    // ============================================================
    // OBTENER PUBLICACIÓN POR ID
    // ============================================================
    public function obtenerPublicacionPorID($id_publicacion) {
        try {
            $sql = "SELECT p.*, u.nombre_usuario
                    FROM publicaciones p
                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                    WHERE p.id_publicacion = :id";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error al obtener la publicación: " . $e->getMessage();
            return null;
        }
    }

    // ============================================================
    // ACTUALIZAR PUBLICACIÓN
    // ============================================================
    public function actualizarPublicacion($id_publicacion, $mensaje, $estado_emocional) {
        try {
            $sql = "UPDATE publicaciones 
                    SET mensaje = :mensaje, estado_emocional = :estado 
                    WHERE id_publicacion = :id";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->bindParam(":mensaje", $mensaje, PDO::PARAM_STR);
            $consulta->bindParam(":estado", $estado_emocional, PDO::PARAM_STR);

            return $consulta->execute();

        } catch (PDOException $e) {
            echo "Error al actualizar publicación: " . $e->getMessage();
            return false;
        }
    }

    // ============================================================
    // ELIMINAR PUBLICACIÓN
    // ============================================================
    public function eliminarPublicacion($idPublicacion) {

        // Borrar comentarios
        $sql = "DELETE FROM comentarios WHERE id_publicacion = :p";
        $this->conexion->prepare($sql)->execute([":p" => $idPublicacion]);

        // Borrar likes
        $sql = "DELETE FROM megusta WHERE id_publicacion = :p";
        $this->conexion->prepare($sql)->execute([":p" => $idPublicacion]);

        // Borrar publicación
        $sql = "DELETE FROM publicaciones WHERE id_publicacion = :p";
        $this->conexion->prepare($sql)->execute([":p" => $idPublicacion]);
    }

    // ============================================================
    // MOSTRAR PUBLICACIONES (PRUEBAS)
    // ============================================================
    public function mostrarPublicacionesHTML($limite = 10) {
        $publicaciones = $this->obtenerPublicaciones($limite);

        if (count($publicaciones) > 0) {
            echo "<h2>Últimas publicaciones</h2>";

            foreach ($publicaciones as $p) {
                echo "<p><strong>" . $p["estado_emocional"] . "</strong>: "
                    . $p["mensaje"] . "<br><em>"
                    . $p["fecha_hora"] . "</em></p>";
            }

        } else {
            echo "No hay publicaciones.";
        }
    }

    // ============================================================
    // PUBLICACIONES DE UN USUARIO
    // ============================================================
    public function obtenerPublicacionesPorUsuario($id_usuario, $limite = 10) {
        try {
            $sql = "SELECT p.*, u.nombre_usuario
                    FROM publicaciones p
                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                    WHERE p.id_usuario = :id_usuario 
                    ORDER BY p.fecha_hora DESC 
                    LIMIT :limite";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $consulta->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error al obtener publicaciones del usuario: " . $e->getMessage();
            return [];
        }
    }

    // ============================================================
    // COMENTARIOS DE UNA PUBLICACIÓN
    // ============================================================
    public function obtenerComentariosPorPublicacion($id_publicacion) {
        try {
            $sql = "SELECT c.texto, c.fecha_hora, u.nombre_usuario 
                    FROM comentarios c
                    JOIN usuarios u ON c.id_usuario = u.id_usuario
                    WHERE c.id_publicacion = :id
                    ORDER BY c.fecha_hora ASC";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error al obtener comentarios: " . $e->getMessage();
            return [];
        }
    }

    // ============================================================
    // CONTAR ME GUSTA
    // ============================================================
    public function contarMeGustaPorPublicacion($id_publicacion) {
        try {
            $sql = "SELECT COUNT(*) as total FROM megusta WHERE id_publicacion = :id";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();

            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            return (int)$fila["total"];

        } catch (PDOException $e) {
            echo "Error al contar me gusta: " . $e->getMessage();
            return 0;
        }
    }

    // ============================================================
    // CONTAR PUBLICACIONES DE UN USUARIO
    // ============================================================
    public function contarPublicacionesPorUsuario($id_usuario) {
        try {
            $sql = "SELECT COUNT(*) as total FROM publicaciones WHERE id_usuario = :id";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(":id", $id_usuario, PDO::PARAM_INT);
            $consulta->execute();

            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            return (int)$fila["total"];

        } catch (PDOException $e) {
            echo "Error al contar publicaciones: " . $e->getMessage();
            return 0;
        }
    }

    // ============================================================
    // FEED: PUBLICACIONES DE SEGUIDOS
    // ============================================================
    public function obtenerPublicacionesSeguidos($id_usuario) {
        $sql = "SELECT p.*, u.nombre_usuario
                FROM publicaciones p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                JOIN seguidores s ON s.id_seguido = p.id_usuario
                WHERE s.id_seguidor = :id_usuario
                ORDER BY p.fecha_hora DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // FEED: POR EMOCIÓN
    // ============================================================
    public function obtenerPublicacionesPorEmocion($emocion) {
        $sql = "SELECT p.*, u.nombre_usuario
                FROM publicaciones p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.estado_emocional = :emocion
                ORDER BY p.fecha_hora DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":emocion", $emocion, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // FEED: POR ETIQUETAS
    // ============================================================
    public function obtenerPublicacionesPorEtiquetas($etiquetas) {

        $placeholders = implode(',', array_fill(0, count($etiquetas), '?'));

        $sql = "SELECT DISTINCT p.*, u.nombre_usuario
                FROM publicaciones p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                JOIN publicacion_etiqueta pe ON p.id_publicacion = pe.id_publicacion
                JOIN etiquetas e ON pe.id_etiqueta = e.id_etiqueta
                WHERE e.nombre_etiqueta IN ($placeholders)
                ORDER BY p.fecha_hora DESC";

        $consulta = $this->conexion->prepare($sql);

        foreach ($etiquetas as $i => $et) {
            $consulta->bindValue($i + 1, $et, PDO::PARAM_STR);
        }

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // TOP EMOCIONES
    // ============================================================
    public function obtenerTopEmociones() {
        $sql = "SELECT estado_emocional, COUNT(*) AS total
                FROM publicaciones
                GROUP BY estado_emocional
                ORDER BY total DESC
                LIMIT 5";

        $consulta = $this->conexion->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // TOP ETIQUETAS
    // ============================================================
    public function obtenerTopEtiquetas() {
        $sql = "SELECT e.nombre_etiqueta, COUNT(*) AS total
                FROM publicacion_etiqueta pe
                JOIN etiquetas e ON pe.id_etiqueta = e.id_etiqueta
                GROUP BY e.id_etiqueta
                ORDER BY total DESC
                LIMIT 5";

        $consulta = $this->conexion->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // COMPROBAR PROPIEDAD DE PUBLICACIÓN
    // ============================================================
    public function esPublicacionDeUsuario($idPublicacion, $idUsuario) {
        $sql = "SELECT 1 FROM publicaciones WHERE id_publicacion = :p AND id_usuario = :u";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([":p" => $idPublicacion, ":u" => $idUsuario]);

        return $consulta->fetch() ? true : false;
    }

    public function usuarioDioMG($idUsuario, $idPublicacion) {
        $sql = "SELECT COUNT(*) FROM megusta WHERE id_usuario = :u AND id_publicacion = :p";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([":u" => $idUsuario, ":p" => $idPublicacion]);
        return $stmt->fetchColumn() > 0;
    }

}
?>

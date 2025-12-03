<?php

require_once "ConexionDB.php"; // Incluimos la conexión (Singleton)
require_once "Publicacion.php";

class PublicacionBBDD {
    private $conn;

    public function __construct() {
        // Obtenemos la conexión única desde el Singleton
        $this->conn = ConexionDB::getConexion("moodloop");
    }

    // ============================
    // CREAR PUBLICACIÓN (tu versión original, devuelve boolean)
    // ============================
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

    // ============================
    // CREAR PUBLICACIÓN Y DEVOLVER ID (nueva, para etiquetas)
    // ============================
    public function crearPublicacionYDevolverId($id_usuario, $mensaje, $estado_emocional) {
        try {
            $sql = "INSERT INTO publicaciones (id_usuario, mensaje, estado_emocional, fecha_hora)
                    VALUES (:id_usuario, :mensaje, :estado_emocional, NOW())";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $consulta->bindParam(":mensaje", $mensaje, PDO::PARAM_STR);
            $consulta->bindParam(":estado_emocional", $estado_emocional, PDO::PARAM_STR);

            if ($consulta->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            echo "Error al crear publicación: " . $e->getMessage();
            return false;
        }
    }

    // ============================
    // OBTENER TODAS LAS PUBLICACIONES
    // ============================
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

    // ============================
    // OBTENER UNA PUBLICACIÓN POR SU ID
    // ============================
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

    // ============================
    // ACTUALIZAR PUBLICACIÓN
    // ============================
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

    // ============================
    // ELIMINAR PUBLICACIÓN
    // ============================
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

    // ============================
    // MOSTRAR PUBLICACIONES EN HTML (para pruebas)
    // ============================
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

    // ============================
    // OBTENER PUBLICACIONES POR USUARIO
    // ============================
    public function obtenerPublicacionesPorUsuario($id_usuario, $limite = 10) {
        try {
            $sql = "SELECT * FROM publicaciones 
                WHERE id_usuario = :id_usuario 
                ORDER BY fecha_hora DESC 
                LIMIT :limite";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $consulta->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener publicaciones del usuario: " . $e->getMessage();
            return [];
        }
    }

    // ============================
    // OBTENER COMENTARIOS DE UNA PUBLICACIÓN
    // ============================
    public function obtenerComentariosPorPublicacion($id_publicacion) {
        try {
            $sql = "SELECT c.texto, c.fecha_hora, u.nombre_usuario 
                FROM comentarios c
                JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.id_publicacion = :id
                ORDER BY c.fecha_hora ASC";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener comentarios: " . $e->getMessage();
            return [];
        }
    }

    // ============================
    // CONTAR ME GUSTA DE UNA PUBLICACIÓN
    // ============================
    public function contarMeGustaPorPublicacion($id_publicacion) {
        try {
            $sql = "SELECT COUNT(*) as total FROM megusta WHERE id_publicacion = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            return $fila["total"];
        } catch (PDOException $e) {
            echo "Error al contar me gusta: " . $e->getMessage();
            return 0;
        }
    }

    // ============================
    // CONTAR PUBLICACIONES DE UN USUARIO
    // ============================
    public function contarPublicacionesPorUsuario($id_usuario) {
        try {
            $sql = "SELECT COUNT(*) as total FROM publicaciones WHERE id_usuario = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_usuario, PDO::PARAM_INT);
            $consulta->execute();
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            return $fila["total"];
        } catch (PDOException $e) {
            echo "Error al contar publicaciones: " . $e->getMessage();
            return 0;
        }
    }

    // ============================
    // FUNCIONES DE FEED
    // ============================

    // Publicaciones de personas que el usuario sigue
    public function obtenerPublicacionesSeguidos($id_usuario) {
        $sql = "SELECT p.*, u.nombre_usuario
            FROM publicaciones p
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            JOIN seguidores s ON s.id_seguido = p.id_usuario
            WHERE s.id_seguidor = :id_usuario
            ORDER BY p.fecha_hora DESC";
        $consulta = $this->conn->prepare($sql);
        $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Publicaciones por emoción
    public function obtenerPublicacionesPorEmocion($emocion) {
        $sql = "SELECT p.*, u.nombre_usuario
            FROM publicaciones p
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            WHERE p.estado_emocional = :emocion
            ORDER BY p.fecha_hora DESC";
        $consulta = $this->conn->prepare($sql);
        $consulta->bindParam(":emocion", $emocion, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Publicaciones filtradas por etiquetas (hasta 5)
    public function obtenerPublicacionesPorEtiquetas($etiquetas) {
        $placeholders = implode(',', array_fill(0, count($etiquetas), '?'));
        $sql = "SELECT p.*, u.nombre_usuario, GROUP_CONCAT(e.nombre_etiqueta) AS etiquetas
            FROM publicaciones p
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            JOIN publicacion_etiqueta pe ON p.id_publicacion = pe.id_publicacion
            JOIN etiquetas e ON pe.id_etiqueta = e.id_etiqueta
            WHERE e.nombre_etiqueta IN ($placeholders)
            GROUP BY p.id_publicacion
            ORDER BY p.fecha_hora DESC";
        $consulta = $this->conn->prepare($sql);
        foreach ($etiquetas as $i => $et) {
            $consulta->bindValue($i+1, $et, PDO::PARAM_STR);
        }
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Top 5 emociones más usadas
    public function obtenerTopEmociones() {
        $sql = "SELECT estado_emocional, COUNT(*) AS total
            FROM publicaciones
            GROUP BY estado_emocional
            ORDER BY total DESC
            LIMIT 5";
        $consulta = $this->conn->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Top 5 etiquetas más usadas
    public function obtenerTopEtiquetas() {
        $sql = "SELECT e.nombre_etiqueta, COUNT(*) AS total
            FROM publicacion_etiqueta pe
            JOIN etiquetas e ON pe.id_etiqueta = e.id_etiqueta
            GROUP BY e.id_etiqueta
            ORDER BY total DESC
            LIMIT 5";
        $consulta = $this->conn->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================
    // UTILIDADES DE ETIQUETAS (nuevas)
    // ============================

    // Agregar etiquetas a una publicación (array de nombres)
    public function agregarEtiquetasAPublicacion($id_publicacion, $etiquetas) {
        try {
            foreach ($etiquetas as $etiqueta) {
                // Normalizar etiqueta: quitar espacios y vacíos
                $etiqueta = trim($etiqueta);
                if ($etiqueta === "") continue;

                // Buscar si la etiqueta ya existe
                $sql = "SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = :nombre";
                $consulta = $this->conn->prepare($sql);
                $consulta->bindParam(":nombre", $etiqueta, PDO::PARAM_STR);
                $consulta->execute();
                $fila = $consulta->fetch(PDO::FETCH_ASSOC);

                if ($fila) {
                    $id_etiqueta = (int)$fila["id_etiqueta"];
                } else {
                    // Insertar nueva etiqueta si no existe
                    $sqlInsert = "INSERT INTO etiquetas (nombre_etiqueta) VALUES (:nombre)";
                    $consultaInsert = $this->conn->prepare($sqlInsert);
                    $consultaInsert->bindParam(":nombre", $etiqueta, PDO::PARAM_STR);
                    $consultaInsert->execute();
                    $id_etiqueta = (int)$this->conn->lastInsertId();
                }

                // Relacionar etiqueta con la publicación (evitar duplicados)
                $sqlChk = "SELECT 1 FROM publicacion_etiqueta WHERE id_publicacion = :id_pub AND id_etiqueta = :id_et";
                $cChk = $this->conn->prepare($sqlChk);
                $cChk->bindParam(":id_pub", $id_publicacion, PDO::PARAM_INT);
                $cChk->bindParam(":id_et", $id_etiqueta, PDO::PARAM_INT);
                $cChk->execute();
                if (!$cChk->fetch()) {
                    $sqlRelacion = "INSERT INTO publicacion_etiqueta (id_publicacion, id_etiqueta)
                                    VALUES (:id_pub, :id_et)";
                    $consultaRelacion = $this->conn->prepare($sqlRelacion);
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

    // Obtener etiquetas de una publicación (para mostrar en feed/perfil)
    public function obtenerEtiquetasPorPublicacion($id_publicacion) {
        try {
            $sql = "SELECT e.nombre_etiqueta
                    FROM publicacion_etiqueta pe
                    JOIN etiquetas e ON pe.id_etiqueta = e.id_etiqueta
                    WHERE pe.id_publicacion = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_COLUMN); // devuelve array simple de nombres
        } catch (PDOException $e) {
            echo "Error al obtener etiquetas: " . $e->getMessage();
            return [];
        }
    }
}

?>

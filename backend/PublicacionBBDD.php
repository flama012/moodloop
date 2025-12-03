<?php
// PublicacionBBDD.php
// Clase que gestiona las publicaciones en la base de datos (crear, leer, actualizar, eliminar, y feed)
// Incluye soporte para etiquetas y métricas como comentarios y me gusta.

require_once "ConexionDB.php"; // Clase Singleton que da una conexión PDO única
require_once "Publicacion.php"; // (Opcional) Tu clase modelo de Publicación si la usas

class PublicacionBBDD {

    // Guardamos la conexión PDO para reutilizarla en todos los métodos
    private $conn;

    public function __construct() {
        // Obtenemos la conexión desde el Singleton (usa el nombre de tu BD)
        $this->conn = ConexionDB::getConexion("moodloop");
    }

    // ============================================================
    // CREAR PUBLICACIÓN (IMPORTANTE: devuelve el ID para asociar etiquetas)
    // ============================================================
    // Crea una publicación y devuelve el ID generado. Si falla, devuelve false.
    public function crearPublicacion($id_usuario, $mensaje, $estado_emocional) {
        try {
            // Preparamos el SQL para insertar una nueva fila en la tabla publicaciones
            $sql = "INSERT INTO publicaciones (id_usuario, mensaje, estado_emocional, fecha_hora)
                    VALUES (:id_usuario, :mensaje, :estado_emocional, NOW())";

            $consulta = $this->conn->prepare($sql);

            // Enlazamos los valores para evitar inyecciones SQL y errores
            $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $consulta->bindParam(":mensaje", $mensaje, PDO::PARAM_STR);
            $consulta->bindParam(":estado_emocional", $estado_emocional, PDO::PARAM_STR);

            // Ejecutamos la consulta. Si todo va bien, pedimos el ID insertado
            if ($consulta->execute()) {
                // Devolvemos el ID real de la publicación recién creada
                return $this->conn->lastInsertId();
            }
            // Si no se pudo insertar, devolvemos false
            return false;
        } catch (PDOException $e) {
            // Si hubo un error, lo mostramos (para depurar) y devolvemos false
            echo "Error al crear publicación: " . $e->getMessage();
            return false;
        }
    }

    // ============================================================
    // AGREGAR ETIQUETAS A UNA PUBLICACIÓN
    // ============================================================
    // Recibe el ID de la publicación y un array de nombres de etiquetas (ej: ["motivacion","felicidad"])
    // Este método crea la etiqueta si no existe y la relaciona con la publicación.
    public function agregarEtiquetasAPublicacion($id_publicacion, $etiquetas) {
        try {
            // Recorremos todas las etiquetas recibidas
            foreach ($etiquetas as $etiqueta) {
                // Quitamos espacios y saltamos vacíos
                $etiqueta = trim($etiqueta);
                if ($etiqueta === "") continue;

                // 1) Buscamos si la etiqueta ya existe en la tabla "etiquetas"
                $sqlBuscar = "SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = :nombre";
                $consultaBuscar = $this->conn->prepare($sqlBuscar);
                $consultaBuscar->bindParam(":nombre", $etiqueta, PDO::PARAM_STR);
                $consultaBuscar->execute();
                $fila = $consultaBuscar->fetch(PDO::FETCH_ASSOC);

                // Si existe, guardamos su ID; si no, la creamos y obtenemos su nuevo ID
                if ($fila) {
                    $id_etiqueta = (int)$fila["id_etiqueta"];
                } else {
                    $sqlInsert = "INSERT INTO etiquetas (nombre_etiqueta) VALUES (:nombre)";
                    $consultaInsert = $this->conn->prepare($sqlInsert);
                    $consultaInsert->bindParam(":nombre", $etiqueta, PDO::PARAM_STR);
                    $consultaInsert->execute();
                    $id_etiqueta = (int)$this->conn->lastInsertId();
                }

                // 2) Comprobamos si ya existe la relación para no duplicar filas
                $sqlExisteRelacion = "SELECT 1 FROM publicacion_etiqueta 
                                      WHERE id_publicacion = :id_pub AND id_etiqueta = :id_et";
                $cExiste = $this->conn->prepare($sqlExisteRelacion);
                $cExiste->bindParam(":id_pub", $id_publicacion, PDO::PARAM_INT);
                $cExiste->bindParam(":id_et", $id_etiqueta, PDO::PARAM_INT);
                $cExiste->execute();

                // Si no existe la relación, la insertamos
                if (!$cExiste->fetch()) {
                    $sqlRelacion = "INSERT INTO publicacion_etiqueta (id_publicacion, id_etiqueta)
                                    VALUES (:id_pub, :id_et)";
                    $consultaRelacion = $this->conn->prepare($sqlRelacion);
                    $consultaRelacion->bindParam(":id_pub", $id_publicacion, PDO::PARAM_INT);
                    $consultaRelacion->bindParam(":id_et", $id_etiqueta, PDO::PARAM_INT);
                    $consultaRelacion->execute();
                }
            }
            // Si todo fue bien, devolvemos true
            return true;
        } catch (PDOException $e) {
            echo "Error al agregar etiquetas: " . $e->getMessage();
            return false;
        }
    }

    // ============================================================
    // OBTENER LISTA DE ETIQUETAS DE UNA PUBLICACIÓN (para mostrar en el feed)
    // ============================================================
    // Devuelve un array simple con los nombres de las etiquetas de una publicación
    public function obtenerEtiquetasPorPublicacion($id_publicacion) {
        try {
            $sql = "SELECT e.nombre_etiqueta
                    FROM publicacion_etiqueta pe
                    JOIN etiquetas e ON pe.id_etiqueta = e.id_etiqueta
                    WHERE pe.id_publicacion = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();
            // FETCH_COLUMN devuelve un array con una sola columna (los nombres)
            return $consulta->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            echo "Error al obtener etiquetas: " . $e->getMessage();
            return [];
        }
    }

    // ============================================================
    // OBTENER TODAS LAS PUBLICACIONES (con nombre del autor)
    // ============================================================
    // Devuelve las publicaciones más recientes y el nombre del usuario que las creó
    public function obtenerPublicaciones($limite = 10) {
        try {
            $sql = "SELECT p.*, u.nombre_usuario
                    FROM publicaciones p
                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                    ORDER BY p.fecha_hora DESC
                    LIMIT :limite";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener publicaciones: " . $e->getMessage();
            return [];
        }
    }

    // ============================================================
    // OBTENER UNA PUBLICACIÓN POR ID (con nombre del autor)
    // ============================================================
    public function obtenerPublicacionPorID($id_publicacion) {
        try {
            $sql = "SELECT p.*, u.nombre_usuario
                    FROM publicaciones p
                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                    WHERE p.id_publicacion = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_publicacion, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener la publicación: " . $e->getMessage();
            return null;
        }
    }

    // ============================================================
    // ACTUALIZAR PUBLICACIÓN (mensaje y emoción)
    // ============================================================
    // Cambia el mensaje y el estado emocional de una publicación concreta
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

    // ============================================================
    // ELIMINAR PUBLICACIÓN
    // ============================================================
    // Borra una publicación por su ID
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

    // ============================================================
    // MOSTRAR PUBLICACIONES EN HTML (para pruebas rápidas)
    // ============================================================
    // Este método imprime publicaciones directamente. Útil para probar.
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
    // OBTENER PUBLICACIONES DE UN USUARIO (con nombre del autor)
    // ============================================================
    // Devuelve publicaciones pertenecientes a un usuario en concreto
    public function obtenerPublicacionesPorUsuario($id_usuario, $limite = 10) {
        try {
            $sql = "SELECT p.*, u.nombre_usuario
                    FROM publicaciones p
                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                    WHERE p.id_usuario = :id_usuario 
                    ORDER BY p.fecha_hora DESC 
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

    // ============================================================
    // OBTENER COMENTARIOS DE UNA PUBLICACIÓN (con nombre del autor del comentario)
    // ============================================================
    // Devuelve los comentarios de una publicación y el nombre del usuario que comenta
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

    // ============================================================
    // CONTAR ME GUSTA DE UNA PUBLICACIÓN
    // ============================================================
    // Devuelve un número con los "me gusta" de una publicación
    public function contarMeGustaPorPublicacion($id_publicacion) {
        try {
            $sql = "SELECT COUNT(*) as total FROM megusta WHERE id_publicacion = :id";
            $consulta = $this->conn->prepare($sql);
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
    // Devuelve cuántas publicaciones tiene un usuario concreto
    public function contarPublicacionesPorUsuario($id_usuario) {
        try {
            $sql = "SELECT COUNT(*) as total FROM publicaciones WHERE id_usuario = :id";
            $consulta = $this->conn->prepare($sql);
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
    // FEED: PUBLICACIONES DE PERSONAS QUE SIGO
    // ============================================================
    // Devuelve publicaciones de los usuarios a los que sigo, con su nombre
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

    // ============================================================
    // FEED: PUBLICACIONES POR EMOCIÓN
    // ============================================================
    // Devuelve publicaciones filtradas por una emoción concreta (ej: "Feliz")
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

    // ============================================================
    // FEED: PUBLICACIONES POR ETIQUETAS (CORREGIDO)
    // ============================================================
    // Devuelve las publicaciones que tengan al menos una de las etiquetas buscadas.
    // IMPORTANTE: usamos DISTINCT y no agrupamos etiquetas aquí.
    // Para mostrar las etiquetas de cada publicación, usa obtenerEtiquetasPorPublicacion(id).
    public function obtenerPublicacionesPorEtiquetas($etiquetas) {
        // Creamos tantos "?" como etiquetas haya para la cláusula IN
        $placeholders = implode(',', array_fill(0, count($etiquetas), '?'));

        $sql = "SELECT DISTINCT p.*, u.nombre_usuario
                FROM publicaciones p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                JOIN publicacion_etiqueta pe ON p.id_publicacion = pe.id_publicacion
                JOIN etiquetas e ON pe.id_etiqueta = e.id_etiqueta
                WHERE e.nombre_etiqueta IN ($placeholders)
                ORDER BY p.fecha_hora DESC";

        $consulta = $this->conn->prepare($sql);

        // Enlazamos cada etiqueta al placeholder correspondiente
        foreach ($etiquetas as $i => $et) {
            $consulta->bindValue($i + 1, $et, PDO::PARAM_STR);
        }

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // TOP EMOCIONES (las 5 más usadas)
    // ============================================================
    public function obtenerTopEmociones() {
        $sql = "SELECT estado_emocional, COUNT(*) AS total
                FROM publicaciones
                GROUP BY estado_emocional
                ORDER BY total DESC
                LIMIT 5";
        $consulta = $this->conn->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // TOP ETIQUETAS (las 5 más usadas)
    // ============================================================
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
}
?>

<?php
// Clase para manejar operaciones relacionadas con usuarios en la base de datos

require_once "Usuario.php";     // Clase Usuario (objeto)
require_once "ConexionDB.php";  // Conexión PDO mediante Singleton

class UsuarioBBDD {

    // Guardamos la conexión para usarla en todos los métodos
    private $conn;

    public function __construct() {
        // Obtenemos la conexión a la base de datos
        $this->conn = ConexionDB::getConexion("moodloop");
    }

    // ============================================================
    // REGISTRO, EXISTENCIA Y CONFIRMACIÓN
    // ============================================================

    // Comprobar si un correo ya existe
    public function existeEmail($correo) {
        $resultado = false;
        try {
            $sql = "SELECT correo FROM usuarios WHERE correo = :correo";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":correo", $correo, PDO::PARAM_STR);
            $consulta->execute();

            // Si hay 1 resultado, el correo existe
            if ($consulta->rowCount() === 1) {
                $resultado = true;
            }
        } catch (PDOException $e) {}
        return $resultado;
    }

    // Insertar un usuario nuevo
    public function insertarUsuario(
        $id_usuario, $nombre, $correo, $password, $biografia,
        $estado_emocional, $id_rol, $confirmado, $baneado, $fecha_registro, $token
    ) {
        try {
            $sql = "INSERT INTO usuarios 
                (id_usuario, nombre_usuario, correo, password, biografia, estado_emocional, id_rol, confirmado, baneado, fecha_registro, token)
                VALUES (:id_usuario, :nombre_usuario, :correo, :password, :biografia, :estado_emocional, :id_rol, :confirmado, :baneado, :fecha_registro, :token)";

            $consulta = $this->conn->prepare($sql);

            // Asignamos cada parámetro
            $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $consulta->bindParam(":nombre_usuario", $nombre, PDO::PARAM_STR);
            $consulta->bindParam(":correo", $correo, PDO::PARAM_STR);
            $consulta->bindParam(":password", $password, PDO::PARAM_STR);
            $consulta->bindParam(":biografia", $biografia, PDO::PARAM_STR);
            $consulta->bindParam(":estado_emocional", $estado_emocional, PDO::PARAM_STR);
            $consulta->bindParam(":id_rol", $id_rol, PDO::PARAM_INT);
            $consulta->bindParam(":confirmado", $confirmado, PDO::PARAM_INT);
            $consulta->bindParam(":baneado", $baneado, PDO::PARAM_INT);
            $consulta->bindParam(":fecha_registro", $fecha_registro, PDO::PARAM_STR);
            $consulta->bindParam(":token", $token, PDO::PARAM_STR);

            return $consulta->execute();

        } catch (PDOException $e) {

            // Error 1062 = duplicado
            if ($e->errorInfo[1] == 1062) {
                return "duplicado_usuario";
            }
            return false;
        }
    }

    // Obtener token por correo
    public function obtenerTokern($correo) {
        $resultado = null;
        try {
            $sql = "SELECT token FROM usuarios WHERE correo = :correo";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":correo", $correo, PDO::PARAM_STR);
            $consulta->execute();

            if ($consulta->rowCount() === 1) {
                $fila = $consulta->fetch(PDO::FETCH_ASSOC);
                $resultado = $fila["token"] ?? null;
            }
        } catch (PDOException $e) {}
        return $resultado;
    }

    // Confirmar un usuario
    public function actualizaConfirmacion($usuario) {
        $resultado = false;
        try {
            $sql = "UPDATE usuarios SET confirmado = 1 WHERE id_usuario = :id";
            $consulta = $this->conn->prepare($sql);

            // Obtenemos el id del objeto Usuario
            $id = $usuario->__get('id_usuario');

            $consulta->bindParam(":id", $id, PDO::PARAM_INT);
            $consulta->execute();

            if ($consulta->rowCount() === 1) {
                $resultado = true;
            }
        } catch (PDOException $e) {}
        return $resultado;
    }

    // ============================================================
    // OBTENCIÓN DE USUARIOS
    // ============================================================

    // Obtener un usuario por correo (devuelve objeto Usuario)
    public function obtenerUsuario($correo) {
        $usuario = false;
        try {
            $sql = "SELECT * FROM usuarios WHERE correo = :correo";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":correo", $correo, PDO::PARAM_STR);
            $consulta->execute();

            if ($consulta->rowCount() === 1) {

                $fila = $consulta->fetch(PDO::FETCH_ASSOC);

                // Creamos el objeto Usuario
                $usuario = new Usuario($fila["nombre_usuario"], $fila["correo"], $fila["password"]);

                // Asignamos el resto de propiedades
                $usuario->__set('id_usuario', $fila["id_usuario"]);
                $usuario->__set('biografia', $fila["biografia"]);
                $usuario->__set('estado_emocional', $fila["estado_emocional"]);
                $usuario->__set('id_rol', $fila["id_rol"]);
                $usuario->__set('confirmado', $fila["confirmado"]);
                $usuario->__set('baneado', $fila["baneado"]);
                $usuario->__set('fechaRegistro', $fila["fecha_registro"]);
                $usuario->__set('token', $fila["token"]);
            }
        } catch (PDOException $e) {}
        return $usuario;
    }

    // Listar usuarios (array)
    public function listarUsuarios() {
        $usuarios = [];
        try {
            $sql = "SELECT id_usuario, nombre_usuario, correo FROM usuarios";
            $consulta = $this->conn->prepare($sql);
            $consulta->execute();
            $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al listar usuarios: " . $e->getMessage();
        }
        return $usuarios;
    }

    // Mostrar usuarios en HTML (solo para pruebas)
    public function mostrarUsuariosHTML() {
        $usuarios = $this->listarUsuarios();

        if (count($usuarios) > 0) {
            echo "<h2>Usuarios registrados</h2><ul>";
            foreach ($usuarios as $u) {
                echo "<li>" . htmlspecialchars($u["nombre_usuario"]) .
                    " (" . htmlspecialchars($u["correo"]) . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "No hay usuarios.";
        }
    }

    // ============================================================
    // ACTUALIZACIONES DE PERFIL
    // ============================================================

    // Actualizar estado emocional
    public function actualizarEstadoEmocional($id_usuario, $estado_emocional) {
        $resultado = false;
        try {
            $sql = "UPDATE usuarios SET estado_emocional = :estado_emocional WHERE id_usuario = :id_usuario";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":estado_emocional", $estado_emocional, PDO::PARAM_STR);
            $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $resultado = $consulta->execute();
        } catch (PDOException $e) {
            echo "Error al actualizar el estado emocional: " . $e->getMessage();
        }
        return $resultado;
    }

    // Actualizar biografía
    public function actualizarBiografia($id_usuario, $biografia) {
        $resultado = false;
        try {
            $sql = "UPDATE usuarios SET biografia = :biografia WHERE id_usuario = :id_usuario";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":biografia", $biografia, PDO::PARAM_STR);
            $consulta->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $resultado = $consulta->execute();
        } catch (PDOException $e) {
            echo "Error al actualizar la biografía: " . $e->getMessage();
        }
        return $resultado;
    }

    // ============================================================
    // RELACIONES DE SEGUIMIENTO
    // ============================================================

    // Contar seguidores
    public function contarSeguidores($id_usuario) {
        try {
            $sql = "SELECT COUNT(*) as total FROM seguidores WHERE id_seguido = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_usuario, PDO::PARAM_INT);
            $consulta->execute();
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            return (int)$fila["total"];
        } catch (PDOException $e) {
            echo "Error al contar seguidores: " . $e->getMessage();
            return 0;
        }
    }

    // Contar seguidos
    public function contarSeguidos($id_usuario) {
        try {
            $sql = "SELECT COUNT(*) as total FROM seguidores WHERE id_seguidor = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_usuario, PDO::PARAM_INT);
            $consulta->execute();
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            return (int)$fila["total"];
        } catch (PDOException $e) {
            echo "Error al contar seguidos: " . $e->getMessage();
            return 0;
        }
    }

    // Obtener seguidores
    public function obtenerSeguidores($id_usuario) {
        try {
            $sql = "SELECT u.id_usuario, u.nombre_usuario
                    FROM usuarios u
                    JOIN seguidores s ON u.id_usuario = s.id_seguidor
                    WHERE s.id_seguido = :id
                    ORDER BY u.nombre_usuario ASC";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_usuario, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }

    // Obtener seguidos
    public function obtenerSeguidos($id_usuario) {
        try {
            $sql = "SELECT u.id_usuario, u.nombre_usuario
                    FROM usuarios u
                    JOIN seguidores s ON u.id_usuario = s.id_seguido
                    WHERE s.id_seguidor = :id
                    ORDER BY u.nombre_usuario ASC";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_usuario, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }

    // ============================================================
    // MÉTODOS OPCIONALES
    // ============================================================

    // Obtener usuario por ID (array)
    public function obtenerUsuarioPorId($id_usuario) {
        try {
            $sql = "SELECT * FROM usuarios WHERE id_usuario = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_usuario, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Obtener usuario por ID como objeto Usuario
    public function obtenerUsuarioObjetoPorId($id_usuario) {
        try {
            $sql = "SELECT * FROM usuarios WHERE id_usuario = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_usuario, PDO::PARAM_INT);
            $consulta->execute();

            if ($consulta->rowCount() === 1) {

                $fila = $consulta->fetch(PDO::FETCH_ASSOC);

                $usuario = new Usuario($fila["nombre_usuario"], $fila["correo"], $fila["password"]);

                $usuario->__set('id_usuario', $fila["id_usuario"]);
                $usuario->__set('biografia', $fila["biografia"]);
                $usuario->__set('estado_emocional', $fila["estado_emocional"]);
                $usuario->__set('id_rol', $fila["id_rol"]);
                $usuario->__set('confirmado', $fila["confirmado"]);
                $usuario->__set('baneado', $fila["baneado"]);
                $usuario->__set('fechaRegistro', $fila["fecha_registro"]);
                $usuario->__set('token', $fila["token"]);

                return $usuario;
            }

        } catch (PDOException $e) {}

        return null;
    }

    // Comprobar si ya sigo a un usuario
    public function existeRelacionSeguimiento($id_seguidor, $id_seguido) {
        try {
            $sql = "SELECT 1 FROM seguidores WHERE id_seguidor = :seguidor AND id_seguido = :seguido";
            $c = $this->conn->prepare($sql);
            $c->bindParam(":seguidor", $id_seguidor, PDO::PARAM_INT);
            $c->bindParam(":seguido", $id_seguido, PDO::PARAM_INT);
            $c->execute();
            return (bool)$c->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Seguir a un usuario
    public function seguirUsuario($id_seguidor, $id_seguido) {
        try {
            // No puedes seguirte a ti mismo
            if ($id_seguidor === $id_seguido) return false;

            // Si ya lo sigues, no hacemos nada
            if ($this->existeRelacionSeguimiento($id_seguidor, $id_seguido)) return true;

            $sql = "INSERT INTO seguidores (id_seguidor, id_seguido) VALUES (:seguidor, :seguido)";
            $c = $this->conn->prepare($sql);
            $c->bindParam(":seguidor", $id_seguidor, PDO::PARAM_INT);
            $c->bindParam(":seguido", $id_seguido, PDO::PARAM_INT);
            return $c->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // Dejar de seguir a un usuario
    public function dejarDeSeguirUsuario($id_seguidor, $id_seguido) {
        try {
            $sql = "DELETE FROM seguidores WHERE id_seguidor = :seguidor AND id_seguido = :seguido";
            $c = $this->conn->prepare($sql);
            $c->bindParam(":seguidor", $id_seguidor, PDO::PARAM_INT);
            $c->bindParam(":seguido", $id_seguido, PDO::PARAM_INT);
            return $c->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Buscar usuarios por nombre
    public function buscarUsuariosPorNombre($textoBuscado) {
        $resultados = [];

        try {
            // Preparamos el texto para el LIKE
            $texto = "%" . $textoBuscado . "%";

            $sql = "SELECT id_usuario, nombre_usuario
                FROM usuarios
                WHERE nombre_usuario LIKE :texto
                ORDER BY nombre_usuario ASC";

            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":texto", $texto, PDO::PARAM_STR);
            $consulta->execute();

            $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error al buscar usuarios: " . $e->getMessage();
        }

        return $resultados;
    }

    // Obtener usuario por ID (objeto simple)
    public function getUsuarioPorId($id) {

        $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

}
?>

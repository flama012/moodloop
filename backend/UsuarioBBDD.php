<?php
// UsuarioBBDD.php
// Clase para manejar operaciones de usuarios en la base de datos

require_once "Usuario.php";     // Clase de entidad Usuario (getters/setters)
require_once "ConexionDB.php";  // Singleton de conexión PDO

class UsuarioBBDD {

    // Conexión PDO reutilizable en toda la clase
    private $conn;

    public function __construct() {
        // Obtenemos la conexión única desde el Singleton
        $this->conn = ConexionDB::getConexion("moodloop");
    }

    // ============================================================
    // REGISTRO, EXISTENCIA Y CONFIRMACIÓN
    // ============================================================

    // Comprobar si existe un email (true/false)
    public function existeEmail($correo) {
        $resultado = false;
        try {
            $sql = "SELECT correo FROM usuarios WHERE correo = :correo";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":correo", $correo, PDO::PARAM_STR);
            $consulta->execute();
            if ($consulta->rowCount() === 1) {
                $resultado = true;
            }
        } catch (PDOException $e) {
            // Manejo silencioso opcional
        }
        return $resultado;
    }

    // Insertar un usuario (devuelve true/false)
    public function insertarUsuario(
        $id_usuario, $nombre, $correo, $password, $biografia,
        $estado_emocional, $id_rol, $confirmado, $baneado, $fecha_registro, $token
    ) {
        $resultado = false;
        try {
            $sql = "INSERT INTO usuarios 
                    (id_usuario, nombre_usuario, correo, password, biografia, estado_emocional, id_rol, confirmado, baneado, fecha_registro, token)
                    VALUES (:id_usuario, :nombre_usuario, :correo, :password, :biografia, :estado_emocional, :id_rol, :confirmado, :baneado, :fecha_registro, :token)";
            $consulta = $this->conn->prepare($sql);
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

            $resultado = $consulta->execute();
        } catch (PDOException $e) {
            echo "Error al insertar usuario: " . $e->getMessage();
        }
        return $resultado;
    }

    // Obtener token del usuario por correo
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
        } catch (PDOException $e) {
            // Manejo silencioso opcional
        }
        return $resultado;
    }

    // Marcar como confirmado (devuelve true/false)
    public function actualizaConfirmacion($usuario) {
        $resultado = false;
        try {
            $sql = "UPDATE usuarios SET confirmado = 1 WHERE id_usuario = :id";
            $consulta = $this->conn->prepare($sql);
            $id = $usuario->__get('id_usuario');
            $consulta->bindParam(":id", $id, PDO::PARAM_INT);
            $consulta->execute();
            if ($consulta->rowCount() === 1) {
                $resultado = true;
            }
        } catch (PDOException $e) {
            // Manejo silencioso opcional
        }
        return $resultado;
    }

    // ============================================================
    // OBTENCIÓN DE USUARIOS
    // ============================================================

    // Obtener un Usuario (objeto) por correo
    public function obtenerUsuario($correo) {
        $usuario = false;
        try {
            $sql = "SELECT * FROM usuarios WHERE correo = :correo";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":correo", $correo, PDO::PARAM_STR);
            $consulta->execute();

            if ($consulta->rowCount() === 1) {
                $fila = $consulta->fetch(PDO::FETCH_ASSOC);

                // Construimos el objeto Usuario con los campos principales
                $usuario = new Usuario($fila["nombre_usuario"], $fila["correo"], $fila["password"]);

                // Asignamos propiedades adicionales usando tus setters mágicos
                $usuario->__set('id_usuario', $fila["id_usuario"]);
                $usuario->__set('biografia', $fila["biografia"]);
                $usuario->__set('estado_emocional', $fila["estado_emocional"]);
                $usuario->__set('id_rol', $fila["id_rol"]);
                $usuario->__set('confirmado', $fila["confirmado"]);
                $usuario->__set('baneado', $fila["baneado"]);
                $usuario->__set('fechaRegistro', $fila["fecha_registro"]);
                $usuario->__set('token', $fila["token"]);
            }
        } catch (PDOException $e) {
            // Manejo silencioso opcional
        }
        return $usuario;
    }

    // Listar usuarios (array asociativo)
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

    // Mostrar usuarios en HTML (para pruebas rápidas)
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

    // Actualizar estado emocional del usuario
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

    // Actualizar biografía del usuario
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
    // RELACIONES DE SEGUIMIENTO (followers/following)
    // ============================================================

    // Contar seguidores (quiénes me siguen)
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

    // Contar seguidos (a quiénes sigo yo)
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

    // ============================================================
    // MÉTODOS OPCIONALES ÚTILES (no rompen nada si no los usas)
    // ============================================================

    // Obtener usuario por ID (útil para sesiones)
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

    // Comprobar si ya sigo a alguien (para evitar duplicados)
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

    // Seguir a un usuario (inserta relación)
    public function seguirUsuario($id_seguidor, $id_seguido) {
        try {
            if ($id_seguidor === $id_seguido) return false; // Evitar seguirse a uno mismo
            if ($this->existeRelacionSeguimiento($id_seguidor, $id_seguido)) return true; // Ya existe

            $sql = "INSERT INTO seguidores (id_seguidor, id_seguido) VALUES (:seguidor, :seguido)";
            $c = $this->conn->prepare($sql);
            $c->bindParam(":seguidor", $id_seguidor, PDO::PARAM_INT);
            $c->bindParam(":seguido", $id_seguido, PDO::PARAM_INT);
            return $c->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Dejar de seguir (eliminar relación)
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

    // ============================================================
    // BUSCAR USUARIOS POR NOMBRE (COINCIDENCIAS PARCIALES)
    // ============================================================
    // Devuelve un array con usuarios cuyo nombre contenga el texto buscado.
    // Ejemplo: buscar "er" encuentra "Erik", "Sergio", "Héctor", etc.
    public function buscarUsuariosPorNombre($textoBuscado) {
        $resultados = [];

        try {
            // Añadimos % para buscar coincidencias parciales
            $texto = "%" . $textoBuscado . "%";

            // Consulta: buscamos solo por nombre_usuario
            $sql = "SELECT id_usuario, nombre_usuario
                FROM usuarios
                WHERE nombre_usuario LIKE :texto
                ORDER BY nombre_usuario ASC";

            // Preparamos la consulta
            $consulta = $this->conn->prepare($sql);

            // Enlazamos el parámetro
            $consulta->bindParam(":texto", $texto, PDO::PARAM_STR);

            // Ejecutamos
            $consulta->execute();

            // Obtenemos todos los resultados como array asociativo
            $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error al buscar usuarios: " . $e->getMessage();
        }

        return $resultados;
    }
    // -------------------------------------------------------------
    // Obtener un usuario por su ID
    // -------------------------------------------------------------
    public function getUsuarioPorId($id) {

        // Consulta SQL para buscar un usuario por su ID
        $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";

        // Preparamos la consulta usando la conexión correcta ($this->conn)
        $stmt = $this->conn->prepare($sql);

        // Ejecutamos la consulta enviando el ID
        $stmt->execute([$id]);

        // Devolvemos el usuario como un objeto
        return $stmt->fetch(PDO::FETCH_OBJ);
    }


}
?>

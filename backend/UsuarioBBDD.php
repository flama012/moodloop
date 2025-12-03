<?php
// Usuario.php
// Clase para manejar usuarios en la base de datos
require_once "Usuario.php";//incluimos la clase Usuario
require_once "ConexionDB.php"; // Incluimos la conexión

class UsuarioBBDD{

    private $conn;

    public function __construct() {
        $this->conn = ConexionDB::getConexion("moodloop");
    }

    //Registro y enviar email
    public function existeEmail($correo){
        $resultado = false;
        try {
            $conexion = ConexionDB::getConexion("moodloop");
            $consulta = $conexion->prepare("SELECT correo FROM usuarios WHERE correo = :correo");
            $consulta->bindParam(":correo", $correo);
            $consulta->execute();
            if ($consulta->rowCount() == 1) {
                $resultado = true;
            }
        }
        catch (PDOException $e) {
        }
        return $resultado;
    }

    public function insertarUsuario($id_usuario, $nombre, $correo, $password, $biografia, $estado_emocional, $id_rol, $confirmado, $baneado, $fecha_registro, $token){
        $resultado = false;
        try {
            $conexion = ConexionDB::getConexion("moodloop");
            $consulta = $conexion->prepare("
            INSERT INTO usuarios 
            (id_usuario, nombre_usuario, correo, password, biografia, estado_emocional, id_rol, confirmado, baneado, fecha_registro, token)
            VALUES (:id_usuario, :nombre_usuario, :correo, :password, :biografia, :estado_emocional, :id_rol, :confirmado, :baneado, :fecha_registro, :token)");
            $consulta->bindParam(":id_usuario", $id_usuario);
            $consulta->bindParam(":nombre_usuario", $nombre);
            $consulta->bindParam(":correo", $correo);
            $consulta->bindParam(":password", $password);
            $consulta->bindParam(":biografia", $biografia);
            $consulta->bindParam(":estado_emocional", $estado_emocional);
            $consulta->bindParam(":id_rol", $id_rol);
            $consulta->bindParam(":confirmado", $confirmado);
            $consulta->bindParam(":baneado", $baneado);
            $consulta->bindParam(":fecha_registro", $fecha_registro);
            $consulta->bindParam(":token", $token);

            $resultado = $consulta->execute();
        }
       catch (PDOException $e) {
            echo "Error al insertar usuario: ".$e->getMessage();

        }
        return $resultado;
    }
    public function obtenerTokern($correo){
        $resultado = null;
        try {
            $conexion = ConexionDB::getConexion("moodloop");
            $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE correo = :correo");
            $consulta->bindParam(":correo", $correo);
            $consulta->execute();
            if ($consulta->rowCount() == 1) {
                $fila = $consulta->fetch(PDO::FETCH_ASSOC);
                $token = $fila["token"];
                $resultado = $token;
            }
        }
        catch (PDOException $e) {
        }
        return $resultado;
    }
    public function obtenerUsuario($correo){
        $usuario = false;
        try {
            $conexion = ConexionDB::getConexion("moodloop");
            $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE correo = :correo");
            $consulta->bindParam(":correo", $correo);
            $consulta->execute();

            if ($consulta->rowCount() == 1) {
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
            }
        } catch (PDOException $e) {
            // Manejo opcional del error
        }

        return $usuario;
    }

    public function actualizaConfirmacion($usuario){
        $resultado = false;
        try{
            $conexion = ConexionDB::getConexion("moodloop");
            $consulta = $conexion->prepare("UPDATE usuarios SET confirmado = 1 WHERE id_usuario = :id");
            $id = $usuario->__get('id_usuario');
            $consulta->bindParam(":id", $id);
            $consulta->execute();
            if ($consulta->rowCount() === 1) {
                $resultado = true;
            }
        }
        catch (PDOException $e) {
            // Manejo silencioso
        }
        return $resultado;
    }



    // Método para obtener todos los usuarios con PDO
    public function listarUsuarios() {
        $usuarios = []; // Array vacío para guardar resultados
        try {
            $conexion = ConexionDB::getConexion("moodloop");
            $sql = "SELECT id_usuario, nombre_usuario, correo FROM usuarios";
            $consulta = $conexion->prepare($sql);
            $consulta->execute();

            // Obtenemos todas las filas como array asociativo
            $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejo opcional del error
            echo "Error al listar usuarios: " . $e->getMessage();
        }

        return $usuarios; // Devolvemos el array de usuarios
    }

    // Método para mostrar usuarios en HTML con PDO
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

    //escoger el estado emocional
    public function actualizarEstadoEmocional($id_usuario, $estado_emocional){
        $resultado = false;
        try {
            $conexion = ConexionDB::getConexion("moodloop");
            $consulta = $conexion->prepare("UPDATE usuarios SET estado_emocional = :estado_emocional WHERE id_usuario = :id_usuario");
            $consulta->bindParam(":estado_emocional", $estado_emocional);
            $consulta->bindParam(":id_usuario", $id_usuario);
            $resultado = $consulta->execute();
        } catch (PDOException $e) {
            echo "Error al actualizar el estado emocional: " . $e->getMessage();
        }
        return $resultado;
    }

    // Actualizar la biografía del usuario
    public function actualizarBiografia($id_usuario, $biografia){
        $resultado = false;
        try {
            $conexion = ConexionDB::getConexion("moodloop");
            $consulta = $conexion->prepare("UPDATE usuarios SET biografia = :biografia WHERE id_usuario = :id_usuario");
            $consulta->bindParam(":biografia", $biografia);
            $consulta->bindParam(":id_usuario", $id_usuario);
            $resultado = $consulta->execute();
        } catch (PDOException $e) {
            echo "Error al actualizar la biografía: " . $e->getMessage();
        }
        return $resultado;
    }


    // Contar seguidores (quiénes me siguen)
    public function contarSeguidores($id_usuario) {
        try {
            $sql = "SELECT COUNT(*) as total FROM seguidores WHERE id_seguido = :id";
            $consulta = $this->conn->prepare($sql);
            $consulta->bindParam(":id", $id_usuario, PDO::PARAM_INT);
            $consulta->execute();
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            return $fila["total"];
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
            return $fila["total"];
        } catch (PDOException $e) {
            echo "Error al contar seguidos: " . $e->getMessage();
            return 0;
        }
    }

}
?>

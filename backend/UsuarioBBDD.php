<?php
// Usuario.php
// Clase para manejar usuarios en la base de datos
require_once "Usuario.php";//incluimos la clase Usuario
require_once "ConexionDB.php"; // Incluimos la conexión

class UsuarioBBDD{
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
        $resultado = false;
        try {
            $conexion = ConexionDB::getConexion("moodloop");
            $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE correo = :correo");
            $consulta->bindParam(":correo", $correo);
            $consulta->execute();
            if ($consulta->rowCount() == 1) {
                $fila = $consulta->fetch(PDO::FETCH_ASSOC);
                $token = $fila["token"];
                return $token;
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
}
?>

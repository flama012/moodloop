<?php
require_once "Usuario.php";
class UsuarioBBDD
{

    // Método para obtener todos los usuarios
    public function listarUsuarios() {
        $sql = "SELECT id_usuario, nombre_usuario, correo FROM Usuarios";
        $resultado = mysqli_query($this->conn, $sql);

        $usuarios = []; // Array vacío para guardar resultados

        // Si hay filas en el resultado, las recorremos con un bucle
        if (mysqli_num_rows($resultado) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $usuarios[] = $fila; // Añadimos cada fila al array
            }
        }

        return $usuarios; // Devolvemos el array de usuarios
    }

    // Método para mostrar usuarios en HTML
    public function mostrarUsuariosHTML() {
        $usuarios = $this->listarUsuarios();

        if (count($usuarios) > 0) {
            echo "<h2>Usuarios registrados</h2><ul>";
            // Recorremos el array con un bucle foreach
            foreach ($usuarios as $u) {
                echo "<li>" . $u["nombre_usuario"] . " (" . $u["correo"] . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "No hay usuarios.";
        }
    }

    // Comprobar repetidos
    public function comprobarRepetido($email) {
        $sql = "SELECT id FROM usuarios WHERE  correo = :email";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }


    public function existeEmail($email){
        $resultado = false;
        try {
            $conexion = ConexionDB::getConexion("prueba");
            $consulta = $conexion->prepare("SELECT email FROM usuarios WHERE email = :email");
            $consulta->bindParam(":email", $email);
            $consulta->execute();
            if ($consulta->rowCount() == 1) {
                $resultado = true;
            }
        }
        catch (PDOException $e) {

        }
        return $resultado;

    }

    public function insertarUsuario($dni, $apellido, $nombre, $email, $password, $token, $rol){
        $resultado = false;
        try {
            $conexion = ConexionDB::getConexion("prueba");
            $consulta = $conexion->prepare("INSERT INTO usuarios (dni, apellidos, nombre, email, password, token, rol) VALUES (:dni, :apellido, :nombre, :email, :password, :token, :rol)");
            $consulta->bindParam(":dni", $dni);
            $consulta->bindParam(":apellido", $apellido);
            $consulta->bindParam(":nombre", $nombre);
            $consulta->bindParam(":email", $email);
            $consulta->bindParam(":password", $password);
            $consulta->bindParam(":token", $token);
            $consulta->bindParam(":rol", $rol);

            $consulta->execute();
        }
        catch (PDOException $e) {
            //no hacemos nada ya que solo la vamos a capturar y devolverá false la función
        }
        if($consulta->rowCount() ==1){
            $resultado = true;
        }

        return $resultado;
    }

    public function obtenerTokern($email){
        $resultado = false;
        try {
            $conexion = ConexionDB::getConexion("prueba");
            $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
            $consulta->bindParam(":email", $email);
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
    public function obtenerUsuario($email){
        $usuario = false;
        try{
            $conexion = ConexionDB::getConexion("prueba");
            $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
            $consulta->bindParam(":email", $email);
            $consulta->execute();
            if ($consulta->rowCount() == 1) {
                $fila = $consulta->fetch(PDO::FETCH_ASSOC);
                $usuario = new Usuario($fila["dni"], $fila["apellidos"], $fila["nombre"], $fila["email"], $fila["password"]);
                $usuario->id=($fila["id"]);
                $usuario->rol=$fila["rol"];
                $usuario->token=$fila["token"];

            }
        }
        catch (PDOException $e) {

        }
        return $usuario;
    }

    public function actualizaRol($usuario){
        $resultado = false;
        try{
            $conexion = ConexionDB::getConexion("prueba");
            $consulta = $conexion->prepare("UPDATE usuarios SET rol = 2 WHERE id = :id");
            $id = $usuario->id;
            $consulta->bindParam(":id", $id);//debemos poner variables simples
            $consulta->execute();
            if ($consulta->rowCount() == 1) {
                $resultado = true;
            }
        }
        catch (PDOException $e) {

        }
        return $resultado;
    }


}
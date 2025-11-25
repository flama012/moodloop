<?php
// UsuarioBBDD.php
// Clase para manejar usuarios en la base de datos

require_once "Conexion.php"; // Incluimos la conexión Singleton

class UsuarioBBDD {
    private $conn; // Guardará la conexión activa con la base de datos

    // Constructor: se ejecuta al crear un objeto UsuarioBBDD
    public function __construct() {
        // Obtenemos la conexión única desde el Singleton
        $this->conn = Conexion::getInstancia()->getConexion();
    }

    // Método para validar si el usuario existe y la contraseña es correcta
    public function validarUsuario($email, $password) {
        $sql = "SELECT contraseña_hash FROM Usuarios WHERE correo = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        // Si se encuentra el usuario, verificamos la contraseña con password_verify
        if ($fila = mysqli_fetch_assoc($resultado)) {
            return password_verify($password, $fila["contraseña_hash"]);
        }
        return false; // No se encontró o la contraseña no coincide
    }

    // Método para obtener todos los datos de un usuario por su correo
    public function getUsuario($email) {
        $sql = "SELECT * FROM Usuarios WHERE correo = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        // Devuelve los datos como array asociativo
        return mysqli_fetch_assoc($resultado);
    }

    // Método para insertar un nuevo usuario en la base de datos
    public function insertarUsuario($nombre, $correo, $hash) {
        $sql = "INSERT INTO Usuarios (nombre_usuario, correo, contraseña_hash, id_rol, confirmado, baneado, fecha_registro) 
                VALUES (?, ?, ?, 2, false, false, NOW())";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $correo, $hash);

        // Devuelve true si la inserción fue exitosa
        return mysqli_stmt_execute($stmt);
    }

    // Método para obtener todos los usuarios registrados
    public function listarUsuarios() {
        $sql = "SELECT id_usuario, nombre_usuario, correo FROM Usuarios ORDER BY fecha_registro DESC";
        $resultado = mysqli_query($this->conn, $sql);

        $usuarios = [];

        // Si hay resultados, los guardamos en un array
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $usuarios[] = $fila;
            }
        }

        return $usuarios;
    }

    // Método para mostrar usuarios en formato HTML
    public function mostrarUsuariosHTML() {
        $usuarios = $this->listarUsuarios();

        if (count($usuarios) > 0) {
            echo "<h2>Usuarios registrados</h2><ul>";
            foreach ($usuarios as $u) {
                echo "<li>" . htmlspecialchars($u["nombre_usuario"]) . " (" . htmlspecialchars($u["correo"]) . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No hay usuarios registrados.</p>";
        }
    }
}
?>

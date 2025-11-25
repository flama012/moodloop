<?php
// UsuarioBBDD.php
// Clase para manejar usuarios en la base de datos

require_once "Conexion.php"; // Incluimos la conexión (Singleton)

class UsuarioBBDD {
    private $conn; // Guardará la conexión

    // Constructor: se ejecuta al crear un objeto UsuarioBBDD
    public function __construct() {
        // Obtenemos la conexión única desde el Singleton
        $this->conn = Conexion::getInstancia()->getConexion();
    }

    // Método para obtener todos los usuarios
    public function listarUsuarios() {
        $sql = "SELECT id_usuario, nombre_usuario, correo FROM Usuarios";
        $resultado = mysqli_query($this->conn, $sql);

        $usuarios = []; // Array vacío para guardar resultados

        // Si hay filas en el resultado, las recorremos con un bucle
        if ($resultado && mysqli_num_rows($resultado) > 0) {
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
}
?>

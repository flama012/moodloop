<?php
// UsuarioBBDD.php
// Clase Usuario
require_once "Usuario.php";
// Clase para manejar usuarios en la base de datos
require_once "db.php"; // Incluimos la conexión

class UsuarioBBDD {


    private $conn; // Guardará la conexión




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
}
?>

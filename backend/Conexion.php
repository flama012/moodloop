<?php
// Conexion.php
// Clase Singleton para conectar a la base de datos MySQL

class Conexion {
    // Instancia única de la clase
    private static $instancia = null;
    private $conn;

    // Datos de conexión
    private $host = "localhost";          // Servidor de la base de datos
    private $usuario = "root";            // Usuario de MySQL
    private $contrasena = "Ciclo2gs";     // Contraseña de MySQL
    private $base_datos = "moodloop";     // Nombre de la base de datos

    // Constructor privado para evitar instanciación directa
    private function __construct() {
        $this->conn = mysqli_connect(
            $this->host,
            $this->usuario,
            $this->contrasena,
            $this->base_datos
        );

        // Verificar si la conexión fue exitosa
        if (!$this->conn) {
            echo "Error de conexión: " . mysqli_connect_error();
            exit; // Detener el programa si falla
        }
    }

    // Método estático para obtener la instancia única
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    // Método para devolver la conexión
    public function getConexion() {
        return $this->conn;
    }
}
?>

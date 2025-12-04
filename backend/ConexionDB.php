<?php
require_once "Config_ConexionDB.php";

/*
    Clase que gestiona conexiones a la base de datos.
    Usa Singleton para que solo exista una conexión por base de datos.
*/
class ConexionDB {

    // Aquí se guardan las conexiones creadas
    private static $instancia;

    // Constructor privado para evitar crear objetos manualmente
    private function __construct() {}

    // Evita clonar el objeto
    private function __clone() {}

    /*
        Devuelve la conexión a una base de datos.
        Si no existe, la crea y la guarda.
    */
    public static function getConexion($nombreBD) {

        // Si no existe conexión para esta BD, la creamos
        if (!isset(self::$instancia[$nombreBD])) {

            try {
                $option = SGBD . ":host=" . SERVIDOR . ";dbname=" . $nombreBD;

                // Creamos la conexión PDO
                self::$instancia[$nombreBD] = new PDO($option, USERDB, PASSWORDDB);

            } catch (PDOException $e) {

                // Si falla, guardamos null
                self::$instancia[$nombreBD] = null;
            }
        }

        // Devolvemos la conexión guardada
        return self::$instancia[$nombreBD];
    }

    /*
        Cierra la conexión poniendo su valor a null.
        PHP también la cierra automáticamente al terminar el script.
    */
    public static function cerrarConexion($nombreBD) {
        self::$instancia[$nombreBD] = null;
    }
}
?>

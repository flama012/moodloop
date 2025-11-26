<?php
require_once "Config.php";
/**
@author PABLO RUIZ
 */
//Clase que usa el patrón de diseño Singleton
//Garantiza que solo va a existir una única conexión a la BBDD por cada base de datos diferente
class ConexionDB{
    private static $instancia;
    private function __construct(){

    }
    private function __clone(){
    }

    public static function getConexion($nombreBD){

        if(!isset(self::$instancia[$nombreBD])){
            try {
                $option=SGBD.":host=".SERVIDOR.";dbname=".$nombreBD;
                self::$instancia[$nombreBD] = new PDO($option, USERDB, PASSWORDDB);
            }catch(PDOException $e){
                self::$instancia[$nombreBD]=null;
            }
        }
        return self::$instancia[$nombreBD];
    }

    public static function cerrarConexion($nombreBD){
        self::$instancia[$nombreBD]=null;
        //con esto es suficiente para cerrar la conexión
        // si no se llama a este método, igualmente se cierra la conexión
        //cuando cerremos el script de php
    }
}

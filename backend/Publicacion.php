<?php
class Publicacion {

    private $id_publicacion;
    private $id_usuario;
    private $mensaje;
    private $estado_emocional;
    private $fecha_hora;

    public function __construct($id_usuario, $mensaje, $estado_emocional) {
        $this->id_publicacion = 0;
        $this->id_usuario = $id_usuario;
        $this->mensaje = $mensaje;
        $this->estado_emocional = $estado_emocional;
        $this->fecha_hora = date("Y-m-d H:i:s"); // Fecha actual
    }

    // Getter genérico
    public function __get( $name) {
        if(isset($this, $name)){
            return $this->$name;
        }
        return null;
    }

    // Setter genérico
    public function __set( $name, $value){
        if(isset($this, $name)){
            $this->$name = $value;
        }
    }
}



?>
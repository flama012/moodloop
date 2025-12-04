<?php
class Publicacion {

    // Propiedades de la publicación
    private $id_publicacion;
    private $id_usuario;
    private $mensaje;
    private $estado_emocional;
    private $fecha_hora;

    // El constructor crea una nueva publicación con los datos necesarios
    public function __construct($id_usuario, $mensaje, $estado_emocional) {

        // El id se pone a 0 porque lo asignará la base de datos
        $this->id_publicacion = 0;

        // Guardamos el usuario que publica
        $this->id_usuario = $id_usuario;

        // Guardamos el mensaje y el estado emocional
        $this->mensaje = $mensaje;
        $this->estado_emocional = $estado_emocional;

        // Guardamos la fecha y hora actual
        $this->fecha_hora = date("Y-m-d H:i:s");
    }

    // Getter genérico: devuelve el valor de una propiedad
    public function __get($name) {

        // Comprobamos que la propiedad existe
        if (isset($this, $name)) {
            return $this->$name;
        }

        return null;
    }

    // Setter genérico: permite cambiar el valor de una propiedad
    public function __set($name, $value) {

        // Comprobamos que la propiedad existe
        if (isset($this, $name)) {
            $this->$name = $value;
        }
    }
}
?>

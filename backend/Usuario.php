<?php
class Usuario {

    // Propiedades del usuario
    private $id_usuario;
    private $nombre_usuario;
    private $correo;
    private $password;
    private $biografia;
    private $estado_emocional;
    private $id_rol;
    private $confirmado;
    private $baneado;
    private $fechaRegistro;
    private $token;

    // El constructor crea un usuario nuevo con los datos básicos
    public function __construct($nombre_usuario, $correo, $password) {

        // El id lo asignará la base de datos
        $this->id_usuario = 0;

        // Datos principales del usuario
        $this->nombre_usuario = $nombre_usuario;
        $this->correo = $correo;
        $this->password = $password;

        // Valores por defecto
        $this->biografia = "";
        $this->estado_emocional = "";
        $this->id_rol = 2;           // 2 = usuario normal
        $this->confirmado = 0;       // 0 = no confirmado
        $this->baneado = false;      // false = no baneado

        // Fecha actual del registro
        $this->fechaRegistro = date("Y-m-d H:i:s");

        // Token vacío hasta que se genere uno
        $this->token = "";
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
    public function __set($name, $value): void {

        // Comprobamos que la propiedad existe
        if (isset($this, $name)) {
            $this->$name = $value;
        }
    }
}

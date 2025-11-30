<?php
class Usuario {
    private  $id_usuario;
    private  $nombre_usuario;
    private  $correo;
    private  $password;
    private  $biografia;
    private  $estado_emocional;
    private  $id_rol;
    private  $confirmado;
    private  $baneado;
    private  $fechaRegistro;
    private  $token;

    public function __construct( $nombre_usuario,  $correo,  $password) {
        $this->id_usuario = 0;
        $this->nombre_usuario = $nombre_usuario;
        $this->correo = $correo;
        $this->password = $password;
        $this->biografia = "";
        $this->estado_emocional = "";
        $this->id_rol = 2;
        $this->confirmado = 0;
        $this->baneado = false;
        $this->fechaRegistro = date("Y-m-d H:i:s");
        $this->token = "";
    }

    // Getter genérico
    public function __get( $name) {
        if(isset($this, $name)){
            return $this->$name;
        }
        return null;
    }

    // Setter genérico
    public function __set( $name, $value): void {
        if(isset($this, $name)){
            $this->$name = $value;
        }
    }
}

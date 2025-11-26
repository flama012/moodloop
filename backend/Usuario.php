<?php
class Usuario {
    private int $id_usuario;
    private string $nombre_usuario;
    private string $correo;
    private string $password;
    private string $biografia;
    private string $estado_emocional;
    private int $id_rol;
    private int $confirmado;
    private bool $baneado;
    private string $fechaRegistro;
    private string $token;

    public function __construct(string $nombre_usuario, string $correo, string $password) {
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
    public function __get(string $name) {
        if(property_exists($this, $name)){
            return $this->$name;
        }
        return null;
    }

    // Setter genérico
    public function __set(string $name, $value): void {
        if(property_exists($this, $name)){
            $this->$name = $value;
        }
    }
}

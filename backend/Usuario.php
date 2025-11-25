<?php

class Usuario
{
    private $id;
    private $nombre;
    private $email;
    private $password;
    private $biografia;
    private $estado;
    private $rol;

    private $confirmado;

    private $baneado;

    private $fechaRegistro;

    public function __get(string $name){
        return $this->$name;
    }


    public function __set(string $name, $value): void{
        $this->$name = $value;
    }
    // Constructor: se ejecuta al crear un objeto UsuarioBBDD
    public function __construct($nombre, $email, $password, $biografia, $estado) {
        $this->conn = conectar(); // Llamamos a la función conectar()
        //javi
        $this->id =0;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->biografia = $biografia;
        $this->estado = $estado;
        $this->rol = 2;//no se si el 2 es el que esta confirmado o no
        $this->confirmado = 0;
        $this->baneado = false;
        $this->fechaRegistro = date("Y-m-d H:i:s");
    }
}
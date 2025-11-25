<?php
// Usuario.php
// Clase para representar un usuario del sistema

class Usuario {
    private $id_usuario;         // ID único del usuario (opcional si se autogenera)
    private $nombre_usuario;     // Nombre del usuario
    private $correo;             // Correo electrónico
    private $contraseña_hash;    // Contraseña encriptada
    private $biografia;          // Texto opcional sobre el usuario
    private $estado_emocional;   // Estado emocional actual
    private $id_rol;             // Rol asignado (admin, usuario, etc.)
    private $confirmado;         // Si el usuario ha confirmado su cuenta
    private $baneado;            // Si el usuario está baneado
    private $fecha_registro;     // Fecha de registro

    // Constructor: permite crear el objeto con los datos esenciales
    public function __construct($nombre_usuario, $correo, $contraseña_hash, $id_rol = 2) {
        $this->nombre_usuario = $nombre_usuario;
        $this->correo = $correo;
        $this->contraseña_hash = $contraseña_hash;
        $this->id_rol = $id_rol;
        $this->confirmado = false;
        $this->baneado = false;
        $this->fecha_registro = date("Y-m-d H:i:s");
    }

    // Getters: permiten acceder a los atributos
    public function getNombre() {
        return $this->nombre_usuario;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function getContraseñaHash() {
        return $this->contraseña_hash;
    }

    public function getIdRol() {
        return $this->id_rol;
    }

    public function getConfirmado() {
        return $this->confirmado;
    }

    public function getBaneado() {
        return $this->baneado;
    }

    public function getFechaRegistro() {
        return $this->fecha_registro;
    }

    public function getBiografia() {
        return $this->biografia;
    }

    public function getEstadoEmocional() {
        return $this->estado_emocional;
    }

    // Setters: permiten modificar atributos opcionales
    public function setBiografia($biografia) {
        $this->biografia = $biografia;
    }

    public function setEstadoEmocional($estado) {
        $this->estado_emocional = $estado;
    }

    public function setConfirmado($valor) {
        $this->confirmado = $valor;
    }

    public function setBaneado($valor) {
        $this->baneado = $valor;
    }
}
?>
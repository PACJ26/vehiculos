<?php

class Propietario {
    private $id_propietario;
    private $nombre;
    private $apellido;
    private $documento;
    private $telefono;
    private $correo;
    private $estado;
    
    // Constructor
    public function __construct($id_propietario = null, $nombre = null, $apellido = null, 
    $documento = null, $telefono = null, $correo = null, $estado = 'Activo') {
        $this->id_propietario = $id_propietario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->documento = $documento;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->estado = $estado;
    }
    
    // Getters
    public function getId() {
        return $this->id_propietario;
    }
    
    public function getNombre() {
        return $this->nombre;
    }
    
    public function getApellido() {
        return $this->apellido;
    }
    
    public function getDocumento() {
        return $this->documento;
    }
    
    public function getTelefono() {
        return $this->telefono;
    }
    
    public function getCorreo() {
        return $this->correo;
    }
    
    public function getEstado() {
        return $this->estado;
    }
    
    // Setters
    public function setId($id_propietario) {
        $this->id_propietario = $id_propietario;
    }
    
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    
    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }
    
    public function setDocumento($documento) {
        $this->documento = $documento;
    }
    
    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }
    
    public function setCorreo($correo) {
        $this->correo = $correo;
    }
    
    public function setEstado($estado) {
        $this->estado = $estado;
    }
    
    // Método para convertir el objeto a un array
    public function toArray() {
        return [
            'id_propietario' => $this->id_propietario,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'documento' => $this->documento,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'estado' => $this->estado
        ];
    }
}
<?php

class Vehiculo {
    private $id_vehiculo;
    private $placa;
    private $clase;
    private $marca;
    private $linea;
    private $modelo;
    private $color;
    private $imagen_url;
    private $id_propietario;
    private $estado;
    
    // Constructor
    public function __construct($id_vehiculo = null, $placa = null, $clase = null, 
    $marca = null, $linea = null, $modelo = null, $color = null, 
    $imagen_url = null, $id_propietario = null, $estado = 'Activo') {
        $this->id_vehiculo = $id_vehiculo;
        $this->placa = $placa;
        $this->clase = $clase;
        $this->marca = $marca;
        $this->linea = $linea;
        $this->modelo = $modelo;
        $this->color = $color;
        $this->imagen_url = $imagen_url;
        $this->id_propietario = $id_propietario;
        $this->estado = $estado;
    }
    
    // Getters
    public function getId() {
        return $this->id_vehiculo;
    }
    
    public function getPlaca() {
        return $this->placa;
    }
    
    public function getClase() {
        return $this->clase;
    }
    
    public function getMarca() {
        return $this->marca;
    }
    
    public function getLinea() {
        return $this->linea;
    }
    
    public function getModelo() {
        return $this->modelo;
    }
    
    public function getColor() {
        return $this->color;
    }
    
    public function getImagenUrl() {
        return $this->imagen_url;
    }
    
    public function getIdPropietario() {
        return $this->id_propietario;
    }
    
    public function getEstado() {
        return $this->estado;
    }
    
    // Setters
    public function setId($id_vehiculo) {
        $this->id_vehiculo = $id_vehiculo;
    }
    
    public function setPlaca($placa) {
        $this->placa = $placa;
    }
    
    public function setClase($clase) {
        $this->clase = $clase;
    }
    
    public function setMarca($marca) {
        $this->marca = $marca;
    }
    
    public function setLinea($linea) {
        $this->linea = $linea;
    }
    
    public function setModelo($modelo) {
        $this->modelo = $modelo;
    }
    
    public function setColor($color) {
        $this->color = $color;
    }
    
    public function setImagenUrl($imagen_url) {
        $this->imagen_url = $imagen_url;
    }
    
    public function setIdPropietario($id_propietario) {
        $this->id_propietario = $id_propietario;
    }
    
    public function setEstado($estado) {
        $this->estado = $estado;
    }
    
    // Método para convertir el objeto a un array
    public function toArray() {
        return [
            'id_vehiculo' => $this->id_vehiculo,
            'placa' => $this->placa,
            'clase' => $this->clase,
            'marca' => $this->marca,
            'linea' => $this->linea,
            'modelo' => $this->modelo,
            'color' => $this->color,
            'imagen_url' => $this->imagen_url,
            'id_propietario' => $this->id_propietario,
            'estado' => $this->estado
        ];
    }
}
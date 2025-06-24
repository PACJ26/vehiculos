<?php

class Mantenimiento {
    private $id;
    private $id_vehiculo;
    private $tipo_mantenimiento;
    private $costo;
    private $fecha;
    private $imagen_antes;
    private $imagen_despues;
    private $archivo_pdf;
    
    // Datos adicionales para mostrar en la interfaz
    private $placa_vehiculo;
    private $marca_vehiculo;
    private $modelo_vehiculo;
    
    public function __construct($id = null, $id_vehiculo = null, $tipo_mantenimiento = null, $costo = null, $fecha = null,$imagen_antes, $imagen_despues ,$archivo_pdf = null) {
        $this->id = $id;
        $this->id_vehiculo = $id_vehiculo;
        $this->tipo_mantenimiento = $tipo_mantenimiento;
        $this->costo = $costo;
        $this->fecha = $fecha;
        $this->imagen_antes = $imagen_antes;
        $this->imagen_despues = $imagen_despues;
        $this->archivo_pdf = $archivo_pdf;
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getIdVehiculo() {
        return $this->id_vehiculo;
    }
    
    public function getTipoMantenimiento() {
        return $this->tipo_mantenimiento;
    }
    
    public function getCosto() {
        return $this->costo;
    }
    
    public function getFecha() {
        return $this->fecha;
    }
    
    public function getPlacaVehiculo() {
        return $this->placa_vehiculo;
    }
    
    public function getMarcaVehiculo() {
        return $this->marca_vehiculo;
    }
    
    public function getModeloVehiculo() {
        return $this->modelo_vehiculo;
    }
    public function getImagenAntes() {
        return $this->imagen_antes;
    }

    public function getImagenDespues() {
        return $this->imagen_despues;
    }

    public function getArchivoPdf() {
        return $this->archivo_pdf;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setIdVehiculo($id_vehiculo) {
        $this->id_vehiculo = $id_vehiculo;
    }
    
    public function setTipoMantenimiento($tipo_mantenimiento) {
        $this->tipo_mantenimiento = $tipo_mantenimiento;
    }
    
    public function setCosto($costo) {
        $this->costo = $costo;
    }
    
    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }
    
    public function setPlacaVehiculo($placa_vehiculo) {
        $this->placa_vehiculo = $placa_vehiculo;
    }
    
    public function setMarcaVehiculo($marca_vehiculo) {
        $this->marca_vehiculo = $marca_vehiculo;
    }
    
    public function setModeloVehiculo($modelo_vehiculo) {
        $this->modelo_vehiculo = $modelo_vehiculo;
    }
    public function setImagenAntes($imagen_antes) {
        $this->imagen_antes = $imagen_antes;
    }

    public function setImagenDespues($imagen_despues) {
        $this->imagen_despues = $imagen_despues;
    }
    
    public function setArchivoPdf($archivo_pdf) {
        $this->archivo_pdf = $archivo_pdf;
    }
}
<?php
class Seguro {
    private $id;
    private $id_vehiculo;
    private $aseguradora;
    private $tipo_poliza;
    private $costo;
    private $fecha_inicio;
    private $fecha_vencimiento;
    private $dias_restantes;
    private $estado;
    private $archivo_pdf;
    
    // Propiedades adicionales para información del vehículo
    private $placa_vehiculo;
    private $marca_vehiculo;
    private $modelo_vehiculo;
    
    public function __construct($id = null, $id_vehiculo = null, $aseguradora = null, $tipo_poliza = null, 
                               $costo = null, $fecha_inicio = null, $fecha_vencimiento = null, 
                               $dias_restantes = null, $estado = null, $archivo_pdf = null) {
        $this->id = $id;
        $this->id_vehiculo = $id_vehiculo;
        $this->aseguradora = $aseguradora;
        $this->tipo_poliza = $tipo_poliza;
        $this->costo = $costo;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_vencimiento = $fecha_vencimiento;
        $this->dias_restantes = $dias_restantes;
        $this->estado = $estado;
        $this->archivo_pdf = $archivo_pdf;
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getIdVehiculo() {
        return $this->id_vehiculo;
    }
    
    public function getAseguradora() {
        return $this->aseguradora;
    }
    
    public function getTipoPoliza() {
        return $this->tipo_poliza;
    }
    
    public function getCosto() {
        return $this->costo;
    }
    
    public function getFechaInicio() {
        return $this->fecha_inicio;
    }
    
    public function getFechaVencimiento() {
        return $this->fecha_vencimiento;
    }
    
    public function getDiasRestantes() {
        return $this->dias_restantes;
    }
    
    public function getEstado() {
        return $this->estado;
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
    
    public function setAseguradora($aseguradora) {
        $this->aseguradora = $aseguradora;
    }
    
    public function setTipoPoliza($tipo_poliza) {
        $this->tipo_poliza = $tipo_poliza;
    }
    
    public function setCosto($costo) {
        $this->costo = $costo;
    }
    
    public function setFechaInicio($fecha_inicio) {
        $this->fecha_inicio = $fecha_inicio;
    }
    
    public function setFechaVencimiento($fecha_vencimiento) {
        $this->fecha_vencimiento = $fecha_vencimiento;
    }
    
    public function setDiasRestantes($dias_restantes) {
        $this->dias_restantes = $dias_restantes;
    }
    
    public function setEstado($estado) {
        $this->estado = $estado;
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
    
    public function setArchivoPdf($archivo_pdf) {
        $this->archivo_pdf = $archivo_pdf;
    }
}
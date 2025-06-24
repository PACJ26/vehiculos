<?php

class Multa {
    private $id;
    private $id_vehiculo;
    private $monto_original;
    private $monto_pagado;
    private $metodo_pago;
    private $motivo;
    private $fecha;
    private $fecha_fin;
    private $dias_restantes;
    private $estado;
    
    // Datos adicionales para mostrar en la interfaz
    private $placa_vehiculo;
    private $marca_vehiculo;
    private $modelo_vehiculo;
    
    public function __construct($id = null, $id_vehiculo = null, $monto_original = null, $monto_pagado = null, 
                               $metodo_pago = null, $motivo = null, $fecha = null, $fecha_fin, $dias_restantes = 0, $estado = null) {
        $this->id = $id;
        $this->id_vehiculo = $id_vehiculo;
        $this->monto_original = $monto_original;
        $this->monto_pagado = $monto_pagado;
        $this->metodo_pago = $metodo_pago;
        $this->motivo = $motivo;
        $this->fecha = $fecha;
        $this->fecha_fin = $fecha_fin;
        $this->dias_restantes = $dias_restantes;
        $this->estado = $estado;
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getIdVehiculo() {
        return $this->id_vehiculo;
    }
    
    public function getMontoOriginal() {
        return $this->monto_original;
    }
    
    public function getMontoPagado() {
        return $this->monto_pagado;
    }
    
    public function getMetodoPago() {
        return $this->metodo_pago;
    }
    
    public function getMotivo() {
        return $this->motivo;
    }
    
    public function getFecha() {
        return $this->fecha;
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
    
    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setIdVehiculo($id_vehiculo) {
        $this->id_vehiculo = $id_vehiculo;
    }
    
    public function setMontoOriginal($monto_original) {
        $this->monto_original = $monto_original;
    }
    
    public function setMontoPagado($monto_pagado) {
        $this->monto_pagado = $monto_pagado;
    }
    
    public function setMetodoPago($metodo_pago) {
        $this->metodo_pago = $metodo_pago;
    }
    
    public function setMotivo($motivo) {
        $this->motivo = $motivo;
    }
    
    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }
    
    public function getFechaFin() {
        return $this->fecha_fin;
    }
    
    public function setFechaFin($fecha_fin) {
        $this->fecha_fin = $fecha_fin;
    }
    
    public function getDiasRestantes() {
        return $this->dias_restantes;
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
}
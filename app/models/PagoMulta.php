<?php

class PagoMulta {
    private $id;
    private $id_multa;
    private $fecha_pago;
    private $monto_pagado;
    private $pagos_pdf;
    
    // Datos adicionales para mostrar en la interfaz
    private $placa_vehiculo;
    private $monto_original_multa;
    
    public function __construct($id = null, $id_multa = null, $fecha_pago = null, $monto_pagado = null) {
        $this->id = $id;
        $this->id_multa = $id_multa;
        $this->fecha_pago = $fecha_pago;
        $this->monto_pagado = $monto_pagado;
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getIdMulta() {
        return $this->id_multa;
    }
    
    public function getFechaPago() {
        return $this->fecha_pago;
    }
    
    public function getMontoPagado() {
        return $this->monto_pagado;
    }
    
    public function getPlacaVehiculo() {
        return $this->placa_vehiculo;
    }
    
    public function getMontoOriginalMulta() {
        return $this->monto_original_multa;
    }
    
    public function getPagosPdf() {
        return $this->pagos_pdf;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setIdMulta($id_multa) {
        $this->id_multa = $id_multa;
    }
    
    public function setFechaPago($fecha_pago) {
        $this->fecha_pago = $fecha_pago;
    }
    
    public function setMontoPagado($monto_pagado) {
        $this->monto_pagado = $monto_pagado;
    }
    
    public function setPlacaVehiculo($placa_vehiculo) {
        $this->placa_vehiculo = $placa_vehiculo;
    }
    
    public function setMontoOriginalMulta($monto_original_multa) {
        $this->monto_original_multa = $monto_original_multa;
    }
    
    public function setPagosPdf($pagos_pdf) {
        $this->pagos_pdf = $pagos_pdf;
    }
}
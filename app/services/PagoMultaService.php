<?php
require_once __DIR__ . '/../../database/conexion.php';
require_once __DIR__ . '/../models/PagoMulta.php';
require_once __DIR__ . '/MultaService.php';

class PagoMultaService {
    private $conexion;
    private $multaService;
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
        $this->multaService = new MultaService();
    }
    
    // Obtener todos los pagos de multas con información adicional
    public function obtenerTodos() {
        $sql = "SELECT pm.*, m.monto_original, v.placa 
                FROM pagos_multas pm 
                LEFT JOIN multas m ON pm.id_multa = m.id_multa 
                LEFT JOIN vehiculos v ON m.id_vehiculo = v.id_vehiculo 
                ORDER BY pm.fecha_pago DESC";
        $result = $this->conexion->query($sql);
        $pagos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pago = new PagoMulta(
                    $row['id_pago'],
                    $row['id_multa'],
                    $row['fecha_pago'],
                    $row['monto_pagado']
                );
                
                // Agregar información adicional
                $pago->setPlacaVehiculo($row['placa']);
                $pago->setMontoOriginalMulta($row['monto_original']);
                if (isset($row['pagos_pdf'])) {
                    $pago->setPagosPdf($row['pagos_pdf']);
                }
                
                $pagos[] = $pago;
            }
        }
        
        return $pagos;
    }
    
    // Obtener pago por ID
    public function obtenerPorId($id) {
        $sql = "SELECT pm.*, m.monto_original, v.placa 
                FROM pagos_multas pm 
                LEFT JOIN multas m ON pm.id_multa = m.id_multa 
                LEFT JOIN vehiculos v ON m.id_vehiculo = v.id_vehiculo 
                WHERE pm.id_pago = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $pago = new PagoMulta(
                $row['id_pago'],
                $row['id_multa'],
                $row['fecha_pago'],
                $row['monto_pagado']
            );
            
            // Agregar información adicional
            $pago->setPlacaVehiculo($row['placa']);
            $pago->setMontoOriginalMulta($row['monto_original']);
            if (isset($row['pagos_pdf'])) {
                $pago->setPagosPdf($row['pagos_pdf']);
            }
            
            return $pago;
        }
        
        return null;
    }
    
    // Obtener pagos por ID de multa
    public function obtenerPorMulta($id_multa) {
        $sql = "SELECT pm.*, m.monto_original, v.placa 
                FROM pagos_multas pm 
                LEFT JOIN multas m ON pm.id_multa = m.id_multa 
                LEFT JOIN vehiculos v ON m.id_vehiculo = v.id_vehiculo 
                WHERE pm.id_multa = ? 
                ORDER BY pm.fecha_pago DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_multa);
        $stmt->execute();
        $result = $stmt->get_result();
        $pagos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pago = new PagoMulta(
                    $row['id_pago'],
                    $row['id_multa'],
                    $row['fecha_pago'],
                    $row['monto_pagado']
                );
                
                // Agregar información adicional
                $pago->setPlacaVehiculo($row['placa']);
                $pago->setMontoOriginalMulta($row['monto_original']);
                
                $pagos[] = $pago;
            }
        }
        
        return $pagos;
    }
    
    // Verificar si existe un pago por ID
    public function existePago($id) {
        $sql = "SELECT id_pago FROM pagos_multas WHERE id_pago = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    // Crear pago desde un array de datos
    public function crearDesdeArray($datos) {
        // Crear objeto PagoMulta
        $pago = new PagoMulta(
            null,
            $datos['id_multa'],
            $datos['fecha_pago'],
            $datos['monto_pagado']
        );
        
        // Agregar ruta del PDF si existe
        if (isset($datos['pagos_pdf'])) {
            $pago->setPagosPdf($datos['pagos_pdf']);
        }
        
        // Guardar en la base de datos
        if ($this->crear($pago)) {
            return ['exito' => true, 'id' => $pago->getId()];
        } else {
            return ['exito' => false];
        }
    }
    
    // Actualizar pago desde un array de datos
    public function actualizarDesdeArray($datos) {
        // Obtener pago existente
        $pago = $this->obtenerPorId($datos['id_pago']);
        if (!$pago) {
            return ['exito' => false];
        }
        
        // Actualizar datos
        $pago->setIdMulta($datos['id_multa']);
        $pago->setFechaPago($datos['fecha_pago']);
        $pago->setMontoPagado($datos['monto_pagado']);
        
        // Guardar cambios
        if ($this->actualizar($pago)) {
            return ['exito' => true];
        } else {
            return ['exito' => false];
        }
    }
    
    // Crear nuevo pago de multa
    public function crear($pago) {
        // Iniciamos una transacción para asegurar la integridad de los datos
        $this->conexion->begin_transaction();
        
        try {
            // Primero insertamos el pago
            $sql = "INSERT INTO pagos_multas (id_multa, fecha_pago, monto_pagado, pagos_pdf) VALUES (?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            
            $id_multa = $pago->getIdMulta();
            $fecha_pago = $pago->getFechaPago();
            $monto_pagado = $pago->getMontoPagado();
            $pagos_pdf = $pago->getPagosPdf();
            
            $stmt->bind_param("isds", $id_multa, $fecha_pago, $monto_pagado, $pagos_pdf);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al registrar el pago");
            }
            
            $pago->setId($stmt->insert_id);
            
            // Luego actualizamos el monto pagado y el estado de la multa
            if (!$this->multaService->actualizarPago($id_multa, $monto_pagado)) {
                throw new Exception("Error al actualizar el estado de la multa");
            }
            
            // Si todo salió bien, confirmamos la transacción
            $this->conexion->commit();
            return true;
            
        } catch (Exception $e) {
            // Si hubo algún error, revertimos la transacción
            $this->conexion->rollback();
            return false;
        }
    }
    
    // Actualizar pago existente
    public function actualizar($pago) {
        // Iniciamos una transacción
        $this->conexion->begin_transaction();
        
        try {
            // Primero obtenemos el pago actual para saber el monto anterior
            $pago_actual = $this->obtenerPorId($pago->getId());
            if (!$pago_actual) {
                throw new Exception("El pago no existe");
            }
            
            // Calculamos la diferencia de montos
            $diferencia_monto = $pago->getMontoPagado() - $pago_actual->getMontoPagado();
            
            // Actualizamos el pago
            $sql = "UPDATE pagos_multas SET id_multa = ?, fecha_pago = ?, monto_pagado = ?, pagos_pdf = ? WHERE id_pago = ?";
            $stmt = $this->conexion->prepare($sql);
            
            $id = $pago->getId();
            $id_multa = $pago->getIdMulta();
            $fecha_pago = $pago->getFechaPago();
            $monto_pagado = $pago->getMontoPagado();
            $pagos_pdf = $pago->getPagosPdf();
            
            $stmt->bind_param("isdsi", $id_multa, $fecha_pago, $monto_pagado, $pagos_pdf, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el pago");
            }
            
            // Actualizamos el monto pagado de la multa si hubo cambio en el monto
            if ($diferencia_monto != 0) {
                if (!$this->multaService->actualizarPago($id_multa, $diferencia_monto)) {
                    throw new Exception("Error al actualizar el estado de la multa");
                }
            }
            
            // Confirmamos la transacción
            $this->conexion->commit();
            return true;
            
        } catch (Exception $e) {
            // Revertimos la transacción en caso de error
            $this->conexion->rollback();
            return false;
        }
    }
    
    // Eliminar pago
    public function eliminar($id) {
        // Iniciamos una transacción
        $this->conexion->begin_transaction();
        
        try {
            // Primero obtenemos el pago para saber el monto y la multa asociada
            $pago = $this->obtenerPorId($id);
            if (!$pago) {
                throw new Exception("El pago no existe");
            }
            
            // Eliminamos el pago
            $sql = "DELETE FROM pagos_multas WHERE id_pago = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar el pago");
            }
            
            // Actualizamos el monto pagado de la multa (restando el monto del pago eliminado)
            $monto_negativo = -1 * $pago->getMontoPagado();
            if (!$this->multaService->actualizarPago($pago->getIdMulta(), $monto_negativo)) {
                throw new Exception("Error al actualizar el estado de la multa");
            }
            
            // Confirmamos la transacción
            $this->conexion->commit();
            return true;
            
        } catch (Exception $e) {
            // Revertimos la transacción en caso de error
            $this->conexion->rollback();
            return false;
        }
    }
}
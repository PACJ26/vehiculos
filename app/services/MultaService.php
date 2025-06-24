<?php
require_once __DIR__ . '/../../database/conexion.php';
require_once __DIR__ . '/../models/Multa.php';

class MultaService {
    private $conexion;
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
        
        // Actualizar automáticamente los días restantes al inicializar el servicio
        $this->actualizarDiasRestantes();
    }
    
    // Obtener todas las multas con información del vehículo
    public function obtenerTodas() {
        $sql = "SELECT m.*, v.placa, v.marca, v.modelo 
                FROM multas m 
                LEFT JOIN vehiculos v ON m.id_vehiculo = v.id_vehiculo 
                ORDER BY m.fecha DESC";
        $result = $this->conexion->query($sql);
        $multas = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $multa = new Multa(
                    $row['id_multa'],
                    $row['id_vehiculo'],
                    $row['monto_original'],
                    $row['monto_pagado'],
                    $row['metodo_pago'],
                    $row['motivo'],
                    $row['fecha'],
                    $row['fecha_fin'], // fecha_fin es obligatorio
                    isset($row['dias_restantes']) ? $row['dias_restantes'] : 0,
                    $row['estado']
                );
                
                // Agregar información del vehículo
                $multa->setPlacaVehiculo($row['placa']);
                $multa->setMarcaVehiculo($row['marca']);
                $multa->setModeloVehiculo($row['modelo']);
                
                $multas[] = $multa;
            }
        }
        
        return $multas;
    }
    
    // Obtener multa por ID
    public function obtenerPorId($id) {
        $sql = "SELECT m.*, v.placa, v.marca, v.modelo 
                FROM multas m 
                LEFT JOIN vehiculos v ON m.id_vehiculo = v.id_vehiculo 
                WHERE m.id_multa = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $multa = new Multa(
                $row['id_multa'],
                $row['id_vehiculo'],
                $row['monto_original'],
                $row['monto_pagado'],
                $row['metodo_pago'],
                $row['motivo'],
                $row['fecha'],
                $row['fecha_fin'], // fecha_fin es obligatorio
                isset($row['dias_restantes']) ? $row['dias_restantes'] : 0,
                $row['estado']
            );
            
            // Agregar información del vehículo
            $multa->setPlacaVehiculo($row['placa']);
            $multa->setMarcaVehiculo($row['marca']);
            $multa->setModeloVehiculo($row['modelo']);
            
            return $multa;
        }
        
        return null;
    }
    
    // Obtener multas por ID de vehículo
    public function obtenerPorVehiculo($id_vehiculo) {
        $sql = "SELECT m.*, v.placa, v.marca, v.modelo 
                FROM multas m 
                LEFT JOIN vehiculos v ON m.id_vehiculo = v.id_vehiculo 
                WHERE m.id_vehiculo = ? 
                ORDER BY m.fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_vehiculo);
        $stmt->execute();
        $result = $stmt->get_result();
        $multas = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $multa = new Multa(
                    $row['id_multa'],
                    $row['id_vehiculo'],
                    $row['monto_original'],
                    $row['monto_pagado'],
                    $row['metodo_pago'],
                    $row['motivo'],
                    $row['fecha'],
                    $row['fecha_fin'], // fecha_fin es obligatorio
                    isset($row['dias_restantes']) ? $row['dias_restantes'] : 0,
                    $row['estado']
                );
                
                // Agregar información del vehículo
                $multa->setPlacaVehiculo($row['placa']);
                $multa->setMarcaVehiculo($row['marca']);
                $multa->setModeloVehiculo($row['modelo']);
                
                $multas[] = $multa;
            }
        }
        
        return $multas;
    }
    
    // Crear nueva multa
    public function crear($multa) {
        $sql = "INSERT INTO multas (id_vehiculo, monto_original, monto_pagado, metodo_pago, motivo, fecha, fecha_fin, dias_restantes, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        
        $id_vehiculo = $multa->getIdVehiculo();
        $monto_original = $multa->getMontoOriginal();
        $monto_pagado = $multa->getMontoPagado();
        $metodo_pago = $multa->getMetodoPago();
        $motivo = $multa->getMotivo();
        $fecha = $multa->getFecha();
        $fecha_fin = $multa->getFechaFin();
        $dias_restantes = $multa->getDiasRestantes();
        $estado = $multa->getEstado();
        
        $stmt->bind_param("iddssssis", $id_vehiculo, $monto_original, $monto_pagado, $metodo_pago, $motivo, $fecha, $fecha_fin, $dias_restantes, $estado);
        
        if ($stmt->execute()) {
            $multa->setId($stmt->insert_id);
            return true;
        }
        
        return false;
    }
    
    // Crear multa desde array de datos
    public function crearDesdeArray($datos) {
        // Crear objeto Multa
        $multa = new Multa(
            null,
            $datos['id_vehiculo'],
            $datos['monto_original'],
            isset($datos['monto_pagado']) ? $datos['monto_pagado'] : 0, // Monto pagado inicial es 0 si no se proporciona
            $datos['metodo_pago'],
            $datos['motivo'],
            $datos['fecha'],
            $datos['fecha_fin'],
            isset($datos['dias_restantes']) ? $datos['dias_restantes'] : 0,
            isset($datos['estado']) ? $datos['estado'] : 'Pendiente' // Estado inicial es Pendiente si no se proporciona
        );
        
        // Guardar multa
        if ($this->crear($multa)) {
            return ['exito' => true, 'mensaje' => 'Multa creada correctamente', 'id' => $multa->getId()];
        } else {
            return ['exito' => false, 'errores' => ['Error al crear la multa']];
        }
    }
    
    // Actualizar multa existente
    public function actualizar($multa) {
        // Obtener los valores de la multa
        $id = $multa->getId();
        $id_vehiculo = $multa->getIdVehiculo();
        $monto_original = $multa->getMontoOriginal();
        $monto_pagado = $multa->getMontoPagado();
        $metodo_pago = $multa->getMetodoPago();
        $motivo = $multa->getMotivo();
        $fecha = $multa->getFecha();
        $fecha_fin = $multa->getFechaFin();
        $dias_restantes = $multa->getDiasRestantes();
        $estado = $multa->getEstado();
        
        // Construir la consulta SQL
        $sql = "UPDATE multas SET 
                id_vehiculo = ?, 
                monto_original = ?, 
                monto_pagado = ?, 
                metodo_pago = ?, 
                motivo = ?, 
                fecha = ?";
        
        // Parámetros para bind_param
        $params = [$id_vehiculo, $monto_original, $monto_pagado, $metodo_pago, $motivo, $fecha];
        $types = "iddsss";
        
        // fecha_fin ya no puede ser NULL
        $sql .= ", fecha_fin = ?";
        $params[] = $fecha_fin;
        $types .= "s";
        
        // Manejar dias_restantes (puede ser NULL)
        if ($dias_restantes === null) {
            $sql .= ", dias_restantes = NULL";
        } else {
            $sql .= ", dias_restantes = ?";
            $params[] = $dias_restantes;
            $types .= "i";
        }
        
        // Completar la consulta
        $sql .= ", estado = ? WHERE id_multa = ?";
        $params[] = $estado;
        $params[] = $id;
        $types .= "si";
        
        // Preparar y ejecutar la consulta
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        return $stmt->execute();
    }
    
    // Actualizar multa desde array de datos
    public function actualizarDesdeArray($datos) {
        // Obtener multa existente
        $multa = $this->obtenerPorId($datos['id_multa']);
        if (!$multa) {
            return ['exito' => false, 'errores' => ['La multa no existe']];
        }
        
        // Actualizar datos
        $multa->setIdVehiculo($datos['id_vehiculo']);
        $multa->setMontoOriginal($datos['monto_original']);
        $multa->setMontoPagado($datos['monto_pagado']);
        $multa->setMetodoPago($datos['metodo_pago']);
        $multa->setMotivo($datos['motivo']);
        $multa->setFecha($datos['fecha']);
        $multa->setFechaFin($datos['fecha_fin']);
        
        if (isset($datos['dias_restantes'])) {
            $multa->setDiasRestantes($datos['dias_restantes']);
        }
        
        $multa->setEstado($datos['estado']);
        
        // Guardar cambios
        if ($this->actualizar($multa)) {
            return ['exito' => true, 'mensaje' => 'Multa actualizada correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al actualizar la multa']];
        }
    }
    
    // Actualizar monto pagado y estado de la multa
    public function actualizarPago($id_multa, $monto_pagado) {
        // Primero obtenemos la multa actual
        $multa = $this->obtenerPorId($id_multa);
        if (!$multa) {
            return false;
        }
        
        // Calculamos el nuevo monto pagado
        $nuevo_monto_pagado = $multa->getMontoPagado() + $monto_pagado;
        
        // Determinamos el estado según el monto pagado
        $estado = $multa->getEstado();
        if ($nuevo_monto_pagado >= $multa->getMontoOriginal()) {
            $estado = 'Pagado';
        } else if ($nuevo_monto_pagado > 0) {
            $estado = 'Pendiente';
        }
        
        // Actualizamos la multa
        $sql = "UPDATE multas SET monto_pagado = ?, estado = ? WHERE id_multa = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("dsi", $nuevo_monto_pagado, $estado, $id_multa);
        
        return $stmt->execute();
    }
    
    // Eliminar multa
    public function eliminar($id) {
        // Primero eliminamos los pagos asociados a esta multa
        $sql_pagos = "DELETE FROM pagos_multas WHERE id_multa = ?";
        $stmt_pagos = $this->conexion->prepare($sql_pagos);
        $stmt_pagos->bind_param("i", $id);
        $stmt_pagos->execute();
        
        // Luego eliminamos la multa
        $sql = "DELETE FROM multas WHERE id_multa = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    // Método para actualizar automáticamente los días restantes de todas las multas
    public function actualizarDiasRestantes() {
        // Obtener todas las multas pendientes (fecha_fin ya es obligatorio)
        $sql = "SELECT id_multa, fecha_fin FROM multas WHERE estado = 'Pendiente'";
        $result = $this->conexion->query($sql);
        
        if ($result->num_rows > 0) {
            $fechaActual = new DateTime(date('Y-m-d'));
            
            while ($row = $result->fetch_assoc()) {
                $fechaFin = new DateTime($row['fecha_fin']);
                $diferencia = $fechaActual->diff($fechaFin);
                
                // Si la fecha fin es futura, calculamos días restantes
                if ($fechaFin >= $fechaActual) {
                    $diasRestantes = $diferencia->days;
                    
                    // Actualizar solo los días restantes
                    $updateSql = "UPDATE multas SET dias_restantes = ? WHERE id_multa = ?";
                    $stmt = $this->conexion->prepare($updateSql);
                    $stmt->bind_param("ii", $diasRestantes, $row['id_multa']);
                    $stmt->execute();
                } else {
                    // Si la fecha ya pasó, ponemos 0 días restantes y cambiamos estado a 'Expirado'
                    $diasRestantes = 0;
                    $estadoExpirado = 'Expirado';
                    
                    // Actualizar días restantes y estado
                    $updateSql = "UPDATE multas SET dias_restantes = ?, estado = ? WHERE id_multa = ?";
                    $stmt = $this->conexion->prepare($updateSql);
                    $stmt->bind_param("isi", $diasRestantes, $estadoExpirado, $row['id_multa']);
                    $stmt->execute();
                }
            }
        }
        
        return true;
    }
}
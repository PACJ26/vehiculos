<?php
require_once __DIR__ . '/../../database/conexion.php';
require_once __DIR__ . '/../models/Seguro.php';

class SeguroService {
    private $conexion;
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
        
        // Actualizar automáticamente los días restantes al inicializar el servicio
        $this->actualizarDiasRestantes();
    }
    
    // Método para obtener la conexión a la base de datos
    public function getConexion() {
        return $this->conexion;
    }
    
    // Obtener todos los seguros con información del vehículo
    public function obtenerTodos() {
        $sql = "SELECT s.*, v.placa, v.marca, v.modelo 
                FROM seguros s 
                LEFT JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo 
                ORDER BY s.fecha_inicio DESC";
        $result = $this->conexion->query($sql);
        $seguros = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $seguro = new Seguro(
                    $row['id_seguro'],
                    $row['id_vehiculo'],
                    $row['aseguradora'],
                    $row['tipo_poliza'],
                    $row['costo'],
                    $row['fecha_inicio'],
                    $row['fecha_vencimiento'],
                    isset($row['dias_restantes']) ? $row['dias_restantes'] : 0,
                    $row['estado'],
                    isset($row['archivo_pdf']) ? $row['archivo_pdf'] : null
                );
                
                // Agregar información del vehículo
                $seguro->setPlacaVehiculo($row['placa']);
                $seguro->setMarcaVehiculo($row['marca']);
                $seguro->setModeloVehiculo($row['modelo']);
                
                $seguros[] = $seguro;
            }
        }
        
        return $seguros;
    }
    
    // Obtener seguro por ID
    public function obtenerPorId($id) {
        $sql = "SELECT s.*, v.placa, v.marca, v.modelo 
                FROM seguros s 
                LEFT JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo 
                WHERE s.id_seguro = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $seguro = new Seguro(
                $row['id_seguro'],
                $row['id_vehiculo'],
                $row['aseguradora'],
                $row['tipo_poliza'],
                $row['costo'],
                $row['fecha_inicio'],
                $row['fecha_vencimiento'],
                isset($row['dias_restantes']) ? $row['dias_restantes'] : 0,
                $row['estado'],
                isset($row['archivo_pdf']) ? $row['archivo_pdf'] : null
            );
            
            // Agregar información del vehículo
            $seguro->setPlacaVehiculo($row['placa']);
            $seguro->setMarcaVehiculo($row['marca']);
            $seguro->setModeloVehiculo($row['modelo']);
            
            return $seguro;
        }
        
        return null;
    }
    
    // Obtener seguros por ID de vehículo
    public function obtenerPorVehiculo($id_vehiculo) {
        $sql = "SELECT s.*, v.placa, v.marca, v.modelo 
                FROM seguros s 
                LEFT JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo 
                WHERE s.id_vehiculo = ? 
                ORDER BY s.fecha_inicio DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_vehiculo);
        $stmt->execute();
        $result = $stmt->get_result();
        $seguros = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $seguro = new Seguro(
                    $row['id_seguro'],
                    $row['id_vehiculo'],
                    $row['aseguradora'],
                    $row['tipo_poliza'],
                    $row['costo'],
                    $row['fecha_inicio'],
                    $row['fecha_vencimiento'],
                    isset($row['dias_restantes']) ? $row['dias_restantes'] : 0,
                    $row['estado'],
                    isset($row['archivo_pdf']) ? $row['archivo_pdf'] : null
                );
                
                // Agregar información del vehículo
                $seguro->setPlacaVehiculo($row['placa']);
                $seguro->setMarcaVehiculo($row['marca']);
                $seguro->setModeloVehiculo($row['modelo']);
                
                $seguros[] = $seguro;
            }
        }
        
        return $seguros;
    }
    
    // Crear nuevo seguro
    public function crear($seguro) {
        $sql = "INSERT INTO seguros (id_vehiculo, aseguradora, tipo_poliza, costo, fecha_inicio, fecha_vencimiento, dias_restantes, estado, archivo_pdf) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        
        $id_vehiculo = $seguro->getIdVehiculo();
        $aseguradora = $seguro->getAseguradora();
        $tipo_poliza = $seguro->getTipoPoliza();
        $costo = $seguro->getCosto();
        $fecha_inicio = $seguro->getFechaInicio();
        $fecha_vencimiento = $seguro->getFechaVencimiento();
        $dias_restantes = $seguro->getDiasRestantes();
        $estado = $seguro->getEstado();
        $archivo_pdf = $seguro->getArchivoPdf();
        
        $stmt->bind_param("issdssiss", $id_vehiculo, $aseguradora, $tipo_poliza, $costo, $fecha_inicio, $fecha_vencimiento, $dias_restantes, $estado, $archivo_pdf);
        
        if ($stmt->execute()) {
            $seguro->setId($stmt->insert_id);
            return true;
        }
        
        return false;
    }
    
    // Crear seguro desde array de datos
    public function crearDesdeArray($datos) {
        try {
            // Validar datos requeridos
            $campos_requeridos = ['id_vehiculo', 'aseguradora', 'tipo_poliza', 'costo', 'fecha_inicio', 'fecha_vencimiento'];
            $errores = [];
            
            foreach ($campos_requeridos as $campo) {
                if (!isset($datos[$campo]) || empty($datos[$campo])) {
                    $errores[] = "El campo {$campo} es requerido";
                }
            }
            
            // Validar que el vehículo existe
            $stmt = $this->conexion->prepare("SELECT id_vehiculo FROM vehiculos WHERE id_vehiculo = ?");
            $stmt->bind_param("i", $datos['id_vehiculo']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $errores[] = "El vehículo seleccionado no existe";
            }
            
            // Si hay errores, retornar
            if (!empty($errores)) {
                return ['exito' => false, 'errores' => $errores];
            }

            // Calcular días restantes
            $fecha_inicio = new DateTime($datos['fecha_inicio']);
            $fecha_vencimiento = new DateTime($datos['fecha_vencimiento']);
            $dias_restantes = $fecha_inicio->diff($fecha_vencimiento)->days;

            // Crear objeto Seguro
            $seguro = new Seguro(
                null,
                $datos['id_vehiculo'],
                $datos['aseguradora'],
                $datos['tipo_poliza'],
                $datos['costo'],
                $datos['fecha_inicio'],
                $datos['fecha_vencimiento'],
                $dias_restantes,
                'Vigente', // Estado inicial siempre es Vigente
                isset($datos['archivo_pdf']) ? $datos['archivo_pdf'] : null
            );
            
            // Guardar seguro
            if ($this->crear($seguro)) {
                return [
                    'exito' => true, 
                    'mensaje' => 'Seguro creado correctamente', 
                    'id' => $seguro->getId()
                ];
            } else {
                return [
                    'exito' => false, 
                    'errores' => ['Error al guardar el seguro en la base de datos']
                ];
            }
        } catch (Exception $e) {
            error_log("Error al crear seguro: " . $e->getMessage());
            return [
                'exito' => false, 
                'errores' => ['Error interno del servidor al crear el seguro']
            ];
        }
    }
    
    // Actualizar seguro existente
    public function actualizar($seguro) {
        // Obtener los valores del seguro
        $id = $seguro->getId();
        $id_vehiculo = $seguro->getIdVehiculo();
        $aseguradora = $seguro->getAseguradora();
        $tipo_poliza = $seguro->getTipoPoliza();
        $costo = $seguro->getCosto();
        $fecha_inicio = $seguro->getFechaInicio();
        $fecha_vencimiento = $seguro->getFechaVencimiento();
        $dias_restantes = $seguro->getDiasRestantes();
        $estado = $seguro->getEstado();
        $archivo_pdf = $seguro->getArchivoPdf();
        
        // Construir la consulta SQL
        $sql = "UPDATE seguros SET 
                id_vehiculo = ?, 
                aseguradora = ?, 
                tipo_poliza = ?, 
                costo = ?, 
                fecha_inicio = ?, 
                fecha_vencimiento = ?, 
                dias_restantes = ?, 
                estado = ?, 
                archivo_pdf = ? 
                WHERE id_seguro = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("issdssissi", $id_vehiculo, $aseguradora, $tipo_poliza, $costo, $fecha_inicio, $fecha_vencimiento, $dias_restantes, $estado, $archivo_pdf, $id);
        
        return $stmt->execute();
    }
    
    // Actualizar seguro desde array de datos
    public function actualizarDesdeArray($datos) {
        // Obtener seguro existente
        $seguro = $this->obtenerPorId($datos['id_seguro']);
        if (!$seguro) {
            return ['exito' => false, 'errores' => ['El seguro no existe']];
        }
        
        // Actualizar datos
        $seguro->setIdVehiculo($datos['id_vehiculo']);
        $seguro->setAseguradora($datos['aseguradora']);
        $seguro->setTipoPoliza($datos['tipo_poliza']);
        $seguro->setCosto($datos['costo']);
        $seguro->setFechaInicio($datos['fecha_inicio']);
        $seguro->setFechaVencimiento($datos['fecha_vencimiento']);
        
        if (isset($datos['dias_restantes'])) {
            $seguro->setDiasRestantes($datos['dias_restantes']);
        }
        
        $seguro->setEstado($datos['estado']);
        
        // Actualizar archivo PDF si existe
        if (isset($datos['archivo_pdf'])) {
            $seguro->setArchivoPdf($datos['archivo_pdf']);
        }
        
        // Guardar cambios
        if ($this->actualizar($seguro)) {
            return ['exito' => true, 'mensaje' => 'Seguro actualizado correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al actualizar el seguro']];
        }
    }
    
    // Eliminar seguro
    public function eliminar($id) {
        $sql = "DELETE FROM seguros WHERE id_seguro = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    // Método para actualizar automáticamente los días restantes de todos los seguros
    public function actualizarDiasRestantes() {
        // Obtener todos los seguros vigentes
        $sql = "SELECT id_seguro, fecha_vencimiento FROM seguros WHERE estado = 'Vigente'";
        $result = $this->conexion->query($sql);
        
        if ($result->num_rows > 0) {
            $fechaActual = new DateTime(date('Y-m-d'));
            
            while ($row = $result->fetch_assoc()) {
                $fechaVencimiento = new DateTime($row['fecha_vencimiento']);
                $diferencia = $fechaActual->diff($fechaVencimiento);
                
                // Si la fecha de vencimiento es futura, calculamos días restantes
                if ($fechaVencimiento >= $fechaActual) {
                    $diasRestantes = $diferencia->days;
                    
                    // Actualizar solo los días restantes
                    $updateSql = "UPDATE seguros SET dias_restantes = ? WHERE id_seguro = ?";
                    $stmt = $this->conexion->prepare($updateSql);
                    $stmt->bind_param("ii", $diasRestantes, $row['id_seguro']);
                    $stmt->execute();
                } else {
                    // Si la fecha ya pasó, ponemos 0 días restantes y cambiamos estado a 'Expirado'
                    $diasRestantes = 0;
                    $estadoExpirado = 'Expirado';
                    
                    // Actualizar días restantes y estado
                    $updateSql = "UPDATE seguros SET dias_restantes = ?, estado = ? WHERE id_seguro = ?";
                    $stmt = $this->conexion->prepare($updateSql);
                    $stmt->bind_param("isi", $diasRestantes, $estadoExpirado, $row['id_seguro']);
                    $stmt->execute();
                }
            }
        }
        
        return true;
    }
    
    // Método para cancelar un seguro
    public function cancelar($id) {
        // Obtener seguro existente
        $seguro = $this->obtenerPorId($id);
        if (!$seguro) {
            return ['exito' => false, 'errores' => ['El seguro no existe']];
        }
        
        // Preparar datos para actualización
        $datos = [
            'id_seguro' => $id,
            'id_vehiculo' => $seguro->getIdVehiculo(),
            'aseguradora' => $seguro->getAseguradora(),
            'tipo_poliza' => $seguro->getTipoPoliza(),
            'costo' => $seguro->getCosto(),
            'fecha_inicio' => $seguro->getFechaInicio(),
            'fecha_vencimiento' => $seguro->getFechaVencimiento(),
            'dias_restantes' => $seguro->getDiasRestantes(),
            'estado' => 'Cancelado'
        ];
        
        // Usar el servicio para actualizar el seguro
        return $this->actualizarDesdeArray($datos);
    }
}
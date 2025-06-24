<?php
require_once __DIR__ . '/../../database/conexion.php';
require_once __DIR__ . '/../models/Propietario.php';

class PropietarioService {
    private $conexion;
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }
    
    // Obtener todos los propietarios (incluyendo inactivos)
    public function obtenerTodos() {
        $propietarios = [];
        $sql = "SELECT * FROM propietarios ORDER BY nombre ASC";
        
        $resultado = $this->conexion->query($sql);
        
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $propietario = new Propietario(
                    $fila['id_propietario'],
                    $fila['nombre'],
                    $fila['apellido'],
                    $fila['documento'],
                    $fila['telefono'],
                    $fila['correo'],
                    $fila['estado']
                );
                $propietarios[] = $propietario;
            }
        }
        
        return $propietarios;
    }
    
    // Obtener propietario por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM propietarios WHERE id_propietario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return new Propietario(
                $fila['id_propietario'],
                $fila['nombre'],
                $fila['apellido'],
                $fila['documento'],
                $fila['telefono'],
                $fila['correo'],
                $fila['estado']
            );
        }
        
        return null;
    }

    public function existeDocumentoOCorreo($documento, $correo) {
        $sql = "SELECT * FROM propietarios WHERE documento =? OR correo =?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $documento, $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0;
    }
    
    // Crear nuevo propietario
    public function crear($propietario) {
        
        $sql = "INSERT INTO propietarios (nombre, apellido, documento, telefono, correo, estado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        
        $nombre = $propietario->getNombre();
        $apellido = $propietario->getApellido();
        $documento = $propietario->getDocumento();
        $telefono = $propietario->getTelefono();
        $correo = $propietario->getCorreo();
        $estado = $propietario->getEstado();
        
        $stmt->bind_param("ssssss", $nombre, $apellido, $documento, $telefono, $correo, $estado);
        
        if ($stmt->execute()) {
            $propietario->setId($stmt->insert_id);
            return true;
        }
        
        return false;
    }
    
    // Actualizar propietario existente
    public function actualizar($propietario) {
        // Verificar si el propietario está siendo marcado como inactivo
        $propietarioActual = $this->obtenerPorId($propietario->getId());
        $nuevoEstado = $propietario->getEstado();
        
        $sql = "UPDATE propietarios SET nombre = ?, apellido = ?, documento = ?, telefono = ?, correo = ?, estado = ? WHERE id_propietario = ?";
        $stmt = $this->conexion->prepare($sql);
        
        $id = $propietario->getId();
        $nombre = $propietario->getNombre();
        $apellido = $propietario->getApellido();
        $documento = $propietario->getDocumento();
        $telefono = $propietario->getTelefono();
        $correo = $propietario->getCorreo();
        $estado = $propietario->getEstado();
        
        $stmt->bind_param("ssssssi", $nombre, $apellido, $documento, $telefono, $correo, $estado, $id);
        
        $resultado = $stmt->execute();
        
        // Si el propietario se marcó como inactivo, actualizar sus vehículos
        if ($resultado && $propietarioActual && $propietarioActual->getEstado() === 'Activo' && $nuevoEstado === 'Inactivo') {
            require_once __DIR__ . '/VehiculoService.php';
            $vehiculoService = new VehiculoService();
            $vehiculoService->actualizarVehiculosPorPropietarioInactivo($id);
        }
        
        return $resultado;
    }
    
    // Eliminar propietario
    public function eliminar($id) {
        // Verificar si el propietario tiene vehículos asociados
        $sqlCheck = "SELECT COUNT(*) as total FROM vehiculos WHERE id_propietario = ?";
        $stmtCheck = $this->conexion->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();
        $resultado = $stmtCheck->get_result();
        $fila = $resultado->fetch_assoc();
        
        if ($fila['total'] > 0) {
            // Si tiene vehículos asociados, cambiar estado a inactivo en lugar de eliminar
            $sql = "UPDATE propietarios SET estado = 'Inactivo' WHERE id_propietario = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } else {
            // Si no tiene vehículos, eliminar el registro
            $sql = "DELETE FROM propietarios WHERE id_propietario = ?";
            // para no eliminar por completo el registro se un set estado = 'Inactivo'
            //$sql = "DELETE FROM propietarios SET estado = 'Inactivo' WHERE id_propietario = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
    }
    
    // Buscar propietarios por término
    public function buscar($termino) {
        $propietarios = [];
        $termino = "%$termino%";
        
        $sql = "SELECT * FROM propietarios WHERE 
               nombre LIKE ? OR 
               apellido LIKE ? OR 
               documento LIKE ? OR 
               correo LIKE ? 
               ORDER BY nombre ASC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssss", $termino, $termino, $termino, $termino);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $propietario = new Propietario(
                    $fila['id_propietario'],
                    $fila['nombre'],
                    $fila['apellido'],
                    $fila['documento'],
                    $fila['telefono'],
                    $fila['correo'],
                    $fila['estado']
                );
                $propietarios[] = $propietario;
            }
        }
        
        return $propietarios;
    }
}
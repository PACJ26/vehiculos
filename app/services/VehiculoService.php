<?php
require_once __DIR__ . '/../../database/conexion.php';
require_once __DIR__ . '/../models/Vehiculo.php';
require_once __DIR__ . '/../models/Propietario.php';

class VehiculoService {
    private $conexion;
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }
    
    // Obtener todos los vehículos
    public function obtenerTodos() {
        $vehiculos = [];
        $sql = "SELECT * FROM vehiculos ORDER BY placa ASC";
        
        $resultado = $this->conexion->query($sql);
        
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $vehiculo = new Vehiculo(
                    $fila['id_vehiculo'],
                    $fila['placa'],
                    $fila['clase'],
                    $fila['marca'],
                    $fila['linea'],
                    $fila['modelo'],
                    $fila['color'],
                    $fila['imagen_url'],
                    $fila['id_propietario'],
                    $fila['estado']
                );
                $vehiculos[] = $vehiculo;
            }
        }
        
        return $vehiculos;
    }
    
    // Obtener vehículo por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM vehiculos WHERE id_vehiculo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return new Vehiculo(
                $fila['id_vehiculo'],
                $fila['placa'],
                $fila['clase'],
                $fila['marca'],
                $fila['linea'],
                $fila['modelo'],
                $fila['color'],
                $fila['imagen_url'],
                $fila['id_propietario'],
                $fila['estado']
            );
        }
        
        return null;
    }

    // Verificar si existe una placa
    public function existePlaca($placa, $id_vehiculo = null) {
        if ($id_vehiculo) {
            $sql = "SELECT * FROM vehiculos WHERE placa = ? AND id_vehiculo != ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("si", $placa, $id_vehiculo);
        } else {
            $sql = "SELECT * FROM vehiculos WHERE placa = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("s", $placa);
        }
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0;
    }
    
    // Crear nuevo vehículo
    public function crear($vehiculo) {
        $sql = "INSERT INTO vehiculos (placa, clase, marca, linea, modelo, color, imagen_url, id_propietario, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        
        $placa = strtoupper($vehiculo->getPlaca());
        $clase = $vehiculo->getClase();
        $marca = $vehiculo->getMarca();
        $linea = $vehiculo->getLinea();
        $modelo = $vehiculo->getModelo();
        $color = $vehiculo->getColor();
        $imagen_url = $vehiculo->getImagenUrl();
        $id_propietario = $vehiculo->getIdPropietario();
        $estado = $vehiculo->getEstado();
        
        $stmt->bind_param("sssssssis", $placa, $clase, $marca, $linea, $modelo, $color, $imagen_url, $id_propietario, $estado);
        
        if ($stmt->execute()) {
            $vehiculo->setId($stmt->insert_id);
            return true;
        }
        
        return false;
    }
    
    // Actualizar vehículo existente
    public function actualizar($vehiculo) {
        $sql = "UPDATE vehiculos SET placa = ?, clase = ?, marca = ?, linea = ?, modelo = ?, color = ?, imagen_url = ?, id_propietario = ?, estado = ? WHERE id_vehiculo = ?";
        $stmt = $this->conexion->prepare($sql);
        
        $id = $vehiculo->getId();
        $placa = strtoupper($vehiculo->getPlaca());
        $clase = $vehiculo->getClase();
        $marca = $vehiculo->getMarca();
        $linea = $vehiculo->getLinea();
        $modelo = $vehiculo->getModelo();
        $color = $vehiculo->getColor();
        $imagen_url = $vehiculo->getImagenUrl();
        $id_propietario = $vehiculo->getIdPropietario();
        $estado = $vehiculo->getEstado();
        
        $stmt->bind_param("sssssssisi", $placa, $clase, $marca, $linea, $modelo,$color, $imagen_url, $id_propietario, $estado, $id);
        
        return $stmt->execute();
    }
    
    // Eliminar vehículo
    public function eliminar($id) {
        // Verificar si tiene seguros asociados
        $sql = "SELECT COUNT(*) as total FROM seguros WHERE id_vehiculo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        
        if ($resultado['total'] > 0) {
            return ['exito' => false, 'errores' => ['No se puede eliminar un vehículo con seguros asociados. Debe eliminar todos los seguros primero.']];
        }
        
        // Verificar si tiene multas asociadas
        $sql = "SELECT COUNT(*) as total FROM multas WHERE id_vehiculo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        
        if ($resultado['total'] > 0) {
            return ['exito' => false, 'errores' => ['No se puede eliminar un vehículo con multas asociadas. Debe eliminar todas las multas primero.']];
        }
        
        // Verificar si tiene registros de mantenimiento asociados
        $sql = "SELECT COUNT(*) as total FROM registros_mantenimiento WHERE id_vehiculo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        
        if ($resultado['total'] > 0) {
            return ['exito' => false, 'errores' => ['No se puede eliminar un vehículo con registros de mantenimiento asociados. Debe eliminar todos los registros primero.']];
        }
        
        // Ejecutar la eliminación
        $sql = "DELETE FROM vehiculos WHERE id_vehiculo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return ['exito' => true, 'mensaje' => 'Vehículo eliminado correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al eliminar el vehículo']];
        }
    }
    
    // Buscar vehículos
    public function buscar($termino) {
        $vehiculos = [];
        $termino = "%$termino%";
        
        $sql = "SELECT * FROM vehiculos WHERE 
               placa LIKE ? OR 
               clase LIKE ? OR 
               marca LIKE ? OR 
               linea LIKE ? OR 
               modelo LIKE ? OR 
               color LIKE ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssssss", $termino, $termino, $termino, $termino, $termino, $termino);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $vehiculo = new Vehiculo(
                    $fila['id_vehiculo'],
                    $fila['placa'],
                    $fila['clase'],
                    $fila['marca'],
                    $fila['linea'],
                    $fila['modelo'],
                    $fila['color'],
                    $fila['imagen_url'],
                    $fila['id_propietario'],
                    $fila['estado']
                );
                $vehiculos[] = $vehiculo;
            }
        }
        
        return $vehiculos;
    }
    
    // Obtener propietario de un vehículo
    public function obtenerPropietario($id_propietario) {
        if (!$id_propietario) return null;
        
        $sql = "SELECT * FROM propietarios WHERE id_propietario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_propietario);
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
    
    // Obtener todos los propietarios activos para el select
    public function obtenerPropietariosActivos() {
        $propietarios = [];
        $sql = "SELECT * FROM propietarios WHERE estado = 'Activo' ORDER BY nombre ASC";
        
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
    
    // Actualizar vehículos cuando un propietario se marca como inactivo
    public function actualizarVehiculosPorPropietarioInactivo($id_propietario) {
        $sql = "UPDATE vehiculos SET id_propietario = NULL WHERE id_propietario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_propietario);
        
        return $stmt->execute();
    }
}
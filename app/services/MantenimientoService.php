<?php
require_once __DIR__ . '/../../database/conexion.php';
require_once __DIR__ . '/../models/Mantenimiento.php';

class MantenimientoService
{
    private $conexion;

    public function __construct()
    {
        global $conexion;
        $this->conexion = $conexion;
    }

    // Obtener todos los registros de mantenimiento
    public function obtenerTodos()
    {
        $sql = "SELECT rm.*, v.placa as placa_vehiculo, v.marca as marca_vehiculo, v.modelo as modelo_vehiculo 
                FROM registros_mantenimiento rm 
                LEFT JOIN vehiculos v ON rm.id_vehiculo = v.id_vehiculo 
                ORDER BY rm.fecha DESC";
        $result = $this->conexion->query($sql);

        $mantenimientos = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $mantenimiento = new Mantenimiento(
                    $row['id_registro'],
                    $row['id_vehiculo'],
                    $row['tipo_mantenimiento'],
                    $row['costo'],
                    $row['fecha'],
                    $row['imagen_antes'],
                    $row['imagen_despues'],
                    $row['archivo_pdf']
                );

                // Establecer datos adicionales del vehículo
                $mantenimiento->setPlacaVehiculo($row['placa_vehiculo']);
                $mantenimiento->setMarcaVehiculo($row['marca_vehiculo']);
                $mantenimiento->setModeloVehiculo($row['modelo_vehiculo']);

                $mantenimientos[] = $mantenimiento;
            }
        }

        return $mantenimientos;
    }

    // Obtener mantenimiento por ID
    public function obtenerPorId($id)
    {
        $sql = "SELECT rm.*, v.placa as placa_vehiculo, v.marca as marca_vehiculo, v.modelo as modelo_vehiculo 
                FROM registros_mantenimiento rm 
                LEFT JOIN vehiculos v ON rm.id_vehiculo = v.id_vehiculo 
                WHERE rm.id_registro = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $mantenimiento = new Mantenimiento(
                $row['id_registro'],
                $row['id_vehiculo'],
                $row['tipo_mantenimiento'],
                $row['costo'],
                $row['fecha'],
                $row['imagen_antes'],
                $row['imagen_despues'],
                $row['archivo_pdf']
            );

            // Establecer datos adicionales del vehículo
            $mantenimiento->setPlacaVehiculo($row['placa_vehiculo']);
            $mantenimiento->setMarcaVehiculo($row['marca_vehiculo']);
            $mantenimiento->setModeloVehiculo($row['modelo_vehiculo']);

            return $mantenimiento;
        }

        return null;
    }

    // Crear mantenimiento desde un array de datos

    public function crearDesdeArray($datos)
    {
        // Crear objeto Mantenimiento
        $mantenimiento = new Mantenimiento(
            null,
            $datos['id_vehiculo'],
            $datos['tipo_mantenimiento'],
            $datos['costo'],
            $datos['fecha'],
            ($datos['imagen_antes']) ?? null,
            ($datos['imagen_despues']) ?? null,
            ($datos['archivo_pdf']) ?? null
        );

        // Guardar en la base de datos
        if ($this->crear($mantenimiento)) {
            return ['exito' => true, 'id' => $mantenimiento->getId()];
        } else {
            return ['exito' => false];
        }
    }

    // Actualizar mantenimiento desde un array de datos
    public function actualizarDesdeArray($datos)
    {
        // Obtener mantenimiento existente
        $mantenimiento = $this->obtenerPorId($datos['id_registro']);
        if (!$mantenimiento) {
            return ['exito' => false];
        }

        // Actualizar datos
        $mantenimiento->setIdVehiculo($datos['id_vehiculo']);
        $mantenimiento->setTipoMantenimiento($datos['tipo_mantenimiento']);
        $mantenimiento->setCosto($datos['costo']);
        $mantenimiento->setFecha($datos['fecha']);

        // Actualizar archivo PDF si está presente
        if (isset($datos['archivo_pdf']) && !empty($datos['archivo_pdf'])) {
            $mantenimiento->setArchivoPdf($datos['archivo_pdf']);
        }
        // Actualizar imágenes si están presentes
        if (isset($datos['imagen_antes']) && !empty($datos['imagen_antes'])) {
            $mantenimiento->setImagenAntes($datos['imagen_antes']);
        }
        if (isset($datos['imagen_despues']) && !empty($datos['imagen_despues'])) {
            $mantenimiento->setImagenDespues($datos['imagen_despues']);
        }

        // Guardar cambios
        if ($this->actualizar($mantenimiento)) {
            return ['exito' => true];
        } else {
            return ['exito' => false];
        }
    }

    // Crear nuevo registro de mantenimiento
    public function crear($mantenimiento)
    {
        $sql = "INSERT INTO registros_mantenimiento (id_vehiculo, tipo_mantenimiento, costo, fecha, imagen_antes, imagen_despues, archivo_pdf) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        $id_vehiculo = $mantenimiento->getIdVehiculo();
        $tipo_mantenimiento = $mantenimiento->getTipoMantenimiento();
        $costo = $mantenimiento->getCosto();
        $fecha = $mantenimiento->getFecha();
        $imagen_antes = $mantenimiento->getImagenAntes();
        $imagen_despues = $mantenimiento->getImagenDespues();
        $archivo_pdf = $mantenimiento->getArchivoPdf();

        $stmt->bind_param("isdssss", $id_vehiculo, $tipo_mantenimiento, $costo, $fecha, $imagen_antes, $imagen_despues, $archivo_pdf);

        if ($stmt->execute()) {
            $mantenimiento->setId($stmt->insert_id);
            return true;
        }

        return false;
    }

    // Actualizar registro de mantenimiento existente
    public function actualizar($mantenimiento)
    {
        $sql = "UPDATE registros_mantenimiento 
                SET id_vehiculo = ?, tipo_mantenimiento = ?, costo = ?, fecha = ?, imagen_antes= ?, imagen_despues = ?, archivo_pdf = ? 
                WHERE id_registro = ?";
        $stmt = $this->conexion->prepare($sql);

        $id = $mantenimiento->getId();
        $id_vehiculo = $mantenimiento->getIdVehiculo();
        $tipo_mantenimiento = $mantenimiento->getTipoMantenimiento();
        $costo = $mantenimiento->getCosto();
        $fecha = $mantenimiento->getFecha();
        $imagen_antes = $mantenimiento->getImagenAntes();
        $imagen_despues = $mantenimiento->getImagenDespues();
        $archivo_pdf = $mantenimiento->getArchivoPdf();

        $stmt->bind_param(
            "isdssssi",
            $id_vehiculo,
            $tipo_mantenimiento,
            $costo,
            $fecha,
            $imagen_antes,
            $imagen_despues,
            $archivo_pdf,
            $id
        );

        return $stmt->execute();
    }

    // Eliminar registro de mantenimiento
    public function eliminar($id)
    {
        // Obtener nombres de archivos antes de eliminar
        $sqlSelect = "SELECT imagen_antes, imagen_despues, archivo_pdf FROM registros_mantenimiento WHERE id_registro = ?";
        $stmtSelect = $this->conexion->prepare($sqlSelect);
        $stmtSelect->bind_param("i", $id);
        $stmtSelect->execute();
        $result = $stmtSelect->get_result();
        $registro = $result->fetch_assoc();
        
        // Eliminar registro
        $sqlDelete = "DELETE FROM registros_mantenimiento WHERE id_registro = ?";
        $stmtDelete = $this->conexion->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $id);
        $exito = $stmtDelete->execute();
        
        if($exito) {
            // Eliminar archivos físicos
            $carpetaBase = $_SERVER['DOCUMENT_ROOT'] . '/vehiculos/soportes/mantenimientos/';
            
            if(!empty($registro['imagen_antes'])) {
                @unlink($carpetaBase . 'antes/' . $registro['imagen_antes']);
            }
            if(!empty($registro['imagen_despues'])) {
                @unlink($carpetaBase . 'despues/' . $registro['imagen_despues']);
            }
            if(!empty($registro['archivo_pdf'])) {
                @unlink($carpetaBase . 'pdf/' . $registro['archivo_pdf']);
            }
        }
    
        return $exito;
    }
}

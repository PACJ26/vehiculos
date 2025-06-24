<?php
require_once __DIR__ . '/../services/SeguroService.php';

class SeguroController {
    private $seguroService;
    
    public function __construct() {
        $this->seguroService = new SeguroService();
    }
    
    // Método para actualizar automáticamente los días restantes
    public function actualizarDiasRestantes() {
        return $this->seguroService->actualizarDiasRestantes();
    }
    
    // Método para manejar las solicitudes según la acción
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'crear':
                $this->crearSeguro();
                break;
            case 'actualizar':
                $this->actualizarSeguro();
                break;
            case 'eliminar':
                $this->eliminarSeguro();
                break;
            case 'cancelar':
                $this->cancelarSeguro();
                break;
            case 'obtenerPorId':
                $this->obtenerSeguroPorId();
                break;
            case 'obtenerPorVehiculo':
                $this->obtenerSegurosPorVehiculo();
                break;
            default:
                echo json_encode(['exito' => false, 'errores' => ['Acción no válida']]);
                break;
        }
    }
    
    // Obtener todos los seguros
    public function obtenerTodos() {
        return $this->seguroService->obtenerTodos();
    }
    
    // Obtener seguro por ID
    public function obtenerPorId($id) {
        return $this->seguroService->obtenerPorId($id);
    }
    
    // Obtener seguros por vehículo
    public function obtenerPorVehiculo($id_vehiculo) {
        return $this->seguroService->obtenerPorVehiculo($id_vehiculo);
    }
    
    // Crear nuevo seguro
    public function crear($datos) {
        try {
            // Validar datos
            $errores = $this->validarDatosSeguro($datos);
            if (!empty($errores)) {
                return ['exito' => false, 'errores' => $errores];
            }
            
            // Procesar archivo PDF si existe
            if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
                // Validar tipo de archivo
                $tipo_archivo = $_FILES['archivo_pdf']['type'];
                if ($tipo_archivo !== 'application/pdf') {
                    return ['exito' => false, 'errores' => ['El archivo debe ser un PDF']];
                }

                // Validar tamaño (máximo 5MB)
                if ($_FILES['archivo_pdf']['size'] > 5 * 1024 * 1024) {
                    return ['exito' => false, 'errores' => ['El archivo no debe superar los 5MB']];
                }

                // Obtener la placa del vehículo
                $id_vehiculo = $datos['id_vehiculo'];
                $stmt = $this->seguroService->getConexion()->prepare("SELECT placa FROM vehiculos WHERE id_vehiculo = ?");
                $stmt->bind_param("i", $id_vehiculo);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    return ['exito' => false, 'errores' => ['Vehículo no encontrado']];
                }
                
                $vehiculo = $result->fetch_assoc();
                $placa = $vehiculo['placa'];
                
                // Crear directorio si no existe
                $directorio = __DIR__ . '/../../soportes/seguros/';
                if (!file_exists($directorio)) {
                    if (!mkdir($directorio, 0777, true)) {
                        error_log("Error al crear directorio: " . $directorio);
                        return ['exito' => false, 'errores' => ['Error al crear el directorio para almacenar archivos']];
                    }
                }
                
                // Generar nombre de archivo con la placa y fecha
                $fecha_actual = date('Ymd_His');
                $nombre_archivo = 'seguro_' . $placa . '_' . $fecha_actual . '.pdf';
                $ruta_archivo = $directorio . $nombre_archivo;
                
                // Mover el archivo
                if (!move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $ruta_archivo)) {
                    error_log("Error al mover archivo a: " . $ruta_archivo);
                    return ['exito' => false, 'errores' => ['Error al guardar el archivo PDF del seguro']];
                }
                
                $datos['archivo_pdf'] = $nombre_archivo;
            }
            
            // Usar el servicio para crear el seguro desde el array de datos
            return $this->seguroService->crearDesdeArray($datos);
            
        } catch (Exception $e) {
            error_log("Error al crear seguro: " . $e->getMessage());
            return ['exito' => false, 'errores' => ['Error interno del servidor al crear el seguro']];
        }
    }
    
    // Actualizar seguro existente
    public function actualizar($datos) {
        // Validar datos
        $errores = $this->validarDatosSeguro($datos, true);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }
        
        // Procesar archivo PDF si existe
        if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
            // Obtener la placa del vehículo
            $id_vehiculo = $datos['id_vehiculo'];
            $stmt = $this->seguroService->getConexion()->prepare("SELECT placa FROM vehiculos WHERE id_vehiculo = ?");
            $stmt->bind_param("i", $id_vehiculo);
            $stmt->execute();
            $result = $stmt->get_result();
            $vehiculo = $result->fetch_assoc();
            $placa = $vehiculo['placa'];
            
            // Crear directorio si no existe
            $directorio = __DIR__ . '/../../soportes/seguros/';
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            // Generar nombre de archivo con la placa y fecha
            $fecha_actual = date('Ymd_His');
            $nombre_archivo = $placa . '_' . $fecha_actual . '.pdf';
            $ruta_archivo = $directorio . $nombre_archivo;
            
            // Mover el archivo
            if (move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $ruta_archivo)) {
                $datos['archivo_pdf'] = $nombre_archivo;
            } else {
                return ['exito' => false, 'errores' => ['Error al guardar el archivo PDF del seguro']];
            }
        }
        
        // Usar el servicio para actualizar el seguro desde el array de datos
        return $this->seguroService->actualizarDesdeArray($datos);
    }
    
    // Eliminar seguro
    public function eliminar($id) {
        if ($this->seguroService->eliminar($id)) {
            return ['exito' => true, 'mensaje' => 'Seguro eliminado correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al eliminar el seguro']];
        }
    }
    
    // Cancelar seguro
    public function cancelar($id) {
        return $this->seguroService->cancelar($id);
    }
    
    // Validar datos de seguro
    private function validarDatosSeguro($datos, $esActualizacion = false) {
        $errores = [];
        
        // Validar ID en caso de actualización
        if ($esActualizacion && (!isset($datos['id_seguro']) || empty($datos['id_seguro']))) {
            $errores[] = 'El ID del seguro es requerido';
        }
        
        // Validar ID de vehículo
        if (!isset($datos['id_vehiculo']) || empty($datos['id_vehiculo'])) {
            $errores[] = 'El vehículo es requerido';
        }
        
        // Validar aseguradora
        if (!isset($datos['aseguradora']) || empty($datos['aseguradora'])) {
            $errores[] = 'La aseguradora es requerida';
        }
        
        // Validar tipo de póliza
        if (!isset($datos['tipo_poliza']) || empty($datos['tipo_poliza'])) {
            $errores[] = 'El tipo de póliza es requerido';
        }
        
        // Validar costo
        if (!isset($datos['costo']) || !is_numeric($datos['costo']) || $datos['costo'] <= 0) {
            $errores[] = 'El costo debe ser un número mayor a cero';
        }
        
        // Validar fecha de inicio
        if (!isset($datos['fecha_inicio']) || empty($datos['fecha_inicio'])) {
            $errores[] = 'La fecha de inicio es requerida';
        } else {
            $fecha_inicio = DateTime::createFromFormat('Y-m-d', $datos['fecha_inicio']);
            if (!$fecha_inicio) {
                $errores[] = 'El formato de fecha de inicio debe ser YYYY-MM-DD';
            }
        }
        
        // Validar fecha de vencimiento
        if (!isset($datos['fecha_vencimiento']) || empty($datos['fecha_vencimiento'])) {
            $errores[] = 'La fecha de vencimiento es requerida';
        } else {
            $fecha_vencimiento = DateTime::createFromFormat('Y-m-d', $datos['fecha_vencimiento']);
            if (!$fecha_vencimiento) {
                $errores[] = 'El formato de fecha de vencimiento debe ser YYYY-MM-DD';
            } else if (isset($fecha_inicio) && $fecha_inicio > $fecha_vencimiento) {
                $errores[] = 'La fecha de vencimiento debe ser posterior a la fecha de inicio';
            }
        }
        
        // Validar dias_restantes si está presente
        if (isset($datos['dias_restantes']) && !is_numeric($datos['dias_restantes'])) {
            $errores[] = 'Los días restantes deben ser un valor numérico';
        }
        
        // Validar estado en caso de actualización
        if ($esActualizacion && isset($datos['estado'])) {
            $estados_validos = ['Vigente', 'Expirado', 'Cancelado'];
            if (!in_array($datos['estado'], $estados_validos)) {
                $errores[] = 'El estado no es válido';
            }
        }
        
        return $errores;
    }
    
    // Métodos para manejar solicitudes AJAX
    private function crearSeguro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->crear($_POST);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
        }
    }
    
    private function actualizarSeguro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->actualizar($_POST);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
        }
    }
    
    private function eliminarSeguro() {
        if (isset($_GET['id'])) {
            $resultado = $this->eliminar($_GET['id']);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de seguro no proporcionado']]);
        }
    }
    
    private function cancelarSeguro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $resultado = $this->cancelar($_POST['id']);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de seguro no proporcionado']]);
        }
    }
    
    private function obtenerSeguroPorId() {
        if (isset($_GET['id'])) {
            $seguro = $this->seguroService->obtenerPorId($_GET['id']);
            if ($seguro) {
                echo json_encode([
                    'exito' => true, 
                    'seguro' => [
                        'id' => $seguro->getId(),
                        'id_vehiculo' => $seguro->getIdVehiculo(),
                        'aseguradora' => $seguro->getAseguradora(),
                        'tipo_poliza' => $seguro->getTipoPoliza(),
                        'costo' => $seguro->getCosto(),
                        'fecha_inicio' => $seguro->getFechaInicio(),
                        'fecha_vencimiento' => $seguro->getFechaVencimiento(),
                        'dias_restantes' => $seguro->getDiasRestantes(),
                        'estado' => $seguro->getEstado(),
                        'archivo_pdf' => $seguro->getArchivoPdf(),
                        'placa_vehiculo' => $seguro->getPlacaVehiculo(),
                        'marca_vehiculo' => $seguro->getMarcaVehiculo(),
                        'modelo_vehiculo' => $seguro->getModeloVehiculo()
                    ]
                ]);
            } else {
                echo json_encode(['exito' => false, 'errores' => ['Seguro no encontrado']]);
            }
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de seguro no proporcionado']]);
        }
    }
    
    private function obtenerSegurosPorVehiculo() {
        if (isset($_GET['id_vehiculo'])) {
            $seguros = $this->seguroService->obtenerPorVehiculo($_GET['id_vehiculo']);
            $resultado = [];
            
            foreach ($seguros as $seguro) {
                $resultado[] = [
                    'id' => $seguro->getId(),
                    'id_vehiculo' => $seguro->getIdVehiculo(),
                    'aseguradora' => $seguro->getAseguradora(),
                    'tipo_poliza' => $seguro->getTipoPoliza(),
                    'costo' => $seguro->getCosto(),
                    'fecha_inicio' => $seguro->getFechaInicio(),
                    'fecha_vencimiento' => $seguro->getFechaVencimiento(),
                    'dias_restantes' => $seguro->getDiasRestantes(),
                    'estado' => $seguro->getEstado(),
                    'archivo_pdf' => $seguro->getArchivoPdf(),
                    'placa_vehiculo' => $seguro->getPlacaVehiculo(),
                    'marca_vehiculo' => $seguro->getMarcaVehiculo(),
                    'modelo_vehiculo' => $seguro->getModeloVehiculo()
                ];
            }
            
            echo json_encode(['exito' => true, 'seguros' => $resultado]);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de vehículo no proporcionado']]);
        }
    }
}

// Procesar solicitudes si se accede directamente al controlador
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    $controller = new SeguroController();
    $controller->handleRequest();
}
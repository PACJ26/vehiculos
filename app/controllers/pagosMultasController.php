<?php
require_once __DIR__ . '/../services/PagoMultaService.php';
require_once __DIR__ . '/../services/MultaService.php';

class PagosMultasController {
    private $pagoMultaService;
    
    public function __construct() {
        $this->pagoMultaService = new PagoMultaService();
    }
    
    // Método para manejar las solicitudes según la acción
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'crear':
                $this->crearPago();
                break;
            case 'actualizar':
                $this->actualizarPago();
                break;
            case 'eliminar':
                $this->eliminarPago();
                break;
            case 'obtenerPorId':
                $this->obtenerPagoPorId();
                break;
            case 'obtenerPorMulta':
                $this->obtenerPagosPorMulta();
                break;
            default:
                echo json_encode(['exito' => false, 'errores' => ['Acción no válida']]);
                break;
        }
    }
    
    // Obtener todos los pagos
    public function obtenerTodos() {
        return $this->pagoMultaService->obtenerTodos();
    }
    
    // Obtener pago por ID
    public function obtenerPorId($id) {
        return $this->pagoMultaService->obtenerPorId($id);
    }
    
    // Obtener pagos por multa
    public function obtenerPorMulta($id_multa) {
        return $this->pagoMultaService->obtenerPorMulta($id_multa);
    }
    
    // Crear nuevo pago
    public function crear($datos) {
        // Validar datos
        $errores = $this->validarDatosPago($datos);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }
        
        // Usar el servicio para crear el pago
        $resultado = $this->pagoMultaService->crearDesdeArray($datos);
        
        if ($resultado['exito']) {
            return ['exito' => true, 'mensaje' => 'Pago registrado correctamente', 'id' => $resultado['id']];
        } else {
            return ['exito' => false, 'errores' => ['Error al registrar el pago']];
        }
    }
    
    // Actualizar pago existente
    public function actualizar($datos) {
        // Validar datos
        $errores = $this->validarDatosPago($datos, true);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }
        
        // Verificar que el pago existe
        if (!$this->pagoMultaService->existePago($datos['id_pago'])) {
            return ['exito' => false, 'errores' => ['El pago no existe']];
        }
        
        // Usar el servicio para actualizar el pago
        $resultado = $this->pagoMultaService->actualizarDesdeArray($datos);
        
        if ($resultado['exito']) {
            return ['exito' => true, 'mensaje' => 'Pago actualizado correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al actualizar el pago']];
        }
    }
    
    // Eliminar pago
    public function eliminar($id) {
        if ($this->pagoMultaService->eliminar($id)) {
            return ['exito' => true, 'mensaje' => 'Pago eliminado correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al eliminar el pago']];
        }
    }
    
    // Validar datos de pago
    private function validarDatosPago($datos, $esActualizacion = false) {
        $errores = [];
        
        // Validar ID en caso de actualización
        if ($esActualizacion && (!isset($datos['id_pago']) || empty($datos['id_pago']))) {
            $errores[] = 'El ID del pago es requerido';
        }
        
        // Validar ID de multa
        if (!isset($datos['id_multa']) || empty($datos['id_multa'])) {
            $errores[] = 'La multa es requerida';
        }
        
        // Validar monto pagado
        if (!isset($datos['monto_pagado']) || !is_numeric($datos['monto_pagado']) || $datos['monto_pagado'] <= 0) {
            $errores[] = 'El monto a pagar debe ser un número mayor a cero';
        } else {
            // Verificar que el monto no exceda el monto pendiente
            $multa = (new MultaService())->obtenerPorId($datos['id_multa']);
            if ($multa) {
                $montoPendiente = $multa->getMontoOriginal() - $multa->getMontoPagado();
                if ($datos['monto_pagado'] > $montoPendiente) {
                    $errores[] = 'El monto a pagar no puede ser mayor al monto pendiente ($' . number_format($montoPendiente, 2) . ')';
                }
            } else {
                $errores[] = 'No se encontró la multa especificada';
            }
        }
        
        // Validar fecha de pago
        if (!isset($datos['fecha_pago']) || empty($datos['fecha_pago'])) {
            $errores[] = 'La fecha de pago es requerida';
        } else {
            $fecha = date_create_from_format('Y-m-d', $datos['fecha_pago']);
            if (!$fecha) {
                $errores[] = 'El formato de fecha no es válido (YYYY-MM-DD)';
            }
        }
        
        return $errores;
    }
    
    // Métodos para manejar solicitudes AJAX
    private function crearPago() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            
            // Procesar el archivo PDF si existe
            if (isset($_FILES['pago_pdf']) && $_FILES['pago_pdf']['error'] === UPLOAD_ERR_OK) {
                // Obtener información del vehículo para incluir la placa en el nombre del archivo
                $multa = (new MultaService())->obtenerPorId($datos['id_multa']);
                $placa = $multa ? $multa->getPlacaVehiculo() : 'sin_placa';
                
                // Crear directorio si no existe
                $directorio = __DIR__ . '/../../soportes/multas/';
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0777, true);
                }
                
                // Generar nombre único para el archivo
                $nombreArchivo = 'pago_multa_' . $placa . '_' . date('YmdHis') . '.pdf';
                $rutaArchivo = $directorio . $nombreArchivo;
                
                // Mover el archivo subido al directorio destino
                if (move_uploaded_file($_FILES['pago_pdf']['tmp_name'], $rutaArchivo)) {
                    // Guardar la ruta relativa en los datos
                    $datos['pagos_pdf'] = 'soportes/multas/' . $nombreArchivo;
                } else {
                    echo json_encode(['exito' => false, 'errores' => ['Error al guardar el archivo PDF']]);
                    return;
                }
            }
            
            $resultado = $this->crear($datos);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
        }
    }
    
    private function actualizarPago() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->actualizar($_POST);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
        }
    }
    
    private function eliminarPago() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $resultado = $this->eliminar($_POST['id']);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de pago no proporcionado']]);
        }
    }
    
    private function obtenerPagoPorId() {
        if (isset($_GET['id'])) {
            $pago = $this->pagoMultaService->obtenerPorId($_GET['id']);
            if ($pago) {
                echo json_encode([
                    'exito' => true, 
                    'pago' => [
                        'id' => $pago->getId(),
                        'id_multa' => $pago->getIdMulta(),
                        'fecha_pago' => $pago->getFechaPago(),
                        'monto_pagado' => $pago->getMontoPagado(),
                        'placa_vehiculo' => $pago->getPlacaVehiculo(),
                        'monto_original_multa' => $pago->getMontoOriginalMulta(),
                        'pagos_pdf' => $pago->getPagosPdf()
                    ]
                ]);
            } else {
                echo json_encode(['exito' => false, 'errores' => ['Pago no encontrado']]);
            }
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de pago no proporcionado']]);
        }
    }
    
    private function obtenerPagosPorMulta() {
        if (isset($_GET['id_multa'])) {
            $pagos = $this->pagoMultaService->obtenerPorMulta($_GET['id_multa']);
            $resultado = [];
            
            foreach ($pagos as $pago) {
                $resultado[] = [
                    'id' => $pago->getId(),
                    'id_multa' => $pago->getIdMulta(),
                    'fecha_pago' => $pago->getFechaPago(),
                    'monto_pagado' => $pago->getMontoPagado(),
                    'placa_vehiculo' => $pago->getPlacaVehiculo(),
                    'monto_original_multa' => $pago->getMontoOriginalMulta(),
                    'pagos_pdf' => $pago->getPagosPdf()
                ];
            }
            
            echo json_encode(['exito' => true, 'pagos' => $resultado]);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de multa no proporcionado']]);
        }
    }
}

// Procesar solicitudes si se accede directamente al controlador
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    $controller = new PagosMultasController();
    $controller->handleRequest();
}
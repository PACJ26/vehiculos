<?php
require_once __DIR__ . '/../services/MultaService.php';

class MultaController {
    private $multaService;
    
    public function __construct() {
        $this->multaService = new MultaService();
    }
    
    // Método para actualizar automáticamente los días restantes
    public function actualizarDiasRestantes() {
        return $this->multaService->actualizarDiasRestantes();
    }
    
    // Método para manejar las solicitudes según la acción
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'crear':
                $this->crearMulta();
                break;
            case 'actualizar':
                $this->actualizarMulta();
                break;
            case 'eliminar':
                $this->eliminarMulta();
                break;
            case 'anular':
                $this->anularMulta();
                break;
            case 'obtenerPorId':
                $this->obtenerMultaPorId();
                break;
            case 'obtenerPorVehiculo':
                $this->obtenerMultasPorVehiculo();
                break;
            default:
                echo json_encode(['exito' => false, 'errores' => ['Acción no válida']]);
                break;
        }
    }
    
    // Obtener todas las multas
    public function obtenerTodas() {
        return $this->multaService->obtenerTodas();
    }
    
    // Obtener multa por ID
    public function obtenerPorId($id) {
        return $this->multaService->obtenerPorId($id);
    }
    
    // Obtener multas por vehículo
    public function obtenerPorVehiculo($id_vehiculo) {
        return $this->multaService->obtenerPorVehiculo($id_vehiculo);
    }
    
    // Crear nueva multa
    public function crear($datos) {
        // Validar datos
        $errores = $this->validarDatosMulta($datos);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }
        
        // Usar el servicio para crear la multa desde el array de datos
        return $this->multaService->crearDesdeArray($datos);
    }
    
    // Actualizar multa existente
    public function actualizar($datos) {
        // Validar datos
        $errores = $this->validarDatosMulta($datos, true);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }
        
        // Usar el servicio para actualizar la multa desde el array de datos
        return $this->multaService->actualizarDesdeArray($datos);
    }
    
    // Eliminar multa
    public function eliminar($id) {
        if ($this->multaService->eliminar($id)) {
            return ['exito' => true, 'mensaje' => 'Multa eliminada correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al eliminar la multa']];
        }
    }
    
    // Anular multa
    public function anular($id) {
        // Obtener multa existente
        $multa = $this->multaService->obtenerPorId($id);
        if (!$multa) {
            return ['exito' => false, 'errores' => ['La multa no existe']];
        }
        
        // Preparar datos para actualización
        $datos = [
            'id_multa' => $id,
            'id_vehiculo' => $multa->getIdVehiculo(),
            'monto_original' => $multa->getMontoOriginal(),
            'monto_pagado' => $multa->getMontoPagado(),
            'metodo_pago' => $multa->getMetodoPago(),
            'motivo' => $multa->getMotivo(),
            'fecha' => $multa->getFecha(),
            'fecha_fin' => $multa->getFechaFin(),
            'dias_restantes' => $multa->getDiasRestantes(),
            'estado' => 'Anulado'
        ];
        
        // Usar el servicio para actualizar la multa
        return $this->multaService->actualizarDesdeArray($datos);
    }
    
    // Validar datos de multa
    private function validarDatosMulta($datos, $esActualizacion = false) {
        $errores = [];
        
        // Validar ID en caso de actualización
        if ($esActualizacion && (!isset($datos['id_multa']) || empty($datos['id_multa']))) {
            $errores[] = 'El ID de la multa es requerido';
        }
        
        // Validar ID de vehículo
        if (!isset($datos['id_vehiculo']) || empty($datos['id_vehiculo'])) {
            $errores[] = 'El vehículo es requerido';
        }
        
        // Validar monto original
        if (!isset($datos['monto_original']) || !is_numeric($datos['monto_original']) || $datos['monto_original'] <= 0) {
            $errores[] = 'El monto debe ser un número mayor a cero';
        }
        
        // Validar método de pago
        if (!isset($datos['metodo_pago']) || empty($datos['metodo_pago'])) {
            $errores[] = 'El método de pago es requerido';
        } else {
            $metodos_validos = ['Efectivo', 'Tarjeta', 'Transferencia'];
            if (!in_array($datos['metodo_pago'], $metodos_validos)) {
                $errores[] = 'El método de pago no es válido';
            }
        }
        
        // Validar motivo
        if (!isset($datos['motivo']) || empty($datos['motivo'])) {
            $errores[] = 'El motivo es requerido';
        }
        
        // Validar fecha
        if (!isset($datos['fecha']) || empty($datos['fecha'])) {
            $errores[] = 'La fecha es requerida';
        } else {
            $fecha = date_create_from_format('Y-m-d', $datos['fecha']);
            if (!$fecha) {
                $errores[] = 'El formato de fecha no es válido (YYYY-MM-DD)';
            }
        }
        
        // Validar fecha_fin 
        if (!isset($datos['fecha_fin']) || empty($datos['fecha_fin'])) {
            $errores[] = 'La fecha fin es requerida';
        } else {
            $fecha_fin = date_create_from_format('Y-m-d', $datos['fecha_fin']);
            if (!$fecha_fin) {
                $errores[] = 'El formato de fecha fin no es válido (YYYY-MM-DD)';
            }
            
            // Verificar que fecha_fin sea posterior a fecha
            if (isset($fecha) && isset($fecha_fin) && $fecha > $fecha_fin) {
                $errores[] = 'La fecha fin debe ser posterior a la fecha de inicio';
            }
        }
        
        // Validar dias_restantes si está presente
        if (isset($datos['dias_restantes']) && !is_numeric($datos['dias_restantes'])) {
            $errores[] = 'Los días restantes deben ser un valor numérico';
        }
        
        // Validar estado en caso de actualización
        if ($esActualizacion && isset($datos['estado'])) {
            $estados_validos = ['Pendiente', 'Pagado', 'Anulado'];
            if (!in_array($datos['estado'], $estados_validos)) {
                $errores[] = 'El estado no es válido';
            }
        }
        
        return $errores;
    }
    
    // Métodos para manejar solicitudes AJAX
    private function crearMulta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->crear($_POST);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
        }
    }
    
    private function actualizarMulta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->actualizar($_POST);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
        }
    }
    
    private function eliminarMulta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $resultado = $this->eliminar($_POST['id']);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de multa no proporcionado']]);
        }
    }
    
    private function anularMulta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $resultado = $this->anular($_POST['id']);
            echo json_encode($resultado);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de multa no proporcionado']]);
        }
    }
    
    private function obtenerMultaPorId() {
        if (isset($_GET['id'])) {
            $multa = $this->multaService->obtenerPorId($_GET['id']);
            if ($multa) {
                echo json_encode([
                    'exito' => true, 
                    'multa' => [
                        'id' => $multa->getId(),
                        'id_vehiculo' => $multa->getIdVehiculo(),
                        'monto_original' => $multa->getMontoOriginal(),
                        'monto_pagado' => $multa->getMontoPagado(),
                        'metodo_pago' => $multa->getMetodoPago(),
                        'motivo' => $multa->getMotivo(),
                        'fecha' => $multa->getFecha(),
                        'fecha_fin' => $multa->getFechaFin(),
                        'dias_restantes' => $multa->getDiasRestantes(),
                        'estado' => $multa->getEstado(),
                        'placa_vehiculo' => $multa->getPlacaVehiculo(),
                        'marca_vehiculo' => $multa->getMarcaVehiculo(),
                        'modelo_vehiculo' => $multa->getModeloVehiculo()
                    ]
                ]);
            } else {
                echo json_encode(['exito' => false, 'errores' => ['Multa no encontrada']]);
            }
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de multa no proporcionado']]);
        }
    }
    
    private function obtenerMultasPorVehiculo() {
        if (isset($_GET['id_vehiculo'])) {
            $multas = $this->multaService->obtenerPorVehiculo($_GET['id_vehiculo']);
            $resultado = [];
            
            foreach ($multas as $multa) {
                $resultado[] = [
                    'id' => $multa->getId(),
                    'id_vehiculo' => $multa->getIdVehiculo(),
                    'monto_original' => $multa->getMontoOriginal(),
                    'monto_pagado' => $multa->getMontoPagado(),
                    'metodo_pago' => $multa->getMetodoPago(),
                    'motivo' => $multa->getMotivo(),
                    'fecha' => $multa->getFecha(),
                    'fecha_fin' => $multa->getFechaFin(),
                    'dias_restantes' => $multa->getDiasRestantes(),
                    'estado' => $multa->getEstado(),
                    'placa_vehiculo' => $multa->getPlacaVehiculo(),
                    'marca_vehiculo' => $multa->getMarcaVehiculo(),
                    'modelo_vehiculo' => $multa->getModeloVehiculo()
                ];
            }
            
            echo json_encode(['exito' => true, 'multas' => $resultado]);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['ID de vehículo no proporcionado']]);
        }
    }
}

// Procesar solicitudes si se accede directamente al controlador
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    $controller = new MultaController();
    $controller->handleRequest();
}
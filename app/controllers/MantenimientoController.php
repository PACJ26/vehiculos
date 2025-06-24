<?php
require_once __DIR__ . '/../services/MantenimientoService.php';

class MantenimientoController
{
    private $mantenimientoService;

    public function __construct()
    {
        $this->mantenimientoService = new MantenimientoService();
    }

    // Método para manejar las solicitudes según la acción
    public function handleRequest()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        switch ($action) {
            case 'crear':
                $this->crearMantenimiento();
                break;
            case 'actualizar':
                $this->actualizarMantenimiento();
                break;
            case 'eliminar':
                $this->eliminarMantenimiento();
                break;
            case 'obtenerPorId':
                $this->obtenerMantenimientoPorId();
                break;
            default:
                echo json_encode(['exito' => false, 'errores' => ['Acción no válida']]);
                break;
        }
    }

    // Obtener todos los mantenimientos
    public function obtenerTodos()
    {
        return $this->mantenimientoService->obtenerTodos();
    }

    // Obtener mantenimiento por ID
    public function obtenerPorId($id)
    {
        return $this->mantenimientoService->obtenerPorId($id);
    }
    
    // Crear nuevo mantenimiento
    public function crear($datos)
    {
        // Validar datos
        $errores = $this->validarDatosMantenimiento($datos);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        // Usar el servicio para crear el mantenimiento
        $resultado = $this->mantenimientoService->crearDesdeArray($datos);

        if ($resultado['exito']) {
            return ['exito' => true, 'mensaje' => 'Registro de mantenimiento creado correctamente', 'id' => $resultado['id']];
        } else {
            return ['exito' => false, 'errores' => ['Error al crear el registro de mantenimiento']];
        }
    }

    // Actualizar mantenimiento existente
    public function actualizar($datos)
    {
        // Validar datos
        $errores = $this->validarDatosMantenimiento($datos, true);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        /*/ Verificar que el mantenimiento existe
        if (!$this->mantenimientoService->existeMantenimiento($datos['id_registro'])) {
            return ['exito' => false, 'errores' => ['Registro de mantenimiento no encontrado']];
        }
        */

        // Usar el servicio para actualizar el mantenimiento
        $resultado = $this->mantenimientoService->actualizarDesdeArray($datos);

        if ($resultado['exito']) {
            return ['exito' => true, 'mensaje' => 'Registro de mantenimiento actualizado correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al actualizar el registro de mantenimiento']];
        }
    }

    // Eliminar mantenimiento
    public function eliminar($id)
    {
        if ($this->mantenimientoService->eliminar($id)) {
            return ['exito' => true, 'mensaje' => 'Registro de mantenimiento eliminado correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al eliminar el registro de mantenimiento']];
        }
    }

    // Validar datos de mantenimiento
    private function validarDatosMantenimiento($datos, $esActualizacion = false)
    {
        $errores = [];

        // Validar ID en caso de actualización
        if ($esActualizacion && (!isset($datos['id_registro']) || empty($datos['id_registro']))) {
            $errores[] = 'El ID del registro de mantenimiento es requerido';
        }

        // Validar ID del vehículo
        if (!isset($datos['id_vehiculo']) || empty($datos['id_vehiculo'])) {
            $errores[] = 'El vehículo es requerido';
        }

        // Validar tipo de mantenimiento
        if (!isset($datos['tipo_mantenimiento']) || empty($datos['tipo_mantenimiento'])) {
            $errores[] = 'El tipo de mantenimiento es requerido';
        }

        // Validar costo
        if (!isset($datos['costo']) || $datos['costo'] === '') {
            $errores[] = 'El costo es requerido';
        } elseif ($datos['costo'] < 0) {
            $errores[] = 'El costo no puede ser negativo';
        }

        // Validar fecha
        if (!isset($datos['fecha']) || empty($datos['fecha'])) {
            $errores[] = 'La fecha es requerida';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $datos['fecha'])) {
            $errores[] = 'El formato de fecha debe ser YYYY-MM-DD';
        }

        // Validar archivo PDF si se ha subido
        if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] != 4) { // 4 = UPLOAD_ERR_NO_FILE
            if ($_FILES['archivo_pdf']['error'] != 0) {
                $errores[] = 'Error al subir el archivo PDF';
            } else if ($_FILES['archivo_pdf']['type'] != 'application/pdf') {
                $errores[] = 'El archivo debe ser de tipo PDF';
            }
        }
        // Validar imágenes si se han subido
        if (isset($_FILES['imagen_antes']) && $_FILES['imagen_antes']['error'] != 4) { // 4 = UPLOAD_ERR_NO_FILE
            if ($_FILES['imagen_antes']['error'] != 0) {
                $errores[] = 'Error al subir la imagen antes';
            }
        }
        if (isset($_FILES['imagen_despues']) && $_FILES['imagen_despues']['error'] != 4) { // 4 = UPLOAD_ERR_NO_FILE
            if ($_FILES['imagen_despues']['error'] != 0) {
                $errores[] = 'Error al subir la imagen después';
            }
        }

        return $errores;
    }

    // Método para manejar la creación de mantenimiento desde la solicitud HTTP
    private function crearMantenimiento()
    {
        // Verificar si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
            return;
        }

        // Obtener y validar datos
        $datos = [
            'id_vehiculo' => isset($_POST['id_vehiculo']) ? $_POST['id_vehiculo'] : '',
            'tipo_mantenimiento' => isset($_POST['tipo_mantenimiento']) ? $_POST['tipo_mantenimiento'] : '',
            'costo' => isset($_POST['costo']) ? $_POST['costo'] : '',
            'fecha' => isset($_POST['fecha']) ? $_POST['fecha'] : ''
        ];

        // Procesar imágenes si existen
        if (isset($_FILES['imagen_antes']) && $_FILES['imagen_antes']['error'] == 0) {
            // Obtener la placa del vehículo para nombrar la imagen
            $vehiculo = $this->obtenerVehiculoParaImagen($datos['id_vehiculo']);
            $placa = $vehiculo ? $vehiculo->getPlaca() : 'sin_placa';

            // Crear directorio si no existe
            $directorio = '../../soportes/mantenimientos/antes/';
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            // Generar nombre único para la imagen
            $extension = pathinfo($_FILES['imagen_antes']['name'], PATHINFO_EXTENSION);
            $nombreImagen = $placa . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
            $rutaImagen = $directorio . $nombreImagen;

            // Mover la imagen subida al directorio de destino
            if (move_uploaded_file($_FILES['imagen_antes']['tmp_name'], $rutaImagen)) {
                $datos['imagen_antes'] = $nombreImagen;
            }
        }
        if (isset($_FILES['imagen_despues']) && $_FILES['imagen_despues']['error'] == 0) {
            // Obtener la placa del vehículo para nombrar la imagen
            $vehiculo = $this->obtenerVehiculoParaImagen($datos['id_vehiculo']);
            $placa = $vehiculo ? $vehiculo->getPlaca() : 'sin_placa';

            // Crear directorio si no existe
            $directorio = '../../soportes/mantenimientos/despues/';
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            // Generar nombre único para la imagen
            $extension = pathinfo($_FILES['imagen_despues']['name'], PATHINFO_EXTENSION);
            $nombreImagen = $placa . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
            $rutaImagen = $directorio . $nombreImagen;

            // Mover la imagen subida al directorio de destino  
            if (move_uploaded_file($_FILES['imagen_despues']['tmp_name'], $rutaImagen)) {
                $datos['imagen_despues'] = $nombreImagen;
            }
        }

        // Procesar archivo PDF si existe
        if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] == 0) {
            // Obtener la placa del vehículo para nombrar el archivo
            $vehiculo = $this->obtenerVehiculoParaArchivo($datos['id_vehiculo']);
            $placa = $vehiculo ? $vehiculo->getPlaca() : 'sin_placa';

            // Crear directorio si no existe
            $directorio = '../../soportes/mantenimientos/pdf/';
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            // Generar nombre único para el archivo
            $extension = pathinfo($_FILES['archivo_pdf']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = $placa . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
            $rutaArchivo = $directorio . $nombreArchivo;

            // Mover el archivo subido al directorio de destino
            if (move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $rutaArchivo)) {
                $datos['archivo_pdf'] = $nombreArchivo;
            }
        }

        // Crear mantenimiento
        $resultado = $this->crear($datos);
        echo json_encode($resultado);
    }

    // Método para manejar la actualización de mantenimiento desde la solicitud HTTP
    private function actualizarMantenimiento()
    {
        // Verificar si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
            return;
        }

        // Obtener y validar datos
        $datos = [
            'id_registro' => isset($_POST['id_registro']) ? $_POST['id_registro'] : '',
            'id_vehiculo' => isset($_POST['id_vehiculo']) ? $_POST['id_vehiculo'] : '',
            'tipo_mantenimiento' => isset($_POST['tipo_mantenimiento']) ? $_POST['tipo_mantenimiento'] : '',
            'costo' => isset($_POST['costo']) ? $_POST['costo'] : '',
            'fecha' => isset($_POST['fecha']) ? $_POST['fecha'] : ''
        ];

        if (isset($_FILES['imagen_antes']) && $_FILES['imagen_antes']['error'] == 0) {
            $vehiculo = $this->obtenerVehiculoParaImagen($datos['id_vehiculo']);
            $placa = $vehiculo ? $vehiculo->getPlaca() : 'sin_placa';

            $imagenAntesActual = $_POST['imagen_antes_actual'] ?? '';
            if (!empty($imagenAntesActual)) {
                $rutaImagenAnterior = '../../soportes/mantenimientos/antes/' . $imagenAntesActual;
                if (file_exists($rutaImagenAnterior)) unlink($rutaImagenAnterior);
            }

            $directorio = '../../soportes/mantenimientos/antes/';
            if (!file_exists($directorio)) mkdir($directorio, 0777, true);

            $extension = pathinfo($_FILES['imagen_antes']['name'], PATHINFO_EXTENSION);
            $nombreImagen = $placa . '_' . date('Ymd_His') . '.' . $extension;
            $rutaImagen = $directorio . $nombreImagen;

            if (move_uploaded_file($_FILES['imagen_antes']['tmp_name'], $rutaImagen)) {
                $datos['imagen_antes'] = $nombreImagen;
            }
        } else {
            // Mantener imagen anterior
            $datos['imagen_antes'] = $_POST['imagen_antes_actual'] ?? null;
        }

        if (isset($_FILES['imagen_despues']) && $_FILES['imagen_despues']['error'] == 0) {
            $vehiculo = $this->obtenerVehiculoParaImagen($datos['id_vehiculo']);
            $placa = $vehiculo ? $vehiculo->getPlaca() : 'sin_placa';

            $imagenDespuesActual = $_POST['imagen_despues_actual'] ?? '';
            if (!empty($imagenDespuesActual)) {
                $rutaImagenAnterior = '../../soportes/mantenimientos/despues/' . $imagenDespuesActual;
                if (file_exists($rutaImagenAnterior)) unlink($rutaImagenAnterior);
            }

            $directorio = '../../soportes/mantenimientos/despues/';
            if (!file_exists($directorio)) mkdir($directorio, 0777, true);

            $extension = pathinfo($_FILES['imagen_despues']['name'], PATHINFO_EXTENSION);
            $nombreImagen = $placa . '_' . date('Ymd_His') . '.' . $extension;
            $rutaImagen = $directorio . $nombreImagen;

            if (move_uploaded_file($_FILES['imagen_despues']['tmp_name'], $rutaImagen)) {
                $datos['imagen_despues'] = $nombreImagen;
            }
        } else {
            // Mantener imagen anterior
            $datos['imagen_despues'] = $_POST['imagen_despues_actual'] ?? null;
        }

        // Procesar archivo PDF si existe
        if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] == 0) {
            $vehiculo = $this->obtenerVehiculoParaArchivo($datos['id_vehiculo']);
            $placa = $vehiculo ? $vehiculo->getPlaca() : 'sin_placa';

            $pdfActual = $_POST['archivo_pdf_actual'] ?? '';
            if (!empty($pdfActual)) {
                $rutaArchivoAnterior = '../../soportes/mantenimientos/pdf/' . $pdfActual;
                if (file_exists($rutaArchivoAnterior)) unlink($rutaArchivoAnterior);
            }

            $directorio = '../../soportes/mantenimientos/pdf/';
            if (!file_exists($directorio)) mkdir($directorio, 0777, true);

            $extension = pathinfo($_FILES['archivo_pdf']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = $placa . '_' . date('Ymd_His') . '.' . $extension;
            $rutaArchivo = $directorio . $nombreArchivo;

            if (move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $rutaArchivo)) {
                $datos['archivo_pdf'] = $nombreArchivo;
            }
        } else {
            // Mantener PDF anterior
            $datos['archivo_pdf'] = $_POST['archivo_pdf_actual'] ?? null;
        }
        // Actualizar mantenimiento
        $resultado = $this->actualizar($datos);
        echo json_encode($resultado);
    }

    // Método para manejar la eliminación de mantenimiento desde la solicitud HTTP
    private function eliminarMantenimiento()
    {
        // Verificar si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
            return;
        }

        // Obtener ID
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if (empty($id)) {
            echo json_encode(['exito' => false, 'errores' => ['ID de mantenimiento requerido']]);
            return;
        }

        // Eliminar mantenimiento
        $resultado = $this->eliminar($id);
        echo json_encode($resultado);
    }

    // Método para obtener mantenimiento por ID desde la solicitud HTTP
    private function obtenerMantenimientoPorId()
    {
        // Verificar si es una solicitud GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
            return;
        }

        // Obtener ID
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        if (empty($id)) {
            echo json_encode(['exito' => false, 'errores' => ['ID de mantenimiento requerido']]);
            return;
        }

        // Obtener mantenimiento
        $mantenimiento = $this->obtenerPorId($id);
        if ($mantenimiento) {
            echo json_encode([
                'exito' => true,
                'mantenimiento' => [
                    'id' => $mantenimiento->getId(),
                    'id_vehiculo' => $mantenimiento->getIdVehiculo(),
                    'placa_vehiculo' => $mantenimiento->getPlacaVehiculo(),
                    'tipo_mantenimiento' => $mantenimiento->getTipoMantenimiento(),
                    'costo' => $mantenimiento->getCosto(),
                    'fecha' => $mantenimiento->getFecha(),
                    'imagen_antes' => $mantenimiento->getImagenAntes(),
                    'imagen_despues' => $mantenimiento->getImagenDespues(),
                    'archivo_pdf' => $mantenimiento->getArchivoPdf()
                ]
            ]);
        } else {
            echo json_encode(['exito' => false, 'errores' => ['Mantenimiento no encontrado']]);
        }
    }

    /*/ Método para obtener mantenimientos por vehículo desde la solicitud HTTP
    private function obtenerMantenimientosPorVehiculo() {
        // Verificar si es una solicitud GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['exito' => false, 'errores' => ['Método no permitido']]);
            return;
        }
        
        // Obtener ID del vehículo
        $id_vehiculo = isset($_GET['id_vehiculo']) ? $_GET['id_vehiculo'] : '';
        if (empty($id_vehiculo)) {
            echo json_encode(['exito' => false, 'errores' => ['ID de vehículo requerido']]);
            return;
        }
        
        // Obtener mantenimientos
        $mantenimientos = $this->obtenerPorVehiculo($id_vehiculo);
        $resultado = [];
        
        foreach ($mantenimientos as $mantenimiento) {
            $resultado[] = [
                'id' => $mantenimiento->getId(),
                'id_vehiculo' => $mantenimiento->getIdVehiculo(),
                'placa_vehiculo' => $mantenimiento->getPlacaVehiculo(),
                'tipo_mantenimiento' => $mantenimiento->getTipoMantenimiento(),
                'costo' => $mantenimiento->getCosto(),
                'fecha' => $mantenimiento->getFecha(),
                'archivo_pdf' => $mantenimiento->getArchivoPdf()
            ];
        }
        
        echo json_encode(['exito' => true, 'mantenimientos' => $resultado]);
    }
    */

    // Método para obtener el vehículo y su placa para nombrar el archivo

    private function obtenerVehiculoParaArchivo($id_vehiculo)
    {
        require_once __DIR__ . '/VehiculoController.php';
        $vehiculoController = new VehiculoController();
        return $vehiculoController->obtenerPorId($id_vehiculo);
    }
    // Método para obtener el vehículo y su placa para nombrar la imagen
    private function obtenerVehiculoParaImagen($id_vehiculo)
    {
        require_once __DIR__ . '/VehiculoController.php';
        $vehiculoController = new VehiculoController();
        return $vehiculoController->obtenerPorId($id_vehiculo);
    }
}

// Manejar solicitudes si se accede directamente al controlador
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    $controller = new MantenimientoController();
    $controller->handleRequest();
}

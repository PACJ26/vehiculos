<?php
require_once __DIR__ . '/../services/PropietarioService.php';

class PropietarioController {
    private $propietarioService;
    
    // Constructor
    public function __construct() {
        $this->propietarioService = new PropietarioService();
        
        // Si es una petición AJAX para obtener un propietario por ID
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtenerPorId' && isset($_GET['id'])) {
            $propietario = $this->obtenerPorId($_GET['id']);
            
            if ($propietario) {
                $datos = [
                    'id' => $propietario->getId(),
                    'nombre' => $propietario->getNombre(),
                    'apellido' => $propietario->getApellido(),
                    'documento' => $propietario->getDocumento(),
                    'telefono' => $propietario->getTelefono(),
                    'correo' => $propietario->getCorreo(),
                    'estado' => $propietario->getEstado()
                ];
                
                header('Content-Type: application/json');
                echo json_encode($datos);
                exit;
            }
        }
    }
    
    // Obtener todos los propietarios para mostrar en la tabla
    public function obtenerTodos() {
        return $this->propietarioService->obtenerTodos();
    }
    
    // Obtener un propietario por su ID
    public function obtenerPorId($id) {
        return $this->propietarioService->obtenerPorId($id);
    }
    
    // Crear un nuevo propietario
    public function crear($datos) {
        // Validar datos
        $errores = $this->validarDatos($datos);
        if(!empty($errores)) {
            return ['exito' => false, 'errores' => $errores, 'tipo' => 'error'];
        }

        if ($this->propietarioService->existeDocumentoOCorreo($datos['documento'],$datos['correo'])) {
            return ['exito' => false, 'errores' => ['El documento o correo ya existen'], 'tipo' => 'error'];
        }
        
        // Crear objeto Propietario
        $propietario = new Propietario(
            null,
            $datos['nombre'],
            $datos['apellido'],
            $datos['documento'],
            $datos['telefono'],
            $datos['correo'],
            $datos['estado']
        );
        
        // Guardar en la base de datos
        $resultado = $this->propietarioService->crear($propietario);
        
        if ($resultado) {
            return ['exito' => true, 'mensaje' => 'Propietario creado correctamente', 'tipo' => 'success'];
        } 
        else {
            return ['exito' => false, 'errores' => ['Error al crear el propietario'], 'tipo' => 'error'];
        }
    }
    
    // Actualizar un propietario existente
    public function actualizar($id, $datos) {
        // Validar datos
        $errores = $this->validarDatos($datos);
        if(!empty($errores)) {
            return ['exito' => false, 'errores' => $errores, 'tipo' => 'error'];
        } 
        // Crear objeto Propietario
        $propietario = new Propietario(
            $id,
            $datos['nombre'],
            $datos['apellido'],
            $datos['documento'],
            $datos['telefono'],
            $datos['correo'],
            $datos['estado']
        );
        
        // Actualizar en la base de datos
        $resultado = $this->propietarioService->actualizar($propietario);
        
        if ($resultado) {
            return ['exito' => true, 'mensaje' => 'Propietario actualizado correctamente', 'tipo' => 'success'];
        } else {
            return ['exito' => false, 'errores' => ['Error al actualizar el propietario'], 'tipo' => 'error'];
        }
    }
    
    // Eliminar un propietario
    public function eliminar($id) {
        $resultado = $this->propietarioService->eliminar($id);
        
        if ($resultado) {
            return ['exito' => true, 'mensaje' => 'Propietario eliminado correctamente', 'tipo' => 'success'];
        } else {
            return ['exito' => false, 'errores' => ['Error al eliminar el propietario'], 'tipo' => 'error'];
        }
    }
    // validar datos
    private function validarDatos($datos) {
        $errores = [];
    
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es obligatorio';
        }  
        if (empty($datos['apellido'])) {
            $errores[] = 'El apellido es obligatorio';
        }
    
        if (empty($datos['documento'])) {
            $errores[] = 'El documento es obligatorio';
        } elseif (!ctype_digit($datos['documento']) || intval($datos['documento']) <= 0) {
            $errores[] = 'El documento no es valido';
        }
    
        if (empty($datos['telefono'])) {
            $errores[] = 'El teléfono es obligatorio'; 
        } elseif (!ctype_digit($datos['telefono']) || intval($datos['telefono']) <= 0) {
            $errores[] = 'El teléfono no es valido';
        }
    
        if (empty($datos['correo'])) {
            $errores[] = 'El correo es obligatorio';
        } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido';
        }
    
        if (empty($datos['estado'])) {
            $errores[] = 'El estado es obligatorio';
        }
    
        return $errores;
    }
    
    // Buscar propietarios
    public function buscar($termino) {
        return $this->propietarioService->buscar($termino);
    }
}
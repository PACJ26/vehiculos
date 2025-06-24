<?php
require_once __DIR__ . '/../services/VehiculoService.php';

class VehiculoController
{
    private $vehiculoService;

    public function __construct()
    {
        $this->vehiculoService = new VehiculoService();
    }

    // Obtener todos los vehículos para mostrar en la tabla
    public function obtenerTodos()
    {
        return $this->vehiculoService->obtenerTodos();
    }

    // Obtener un vehículo por su ID
    public function obtenerPorId($id)
    {
        return $this->vehiculoService->obtenerPorId($id);
    }

    // Crear un nuevo vehículo
    public function crear($datos)
    {
        // Validar que la placa no exista
        if ($this->vehiculoService->existePlaca($datos['placa'])) {
            return ['exito' => false, 'errores' => ['La placa ya está registrada en el sistema']];
        }

        // Procesar imagen si se ha subido
        $imagen_url = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagen_url = $this->procesarImagen($_FILES['imagen'], $_POST['placa']);
            if (!$imagen_url) {
                return ['exito' => false, 'errores' => ['Error al procesar la imagen']];
            }
        }

        // Crear objeto Vehiculo
        $vehiculo = new Vehiculo(
            null,
            $datos['placa'],
            $datos['clase'],
            $datos['marca'],
            $datos['linea'],
            $datos['modelo'],
            $datos['color'],
            $imagen_url,
            $datos['id_propietario'],
            $datos['estado']
        );

        // Guardar en la base de datos
        $resultado = $this->vehiculoService->crear($vehiculo);

        if ($resultado) {
            return ['exito' => true, 'mensaje' => 'Vehículo creado correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al crear el vehículo']];
        }
    }

    // Actualizar un vehículo existente
    public function actualizar($id, $datos)
    {
        // Validar que la placa no exista (excepto para este vehículo)
        if ($this->vehiculoService->existePlaca($datos['placa'], $id)) {
            return ['exito' => false, 'errores' => ['La placa ya está registrada en otro vehículo']];
        }

        // Obtener vehículo actual para mantener la imagen si no se sube una nueva
        $vehiculoActual = $this->vehiculoService->obtenerPorId($id);
        $imagen_url = $vehiculoActual->getImagenUrl();

        // Procesar nueva imagen si se ha subido
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nueva_imagen_url = $this->procesarImagen($_FILES['imagen'], $_POST['placa']);
            if ($nueva_imagen_url) {
                // Eliminar imagen anterior si existe
                if ($imagen_url && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagen_url)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $imagen_url);
                }
                $imagen_url = $nueva_imagen_url;
            }
        }

        // Crear objeto Vehiculo
        $vehiculo = new Vehiculo(
            $id,
            $datos['placa'],
            $datos['clase'],
            $datos['marca'],
            $datos['linea'],
            $datos['modelo'],
            $datos['color'],
            $imagen_url,
            $datos['id_propietario'],
            $datos['estado']
        );

        // Actualizar en la base de datos
        $resultado = $this->vehiculoService->actualizar($vehiculo);

        if ($resultado) {
            return ['exito' => true, 'mensaje' => 'Vehículo actualizado correctamente'];
        } else {
            return ['exito' => false, 'errores' => ['Error al actualizar el vehículo']];
        }
    }

    // Eliminar un vehículo
    public function eliminar($id)
    {
        // Obtener vehículo para eliminar su imagen
        $vehiculo = $this->vehiculoService->obtenerPorId($id);

        if (!$vehiculo) {
            return ['exito' => false, 'errores' => ['Vehículo no encontrado']];
        }

        $imagen_url = $vehiculo->getImagenUrl();

        // Verificar si el vehículo tiene propietario asignado
        if ($vehiculo->getIdPropietario()) {
            return ['exito' => false, 'errores' => ['No se puede eliminar un vehículo con propietario asignado']];
        }
        
        // Llamar al servicio para eliminar el vehículo (incluye verificaciones de registros asociados)
        $resultado = $this->vehiculoService->eliminar($id);

        // Si la eliminación fue exitosa, eliminar la imagen
        if ($resultado['exito']) {
            // Eliminar imagen si existe
            if ($imagen_url && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagen_url)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $imagen_url);
            }
        }
        
        return $resultado;
    }

    // Buscar vehículos
    public function buscar($termino)
    {
        return $this->vehiculoService->buscar($termino);
    }

    // Obtener propietario de un vehículo
    public function obtenerPropietario($id_propietario)
    {
        return $this->vehiculoService->obtenerPropietario($id_propietario);
    }

    // Obtener todos los propietarios activos para el select
    public function obtenerPropietariosActivos()
    {
        return $this->vehiculoService->obtenerPropietariosActivos();
    }

    // Procesar imagen subida
   private function procesarImagen($archivo, $placa) {
    $directorio_relativo = '/Modulo_Vehiculos_v2/Public/img/vehiculos/';
    $directorio_absoluto = $_SERVER['DOCUMENT_ROOT'] . $directorio_relativo;

    // Crear directorio si no existe
    if (!file_exists($directorio_absoluto)) {
        mkdir($directorio_absoluto, 0777, true);
    }

    // Limpiar placa (eliminar caracteres no válidos para nombres de archivo)
    $placa_limpia = preg_replace('/[^a-zA-Z0-9_-]/', '', $placa);

    // Obtener extensión del archivo
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);

    // Generar nombre con la placa
    $nombre_archivo = 'vehiculo_' . strtoupper($placa_limpia) . '.' . $extension;

    $ruta_archivo = $directorio_absoluto . $nombre_archivo;

    // Mover archivo subido
    if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
        return $directorio_relativo . $nombre_archivo; // Ruta para guardar en la base de datos
    }

    return null;
}

}

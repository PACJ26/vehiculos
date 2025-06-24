<?php
require_once __DIR__ . '/../../app/controllers/PropietarioController.php';

// Verificar que se recibió el ID del propietario
if (isset($_GET['id_propietario']) && !empty($_GET['id_propietario'])) {
    $id_propietario = $_GET['id_propietario'];

    // Crear instancia del controlador
    $propietarioController = new PropietarioController();

    // Obtener el propietario por ID
    $propietario = $propietarioController->obtenerPorId($id_propietario);

    // Verificar si se encontró el propietario
    if ($propietario) {
        // Preparar datos para respuesta JSON
        $datos = [
            'id' => $propietario->getId(),
            'nombre' => $propietario->getNombre(),
            'apellido' => $propietario->getApellido(),
            'documento' => $propietario->getDocumento(),
            'telefono' => $propietario->getTelefono(),
            'correo' => $propietario->getCorreo(),
            'estado' => $propietario->getEstado()
        ];

        // Enviar respuesta JSON
        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }
}

// Si no se encontró el propietario o no se proporcionó ID, devolver respuesta vacía
header('Content-Type: application/json');
echo json_encode(null);

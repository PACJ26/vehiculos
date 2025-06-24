<?php

require_once __DIR__ . '/../../app/controllers/VehiculoController.php';
require_once __DIR__ . '/../../app/controllers/PropietarioController.php';

$vehiculoController = new VehiculoController();
$propietarioController = new PropietarioController();

// Procesar formulario de creación/edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        $datos = [
            'placa' => $_POST['placa'],
            'clase' => $_POST['clase'],
            'marca' => $_POST['marca'],
            'linea' => $_POST['linea'],
            'modelo' => $_POST['modelo'],
            'color' => $_POST['color'],
            'id_propietario' => empty($_POST['id_propietario']) ? null : $_POST['id_propietario'],
            'estado' => $_POST['estado']
        ];

        if ($_POST['accion'] === 'crear') {
            $resultado = $vehiculoController->crear($datos);
            $_SESSION['resultado'] = $resultado;
            
            // Si hay errores, guardar los datos en la sesión para recuperarlos
            if (!$resultado['exito']) {
                $_SESSION['datos_formulario'] = $datos;
                $_SESSION['accion_formulario'] = 'crear';
            } else {
                // Si fue exitoso, limpiar los datos del formulario
                unset($_SESSION['datos_formulario']);
                unset($_SESSION['accion_formulario']);
            }
            
            header('Location: vehiculos.php');
            exit;
        } elseif ($_POST['accion'] === 'editar' && isset($_POST['id_vehiculo'])) {
            $resultado = $vehiculoController->actualizar($_POST['id_vehiculo'], $datos);
            $_SESSION['resultado'] = $resultado;
            
            // Si hay errores, guardar los datos en la sesión para recuperarlos
            if (!$resultado['exito']) {
                $_SESSION['datos_formulario'] = $datos;
                $_SESSION['accion_formulario'] = 'editar';
                $_SESSION['id_vehiculo'] = $_POST['id_vehiculo'];
            } else {
                // Si fue exitoso, limpiar los datos del formulario
                unset($_SESSION['datos_formulario']);
                unset($_SESSION['accion_formulario']);
                unset($_SESSION['id_vehiculo']);
            }
            
            header('Location: vehiculos.php');
            exit;
        }
    } elseif (isset($_POST['eliminar']) && isset($_POST['id_vehiculo'])) {
        $resultado = $vehiculoController->eliminar($_POST['id_vehiculo']);
        $_SESSION['resultado'] = $resultado;
        header('Location: vehiculos.php');
        exit;
    }
}

// Obtener vehículos para la tabla
$vehiculos = $vehiculoController->obtenerTodos();

// Obtener propietarios para el select
$propietarios = $vehiculoController->obtenerPropietariosActivos();

// Buscar vehículos
if (isset($_GET['buscar']) && !empty($_GET['termino'])) {
    $vehiculos = $vehiculoController->buscar($_GET['termino']);
}

?>
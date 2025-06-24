<?php
    require_once __DIR__ . '/../../app/controllers/PropietarioController.php';

    $propietarioController = new PropietarioController();
    
    // Procesar formulario de creación/edición
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['accion'])) {
            $datos = [
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'documento' => $_POST['documento'],
                'telefono' => $_POST['telefono'],
                'correo' => $_POST['correo'],
                'estado' => $_POST['estado']
            ];
    
            if ($_POST['accion'] === 'crear') {
                $resultado = $propietarioController->crear($datos);
                // Asegurar que el resultado tenga el campo 'tipo' para iziToast
                if (!isset($resultado['tipo'])) {
                    $resultado['tipo'] = $resultado['exito'] ? 'success' : 'error';
                }
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
    
                header('Location: propietarios.php');
                exit;
            } elseif ($_POST['accion'] === 'editar' && isset($_POST['id_propietario'])) {
                $resultado = $propietarioController->actualizar($_POST['id_propietario'], $datos);
                // Asegurar que el resultado tenga el campo 'tipo' para iziToast
                if (!isset($resultado['tipo'])) {
                    $resultado['tipo'] = $resultado['exito'] ? 'success' : 'error';
                }
                $_SESSION['resultado'] = $resultado;
    
                // Si hay errores, guardar los datos en la sesión para recuperarlos
                if (!$resultado['exito']) {
                    $_SESSION['datos_formulario'] = $datos;
                    $_SESSION['accion_formulario'] = 'editar';
                    $_SESSION['id_propietario'] = $_POST['id_propietario'];
                } else {
                    // Si fue exitoso, limpiar los datos del formulario
                    unset($_SESSION['datos_formulario']);
                    unset($_SESSION['accion_formulario']);
                    unset($_SESSION['id_propietario']);
                }
    
                header('Location: propietarios.php');
                exit;
            }
        } elseif (isset($_POST['eliminar']) && isset($_POST['id_propietario'])) {
            $resultado = $propietarioController->eliminar($_POST['id_propietario']);
            // Asegurar que el resultado tenga el campo 'tipo' para iziToast
            if (!isset($resultado['tipo'])) {
                $resultado['tipo'] = $resultado['exito'] ? 'success' : 'error';
            }
            $_SESSION['resultado'] = $resultado;
            header('Location: propietarios.php');
            exit;
        }
    }
    
    // Obtener propietarios para la tabla
    $propietarios = $propietarioController->obtenerTodos();
    
    // Buscar propietarios
    if (isset($_GET['buscar']) && !empty($_GET['termino'])) {
        $propietarios = $propietarioController->buscar($_GET['termino']);
    }
?>
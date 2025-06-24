<?php
require_once '../../app/controllers/MultaController.php';
require_once '../../app/controllers/pagosMultasController.php';
require_once '../../app/controllers/VehiculoController.php';

$multaController = new MultaController();
$pagosMultasController = new PagosMultasController();
$vehiculoController = new VehiculoController();

// Obtener todas las multas
$multas = $multaController->obtenerTodas();

// Obtener todos los vehículos para el formulario
$vehiculos = $vehiculoController->obtenerTodos();
?>
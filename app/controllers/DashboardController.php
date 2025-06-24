<?php
require_once __DIR__ . '/../../database/conexion.php';

function obtenerEstadisticasSistema() {
    global $conexion;
    
    // Consultas para obtener los conteos
    $sqlVehiculos = "SELECT COUNT(*) as total FROM vehiculos";
    $sqlPropietarios = "SELECT COUNT(*) as total FROM propietarios";
    $sqlSeguros = "SELECT COUNT(*) as total FROM seguros";
    $sqlMantenimientos = "SELECT COUNT(*) as total FROM registros_mantenimiento";
    
    // Ejecutar consultas
    $resultVehiculos = $conexion->query($sqlVehiculos);
    $resultPropietarios = $conexion->query($sqlPropietarios);
    $resultSeguros = $conexion->query($sqlSeguros);
    $resultMantenimientos = $conexion->query($sqlMantenimientos);
    
    // Obtener resultados
    return [
        'vehiculos' => ($resultVehiculos) ? $resultVehiculos->fetch_assoc()['total'] : 0,
        'propietarios' => ($resultPropietarios) ? $resultPropietarios->fetch_assoc()['total'] : 0,
        'seguros' => ($resultSeguros) ? $resultSeguros->fetch_assoc()['total'] : 0,
        'mantenimientos' => ($resultMantenimientos) ? $resultMantenimientos->fetch_assoc()['total'] : 0
    ];
}
<?php
require_once '../../app/controllers/DashboardController.php';
$estadisticas = obtenerEstadisticasSistema();

$totalVehiculos = $estadisticas['vehiculos'];
$totalPropietarios = $estadisticas['propietarios'];
$totalSeguros = $estadisticas['seguros'];
$totalMantenimientos = $estadisticas['mantenimientos'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Gestión de Vehículos</title>
    <link rel="icon" href="../icono/nexus-logo.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <!-- Cabecera del Dashboard -->
    <header class="dashboard-header">
        <div class="container">
            <h1 class="h3">Panel de Control</h1>
            <p class="text-muted">Bienvenido al sistema de gestión de vehículos</p>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="container">
        <!-- Contenedor para notificaciones -->
        <div class="toast-container position-fixed top-0 end-0 p-3"></div>

        <!-- Tarjetas de módulos -->
        <div class="row">

            <!-- Módulo de Propietarios -->
            <div class="col-md-4">
                <div class="card module-card propietarios-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users module-icon"></i>
                        <h5 class="card-title">Propietarios</h5>
                        <p class="card-text">Administra la información de propietarios y sus vehículos asociados.</p>
                        <a href="propietarios.php" class="btn" style="background-color: #E12F25; color: white;">
                            <i class="fas fa-arrow-right me-1"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Vehículos -->
            <div class="col-md-4">
                <div class="card module-card vehiculos-card">
                    <div class="card-body text-center">
                        <i class="fas fa-car module-icon"></i>
                        <h5 class="card-title">Vehículos</h5>
                        <p class="card-text">Gestiona el inventario de vehículos, consulta información detallada y actualiza registros.</p>
                        <a href="vehiculos.php" class="btn" style="background-color: #023373; color: white;">
                            <i class="fas fa-arrow-right me-1"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Seguros -->
            <div class="col-md-4">
                <div class="card module-card seguros-card">
                    <div class="card-body text-center">
                        <i class="fas fa-shield-alt module-icon"></i>
                        <h5 class="card-title">Seguros</h5>
                        <p class="card-text">Gestiona pólizas de seguros, fechas de vencimiento y coberturas.</p>
                        <a href="seguros.php" class="btn" style="background-color: #F2A71B; color: white;">
                            <i class="fas fa-arrow-right me-1"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Mantenimiento -->
            <div class="col-md-4">
                <div class="card module-card mantenimiento-card">
                    <div class="card-body text-center">
                        <i class="fas fa-tools module-icon"></i>
                        <h5 class="card-title">Mantenimiento</h5>
                        <p class="card-text">Programa y registra mantenimientos preventivos y correctivos de los vehículos.</p>
                        <a href="mantenimiento.php" class="btn" style="background-color: #F26E22; color: white;">
                            <i class="fas fa-arrow-right me-1"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Multas -->
            <div class="col-md-4">
                <div class="card module-card multas-card">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle module-icon"></i>
                        <h5 class="card-title">Multas</h5>
                        <p class="card-text">Registra y gestiona multas e infracciones asociadas a los vehículos.</p>
                        <a href="multas.php" class="btn" style="background-color: #2c3e50; color: white;">
                            <i class="fas fa-arrow-right me-1"></i> Acceder
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de estadísticas -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Resumen del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-primary"><?php echo $totalVehiculos; ?></h3>
                                    <p class="mb-0 text-muted">Vehículos</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-success"><?php echo $totalPropietarios; ?></h3>
                                    <p class="mb-0 text-muted">Propietarios</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-warning"><?php echo $totalSeguros; ?></h3>
                                    <p class="mb-0 text-muted">Seguros</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-danger"><?php echo $totalMantenimientos; ?></h3>
                                    <p class="mb-0 text-muted">Mantenimientos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
</body>

</html>
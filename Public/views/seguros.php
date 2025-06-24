<?php
session_start();
require_once '../../app/controllers/SeguroController.php';
require_once '../../app/controllers/VehiculoController.php';

$seguroController = new SeguroController();
$vehiculoController = new VehiculoController();

// Actualizar días restantes y estados automáticamente
$seguroController->actualizarDiasRestantes();

// Obtener todos los seguros
$seguros = $seguroController->obtenerTodos();

// Obtener todos los vehículos para el formulario
$vehiculos = $vehiculoController->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Seguros</title>
    <link rel="icon" href="../icono/nexus-logo.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/seguro.css">
    <!-- iziToast CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">

</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- meunu sidebar -->
            <?php require_once 'sidebar.php'; ?>
            <!-- Contenido principal -->
            <div class="col-md-10 content-area">
                <div class="container py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-file-contract text-primary me-2"></i> Gestión de Seguros</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSeguro" onclick="prepararNuevoSeguro()">
                            <i class="fas fa-plus me-1"></i> Nuevo Seguro
                        </button>
                    </div>
                    <!-- Los mensajes ahora se mostrarán con iziToast -->
                    <div id="mensajes-container" style="display: none;" data-mensajes="<?php
                        if (isset($_SESSION['resultado'])) {
                            echo htmlspecialchars(json_encode($_SESSION['resultado']), ENT_QUOTES, 'UTF-8');
                                unset($_SESSION['resultado']);
                            }
                     ?>"></div>
                    <!-- Tabla de seguros -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablaSeguros" class="table table-striped table-hover">
                                <thead class="table" style="background: #023373; color: white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Vehículo</th>
                                            <th>Aseguradora</th>
                                            <th>Tipo Póliza</th>
                                            <th>Costo</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Días Restantes</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($seguros as $seguro): ?>
                                            <tr>
                                                <td><?= $seguro->getId() ?></td>
                                                <td><?= $seguro->getPlacaVehiculo() ?></td>
                                                <td><?= $seguro->getAseguradora() ?></td>
                                                <td><?= $seguro->getTipoPoliza() ?></td>
                                                <td>$<?= number_format($seguro->getCosto(), 2) ?></td>
                                                <td><?= $seguro->getFechaInicio() ?></td>
                                                <td><?= $seguro->getFechaVencimiento() ?></td>
                                                <td>
                                                    <?php if ($seguro->getDiasRestantes() !== null): ?>
                                                        <?php if ($seguro->getDiasRestantes() <= 5 && $seguro->getDiasRestantes() >= 0): ?>
                                                            <span style="color: red; font-weight: bold;"><?= $seguro->getDiasRestantes() ?></span>
                                                        <?php else: ?>
                                                            <?= $seguro->getDiasRestantes() ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($seguro->getEstado() === 'Vigente'): ?>
                                                        <span class="badge rounded-pill badge-vigente">Vigente</span>
                                                    <?php elseif ($seguro->getEstado() === 'Expirado'): ?>
                                                        <span class="badge rounded-pill badge-expirado">Expirado</span>
                                                    <?php else: ?>
                                                        <span class="badge rounded-pill badge-cancelado">Cancelado</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-nowrap">
                                                        <button type="button" class="btn btn-sm btn-info me-1"
                                                            onclick="verDetalleSeguro(<?= $seguro->getId() ?>)"
                                                            data-bs-toggle="modal" data-bs-target="#modalDetalleSeguro"
                                                            title="Ver detalle">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-primary me-1"
                                                            onclick="cargarSeguroParaEditar(<?= $seguro->getId() ?>)"
                                                            data-bs-toggle="modal" data-bs-target="#modalSeguro"
                                                            title="Editar seguro">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="confirmarEliminarSeguro(<?= $seguro->getId() ?>)"
                                                            title="Eliminar seguro">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar seguro -->
    <div class="modal fade" id="modalSeguro" tabindex="-1" aria-labelledby="modalSeguroLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalSeguroLabel">Nuevo Seguro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSeguro" enctype="multipart/form-data">
                        <input type="hidden" id="id_seguro" name="id_seguro" value="">
                        <input type="hidden" id="accion" name="accion" value="crear">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_vehiculo" class="form-label">Vehículo</label>
                                <select class="form-select" id="id_vehiculo" name="id_vehiculo" required>
                                    <option value="">Seleccione un vehículo</option>
                                    <?php foreach ($vehiculos as $vehiculo): ?>
                                        <option value="<?= $vehiculo->getId() ?>">
                                            <?= $vehiculo->getPlaca() ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="aseguradora" class="form-label">Aseguradora</label>
                                <input type="text" class="form-control" id="aseguradora" name="aseguradora" placeholder="aseguradora" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo_poliza" class="form-label">Tipo de Póliza</label>
                                <input type="text" class="form-control" id="tipo_poliza" name="tipo_poliza" placeholder="poliza" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="costo" class="form-label">Costo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="costo" name="costo" placeholder="1000000" step="0.01" min="0.01" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                                <small class="text-muted">Fecha límite para el pago del seguro</small>
                            </div>

                            <div class="col-md-6 mb-3" id="estado_container" style="display: none;">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="">Seleccione un estado</option>
                                    <option value="Vigente">Vigente</option>
                                    <option value="Expirado">Expirado</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="archivo_pdf" class="form-label">Archivo PDF del Seguro</label>
                                <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" accept=".pdf">
                                <small class="text-muted">Sube el documento PDF del seguro (opcional)</small>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarSeguro" style="background-color: var(--primary-color);">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Detalle de Seguro -->
    <div class="modal fade" id="modalDetalleSeguro" tabindex="-1" aria-labelledby="modalDetalleSeguroLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalDetalleSeguroLabel">Detalle del Seguro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <span id="detalle_id"></span></p>
                            <p><strong>Vehículo:</strong> <span id="detalle_vehiculo"></span></p>
                            <p><strong>Aseguradora:</strong> <span id="detalle_aseguradora"></span></p>
                            <p><strong>Tipo de Póliza:</strong> <span id="detalle_tipo_poliza"></span></p>
                            <p><strong>Costo:</strong> <span id="detalle_costo"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha de Inicio:</strong> <span id="detalle_fecha_inicio"></span></p>
                            <p><strong>Fecha de Vencimiento:</strong> <span id="detalle_fecha_vencimiento"></span></p>
                            <p><strong>Días Restantes:</strong> <span id="detalle_dias_restantes"></span></p>
                            <p><strong>Estado:</strong> <span id="detalle_estado"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- El modal de eliminación ha sido reemplazado por SweetAlert2 -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- iziToast JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="../js/iziToast.js"></script>
    <script src="../js/seguros.js"></script>

    <!-- Script para procesar mensajes con iziToast -->
    <script>
        $(document).ready(function() {
            // Procesar mensajes del servidor
            const mensajesContainer = document.getElementById('mensajes-container');
            if (mensajesContainer && mensajesContainer.dataset.mensajes) {
                try {
                    const resultado = JSON.parse(mensajesContainer.dataset.mensajes);
                    if (resultado.exito) {
                        mostrarNotificacion('success', resultado.mensaje);
                    } else if (resultado.errores && Array.isArray(resultado.errores)) {
                        mostrarErrores(resultado.errores);
                    }
                } catch (error) {
                    console.error('Error al procesar los mensajes:', error);
                }
            }
        });
    </script>
</body>

</html>
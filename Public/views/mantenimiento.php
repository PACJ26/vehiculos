<?php
require_once '../../app/controllers/MantenimientoController.php';
require_once '../../app/controllers/VehiculoController.php';

$mantenimientoController = new MantenimientoController();
$vehiculoController = new VehiculoController();

// Obtener todos los registros de mantenimiento
$mantenimientos = $mantenimientoController->obtenerTodos();

// Obtener todos los vehículos para el formulario
$vehiculos = $vehiculoController->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mantenimientos</title>
    <link rel="icon" href="../icono/nexus-logo.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/styles.css">
    <!-- iziToast CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- menu sidebar -->
            <?php require_once 'sidebar.php'; ?>
            <!-- Contenido principal -->
            <div class="col-md-10 content-area">
                <div class="container py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-tools text-primary me-2"></i> Gestión de Mantenimientos</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoMantenimiento">
                            <i class="fas fa-plus me-1"></i> Nuevo Mantenimiento
                        </button>
                    </div>
                    <!-- Los mensajes ahora se mostrarán con iziToast -->
                    <div id="mensajes-container" style="display: none;" data-mensajes="<?php
                                                                                        if (isset($_SESSION['resultado'])) {
                                                                                            echo htmlspecialchars(json_encode($_SESSION['resultado']), ENT_QUOTES, 'UTF-8');
                                                                                            unset($_SESSION['resultado']);
                                                                                        } ?>">
                    </div>
                    <!-- Tabla de mantenimientos -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablaMantenimientos" class="table table-striped table-hover">
                                    <thead class="table" style="background: #023373; color: white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Vehículo</th>
                                            <th>Tipo de Mantenimiento</th>
                                            <th>Costo</th>
                                            <th>Fecha del Mantenimiento</th>
                                            <th>Imagen antes</th>
                                            <th>Imagen después</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mantenimientos as $mantenimiento): ?>
                                            <tr>
                                                <td><?= $mantenimiento->getId() ?></td>
                                                <td><?= $mantenimiento->getPlacaVehiculo() ?></td>
                                                <td><?= $mantenimiento->getTipoMantenimiento() ?></td>
                                                <td>$<?= number_format($mantenimiento->getCosto(), 2) ?></td>
                                                <td><?= $mantenimiento->getFecha() ?></td>
                                                <td>
                                                    <?php if ($mantenimiento->getImagenAntes()): ?>
                                                        <img src="../../../vehiculos/soportes/mantenimientos/antes/<?=$mantenimiento->getImagenAntes() ?>"
                                                            alt="Imagen del antes"
                                                            class="img-thumbnail"
                                                            style="max-width: 100px; height: auto;"
                                                            onclick="mostrarImagenCompleta('/vehiculos/soportes/mantenimientos/antes/<?= $mantenimiento->getImagenAntes() ?>')">
                                                    <?php else: ?>
                                                        <span class="text-muted">Sin imagen</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($mantenimiento->getImagenDespues()): ?>
                                                        <img src="/vehiculos/soportes/mantenimientos/despues/<?= $mantenimiento->getImagenDespues() ?>"
                                                            alt="Imagen del despues"
                                                            class="img-thumbnail"
                                                            style="max-width: 100px; height: auto;"
                                                            onclick="mostrarImagenCompleta('/vehiculos/soportes/mantenimientos/despues/<?= $mantenimiento->getImagenDespues() ?>')">
                                                    <?php else: ?>
                                                        <span class="text-muted">Sin imagen</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-nowrap">
                                                        <button type="button" class="btn btn-sm btn-info me-1"
                                                            onclick="verDetalleMantenimiento(<?= $mantenimiento->getId() ?>)"
                                                            data-bs-toggle="modal" data-bs-target="#modalDetalleMantenimiento"
                                                            title="Ver detalle">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-primary me-1"
                                                            onclick="cargarMantenimientoParaEditar(<?= $mantenimiento->getId() ?>)"
                                                            data-bs-toggle="modal" data-bs-target="#modalEditarMantenimiento"
                                                            title="Editar mantenimiento">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="confirmarEliminarMantenimiento(<?= $mantenimiento->getId() ?>)"
                                                            title="Eliminar mantenimiento">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <?php if (!empty($mantenimiento->getArchivoPdf())): ?>
                                                            <button type="button" class="btn btn-sm btn-info"
                                                                title="Ver Soporte"
                                                                onclick="window.open('/vehiculos/soportes/mantenimientos/pdf/<?= $mantenimiento->getArchivoPdf() ?>', '_blank')"
                                                                data-pdf="<?= $mantenimiento->getArchivoPdf() ?>">
                                                                <i class="fa-solid fa-file-pdf"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-sm btn-secondary" 
                                                                title="No hay soporte disponible"
                                                                disabled>
                                                                <i class="fa-solid fa-file-pdf"></i>
                                                            </button>
                                                        <?php endif; ?>
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

    <!-- Modal para Nuevo Mantenimiento -->
    <div class="modal fade" id="modalNuevoMantenimiento" tabindex="-1" aria-labelledby="modalNuevoMantenimientoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalNuevoMantenimientoLabel">Registrar Nuevo Mantenimiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoMantenimiento" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="id_vehiculo" class="form-label">Vehículo</label>
                                <select class="form-select" id="id_vehiculo" name="id_vehiculo" required>
                                    <option value="">Seleccione un vehículo</option>
                                    <?php foreach ($vehiculos as $vehiculo): ?>
                                        <option value="<?= $vehiculo->getId() ?>"><?= $vehiculo->getPlaca() ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tipo_mantenimiento" class="form-label">Tipo de Mantenimiento</label>
                                <input type="text" class="form-control" id="tipo_mantenimiento" name="tipo_mantenimiento" required>
                            </div>
                            <div class="col-md-6">
                                <label for="costo" class="form-label">Costo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="costo" name="costo" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="imagen_antes" class="form-label">Imagen antes del mantenimiento</label>
                                <input type="file" class="form-control" id="imagen_antes" name="imagen_antes" accept="image/*">
                                <div class="form-text">Seleccione una imagen antes del mantenimiento.</div>
                            </div>
                            <div class="col-12">
                                <label for="imagen_despues" class="form-label">Imagen después del mantenimiento</label>
                                <input type="file" class="form-control" id="imagen_despues" name="imagen_despues" accept="image/*">
                                <div class="form-text">Seleccione una imagen después del mantenimiento.</div>
                            </div>
                            <div class="col-12">
                                <label for="archivo_pdf" class="form-label">Soporte PDF</label>
                                <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" accept=".pdf">
                                <div class="form-text">Seleccione un archivo PDF como soporte del mantenimiento.</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarNuevoMantenimiento()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Mantenimiento -->
    <div class="modal fade" id="modalEditarMantenimiento" tabindex="-1" aria-labelledby="modalEditarMantenimientoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditarMantenimientoLabel">Editar Mantenimiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="formEditarMantenimiento" enctype="multipart/form-data">
                        <input type="hidden" id="edit_id_registro" name="id_registro">
                        <input type="hidden" id="edit_imagen_antes_actual" name="imagen_antes_actual">
                        <input type="hidden" id="edit_imagen_despues_actual" name="imagen_despues_actual">
                        <input type="hidden" id="edit_archivo_pdf_actual" name="archivo_pdf_actual">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_id_vehiculo" class="form-label">Vehículo</label>
                                <select class="form-select" id="edit_id_vehiculo" name="id_vehiculo" required>
                                    <option value="">Seleccione un vehículo</option>
                                    <?php foreach ($vehiculos as $vehiculo): ?>
                                        <option value="<?= $vehiculo->getId() ?>"><?= $vehiculo->getPlaca() ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_tipo_mantenimiento" class="form-label">Tipo de Mantenimiento</label>
                                <input type="text" class="form-control" id="edit_tipo_mantenimiento" name="tipo_mantenimiento" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_costo" class="form-label">Costo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="edit_costo" name="costo" step="0.01" min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="edit_fecha" name="fecha" required>
                            </div>

                            <!-- Imagen Antes -->
                            <div class="col-md-6">
                                <label for="edit_imagen_antes" class="form-label">Imagen del Antes</label>
                                <input type="file" class="form-control" id="edit_imagen_antes" name="imagen_antes" accept="image/*">
                                <div class="form-text">Seleccione una imagen si desea reemplazar la actual.</div>
                                <div id="imagen_antes_actual_container" class="mt-2" style="display: none;">
                                    <p>Imagen actual:</p>
                                    <img id="imagen_antes_actual" src="#" alt="Imagen antes actual" class="img-thumbnail" style="max-width: 120px;">
                                </div>
                            </div>

                            <!-- Imagen Después -->
                            <div class="col-md-6">
                                <label for="edit_imagen_despues" class="form-label">Imagen del Después</label>
                                <input type="file" class="form-control" id="edit_imagen_despues" name="imagen_despues" accept="image/*">
                                <div class="form-text">Seleccione una imagen si desea reemplazar la actual.</div>
                                <div id="imagen_despues_actual_container" class="mt-2" style="display: none;">
                                    <p>Imagen actual:</p>
                                    <img id="imagen_despues_actual" src="#" alt="Imagen después actual" class="img-thumbnail" style="max-width: 120px;">
                                </div>
                            </div>

                            <!-- PDF Actual -->
                            <div class="col-md-12">
                                <label for="edit_archivo_pdf" class="form-label">Soporte PDF</label>
                                <input type="file" class="form-control" id="edit_archivo_pdf" name="archivo_pdf" accept=".pdf">
                                <div class="form-text">Seleccione un archivo PDF si desea reemplazar el actual.</div>
                                <div id="pdf_actual_container" class="mt-2" style="display: none;">
                                    <p>Archivo actual:
                                        <a href="#" id="pdf_actual_enlace" target="_blank">
                                            <span id="nombre_pdf_actual"></span>
                                        </a>
                                        <a href="#" id="pdf_actual_descargar" download class="btn btn-sm btn-outline-secondary ms-2">Descargar</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="actualizarMantenimiento()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalle de Mantenimiento -->
    <div class="modal fade" id="modalDetalleMantenimiento" tabindex="-1" aria-labelledby="modalDetalleMantenimientoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetalleMantenimientoLabel">Detalle de Mantenimiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold" style="color: var(--primary-color);">Información del Vehículo</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="background-color: var(--light-color);">Placa:</th>
                                    <td id="detalle_placa"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold" style="color: var(--primary-color);">Información del Mantenimiento</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="background-color: var(--light-color);">Tipo:</th>
                                    <td id="detalle_tipo_mantenimiento"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Costo:</th>
                                    <td id="detalle_costo"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Fecha:</th>
                                    <td id="detalle_fecha"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar imagen completa -->
    <div class="modal fade" id="modalImagenCompleta">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Soporte Visual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagenCompleta" src="" alt="Imagen completa del equipo" style="max-width: 100%; height: auto;">
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
    <!-- jsPDF para generar PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <!-- iziToast JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="../js/iziToast.js"></script>
    <script src="../js/mantenimientos.js"></script>


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
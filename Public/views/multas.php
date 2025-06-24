<?php
require_once '../../app/controllers/procesarMultas.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Multas</title>
    <link rel="icon" href="../icono/nexus-logo.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/multas.css">
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
                        <h2><i class="fas fa-exclamation-triangle text-primary me-2"></i> Gestión de Multas</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaMulta">
                            <i class="fas fa-plus me-1"></i> Nueva Multa
                        </button>
                    </div>
                    <!-- Los mensajes ahora se mostrarán con iziToast -->
                    <div id="mensajes-container" style="display: none;" data-mensajes="<?php
                                                                                        if (isset($_SESSION['resultado'])) {
                                                                                            echo htmlspecialchars(json_encode($_SESSION['resultado']), ENT_QUOTES, 'UTF-8');
                                                                                            unset($_SESSION['resultado']);
                                                                                        }
                                                                                        ?>"></div>
                    <!-- Tabla de multas -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablaMultas" class="table table-striped table-hover">
                                <thead class="table" style="background: #023373; color: white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Vehículo</th>
                                            <th>Motivo</th>
                                            <th>Fecha</th>
                                            <th>Fecha Fin</th>
                                            <th>Días</th>
                                            <th>Monto Original</th>
                                            <th>Monto Pagado</th>
                                            <th>Método Pago</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($multas as $multa): ?>
                                            <tr>
                                                <td><?= $multa->getId() ?></td>
                                                <td><?= $multa->getPlacaVehiculo() ?></td>
                                                <td class="text-truncate" title="<?= htmlspecialchars($multa->getMotivo()) ?>"><?= $multa->getMotivo() ?></td>
                                                <td><?= $multa->getFecha() ?></td>
                                                <td><?= $multa->getFechaFin() ? $multa->getFechaFin() : 'N/A' ?></td>
                                                <td>
                                                    <?php if ($multa->getDiasRestantes() !== null): ?>
                                                        <?php if ($multa->getDiasRestantes() <= 5 && $multa->getDiasRestantes() >= 0): ?>
                                                            <span style="color: red; font-weight: bold;"><?= $multa->getDiasRestantes() ?></span>
                                                        <?php else: ?>
                                                            <?= $multa->getDiasRestantes() ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td>$<?= number_format($multa->getMontoOriginal(), 2) ?></td>
                                                <td>$<?= number_format($multa->getMontoPagado(), 2) ?></td>
                                                <td><?= $multa->getMetodoPago() ?></td>
                                                <td>
                                                    <?php if ($multa->getEstado() === 'Pendiente'): ?>
                                                        <span class="badge rounded-pill badge-pendiente">Pendiente</span>
                                                    <?php elseif ($multa->getEstado() === 'Pagado'): ?>
                                                        <span class="badge rounded-pill badge-pagado">Pagado</span>
                                                    <?php elseif ($multa->getEstado() === 'Expirado'): ?>
                                                        <span class="badge rounded-pill badge-expirado">Expirado</span>
                                                    <?php else: ?>
                                                        <span class="badge rounded-pill badge-anulado">Anulado</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-nowrap">
                                                        <button type="button" class="btn btn-sm btn-info me-1"
                                                            onclick="verDetalleMulta(<?= $multa->getId() ?>)"
                                                            data-bs-toggle="modal" data-bs-target="#modalDetalleMulta"
                                                            title="Ver detalle">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-primary me-1"
                                                            onclick="cargarMultaParaEditar(<?= $multa->getId() ?>)"
                                                            data-bs-toggle="modal" data-bs-target="#modalEditarMulta"
                                                            title="Editar multa">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <?php if ($multa->getEstado() !== 'Anulado' && $multa->getEstado() !== 'Expirado'): ?>
                                                            <button type="button" class="btn btn-sm btn-success me-1"
                                                                onclick="cargarMultaParaPago(<?= $multa->getId() ?>)"
                                                                data-bs-toggle="modal" data-bs-target="#modalRegistrarPago"
                                                                title="Registrar pago">
                                                                <i class="fas fa-money-bill"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if ($multa->getEstado() === 'Anulado' || $multa->getEstado() === 'Expirado'): ?>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="confirmarEliminarMulta(<?= $multa->getId() ?>)"
                                                                title="Eliminar multa">
                                                                <i class="fas fa-trash"></i>
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

    <!-- Modal para Nueva Multa -->
    <div class="modal fade" id="modalNuevaMulta" tabindex="-1" aria-labelledby="modalNuevaMultaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalNuevaMultaLabel">Registrar Nueva Multa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaMulta">
                        <div class="row mb-3">
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
                                <label for="metodo_pago" class="form-label">Método de Pago</label>
                                <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="monto_original" class="form-label">Monto</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="monto_original" name="monto_original" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                <small class="text-muted">Fecha límite para el pago de la multa</small>
                            </div>
                            <div class="col-md-6">
                                <label for="motivo" class="form-label">Motivo</label>
                                <textarea class="form-control" id="motivo" name="motivo" rows="3" required></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" style="background-color: var(--primary-color);" onclick="guardarNuevaMulta()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para Editar Multa -->
    <div class="modal fade" id="modalEditarMulta" tabindex="-1" aria-labelledby="modalEditarMultaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditarMultaLabel">Editar Multa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarMulta">
                        <input type="hidden" id="edit_id_multa" name="id_multa">

                        <div class="row mb-3">
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
                                <label for="edit_metodo_pago" class="form-label">Método de Pago</label>
                                <select class="form-select" id="edit_metodo_pago" name="metodo_pago" required>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_monto_original" class="form-label">Monto Original</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="edit_monto_original" name="monto_original" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_monto_pagado" class="form-label">Monto Pagado</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="edit_monto_pagado" name="monto_pagado" step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="edit_fecha" name="fecha" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="edit_fecha_fin" name="fecha_fin">
                                <small class="text-muted">Fecha límite para el pago de la multa</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_motivo" class="form-label">Motivo</label>
                            <textarea class="form-control" id="edit_motivo" name="motivo" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_estado" class="form-label">Estado</label>
                            <select class="form-select" id="edit_estado" name="estado" required>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Pagado">Pagado</option>
                                <option value="Anulado">Anulado</option>
                                <option value="Expirado">Expirado</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" style="background-color: var(--primary-color);" onclick="actualizarMulta()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para Registrar Pago -->
    <div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-labelledby="modalRegistrarPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalRegistrarPagoLabel">Registrar Pago de Multa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formRegistrarPago" enctype="multipart/form-data">
                        <input type="hidden" id="pago_id_multa" name="id_multa">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Vehículo</label>
                                <input type="text" class="form-control" id="pago_vehiculo" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Monto Original</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="pago_monto_original" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Monto Pagado</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="pago_monto_pagado" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Monto Pendiente</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="pago_monto_pendiente" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="monto_pago" class="form-label">Monto a Pagar</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="monto_pago" name="monto_pago" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                                <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="pago_pdf" class="form-label">Comprobante de Pago (PDF)</label>
                            <input type="file" class="form-control" id="pago_pdf" name="pago_pdf" accept=".pdf" required>
                            <small class="text-muted">Suba un archivo PDF como comprobante del pago realizado</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnRegistrarPago" class="btn btn-primary" style="background-color: var(--primary-color);" onclick="registrarPago()">Registrar Pago</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para Detalle de Multa -->
    <div class="modal fade" id="modalDetalleMulta" tabindex="-1" aria-labelledby="modalDetalleMultaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetalleMultaLabel">Detalle de Multa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold" style="color: var(--primary-color);">Información de la Multa</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="background-color: var(--light-color);">Vehículo:</th>
                                    <td id="detalle_vehiculo"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Motivo:</th>
                                    <td id="detalle_motivo"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Fecha:</th>
                                    <td id="detalle_fecha"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Método de Pago:</th>
                                    <td id="detalle_metodo_pago"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold" style="color: var(--primary-color);">Información Financiera</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="background-color: var(--light-color);">Monto Original:</th>
                                    <td id="detalle_monto_original"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Monto Pagado:</th>
                                    <td id="detalle_monto_pagado"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Monto Pendiente:</th>
                                    <td id="detalle_monto_pendiente"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Fecha Fin:</th>
                                    <td id="detalle_fecha_fin"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Días Restantes:</th>
                                    <td id="detalle_dias_restantes"></td>
                                </tr>
                                <tr>
                                    <th style="background-color: var(--light-color);">Estado:</th>
                                    <td id="detalle_estado"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold mt-4" style="color: var(--primary-color);">Historial de Pagos</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark" style="background-color: var(--dark-color);">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Monto Pagado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla_pagos">
                                        <!-- Los pagos se cargarán dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" style="background-color: var(--primary-color);" onclick="imprimirDetalleMulta()"><i class="fas fa-file-pdf me-1"></i> Descargar PDF</button>
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
    <script src="../js/multas.js"></script>

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
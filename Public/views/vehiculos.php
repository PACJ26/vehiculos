<?php
session_start();
include '../../app/controllers/procesarVehiculo.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vehículos</title>
    <link rel="icon" href="../icono/nexus-logo.ico" type="image/x-icon">
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/vehiculo.css">
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
                        <h2><i class="fas fa-car text-primary me-2"></i> Gestión de Vehículos</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVehiculo">
                            <i class="fas fa-plus me-1"></i> Nuevo Vehículo
                        </button>
                    </div>

               <!-- Los mensajes ahora se mostrarán con iziToast -->
                    <div id="mensajes-container" style="display: none;" data-mensajes="<?php
                        if (isset($_SESSION['resultado'])) {
                            echo htmlspecialchars(json_encode($_SESSION['resultado']), ENT_QUOTES, 'UTF-8');
                                unset($_SESSION['resultado']);
                            }
                     ?>"></div>

                    <!-- Tabla de vehículos -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablaVehiculos" class="table table-striped table-hover">
                                <thead class="table" style="background: #023373; color: white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Imagen</th>
                                            <th>Placa</th>
                                            <th>Clase</th>
                                            <th>Marca</th>
                                            <th>Línea</th>
                                            <th>Modelo</th>
                                            <th>Color</th>
                                            <th>Propietario</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($vehiculos as $vehiculo): ?>
                                            <?php
                                            $propietario = $vehiculoController->obtenerPropietario($vehiculo->getIdPropietario());
                                            $nombrePropietario = $propietario ? $propietario->getNombre() . ' ' . $propietario->getApellido() : 'Sin propietario';
                                            ?>
                                            <tr>
                                                <td><?php echo $vehiculo->getId(); ?></td>
                                                <td>
                                                    <?php if ($vehiculo->getImagenUrl()): ?>
                                                        <img src="<?php echo $vehiculo->getImagenUrl(); ?>" class="vehiculo-imagen" alt="Imagen del vehículo">
                                                    <?php else: ?>
                                                        <div class="vehiculo-imagen d-flex justify-content-center align-items-center bg-light text-secondary">Sin imagen</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($vehiculo->getPlaca()); ?></td>
                                                <td><?php echo htmlspecialchars($vehiculo->getClase()); ?></td>
                                                <td><?php echo htmlspecialchars($vehiculo->getMarca()); ?></td>
                                                <td><?php echo htmlspecialchars($vehiculo->getLinea()); ?></td>
                                                <td><?php echo htmlspecialchars($vehiculo->getModelo()); ?></td>
                                                <td><?php echo htmlspecialchars($vehiculo->getColor()); ?></td>
                                                <td><?php echo htmlspecialchars($nombrePropietario); ?></td>
                                                <td>
                                                    <?php
                                                    $badgeClass = '';
                                                    switch ($vehiculo->getEstado()) {
                                                        case 'Activo':
                                                            $badgeClass = 'badge-activo';
                                                            break;
                                                        case 'Inactivo':
                                                            $badgeClass = 'badge-inactivo';
                                                            break;
                                                        case 'En mantenimiento':
                                                            $badgeClass = 'badge-mantenimiento';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge rounded-pill <?php echo $badgeClass; ?>">
                                                        <?php echo $vehiculo->getEstado(); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary btn-detalles"
                                                        data-id="<?php echo $vehiculo->getId(); ?>"
                                                        data-placa="<?php echo htmlspecialchars($vehiculo->getPlaca()); ?>"
                                                        data-clase="<?php echo htmlspecialchars($vehiculo->getClase()); ?>"
                                                        data-marca="<?php echo htmlspecialchars($vehiculo->getMarca()); ?>"
                                                        data-linea="<?php echo htmlspecialchars($vehiculo->getLinea()); ?>"
                                                        data-modelo="<?php echo htmlspecialchars($vehiculo->getModelo()); ?>"
                                                        data-color="<?php echo htmlspecialchars($vehiculo->getColor()); ?>"
                                                        data-imagen="<?php echo $vehiculo->getImagenUrl(); ?>"
                                                        data-propietario="<?php echo $vehiculo->getIdPropietario(); ?>"
                                                        data-estado="<?php echo $vehiculo->getEstado(); ?>"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-info btn-editar"
                                                        data-id="<?php echo $vehiculo->getId(); ?>"
                                                        data-placa="<?php echo htmlspecialchars($vehiculo->getPlaca()); ?>"
                                                        data-clase="<?php echo htmlspecialchars($vehiculo->getClase()); ?>"
                                                        data-marca="<?php echo htmlspecialchars($vehiculo->getMarca()); ?>"
                                                        data-linea="<?php echo htmlspecialchars($vehiculo->getLinea()); ?>"
                                                        data-modelo="<?php echo htmlspecialchars($vehiculo->getModelo()); ?>"
                                                        data-color="<?php echo htmlspecialchars($vehiculo->getColor()); ?>"
                                                        data-imagen="<?php echo $vehiculo->getImagenUrl(); ?>"
                                                        data-propietario="<?php echo $vehiculo->getIdPropietario(); ?>"
                                                        data-estado="<?php echo $vehiculo->getEstado(); ?>"
                                                        data-bs-toggle="modal" data-bs-target="#modalVehiculo">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar"
                                                        data-id="<?php echo $vehiculo->getId(); ?>"
                                                        data-placa="<?php echo htmlspecialchars($vehiculo->getPlaca()); ?>"
                                                        data-propietario="<?php echo $vehiculo->getIdPropietario(); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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

    <!-- Modal para crear/editar vehículo -->
    <div class="modal fade" id="modalVehiculo" tabindex="-1" aria-labelledby="modalVehiculoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalVehiculoLabel">Nuevo Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formVehiculo" method="POST" action="" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_vehiculo" id="id_vehiculo">
                        <input type="hidden" name="accion" id="accion" value="crear">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="placa" class="form-label">Placa</label>
                                <input type="text" class="form-control" id="placa" name="placa" placeholder="XXX-000" required maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label for="clase" class="form-label">Clase</label>
                                <input type="text" class="form-control" id="clase" name="clase" placeholder="CAMIONETA" required maxlength="30">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca" placeholder="TOYOTA" required maxlength="30">
                            </div>
                            <div class="col-md-6">
                                <label for="linea" class="form-label">Línea</label>
                                <input type="text" class="form-control" id="linea" name="linea" placeholder="HILUX" required maxlength="30">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" placeholder="2025" required min="1900" max="2100">
                            </div>
                            <div class="col-md-4">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color" placeholder="AZUL" maxlength="30">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_propietario" class="form-label">Propietario</label>
                                <select class="form-select" id="id_propietario" name="id_propietario">
                                    <option value="">Seleccione un propietario</option>
                                    <?php foreach ($propietarios as $propietario): ?>
                                        <option value="<?php echo $propietario->getId(); ?>">
                                            <?php echo htmlspecialchars($propietario->getNombre() . ' ' . $propietario->getApellido() . ' - ' . $propietario->getDocumento()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="">Seleccione un estado</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                    <option value="En mantenimiento">En mantenimiento</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="imagen" class="form-label">Imagen del Vehículo</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                                <small class="form-text text-muted">Seleccione una nueva imagen si desea cambiar la actual.</small>
                                <div id="imagen-preview-container" class="mt-2 d-none">
                                    <p class="mb-1"><strong>Imagen actual:</strong></p>
                                    <img id="imagen-preview" class="imagen-preview" src="" alt="Vista previa de la imagen">
                                </div>
                                <div id="sin-imagen-mensaje" class="mt-2 d-none p-3 border rounded bg-light text-dark">No hay imagen actual. Puede subir una nueva.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles del vehículo y propietario -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetallesLabel">Detalles del Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Información del Vehículo</h5>
                            <div class="mb-3 text-center" id="detalle-imagen-container">
                                <img id="detalle-imagen" class="imagen-preview" src="" alt="Imagen del vehículo">
                                <div id="detalle-sin-imagen" class="d-none p-3 border rounded bg-light text-dark">No hay imagen disponible</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Placa:</div>
                                <div class="col-md-8" id="detalle-placa"></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Clase:</div>
                                <div class="col-md-8" id="detalle-clase"></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Marca:</div>
                                <div class="col-md-8" id="detalle-marca"></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Línea:</div>
                                <div class="col-md-8" id="detalle-linea"></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Modelo:</div>
                                <div class="col-md-8" id="detalle-modelo"></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Color:</div>
                                <div class="col-md-8" id="detalle-color"></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Estado:</div>
                                <div class="col-md-8">
                                    <span class="badge rounded-pill" id="detalle-estado-badge"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="info-propietario">
                            <h5 class="border-bottom pb-2">Información del Propietario</h5>
                            <div id="propietario-info-container">
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Nombre:</div>
                                    <div class="col-md-8" id="detalle-nombre"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Apellido:</div>
                                    <div class="col-md-8" id="detalle-apellido"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Documento:</div>
                                    <div class="col-md-8" id="detalle-documento"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Teléfono:</div>
                                    <div class="col-md-8" id="detalle-telefono"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Correo:</div>
                                    <div class="col-md-8" id="detalle-correo"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Estado:</div>
                                    <div class="col-md-8">
                                        <span class="badge rounded-pill" id="detalle-propietario-estado"></span>
                                    </div>
                                </div>
                            </div>
                            <div id="sin-propietario" class="alert alert-warning d-none">
                                <i class="fas fa-exclamation-triangle me-2"></i> Este vehículo no tiene propietario asignado.
                            </div>
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
    <script src="../js/vehiculos.js"></script>

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
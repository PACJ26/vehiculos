<?php
session_start();
include '../../app/controllers/procesarPropietario.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Propietarios</title>
    <link rel="icon" href="../icono/nexus-logo.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/propietario.css">
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
                        <h2><i class="fas fa-users text-primary me-2"></i> Gestión de Propietarios</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPropietario">
                            <i class="fas fa-plus me-1"></i> Nuevo Propietario
                        </button>
                    </div>

                    <!-- Los mensajes ahora se mostrarán con iziToast -->
                    <div id="mensajes-container" style="display: none;" data-mensajes="<?php 
                        if (isset($_SESSION['resultado'])) {
                            echo htmlspecialchars(json_encode($_SESSION['resultado']), ENT_QUOTES, 'UTF-8');
                            unset($_SESSION['resultado']);
                        }
                    ?>"></div>
                    <!-- Tabla de propietarios -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablaPropietarios" class="table table-striped table-hover">
                                <thead class="table" style="background: #023373; color: white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Documento</th>
                                            <th>Teléfono</th>
                                            <th>Correo</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($propietarios as $propietario): ?>
                                            <tr>
                                                <td><?php echo $propietario->getId(); ?></td>
                                                <td><?php echo htmlspecialchars($propietario->getNombre()); ?></td>
                                                <td><?php echo htmlspecialchars($propietario->getApellido()); ?></td>
                                                <td><?php echo htmlspecialchars($propietario->getDocumento()); ?></td>
                                                <td><?php echo htmlspecialchars($propietario->getTelefono()); ?></td>
                                                <td><?php echo htmlspecialchars($propietario->getCorreo()); ?></td>
                                                <td>
                                                    <span class="badge rounded-pill <?php echo $propietario->getEstado() === 'Activo' ? 'badge-activo' : 'badge-inactivo'; ?>">
                                                        <?php echo $propietario->getEstado(); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info btn-editar"
                                                        data-id="<?php echo $propietario->getId(); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($propietario->getNombre()); ?>"
                                                        data-apellido="<?php echo htmlspecialchars($propietario->getApellido()); ?>"
                                                        data-documento="<?php echo htmlspecialchars($propietario->getDocumento()); ?>"
                                                        data-telefono="<?php echo htmlspecialchars($propietario->getTelefono()); ?>"
                                                        data-correo="<?php echo htmlspecialchars($propietario->getCorreo()); ?>"
                                                        data-estado="<?php echo $propietario->getEstado(); ?>"
                                                        data-bs-toggle="modal" data-bs-target="#modalPropietario">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar"
                                                        data-id="<?php echo $propietario->getId(); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($propietario->getNombre() . ' ' . $propietario->getApellido()); ?>">
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

    <!-- Modal para crear/editar propietario -->
    <div class="modal fade" id="modalPropietario" tabindex="-1" aria-labelledby="modalPropietarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalPropietarioLabel">Nuevo Propietario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPropietario" method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="id_propietario" id="id_propietario" value="<?php echo isset($_SESSION['id_propietario']) ? $_SESSION['id_propietario'] : ''; ?>">
                        <input type="hidden" name="accion" id="accion" value="<?php echo isset($_SESSION['accion_formulario']) ? $_SESSION['accion_formulario'] : 'crear'; ?>">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Pepito Andres" required maxlength="100" value="<?php echo isset($_SESSION['datos_formulario']['nombre']) ? htmlspecialchars($_SESSION['datos_formulario']['nombre']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="apellido" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Martinez Prado" required maxlength="100" value="<?php echo isset($_SESSION['datos_formulario']['apellido']) ? htmlspecialchars($_SESSION['datos_formulario']['apellido']) : ''; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="text" class="form-control" id="documento" name="documento" placeholder="1082353392" required maxlength="50" value="<?php echo isset($_SESSION['datos_formulario']['documento']) ? htmlspecialchars($_SESSION['datos_formulario']['documento']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="30020112745" required maxlength="15" value="<?php echo isset($_SESSION['datos_formulario']['telefono']) ? htmlspecialchars($_SESSION['datos_formulario']['telefono']) : ''; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" placeholder="example@gmail.com" required maxlength="100" value="<?php echo isset($_SESSION['datos_formulario']['correo']) ? htmlspecialchars($_SESSION['datos_formulario']['correo']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="Activo" <?php echo (isset($_SESSION['datos_formulario']['estado']) && $_SESSION['datos_formulario']['estado'] === 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                    <option value="Inactivo" <?php echo (isset($_SESSION['datos_formulario']['estado']) && $_SESSION['datos_formulario']['estado'] === 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
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
    <script src="../js/propietario.js"></script>
    
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
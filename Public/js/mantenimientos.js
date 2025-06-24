
// Inicializar DataTable
$(document).ready(function () {
    $('#tablaMantenimientos').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        responsive: true
    });
});

// Función para guardar nuevo mantenimiento
function guardarNuevoMantenimiento() {
    // Crear un objeto FormData para manejar la carga de archivos
    const formData = new FormData();
    formData.append('id_vehiculo', $('#id_vehiculo').val());
    formData.append('tipo_mantenimiento', $('#tipo_mantenimiento').val());
    formData.append('costo', $('#costo').val());
    formData.append('fecha', $('#fecha').val());

    // Agregar imagen antes si existe
    if ($('#imagen_antes')[0].files[0]) {
        formData.append('imagen_antes', $('#imagen_antes')[0].files[0]);
    }
    // Agregar imagen posterior si existe
    if ($('#imagen_despues')[0].files[0]) {
        formData.append('imagen_despues', $('#imagen_despues')[0].files[0]);
    }

    // Agregar el archivo PDF si existe
    if ($('#archivo_pdf')[0].files[0]) {
        formData.append('archivo_pdf', $('#archivo_pdf')[0].files[0]);
    }

    $.ajax({
        url: '../../app/controllers/MantenimientoController.php?action=crear',
        type: 'POST',
        data: formData,
        processData: false,  // Importante para FormData
        contentType: false,  // Importante para FormData
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                $('#modalNuevoMantenimiento').modal('hide');
                mostrarNotificacion('success', response.mensaje || 'Mantenimiento registrado correctamente');
                setTimeout(function () {
                    location.reload();
                }, 1500);
            } else {
                if (Array.isArray(response.errores)) {
                    response.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
                }
            }
        },
        error: function () {
            mostrarNotificacion('error', 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
        }
    });
}

// Función para cargar mantenimiento para editar
function cargarMantenimientoParaEditar(id) {
    $.ajax({
        url: '../../app/controllers/MantenimientoController.php?action=obtenerPorId',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                const mantenimiento = response.mantenimiento;

                // Llenar campos básicos
                $('#edit_id_registro').val(mantenimiento.id);
                $('#edit_id_vehiculo').val(mantenimiento.id_vehiculo);
                $('#edit_tipo_mantenimiento').val(mantenimiento.tipo_mantenimiento);
                $('#edit_costo').val(mantenimiento.costo);
                $('#edit_fecha').val(mantenimiento.fecha);
                $('#edit_imagen_antes_actual').val(mantenimiento.imagen_antes);
                $('#edit_imagen_despues_actual').val(mantenimiento.imagen_despues);
                $('#edit_archivo_pdf_actual').val(mantenimiento.archivo_pdf);


                // Mostrar archivo PDF actual si existe
                if (mantenimiento.archivo_pdf) {
                    $('#nombre_pdf_actual').text(mantenimiento.archivo_pdf);
                    $('#pdf_actual_enlace').attr('href', '/vehiculos/soportes/mantenimientos/pdf/' + mantenimiento.archivo_pdf);
                    $('#pdf_actual_descargar').attr('href', '/vehiculos/soportes/mantenimientos/pdf/' + mantenimiento.archivo_pdf);
                    $('#pdf_actual_container').show();
                } else {
                    $('#pdf_actual_container').hide();
                }

                // Mostrar imagen antes si existe
                if (mantenimiento.imagen_antes) {
                    $('#imagen_antes_actual').attr('src', '/vehiculos/soportes/mantenimientos/antes/' + mantenimiento.imagen_antes);
                    $('#imagen_antes_actual_container').show();
                } else {
                    $('#imagen_antes_actual_container').hide();
                }

                // Mostrar imagen después si existe
                if (mantenimiento.imagen_despues) {
                    $('#imagen_despues_actual').attr('src', '/vehiculos/soportes/mantenimientos/despues/' + mantenimiento.imagen_despues);
                    $('#imagen_despues_actual_container').show();
                } else {
                    $('#imagen_despues_actual_container').hide();
                }

                // Mostrar modal
                const modalElement = document.getElementById('modalEditarMantenimiento');
                const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                modal.show();

            } else {
                if (Array.isArray(response.errores)) {
                    response.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al cargar los datos del mantenimiento');
                }
            }
        },
        error: function () {
            mostrarNotificacion('error', 'Error al cargar los datos del mantenimiento');
        }
    });
}



// Función para actualizar mantenimiento
function actualizarMantenimiento() {
    const formData = new FormData();

    // Campos básicos
    formData.append('id_registro', $('#edit_id_registro').val());
    formData.append('id_vehiculo', $('#edit_id_vehiculo').val());
    formData.append('tipo_mantenimiento', $('#edit_tipo_mantenimiento').val());
    formData.append('costo', $('#edit_costo').val());
    formData.append('fecha', $('#edit_fecha').val());

    // Imágenes nuevas (si fueron seleccionadas)
    const imagenAntes = $('#edit_imagen_antes')[0].files[0];
    const imagenDespues = $('#edit_imagen_despues')[0].files[0];

    if (imagenAntes) {
        formData.append('imagen_antes', imagenAntes);
    }

    if (imagenDespues) {
        formData.append('imagen_despues', imagenDespues);
    }

    // Archivo PDF nuevo
    const archivoPDF = $('#edit_archivo_pdf')[0].files[0];
    if (archivoPDF) {
        formData.append('archivo_pdf', archivoPDF);
    }

    // Agregar nombres de archivos actuales (para borrarlos si se reemplazan)
    formData.append('imagen_antes_actual', $('#edit_imagen_antes_actual').val());
    formData.append('imagen_despues_actual', $('#edit_imagen_despues_actual').val());
    formData.append('archivo_pdf_actual', $('#edit_archivo_pdf_actual').val());

    // Enviar AJAX
    $.ajax({
        url: '../../app/controllers/MantenimientoController.php?action=actualizar',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                $('#modalEditarMantenimiento').modal('hide');
                mostrarNotificacion('success', response.mensaje || 'Mantenimiento actualizado correctamente');
                setTimeout(() => location.reload(), 1500);
            } else {
                if (Array.isArray(response.errores)) {
                    response.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al procesar la solicitud.');
                }
            }
        },
        error: function () {
            mostrarNotificacion('error', 'Error al procesar la solicitud');
        }
    });
}

// Función para confirmar eliminación de mantenimiento
function confirmarEliminarMantenimiento(id) {
    // Obtener información del mantenimiento para mostrar en el mensaje
    $.ajax({
        url: '../../app/controllers/MantenimientoController.php?action=obtenerPorId',
        type: 'GET',
        data: {
            id: id
        },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                const mantenimiento = response.mantenimiento;

                Swal.fire({
                    title: 'Confirmar Eliminación',
                    html: `¿Está seguro que desea eliminar el mantenimiento para el vehículo <strong>${mantenimiento.placa_vehiculo || 'seleccionado'}</strong>?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    focusCancel: true, // Foco automático en el botón Cancelar
                    didOpen: () => {
                        const cancelBtn = document.querySelector('.swal2-cancel');
                        if (cancelBtn) {
                            cancelBtn.focus(); // Asegura el foco en "Cancelar"
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        eliminarMantenimiento(id);
                    }
                });
            } else {
                // Si no se puede obtener la información del mantenimiento, mostrar un mensaje genérico
                Swal.fire({
                    title: 'Confirmar Eliminación',
                    html: `¿Está seguro que desea eliminar este mantenimiento?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        eliminarMantenimiento(id);
                    }
                });
            }
        },
        error: function () {
            console.error('Error al obtener información del mantenimiento');
            // En caso de error, mostrar un mensaje genérico
            Swal.fire({
                title: 'Confirmar Eliminación',
                html: `¿Está seguro que desea eliminar este mantenimiento?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarMantenimiento(id);
                }
            });
        }
    });
}

// Función para eliminar mantenimiento
function eliminarMantenimiento(id) {
    $.ajax({
        url: '../../app/controllers/MantenimientoController.php?action=eliminar',
        type: 'POST',
        data: {
            id: id
        },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                mostrarNotificacion('success', response.mensaje || 'Mantenimiento eliminado correctamente');
                setTimeout(function () {
                    location.reload();
                }, 1500);
            } else {
                if (Array.isArray(response.errores)) {
                    response.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
                }
            }
        },
        error: function () {
            mostrarNotificacion('error', 'Error al procesar la solicitud');
        }
    });
}

// Función para ver detalle de mantenimiento
function verDetalleMantenimiento(id) {
    $.ajax({
        url: '../../app/controllers/MantenimientoController.php?action=obtenerPorId',
        type: 'GET',
        data: {
            id: id
        },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                const mantenimiento = response.mantenimiento;
                $('#detalle_placa').text(mantenimiento.placa_vehiculo);
                $('#detalle_tipo_mantenimiento').text(mantenimiento.tipo_mantenimiento);
                $('#detalle_costo').text('$' + parseFloat(mantenimiento.costo).toFixed(2));
                $('#detalle_fecha').text(mantenimiento.fecha);

            } else {
                if (Array.isArray(response.errores)) {
                    response.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al cargar los detalles del mantenimiento');
                }
            }
        },
        error: function () {
            mostrarNotificacion('error', 'Error al cargar los detalles del mantenimiento');
        }
    });
}

function mostrarImagenCompleta(rutaImagen) {
    const imagenCompleta = document.getElementById('imagenCompleta');
    if (imagenCompleta) {
        imagenCompleta.src = rutaImagen;
    }
    const modal = new bootstrap.Modal(document.getElementById('modalImagenCompleta'));
    modal.show();
}






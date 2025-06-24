$(document).ready(function () {
    // Inicializar DataTable
    $('#tablaVehiculos').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        pageLength: 10,
        responsive: true
    });

    // Configurar modal para editar
    $('.btn-editar').click(function () {
        $('#modalVehiculoLabel').text('Editar Vehículo');
        $('#accion').val('editar');
        $('#id_vehiculo').val($(this).data('id'));
        $('#placa').val($(this).data('placa'));
        $('#clase').val($(this).data('clase'));
        $('#marca').val($(this).data('marca'));
        $('#linea').val($(this).data('linea'));
        $('#modelo').val($(this).data('modelo'));
        $('#color').val($(this).data('color'));
        $('#id_propietario').val($(this).data('propietario'));
        $('#estado').val($(this).data('estado'));

        // Mostrar imagen actual si existe
        if ($(this).data('imagen')) {
            $('#imagen-preview').attr('src', $(this).data('imagen'));
            $('#imagen-preview-container').removeClass('d-none');
            $('#sin-imagen-mensaje').addClass('d-none');
            // Añadir mensaje informativo sobre cambio de imagen
            $('.form-text.text-muted').show();
        } else {
            $('#imagen-preview-container').addClass('d-none');
            $('#sin-imagen-mensaje').removeClass('d-none');
            // Ocultar mensaje informativo si no hay imagen
            $('.form-text.text-muted').hide();
        }
    });

    // Configurar modal para ver detalles
    $('.btn-detalles').click(function () {
        // Cargar datos del vehículo
        $('#detalle-placa').text($(this).data('placa'));
        $('#detalle-clase').text($(this).data('clase'));
        $('#detalle-marca').text($(this).data('marca'));
        $('#detalle-linea').text($(this).data('linea'));
        $('#detalle-modelo').text($(this).data('modelo'));
        $('#detalle-color').text($(this).data('color'));

        // Mostrar imagen si existe
        if ($(this).data('imagen')) {
            $('#detalle-imagen').attr('src', $(this).data('imagen'));
            $('#detalle-imagen').removeClass('d-none');
            $('#detalle-sin-imagen').addClass('d-none');
        } else {
            $('#detalle-imagen').addClass('d-none');
            $('#detalle-sin-imagen').removeClass('d-none');
        }

        // Mostrar estado con el color correspondiente
        const estado = $(this).data('estado');
        $('#detalle-estado-badge').text(estado);

        let badgeClass = '';
        switch (estado) {
            case 'Activo':
                badgeClass = 'badge-activo';
                break;
            case 'Inactivo':
                badgeClass = 'badge-inactivo';
                break;
            case 'En mantenimiento':
                badgeClass = 'badge-mantenimiento';
                break;
        }
        $('#detalle-estado-badge').removeClass().addClass('badge rounded-pill ' + badgeClass);

        // Mostrar imagen si existe
        if ($(this).data('imagen')) {
            $('#detalle-imagen').attr('src', $(this).data('imagen'));
            $('#detalle-imagen').removeClass('d-none');
            $('#detalle-sin-imagen').addClass('d-none');
        } else {
            $('#detalle-imagen').addClass('d-none');
            $('#detalle-sin-imagen').removeClass('d-none');
        }

        // Cargar datos del propietario
        const idPropietario = $(this).data('propietario');

        if (idPropietario) {
            // Hacer una petición AJAX para obtener los datos del propietario
            $.ajax({
                url: '../views/obtener_propietario.php',
                type: 'GET',
                data: { id_propietario: idPropietario },
                dataType: 'json',
                success: function (propietario) {
                    if (propietario && propietario.id) {
                        $('#detalle-nombre').text(propietario.nombre);
                        $('#detalle-apellido').text(propietario.apellido);
                        $('#detalle-documento').text(propietario.documento);
                        $('#detalle-telefono').text(propietario.telefono);
                        $('#detalle-correo').text(propietario.correo);

                        // Mostrar estado del propietario
                        $('#detalle-propietario-estado').text(propietario.estado);
                        $('#detalle-propietario-estado').removeClass()
                            .addClass('badge rounded-pill ' +
                                (propietario.estado === 'Activo' ? 'badge-activo' : 'badge-inactivo'));

                        $('#propietario-info-container').removeClass('d-none');
                        $('#sin-propietario').addClass('d-none');
                    } else {
                        $('#sin-propietario').removeClass('d-none');
                        $('#propietario-info-container').addClass('d-none');
                    }
                },
                error: function () {
                    $('#sin-propietario').removeClass('d-none');
                    $('#propietario-info-container').addClass('d-none');
                }
            });
        } else {
            $('#sin-propietario').removeClass('d-none');
            $('#propietario-info-container').addClass('d-none');
        }
    });

    // Configurar modal para crear
    $('#modalVehiculo').on('hidden.bs.modal', function () {
        $('#formVehiculo').trigger('reset');
        $('#modalVehiculoLabel').text('Nuevo Vehículo');
        $('#accion').val('crear');
        $('#id_vehiculo').val('');
        $('#imagen-preview-container').addClass('d-none');
        $('#sin-imagen-mensaje').removeClass('d-none').text('Sin imagen');
        // Ocultar mensaje informativo sobre cambio de imagen para un nuevo vehículo
        $('.form-text.text-muted').hide();
    });


    // Vista previa de la imagen
    $('#imagen').change(function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#imagen-preview').attr('src', e.target.result);
                $('#imagen-preview-container').removeClass('d-none');
                $('#sin-imagen-mensaje').addClass('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagen-preview-container').addClass('d-none');
            $('#sin-imagen-mensaje').removeClass('d-none');
        }
    });

      // Configurar SweetAlert2 para eliminar
      $('.btn-eliminar').click(function () {
        const placaVehiculo = $(this).data('placa');
        const idPropietario = $(this).data('propietario');
        const idVehiculo = $(this).data('id'); // Guardar el ID del vehículo

        // Primero obtener los datos del propietario
        $.ajax({
            url: '../views/obtener_propietario.php',
            type: 'GET',
            data: { id_propietario: idPropietario },
            dataType: 'json',
            success: function(propietario) {
                const nombreCompleto = propietario ? `${propietario.nombre} ${propietario.apellido}` : 'Sin propietario';
                
                Swal.fire({
                    title: 'Confirmar Eliminación',
                    html: `¿Está seguro que desea eliminar el vehículo con placa <strong>${placaVehiculo}</strong>?<br>Propietario: <strong>${nombreCompleto}</strong><br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    focusCancel: true,
                    didOpen: () => {
                        const cancelBtn = document.querySelector('.swal2-cancel');
                        if (cancelBtn) {
                            cancelBtn.focus();
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Crear un formulario dinámico para enviar la solicitud
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';

                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'id_vehiculo';
                        input.value = idVehiculo; // Usar el ID guardado

                        const submitBtn = document.createElement('input');
                        submitBtn.type = 'hidden';
                        submitBtn.name = 'eliminar';

                        form.appendChild(input);
                        form.appendChild(submitBtn);
                        document.body.appendChild(form);

                        form.submit();
                    }
                });
            },
            error: function() {
                // Si hay error al obtener el propietario, mostrar el mensaje sin información del propietario
                Swal.fire({
                    title: 'Confirmar Eliminación',
                    html: `¿Está seguro que desea eliminar el vehículo con placa <strong>${placaVehiculo}</strong>?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    focusCancel: true,
                    didOpen: () => {
                        const cancelBtn = document.querySelector('.swal2-cancel');
                        if (cancelBtn) {
                            cancelBtn.focus();
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Crear un formulario dinámico para enviar la solicitud
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';

                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'id_vehiculo';
                        input.value = idVehiculo; // Usar el ID guardado

                        const submitBtn = document.createElement('input');
                        submitBtn.type = 'hidden';
                        submitBtn.name = 'eliminar';

                        form.appendChild(input);
                        form.appendChild(submitBtn);
                        document.body.appendChild(form);

                        form.submit();
                    }
                });
            }
        });
    });

});
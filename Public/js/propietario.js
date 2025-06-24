$(document).ready(function () {
    // Inicializar DataTable
    $('#tablaPropietarios').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        pageLength: 10,
        responsive: true
    });

    // Configurar modal para editar
    $('.btn-editar').click(function () {
        $('#modalPropietarioLabel').text('Editar Propietario');
        $('#accion').val('editar');
        $('#id_propietario').val($(this).data('id'));
        $('#nombre').val($(this).data('nombre'));
        $('#apellido').val($(this).data('apellido'));
        $('#documento').val($(this).data('documento'));
        $('#telefono').val($(this).data('telefono'));
        $('#correo').val($(this).data('correo'));
        $('#estado').val($(this).data('estado'));
    });

    // Configurar modal para crear
    $('#modalPropietario').on('hidden.bs.modal', function () {
        $('#formPropietario').trigger('reset');
        $('#modalPropietarioLabel').text('Nuevo Propietario');
        $('#accion').val('crear');
        $('#id_propietario').val('');
    });

    // Configurar SweetAlert2 para eliminar
    $('.btn-eliminar').click(function () {
        const idPropietario = $(this).data('id');
        const nombrePropietario = $(this).data('nombre');

        Swal.fire({
            title: 'Confirmar Eliminación',
            html: `¿Está seguro que desea eliminar al propietario <strong>${nombrePropietario}</strong>?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
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
                // Crear un formulario dinámico para enviar la solicitud
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id_propietario';
                input.value = idPropietario;

                const submitBtn = document.createElement('input');
                submitBtn.type = 'hidden';
                submitBtn.name = 'eliminar';

                form.appendChild(input);
                form.appendChild(submitBtn);
                document.body.appendChild(form);

                form.submit();
            }
        });
    });
});
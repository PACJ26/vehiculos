// Inicializar DataTable
$(document).ready(function () {
    $('#tablaSeguros').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        responsive: true
    });

    // Configurar el botón de guardar para que llame a la función correcta
    $('#btnGuardarSeguro').click(function () {
        guardarSeguro();
    });
});

// Función para preparar el modal para un nuevo seguro
function prepararNuevoSeguro() {
    // Cambiar el título del modal
    document.getElementById('modalSeguroLabel').textContent = 'Registrar Nuevo Seguro';

    // Limpiar el formulario
    document.getElementById('formSeguro').reset();

    // Establecer valores iniciales
    document.getElementById('id_seguro').value = '';
    document.getElementById('accion').value = 'crear';
    document.getElementById('fecha_inicio').value = new Date().toISOString().slice(0, 10);

    // Ocultar el campo de estado (solo se muestra al editar)
    document.getElementById('estado_container').style.display = 'none';
}

// Función para guardar o actualizar seguro
function guardarSeguro() {
    const formData = new FormData(document.getElementById('formSeguro'));
    const accion = document.getElementById('accion').value;
    const url = accion === 'crear'
        ? '../../app/controllers/SeguroController.php?action=crear'
        : '../../app/controllers/SeguroController.php?action=actualizar';
    
    // Verificar si se ha seleccionado un archivo PDF
    const archivoPdf = document.getElementById('archivo_pdf').files[0];
    if (archivoPdf) {
        formData.append('archivo_pdf', archivoPdf);
    }

    fetch(url, {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.exito) {
                mostrarNotificacion('success', data.mensaje);
                $('#modalSeguro').modal('hide');

                // Recargar la página después de un breve retraso
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                if (Array.isArray(data.errores)) {
                    data.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('error', 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
        });
}

// Función para cargar seguro para editar
function cargarSeguroParaEditar(id) {
    // Cambiar el título del modal
    document.getElementById('modalSeguroLabel').textContent = 'Editar Seguro';

    // Establecer acción como editar
    document.getElementById('accion').value = 'editar';

    // Mostrar el campo de estado
    document.getElementById('estado_container').style.display = 'block';

    fetch(`../../app/controllers/SeguroController.php?action=obtenerPorId&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                const seguro = data.seguro;
                document.getElementById('id_seguro').value = seguro.id;
                document.getElementById('id_vehiculo').value = seguro.id_vehiculo;
                document.getElementById('aseguradora').value = seguro.aseguradora;
                document.getElementById('tipo_poliza').value = seguro.tipo_poliza;
                document.getElementById('costo').value = seguro.costo;
                document.getElementById('fecha_inicio').value = seguro.fecha_inicio;
                document.getElementById('fecha_vencimiento').value = seguro.fecha_vencimiento;
                document.getElementById('estado').value = seguro.estado;
            } else {
                if (Array.isArray(data.errores)) {
                    data.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('error', 'Error al cargar los datos del seguro. Por favor, inténtelo de nuevo.');
        });
}

// Función para ver detalle de seguro
function verDetalleSeguro(id) {
    fetch(`../../app/controllers/SeguroController.php?action=obtenerPorId&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.exito) {
                const seguro = data.seguro;
                // Verificar que los elementos existen antes de manipularlos
                const elementosDetalle = {
                    'detalle_id': seguro.id,
                    'detalle_vehiculo': seguro.placa_vehiculo,
                    'detalle_aseguradora': seguro.aseguradora,
                    'detalle_tipo_poliza': seguro.tipo_poliza,
                    'detalle_costo': `$${parseFloat(seguro.costo).toFixed(2)}`,
                    'detalle_fecha_inicio': seguro.fecha_inicio,
                    'detalle_fecha_vencimiento': seguro.fecha_vencimiento,
                    'detalle_dias_restantes': seguro.dias_restantes
                };

                // Actualizar cada elemento si existe
                for (const [id, valor] of Object.entries(elementosDetalle)) {
                    const elemento = document.getElementById(id);
                    if (elemento) {
                        elemento.textContent = valor;
                    }
                }

                // Manejar el estado con verificación de existencia
                let estadoElement = document.getElementById('detalle_estado');
                if (estadoElement) {
                    estadoElement.textContent = seguro.estado;
                    estadoElement.className = '';
                    if (seguro.estado === 'Vigente') {
                        estadoElement.classList.add('badge', 'rounded-pill', 'badge-vigente');
                    } else if (seguro.estado === 'Expirado') {
                        estadoElement.classList.add('badge', 'rounded-pill', 'badge-expirado');
                    } else {
                        estadoElement.classList.add('badge', 'rounded-pill', 'badge-cancelado');
                    }
                }
            } else {
                if (Array.isArray(data.errores)) {
                    data.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('error', 'Error al cargar los datos del seguro. Por favor, inténtelo de nuevo.');
        });
}

// Función para eliminar seguro
function eliminarSeguro(id) {
    fetch(`../../app/controllers/SeguroController.php?action=eliminar&id=${id}`, {
        method: 'GET'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.exito) {
                // Mostrar mensaje de éxito usando iziToast
                mostrarNotificacion('success', data.mensaje);

                // Recargar la página después de un breve retraso
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                if (Array.isArray(data.errores)) {
                    data.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('error', 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
        });
}

// Función para confirmar eliminación de seguro
function confirmarEliminarSeguro(id) {
    // Obtener información del seguro para mostrar en el mensaje
    fetch(`../../app/controllers/SeguroController.php?action=obtenerPorId&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                const seguro = data.seguro;
                
                Swal.fire({
                    title: 'Confirmar Eliminación',
                    html: `¿Está seguro que desea eliminar el seguro de <strong>${seguro.aseguradora}</strong> para el vehículo <strong>${seguro.placa_vehiculo || 'seleccionado'}</strong>?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
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
                        eliminarSeguro(id);
                    }
                });
            } else {
                // Si no se puede obtener la información del seguro, mostrar un mensaje genérico
                Swal.fire({
                    title: 'Confirmar Eliminación',
                    html: `¿Está seguro que desea eliminar este seguro?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        eliminarSeguro(id);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // En caso de error, mostrar un mensaje genérico
            Swal.fire({
                title: 'Confirmar Eliminación',
                html: `¿Está seguro que desea eliminar este seguro?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarSeguro(id);
                }
            });
        });
}


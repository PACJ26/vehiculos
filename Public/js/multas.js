
// Inicializar DataTable
$(document).ready(function () {
    $('#tablaMultas').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        responsive: true
    });
});

// Función para guardar nueva multa
function guardarNuevaMulta() {
    const formData = {
        id_vehiculo: $('#id_vehiculo').val(),
        motivo: $('#motivo').val(),
        monto_original: $('#monto_original').val(),
        metodo_pago: $('#metodo_pago').val(),
        fecha: $('#fecha').val(),
        fecha_fin: $('#fecha_fin').val()
    };

    $.ajax({
        url: '../../app/controllers/MultaController.php?action=crear',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                $('#modalNuevaMulta').modal('hide');
                mostrarNotificacion('success', response.mensaje || 'Multa registrada correctamente');
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

// Función para cargar multa para editar
function cargarMultaParaEditar(id) {
    $.ajax({
        url: '../../app/controllers/MultaController.php?action=obtenerPorId',
        type: 'GET',
        data: {
            id: id
        },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                const multa = response.multa;
                $('#edit_id_multa').val(multa.id);
                $('#edit_id_vehiculo').val(multa.id_vehiculo);
                $('#edit_motivo').val(multa.motivo);
                $('#edit_monto_original').val(multa.monto_original);
                $('#edit_monto_pagado').val(multa.monto_pagado);
                $('#edit_metodo_pago').val(multa.metodo_pago);
                $('#edit_fecha').val(multa.fecha);
                $('#edit_fecha_fin').val(multa.fecha_fin);
                $('#edit_estado').val(multa.estado);
            } else {
                if (Array.isArray(response.errores)) {
                    response.errores.forEach(error => mostrarNotificacion('error', error));
                } else {
                    mostrarNotificacion('error', 'Error al cargar los datos de la multa');
                }
            }
        },
        error: function () {
            mostrarNotificacion('error', 'Error al procesar la solicitud');
        }
    });
}

// Función para actualizar multa
function actualizarMulta() {
    const formData = {
        id_multa: $('#edit_id_multa').val(),
        id_vehiculo: $('#edit_id_vehiculo').val(),
        motivo: $('#edit_motivo').val(),
        monto_original: $('#edit_monto_original').val(),
        monto_pagado: $('#edit_monto_pagado').val(),
        metodo_pago: $('#edit_metodo_pago').val(),
        fecha: $('#edit_fecha').val(),
        fecha_fin: $('#edit_fecha_fin').val(),
        estado: $('#edit_estado').val()
    };

    $.ajax({
        url: '../../app/controllers/MultaController.php?action=actualizar',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                $('#modalEditarMulta').modal('hide');
                mostrarNotificacion('success', response.mensaje || 'Multa actualizada correctamente');
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

// Función para cargar multa para pago
function cargarMultaParaPago(id) {
    $.ajax({
        url: '../../app/controllers/MultaController.php?action=obtenerPorId',
        type: 'GET',
        data: {
            id: id
        },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                const multa = response.multa;
                $('#pago_id_multa').val(multa.id);
                $('#pago_vehiculo').val(multa.placa_vehiculo + ' - ' + multa.marca_vehiculo + ' ' + multa.modelo_vehiculo);
                $('#pago_monto_original').val(multa.monto_original);
                $('#pago_monto_pagado').val(multa.monto_pagado);

                // Calcular monto pendiente
                const montoPendiente = multa.monto_original - multa.monto_pagado;
                $('#pago_monto_pendiente').val(montoPendiente.toFixed(2));

                // Establecer el monto a pagar por defecto como el monto pendiente
                $('#monto_pago').val(montoPendiente.toFixed(2));
                $('#monto_pago').attr('max', montoPendiente.toFixed(2));
            } else {
                mostrarError('Error al cargar los datos de la multa');
            }
        },
        error: function () {
            mostrarError('Error al procesar la solicitud');
        }
    });
}

// Función para registrar pago
function registrarPago() {
    // Crear un objeto FormData para manejar archivos
    const formData = new FormData();
    formData.append('id_multa', $('#pago_id_multa').val());
    formData.append('monto_pagado', $('#monto_pago').val());
    formData.append('fecha_pago', $('#fecha_pago').val());
    
    // Añadir el archivo PDF si existe
    if ($('#pago_pdf')[0].files[0]) {
        formData.append('pago_pdf', $('#pago_pdf')[0].files[0]);
    }

    $.ajax({
        url: '../../app/controllers/pagosMultasController.php?action=crear',
        type: 'POST',
        data: formData,
        processData: false,  // Necesario para FormData
        contentType: false,  // Necesario para FormData
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                $('#modalRegistrarPago').modal('hide');
                mostrarNotificacion('success', response.mensaje || 'Pago registrado correctamente');
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

// Función para ver detalle de multa
function verDetalleMulta(id) {
    $.ajax({
        url: '../../app/controllers/MultaController.php?action=obtenerPorId',
        type: 'GET',
        data: {
            id: id
        },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                const multa = response.multa;

                // Llenar información de la multa
                $('#detalle_vehiculo').text(multa.placa_vehiculo);
                $('#detalle_motivo').text(multa.motivo);
                $('#detalle_fecha').text(multa.fecha);
                $('#detalle_fecha_fin').text(multa.fecha_fin ? multa.fecha_fin : 'N/A');

                // Mostrar días restantes con color rojo si son pocos
                if (multa.dias_restantes !== null) {
                    if (multa.dias_restantes <= 5 && multa.dias_restantes >= 0) {
                        $('#detalle_dias_restantes').html('<span style="color: red; font-weight: bold;">' + multa.dias_restantes + '</span>');
                    } else {
                        $('#detalle_dias_restantes').text(multa.dias_restantes);
                    }
                } else {
                    $('#detalle_dias_restantes').text('N/A');
                }

                $('#detalle_metodo_pago').text(multa.metodo_pago);

                // Llenar información financiera
                $('#detalle_monto_original').text('$' + parseFloat(multa.monto_original).toFixed(2));
                $('#detalle_monto_pagado').text('$' + parseFloat(multa.monto_pagado).toFixed(2));

                const montoPendiente = multa.monto_original - multa.monto_pagado;
                $('#detalle_monto_pendiente').text('$' + montoPendiente.toFixed(2));

                // Mostrar estado con color
                let estadoHTML = '';
                if (multa.estado === 'Pendiente') {
                    estadoHTML = '<span class="badge bg-warning text-dark">Pendiente</span>';
                } else if (multa.estado === 'Pagado') {
                    estadoHTML = '<span class="badge bg-success">Pagado</span>';
                } else {
                    estadoHTML = '<span class="badge bg-danger">Anulado</span>';
                }
                $('#detalle_estado').html(estadoHTML);

                // Cargar historial de pagos
                cargarHistorialPagos(id);
            } else {
                mostrarError('Error al cargar los datos de la multa');
            }
        },
        error: function () {
            mostrarError('Error al procesar la solicitud');
        }
    });
}

// Función para cargar historial de pagos
function cargarHistorialPagos(id_multa) {
    $.ajax({
        url: '../../app/controllers/pagosMultasController.php?action=obtenerPorMulta',
        type: 'GET',
        data: {
            id_multa: id_multa
        },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                const pagos = response.pagos;
                let html = '';

                if (pagos.length === 0) {
                    html = '<tr><td colspan="4" class="text-center">No hay pagos registrados</td></tr>';
                } else {
                    pagos.forEach(function (pago) {
                        html += '<tr>';
                        html += '<td>' + pago.id + '</td>';
                        html += '<td>' + pago.fecha_pago + '</td>';
                        html += '<td>$' + parseFloat(pago.monto_pagado).toFixed(2) + '</td>';
                    });
                }

                $('#tabla_pagos').html(html);
            } else {
                mostrarError('Error al cargar el historial de pagos');
            }
        },
        error: function () {
            mostrarError('Error al procesar la solicitud');
        }
    });
}

// Función para confirmar eliminación de multa
function confirmarEliminarMulta(id) {
    // Obtener información de la multa para mostrar en el mensaje
    $.ajax({
        url: '../../app/controllers/MultaController.php?action=obtenerPorId',
        type: 'GET',
        data: {
            id: id
        },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                const multa = response.multa;
                
                Swal.fire({
                    title: 'Confirmar Eliminación',
                    html: `¿Está seguro que desea eliminar la multa para el vehículo <strong>${multa.placa_vehiculo || 'seleccionado'}</strong>?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
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
                        eliminarMulta(id);
                    }
                });
            } else {
                // Si no se puede obtener la información de la multa, mostrar un mensaje genérico
                Swal.fire({
                    title: 'Confirmar Eliminación',
                    html: `¿Está seguro que desea eliminar esta multa?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        eliminarMulta(id);
                    }
                });
            }
        },
        error: function () {
            console.error('Error al obtener información de la multa');
            // En caso de error, mostrar un mensaje genérico
            Swal.fire({
                title: 'Confirmar Eliminación',
                html: `¿Está seguro que desea eliminar esta multa?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarMulta(id);
                }
            });
        }
    });
}

// Función para eliminar multa
function eliminarMulta(id) {
    $.ajax({
        url: '../../app/controllers/MultaController.php?action=eliminar',
        type: 'POST',
        data: {
            id: id
        },
        dataType: 'json',
        success: function (response) {
            if (response.exito) {
                mostrarNotificacion('success', response.mensaje || 'Multa eliminada correctamente');
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

// Función para generar y descargar PDF de detalle de multa
function imprimirDetalleMulta() {
    // Inicializar jsPDF
    const { 
        jsPDF
    } = window.jspdf;
    const doc = new jsPDF();

    // Obtener datos del detalle
    const placa = $('#detalle_vehiculo').text();
    const motivo = $('#detalle_motivo').text();
    const fecha = $('#detalle_fecha').text();
    const metodoPago = $('#detalle_metodo_pago').text();
    const montoOriginal = $('#detalle_monto_original').text();
    const montoPagado = $('#detalle_monto_pagado').text();
    const montoPendiente = $('#detalle_monto_pendiente').text();
    const estado = $('#detalle_estado').text().replace(/<[^>]*>/g, '');

    // Configurar documento
    doc.setFontSize(20);
    doc.text('Detalle de Multa', 105, 20, {
        align: 'center'
    });

    // Agregar logo o encabezado si es necesario
    doc.setFontSize(12);
    doc.text('Módulo de Gestión de Vehículos', 105, 30, {
        align: 'center'
    });
    doc.text('Fecha de generación: ' + new Date().toLocaleDateString(), 105, 35, {
        align: 'center'
    });

    // Información de la multa
    doc.setFontSize(16);
    doc.text('Información de la Multa', 20, 50);
    doc.setFontSize(12);

    const infoMulta = [

        ['Placa:', placa],
        ['Motivo:', motivo],
        ['Fecha:', fecha],
        ['Método de Pago:', metodoPago]
    ];

    doc.autoTable({
        startY: 55,
        head: [
            ['Información', 'Datos']
        ],
        body: infoMulta,
        theme: 'striped',
        headStyles: {
            fillColor: [41, 128, 185]
        },
        margin: {
            left: 20
        },
        tableWidth: 170
    });

    // Información financiera
    doc.setFontSize(16);
    doc.text('Información Financiera', 20, doc.lastAutoTable.finalY + 15);
    doc.setFontSize(12);

    const infoFinanciera = [
        ['Monto Original:', montoOriginal],
        ['Monto Pagado:', montoPagado],
        ['Monto Pendiente:', montoPendiente],
        ['Estado:', estado]
    ];

    doc.autoTable({
        startY: doc.lastAutoTable.finalY + 20,
        head: [
            ['Información', 'Valores']
        ],
        body: infoFinanciera,
        theme: 'striped',
        headStyles: {
            fillColor: [41, 128, 185]
        },
        margin: {
            left: 20
        },
        tableWidth: 170
    });

    // Historial de pagos
    doc.setFontSize(16);
    doc.text('Historial de Pagos', 20, doc.lastAutoTable.finalY + 15);
    doc.setFontSize(12);

    // Obtener datos de la tabla de pagos
    const pagoRows = [];
    $('#tabla_pagos tr').each(function () {
        const id = $(this).find('td:eq(0)').text();
        const fecha = $(this).find('td:eq(1)').text();
        const monto = $(this).find('td:eq(2)').text();

        if (id && fecha && monto) {
            pagoRows.push([id, fecha, monto]);
        }
    });

    if (pagoRows.length === 0) {
        pagoRows.push(['No hay pagos registrados', '', '']);
    }

    doc.autoTable({
        startY: doc.lastAutoTable.finalY + 20,
        head: [
            ['ID', 'Fecha', 'Monto Pagado']
        ],
        body: pagoRows,
        theme: 'striped',
        headStyles: {
            fillColor: [41, 128, 185]
        },
        margin: {
            left: 20
        },
        tableWidth: 170
    });

    // Pie de página
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(10);
        doc.text('Página ' + i + ' de ' + pageCount, 105, doc.internal.pageSize.height - 10, {
            align: 'center'
        });
    }

    // Descargar el PDF
    doc.save('DetalleMultaPlaca_' + placa + '.pdf');
}

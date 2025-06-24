/**
 * Archivo para manejar las notificaciones con iziToast
 * Basado en la biblioteca iziToast: https://marcelodolza.github.io/iziToast/
 */

// Función para mostrar notificaciones con iziToast
function mostrarNotificacion(tipo, mensaje) {
    // Configuración base para todas las notificaciones
    const configuracionBase = {
        position: 'topRight',
        timeout: 5000,
        close: true,
        closeOnEscape: true,
        progressBar: true,
        pauseOnHover: true,
        displayMode: 2, // Reemplaza notificaciones del mismo tipo
        message: mensaje,
        transitionIn: 'fadeInDown',
        transitionOut: 'fadeOutUp',
    };

    // Configuraciones específicas según el tipo de notificación
    switch (tipo) {
        case 'success':
            iziToast.success({
                ...configuracionBase,
                title: 'Éxito',
                titleColor: '#ffffff',
                messageColor: '#ffffff',
                backgroundColor: '#28a745',
                icon: 'fas fa-check-circle',
                iconColor: '#ffffff'
            });
            break;
        case 'error':
            iziToast.error({
                ...configuracionBase,
                title: 'Error',
                titleColor: '#ffffff',
                messageColor: '#ffffff',
                backgroundColor: '#dc3545',
                icon: 'fas fa-exclamation-circle',
                iconColor: '#ffffff'
            });
            break;
        case 'warning':
            iziToast.warning({
                ...configuracionBase,
                title: 'Advertencia',
                titleColor: '#ffffff',
                messageColor: '#ffffff',
                backgroundColor: '#ffc107',
                icon: 'fas fa-exclamation-triangle',
                iconColor: '#ffffff'
            });
            break;
        case 'info':
            iziToast.info({
                ...configuracionBase,
                title: 'Información',
                titleColor: '#ffffff',
                messageColor: '#ffffff',
                backgroundColor: '#17a2b8',
                icon: 'fas fa-info-circle',
                iconColor: '#ffffff'
            });
            break;
        default:
            iziToast.show({
                ...configuracionBase,
                title: 'Notificación',
                titleColor: '#ffffff',
                messageColor: '#ffffff',
                backgroundColor: '#6c757d',
                icon: 'fas fa-bell',
                iconColor: '#ffffff'
            });
    }
}

// Función para mostrar múltiples errores
function mostrarErrores(errores) {
    if (Array.isArray(errores) && errores.length > 0) {
        // Crear mensaje HTML con lista de errores
        let mensajeHTML = '<ul style="padding-left: 20px; margin: 0;">';
        errores.forEach(error => {
            mensajeHTML += `<li>${error}</li>`;
        });
        mensajeHTML += '</ul>';

        // Mostrar notificación con la lista de errores
        iziToast.error({
            position: 'topRight',
            timeout: 8000, // Más tiempo para leer múltiples errores
            close: true,
            closeOnEscape: true,
            progressBar: true,
            pauseOnHover: true,
            displayMode: 2,
            title: 'Errores:',
            titleColor: '#ffffff',
            messageColor: '#ffffff',
            backgroundColor: '#dc3545',
            icon: 'fas fa-exclamation-circle',
            iconColor: '#ffffff',
            message: mensajeHTML,
            transitionIn: 'fadeInDown',
            transitionOut: 'fadeOutUp',
        });
    }
}

// Función para procesar respuestas JSON del servidor
function procesarRespuestaJSON(respuesta) {
    if (respuesta.exito) {
        mostrarNotificacion('success', respuesta.mensaje);
        return true;
    } else {
        if (respuesta.errores && Array.isArray(respuesta.errores)) {
            mostrarErrores(respuesta.errores);
        } else {
            mostrarNotificacion('error', 'Ha ocurrido un error inesperado');
        }
        return false;
    }
}
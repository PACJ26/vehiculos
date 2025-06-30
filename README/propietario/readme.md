# Módulo de Gestión de Propietarios

## Componentes Principales

### 1. Modelo (`app/models/Propietario.php`)
- Entidad que representa a un propietario de vehículo
- Atributos:
  - Datos personales: nombre, apellido, documento
  - Contacto: teléfono, correo
  - Estado (Activo/Inactivo)
- Métodos:
  - Getters/Setters para acceso controlado
  - `toArray()` para conversión a formato JSON

### 2. Servicio (`app/services/PropietarioService.php`)
- Lógica de negocio y operaciones con base de datos
- Funcionalidades clave:
  - CRUD completo con validación de unicidad (documento/correo)
  - Eliminación segura (verificación de vehículos asociados)
  - Búsqueda por múltiples campos
  - Integración con módulo de Vehículos al cambiar estado

### 3. Controlador (`app/controllers/PropietarioController.php`)
- Manejo de peticiones HTTP/REST
- Endpoints principales:
  - Creación/Edición con validación de datos
  - Eliminación lógica (cambio a Inactivo)
  - Búsqueda por término
  - Obtención de datos para formularios
- Respuestas estandarizadas en formato JSON

## Flujo de Operación
1. Validación estricta de datos en controlador
2. Transformación a objeto Propietario
3. Ejecución de operación en capa de servicio
4. Manejo de relaciones con vehículos (actualización en cascada)
5. Retorno de respuesta estructurada

## Validaciones Implementadas
- Unicidad de documento y correo
- Formato correcto de teléfono (numérico)
- Email válido
- Estado permitido (Activo/Inactivo)
- Prevención de eliminación con vehículos asociados
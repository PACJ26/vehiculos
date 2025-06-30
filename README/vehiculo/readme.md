# Módulo de Mantenimiento de Vehículos

## Estructura MVC

### 1. Modelo (`app/models/Mantenimiento.php`)
- Clase que representa la entidad de mantenimiento
- Atributos:
  - Datos principales: ID, vehículo, tipo, costo, fecha
  - Archivos adjuntos: imágenes (antes/después) y PDF
  - Datos adicionales del vehículo (placa, marca, modelo)
- Métodos: Getters/Setters para acceso a propiedades

### 2. Servicio (`app/services/MantenimientoService.php`)
- Capa de negocio para operaciones con la base de datos
- Funcionalidades clave:
  - CRUD completo de registros
  - Consultas JOIN con tabla de vehículos
  - Eliminación física de archivos adjuntos
  - Métodos helper para creación/actualización desde arrays

### 3. Controlador (`app/controllers/MantenimientoController.php`)
- Manejo de solicitudes HTTP
- Acciones principales:
  - Creación/Edición con validación de datos
  - Gestión de uploads de imágenes y PDF
  - Eliminación de registros
  - Búsqueda por ID
- Integración con:
  - Servicio de Mantenimiento
  - Controlador de Vehículos para datos relacionados

## Flujo de trabajo típico
1. El controlador recibe una solicitud
2. Valida datos y archivos adjuntos
3. Crea array estructurado con los datos
4. Invoca métodos del servicio para persistencia
5. Gestiona respuesta JSON al cliente

CREATE DATABASE vehiculos;
USE vehiculos;


/* Tabla propietarios*/
CREATE TABLE propietarios (
    id_propietario INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    documento VARCHAR(50) UNIQUE NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo' NOT NULL
);

/* Tabla vehículos */
CREATE TABLE vehiculos (
    id_vehiculo INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) UNIQUE NOT NULL,
    clase VARCHAR(30) NOT NULL,
    marca VARCHAR(30) NOT NULL,
    linea VARCHAR(30) NOT NULL,
    modelo VARCHAR(30) NOT NULL,
    color VARCHAR(30),
    imagen_url VARCHAR(255),
    id_propietario INT,
    estado ENUM('Activo', 'Inactivo', 'En mantenimiento') DEFAULT 'Activo' NOT NULL,
    FOREIGN KEY (id_propietario) REFERENCES propietarios(id_propietario) ON DELETE SET NULL
);

/* Tabla seguros */
CREATE TABLE seguros (
    id_seguro INT AUTO_INCREMENT PRIMARY KEY,
    id_vehiculo INT,
    aseguradora VARCHAR(50) NOT NULL,
    tipo_poliza VARCHAR(50) NOT NULL,
    costo DECIMAL(10,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    dias_restantes INT,
    estado ENUM('Vigente', 'Expirado', 'Cancelado') DEFAULT 'Vigente',
    archivo_pdf varchar (255),
    FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id_vehiculo) ON DELETE RESTRICT
);


/* Tabla multas */
CREATE TABLE multas (
    id_multa INT AUTO_INCREMENT PRIMARY KEY,
    id_vehiculo INT,
    monto_original DECIMAL(10,2) DEFAULT 0 NOT NULL,
    monto_pagado DECIMAL(10,2) DEFAULT 0 NOT NULL, 
    metodo_pago ENUM('Efectivo', 'Tarjeta', 'Transferencia') NOT NULL,
    motivo TEXT NOT NULL,
    fecha DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    dias_restantes INT,
    estado ENUM('Pendiente', 'Pagado', 'Anulado', 'Expirado') DEFAULT 'Pendiente',
    FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id_vehiculo) ON DELETE RESTRICT
);

/* Tabla pagos_multas */
CREATE TABLE pagos_multas (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_multa INT,
    fecha_pago DATE NOT NULL,
    monto_pagado DECIMAL(10,2) NOT NULL,
    pagos_pdf varchar (255),
    FOREIGN KEY (id_multa) REFERENCES multas(id_multa) ON DELETE SET NULL
);

/* Tabla registros_mantenimiento */
CREATE TABLE registros_mantenimiento (
    id_registro INT AUTO_INCREMENT PRIMARY KEY,
    id_vehiculo INT,
    tipo_mantenimiento VARCHAR(100) NOT NULL,
    costo DECIMAL(10,2),
    fecha DATE NOT NULL,
    archivo_pdf varchar (255),
    FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id_vehiculo) ON DELETE RESTRICT
);

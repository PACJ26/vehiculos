-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: vehiculos
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `multas`
--

DROP TABLE IF EXISTS `multas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `multas` (
  `id_multa` int NOT NULL AUTO_INCREMENT,
  `id_vehiculo` int DEFAULT NULL,
  `monto_original` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metodo_pago` enum('Efectivo','Tarjeta','Transferencia') NOT NULL,
  `motivo` text NOT NULL,
  `fecha` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `dias_restantes` int DEFAULT NULL,
  `estado` enum('Pendiente','Pagado','Anulado','Expirado') DEFAULT 'Pendiente',
  PRIMARY KEY (`id_multa`),
  KEY `id_vehiculo` (`id_vehiculo`),
  CONSTRAINT `multas_ibfk_1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multas`
--

LOCK TABLES `multas` WRITE;
/*!40000 ALTER TABLE `multas` DISABLE KEYS */;
/*!40000 ALTER TABLE `multas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos_multas`
--

DROP TABLE IF EXISTS `pagos_multas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos_multas` (
  `id_pago` int NOT NULL AUTO_INCREMENT,
  `id_multa` int DEFAULT NULL,
  `fecha_pago` date NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL,
  `pagos_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `id_multa` (`id_multa`),
  CONSTRAINT `pagos_multas_ibfk_1` FOREIGN KEY (`id_multa`) REFERENCES `multas` (`id_multa`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_multas`
--

LOCK TABLES `pagos_multas` WRITE;
/*!40000 ALTER TABLE `pagos_multas` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos_multas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `propietarios`
--

DROP TABLE IF EXISTS `propietarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `propietarios` (
  `id_propietario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `documento` varchar(50) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_propietario`),
  UNIQUE KEY `documento` (`documento`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `propietarios`
--

LOCK TABLES `propietarios` WRITE;
/*!40000 ALTER TABLE `propietarios` DISABLE KEYS */;
INSERT INTO `propietarios` VALUES (1,'C.I. FAMAR','S.A','800078508','0354240099','SINDUSTRIAL@CIFAMAR.COM','Activo'),(2,'TRANSPORTE E INVERSIONES','MESA RODRIGUEZ','901276309','3106723301','JUSTOPASTORMESAMARTINEZ@GMAIL.COM','Activo'),(3,'AMVER TRADING','INT. S.A','900877509','3015116851','AMVERTRADINGQ@MAIL.COM','Activo');
/*!40000 ALTER TABLE `propietarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registros_mantenimiento`
--

DROP TABLE IF EXISTS `registros_mantenimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registros_mantenimiento` (
  `id_registro` int NOT NULL AUTO_INCREMENT,
  `id_vehiculo` int DEFAULT NULL,
  `tipo_mantenimiento` varchar(100) NOT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `fecha` date NOT NULL,
  `archivo_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_registro`),
  KEY `id_vehiculo` (`id_vehiculo`),
  CONSTRAINT `registros_mantenimiento_ibfk_1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registros_mantenimiento`
--

LOCK TABLES `registros_mantenimiento` WRITE;
/*!40000 ALTER TABLE `registros_mantenimiento` DISABLE KEYS */;
INSERT INTO `registros_mantenimiento` VALUES (1,1,'CORRECTIVO CAMBIO LLANTAS (2 DELANTERAS, 2 TRASERAS)',10.00,'2025-04-21',NULL);
/*!40000 ALTER TABLE `registros_mantenimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguros`
--

DROP TABLE IF EXISTS `seguros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguros` (
  `id_seguro` int NOT NULL AUTO_INCREMENT,
  `id_vehiculo` int DEFAULT NULL,
  `aseguradora` varchar(50) NOT NULL,
  `tipo_poliza` varchar(50) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `dias_restantes` int DEFAULT NULL,
  `estado` enum('Vigente','Expirado','Cancelado') DEFAULT 'Vigente',
  `archivo_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_seguro`),
  KEY `id_vehiculo` (`id_vehiculo`),
  CONSTRAINT `seguros_ibfk_1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguros`
--

LOCK TABLES `seguros` WRITE;
/*!40000 ALTER TABLE `seguros` DISABLE KEYS */;
INSERT INTO `seguros` VALUES (1,1,'SEGUROS MUNDIAL','SOAT',884700.00,'2025-02-09','2026-02-09',270,'Vigente',NULL),(2,1,'SEGUROS MUNDIAL','TODO RIESGO',67200000.00,'2025-04-23','2026-04-23',343,'Vigente',NULL),(3,2,'SEGUROS MUNDIAL','SOAT',968800.00,'2024-12-20','2025-12-19',218,'Vigente',NULL),(4,3,'SEGUROS MUNDIAL','SOAT',1116800.00,'2025-02-04','2026-02-03',264,'Vigente',NULL),(5,4,'SEGUROS MUNDIAL','SOAT',1222900.00,'2024-11-14','2025-11-13',182,'Vigente',NULL),(6,4,'CDA LA PRADERA','CERTIFICADO REVISION TECNOMECANICA Y EMISIONES CON',329961.00,'2025-02-05','2026-02-05',266,'Vigente',NULL),(7,1,'CDA SUPER CARS E.U','CERTIFICADO REVISION TECNOMECANICA Y EMISIONES CON',533901.00,'2024-08-16','2025-08-16',93,'Vigente',NULL),(8,2,'CDA COMERCIALIZADORA SERVISUPER LTDA STA MTA	','CERTIFICADO REVISION TECNOMECANICA Y EMISIONES CON',533901.00,'2024-12-23','2025-12-23',222,'Vigente',NULL),(9,3,'CDA SUPER CARS E.U','CERTIFICADO REVISION TECNOMECANICA Y EMISIONES CON',329961.00,'2025-02-13','2026-02-13',274,'Vigente',NULL),(10,5,'CDA SUPER CARS LA CONCORDIA','CERTIFICADO REVISION TECNOMECANICA Y EMISIONES CON',534301.00,'2025-01-16','2026-01-16',246,'Vigente',NULL),(11,5,'BOLIVAR','SOAT',1768900.00,'2024-12-16','2025-12-15',214,'Vigente',NULL),(12,8,'METROCAR S.E.S','CERTIFICADO REVISION TECNOMECANICA Y EMISIONES CON',534301.00,'2024-09-14','2025-09-14',122,'Vigente',NULL),(13,8,'BOLIVAR','SOAT',1615500.00,'2025-04-05','2026-04-04',324,'Vigente',NULL),(14,7,'PREVISORA','SOAT',1615500.00,'2025-03-01','2026-06-28',409,'Vigente',NULL),(15,6,'BOLIVAR','SOAT',1200000.00,'2024-09-28','2025-09-27',135,'Vigente',NULL),(16,6,'METROCAR S.E.S','CERTIFICADO REVISION TECNOMECANICA Y EMISIONES CON',534301.00,'2024-11-17','2025-11-17',186,'Vigente',NULL),(17,5,'CDA SUPER CARS LA CORDIALIDAD','CERTIFICADO REVISION TECNOMECANICA Y EMISIONES CON',534301.00,'2025-01-16','2026-01-16',246,'Vigente',NULL),(18,5,'BOLIVAR','SOAT',1768900.00,'2024-12-16','2025-12-15',214,'Vigente',NULL);
/*!40000 ALTER TABLE `seguros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculos` (
  `id_vehiculo` int NOT NULL AUTO_INCREMENT,
  `placa` varchar(10) NOT NULL,
  `clase` varchar(30) NOT NULL,
  `marca` varchar(30) NOT NULL,
  `linea` varchar(30) NOT NULL,
  `modelo` varchar(30) NOT NULL,
  `color` varchar(30) DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `id_propietario` int DEFAULT NULL,
  `estado` enum('Activo','Inactivo','En mantenimiento') NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `placa` (`placa`),
  KEY `id_propietario` (`id_propietario`),
  CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`id_propietario`) REFERENCES `propietarios` (`id_propietario`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (1,'SJK-767','FURGON CARGA O MIXTO','NISSAN','U41','2012','BLANCO',NULL,1,'Activo'),(2,'AWK-673','FURGON CARGA O MIXTO','MAZDA','T45','1997','ROJO',NULL,1,'Activo'),(3,'QFG-765','CAMIONETA','TOYOTA HILUX','PICK UP','2009','PLATEADO METALICO',NULL,1,'Activo'),(4,'QFG-780','CAMIONETA','TOYOTA HILUX	','PICK UP','2009','PLATEADO METALICO',NULL,1,'Activo'),(5,'SZK-473','TRACTOCAMION','KENWORTH','T800','2012','AMARILLO',NULL,2,'Activo'),(6,'STR-947','TRACTOCAMION','KENWORTH','T800B','2012','NARANJA MARRON PERLA',NULL,2,'Activo'),(7,'SOR-120','TRACTOCAMION','INTERNATIONAL PROSTAR','PROSTAR','2012','BLANCO',NULL,3,'Activo'),(8,'UYZ-490','TRACTOCAMION','KENWORTH','T800','2008','ROJO GRIS	',NULL,2,'Activo');
/*!40000 ALTER TABLE `vehiculos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-14 21:45:13

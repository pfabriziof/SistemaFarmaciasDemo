-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 14, 2024 at 08:17 PM
-- Server version: 8.0.36-0ubuntu0.22.04.1
-- PHP Version: 8.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_pharmacy`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `prd_ventas` ()  NO SQL
SELECT
    t1.month,
    ifnull(sum(t2.amount),0) AS expenses,
    ifnull(sum(t3.amount),0) AS incomes
FROM
(
    
    
    SELECT fechas.fecha AS month
FROM (
SELECT 
(DATE(NOW())) fecha UNION
SELECT 
(DATE(NOW()) - INTERVAL 1 DAY) fecha UNION 
SELECT 
(DATE(NOW()) - INTERVAL 2 DAY) fecha UNION 
SELECT 
(DATE(NOW()) - INTERVAL 3 DAY) fecha UNION 
SELECT 
(DATE(NOW()) - INTERVAL 4 DAY) fecha UNION 
SELECT 
(DATE(NOW()) - INTERVAL 5 DAY) fecha UNION 
SELECT 
(DATE(NOW()) - INTERVAL 6 DAY) fecha UNION 
SELECT 
(DATE(NOW()) - INTERVAL 7 DAY) fecha) as fechas
    
    
) t1

LEFT JOIN
(
    
    SELECT c.`fecha_emision` AS month, ifnull(sum(total),0) AS amount
FROM `comprobantes` as c
GROUP BY c.`fecha_emision`
    
    
) t2
    ON t1.month = t2.month
LEFT JOIN
(
    
    
SELECT DATE(e.`fecha_egreso`) AS month, ifnull(sum(monto),0)  AS amount
FROM `egresos` as e
GROUP BY DATE(e.`fecha_egreso`)
    
    
) t3
    ON t1.month = t3.month
 GROUP BY t1.month
ORDER BY
    t1.month$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `prd_ventas_today` ()  NO SQL
SELECT
    t1.month,
    ifnull(sum(t2.amount),0) AS ventas,
    ifnull(sum(t3.amount),0) AS egresos
FROM
(
    
    
    SELECT DATE(NOW()) AS month

) t1

LEFT JOIN
(
    
    SELECT c.`fecha_emision` AS month, ifnull(sum(total),0) AS amount
FROM `comprobantes` as c
GROUP BY c.`fecha_emision`
    
    
) t2
    ON t1.month = t2.month
LEFT JOIN
(
    
    
SELECT DATE(e.`fecha_egreso`) AS month, ifnull(sum(monto),0)  AS amount
FROM `egresos` as e
GROUP BY DATE(e.`fecha_egreso`)
    
    
) t3
    ON t1.month = t3.month
 GROUP BY t1.month
ORDER BY
    t1.month$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `almacen_movimientos`
--

CREATE TABLE `almacen_movimientos` (
  `id_almacen_movimientos` bigint UNSIGNED NOT NULL,
  `id_sucursal` int UNSIGNED NOT NULL,
  `id_usuario` bigint UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `NombreProducto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_tipo_movimiento` tinyint UNSIGNED NOT NULL,
  `id_unidad_medida` int UNSIGNED NOT NULL,
  `und_simbolo` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad` decimal(11,2) DEFAULT NULL,
  `precioUnitario` decimal(11,2) DEFAULT NULL,
  `precioTotal` decimal(11,2) DEFAULT NULL,
  `stock_actual` decimal(11,2) DEFAULT NULL,
  `fecha_movimiento` date DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `almacen_movimientos`
--

INSERT INTO `almacen_movimientos` (`id_almacen_movimientos`, `id_sucursal`, `id_usuario`, `id_producto`, `NombreProducto`, `id_tipo_movimiento`, `id_unidad_medida`, `und_simbolo`, `cantidad`, `precioUnitario`, `precioTotal`, `stock_actual`, `fecha_movimiento`, `updated_at`, `created_at`) VALUES
(27, 1, 2, 1, 'Producto 1', 1, 2, 'UND', '1000.00', '4.00', '4000.00', '1995.00', '2024-03-12', '2024-03-13 02:07:41', '2024-03-13 02:07:41'),
(28, 1, 2, 2, 'Producto 2', 1, 2, 'UND', '1000.00', '0.60', '600.00', '2692.00', '2024-03-12', '2024-03-13 02:07:43', '2024-03-13 02:07:43'),
(29, 1, 2, 1, 'Producto 1', 3, 2, 'UND', '1000.00', '4.00', '4000.00', '995.00', '2024-03-12', '2024-03-13 02:08:45', '2024-03-13 02:08:45'),
(30, 1, 2, 2, 'Producto 2', 3, 2, 'UND', '1000.00', '0.60', '600.00', '1692.00', '2024-03-12', '2024-03-13 02:08:46', '2024-03-13 02:08:46'),
(31, 1, 2, 3, 'Producto 3', 3, 2, 'UND', '1000.00', '65.00', '1950.00', '935.00', '2024-03-12', '2024-03-13 02:35:27', '2024-03-13 02:09:27'),
(32, 1, 2, 1, 'Producto 1', 1, 2, 'UND', '2500.00', '1.00', '500.00', '4500.00', '2024-03-13', '2024-03-14 01:43:50', '2024-03-14 01:43:50'),
(33, 1, 2, 2, 'Producto 2', 1, 2, 'UND', '500.00', '1.00', '500.00', '2500.00', '2024-03-13', '2024-03-14 01:43:51', '2024-03-14 01:43:51'),
(34, 1, 2, 3, 'Producto 3', 1, 2, 'UND', '500.00', '2.00', '1000.00', '2500.00', '2024-03-13', '2024-03-14 01:43:52', '2024-03-14 01:43:52'),
(35, 1, 2, 1, 'Producto 1', 2, 2, 'UND', '5.00', '2.00', '10.00', '4495.00', '2024-03-13', '2024-03-14 01:54:58', '2024-03-14 01:54:58'),
(36, 1, 2, 2, 'Producto 2', 2, 2, 'UND', '5.00', '2.00', '10.00', '2495.00', '2024-03-13', '2024-03-14 01:54:59', '2024-03-14 01:54:59'),
(37, 1, 2, 3, 'Producto 3', 2, 2, 'UND', '5.00', '4.00', '20.00', '2495.00', '2024-03-13', '2024-03-14 01:55:00', '2024-03-14 01:55:00'),
(38, 1, 2, 1, 'Producto 1', 2, 2, 'UND', '5.00', '2.00', '10.00', '4490.00', '2024-03-13', '2024-03-14 01:55:46', '2024-03-14 01:55:46'),
(39, 1, 2, 2, 'Producto 2', 2, 2, 'UND', '5.00', '2.00', '10.00', '2490.00', '2024-03-13', '2024-03-14 01:55:48', '2024-03-14 01:55:48'),
(40, 1, 2, 3, 'Producto 3', 2, 2, 'UND', '5.00', '4.00', '20.00', '2490.00', '2024-03-13', '2024-03-14 01:55:49', '2024-03-14 01:55:49'),
(41, 1, 2, 1, 'Producto 1', 2, 2, 'UND', '5.00', '2.00', '10.00', '4485.00', '2024-03-13', '2024-03-14 01:56:32', '2024-03-14 01:56:32'),
(42, 1, 2, 2, 'Producto 2', 2, 2, 'UND', '5.00', '2.00', '10.00', '2485.00', '2024-03-13', '2024-03-14 01:56:33', '2024-03-14 01:56:33'),
(43, 1, 2, 3, 'Producto 3', 2, 2, 'UND', '5.00', '4.00', '20.00', '2485.00', '2024-03-13', '2024-03-14 01:56:34', '2024-03-14 01:56:34');

-- --------------------------------------------------------

--
-- Table structure for table `caja`
--

CREATE TABLE `caja` (
  `id_caja` bigint UNSIGNED NOT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `id_usuario` bigint UNSIGNED DEFAULT NULL,
  `fecha_apertura` timestamp NULL DEFAULT NULL,
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  `monto_apertura` decimal(11,2) DEFAULT NULL,
  `monto_cierre` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `caja`
--

INSERT INTO `caja` (`id_caja`, `id_sucursal`, `id_usuario`, `fecha_apertura`, `fecha_cierre`, `monto_apertura`, `monto_cierre`) VALUES
(4, 1, 2, '2024-03-14 01:21:14', NULL, '0.00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `caja_detalle`
--

CREATE TABLE `caja_detalle` (
  `id_caja_det` bigint UNSIGNED NOT NULL,
  `id_caja` bigint UNSIGNED DEFAULT NULL,
  `id_medio_pago` tinyint UNSIGNED DEFAULT NULL,
  `monto` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` bigint UNSIGNED NOT NULL,
  `id_tipo_doc` tinyint UNSIGNED DEFAULT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `tipo_cliente` tinyint UNSIGNED DEFAULT NULL COMMENT '1=Interno, 2=Distribuidor',
  `nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_doc` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_telefono` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_departamento` int DEFAULT NULL,
  `id_provincia` int DEFAULT NULL,
  `id_distrito` int DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `id_tipo_doc`, `id_sucursal`, `tipo_cliente`, `nombre`, `nro_doc`, `email`, `telefono`, `contacto_nombre`, `contacto_telefono`, `id_departamento`, `id_provincia`, `id_distrito`, `estado`, `updated_at`, `created_at`) VALUES
(1, 2, 1, 1, 'CLIENTES VARIOS', '00000000', NULL, NULL, NULL, NULL, 4, 401, 40101, 1, '2024-02-03 14:46:49', '2024-02-03 14:43:57'),
(2, 2, 1, 1, 'RENZO PAREDES RAMIREZ', '12345678', NULL, NULL, NULL, NULL, 15, 1501, 150122, 1, '2024-02-03 14:47:59', '2024-02-03 14:47:59'),
(3, 2, 1, 1, 'LUIS FERNANDO JAMANCA TREJO', '45678942', NULL, NULL, NULL, NULL, 6, 601, 60101, 1, '2024-02-26 00:55:36', '2024-02-26 00:55:36');

-- --------------------------------------------------------

--
-- Table structure for table `cliente_direcciones`
--

CREATE TABLE `cliente_direcciones` (
  `id_direccion` bigint UNSIGNED NOT NULL,
  `id_cliente` bigint UNSIGNED NOT NULL,
  `direccion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cliente_direcciones`
--

INSERT INTO `cliente_direcciones` (`id_direccion`, `id_cliente`, `direccion`, `estado`) VALUES
(1, 1, 'Calle Las Rosas 123', 1),
(2, 1, 'Calle Las Petunias 456', 1),
(3, 2, 'URB. LOS GIRASOLES 123', 1),
(4, 3, 'Urb. Rosales G-23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `compras`
--

CREATE TABLE `compras` (
  `id_compra` bigint UNSIGNED NOT NULL,
  `id_usuario` bigint UNSIGNED DEFAULT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `id_proveedor` bigint UNSIGNED DEFAULT NULL,
  `id_moneda` tinyint UNSIGNED DEFAULT NULL,
  `id_medio_pago` tinyint UNSIGNED DEFAULT NULL,
  `id_tipo_cambio` tinyint UNSIGNED DEFAULT NULL,
  `id_tipo_comprobante` tinyint UNSIGNED DEFAULT NULL,
  `correlativo` bigint DEFAULT NULL,
  `nombreProveedor` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nroDocProveedor` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_guia_remision` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serie_factura` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_factura` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `origen_dinero` tinyint DEFAULT NULL COMMENT '1=Caja Chica, 2=Cuenta Bancaria, 3=Otra Fuente',
  `op_inafectas` decimal(11,2) DEFAULT NULL,
  `op_exoneradas` decimal(11,2) DEFAULT NULL,
  `op_gravadas` decimal(11,2) DEFAULT NULL,
  `icbper` decimal(11,2) DEFAULT NULL,
  `porcentaje_igv` decimal(11,2) DEFAULT NULL,
  `igv` decimal(11,2) DEFAULT NULL,
  `total` decimal(11,2) DEFAULT NULL,
  `deuda_id` bigint UNSIGNED DEFAULT NULL,
  `deuda_generada` tinyint(1) DEFAULT NULL,
  `deuda_adelanto` decimal(11,2) DEFAULT NULL,
  `id_estado` tinyint UNSIGNED NOT NULL DEFAULT '3' COMMENT '1=Aprobada, 2=Desestimada, 3=Espera',
  `fecha_anulacion` date DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compras`
--

INSERT INTO `compras` (`id_compra`, `id_usuario`, `id_sucursal`, `id_proveedor`, `id_moneda`, `id_medio_pago`, `id_tipo_cambio`, `id_tipo_comprobante`, `correlativo`, `nombreProveedor`, `nroDocProveedor`, `email`, `nro_guia_remision`, `serie_factura`, `nro_factura`, `fecha_emision`, `fecha_vencimiento`, `origen_dinero`, `op_inafectas`, `op_exoneradas`, `op_gravadas`, `icbper`, `porcentaje_igv`, `igv`, `total`, `deuda_id`, `deuda_generada`, `deuda_adelanto`, `id_estado`, `fecha_anulacion`, `updated_at`, `created_at`) VALUES
(1, 2, 1, 1, 1, 1, 1, 1, 1, 'PROVEEDOR INTERNO', '12345678912', 'proveedorinterno@gmail.com', NULL, 'F001', '12345612', '2024-03-13', '2024-03-13', 1, '0.00', '0.00', '1694.92', '0.00', '18.00', '305.08', '2000.00', NULL, NULL, NULL, 1, NULL, '2024-03-14 01:43:48', '2024-03-14 01:43:43');

-- --------------------------------------------------------

--
-- Table structure for table `compras_detalle`
--

CREATE TABLE `compras_detalle` (
  `id_compra_detalle` bigint UNSIGNED NOT NULL,
  `id_compra` bigint UNSIGNED DEFAULT NULL,
  `id_producto` bigint UNSIGNED DEFAULT NULL,
  `nombre_producto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_unidad_medida` int UNSIGNED DEFAULT NULL,
  `und_simbolo` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_lista_detalle` bigint UNSIGNED DEFAULT NULL,
  `lote_id` bigint UNSIGNED DEFAULT NULL,
  `lote_name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lote_fecha_exp` date DEFAULT NULL,
  `cantidad` decimal(11,2) DEFAULT NULL,
  `cantidad_visual` decimal(11,2) DEFAULT NULL,
  `precio_unitario` decimal(11,2) DEFAULT NULL,
  `precio_total` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compras_detalle`
--

INSERT INTO `compras_detalle` (`id_compra_detalle`, `id_compra`, `id_producto`, `nombre_producto`, `id_unidad_medida`, `und_simbolo`, `id_lista_detalle`, `lote_id`, `lote_name`, `lote_fecha_exp`, `cantidad`, `cantidad_visual`, `precio_unitario`, `precio_total`) VALUES
(7, 1, 1, 'Producto 1', 2, 'UND', 1, 12, 'L1003', '2024-12-31', '2500.00', '500.00', '1.00', '500.00'),
(8, 1, 2, 'Producto 2', 2, 'UND', 4, 13, 'L2003', '2024-12-31', '500.00', '500.00', '1.00', '500.00'),
(9, 1, 3, 'Producto 3', 2, 'UND', 7, 14, 'L3003', '2024-12-31', '500.00', '500.00', '2.00', '1000.00');

-- --------------------------------------------------------

--
-- Table structure for table `compra_estado`
--

CREATE TABLE `compra_estado` (
  `id` tinyint UNSIGNED NOT NULL,
  `estado` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compra_estado`
--

INSERT INTO `compra_estado` (`id`, `estado`) VALUES
(1, 'Aprobada'),
(2, 'Desestimada'),
(3, 'En espera');

-- --------------------------------------------------------

--
-- Table structure for table `compressed_tables`
--

CREATE TABLE `compressed_tables` (
  `id` tinyint UNSIGNED NOT NULL,
  `title` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `query` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compressed_tables`
--

INSERT INTO `compressed_tables` (`id`, `title`, `table_name`, `query`, `prompt`) VALUES
(1, 'Productos', 'productos_servicios', 'TABLE productos_servicios(id_producto int,codigo_producto varchar,id_marca int,id_categoria int,id_unidad_medida int,id_sucursal int,nombreProducto varchar,stock decimal,servicio tinyint COMMENT \'0=no, 1=si\',registro_sanitario varchar,vigencia_registro date,estado tinyint COMMENT \'0=inactivo, 1=activo\')\r\nTABLE marcas_productos (id_marca int,marca varchar,estado tinyint COMMENT \'0=inactivo,1=activo\')', 'Si existen los siguientes datos en la consulta, debes incluirlos: codigo producto, nombre producto, stock, stock minimo, servicio, ubicacion, registro sanitario y marca.'),
(2, 'Comprobantes', 'comprobantes', 'TABLE comprobantes(id_comprobante int,id_tipo_comprobante tinyint,id_estado_comprobante tinyint,id_serie int,correlativo int,nombreCliente varchar,nroDocCliente varchar,fecha_emision date,igv float,total float) TABLE comprobantes_detalle(id int,id_comprobante int, nombre_producto,precio_unitario, cantidad) TABLE tipos_comprobante(id int,tipo_comprobante varchar)', 'Si existen los siguientes datos en la consulta, debes incluirlos: tipo_comprobante, serie, correlativo, nombreCliente, nroDocCliente, fecha_emision, igv, total, nombre_producto, precio_unitario, cantidad.'),
(3, 'Compras', 'compras', 'TABLE compras(id_compra int,id_sucursal int, id_tipo_comprobante tinyint,nombreProveedor varchar,nroDocProveedor varchar,nro_guia_remision varchar, serie_factura varchar, nro_factura,fecha_emision date,igv float,total float) TABLE compras_detalle(id int,id_compra int, nombre_producto,lote_name varchar,precio_unitario, cantidad) TABLE tipos_comprobante(id int,tipo_comprobante varchar)', 'Si existen los siguientes datos en la consulta, debes incluirlos: tipo_comprobante,nombreProveedor,nroDocProveedor,serie_factura,nro_factura,nro_guia_remision,fecha_emision,igv,total,nombre_producto,precio_unitario,cantidad,lote_name.');

-- --------------------------------------------------------

--
-- Table structure for table `comprobantes`
--

CREATE TABLE `comprobantes` (
  `id_comprobante` bigint UNSIGNED NOT NULL,
  `id_cliente` bigint UNSIGNED DEFAULT NULL,
  `id_usuario` bigint UNSIGNED DEFAULT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `id_tipo_comprobante` tinyint UNSIGNED DEFAULT NULL,
  `id_moneda` tinyint UNSIGNED DEFAULT NULL,
  `id_estado_comprobante` tinyint UNSIGNED DEFAULT NULL,
  `id_medio_pago` tinyint UNSIGNED DEFAULT NULL,
  `id_tipo_cambio` tinyint UNSIGNED DEFAULT NULL,
  `id_serie` int UNSIGNED DEFAULT NULL,
  `correlativo` bigint DEFAULT NULL,
  `nombreCliente` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nroDocCliente` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccionCliente` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `op_inafectas` decimal(11,2) DEFAULT NULL,
  `op_exoneradas` decimal(11,2) DEFAULT NULL,
  `op_gravadas` decimal(11,2) DEFAULT NULL,
  `icbper` decimal(11,2) DEFAULT NULL,
  `porcentaje_igv` decimal(11,2) DEFAULT NULL,
  `igv` decimal(11,2) DEFAULT NULL,
  `total` decimal(11,2) DEFAULT NULL,
  `external_id` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `formato_impresion` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_anulacion` date DEFAULT NULL,
  `motivo_anulacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comprobantes`
--

INSERT INTO `comprobantes` (`id_comprobante`, `id_cliente`, `id_usuario`, `id_sucursal`, `id_tipo_comprobante`, `id_moneda`, `id_estado_comprobante`, `id_medio_pago`, `id_tipo_cambio`, `id_serie`, `correlativo`, `nombreCliente`, `nroDocCliente`, `direccionCliente`, `fecha_emision`, `fecha_vencimiento`, `comentario`, `op_inafectas`, `op_exoneradas`, `op_gravadas`, `icbper`, `porcentaje_igv`, `igv`, `total`, `external_id`, `formato_impresion`, `fecha_anulacion`, `motivo_anulacion`, `updated_at`, `created_at`) VALUES
(11, 1, 2, 1, 2, 1, 4, 1, 1, 2, 1, 'CLIENTES VARIOS', '00000000', 'Calle Las Rosas 123', '2024-03-13', '2024-03-13', NULL, '0.00', '0.00', '33.90', '0.00', '18.00', '6.10', '40.00', NULL, 'ticket', NULL, NULL, '2024-03-14 01:55:01', '2024-03-14 01:54:57'),
(12, 1, 2, 1, 2, 1, 4, 1, 1, 2, 2, 'CLIENTES VARIOS', '00000000', 'Calle Las Rosas 123', '2024-03-13', '2024-03-13', NULL, '0.00', '0.00', '33.90', '0.00', '18.00', '6.10', '40.00', NULL, 'ticket', NULL, NULL, '2024-03-14 01:55:49', '2024-03-14 01:55:45'),
(13, 1, 2, 1, 2, 1, 4, 1, 1, 2, 3, 'CLIENTES VARIOS', '00000000', 'Calle Las Rosas 123', '2024-03-13', '2024-03-13', NULL, '0.00', '0.00', '33.90', '0.00', '18.00', '6.10', '40.00', NULL, 'ticket', NULL, NULL, '2024-03-14 01:56:35', '2024-03-14 01:56:30');

-- --------------------------------------------------------

--
-- Table structure for table `comprobante_detalle`
--

CREATE TABLE `comprobante_detalle` (
  `id_comp_detalle` bigint UNSIGNED NOT NULL,
  `id_comprobante` bigint UNSIGNED DEFAULT NULL,
  `id_producto` bigint UNSIGNED DEFAULT NULL,
  `nombre_producto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_unidad_medida` int UNSIGNED DEFAULT NULL,
  `und_simbolo` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_lista_detalle` bigint UNSIGNED DEFAULT NULL,
  `id_lote` bigint UNSIGNED DEFAULT NULL,
  `lote_producto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_unitario` decimal(11,2) DEFAULT NULL,
  `cantidad` decimal(11,2) DEFAULT NULL,
  `cantidad_visual` decimal(11,2) DEFAULT NULL,
  `precio_total` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comprobante_detalle`
--

INSERT INTO `comprobante_detalle` (`id_comp_detalle`, `id_comprobante`, `id_producto`, `nombre_producto`, `id_unidad_medida`, `und_simbolo`, `id_lista_detalle`, `id_lote`, `lote_producto`, `precio_unitario`, `cantidad`, `cantidad_visual`, `precio_total`) VALUES
(11, 11, 1, 'Producto 1', 2, 'UND', 1, 12, 'L1003', '2.00', '5.00', '5.00', '10.00'),
(12, 11, 2, 'Producto 2', 2, 'UND', 4, 13, 'L2003', '2.00', '5.00', '5.00', '10.00'),
(13, 11, 3, 'Producto 3', 2, 'UND', 7, 14, 'L3003', '4.00', '5.00', '5.00', '20.00'),
(14, 12, 1, 'Producto 1', 2, 'UND', 1, 12, 'L1003', '2.00', '5.00', '5.00', '10.00'),
(15, 12, 2, 'Producto 2', 2, 'UND', 4, 13, 'L2003', '2.00', '5.00', '5.00', '10.00'),
(16, 12, 3, 'Producto 3', 2, 'UND', 7, 14, 'L3003', '4.00', '5.00', '5.00', '20.00'),
(17, 13, 1, 'Producto 1', 2, 'UND', 1, 12, 'L1003', '2.00', '5.00', '5.00', '10.00'),
(18, 13, 2, 'Producto 2', 2, 'UND', 4, 13, 'L2003', '2.00', '5.00', '5.00', '10.00'),
(19, 13, 3, 'Producto 3', 2, 'UND', 7, 14, 'L3003', '4.00', '5.00', '5.00', '20.00');

-- --------------------------------------------------------

--
-- Table structure for table `comprobante_estados`
--

CREATE TABLE `comprobante_estados` (
  `id_estado_comprobante` tinyint UNSIGNED NOT NULL,
  `estado` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comprobante_estados`
--

INSERT INTO `comprobante_estados` (`id_estado_comprobante`, `estado`) VALUES
(1, 'Aprobado'),
(2, 'Anulado'),
(3, 'Rechazado'),
(4, 'Pendiente');

-- --------------------------------------------------------

--
-- Table structure for table `condiciones_almacenamiento`
--

CREATE TABLE `condiciones_almacenamiento` (
  `id_condicion_alm` int UNSIGNED NOT NULL,
  `descripcion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `condiciones_almacenamiento`
--

INSERT INTO `condiciones_almacenamiento` (`id_condicion_alm`, `descripcion`, `estado`) VALUES
(1, 'Ambiente', 1),
(2, 'Congelado', 1),
(3, 'Temperatura Fría', 1);

-- --------------------------------------------------------

--
-- Table structure for table `deudas_compras`
--

CREATE TABLE `deudas_compras` (
  `id_deuda` bigint UNSIGNED NOT NULL,
  `id_compra` bigint UNSIGNED NOT NULL,
  `id_proveedor` bigint UNSIGNED NOT NULL,
  `total_adelanto` decimal(11,2) DEFAULT NULL,
  `total_deuda` decimal(11,2) DEFAULT NULL,
  `total_monto_pagado` decimal(11,2) DEFAULT NULL,
  `total_monto_pendiente` decimal(11,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deudas_compras_pagos`
--

CREATE TABLE `deudas_compras_pagos` (
  `id_pago` bigint UNSIGNED NOT NULL,
  `id_deuda` bigint UNSIGNED NOT NULL,
  `monto_pagado` decimal(11,2) DEFAULT NULL,
  `comentario` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deudas_comprobantes`
--

CREATE TABLE `deudas_comprobantes` (
  `id_deuda` bigint UNSIGNED NOT NULL,
  `id_comprobante` bigint UNSIGNED NOT NULL,
  `id_cliente` bigint UNSIGNED NOT NULL,
  `total_adelanto` decimal(11,2) DEFAULT NULL,
  `total_deuda` decimal(11,2) DEFAULT NULL,
  `total_monto_pagado` decimal(11,2) DEFAULT NULL,
  `total_monto_pendiente` decimal(11,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deudas_comprobantes_pagos`
--

CREATE TABLE `deudas_comprobantes_pagos` (
  `id_pago` bigint UNSIGNED NOT NULL,
  `id_deuda` bigint UNSIGNED NOT NULL,
  `monto_pagado` decimal(11,2) DEFAULT NULL,
  `comentario` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `egresos`
--

CREATE TABLE `egresos` (
  `id_egreso` bigint UNSIGNED NOT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `id_usuario` bigint UNSIGNED DEFAULT NULL,
  `id_compra` bigint UNSIGNED DEFAULT NULL,
  `id_tipo_egreso` tinyint UNSIGNED DEFAULT NULL,
  `id_motivo_egreso` tinyint UNSIGNED DEFAULT NULL,
  `metodo_gasto` tinyint UNSIGNED DEFAULT NULL COMMENT '1=Caja Chica, 2=Cuenta Bancaria',
  `fecha_egreso` datetime DEFAULT NULL,
  `monto` decimal(11,2) DEFAULT NULL,
  `detalle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `egresos`
--

INSERT INTO `egresos` (`id_egreso`, `id_sucursal`, `id_usuario`, `id_compra`, `id_tipo_egreso`, `id_motivo_egreso`, `metodo_gasto`, `fecha_egreso`, `monto`, `detalle`, `estado`) VALUES
(7, 1, 2, 1, 4, 3, 1, '2024-03-13 20:43:52', '2000.00', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `egreso_motivos`
--

CREATE TABLE `egreso_motivos` (
  `id_egreso_motivo` tinyint UNSIGNED NOT NULL,
  `motivo` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `egreso_motivos`
--

INSERT INTO `egreso_motivos` (`id_egreso_motivo`, `motivo`) VALUES
(1, 'Representación de la empresa'),
(2, 'Trabajo de campo'),
(3, 'Varios');

-- --------------------------------------------------------

--
-- Table structure for table `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` bigint UNSIGNED NOT NULL,
  `ruc` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_file` bigint UNSIGNED DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `ruc`, `nombre`, `id_file`, `estado`, `created_at`, `updated_at`) VALUES
(1, '123456789', 'EMPRESA GENERICA', NULL, 1, '2023-12-16 17:31:43', '2023-12-16 17:31:43');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_paths`
--

CREATE TABLE `file_paths` (
  `id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laboratorios`
--

CREATE TABLE `laboratorios` (
  `id_laboratorio` bigint UNSIGNED NOT NULL,
  `nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laboratorios`
--

INSERT INTO `laboratorios` (`id_laboratorio`, `nombre`, `estado`) VALUES
(1, 'Laboratorio 1', 1),
(2, 'Laboratorio 2', 1),
(3, 'Laboratorio 3', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lista_precios`
--

CREATE TABLE `lista_precios` (
  `id_lista_precio` int UNSIGNED NOT NULL,
  `codigo` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lista_precios`
--

INSERT INTO `lista_precios` (`id_lista_precio`, `codigo`, `nombre`, `estado`) VALUES
(1, 'NIU', 'Unidad', 1),
(2, 'BX', 'Caja', 1),
(3, 'BLT', 'Blister', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lista_precios_detalle`
--

CREATE TABLE `lista_precios_detalle` (
  `id_lista_detalle` bigint UNSIGNED NOT NULL,
  `id_lista_precio` int UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `precio_venta` decimal(11,2) DEFAULT NULL,
  `precio_compra` decimal(11,2) DEFAULT NULL,
  `unidades` int NOT NULL DEFAULT '1',
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lista_precios_detalle`
--

INSERT INTO `lista_precios_detalle` (`id_lista_detalle`, `id_lista_precio`, `id_producto`, `id_sucursal`, `precio_venta`, `precio_compra`, `unidades`, `estado`) VALUES
(1, 1, 1, 1, '2.00', '1.00', 1, 1),
(2, 3, 1, 1, '5.00', '3.00', 10, 1),
(3, 2, 1, 1, '60.00', '30.00', 50, 1),
(4, 1, 2, 1, '2.00', '1.00', 1, 1),
(5, 3, 2, 1, '7.00', '5.00', 8, 1),
(6, 2, 2, 1, '70.00', '45.00', 80, 1),
(7, 1, 3, 1, '4.00', '2.00', 1, 1),
(8, 3, 3, 1, '15.00', '8.00', 10, 1),
(9, 2, 3, 1, '135.00', '70.00', 100, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lote_productos`
--

CREATE TABLE `lote_productos` (
  `id_lote` bigint UNSIGNED NOT NULL,
  `id_sucursal` int UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `lote` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `fecha_expiracion` date NOT NULL,
  `estado` tinyint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lote_productos`
--

INSERT INTO `lote_productos` (`id_lote`, `id_sucursal`, `id_producto`, `lote`, `cantidad`, `fecha_expiracion`, `estado`) VALUES
(1, 1, 1, 'L1001', 1000, '2024-12-31', 1),
(2, 1, 2, 'L2001', 1000, '2024-12-31', 1),
(3, 1, 3, 'L3001', 1000, '2024-12-31', 1),
(4, 1, 3, 'L3002', 1000, '2024-12-31', 1),
(5, 1, 1, 'L1002', 1000, '2024-12-31', 1),
(6, 1, 2, 'L2002', 1000, '2024-12-31', 1),
(12, 1, 1, 'L1003', 2485, '2024-12-31', 1),
(13, 1, 2, 'L2003', 485, '2024-12-31', 1),
(14, 1, 3, 'L3003', 485, '2024-12-31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `marcas_productos`
--

CREATE TABLE `marcas_productos` (
  `id_marca` bigint UNSIGNED NOT NULL,
  `marca` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marcas_productos`
--

INSERT INTO `marcas_productos` (`id_marca`, `marca`, `estado`) VALUES
(1, 'S/M', 1),
(2, 'Marca 1', 1),
(3, 'Marca 2', 1),
(4, 'Marca 3', 1);

-- --------------------------------------------------------

--
-- Table structure for table `medio_pago`
--

CREATE TABLE `medio_pago` (
  `id_medio_pago` tinyint UNSIGNED NOT NULL,
  `medio_pago` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medio_pago`
--

INSERT INTO `medio_pago` (`id_medio_pago`, `medio_pago`) VALUES
(1, 'Efectivo'),
(2, 'Tarjeta'),
(3, 'Depósito');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_08_19_000000_create_failed_jobs_table', 1),
(9, '2022_05_23_110813_create_permission_tables', 1),
(10, '2023_10_01_201125_create_almacen_movimientos_table', 1),
(11, '2023_10_01_201439_create_caja_detalle_table', 1),
(12, '2023_10_01_201439_create_caja_table', 1),
(13, '2023_10_01_201439_create_cliente_direcciones_table', 1),
(14, '2023_10_01_201439_create_clientes_table', 1),
(15, '2023_10_01_201439_create_compra_estado_table', 1),
(16, '2023_10_01_201439_create_compras_detalle_table', 1),
(17, '2023_10_01_201439_create_compras_table', 1),
(18, '2023_10_01_201439_create_comprobante_detalle_table', 1),
(19, '2023_10_01_201439_create_comprobante_estados_table', 1),
(20, '2023_10_01_201439_create_comprobantes_table', 1),
(21, '2023_10_01_201439_create_condiciones_almacenamiento_table', 1),
(22, '2023_10_01_201439_create_deudas_compras_pagos_table', 1),
(23, '2023_10_01_201439_create_deudas_compras_table', 1),
(24, '2023_10_01_201439_create_deudas_comprobantes_pagos_table', 1),
(25, '2023_10_01_201439_create_deudas_comprobantes_table', 1),
(26, '2023_10_01_201439_create_egreso_motivos_table', 1),
(27, '2023_10_01_201439_create_egresos_table', 1),
(28, '2023_10_01_201439_create_empresas_table', 1),
(29, '2023_10_01_201439_create_file_paths_table', 1),
(30, '2023_10_01_201439_create_laboratorios_table', 1),
(31, '2023_10_01_201439_create_lista_precios_detalle_table', 1),
(32, '2023_10_01_201439_create_lista_precios_table', 1),
(33, '2023_10_01_201439_create_lote_productos_table', 1),
(34, '2023_10_01_201439_create_marcas_productos_table', 1),
(35, '2023_10_01_201439_create_medio_pago_table', 1),
(36, '2023_10_01_201439_create_monedas_table', 1),
(37, '2023_10_01_201439_create_orden_compra_detalle_table', 1),
(38, '2023_10_01_201439_create_orden_compra_table', 1),
(39, '2023_10_01_201439_create_producto_tipos_table', 1),
(40, '2023_10_01_201439_create_productos_categorias_table', 1),
(41, '2023_10_01_201439_create_productos_servicios_table', 1),
(42, '2023_10_01_201439_create_proveedores_table', 1),
(43, '2023_10_01_201439_create_prv_cotizacion_detalle_table', 1),
(44, '2023_10_01_201439_create_prv_cotizaciones_table', 1),
(45, '2023_10_01_201439_create_series_inv_table', 1),
(46, '2023_10_01_201439_create_sucursales_table', 1),
(47, '2023_10_01_201439_create_tipo_cambio_table', 1),
(48, '2023_10_01_201439_create_tipo_documento_table', 1),
(49, '2023_10_01_201439_create_tipos_comprobante_table', 1),
(50, '2023_10_01_201439_create_tipos_egreso_table', 1),
(51, '2023_10_01_201439_create_tipos_movimiento_table', 1),
(52, '2023_10_01_201439_create_unidades_medida_table', 1),
(53, '2023_10_01_201441_create_prd_ventas_proc', 1),
(54, '2023_10_01_201441_create_prd_ventas_today_proc', 1),
(55, '2023_10_01_201442_add_foreign_keys_to_almacen_movimientos_table', 1),
(56, '2023_10_01_201442_add_foreign_keys_to_caja_table', 1),
(57, '2023_10_01_201442_add_foreign_keys_to_cliente_direcciones_table', 1),
(58, '2023_10_01_201442_add_foreign_keys_to_clientes_table', 1),
(59, '2023_10_01_201442_add_foreign_keys_to_compras_detalle_table', 1),
(60, '2023_10_01_201442_add_foreign_keys_to_compras_table', 1),
(61, '2023_10_01_201442_add_foreign_keys_to_comprobante_detalle_table', 1),
(62, '2023_10_01_201442_add_foreign_keys_to_comprobantes_table', 1),
(63, '2023_10_01_201442_add_foreign_keys_to_deudas_compras_pagos_table', 1),
(64, '2023_10_01_201442_add_foreign_keys_to_deudas_compras_table', 1),
(65, '2023_10_01_201442_add_foreign_keys_to_deudas_comprobantes_pagos_table', 1),
(66, '2023_10_01_201442_add_foreign_keys_to_deudas_comprobantes_table', 1),
(67, '2023_10_01_201442_add_foreign_keys_to_egresos_table', 1),
(68, '2023_10_01_201442_add_foreign_keys_to_empresas_table', 1),
(69, '2023_10_01_201442_add_foreign_keys_to_lista_precios_detalle_table', 1),
(70, '2023_10_01_201442_add_foreign_keys_to_lote_productos_table', 1),
(71, '2023_10_01_201442_add_foreign_keys_to_orden_compra_detalle_table', 1),
(72, '2023_10_01_201442_add_foreign_keys_to_orden_compra_table', 1),
(73, '2023_10_01_201442_add_foreign_keys_to_productos_servicios_table', 1),
(74, '2023_10_01_201442_add_foreign_keys_to_proveedores_table', 1),
(75, '2023_10_01_201442_add_foreign_keys_to_prv_cotizacion_detalle_table', 1),
(76, '2023_10_01_201442_add_foreign_keys_to_prv_cotizaciones_table', 1),
(77, '2023_10_01_201442_add_foreign_keys_to_series_inv_table', 1),
(78, '2023_10_01_201442_add_foreign_keys_to_sucursales_table', 1),
(79, '2023_10_01_201442_add_foreign_keys_to_users_table', 1),
(80, '2023_12_13_083615_create_compressed_tables_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(5, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5);

-- --------------------------------------------------------

--
-- Table structure for table `monedas`
--

CREATE TABLE `monedas` (
  `id_moneda` tinyint UNSIGNED NOT NULL,
  `moneda` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `simbolo` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monedas`
--

INSERT INTO `monedas` (`id_moneda`, `moneda`, `nombre`, `simbolo`) VALUES
(1, 'PEN', 'Soles', 'S/.'),
(2, 'USD', 'Dólares', '$');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `client_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint UNSIGNED NOT NULL,
  `client_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orden_compra`
--

CREATE TABLE `orden_compra` (
  `id_orden_compra` bigint UNSIGNED NOT NULL,
  `id_usuario` bigint UNSIGNED DEFAULT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `id_proveedor` bigint UNSIGNED NOT NULL,
  `id_moneda` tinyint UNSIGNED NOT NULL,
  `id_medio_pago` tinyint UNSIGNED NOT NULL,
  `id_tipo_cambio` tinyint UNSIGNED NOT NULL,
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `numeracion` bigint DEFAULT NULL,
  `op_inafectas` decimal(11,2) DEFAULT NULL,
  `op_exoneradas` decimal(11,2) DEFAULT NULL,
  `op_gravadas` decimal(11,2) DEFAULT NULL,
  `icbper` decimal(11,2) DEFAULT NULL,
  `porcentaje_igv` decimal(11,2) DEFAULT NULL,
  `igv` decimal(11,2) DEFAULT NULL,
  `total` decimal(11,2) DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '3' COMMENT '1=Aprobada, 2=Desestimada, 3=Espera',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orden_compra`
--

INSERT INTO `orden_compra` (`id_orden_compra`, `id_usuario`, `id_sucursal`, `id_proveedor`, `id_moneda`, `id_medio_pago`, `id_tipo_cambio`, `email`, `fecha_emision`, `fecha_vencimiento`, `numeracion`, `op_inafectas`, `op_exoneradas`, `op_gravadas`, `icbper`, `porcentaje_igv`, `igv`, `total`, `estado`, `created_at`, `updated_at`) VALUES
(3, 2, 1, 1, 1, 1, 1, 'proveedorinterno@gmail.com', '2024-03-13', '2024-03-13', 1, '0.00', '0.00', '1694.92', '0.00', '18.00', '305.08', '2000.00', 1, '2024-03-14 01:29:25', '2024-03-14 01:29:40');

-- --------------------------------------------------------

--
-- Table structure for table `orden_compra_detalle`
--

CREATE TABLE `orden_compra_detalle` (
  `id_orden_detalle` bigint UNSIGNED NOT NULL,
  `id_orden_compra` bigint UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `nombre_producto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_unidad_medida` int UNSIGNED DEFAULT NULL,
  `und_simbolo` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_lista_detalle` bigint UNSIGNED DEFAULT NULL,
  `cantidad` decimal(11,2) DEFAULT NULL,
  `cantidad_visual` decimal(11,2) DEFAULT NULL,
  `precio_unitario` decimal(11,2) DEFAULT NULL,
  `precio_total` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orden_compra_detalle`
--

INSERT INTO `orden_compra_detalle` (`id_orden_detalle`, `id_orden_compra`, `id_producto`, `nombre_producto`, `id_unidad_medida`, `und_simbolo`, `id_lista_detalle`, `cantidad`, `cantidad_visual`, `precio_unitario`, `precio_total`) VALUES
(1, 3, 1, 'Producto 1', 2, 'UND', 1, '500.00', NULL, '1.00', '500.00'),
(2, 3, 2, 'Producto 2', 2, 'UND', 4, '500.00', NULL, '1.00', '500.00'),
(3, 3, 3, 'Producto 3', 2, 'UND', 7, '500.00', NULL, '2.00', '1000.00');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `active` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `title`, `status`, `active`, `created_at`, `updated_at`) VALUES
(1, 'users_create', 'web', 'Crear usuarios', 1, 1, '2023-12-16 17:29:47', '2023-12-16 17:29:47'),
(2, 'users_index', 'web', 'Leer usuarios', 1, 1, '2023-12-16 17:29:48', '2023-12-16 17:29:48'),
(3, 'users_update', 'web', 'Actualizar usuarios', 1, 1, '2023-12-16 17:29:49', '2023-12-16 17:29:49'),
(4, 'users_delete', 'web', 'Eliminar usuarios', 1, 1, '2023-12-16 17:29:50', '2023-12-16 17:29:50'),
(5, 'roles_create', 'web', 'Crear roles', 1, 1, '2023-12-16 17:29:51', '2023-12-16 17:29:51'),
(6, 'roles_index', 'web', 'Leer roles', 1, 1, '2023-12-16 17:29:52', '2023-12-16 17:29:52'),
(7, 'roles_update', 'web', 'Actualizar roles', 1, 1, '2023-12-16 17:29:52', '2023-12-16 17:29:52'),
(8, 'roles_delete', 'web', 'Eliminar roles', 1, 1, '2023-12-16 17:29:53', '2023-12-16 17:29:53'),
(9, 'permissions_create', 'web', 'Crear permisos', 1, 1, '2023-12-16 17:29:54', '2023-12-16 17:29:54'),
(10, 'permissions_index', 'web', 'Leer permisos', 1, 1, '2023-12-16 17:29:54', '2023-12-16 17:29:54'),
(11, 'permissions_update', 'web', 'Actualizar permisos', 1, 1, '2023-12-16 17:29:55', '2023-12-16 17:29:55'),
(12, 'permissions_delete', 'web', 'Eliminar permisos', 1, 1, '2023-12-16 17:29:56', '2023-12-16 17:29:56'),
(13, 'assign_permissions', 'web', 'Asignar permisos', 1, 1, '2023-12-16 17:29:56', '2023-12-16 17:29:56'),
(14, 'seriesinv_create', 'web', 'Crear series comprobantes', 1, 1, '2023-12-16 17:29:57', '2023-12-16 17:29:57'),
(15, 'seriesinv_index', 'web', 'Leer series comprobantes', 1, 1, '2023-12-16 17:29:58', '2023-12-16 17:29:58'),
(16, 'seriesinv_update', 'web', 'Actualizar series comprobantes', 1, 1, '2023-12-16 17:29:59', '2023-12-16 17:29:59'),
(17, 'seriesinv_delete', 'web', 'Eliminar series comprobantes', 1, 1, '2023-12-16 17:30:00', '2023-12-16 17:30:00'),
(18, 'proveedores_create', 'web', 'Crear proveedores', 1, 1, '2023-12-16 17:30:01', '2023-12-16 17:30:01'),
(19, 'proveedores_index', 'web', 'Leer proveedores', 1, 1, '2023-12-16 17:30:02', '2023-12-16 17:30:02'),
(20, 'proveedores_update', 'web', 'Actualizar proveedores', 1, 1, '2023-12-16 17:30:03', '2023-12-16 17:30:03'),
(21, 'proveedores_delete', 'web', 'Eliminar proveedores', 1, 1, '2023-12-16 17:30:04', '2023-12-16 17:30:04'),
(22, 'empresas_create', 'web', 'Crear empresas', 1, 1, '2023-12-16 17:30:05', '2023-12-16 17:30:05'),
(23, 'empresas_index', 'web', 'Leer empresas', 1, 1, '2023-12-16 17:30:06', '2023-12-16 17:30:06'),
(24, 'empresas_update', 'web', 'Actualizar empresas', 1, 1, '2023-12-16 17:30:07', '2023-12-16 17:30:07'),
(25, 'empresas_delete', 'web', 'Eliminar empresas', 1, 1, '2023-12-16 17:30:08', '2023-12-16 17:30:08'),
(26, 'products_create', 'web', 'Crear productos', 1, 1, '2023-12-16 17:30:09', '2023-12-16 17:30:09'),
(27, 'products_index', 'web', 'Leer productos', 1, 1, '2023-12-16 17:30:10', '2023-12-16 17:30:10'),
(28, 'products_update', 'web', 'Actualizar productos', 1, 1, '2023-12-16 17:30:11', '2023-12-16 17:30:11'),
(29, 'products_delete', 'web', 'Eliminar productos', 1, 1, '2023-12-16 17:30:12', '2023-12-16 17:30:12'),
(30, 'categories_create', 'web', 'Crear categorias', 1, 1, '2023-12-16 17:30:13', '2023-12-16 17:30:13'),
(31, 'categories_index', 'web', 'Leer categorias', 1, 1, '2023-12-16 17:30:14', '2023-12-16 17:30:14'),
(32, 'categories_update', 'web', 'Actualizar categorias', 1, 1, '2023-12-16 17:30:15', '2023-12-16 17:30:15'),
(33, 'categories_delete', 'web', 'Eliminar categorias', 1, 1, '2023-12-16 17:30:16', '2023-12-16 17:30:16'),
(34, 'marcas_create', 'web', 'Crear marcas', 1, 1, '2023-12-16 17:30:17', '2023-12-16 17:30:17'),
(35, 'marcas_index', 'web', 'Leer marcas', 1, 1, '2023-12-16 17:30:18', '2023-12-16 17:30:18'),
(36, 'marcas_update', 'web', 'Actualizar marcas', 1, 1, '2023-12-16 17:30:19', '2023-12-16 17:30:19'),
(37, 'marcas_delete', 'web', 'Eliminar marcas', 1, 1, '2023-12-16 17:30:20', '2023-12-16 17:30:20'),
(38, 'labs_create', 'web', 'Crear laboratorios', 1, 1, '2023-12-16 17:30:21', '2023-12-16 17:30:21'),
(39, 'labs_index', 'web', 'Leer laboratorios', 1, 1, '2023-12-16 17:30:23', '2023-12-16 17:30:23'),
(40, 'labs_update', 'web', 'Actualizar laboratorios', 1, 1, '2023-12-16 17:30:24', '2023-12-16 17:30:24'),
(41, 'labs_delete', 'web', 'Eliminar laboratorios', 1, 1, '2023-12-16 17:30:25', '2023-12-16 17:30:25'),
(42, 'condicionesalm_create', 'web', 'Crear condiciones alm.', 1, 1, '2023-12-16 17:30:26', '2023-12-16 17:30:26'),
(43, 'condicionesalm_index', 'web', 'Leer condiciones alm.', 1, 1, '2023-12-16 17:30:27', '2023-12-16 17:30:27'),
(44, 'condicionesalm_update', 'web', 'Actualizar condiciones alm.', 1, 1, '2023-12-16 17:30:28', '2023-12-16 17:30:28'),
(45, 'condicionesalm_delete', 'web', 'Eliminar condiciones alm.', 1, 1, '2023-12-16 17:30:30', '2023-12-16 17:30:30'),
(46, 'unidadesmed_create', 'web', 'Crear unidades medida', 1, 1, '2023-12-16 17:30:31', '2023-12-16 17:30:31'),
(47, 'unidadesmed_index', 'web', 'Leer unidades medida', 1, 1, '2023-12-16 17:30:32', '2023-12-16 17:30:32'),
(48, 'unidadesmed_update', 'web', 'Actualizar unidades medida', 1, 1, '2023-12-16 17:30:33', '2023-12-16 17:30:33'),
(49, 'unidadesmed_delete', 'web', 'Eliminar unidades medida', 1, 1, '2023-12-16 17:30:35', '2023-12-16 17:30:35'),
(50, 'listsprice_create', 'web', 'Crear listas precio', 1, 1, '2023-12-16 17:30:36', '2023-12-16 17:30:36'),
(51, 'listsprice_index', 'web', 'Leer listas precio', 1, 1, '2023-12-16 17:30:37', '2023-12-16 17:30:37'),
(52, 'listsprice_update', 'web', 'Actualizar listas precio', 1, 1, '2023-12-16 17:30:38', '2023-12-16 17:30:38'),
(53, 'listsprice_delete', 'web', 'Eliminar listas precio', 1, 1, '2023-12-16 17:30:39', '2023-12-16 17:30:39'),
(54, 'historicing_create', 'web', 'Crear historico ingresos', 1, 1, '2023-12-16 17:30:40', '2023-12-16 17:30:40'),
(55, 'historicing_index', 'web', 'Leer historico ingresos', 1, 1, '2023-12-16 17:30:41', '2023-12-16 17:30:41'),
(56, 'historicsal_create', 'web', 'Crear historico salidas', 1, 1, '2023-12-16 17:30:42', '2023-12-16 17:30:42'),
(57, 'historicsal_index', 'web', 'Leer historico salidas', 1, 1, '2023-12-16 17:30:43', '2023-12-16 17:30:43'),
(58, 'almacen_index', 'web', 'Leer almacen', 1, 1, '2023-12-16 17:30:44', '2023-12-16 17:30:44'),
(59, 'cuentascobrar_create', 'web', 'Crear cuentas cobrar', 1, 1, '2023-12-16 17:30:45', '2023-12-16 17:30:45'),
(60, 'cuentascobrar_index', 'web', 'Leer cuentas cobrar', 1, 1, '2023-12-16 17:30:46', '2023-12-16 17:30:46'),
(61, 'cuentascobrar_update', 'web', 'Actualizar cuentas cobrar', 1, 1, '2023-12-16 17:30:47', '2023-12-16 17:30:47'),
(62, 'cuentascobrar_delete', 'web', 'Eliminar cuentas cobrar', 1, 1, '2023-12-16 17:30:48', '2023-12-16 17:30:48'),
(63, 'cuentaspagar_create', 'web', 'Crear cuentas pagar', 1, 1, '2023-12-16 17:30:49', '2023-12-16 17:30:49'),
(64, 'cuentaspagar_index', 'web', 'Leer cuentas pagar', 1, 1, '2023-12-16 17:30:50', '2023-12-16 17:30:50'),
(65, 'cuentaspagar_update', 'web', 'Actualizar cuentas pagar', 1, 1, '2023-12-16 17:30:51', '2023-12-16 17:30:51'),
(66, 'cuentaspagar_delete', 'web', 'Eliminar cuentas pagar', 1, 1, '2023-12-16 17:30:52', '2023-12-16 17:30:52'),
(67, 'compras_create', 'web', 'Crear compras', 1, 1, '2023-12-16 17:30:53', '2023-12-16 17:30:53'),
(68, 'compras_index', 'web', 'Leer compras', 1, 1, '2023-12-16 17:30:54', '2023-12-16 17:30:54'),
(69, 'compras_update', 'web', 'Actualizar compras', 1, 1, '2023-12-16 17:30:55', '2023-12-16 17:30:55'),
(70, 'compras_delete', 'web', 'Eliminar compras', 1, 1, '2023-12-16 17:30:57', '2023-12-16 17:30:57'),
(71, 'ordcomp_create', 'web', 'Crear ordenes compra', 1, 1, '2023-12-16 17:30:58', '2023-12-16 17:30:58'),
(72, 'ordcomp_index', 'web', 'Leer ordenes compra', 1, 1, '2023-12-16 17:30:59', '2023-12-16 17:30:59'),
(73, 'ordcomp_update', 'web', 'Actualizar ordenes compra', 1, 1, '2023-12-16 17:31:00', '2023-12-16 17:31:00'),
(74, 'ordcomp_delete', 'web', 'Eliminar ordenes compra', 1, 1, '2023-12-16 17:31:01', '2023-12-16 17:31:01'),
(75, 'cotizaciones_create', 'web', 'Crear cotizaciones', 1, 1, '2023-12-16 17:31:03', '2023-12-16 17:31:03'),
(76, 'cotizaciones_index', 'web', 'Leer cotizaciones', 1, 1, '2023-12-16 17:31:04', '2023-12-16 17:31:04'),
(77, 'cotizaciones_update', 'web', 'Actualizar cotizaciones', 1, 1, '2023-12-16 17:31:05', '2023-12-16 17:31:05'),
(78, 'cotizaciones_delete', 'web', 'Eliminar cotizaciones', 1, 1, '2023-12-16 17:31:06', '2023-12-16 17:31:06'),
(79, 'cajaopen_create', 'web', 'Crear caja apertura', 1, 1, '2023-12-16 17:31:07', '2023-12-16 17:31:07'),
(80, 'cajaopen_index', 'web', 'Leer caja apertura', 1, 1, '2023-12-16 17:31:08', '2023-12-16 17:31:08'),
(81, 'cajaopen_update', 'web', 'Actualizar caja apertura', 1, 1, '2023-12-16 17:31:09', '2023-12-16 17:31:09'),
(82, 'cajaopen_delete', 'web', 'Eliminar caja apertura', 1, 1, '2023-12-16 17:31:10', '2023-12-16 17:31:10'),
(83, 'cajaegresos_create', 'web', 'Crear caja egresos', 1, 1, '2023-12-16 17:31:12', '2023-12-16 17:31:12'),
(84, 'cajaegresos_index', 'web', 'Leer caja egresos', 1, 1, '2023-12-16 17:31:13', '2023-12-16 17:31:13'),
(85, 'cajaegresos_update', 'web', 'Actualizar caja egresos', 1, 1, '2023-12-16 17:31:14', '2023-12-16 17:31:14'),
(86, 'cajaegresos_delete', 'web', 'Eliminar caja egresos', 1, 1, '2023-12-16 17:31:15', '2023-12-16 17:31:15'),
(87, 'invoice_create', 'web', 'Crear comprobantes', 1, 1, '2023-12-16 17:31:16', '2023-12-16 17:31:16'),
(88, 'invoice_index', 'web', 'Leer comprobantes', 1, 1, '2023-12-16 17:31:17', '2023-12-16 17:31:17'),
(89, 'invoice_update', 'web', 'Actualizar comprobantes', 1, 1, '2023-12-16 17:31:18', '2023-12-16 17:31:18'),
(90, 'invoice_delete', 'web', 'Eliminar comprobantes', 1, 1, '2023-12-16 17:31:19', '2023-12-16 17:31:19'),
(91, 'kardexreport_index', 'web', 'Leer reporte kardex', 1, 1, '2023-12-16 17:31:20', '2023-12-16 17:31:20'),
(92, 'kardexreport_export', 'web', 'Exportar reporte kardex', 1, 1, '2023-12-16 17:31:21', '2023-12-16 17:31:21'),
(93, 'comprasreport_index', 'web', 'Leer reporte compras', 1, 1, '2023-12-16 17:31:22', '2023-12-16 17:31:22'),
(94, 'comprasreport_export', 'web', 'Exportar reporte compras', 1, 1, '2023-12-16 17:31:23', '2023-12-16 17:31:23'),
(95, 'invoicereportgen_index', 'web', 'Leer reporte general comprobantes', 1, 1, '2023-12-16 17:31:24', '2023-12-16 17:31:24'),
(96, 'invoicereportgen_export', 'web', 'Exportar reporte general comprobantes', 1, 1, '2023-12-16 17:31:25', '2023-12-16 17:31:25'),
(97, 'invoicereportform_index', 'web', 'Leer reporte formato comprobantes', 1, 1, '2023-12-16 17:31:26', '2023-12-16 17:31:26'),
(98, 'invoicereportform_export', 'web', 'Exportar reporte formato comprobantes', 1, 1, '2023-12-16 17:31:27', '2023-12-16 17:31:27'),
(99, 'obsproductsreport_index', 'web', 'Leer reporte observatorio productos', 1, 1, '2023-12-16 17:31:28', '2023-12-16 17:31:28'),
(100, 'obsproductsreport_export', 'web', 'Exportar reporte observatorio productos', 1, 1, '2023-12-16 17:31:29', '2023-12-16 17:31:29'),
(101, 'pricelistreport_index', 'web', 'Leer reporte listado precios', 1, 1, '2023-12-16 17:31:30', '2023-12-16 17:31:30'),
(102, 'pricelistreport_export', 'web', 'Exportar reporte listado precios', 1, 1, '2023-12-16 17:31:31', '2023-12-16 17:31:31'),
(103, 'lotesreport_index', 'web', 'Leer reporte lotes', 1, 1, '2023-12-16 17:31:32', '2023-12-16 17:31:32'),
(104, 'lotesreport_export', 'web', 'Exportar reporte lotes', 1, 1, '2023-12-16 17:31:33', '2023-12-16 17:31:33'),
(105, 'menu_productos', 'web', 'Menu productos', 1, 1, '2023-12-16 17:31:34', '2023-12-16 17:31:34'),
(106, 'menu_empresa', 'web', 'Menu empresa', 1, 1, '2023-12-16 17:31:35', '2023-12-16 17:31:35'),
(107, 'menu_historicadj', 'web', 'Menu historico ajustes', 1, 1, '2023-12-16 17:31:36', '2023-12-16 17:31:36'),
(108, 'menu_cuentas', 'web', 'Menu cuentas', 1, 1, '2023-12-16 17:31:37', '2023-12-16 17:31:37'),
(109, 'menu_compras', 'web', 'Menu compras', 1, 1, '2023-12-16 17:31:38', '2023-12-16 17:31:38'),
(110, 'menu_caja', 'web', 'Menu caja', 1, 1, '2023-12-16 17:31:39', '2023-12-16 17:31:39'),
(111, 'menu_ventas', 'web', 'Menu ventas', 1, 1, '2023-12-16 17:31:40', '2023-12-16 17:31:40'),
(112, 'menu_invreport', 'web', 'Menu reporte comprobantes', 1, 1, '2023-12-16 17:31:41', '2023-12-16 17:31:41'),
(113, 'menu_digemid', 'web', 'Menu digemid', 1, 1, '2023-12-16 17:31:42', '2023-12-16 17:31:42'),
(114, 'chat_gpt', 'web', 'Chat GPT', 1, 1, '2023-12-16 17:31:42', '2023-12-16 17:31:42'),
(115, 'clients_create', 'web', 'Crear clientes', 1, 1, '2024-01-17 02:43:50', '2024-01-17 02:43:50'),
(116, 'clients_index', 'web', 'Leer clientes', 1, 1, '2024-01-17 02:43:50', '2024-01-17 02:43:50'),
(117, 'clients_update', 'web', 'Actualizar clientes', 1, 1, '2024-01-17 02:43:50', '2024-01-17 02:43:50'),
(118, 'clients_delete', 'web', 'Eliminar clientes', 1, 1, '2024-01-17 02:43:50', '2024-01-17 02:43:50'),
(119, 'sucursales_create', 'web', 'Crear sucursales', 1, 1, NULL, NULL),
(120, 'sucursales_index', 'web', 'Leer sucursales', 1, 1, NULL, NULL),
(121, 'sucursales_update', 'web', 'Actualizar sucursales', 1, 1, NULL, NULL),
(122, 'sucursales_delete', 'web', 'Eliminar sucursales', 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `productos_categorias`
--

CREATE TABLE `productos_categorias` (
  `id_categoria` int UNSIGNED NOT NULL,
  `codigo` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `productos_categorias`
--

INSERT INTO `productos_categorias` (`id_categoria`, `codigo`, `categoria`, `estado`) VALUES
(1, 'categoria_1', 'Categoria 1', 1),
(2, 'categoria_2', 'Categoria 2', 1),
(3, 'categoria_3', 'Categoria 3', 1);

-- --------------------------------------------------------

--
-- Table structure for table `productos_servicios`
--

CREATE TABLE `productos_servicios` (
  `id_producto` bigint UNSIGNED NOT NULL,
  `codigo_producto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_marca` bigint UNSIGNED DEFAULT NULL,
  `id_categoria` int UNSIGNED NOT NULL,
  `id_unidad_medida` int UNSIGNED DEFAULT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `id_laboratorio` bigint UNSIGNED DEFAULT NULL,
  `id_condicion_alm` int UNSIGNED DEFAULT NULL,
  `id_tipo_producto` tinyint UNSIGNED DEFAULT NULL,
  `nombreProducto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` decimal(11,2) DEFAULT '1.00',
  `stock_minimo` decimal(11,2) DEFAULT '1.00',
  `servicio` tinyint DEFAULT '0' COMMENT '0=no, 1=si',
  `principio_activo` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indicaciones` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `concentracion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registro_sanitario` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vigencia_registro` date DEFAULT NULL,
  `ubicacion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `productos_servicios`
--

INSERT INTO `productos_servicios` (`id_producto`, `codigo_producto`, `id_marca`, `id_categoria`, `id_unidad_medida`, `id_sucursal`, `id_laboratorio`, `id_condicion_alm`, `id_tipo_producto`, `nombreProducto`, `stock`, `stock_minimo`, `servicio`, `principio_activo`, `indicaciones`, `concentracion`, `registro_sanitario`, `vigencia_registro`, `ubicacion`, `estado`, `updated_at`, `created_at`) VALUES
(1, 'PR001', 2, 1, 2, 1, 1, 1, 1, 'Producto 1', '4485.00', '100.00', 0, NULL, NULL, NULL, NULL, '2024-03-12', 'Almacen', 1, '2024-03-14 01:56:30', '2024-03-13 02:52:13'),
(2, 'PR002', 3, 2, 2, 1, 2, 1, 1, 'Producto 2', '2485.00', '100.00', 0, NULL, NULL, NULL, NULL, '2024-12-31', 'Almacen', 1, '2024-03-14 01:56:32', '2024-03-13 02:53:52'),
(3, 'PR003', 4, 3, 2, 1, 3, 1, 1, 'Producto 3', '2485.00', '100.00', 0, NULL, NULL, NULL, NULL, '2024-12-31', 'Almacen', 1, '2024-03-14 01:56:33', '2024-03-13 02:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `producto_tipos`
--

CREATE TABLE `producto_tipos` (
  `id_producto_tipo` tinyint UNSIGNED NOT NULL,
  `tipo` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `impuesto` tinyint DEFAULT NULL COMMENT '1=Gravado, 2=inafecto, 3= exonerado,\n4=icbper',
  `icbper` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `producto_tipos`
--

INSERT INTO `producto_tipos` (`id_producto_tipo`, `tipo`, `impuesto`, `icbper`) VALUES
(1, 'Gravado - Operación Onerosa', 1, NULL),
(2, 'Gravado - Retiro por premio', 1, NULL),
(3, 'Gravado - Retiro por donación', 1, NULL),
(4, 'Gravado - Retiro', 1, NULL),
(5, 'Gravado - Retiro por publicidad', 1, NULL),
(6, 'Gravado - Bonificaciones', 1, NULL),
(7, 'Gravado - Retiro por entrega a trabajadores', 1, NULL),
(8, 'Exonerado - Operación Onerosa', 3, NULL),
(9, 'Exonerado - Transferencia Gratuita', 3, NULL),
(10, 'Inafecto - Operación Onerosa', 2, NULL),
(11, 'Inafecto - Retiro por Bonificación', 2, NULL),
(12, 'Inafecto - Retiro', 2, NULL),
(13, 'Inafecto - Retiro por Muestras Médicas', 2, NULL),
(14, 'Inafecto - Retiro por Convenio Colectivo', 2, NULL),
(15, 'Inafecto - Retiro por premio', 2, NULL),
(16, 'Inafecto - Retiro por publicidad', 2, NULL),
(17, 'Inafecto - Transferencia gratuita', 2, NULL),
(18, 'Impuesto al Consumo de las bolsas de Plástico', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` bigint UNSIGNED NOT NULL,
  `id_tipo_doc` tinyint UNSIGNED DEFAULT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `tipo_proveedor` tinyint UNSIGNED DEFAULT NULL COMMENT '	1=Interno, 2=Distribuidor	',
  `nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_doc` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_telefono` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_departamento` int DEFAULT NULL,
  `id_provincia` int DEFAULT NULL,
  `id_distrito` int DEFAULT NULL,
  `estado` tinyint DEFAULT '1' COMMENT '1=Habilitado, 0=Deshabilitado',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `id_tipo_doc`, `id_sucursal`, `tipo_proveedor`, `nombre`, `nro_doc`, `email`, `telefono`, `direccion`, `contacto_nombre`, `contacto_telefono`, `id_departamento`, `id_provincia`, `id_distrito`, `estado`, `updated_at`, `created_at`) VALUES
(1, 1, 1, 1, 'PROVEEDOR INTERNO', '12345678912', 'proveedorinterno@gmail.com', '123456789', 'Calle los girasoles 123', NULL, NULL, 4, 401, 40101, 1, '2023-12-16 17:31:54', '2023-12-16 17:31:54'),
(2, 1, 1, 2, 'PROVEEDOR DISTRIBUIDOR', '98765432198', 'proveedordistribuidor@gmail.com', '987654321', 'Calle los girasoles 987', NULL, NULL, 4, 401, 40101, 1, '2023-12-16 17:31:54', '2023-12-16 17:31:54');

-- --------------------------------------------------------

--
-- Table structure for table `prv_cotizaciones`
--

CREATE TABLE `prv_cotizaciones` (
  `id_cotizacion_prv` bigint UNSIGNED NOT NULL,
  `id_usuario` bigint UNSIGNED DEFAULT NULL,
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `id_proveedor` bigint UNSIGNED NOT NULL,
  `numeracion` bigint DEFAULT NULL,
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `id_estado` tinyint UNSIGNED NOT NULL DEFAULT '3' COMMENT '1=Aprobada, 2=Desestimada, 3=Espera',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prv_cotizaciones`
--

INSERT INTO `prv_cotizaciones` (`id_cotizacion_prv`, `id_usuario`, `id_sucursal`, `id_proveedor`, `numeracion`, `email`, `fecha_emision`, `id_estado`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, 1, 'proveedorinterno@gmail.com', '2024-03-13', 1, '2024-03-14 01:22:03', '2024-03-14 01:22:09');

-- --------------------------------------------------------

--
-- Table structure for table `prv_cotizacion_detalle`
--

CREATE TABLE `prv_cotizacion_detalle` (
  `id_cotz_detalle_prv` bigint UNSIGNED NOT NULL,
  `id_cotizacion_prv` bigint UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `nombre_producto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_unidad_medida` int UNSIGNED DEFAULT NULL,
  `und_simbolo` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prv_cotizacion_detalle`
--

INSERT INTO `prv_cotizacion_detalle` (`id_cotz_detalle_prv`, `id_cotizacion_prv`, `id_producto`, `nombre_producto`, `id_unidad_medida`, `und_simbolo`, `cantidad`) VALUES
(1, 1, 1, 'Producto 1', 2, 'UND', '500.00'),
(2, 1, 2, 'Producto 2', 2, 'UND', '500.00'),
(3, 1, 3, 'Producto 3', 2, 'UND', '500.00');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `active` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `title`, `status`, `active`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'web', 'Superadmin', 1, 1, '2023-12-16 17:29:46', '2023-12-16 17:29:46'),
(2, 'admin', 'web', 'Administrador', 1, 1, '2023-12-16 17:29:46', '2023-12-16 17:29:46'),
(3, 'accounting', 'web', 'Contabilidad', 1, 1, '2023-12-16 17:29:47', '2023-12-16 17:29:47'),
(4, 'warehouse', 'web', 'Almacén', 1, 1, '2023-12-16 17:29:47', '2023-12-16 17:29:47'),
(5, 'cashier', 'web', 'Cajero', 1, 1, '2023-12-16 17:29:47', '2023-12-16 17:29:47');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(45, 2),
(46, 2),
(47, 2),
(48, 2),
(49, 2),
(50, 2),
(51, 2),
(52, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(78, 2),
(79, 2),
(80, 2),
(81, 2),
(82, 2),
(83, 2),
(84, 2),
(85, 2),
(86, 2),
(87, 2),
(88, 2),
(89, 2),
(90, 2),
(91, 2),
(92, 2),
(93, 2),
(94, 2),
(95, 2),
(96, 2),
(97, 2),
(98, 2),
(99, 2),
(100, 2),
(101, 2),
(102, 2),
(103, 2),
(104, 2),
(105, 2),
(106, 2),
(107, 2),
(108, 2),
(109, 2),
(110, 2),
(111, 2),
(112, 2),
(113, 2),
(114, 2),
(115, 2),
(116, 2),
(117, 2),
(118, 2),
(119, 2),
(120, 2),
(121, 2),
(122, 2),
(14, 3),
(15, 3),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(26, 3),
(27, 3),
(28, 3),
(29, 3),
(30, 3),
(31, 3),
(32, 3),
(33, 3),
(34, 3),
(35, 3),
(36, 3),
(37, 3),
(38, 3),
(39, 3),
(40, 3),
(41, 3),
(42, 3),
(43, 3),
(44, 3),
(45, 3),
(47, 3),
(51, 3),
(54, 3),
(55, 3),
(56, 3),
(57, 3),
(58, 3),
(59, 3),
(60, 3),
(61, 3),
(62, 3),
(63, 3),
(64, 3),
(65, 3),
(66, 3),
(67, 3),
(68, 3),
(69, 3),
(70, 3),
(71, 3),
(72, 3),
(73, 3),
(74, 3),
(75, 3),
(76, 3),
(77, 3),
(78, 3),
(79, 3),
(80, 3),
(81, 3),
(82, 3),
(83, 3),
(84, 3),
(85, 3),
(86, 3),
(87, 3),
(88, 3),
(89, 3),
(90, 3),
(91, 3),
(92, 3),
(93, 3),
(94, 3),
(95, 3),
(96, 3),
(97, 3),
(98, 3),
(99, 3),
(100, 3),
(101, 3),
(102, 3),
(103, 3),
(104, 3),
(105, 3),
(107, 3),
(108, 3),
(109, 3),
(110, 3),
(111, 3),
(112, 3),
(113, 3),
(114, 3),
(115, 3),
(116, 3),
(117, 3),
(118, 3),
(120, 3),
(15, 4),
(18, 4),
(19, 4),
(20, 4),
(21, 4),
(26, 4),
(27, 4),
(28, 4),
(29, 4),
(31, 4),
(35, 4),
(39, 4),
(43, 4),
(47, 4),
(51, 4),
(54, 4),
(55, 4),
(56, 4),
(57, 4),
(58, 4),
(63, 4),
(64, 4),
(65, 4),
(66, 4),
(67, 4),
(68, 4),
(69, 4),
(70, 4),
(71, 4),
(72, 4),
(73, 4),
(74, 4),
(75, 4),
(76, 4),
(77, 4),
(78, 4),
(80, 4),
(84, 4),
(88, 4),
(91, 4),
(92, 4),
(93, 4),
(94, 4),
(95, 4),
(96, 4),
(97, 4),
(98, 4),
(99, 4),
(100, 4),
(101, 4),
(102, 4),
(103, 4),
(104, 4),
(105, 4),
(107, 4),
(108, 4),
(109, 4),
(110, 4),
(111, 4),
(112, 4),
(113, 4),
(114, 4),
(116, 4),
(120, 4),
(2, 5),
(15, 5),
(19, 5),
(26, 5),
(27, 5),
(28, 5),
(31, 5),
(34, 5),
(35, 5),
(36, 5),
(38, 5),
(39, 5),
(40, 5),
(42, 5),
(43, 5),
(44, 5),
(47, 5),
(50, 5),
(51, 5),
(52, 5),
(54, 5),
(55, 5),
(56, 5),
(57, 5),
(58, 5),
(59, 5),
(60, 5),
(61, 5),
(63, 5),
(64, 5),
(65, 5),
(67, 5),
(68, 5),
(69, 5),
(70, 5),
(71, 5),
(72, 5),
(73, 5),
(74, 5),
(75, 5),
(76, 5),
(77, 5),
(78, 5),
(79, 5),
(80, 5),
(81, 5),
(82, 5),
(83, 5),
(84, 5),
(85, 5),
(86, 5),
(87, 5),
(88, 5),
(89, 5),
(90, 5),
(91, 5),
(92, 5),
(93, 5),
(94, 5),
(95, 5),
(96, 5),
(97, 5),
(98, 5),
(99, 5),
(100, 5),
(101, 5),
(102, 5),
(103, 5),
(104, 5),
(105, 5),
(106, 5),
(107, 5),
(108, 5),
(109, 5),
(110, 5),
(111, 5),
(112, 5),
(113, 5),
(114, 5),
(115, 5),
(116, 5),
(117, 5),
(120, 5);

-- --------------------------------------------------------

--
-- Table structure for table `series_inv`
--

CREATE TABLE `series_inv` (
  `id_serie` int UNSIGNED NOT NULL,
  `id_sucursal` int UNSIGNED NOT NULL,
  `id_tipo_comprobante` tinyint UNSIGNED NOT NULL,
  `serie` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `series_inv`
--

INSERT INTO `series_inv` (`id_serie`, `id_sucursal`, `id_tipo_comprobante`, `serie`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'F001', 1, '2023-12-16 17:31:55', '2023-12-16 12:31:55'),
(2, 1, 2, 'B001', 1, '2023-12-16 17:31:55', '2023-12-16 12:31:55'),
(3, 2, 1, 'F002', 1, '2023-12-16 17:31:55', '2023-12-16 12:31:55'),
(4, 2, 2, 'B002', 1, '2023-12-16 17:31:55', '2023-12-16 12:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `sucursales`
--

CREATE TABLE `sucursales` (
  `id_sucursal` int UNSIGNED NOT NULL,
  `id_empresa` bigint UNSIGNED DEFAULT NULL,
  `nombre_sucursal` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cod_domicilio_fiscal` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_fiscal` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_departamento` int DEFAULT NULL,
  `id_provincia` int DEFAULT NULL,
  `id_distrito` int DEFAULT NULL,
  `telefono` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_comercial` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_web` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_cuenta_bancario` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cci_bancario` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_url` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sucursales`
--

INSERT INTO `sucursales` (`id_sucursal`, `id_empresa`, `nombre_sucursal`, `cod_domicilio_fiscal`, `direccion_fiscal`, `id_departamento`, `id_provincia`, `id_distrito`, `telefono`, `direccion_comercial`, `email`, `direccion_web`, `nro_cuenta_bancario`, `cci_bancario`, `api_url`, `api_token`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sucursal 1', '111', 'dirfis2', 4, 401, 40101, '123456789', 'dircom1', 'sucursal1@gmail.com', 'dirweb1', '123456', '1111111111', 'https://pruebas.bytesoluciones.net/', '343KdvnhncBIFKMkMcAXF5QzRUKRXAHPpW8Sp1AO5CA6SbdJKV', 1, '2023-12-16 17:31:44', '2023-12-16 12:31:44'),
(2, 1, 'Sucursal 2', '222', 'dirfis1', 4, 402, 40201, '987654321', 'dircom2', 'sucursal2@gmail.com', 'dirweb2', '654321', '22222222222', 'https://pruebas.bytesoluciones.net/', '343KdvnhncBIFKMkMcAXF5QzRUKRXAHPpW8Sp1AO5CA6SbdJKV', 1, '2023-12-16 17:31:44', '2023-12-16 12:31:44');

-- --------------------------------------------------------

--
-- Table structure for table `tipos_comprobante`
--

CREATE TABLE `tipos_comprobante` (
  `id_tipo_comprobante` tinyint UNSIGNED NOT NULL,
  `tipo_comprobante` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_sunat` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipos_comprobante`
--

INSERT INTO `tipos_comprobante` (`id_tipo_comprobante`, `tipo_comprobante`, `codigo_sunat`) VALUES
(1, 'Factura', '01'),
(2, 'Boleta', '03');

-- --------------------------------------------------------

--
-- Table structure for table `tipos_egreso`
--

CREATE TABLE `tipos_egreso` (
  `id_tipo_egreso` tinyint UNSIGNED NOT NULL,
  `tipo_egreso` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipos_egreso`
--

INSERT INTO `tipos_egreso` (`id_tipo_egreso`, `tipo_egreso`) VALUES
(1, 'Planilla'),
(2, 'Recibo por Honorarios'),
(3, 'Servicios'),
(4, 'Otros');

-- --------------------------------------------------------

--
-- Table structure for table `tipos_movimiento`
--

CREATE TABLE `tipos_movimiento` (
  `id_tipo_movimiento` tinyint UNSIGNED NOT NULL,
  `tipo_movimiento` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipos_movimiento`
--

INSERT INTO `tipos_movimiento` (`id_tipo_movimiento`, `tipo_movimiento`) VALUES
(1, 'Entrada'),
(2, 'Salida'),
(3, 'Devolución'),
(5, 'Stock Inicial');

-- --------------------------------------------------------

--
-- Table structure for table `tipo_cambio`
--

CREATE TABLE `tipo_cambio` (
  `id_tipo_cambio` tinyint UNSIGNED NOT NULL,
  `tipo_cambio` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cambio` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipo_cambio`
--

INSERT INTO `tipo_cambio` (`id_tipo_cambio`, `tipo_cambio`, `cambio`) VALUES
(1, 'Dólares', '3.70');

-- --------------------------------------------------------

--
-- Table structure for table `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id_tipo_doc` tinyint UNSIGNED NOT NULL,
  `tipo_documento` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_sunat` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipo_documento`
--

INSERT INTO `tipo_documento` (`id_tipo_doc`, `tipo_documento`, `codigo_sunat`) VALUES
(1, 'RUC', '6'),
(2, 'DNI', '1'),
(4, 'CARNET DE EXTRANJERIA', '4'),
(7, 'PASAPORTE', '7');

-- --------------------------------------------------------

--
-- Table structure for table `ubigeo_peru_departments`
--

CREATE TABLE `ubigeo_peru_departments` (
  `id` varchar(2) NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ubigeo_peru_departments`
--

INSERT INTO `ubigeo_peru_departments` (`id`, `name`) VALUES
('01', 'Amazonas'),
('02', 'Áncash'),
('03', 'Apurímac'),
('04', 'Arequipa'),
('05', 'Ayacucho'),
('06', 'Cajamarca'),
('07', 'Callao'),
('08', 'Cusco'),
('09', 'Huancavelica'),
('10', 'Huánuco'),
('11', 'Ica'),
('12', 'Junín'),
('13', 'La Libertad'),
('14', 'Lambayeque'),
('15', 'Lima'),
('16', 'Loreto'),
('17', 'Madre de Dios'),
('18', 'Moquegua'),
('19', 'Pasco'),
('20', 'Piura'),
('21', 'Puno'),
('22', 'San Martín'),
('23', 'Tacna'),
('24', 'Tumbes'),
('25', 'Ucayali');

-- --------------------------------------------------------

--
-- Table structure for table `ubigeo_peru_districts`
--

CREATE TABLE `ubigeo_peru_districts` (
  `id` varchar(6) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `province_id` varchar(4) DEFAULT NULL,
  `department_id` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ubigeo_peru_districts`
--

INSERT INTO `ubigeo_peru_districts` (`id`, `name`, `province_id`, `department_id`) VALUES
('010101', 'Chachapoyas', '0101', '01'),
('010102', 'Asunción', '0101', '01'),
('010103', 'Balsas', '0101', '01'),
('010104', 'Cheto', '0101', '01'),
('010105', 'Chiliquin', '0101', '01'),
('010106', 'Chuquibamba', '0101', '01'),
('010107', 'Granada', '0101', '01'),
('010108', 'Huancas', '0101', '01'),
('010109', 'La Jalca', '0101', '01'),
('010110', 'Leimebamba', '0101', '01'),
('010111', 'Levanto', '0101', '01'),
('010112', 'Magdalena', '0101', '01'),
('010113', 'Mariscal Castilla', '0101', '01'),
('010114', 'Molinopampa', '0101', '01'),
('010115', 'Montevideo', '0101', '01'),
('010116', 'Olleros', '0101', '01'),
('010117', 'Quinjalca', '0101', '01'),
('010118', 'San Francisco de Daguas', '0101', '01'),
('010119', 'San Isidro de Maino', '0101', '01'),
('010120', 'Soloco', '0101', '01'),
('010121', 'Sonche', '0101', '01'),
('010201', 'Bagua', '0102', '01'),
('010202', 'Aramango', '0102', '01'),
('010203', 'Copallin', '0102', '01'),
('010204', 'El Parco', '0102', '01'),
('010205', 'Imaza', '0102', '01'),
('010206', 'La Peca', '0102', '01'),
('010301', 'Jumbilla', '0103', '01'),
('010302', 'Chisquilla', '0103', '01'),
('010303', 'Churuja', '0103', '01'),
('010304', 'Corosha', '0103', '01'),
('010305', 'Cuispes', '0103', '01'),
('010306', 'Florida', '0103', '01'),
('010307', 'Jazan', '0103', '01'),
('010308', 'Recta', '0103', '01'),
('010309', 'San Carlos', '0103', '01'),
('010310', 'Shipasbamba', '0103', '01'),
('010311', 'Valera', '0103', '01'),
('010312', 'Yambrasbamba', '0103', '01'),
('010401', 'Nieva', '0104', '01'),
('010402', 'El Cenepa', '0104', '01'),
('010403', 'Río Santiago', '0104', '01'),
('010501', 'Lamud', '0105', '01'),
('010502', 'Camporredondo', '0105', '01'),
('010503', 'Cocabamba', '0105', '01'),
('010504', 'Colcamar', '0105', '01'),
('010505', 'Conila', '0105', '01'),
('010506', 'Inguilpata', '0105', '01'),
('010507', 'Longuita', '0105', '01'),
('010508', 'Lonya Chico', '0105', '01'),
('010509', 'Luya', '0105', '01'),
('010510', 'Luya Viejo', '0105', '01'),
('010511', 'María', '0105', '01'),
('010512', 'Ocalli', '0105', '01'),
('010513', 'Ocumal', '0105', '01'),
('010514', 'Pisuquia', '0105', '01'),
('010515', 'Providencia', '0105', '01'),
('010516', 'San Cristóbal', '0105', '01'),
('010517', 'San Francisco de Yeso', '0105', '01'),
('010518', 'San Jerónimo', '0105', '01'),
('010519', 'San Juan de Lopecancha', '0105', '01'),
('010520', 'Santa Catalina', '0105', '01'),
('010521', 'Santo Tomas', '0105', '01'),
('010522', 'Tingo', '0105', '01'),
('010523', 'Trita', '0105', '01'),
('010601', 'San Nicolás', '0106', '01'),
('010602', 'Chirimoto', '0106', '01'),
('010603', 'Cochamal', '0106', '01'),
('010604', 'Huambo', '0106', '01'),
('010605', 'Limabamba', '0106', '01'),
('010606', 'Longar', '0106', '01'),
('010607', 'Mariscal Benavides', '0106', '01'),
('010608', 'Milpuc', '0106', '01'),
('010609', 'Omia', '0106', '01'),
('010610', 'Santa Rosa', '0106', '01'),
('010611', 'Totora', '0106', '01'),
('010612', 'Vista Alegre', '0106', '01'),
('010701', 'Bagua Grande', '0107', '01'),
('010702', 'Cajaruro', '0107', '01'),
('010703', 'Cumba', '0107', '01'),
('010704', 'El Milagro', '0107', '01'),
('010705', 'Jamalca', '0107', '01'),
('010706', 'Lonya Grande', '0107', '01'),
('010707', 'Yamon', '0107', '01'),
('020101', 'Huaraz', '0201', '02'),
('020102', 'Cochabamba', '0201', '02'),
('020103', 'Colcabamba', '0201', '02'),
('020104', 'Huanchay', '0201', '02'),
('020105', 'Independencia', '0201', '02'),
('020106', 'Jangas', '0201', '02'),
('020107', 'La Libertad', '0201', '02'),
('020108', 'Olleros', '0201', '02'),
('020109', 'Pampas Grande', '0201', '02'),
('020110', 'Pariacoto', '0201', '02'),
('020111', 'Pira', '0201', '02'),
('020112', 'Tarica', '0201', '02'),
('020201', 'Aija', '0202', '02'),
('020202', 'Coris', '0202', '02'),
('020203', 'Huacllan', '0202', '02'),
('020204', 'La Merced', '0202', '02'),
('020205', 'Succha', '0202', '02'),
('020301', 'Llamellin', '0203', '02'),
('020302', 'Aczo', '0203', '02'),
('020303', 'Chaccho', '0203', '02'),
('020304', 'Chingas', '0203', '02'),
('020305', 'Mirgas', '0203', '02'),
('020306', 'San Juan de Rontoy', '0203', '02'),
('020401', 'Chacas', '0204', '02'),
('020402', 'Acochaca', '0204', '02'),
('020501', 'Chiquian', '0205', '02'),
('020502', 'Abelardo Pardo Lezameta', '0205', '02'),
('020503', 'Antonio Raymondi', '0205', '02'),
('020504', 'Aquia', '0205', '02'),
('020505', 'Cajacay', '0205', '02'),
('020506', 'Canis', '0205', '02'),
('020507', 'Colquioc', '0205', '02'),
('020508', 'Huallanca', '0205', '02'),
('020509', 'Huasta', '0205', '02'),
('020510', 'Huayllacayan', '0205', '02'),
('020511', 'La Primavera', '0205', '02'),
('020512', 'Mangas', '0205', '02'),
('020513', 'Pacllon', '0205', '02'),
('020514', 'San Miguel de Corpanqui', '0205', '02'),
('020515', 'Ticllos', '0205', '02'),
('020601', 'Carhuaz', '0206', '02'),
('020602', 'Acopampa', '0206', '02'),
('020603', 'Amashca', '0206', '02'),
('020604', 'Anta', '0206', '02'),
('020605', 'Ataquero', '0206', '02'),
('020606', 'Marcara', '0206', '02'),
('020607', 'Pariahuanca', '0206', '02'),
('020608', 'San Miguel de Aco', '0206', '02'),
('020609', 'Shilla', '0206', '02'),
('020610', 'Tinco', '0206', '02'),
('020611', 'Yungar', '0206', '02'),
('020701', 'San Luis', '0207', '02'),
('020702', 'San Nicolás', '0207', '02'),
('020703', 'Yauya', '0207', '02'),
('020801', 'Casma', '0208', '02'),
('020802', 'Buena Vista Alta', '0208', '02'),
('020803', 'Comandante Noel', '0208', '02'),
('020804', 'Yautan', '0208', '02'),
('020901', 'Corongo', '0209', '02'),
('020902', 'Aco', '0209', '02'),
('020903', 'Bambas', '0209', '02'),
('020904', 'Cusca', '0209', '02'),
('020905', 'La Pampa', '0209', '02'),
('020906', 'Yanac', '0209', '02'),
('020907', 'Yupan', '0209', '02'),
('021001', 'Huari', '0210', '02'),
('021002', 'Anra', '0210', '02'),
('021003', 'Cajay', '0210', '02'),
('021004', 'Chavin de Huantar', '0210', '02'),
('021005', 'Huacachi', '0210', '02'),
('021006', 'Huacchis', '0210', '02'),
('021007', 'Huachis', '0210', '02'),
('021008', 'Huantar', '0210', '02'),
('021009', 'Masin', '0210', '02'),
('021010', 'Paucas', '0210', '02'),
('021011', 'Ponto', '0210', '02'),
('021012', 'Rahuapampa', '0210', '02'),
('021013', 'Rapayan', '0210', '02'),
('021014', 'San Marcos', '0210', '02'),
('021015', 'San Pedro de Chana', '0210', '02'),
('021016', 'Uco', '0210', '02'),
('021101', 'Huarmey', '0211', '02'),
('021102', 'Cochapeti', '0211', '02'),
('021103', 'Culebras', '0211', '02'),
('021104', 'Huayan', '0211', '02'),
('021105', 'Malvas', '0211', '02'),
('021201', 'Caraz', '0212', '02'),
('021202', 'Huallanca', '0212', '02'),
('021203', 'Huata', '0212', '02'),
('021204', 'Huaylas', '0212', '02'),
('021205', 'Mato', '0212', '02'),
('021206', 'Pamparomas', '0212', '02'),
('021207', 'Pueblo Libre', '0212', '02'),
('021208', 'Santa Cruz', '0212', '02'),
('021209', 'Santo Toribio', '0212', '02'),
('021210', 'Yuracmarca', '0212', '02'),
('021301', 'Piscobamba', '0213', '02'),
('021302', 'Casca', '0213', '02'),
('021303', 'Eleazar Guzmán Barron', '0213', '02'),
('021304', 'Fidel Olivas Escudero', '0213', '02'),
('021305', 'Llama', '0213', '02'),
('021306', 'Llumpa', '0213', '02'),
('021307', 'Lucma', '0213', '02'),
('021308', 'Musga', '0213', '02'),
('021401', 'Ocros', '0214', '02'),
('021402', 'Acas', '0214', '02'),
('021403', 'Cajamarquilla', '0214', '02'),
('021404', 'Carhuapampa', '0214', '02'),
('021405', 'Cochas', '0214', '02'),
('021406', 'Congas', '0214', '02'),
('021407', 'Llipa', '0214', '02'),
('021408', 'San Cristóbal de Rajan', '0214', '02'),
('021409', 'San Pedro', '0214', '02'),
('021410', 'Santiago de Chilcas', '0214', '02'),
('021501', 'Cabana', '0215', '02'),
('021502', 'Bolognesi', '0215', '02'),
('021503', 'Conchucos', '0215', '02'),
('021504', 'Huacaschuque', '0215', '02'),
('021505', 'Huandoval', '0215', '02'),
('021506', 'Lacabamba', '0215', '02'),
('021507', 'Llapo', '0215', '02'),
('021508', 'Pallasca', '0215', '02'),
('021509', 'Pampas', '0215', '02'),
('021510', 'Santa Rosa', '0215', '02'),
('021511', 'Tauca', '0215', '02'),
('021601', 'Pomabamba', '0216', '02'),
('021602', 'Huayllan', '0216', '02'),
('021603', 'Parobamba', '0216', '02'),
('021604', 'Quinuabamba', '0216', '02'),
('021701', 'Recuay', '0217', '02'),
('021702', 'Catac', '0217', '02'),
('021703', 'Cotaparaco', '0217', '02'),
('021704', 'Huayllapampa', '0217', '02'),
('021705', 'Llacllin', '0217', '02'),
('021706', 'Marca', '0217', '02'),
('021707', 'Pampas Chico', '0217', '02'),
('021708', 'Pararin', '0217', '02'),
('021709', 'Tapacocha', '0217', '02'),
('021710', 'Ticapampa', '0217', '02'),
('021801', 'Chimbote', '0218', '02'),
('021802', 'Cáceres del Perú', '0218', '02'),
('021803', 'Coishco', '0218', '02'),
('021804', 'Macate', '0218', '02'),
('021805', 'Moro', '0218', '02'),
('021806', 'Nepeña', '0218', '02'),
('021807', 'Samanco', '0218', '02'),
('021808', 'Santa', '0218', '02'),
('021809', 'Nuevo Chimbote', '0218', '02'),
('021901', 'Sihuas', '0219', '02'),
('021902', 'Acobamba', '0219', '02'),
('021903', 'Alfonso Ugarte', '0219', '02'),
('021904', 'Cashapampa', '0219', '02'),
('021905', 'Chingalpo', '0219', '02'),
('021906', 'Huayllabamba', '0219', '02'),
('021907', 'Quiches', '0219', '02'),
('021908', 'Ragash', '0219', '02'),
('021909', 'San Juan', '0219', '02'),
('021910', 'Sicsibamba', '0219', '02'),
('022001', 'Yungay', '0220', '02'),
('022002', 'Cascapara', '0220', '02'),
('022003', 'Mancos', '0220', '02'),
('022004', 'Matacoto', '0220', '02'),
('022005', 'Quillo', '0220', '02'),
('022006', 'Ranrahirca', '0220', '02'),
('022007', 'Shupluy', '0220', '02'),
('022008', 'Yanama', '0220', '02'),
('030101', 'Abancay', '0301', '03'),
('030102', 'Chacoche', '0301', '03'),
('030103', 'Circa', '0301', '03'),
('030104', 'Curahuasi', '0301', '03'),
('030105', 'Huanipaca', '0301', '03'),
('030106', 'Lambrama', '0301', '03'),
('030107', 'Pichirhua', '0301', '03'),
('030108', 'San Pedro de Cachora', '0301', '03'),
('030109', 'Tamburco', '0301', '03'),
('030201', 'Andahuaylas', '0302', '03'),
('030202', 'Andarapa', '0302', '03'),
('030203', 'Chiara', '0302', '03'),
('030204', 'Huancarama', '0302', '03'),
('030205', 'Huancaray', '0302', '03'),
('030206', 'Huayana', '0302', '03'),
('030207', 'Kishuara', '0302', '03'),
('030208', 'Pacobamba', '0302', '03'),
('030209', 'Pacucha', '0302', '03'),
('030210', 'Pampachiri', '0302', '03'),
('030211', 'Pomacocha', '0302', '03'),
('030212', 'San Antonio de Cachi', '0302', '03'),
('030213', 'San Jerónimo', '0302', '03'),
('030214', 'San Miguel de Chaccrampa', '0302', '03'),
('030215', 'Santa María de Chicmo', '0302', '03'),
('030216', 'Talavera', '0302', '03'),
('030217', 'Tumay Huaraca', '0302', '03'),
('030218', 'Turpo', '0302', '03'),
('030219', 'Kaquiabamba', '0302', '03'),
('030220', 'José María Arguedas', '0302', '03'),
('030301', 'Antabamba', '0303', '03'),
('030302', 'El Oro', '0303', '03'),
('030303', 'Huaquirca', '0303', '03'),
('030304', 'Juan Espinoza Medrano', '0303', '03'),
('030305', 'Oropesa', '0303', '03'),
('030306', 'Pachaconas', '0303', '03'),
('030307', 'Sabaino', '0303', '03'),
('030401', 'Chalhuanca', '0304', '03'),
('030402', 'Capaya', '0304', '03'),
('030403', 'Caraybamba', '0304', '03'),
('030404', 'Chapimarca', '0304', '03'),
('030405', 'Colcabamba', '0304', '03'),
('030406', 'Cotaruse', '0304', '03'),
('030407', 'Ihuayllo', '0304', '03'),
('030408', 'Justo Apu Sahuaraura', '0304', '03'),
('030409', 'Lucre', '0304', '03'),
('030410', 'Pocohuanca', '0304', '03'),
('030411', 'San Juan de Chacña', '0304', '03'),
('030412', 'Sañayca', '0304', '03'),
('030413', 'Soraya', '0304', '03'),
('030414', 'Tapairihua', '0304', '03'),
('030415', 'Tintay', '0304', '03'),
('030416', 'Toraya', '0304', '03'),
('030417', 'Yanaca', '0304', '03'),
('030501', 'Tambobamba', '0305', '03'),
('030502', 'Cotabambas', '0305', '03'),
('030503', 'Coyllurqui', '0305', '03'),
('030504', 'Haquira', '0305', '03'),
('030505', 'Mara', '0305', '03'),
('030506', 'Challhuahuacho', '0305', '03'),
('030601', 'Chincheros', '0306', '03'),
('030602', 'Anco_Huallo', '0306', '03'),
('030603', 'Cocharcas', '0306', '03'),
('030604', 'Huaccana', '0306', '03'),
('030605', 'Ocobamba', '0306', '03'),
('030606', 'Ongoy', '0306', '03'),
('030607', 'Uranmarca', '0306', '03'),
('030608', 'Ranracancha', '0306', '03'),
('030609', 'Rocchacc', '0306', '03'),
('030610', 'El Porvenir', '0306', '03'),
('030611', 'Los Chankas', '0306', '03'),
('030701', 'Chuquibambilla', '0307', '03'),
('030702', 'Curpahuasi', '0307', '03'),
('030703', 'Gamarra', '0307', '03'),
('030704', 'Huayllati', '0307', '03'),
('030705', 'Mamara', '0307', '03'),
('030706', 'Micaela Bastidas', '0307', '03'),
('030707', 'Pataypampa', '0307', '03'),
('030708', 'Progreso', '0307', '03'),
('030709', 'San Antonio', '0307', '03'),
('030710', 'Santa Rosa', '0307', '03'),
('030711', 'Turpay', '0307', '03'),
('030712', 'Vilcabamba', '0307', '03'),
('030713', 'Virundo', '0307', '03'),
('030714', 'Curasco', '0307', '03'),
('040101', 'Arequipa', '0401', '04'),
('040102', 'Alto Selva Alegre', '0401', '04'),
('040103', 'Cayma', '0401', '04'),
('040104', 'Cerro Colorado', '0401', '04'),
('040105', 'Characato', '0401', '04'),
('040106', 'Chiguata', '0401', '04'),
('040107', 'Jacobo Hunter', '0401', '04'),
('040108', 'La Joya', '0401', '04'),
('040109', 'Mariano Melgar', '0401', '04'),
('040110', 'Miraflores', '0401', '04'),
('040111', 'Mollebaya', '0401', '04'),
('040112', 'Paucarpata', '0401', '04'),
('040113', 'Pocsi', '0401', '04'),
('040114', 'Polobaya', '0401', '04'),
('040115', 'Quequeña', '0401', '04'),
('040116', 'Sabandia', '0401', '04'),
('040117', 'Sachaca', '0401', '04'),
('040118', 'San Juan de Siguas', '0401', '04'),
('040119', 'San Juan de Tarucani', '0401', '04'),
('040120', 'Santa Isabel de Siguas', '0401', '04'),
('040121', 'Santa Rita de Siguas', '0401', '04'),
('040122', 'Socabaya', '0401', '04'),
('040123', 'Tiabaya', '0401', '04'),
('040124', 'Uchumayo', '0401', '04'),
('040125', 'Vitor', '0401', '04'),
('040126', 'Yanahuara', '0401', '04'),
('040127', 'Yarabamba', '0401', '04'),
('040128', 'Yura', '0401', '04'),
('040129', 'José Luis Bustamante Y Rivero', '0401', '04'),
('040201', 'Camaná', '0402', '04'),
('040202', 'José María Quimper', '0402', '04'),
('040203', 'Mariano Nicolás Valcárcel', '0402', '04'),
('040204', 'Mariscal Cáceres', '0402', '04'),
('040205', 'Nicolás de Pierola', '0402', '04'),
('040206', 'Ocoña', '0402', '04'),
('040207', 'Quilca', '0402', '04'),
('040208', 'Samuel Pastor', '0402', '04'),
('040301', 'Caravelí', '0403', '04'),
('040302', 'Acarí', '0403', '04'),
('040303', 'Atico', '0403', '04'),
('040304', 'Atiquipa', '0403', '04'),
('040305', 'Bella Unión', '0403', '04'),
('040306', 'Cahuacho', '0403', '04'),
('040307', 'Chala', '0403', '04'),
('040308', 'Chaparra', '0403', '04'),
('040309', 'Huanuhuanu', '0403', '04'),
('040310', 'Jaqui', '0403', '04'),
('040311', 'Lomas', '0403', '04'),
('040312', 'Quicacha', '0403', '04'),
('040313', 'Yauca', '0403', '04'),
('040401', 'Aplao', '0404', '04'),
('040402', 'Andagua', '0404', '04'),
('040403', 'Ayo', '0404', '04'),
('040404', 'Chachas', '0404', '04'),
('040405', 'Chilcaymarca', '0404', '04'),
('040406', 'Choco', '0404', '04'),
('040407', 'Huancarqui', '0404', '04'),
('040408', 'Machaguay', '0404', '04'),
('040409', 'Orcopampa', '0404', '04'),
('040410', 'Pampacolca', '0404', '04'),
('040411', 'Tipan', '0404', '04'),
('040412', 'Uñon', '0404', '04'),
('040413', 'Uraca', '0404', '04'),
('040414', 'Viraco', '0404', '04'),
('040501', 'Chivay', '0405', '04'),
('040502', 'Achoma', '0405', '04'),
('040503', 'Cabanaconde', '0405', '04'),
('040504', 'Callalli', '0405', '04'),
('040505', 'Caylloma', '0405', '04'),
('040506', 'Coporaque', '0405', '04'),
('040507', 'Huambo', '0405', '04'),
('040508', 'Huanca', '0405', '04'),
('040509', 'Ichupampa', '0405', '04'),
('040510', 'Lari', '0405', '04'),
('040511', 'Lluta', '0405', '04'),
('040512', 'Maca', '0405', '04'),
('040513', 'Madrigal', '0405', '04'),
('040514', 'San Antonio de Chuca', '0405', '04'),
('040515', 'Sibayo', '0405', '04'),
('040516', 'Tapay', '0405', '04'),
('040517', 'Tisco', '0405', '04'),
('040518', 'Tuti', '0405', '04'),
('040519', 'Yanque', '0405', '04'),
('040520', 'Majes', '0405', '04'),
('040601', 'Chuquibamba', '0406', '04'),
('040602', 'Andaray', '0406', '04'),
('040603', 'Cayarani', '0406', '04'),
('040604', 'Chichas', '0406', '04'),
('040605', 'Iray', '0406', '04'),
('040606', 'Río Grande', '0406', '04'),
('040607', 'Salamanca', '0406', '04'),
('040608', 'Yanaquihua', '0406', '04'),
('040701', 'Mollendo', '0407', '04'),
('040702', 'Cocachacra', '0407', '04'),
('040703', 'Dean Valdivia', '0407', '04'),
('040704', 'Islay', '0407', '04'),
('040705', 'Mejia', '0407', '04'),
('040706', 'Punta de Bombón', '0407', '04'),
('040801', 'Cotahuasi', '0408', '04'),
('040802', 'Alca', '0408', '04'),
('040803', 'Charcana', '0408', '04'),
('040804', 'Huaynacotas', '0408', '04'),
('040805', 'Pampamarca', '0408', '04'),
('040806', 'Puyca', '0408', '04'),
('040807', 'Quechualla', '0408', '04'),
('040808', 'Sayla', '0408', '04'),
('040809', 'Tauria', '0408', '04'),
('040810', 'Tomepampa', '0408', '04'),
('040811', 'Toro', '0408', '04'),
('050101', 'Ayacucho', '0501', '05'),
('050102', 'Acocro', '0501', '05'),
('050103', 'Acos Vinchos', '0501', '05'),
('050104', 'Carmen Alto', '0501', '05'),
('050105', 'Chiara', '0501', '05'),
('050106', 'Ocros', '0501', '05'),
('050107', 'Pacaycasa', '0501', '05'),
('050108', 'Quinua', '0501', '05'),
('050109', 'San José de Ticllas', '0501', '05'),
('050110', 'San Juan Bautista', '0501', '05'),
('050111', 'Santiago de Pischa', '0501', '05'),
('050112', 'Socos', '0501', '05'),
('050113', 'Tambillo', '0501', '05'),
('050114', 'Vinchos', '0501', '05'),
('050115', 'Jesús Nazareno', '0501', '05'),
('050116', 'Andrés Avelino Cáceres Dorregaray', '0501', '05'),
('050201', 'Cangallo', '0502', '05'),
('050202', 'Chuschi', '0502', '05'),
('050203', 'Los Morochucos', '0502', '05'),
('050204', 'María Parado de Bellido', '0502', '05'),
('050205', 'Paras', '0502', '05'),
('050206', 'Totos', '0502', '05'),
('050301', 'Sancos', '0503', '05'),
('050302', 'Carapo', '0503', '05'),
('050303', 'Sacsamarca', '0503', '05'),
('050304', 'Santiago de Lucanamarca', '0503', '05'),
('050401', 'Huanta', '0504', '05'),
('050402', 'Ayahuanco', '0504', '05'),
('050403', 'Huamanguilla', '0504', '05'),
('050404', 'Iguain', '0504', '05'),
('050405', 'Luricocha', '0504', '05'),
('050406', 'Santillana', '0504', '05'),
('050407', 'Sivia', '0504', '05'),
('050408', 'Llochegua', '0504', '05'),
('050409', 'Canayre', '0504', '05'),
('050410', 'Uchuraccay', '0504', '05'),
('050411', 'Pucacolpa', '0504', '05'),
('050412', 'Chaca', '0504', '05'),
('050501', 'San Miguel', '0505', '05'),
('050502', 'Anco', '0505', '05'),
('050503', 'Ayna', '0505', '05'),
('050504', 'Chilcas', '0505', '05'),
('050505', 'Chungui', '0505', '05'),
('050506', 'Luis Carranza', '0505', '05'),
('050507', 'Santa Rosa', '0505', '05'),
('050508', 'Tambo', '0505', '05'),
('050509', 'Samugari', '0505', '05'),
('050510', 'Anchihuay', '0505', '05'),
('050511', 'Oronccoy', '0505', '05'),
('050601', 'Puquio', '0506', '05'),
('050602', 'Aucara', '0506', '05'),
('050603', 'Cabana', '0506', '05'),
('050604', 'Carmen Salcedo', '0506', '05'),
('050605', 'Chaviña', '0506', '05'),
('050606', 'Chipao', '0506', '05'),
('050607', 'Huac-Huas', '0506', '05'),
('050608', 'Laramate', '0506', '05'),
('050609', 'Leoncio Prado', '0506', '05'),
('050610', 'Llauta', '0506', '05'),
('050611', 'Lucanas', '0506', '05'),
('050612', 'Ocaña', '0506', '05'),
('050613', 'Otoca', '0506', '05'),
('050614', 'Saisa', '0506', '05'),
('050615', 'San Cristóbal', '0506', '05'),
('050616', 'San Juan', '0506', '05'),
('050617', 'San Pedro', '0506', '05'),
('050618', 'San Pedro de Palco', '0506', '05'),
('050619', 'Sancos', '0506', '05'),
('050620', 'Santa Ana de Huaycahuacho', '0506', '05'),
('050621', 'Santa Lucia', '0506', '05'),
('050701', 'Coracora', '0507', '05'),
('050702', 'Chumpi', '0507', '05'),
('050703', 'Coronel Castañeda', '0507', '05'),
('050704', 'Pacapausa', '0507', '05'),
('050705', 'Pullo', '0507', '05'),
('050706', 'Puyusca', '0507', '05'),
('050707', 'San Francisco de Ravacayco', '0507', '05'),
('050708', 'Upahuacho', '0507', '05'),
('050801', 'Pausa', '0508', '05'),
('050802', 'Colta', '0508', '05'),
('050803', 'Corculla', '0508', '05'),
('050804', 'Lampa', '0508', '05'),
('050805', 'Marcabamba', '0508', '05'),
('050806', 'Oyolo', '0508', '05'),
('050807', 'Pararca', '0508', '05'),
('050808', 'San Javier de Alpabamba', '0508', '05'),
('050809', 'San José de Ushua', '0508', '05'),
('050810', 'Sara Sara', '0508', '05'),
('050901', 'Querobamba', '0509', '05'),
('050902', 'Belén', '0509', '05'),
('050903', 'Chalcos', '0509', '05'),
('050904', 'Chilcayoc', '0509', '05'),
('050905', 'Huacaña', '0509', '05'),
('050906', 'Morcolla', '0509', '05'),
('050907', 'Paico', '0509', '05'),
('050908', 'San Pedro de Larcay', '0509', '05'),
('050909', 'San Salvador de Quije', '0509', '05'),
('050910', 'Santiago de Paucaray', '0509', '05'),
('050911', 'Soras', '0509', '05'),
('051001', 'Huancapi', '0510', '05'),
('051002', 'Alcamenca', '0510', '05'),
('051003', 'Apongo', '0510', '05'),
('051004', 'Asquipata', '0510', '05'),
('051005', 'Canaria', '0510', '05'),
('051006', 'Cayara', '0510', '05'),
('051007', 'Colca', '0510', '05'),
('051008', 'Huamanquiquia', '0510', '05'),
('051009', 'Huancaraylla', '0510', '05'),
('051010', 'Hualla', '0510', '05'),
('051011', 'Sarhua', '0510', '05'),
('051012', 'Vilcanchos', '0510', '05'),
('051101', 'Vilcas Huaman', '0511', '05'),
('051102', 'Accomarca', '0511', '05'),
('051103', 'Carhuanca', '0511', '05'),
('051104', 'Concepción', '0511', '05'),
('051105', 'Huambalpa', '0511', '05'),
('051106', 'Independencia', '0511', '05'),
('051107', 'Saurama', '0511', '05'),
('051108', 'Vischongo', '0511', '05'),
('060101', 'Cajamarca', '0601', '06'),
('060102', 'Asunción', '0601', '06'),
('060103', 'Chetilla', '0601', '06'),
('060104', 'Cospan', '0601', '06'),
('060105', 'Encañada', '0601', '06'),
('060106', 'Jesús', '0601', '06'),
('060107', 'Llacanora', '0601', '06'),
('060108', 'Los Baños del Inca', '0601', '06'),
('060109', 'Magdalena', '0601', '06'),
('060110', 'Matara', '0601', '06'),
('060111', 'Namora', '0601', '06'),
('060112', 'San Juan', '0601', '06'),
('060201', 'Cajabamba', '0602', '06'),
('060202', 'Cachachi', '0602', '06'),
('060203', 'Condebamba', '0602', '06'),
('060204', 'Sitacocha', '0602', '06'),
('060301', 'Celendín', '0603', '06'),
('060302', 'Chumuch', '0603', '06'),
('060303', 'Cortegana', '0603', '06'),
('060304', 'Huasmin', '0603', '06'),
('060305', 'Jorge Chávez', '0603', '06'),
('060306', 'José Gálvez', '0603', '06'),
('060307', 'Miguel Iglesias', '0603', '06'),
('060308', 'Oxamarca', '0603', '06'),
('060309', 'Sorochuco', '0603', '06'),
('060310', 'Sucre', '0603', '06'),
('060311', 'Utco', '0603', '06'),
('060312', 'La Libertad de Pallan', '0603', '06'),
('060401', 'Chota', '0604', '06'),
('060402', 'Anguia', '0604', '06'),
('060403', 'Chadin', '0604', '06'),
('060404', 'Chiguirip', '0604', '06'),
('060405', 'Chimban', '0604', '06'),
('060406', 'Choropampa', '0604', '06'),
('060407', 'Cochabamba', '0604', '06'),
('060408', 'Conchan', '0604', '06'),
('060409', 'Huambos', '0604', '06'),
('060410', 'Lajas', '0604', '06'),
('060411', 'Llama', '0604', '06'),
('060412', 'Miracosta', '0604', '06'),
('060413', 'Paccha', '0604', '06'),
('060414', 'Pion', '0604', '06'),
('060415', 'Querocoto', '0604', '06'),
('060416', 'San Juan de Licupis', '0604', '06'),
('060417', 'Tacabamba', '0604', '06'),
('060418', 'Tocmoche', '0604', '06'),
('060419', 'Chalamarca', '0604', '06'),
('060501', 'Contumaza', '0605', '06'),
('060502', 'Chilete', '0605', '06'),
('060503', 'Cupisnique', '0605', '06'),
('060504', 'Guzmango', '0605', '06'),
('060505', 'San Benito', '0605', '06'),
('060506', 'Santa Cruz de Toledo', '0605', '06'),
('060507', 'Tantarica', '0605', '06'),
('060508', 'Yonan', '0605', '06'),
('060601', 'Cutervo', '0606', '06'),
('060602', 'Callayuc', '0606', '06'),
('060603', 'Choros', '0606', '06'),
('060604', 'Cujillo', '0606', '06'),
('060605', 'La Ramada', '0606', '06'),
('060606', 'Pimpingos', '0606', '06'),
('060607', 'Querocotillo', '0606', '06'),
('060608', 'San Andrés de Cutervo', '0606', '06'),
('060609', 'San Juan de Cutervo', '0606', '06'),
('060610', 'San Luis de Lucma', '0606', '06'),
('060611', 'Santa Cruz', '0606', '06'),
('060612', 'Santo Domingo de la Capilla', '0606', '06'),
('060613', 'Santo Tomas', '0606', '06'),
('060614', 'Socota', '0606', '06'),
('060615', 'Toribio Casanova', '0606', '06'),
('060701', 'Bambamarca', '0607', '06'),
('060702', 'Chugur', '0607', '06'),
('060703', 'Hualgayoc', '0607', '06'),
('060801', 'Jaén', '0608', '06'),
('060802', 'Bellavista', '0608', '06'),
('060803', 'Chontali', '0608', '06'),
('060804', 'Colasay', '0608', '06'),
('060805', 'Huabal', '0608', '06'),
('060806', 'Las Pirias', '0608', '06'),
('060807', 'Pomahuaca', '0608', '06'),
('060808', 'Pucara', '0608', '06'),
('060809', 'Sallique', '0608', '06'),
('060810', 'San Felipe', '0608', '06'),
('060811', 'San José del Alto', '0608', '06'),
('060812', 'Santa Rosa', '0608', '06'),
('060901', 'San Ignacio', '0609', '06'),
('060902', 'Chirinos', '0609', '06'),
('060903', 'Huarango', '0609', '06'),
('060904', 'La Coipa', '0609', '06'),
('060905', 'Namballe', '0609', '06'),
('060906', 'San José de Lourdes', '0609', '06'),
('060907', 'Tabaconas', '0609', '06'),
('061001', 'Pedro Gálvez', '0610', '06'),
('061002', 'Chancay', '0610', '06'),
('061003', 'Eduardo Villanueva', '0610', '06'),
('061004', 'Gregorio Pita', '0610', '06'),
('061005', 'Ichocan', '0610', '06'),
('061006', 'José Manuel Quiroz', '0610', '06'),
('061007', 'José Sabogal', '0610', '06'),
('061101', 'San Miguel', '0611', '06'),
('061102', 'Bolívar', '0611', '06'),
('061103', 'Calquis', '0611', '06'),
('061104', 'Catilluc', '0611', '06'),
('061105', 'El Prado', '0611', '06'),
('061106', 'La Florida', '0611', '06'),
('061107', 'Llapa', '0611', '06'),
('061108', 'Nanchoc', '0611', '06'),
('061109', 'Niepos', '0611', '06'),
('061110', 'San Gregorio', '0611', '06'),
('061111', 'San Silvestre de Cochan', '0611', '06'),
('061112', 'Tongod', '0611', '06'),
('061113', 'Unión Agua Blanca', '0611', '06'),
('061201', 'San Pablo', '0612', '06'),
('061202', 'San Bernardino', '0612', '06'),
('061203', 'San Luis', '0612', '06'),
('061204', 'Tumbaden', '0612', '06'),
('061301', 'Santa Cruz', '0613', '06'),
('061302', 'Andabamba', '0613', '06'),
('061303', 'Catache', '0613', '06'),
('061304', 'Chancaybaños', '0613', '06'),
('061305', 'La Esperanza', '0613', '06'),
('061306', 'Ninabamba', '0613', '06'),
('061307', 'Pulan', '0613', '06'),
('061308', 'Saucepampa', '0613', '06'),
('061309', 'Sexi', '0613', '06'),
('061310', 'Uticyacu', '0613', '06'),
('061311', 'Yauyucan', '0613', '06'),
('070101', 'Callao', '0701', '07'),
('070102', 'Bellavista', '0701', '07'),
('070103', 'Carmen de la Legua Reynoso', '0701', '07'),
('070104', 'La Perla', '0701', '07'),
('070105', 'La Punta', '0701', '07'),
('070106', 'Ventanilla', '0701', '07'),
('070107', 'Mi Perú', '0701', '07'),
('080101', 'Cusco', '0801', '08'),
('080102', 'Ccorca', '0801', '08'),
('080103', 'Poroy', '0801', '08'),
('080104', 'San Jerónimo', '0801', '08'),
('080105', 'San Sebastian', '0801', '08'),
('080106', 'Santiago', '0801', '08'),
('080107', 'Saylla', '0801', '08'),
('080108', 'Wanchaq', '0801', '08'),
('080201', 'Acomayo', '0802', '08'),
('080202', 'Acopia', '0802', '08'),
('080203', 'Acos', '0802', '08'),
('080204', 'Mosoc Llacta', '0802', '08'),
('080205', 'Pomacanchi', '0802', '08'),
('080206', 'Rondocan', '0802', '08'),
('080207', 'Sangarara', '0802', '08'),
('080301', 'Anta', '0803', '08'),
('080302', 'Ancahuasi', '0803', '08'),
('080303', 'Cachimayo', '0803', '08'),
('080304', 'Chinchaypujio', '0803', '08'),
('080305', 'Huarocondo', '0803', '08'),
('080306', 'Limatambo', '0803', '08'),
('080307', 'Mollepata', '0803', '08'),
('080308', 'Pucyura', '0803', '08'),
('080309', 'Zurite', '0803', '08'),
('080401', 'Calca', '0804', '08'),
('080402', 'Coya', '0804', '08'),
('080403', 'Lamay', '0804', '08'),
('080404', 'Lares', '0804', '08'),
('080405', 'Pisac', '0804', '08'),
('080406', 'San Salvador', '0804', '08'),
('080407', 'Taray', '0804', '08'),
('080408', 'Yanatile', '0804', '08'),
('080501', 'Yanaoca', '0805', '08'),
('080502', 'Checca', '0805', '08'),
('080503', 'Kunturkanki', '0805', '08'),
('080504', 'Langui', '0805', '08'),
('080505', 'Layo', '0805', '08'),
('080506', 'Pampamarca', '0805', '08'),
('080507', 'Quehue', '0805', '08'),
('080508', 'Tupac Amaru', '0805', '08'),
('080601', 'Sicuani', '0806', '08'),
('080602', 'Checacupe', '0806', '08'),
('080603', 'Combapata', '0806', '08'),
('080604', 'Marangani', '0806', '08'),
('080605', 'Pitumarca', '0806', '08'),
('080606', 'San Pablo', '0806', '08'),
('080607', 'San Pedro', '0806', '08'),
('080608', 'Tinta', '0806', '08'),
('080701', 'Santo Tomas', '0807', '08'),
('080702', 'Capacmarca', '0807', '08'),
('080703', 'Chamaca', '0807', '08'),
('080704', 'Colquemarca', '0807', '08'),
('080705', 'Livitaca', '0807', '08'),
('080706', 'Llusco', '0807', '08'),
('080707', 'Quiñota', '0807', '08'),
('080708', 'Velille', '0807', '08'),
('080801', 'Espinar', '0808', '08'),
('080802', 'Condoroma', '0808', '08'),
('080803', 'Coporaque', '0808', '08'),
('080804', 'Ocoruro', '0808', '08'),
('080805', 'Pallpata', '0808', '08'),
('080806', 'Pichigua', '0808', '08'),
('080807', 'Suyckutambo', '0808', '08'),
('080808', 'Alto Pichigua', '0808', '08'),
('080901', 'Santa Ana', '0809', '08'),
('080902', 'Echarate', '0809', '08'),
('080903', 'Huayopata', '0809', '08'),
('080904', 'Maranura', '0809', '08'),
('080905', 'Ocobamba', '0809', '08'),
('080906', 'Quellouno', '0809', '08'),
('080907', 'Kimbiri', '0809', '08'),
('080908', 'Santa Teresa', '0809', '08'),
('080909', 'Vilcabamba', '0809', '08'),
('080910', 'Pichari', '0809', '08'),
('080911', 'Inkawasi', '0809', '08'),
('080912', 'Villa Virgen', '0809', '08'),
('080913', 'Villa Kintiarina', '0809', '08'),
('080914', 'Megantoni', '0809', '08'),
('081001', 'Paruro', '0810', '08'),
('081002', 'Accha', '0810', '08'),
('081003', 'Ccapi', '0810', '08'),
('081004', 'Colcha', '0810', '08'),
('081005', 'Huanoquite', '0810', '08'),
('081006', 'Omachaç', '0810', '08'),
('081007', 'Paccaritambo', '0810', '08'),
('081008', 'Pillpinto', '0810', '08'),
('081009', 'Yaurisque', '0810', '08'),
('081101', 'Paucartambo', '0811', '08'),
('081102', 'Caicay', '0811', '08'),
('081103', 'Challabamba', '0811', '08'),
('081104', 'Colquepata', '0811', '08'),
('081105', 'Huancarani', '0811', '08'),
('081106', 'Kosñipata', '0811', '08'),
('081201', 'Urcos', '0812', '08'),
('081202', 'Andahuaylillas', '0812', '08'),
('081203', 'Camanti', '0812', '08'),
('081204', 'Ccarhuayo', '0812', '08'),
('081205', 'Ccatca', '0812', '08'),
('081206', 'Cusipata', '0812', '08'),
('081207', 'Huaro', '0812', '08'),
('081208', 'Lucre', '0812', '08'),
('081209', 'Marcapata', '0812', '08'),
('081210', 'Ocongate', '0812', '08'),
('081211', 'Oropesa', '0812', '08'),
('081212', 'Quiquijana', '0812', '08'),
('081301', 'Urubamba', '0813', '08'),
('081302', 'Chinchero', '0813', '08'),
('081303', 'Huayllabamba', '0813', '08'),
('081304', 'Machupicchu', '0813', '08'),
('081305', 'Maras', '0813', '08'),
('081306', 'Ollantaytambo', '0813', '08'),
('081307', 'Yucay', '0813', '08'),
('090101', 'Huancavelica', '0901', '09'),
('090102', 'Acobambilla', '0901', '09'),
('090103', 'Acoria', '0901', '09'),
('090104', 'Conayca', '0901', '09'),
('090105', 'Cuenca', '0901', '09'),
('090106', 'Huachocolpa', '0901', '09'),
('090107', 'Huayllahuara', '0901', '09'),
('090108', 'Izcuchaca', '0901', '09'),
('090109', 'Laria', '0901', '09'),
('090110', 'Manta', '0901', '09'),
('090111', 'Mariscal Cáceres', '0901', '09'),
('090112', 'Moya', '0901', '09'),
('090113', 'Nuevo Occoro', '0901', '09'),
('090114', 'Palca', '0901', '09'),
('090115', 'Pilchaca', '0901', '09'),
('090116', 'Vilca', '0901', '09'),
('090117', 'Yauli', '0901', '09'),
('090118', 'Ascensión', '0901', '09'),
('090119', 'Huando', '0901', '09'),
('090201', 'Acobamba', '0902', '09'),
('090202', 'Andabamba', '0902', '09'),
('090203', 'Anta', '0902', '09'),
('090204', 'Caja', '0902', '09'),
('090205', 'Marcas', '0902', '09'),
('090206', 'Paucara', '0902', '09'),
('090207', 'Pomacocha', '0902', '09'),
('090208', 'Rosario', '0902', '09'),
('090301', 'Lircay', '0903', '09'),
('090302', 'Anchonga', '0903', '09'),
('090303', 'Callanmarca', '0903', '09'),
('090304', 'Ccochaccasa', '0903', '09'),
('090305', 'Chincho', '0903', '09'),
('090306', 'Congalla', '0903', '09'),
('090307', 'Huanca-Huanca', '0903', '09'),
('090308', 'Huayllay Grande', '0903', '09'),
('090309', 'Julcamarca', '0903', '09'),
('090310', 'San Antonio de Antaparco', '0903', '09'),
('090311', 'Santo Tomas de Pata', '0903', '09'),
('090312', 'Secclla', '0903', '09'),
('090401', 'Castrovirreyna', '0904', '09'),
('090402', 'Arma', '0904', '09'),
('090403', 'Aurahua', '0904', '09'),
('090404', 'Capillas', '0904', '09'),
('090405', 'Chupamarca', '0904', '09'),
('090406', 'Cocas', '0904', '09'),
('090407', 'Huachos', '0904', '09'),
('090408', 'Huamatambo', '0904', '09'),
('090409', 'Mollepampa', '0904', '09'),
('090410', 'San Juan', '0904', '09'),
('090411', 'Santa Ana', '0904', '09'),
('090412', 'Tantara', '0904', '09'),
('090413', 'Ticrapo', '0904', '09'),
('090501', 'Churcampa', '0905', '09'),
('090502', 'Anco', '0905', '09'),
('090503', 'Chinchihuasi', '0905', '09'),
('090504', 'El Carmen', '0905', '09'),
('090505', 'La Merced', '0905', '09'),
('090506', 'Locroja', '0905', '09'),
('090507', 'Paucarbamba', '0905', '09'),
('090508', 'San Miguel de Mayocc', '0905', '09'),
('090509', 'San Pedro de Coris', '0905', '09'),
('090510', 'Pachamarca', '0905', '09'),
('090511', 'Cosme', '0905', '09'),
('090601', 'Huaytara', '0906', '09'),
('090602', 'Ayavi', '0906', '09'),
('090603', 'Córdova', '0906', '09'),
('090604', 'Huayacundo Arma', '0906', '09'),
('090605', 'Laramarca', '0906', '09'),
('090606', 'Ocoyo', '0906', '09'),
('090607', 'Pilpichaca', '0906', '09'),
('090608', 'Querco', '0906', '09'),
('090609', 'Quito-Arma', '0906', '09'),
('090610', 'San Antonio de Cusicancha', '0906', '09'),
('090611', 'San Francisco de Sangayaico', '0906', '09'),
('090612', 'San Isidro', '0906', '09'),
('090613', 'Santiago de Chocorvos', '0906', '09'),
('090614', 'Santiago de Quirahuara', '0906', '09'),
('090615', 'Santo Domingo de Capillas', '0906', '09'),
('090616', 'Tambo', '0906', '09'),
('090701', 'Pampas', '0907', '09'),
('090702', 'Acostambo', '0907', '09'),
('090703', 'Acraquia', '0907', '09'),
('090704', 'Ahuaycha', '0907', '09'),
('090705', 'Colcabamba', '0907', '09'),
('090706', 'Daniel Hernández', '0907', '09'),
('090707', 'Huachocolpa', '0907', '09'),
('090709', 'Huaribamba', '0907', '09'),
('090710', 'Ñahuimpuquio', '0907', '09'),
('090711', 'Pazos', '0907', '09'),
('090713', 'Quishuar', '0907', '09'),
('090714', 'Salcabamba', '0907', '09'),
('090715', 'Salcahuasi', '0907', '09'),
('090716', 'San Marcos de Rocchac', '0907', '09'),
('090717', 'Surcubamba', '0907', '09'),
('090718', 'Tintay Puncu', '0907', '09'),
('090719', 'Quichuas', '0907', '09'),
('090720', 'Andaymarca', '0907', '09'),
('090721', 'Roble', '0907', '09'),
('090722', 'Pichos', '0907', '09'),
('090723', 'Santiago de Tucuma', '0907', '09'),
('100101', 'Huanuco', '1001', '10'),
('100102', 'Amarilis', '1001', '10'),
('100103', 'Chinchao', '1001', '10'),
('100104', 'Churubamba', '1001', '10'),
('100105', 'Margos', '1001', '10'),
('100106', 'Quisqui (Kichki)', '1001', '10'),
('100107', 'San Francisco de Cayran', '1001', '10'),
('100108', 'San Pedro de Chaulan', '1001', '10'),
('100109', 'Santa María del Valle', '1001', '10'),
('100110', 'Yarumayo', '1001', '10'),
('100111', 'Pillco Marca', '1001', '10'),
('100112', 'Yacus', '1001', '10'),
('100113', 'San Pablo de Pillao', '1001', '10'),
('100201', 'Ambo', '1002', '10'),
('100202', 'Cayna', '1002', '10'),
('100203', 'Colpas', '1002', '10'),
('100204', 'Conchamarca', '1002', '10'),
('100205', 'Huacar', '1002', '10'),
('100206', 'San Francisco', '1002', '10'),
('100207', 'San Rafael', '1002', '10'),
('100208', 'Tomay Kichwa', '1002', '10'),
('100301', 'La Unión', '1003', '10'),
('100307', 'Chuquis', '1003', '10'),
('100311', 'Marías', '1003', '10'),
('100313', 'Pachas', '1003', '10'),
('100316', 'Quivilla', '1003', '10'),
('100317', 'Ripan', '1003', '10'),
('100321', 'Shunqui', '1003', '10'),
('100322', 'Sillapata', '1003', '10'),
('100323', 'Yanas', '1003', '10'),
('100401', 'Huacaybamba', '1004', '10'),
('100402', 'Canchabamba', '1004', '10'),
('100403', 'Cochabamba', '1004', '10'),
('100404', 'Pinra', '1004', '10'),
('100501', 'Llata', '1005', '10'),
('100502', 'Arancay', '1005', '10'),
('100503', 'Chavín de Pariarca', '1005', '10'),
('100504', 'Jacas Grande', '1005', '10'),
('100505', 'Jircan', '1005', '10'),
('100506', 'Miraflores', '1005', '10'),
('100507', 'Monzón', '1005', '10'),
('100508', 'Punchao', '1005', '10'),
('100509', 'Puños', '1005', '10'),
('100510', 'Singa', '1005', '10'),
('100511', 'Tantamayo', '1005', '10'),
('100601', 'Rupa-Rupa', '1006', '10'),
('100602', 'Daniel Alomía Robles', '1006', '10'),
('100603', 'Hermílio Valdizan', '1006', '10'),
('100604', 'José Crespo y Castillo', '1006', '10'),
('100605', 'Luyando', '1006', '10'),
('100606', 'Mariano Damaso Beraun', '1006', '10'),
('100607', 'Pucayacu', '1006', '10'),
('100608', 'Castillo Grande', '1006', '10'),
('100609', 'Pueblo Nuevo', '1006', '10'),
('100610', 'Santo Domingo de Anda', '1006', '10'),
('100701', 'Huacrachuco', '1007', '10'),
('100702', 'Cholon', '1007', '10'),
('100703', 'San Buenaventura', '1007', '10'),
('100704', 'La Morada', '1007', '10'),
('100705', 'Santa Rosa de Alto Yanajanca', '1007', '10'),
('100801', 'Panao', '1008', '10'),
('100802', 'Chaglla', '1008', '10'),
('100803', 'Molino', '1008', '10'),
('100804', 'Umari', '1008', '10'),
('100901', 'Puerto Inca', '1009', '10'),
('100902', 'Codo del Pozuzo', '1009', '10'),
('100903', 'Honoria', '1009', '10'),
('100904', 'Tournavista', '1009', '10'),
('100905', 'Yuyapichis', '1009', '10'),
('101001', 'Jesús', '1010', '10'),
('101002', 'Baños', '1010', '10'),
('101003', 'Jivia', '1010', '10'),
('101004', 'Queropalca', '1010', '10'),
('101005', 'Rondos', '1010', '10'),
('101006', 'San Francisco de Asís', '1010', '10'),
('101007', 'San Miguel de Cauri', '1010', '10'),
('101101', 'Chavinillo', '1011', '10'),
('101102', 'Cahuac', '1011', '10'),
('101103', 'Chacabamba', '1011', '10'),
('101104', 'Aparicio Pomares', '1011', '10'),
('101105', 'Jacas Chico', '1011', '10'),
('101106', 'Obas', '1011', '10'),
('101107', 'Pampamarca', '1011', '10'),
('101108', 'Choras', '1011', '10'),
('110101', 'Ica', '1101', '11'),
('110102', 'La Tinguiña', '1101', '11'),
('110103', 'Los Aquijes', '1101', '11'),
('110104', 'Ocucaje', '1101', '11'),
('110105', 'Pachacutec', '1101', '11'),
('110106', 'Parcona', '1101', '11'),
('110107', 'Pueblo Nuevo', '1101', '11'),
('110108', 'Salas', '1101', '11'),
('110109', 'San José de Los Molinos', '1101', '11'),
('110110', 'San Juan Bautista', '1101', '11'),
('110111', 'Santiago', '1101', '11'),
('110112', 'Subtanjalla', '1101', '11'),
('110113', 'Tate', '1101', '11'),
('110114', 'Yauca del Rosario', '1101', '11'),
('110201', 'Chincha Alta', '1102', '11'),
('110202', 'Alto Laran', '1102', '11'),
('110203', 'Chavin', '1102', '11'),
('110204', 'Chincha Baja', '1102', '11'),
('110205', 'El Carmen', '1102', '11'),
('110206', 'Grocio Prado', '1102', '11'),
('110207', 'Pueblo Nuevo', '1102', '11'),
('110208', 'San Juan de Yanac', '1102', '11'),
('110209', 'San Pedro de Huacarpana', '1102', '11'),
('110210', 'Sunampe', '1102', '11'),
('110211', 'Tambo de Mora', '1102', '11'),
('110301', 'Nasca', '1103', '11'),
('110302', 'Changuillo', '1103', '11'),
('110303', 'El Ingenio', '1103', '11'),
('110304', 'Marcona', '1103', '11'),
('110305', 'Vista Alegre', '1103', '11'),
('110401', 'Palpa', '1104', '11'),
('110402', 'Llipata', '1104', '11'),
('110403', 'Río Grande', '1104', '11'),
('110404', 'Santa Cruz', '1104', '11'),
('110405', 'Tibillo', '1104', '11'),
('110501', 'Pisco', '1105', '11'),
('110502', 'Huancano', '1105', '11'),
('110503', 'Humay', '1105', '11'),
('110504', 'Independencia', '1105', '11'),
('110505', 'Paracas', '1105', '11'),
('110506', 'San Andrés', '1105', '11'),
('110507', 'San Clemente', '1105', '11'),
('110508', 'Tupac Amaru Inca', '1105', '11'),
('120101', 'Huancayo', '1201', '12'),
('120104', 'Carhuacallanga', '1201', '12'),
('120105', 'Chacapampa', '1201', '12'),
('120106', 'Chicche', '1201', '12'),
('120107', 'Chilca', '1201', '12'),
('120108', 'Chongos Alto', '1201', '12'),
('120111', 'Chupuro', '1201', '12'),
('120112', 'Colca', '1201', '12'),
('120113', 'Cullhuas', '1201', '12'),
('120114', 'El Tambo', '1201', '12'),
('120116', 'Huacrapuquio', '1201', '12'),
('120117', 'Hualhuas', '1201', '12'),
('120119', 'Huancan', '1201', '12'),
('120120', 'Huasicancha', '1201', '12'),
('120121', 'Huayucachi', '1201', '12'),
('120122', 'Ingenio', '1201', '12'),
('120124', 'Pariahuanca', '1201', '12'),
('120125', 'Pilcomayo', '1201', '12'),
('120126', 'Pucara', '1201', '12'),
('120127', 'Quichuay', '1201', '12'),
('120128', 'Quilcas', '1201', '12'),
('120129', 'San Agustín', '1201', '12'),
('120130', 'San Jerónimo de Tunan', '1201', '12'),
('120132', 'Saño', '1201', '12'),
('120133', 'Sapallanga', '1201', '12'),
('120134', 'Sicaya', '1201', '12'),
('120135', 'Santo Domingo de Acobamba', '1201', '12'),
('120136', 'Viques', '1201', '12'),
('120201', 'Concepción', '1202', '12'),
('120202', 'Aco', '1202', '12'),
('120203', 'Andamarca', '1202', '12'),
('120204', 'Chambara', '1202', '12'),
('120205', 'Cochas', '1202', '12'),
('120206', 'Comas', '1202', '12'),
('120207', 'Heroínas Toledo', '1202', '12'),
('120208', 'Manzanares', '1202', '12'),
('120209', 'Mariscal Castilla', '1202', '12'),
('120210', 'Matahuasi', '1202', '12'),
('120211', 'Mito', '1202', '12'),
('120212', 'Nueve de Julio', '1202', '12'),
('120213', 'Orcotuna', '1202', '12'),
('120214', 'San José de Quero', '1202', '12'),
('120215', 'Santa Rosa de Ocopa', '1202', '12'),
('120301', 'Chanchamayo', '1203', '12'),
('120302', 'Perene', '1203', '12'),
('120303', 'Pichanaqui', '1203', '12'),
('120304', 'San Luis de Shuaro', '1203', '12'),
('120305', 'San Ramón', '1203', '12'),
('120306', 'Vitoc', '1203', '12'),
('120401', 'Jauja', '1204', '12'),
('120402', 'Acolla', '1204', '12'),
('120403', 'Apata', '1204', '12'),
('120404', 'Ataura', '1204', '12'),
('120405', 'Canchayllo', '1204', '12'),
('120406', 'Curicaca', '1204', '12'),
('120407', 'El Mantaro', '1204', '12'),
('120408', 'Huamali', '1204', '12'),
('120409', 'Huaripampa', '1204', '12'),
('120410', 'Huertas', '1204', '12'),
('120411', 'Janjaillo', '1204', '12'),
('120412', 'Julcán', '1204', '12'),
('120413', 'Leonor Ordóñez', '1204', '12'),
('120414', 'Llocllapampa', '1204', '12'),
('120415', 'Marco', '1204', '12'),
('120416', 'Masma', '1204', '12'),
('120417', 'Masma Chicche', '1204', '12'),
('120418', 'Molinos', '1204', '12'),
('120419', 'Monobamba', '1204', '12'),
('120420', 'Muqui', '1204', '12'),
('120421', 'Muquiyauyo', '1204', '12'),
('120422', 'Paca', '1204', '12'),
('120423', 'Paccha', '1204', '12'),
('120424', 'Pancan', '1204', '12'),
('120425', 'Parco', '1204', '12'),
('120426', 'Pomacancha', '1204', '12'),
('120427', 'Ricran', '1204', '12'),
('120428', 'San Lorenzo', '1204', '12'),
('120429', 'San Pedro de Chunan', '1204', '12'),
('120430', 'Sausa', '1204', '12'),
('120431', 'Sincos', '1204', '12'),
('120432', 'Tunan Marca', '1204', '12'),
('120433', 'Yauli', '1204', '12'),
('120434', 'Yauyos', '1204', '12'),
('120501', 'Junin', '1205', '12'),
('120502', 'Carhuamayo', '1205', '12'),
('120503', 'Ondores', '1205', '12'),
('120504', 'Ulcumayo', '1205', '12'),
('120601', 'Satipo', '1206', '12'),
('120602', 'Coviriali', '1206', '12'),
('120603', 'Llaylla', '1206', '12'),
('120604', 'Mazamari', '1206', '12'),
('120605', 'Pampa Hermosa', '1206', '12'),
('120606', 'Pangoa', '1206', '12'),
('120607', 'Río Negro', '1206', '12'),
('120608', 'Río Tambo', '1206', '12'),
('120609', 'Vizcatan del Ene', '1206', '12'),
('120701', 'Tarma', '1207', '12'),
('120702', 'Acobamba', '1207', '12'),
('120703', 'Huaricolca', '1207', '12'),
('120704', 'Huasahuasi', '1207', '12'),
('120705', 'La Unión', '1207', '12'),
('120706', 'Palca', '1207', '12'),
('120707', 'Palcamayo', '1207', '12'),
('120708', 'San Pedro de Cajas', '1207', '12'),
('120709', 'Tapo', '1207', '12'),
('120801', 'La Oroya', '1208', '12'),
('120802', 'Chacapalpa', '1208', '12'),
('120803', 'Huay-Huay', '1208', '12'),
('120804', 'Marcapomacocha', '1208', '12'),
('120805', 'Morococha', '1208', '12'),
('120806', 'Paccha', '1208', '12'),
('120807', 'Santa Bárbara de Carhuacayan', '1208', '12'),
('120808', 'Santa Rosa de Sacco', '1208', '12'),
('120809', 'Suitucancha', '1208', '12'),
('120810', 'Yauli', '1208', '12'),
('120901', 'Chupaca', '1209', '12'),
('120902', 'Ahuac', '1209', '12'),
('120903', 'Chongos Bajo', '1209', '12'),
('120904', 'Huachac', '1209', '12'),
('120905', 'Huamancaca Chico', '1209', '12'),
('120906', 'San Juan de Iscos', '1209', '12'),
('120907', 'San Juan de Jarpa', '1209', '12'),
('120908', 'Tres de Diciembre', '1209', '12'),
('120909', 'Yanacancha', '1209', '12'),
('130101', 'Trujillo', '1301', '13'),
('130102', 'El Porvenir', '1301', '13'),
('130103', 'Florencia de Mora', '1301', '13'),
('130104', 'Huanchaco', '1301', '13'),
('130105', 'La Esperanza', '1301', '13'),
('130106', 'Laredo', '1301', '13'),
('130107', 'Moche', '1301', '13'),
('130108', 'Poroto', '1301', '13'),
('130109', 'Salaverry', '1301', '13'),
('130110', 'Simbal', '1301', '13'),
('130111', 'Victor Larco Herrera', '1301', '13'),
('130201', 'Ascope', '1302', '13'),
('130202', 'Chicama', '1302', '13'),
('130203', 'Chocope', '1302', '13'),
('130204', 'Magdalena de Cao', '1302', '13'),
('130205', 'Paijan', '1302', '13'),
('130206', 'Rázuri', '1302', '13'),
('130207', 'Santiago de Cao', '1302', '13'),
('130208', 'Casa Grande', '1302', '13'),
('130301', 'Bolívar', '1303', '13'),
('130302', 'Bambamarca', '1303', '13'),
('130303', 'Condormarca', '1303', '13'),
('130304', 'Longotea', '1303', '13'),
('130305', 'Uchumarca', '1303', '13'),
('130306', 'Ucuncha', '1303', '13'),
('130401', 'Chepen', '1304', '13'),
('130402', 'Pacanga', '1304', '13'),
('130403', 'Pueblo Nuevo', '1304', '13'),
('130501', 'Julcan', '1305', '13'),
('130502', 'Calamarca', '1305', '13'),
('130503', 'Carabamba', '1305', '13'),
('130504', 'Huaso', '1305', '13'),
('130601', 'Otuzco', '1306', '13'),
('130602', 'Agallpampa', '1306', '13'),
('130604', 'Charat', '1306', '13'),
('130605', 'Huaranchal', '1306', '13'),
('130606', 'La Cuesta', '1306', '13'),
('130608', 'Mache', '1306', '13'),
('130610', 'Paranday', '1306', '13'),
('130611', 'Salpo', '1306', '13'),
('130613', 'Sinsicap', '1306', '13'),
('130614', 'Usquil', '1306', '13'),
('130701', 'San Pedro de Lloc', '1307', '13'),
('130702', 'Guadalupe', '1307', '13'),
('130703', 'Jequetepeque', '1307', '13'),
('130704', 'Pacasmayo', '1307', '13'),
('130705', 'San José', '1307', '13'),
('130801', 'Tayabamba', '1308', '13'),
('130802', 'Buldibuyo', '1308', '13'),
('130803', 'Chillia', '1308', '13'),
('130804', 'Huancaspata', '1308', '13'),
('130805', 'Huaylillas', '1308', '13'),
('130806', 'Huayo', '1308', '13'),
('130807', 'Ongon', '1308', '13'),
('130808', 'Parcoy', '1308', '13'),
('130809', 'Pataz', '1308', '13'),
('130810', 'Pias', '1308', '13'),
('130811', 'Santiago de Challas', '1308', '13'),
('130812', 'Taurija', '1308', '13'),
('130813', 'Urpay', '1308', '13'),
('130901', 'Huamachuco', '1309', '13'),
('130902', 'Chugay', '1309', '13'),
('130903', 'Cochorco', '1309', '13'),
('130904', 'Curgos', '1309', '13'),
('130905', 'Marcabal', '1309', '13'),
('130906', 'Sanagoran', '1309', '13'),
('130907', 'Sarin', '1309', '13'),
('130908', 'Sartimbamba', '1309', '13'),
('131001', 'Santiago de Chuco', '1310', '13'),
('131002', 'Angasmarca', '1310', '13'),
('131003', 'Cachicadan', '1310', '13'),
('131004', 'Mollebamba', '1310', '13'),
('131005', 'Mollepata', '1310', '13'),
('131006', 'Quiruvilca', '1310', '13'),
('131007', 'Santa Cruz de Chuca', '1310', '13'),
('131008', 'Sitabamba', '1310', '13'),
('131101', 'Cascas', '1311', '13'),
('131102', 'Lucma', '1311', '13'),
('131103', 'Marmot', '1311', '13'),
('131104', 'Sayapullo', '1311', '13'),
('131201', 'Viru', '1312', '13'),
('131202', 'Chao', '1312', '13'),
('131203', 'Guadalupito', '1312', '13'),
('140101', 'Chiclayo', '1401', '14'),
('140102', 'Chongoyape', '1401', '14'),
('140103', 'Eten', '1401', '14'),
('140104', 'Eten Puerto', '1401', '14'),
('140105', 'José Leonardo Ortiz', '1401', '14'),
('140106', 'La Victoria', '1401', '14'),
('140107', 'Lagunas', '1401', '14'),
('140108', 'Monsefu', '1401', '14'),
('140109', 'Nueva Arica', '1401', '14'),
('140110', 'Oyotun', '1401', '14'),
('140111', 'Picsi', '1401', '14'),
('140112', 'Pimentel', '1401', '14'),
('140113', 'Reque', '1401', '14'),
('140114', 'Santa Rosa', '1401', '14'),
('140115', 'Saña', '1401', '14'),
('140116', 'Cayalti', '1401', '14'),
('140117', 'Patapo', '1401', '14'),
('140118', 'Pomalca', '1401', '14'),
('140119', 'Pucala', '1401', '14'),
('140120', 'Tuman', '1401', '14'),
('140201', 'Ferreñafe', '1402', '14'),
('140202', 'Cañaris', '1402', '14'),
('140203', 'Incahuasi', '1402', '14'),
('140204', 'Manuel Antonio Mesones Muro', '1402', '14'),
('140205', 'Pitipo', '1402', '14'),
('140206', 'Pueblo Nuevo', '1402', '14'),
('140301', 'Lambayeque', '1403', '14'),
('140302', 'Chochope', '1403', '14'),
('140303', 'Illimo', '1403', '14'),
('140304', 'Jayanca', '1403', '14'),
('140305', 'Mochumi', '1403', '14'),
('140306', 'Morrope', '1403', '14'),
('140307', 'Motupe', '1403', '14'),
('140308', 'Olmos', '1403', '14'),
('140309', 'Pacora', '1403', '14'),
('140310', 'Salas', '1403', '14'),
('140311', 'San José', '1403', '14'),
('140312', 'Tucume', '1403', '14'),
('150101', 'Lima', '1501', '15'),
('150102', 'Ancón', '1501', '15'),
('150103', 'Ate', '1501', '15'),
('150104', 'Barranco', '1501', '15'),
('150105', 'Breña', '1501', '15'),
('150106', 'Carabayllo', '1501', '15'),
('150107', 'Chaclacayo', '1501', '15'),
('150108', 'Chorrillos', '1501', '15'),
('150109', 'Cieneguilla', '1501', '15'),
('150110', 'Comas', '1501', '15'),
('150111', 'El Agustino', '1501', '15'),
('150112', 'Independencia', '1501', '15'),
('150113', 'Jesús María', '1501', '15'),
('150114', 'La Molina', '1501', '15'),
('150115', 'La Victoria', '1501', '15'),
('150116', 'Lince', '1501', '15'),
('150117', 'Los Olivos', '1501', '15'),
('150118', 'Lurigancho', '1501', '15'),
('150119', 'Lurin', '1501', '15'),
('150120', 'Magdalena del Mar', '1501', '15'),
('150121', 'Pueblo Libre', '1501', '15'),
('150122', 'Miraflores', '1501', '15'),
('150123', 'Pachacamac', '1501', '15'),
('150124', 'Pucusana', '1501', '15'),
('150125', 'Puente Piedra', '1501', '15'),
('150126', 'Punta Hermosa', '1501', '15'),
('150127', 'Punta Negra', '1501', '15'),
('150128', 'Rímac', '1501', '15'),
('150129', 'San Bartolo', '1501', '15'),
('150130', 'San Borja', '1501', '15'),
('150131', 'San Isidro', '1501', '15'),
('150132', 'San Juan de Lurigancho', '1501', '15'),
('150133', 'San Juan de Miraflores', '1501', '15'),
('150134', 'San Luis', '1501', '15'),
('150135', 'San Martín de Porres', '1501', '15'),
('150136', 'San Miguel', '1501', '15'),
('150137', 'Santa Anita', '1501', '15'),
('150138', 'Santa María del Mar', '1501', '15'),
('150139', 'Santa Rosa', '1501', '15'),
('150140', 'Santiago de Surco', '1501', '15'),
('150141', 'Surquillo', '1501', '15'),
('150142', 'Villa El Salvador', '1501', '15'),
('150143', 'Villa María del Triunfo', '1501', '15'),
('150201', 'Barranca', '1502', '15'),
('150202', 'Paramonga', '1502', '15'),
('150203', 'Pativilca', '1502', '15'),
('150204', 'Supe', '1502', '15'),
('150205', 'Supe Puerto', '1502', '15'),
('150301', 'Cajatambo', '1503', '15'),
('150302', 'Copa', '1503', '15'),
('150303', 'Gorgor', '1503', '15'),
('150304', 'Huancapon', '1503', '15'),
('150305', 'Manas', '1503', '15'),
('150401', 'Canta', '1504', '15'),
('150402', 'Arahuay', '1504', '15'),
('150403', 'Huamantanga', '1504', '15'),
('150404', 'Huaros', '1504', '15'),
('150405', 'Lachaqui', '1504', '15'),
('150406', 'San Buenaventura', '1504', '15'),
('150407', 'Santa Rosa de Quives', '1504', '15');
INSERT INTO `ubigeo_peru_districts` (`id`, `name`, `province_id`, `department_id`) VALUES
('150501', 'San Vicente de Cañete', '1505', '15'),
('150502', 'Asia', '1505', '15'),
('150503', 'Calango', '1505', '15'),
('150504', 'Cerro Azul', '1505', '15'),
('150505', 'Chilca', '1505', '15'),
('150506', 'Coayllo', '1505', '15'),
('150507', 'Imperial', '1505', '15'),
('150508', 'Lunahuana', '1505', '15'),
('150509', 'Mala', '1505', '15'),
('150510', 'Nuevo Imperial', '1505', '15'),
('150511', 'Pacaran', '1505', '15'),
('150512', 'Quilmana', '1505', '15'),
('150513', 'San Antonio', '1505', '15'),
('150514', 'San Luis', '1505', '15'),
('150515', 'Santa Cruz de Flores', '1505', '15'),
('150516', 'Zúñiga', '1505', '15'),
('150601', 'Huaral', '1506', '15'),
('150602', 'Atavillos Alto', '1506', '15'),
('150603', 'Atavillos Bajo', '1506', '15'),
('150604', 'Aucallama', '1506', '15'),
('150605', 'Chancay', '1506', '15'),
('150606', 'Ihuari', '1506', '15'),
('150607', 'Lampian', '1506', '15'),
('150608', 'Pacaraos', '1506', '15'),
('150609', 'San Miguel de Acos', '1506', '15'),
('150610', 'Santa Cruz de Andamarca', '1506', '15'),
('150611', 'Sumbilca', '1506', '15'),
('150612', 'Veintisiete de Noviembre', '1506', '15'),
('150701', 'Matucana', '1507', '15'),
('150702', 'Antioquia', '1507', '15'),
('150703', 'Callahuanca', '1507', '15'),
('150704', 'Carampoma', '1507', '15'),
('150705', 'Chicla', '1507', '15'),
('150706', 'Cuenca', '1507', '15'),
('150707', 'Huachupampa', '1507', '15'),
('150708', 'Huanza', '1507', '15'),
('150709', 'Huarochiri', '1507', '15'),
('150710', 'Lahuaytambo', '1507', '15'),
('150711', 'Langa', '1507', '15'),
('150712', 'Laraos', '1507', '15'),
('150713', 'Mariatana', '1507', '15'),
('150714', 'Ricardo Palma', '1507', '15'),
('150715', 'San Andrés de Tupicocha', '1507', '15'),
('150716', 'San Antonio', '1507', '15'),
('150717', 'San Bartolomé', '1507', '15'),
('150718', 'San Damian', '1507', '15'),
('150719', 'San Juan de Iris', '1507', '15'),
('150720', 'San Juan de Tantaranche', '1507', '15'),
('150721', 'San Lorenzo de Quinti', '1507', '15'),
('150722', 'San Mateo', '1507', '15'),
('150723', 'San Mateo de Otao', '1507', '15'),
('150724', 'San Pedro de Casta', '1507', '15'),
('150725', 'San Pedro de Huancayre', '1507', '15'),
('150726', 'Sangallaya', '1507', '15'),
('150727', 'Santa Cruz de Cocachacra', '1507', '15'),
('150728', 'Santa Eulalia', '1507', '15'),
('150729', 'Santiago de Anchucaya', '1507', '15'),
('150730', 'Santiago de Tuna', '1507', '15'),
('150731', 'Santo Domingo de Los Olleros', '1507', '15'),
('150732', 'Surco', '1507', '15'),
('150801', 'Huacho', '1508', '15'),
('150802', 'Ambar', '1508', '15'),
('150803', 'Caleta de Carquin', '1508', '15'),
('150804', 'Checras', '1508', '15'),
('150805', 'Hualmay', '1508', '15'),
('150806', 'Huaura', '1508', '15'),
('150807', 'Leoncio Prado', '1508', '15'),
('150808', 'Paccho', '1508', '15'),
('150809', 'Santa Leonor', '1508', '15'),
('150810', 'Santa María', '1508', '15'),
('150811', 'Sayan', '1508', '15'),
('150812', 'Vegueta', '1508', '15'),
('150901', 'Oyon', '1509', '15'),
('150902', 'Andajes', '1509', '15'),
('150903', 'Caujul', '1509', '15'),
('150904', 'Cochamarca', '1509', '15'),
('150905', 'Navan', '1509', '15'),
('150906', 'Pachangara', '1509', '15'),
('151001', 'Yauyos', '1510', '15'),
('151002', 'Alis', '1510', '15'),
('151003', 'Allauca', '1510', '15'),
('151004', 'Ayaviri', '1510', '15'),
('151005', 'Azángaro', '1510', '15'),
('151006', 'Cacra', '1510', '15'),
('151007', 'Carania', '1510', '15'),
('151008', 'Catahuasi', '1510', '15'),
('151009', 'Chocos', '1510', '15'),
('151010', 'Cochas', '1510', '15'),
('151011', 'Colonia', '1510', '15'),
('151012', 'Hongos', '1510', '15'),
('151013', 'Huampara', '1510', '15'),
('151014', 'Huancaya', '1510', '15'),
('151015', 'Huangascar', '1510', '15'),
('151016', 'Huantan', '1510', '15'),
('151017', 'Huañec', '1510', '15'),
('151018', 'Laraos', '1510', '15'),
('151019', 'Lincha', '1510', '15'),
('151020', 'Madean', '1510', '15'),
('151021', 'Miraflores', '1510', '15'),
('151022', 'Omas', '1510', '15'),
('151023', 'Putinza', '1510', '15'),
('151024', 'Quinches', '1510', '15'),
('151025', 'Quinocay', '1510', '15'),
('151026', 'San Joaquín', '1510', '15'),
('151027', 'San Pedro de Pilas', '1510', '15'),
('151028', 'Tanta', '1510', '15'),
('151029', 'Tauripampa', '1510', '15'),
('151030', 'Tomas', '1510', '15'),
('151031', 'Tupe', '1510', '15'),
('151032', 'Viñac', '1510', '15'),
('151033', 'Vitis', '1510', '15'),
('160101', 'Iquitos', '1601', '16'),
('160102', 'Alto Nanay', '1601', '16'),
('160103', 'Fernando Lores', '1601', '16'),
('160104', 'Indiana', '1601', '16'),
('160105', 'Las Amazonas', '1601', '16'),
('160106', 'Mazan', '1601', '16'),
('160107', 'Napo', '1601', '16'),
('160108', 'Punchana', '1601', '16'),
('160110', 'Torres Causana', '1601', '16'),
('160112', 'Belén', '1601', '16'),
('160113', 'San Juan Bautista', '1601', '16'),
('160201', 'Yurimaguas', '1602', '16'),
('160202', 'Balsapuerto', '1602', '16'),
('160205', 'Jeberos', '1602', '16'),
('160206', 'Lagunas', '1602', '16'),
('160210', 'Santa Cruz', '1602', '16'),
('160211', 'Teniente Cesar López Rojas', '1602', '16'),
('160301', 'Nauta', '1603', '16'),
('160302', 'Parinari', '1603', '16'),
('160303', 'Tigre', '1603', '16'),
('160304', 'Trompeteros', '1603', '16'),
('160305', 'Urarinas', '1603', '16'),
('160401', 'Ramón Castilla', '1604', '16'),
('160402', 'Pebas', '1604', '16'),
('160403', 'Yavari', '1604', '16'),
('160404', 'San Pablo', '1604', '16'),
('160501', 'Requena', '1605', '16'),
('160502', 'Alto Tapiche', '1605', '16'),
('160503', 'Capelo', '1605', '16'),
('160504', 'Emilio San Martín', '1605', '16'),
('160505', 'Maquia', '1605', '16'),
('160506', 'Puinahua', '1605', '16'),
('160507', 'Saquena', '1605', '16'),
('160508', 'Soplin', '1605', '16'),
('160509', 'Tapiche', '1605', '16'),
('160510', 'Jenaro Herrera', '1605', '16'),
('160511', 'Yaquerana', '1605', '16'),
('160601', 'Contamana', '1606', '16'),
('160602', 'Inahuaya', '1606', '16'),
('160603', 'Padre Márquez', '1606', '16'),
('160604', 'Pampa Hermosa', '1606', '16'),
('160605', 'Sarayacu', '1606', '16'),
('160606', 'Vargas Guerra', '1606', '16'),
('160701', 'Barranca', '1607', '16'),
('160702', 'Cahuapanas', '1607', '16'),
('160703', 'Manseriche', '1607', '16'),
('160704', 'Morona', '1607', '16'),
('160705', 'Pastaza', '1607', '16'),
('160706', 'Andoas', '1607', '16'),
('160801', 'Putumayo', '1608', '16'),
('160802', 'Rosa Panduro', '1608', '16'),
('160803', 'Teniente Manuel Clavero', '1608', '16'),
('160804', 'Yaguas', '1608', '16'),
('170101', 'Tambopata', '1701', '17'),
('170102', 'Inambari', '1701', '17'),
('170103', 'Las Piedras', '1701', '17'),
('170104', 'Laberinto', '1701', '17'),
('170201', 'Manu', '1702', '17'),
('170202', 'Fitzcarrald', '1702', '17'),
('170203', 'Madre de Dios', '1702', '17'),
('170204', 'Huepetuhe', '1702', '17'),
('170301', 'Iñapari', '1703', '17'),
('170302', 'Iberia', '1703', '17'),
('170303', 'Tahuamanu', '1703', '17'),
('180101', 'Moquegua', '1801', '18'),
('180102', 'Carumas', '1801', '18'),
('180103', 'Cuchumbaya', '1801', '18'),
('180104', 'Samegua', '1801', '18'),
('180105', 'San Cristóbal', '1801', '18'),
('180106', 'Torata', '1801', '18'),
('180201', 'Omate', '1802', '18'),
('180202', 'Chojata', '1802', '18'),
('180203', 'Coalaque', '1802', '18'),
('180204', 'Ichuña', '1802', '18'),
('180205', 'La Capilla', '1802', '18'),
('180206', 'Lloque', '1802', '18'),
('180207', 'Matalaque', '1802', '18'),
('180208', 'Puquina', '1802', '18'),
('180209', 'Quinistaquillas', '1802', '18'),
('180210', 'Ubinas', '1802', '18'),
('180211', 'Yunga', '1802', '18'),
('180301', 'Ilo', '1803', '18'),
('180302', 'El Algarrobal', '1803', '18'),
('180303', 'Pacocha', '1803', '18'),
('190101', 'Chaupimarca', '1901', '19'),
('190102', 'Huachon', '1901', '19'),
('190103', 'Huariaca', '1901', '19'),
('190104', 'Huayllay', '1901', '19'),
('190105', 'Ninacaca', '1901', '19'),
('190106', 'Pallanchacra', '1901', '19'),
('190107', 'Paucartambo', '1901', '19'),
('190108', 'San Francisco de Asís de Yarusyacan', '1901', '19'),
('190109', 'Simon Bolívar', '1901', '19'),
('190110', 'Ticlacayan', '1901', '19'),
('190111', 'Tinyahuarco', '1901', '19'),
('190112', 'Vicco', '1901', '19'),
('190113', 'Yanacancha', '1901', '19'),
('190201', 'Yanahuanca', '1902', '19'),
('190202', 'Chacayan', '1902', '19'),
('190203', 'Goyllarisquizga', '1902', '19'),
('190204', 'Paucar', '1902', '19'),
('190205', 'San Pedro de Pillao', '1902', '19'),
('190206', 'Santa Ana de Tusi', '1902', '19'),
('190207', 'Tapuc', '1902', '19'),
('190208', 'Vilcabamba', '1902', '19'),
('190301', 'Oxapampa', '1903', '19'),
('190302', 'Chontabamba', '1903', '19'),
('190303', 'Huancabamba', '1903', '19'),
('190304', 'Palcazu', '1903', '19'),
('190305', 'Pozuzo', '1903', '19'),
('190306', 'Puerto Bermúdez', '1903', '19'),
('190307', 'Villa Rica', '1903', '19'),
('190308', 'Constitución', '1903', '19'),
('200101', 'Piura', '2001', '20'),
('200104', 'Castilla', '2001', '20'),
('200105', 'Catacaos', '2001', '20'),
('200107', 'Cura Mori', '2001', '20'),
('200108', 'El Tallan', '2001', '20'),
('200109', 'La Arena', '2001', '20'),
('200110', 'La Unión', '2001', '20'),
('200111', 'Las Lomas', '2001', '20'),
('200114', 'Tambo Grande', '2001', '20'),
('200115', 'Veintiseis de Octubre', '2001', '20'),
('200201', 'Ayabaca', '2002', '20'),
('200202', 'Frias', '2002', '20'),
('200203', 'Jilili', '2002', '20'),
('200204', 'Lagunas', '2002', '20'),
('200205', 'Montero', '2002', '20'),
('200206', 'Pacaipampa', '2002', '20'),
('200207', 'Paimas', '2002', '20'),
('200208', 'Sapillica', '2002', '20'),
('200209', 'Sicchez', '2002', '20'),
('200210', 'Suyo', '2002', '20'),
('200301', 'Huancabamba', '2003', '20'),
('200302', 'Canchaque', '2003', '20'),
('200303', 'El Carmen de la Frontera', '2003', '20'),
('200304', 'Huarmaca', '2003', '20'),
('200305', 'Lalaquiz', '2003', '20'),
('200306', 'San Miguel de El Faique', '2003', '20'),
('200307', 'Sondor', '2003', '20'),
('200308', 'Sondorillo', '2003', '20'),
('200401', 'Chulucanas', '2004', '20'),
('200402', 'Buenos Aires', '2004', '20'),
('200403', 'Chalaco', '2004', '20'),
('200404', 'La Matanza', '2004', '20'),
('200405', 'Morropon', '2004', '20'),
('200406', 'Salitral', '2004', '20'),
('200407', 'San Juan de Bigote', '2004', '20'),
('200408', 'Santa Catalina de Mossa', '2004', '20'),
('200409', 'Santo Domingo', '2004', '20'),
('200410', 'Yamango', '2004', '20'),
('200501', 'Paita', '2005', '20'),
('200502', 'Amotape', '2005', '20'),
('200503', 'Arenal', '2005', '20'),
('200504', 'Colan', '2005', '20'),
('200505', 'La Huaca', '2005', '20'),
('200506', 'Tamarindo', '2005', '20'),
('200507', 'Vichayal', '2005', '20'),
('200601', 'Sullana', '2006', '20'),
('200602', 'Bellavista', '2006', '20'),
('200603', 'Ignacio Escudero', '2006', '20'),
('200604', 'Lancones', '2006', '20'),
('200605', 'Marcavelica', '2006', '20'),
('200606', 'Miguel Checa', '2006', '20'),
('200607', 'Querecotillo', '2006', '20'),
('200608', 'Salitral', '2006', '20'),
('200701', 'Pariñas', '2007', '20'),
('200702', 'El Alto', '2007', '20'),
('200703', 'La Brea', '2007', '20'),
('200704', 'Lobitos', '2007', '20'),
('200705', 'Los Organos', '2007', '20'),
('200706', 'Mancora', '2007', '20'),
('200801', 'Sechura', '2008', '20'),
('200802', 'Bellavista de la Unión', '2008', '20'),
('200803', 'Bernal', '2008', '20'),
('200804', 'Cristo Nos Valga', '2008', '20'),
('200805', 'Vice', '2008', '20'),
('200806', 'Rinconada Llicuar', '2008', '20'),
('210101', 'Puno', '2101', '21'),
('210102', 'Acora', '2101', '21'),
('210103', 'Amantani', '2101', '21'),
('210104', 'Atuncolla', '2101', '21'),
('210105', 'Capachica', '2101', '21'),
('210106', 'Chucuito', '2101', '21'),
('210107', 'Coata', '2101', '21'),
('210108', 'Huata', '2101', '21'),
('210109', 'Mañazo', '2101', '21'),
('210110', 'Paucarcolla', '2101', '21'),
('210111', 'Pichacani', '2101', '21'),
('210112', 'Plateria', '2101', '21'),
('210113', 'San Antonio', '2101', '21'),
('210114', 'Tiquillaca', '2101', '21'),
('210115', 'Vilque', '2101', '21'),
('210201', 'Azángaro', '2102', '21'),
('210202', 'Achaya', '2102', '21'),
('210203', 'Arapa', '2102', '21'),
('210204', 'Asillo', '2102', '21'),
('210205', 'Caminaca', '2102', '21'),
('210206', 'Chupa', '2102', '21'),
('210207', 'José Domingo Choquehuanca', '2102', '21'),
('210208', 'Muñani', '2102', '21'),
('210209', 'Potoni', '2102', '21'),
('210210', 'Saman', '2102', '21'),
('210211', 'San Anton', '2102', '21'),
('210212', 'San José', '2102', '21'),
('210213', 'San Juan de Salinas', '2102', '21'),
('210214', 'Santiago de Pupuja', '2102', '21'),
('210215', 'Tirapata', '2102', '21'),
('210301', 'Macusani', '2103', '21'),
('210302', 'Ajoyani', '2103', '21'),
('210303', 'Ayapata', '2103', '21'),
('210304', 'Coasa', '2103', '21'),
('210305', 'Corani', '2103', '21'),
('210306', 'Crucero', '2103', '21'),
('210307', 'Ituata', '2103', '21'),
('210308', 'Ollachea', '2103', '21'),
('210309', 'San Gaban', '2103', '21'),
('210310', 'Usicayos', '2103', '21'),
('210401', 'Juli', '2104', '21'),
('210402', 'Desaguadero', '2104', '21'),
('210403', 'Huacullani', '2104', '21'),
('210404', 'Kelluyo', '2104', '21'),
('210405', 'Pisacoma', '2104', '21'),
('210406', 'Pomata', '2104', '21'),
('210407', 'Zepita', '2104', '21'),
('210501', 'Ilave', '2105', '21'),
('210502', 'Capazo', '2105', '21'),
('210503', 'Pilcuyo', '2105', '21'),
('210504', 'Santa Rosa', '2105', '21'),
('210505', 'Conduriri', '2105', '21'),
('210601', 'Huancane', '2106', '21'),
('210602', 'Cojata', '2106', '21'),
('210603', 'Huatasani', '2106', '21'),
('210604', 'Inchupalla', '2106', '21'),
('210605', 'Pusi', '2106', '21'),
('210606', 'Rosaspata', '2106', '21'),
('210607', 'Taraco', '2106', '21'),
('210608', 'Vilque Chico', '2106', '21'),
('210701', 'Lampa', '2107', '21'),
('210702', 'Cabanilla', '2107', '21'),
('210703', 'Calapuja', '2107', '21'),
('210704', 'Nicasio', '2107', '21'),
('210705', 'Ocuviri', '2107', '21'),
('210706', 'Palca', '2107', '21'),
('210707', 'Paratia', '2107', '21'),
('210708', 'Pucara', '2107', '21'),
('210709', 'Santa Lucia', '2107', '21'),
('210710', 'Vilavila', '2107', '21'),
('210801', 'Ayaviri', '2108', '21'),
('210802', 'Antauta', '2108', '21'),
('210803', 'Cupi', '2108', '21'),
('210804', 'Llalli', '2108', '21'),
('210805', 'Macari', '2108', '21'),
('210806', 'Nuñoa', '2108', '21'),
('210807', 'Orurillo', '2108', '21'),
('210808', 'Santa Rosa', '2108', '21'),
('210809', 'Umachiri', '2108', '21'),
('210901', 'Moho', '2109', '21'),
('210902', 'Conima', '2109', '21'),
('210903', 'Huayrapata', '2109', '21'),
('210904', 'Tilali', '2109', '21'),
('211001', 'Putina', '2110', '21'),
('211002', 'Ananea', '2110', '21'),
('211003', 'Pedro Vilca Apaza', '2110', '21'),
('211004', 'Quilcapuncu', '2110', '21'),
('211005', 'Sina', '2110', '21'),
('211101', 'Juliaca', '2111', '21'),
('211102', 'Cabana', '2111', '21'),
('211103', 'Cabanillas', '2111', '21'),
('211104', 'Caracoto', '2111', '21'),
('211105', 'San Miguel', '2111', '21'),
('211201', 'Sandia', '2112', '21'),
('211202', 'Cuyocuyo', '2112', '21'),
('211203', 'Limbani', '2112', '21'),
('211204', 'Patambuco', '2112', '21'),
('211205', 'Phara', '2112', '21'),
('211206', 'Quiaca', '2112', '21'),
('211207', 'San Juan del Oro', '2112', '21'),
('211208', 'Yanahuaya', '2112', '21'),
('211209', 'Alto Inambari', '2112', '21'),
('211210', 'San Pedro de Putina Punco', '2112', '21'),
('211301', 'Yunguyo', '2113', '21'),
('211302', 'Anapia', '2113', '21'),
('211303', 'Copani', '2113', '21'),
('211304', 'Cuturapi', '2113', '21'),
('211305', 'Ollaraya', '2113', '21'),
('211306', 'Tinicachi', '2113', '21'),
('211307', 'Unicachi', '2113', '21'),
('220101', 'Moyobamba', '2201', '22'),
('220102', 'Calzada', '2201', '22'),
('220103', 'Habana', '2201', '22'),
('220104', 'Jepelacio', '2201', '22'),
('220105', 'Soritor', '2201', '22'),
('220106', 'Yantalo', '2201', '22'),
('220201', 'Bellavista', '2202', '22'),
('220202', 'Alto Biavo', '2202', '22'),
('220203', 'Bajo Biavo', '2202', '22'),
('220204', 'Huallaga', '2202', '22'),
('220205', 'San Pablo', '2202', '22'),
('220206', 'San Rafael', '2202', '22'),
('220301', 'San José de Sisa', '2203', '22'),
('220302', 'Agua Blanca', '2203', '22'),
('220303', 'San Martín', '2203', '22'),
('220304', 'Santa Rosa', '2203', '22'),
('220305', 'Shatoja', '2203', '22'),
('220401', 'Saposoa', '2204', '22'),
('220402', 'Alto Saposoa', '2204', '22'),
('220403', 'El Eslabón', '2204', '22'),
('220404', 'Piscoyacu', '2204', '22'),
('220405', 'Sacanche', '2204', '22'),
('220406', 'Tingo de Saposoa', '2204', '22'),
('220501', 'Lamas', '2205', '22'),
('220502', 'Alonso de Alvarado', '2205', '22'),
('220503', 'Barranquita', '2205', '22'),
('220504', 'Caynarachi', '2205', '22'),
('220505', 'Cuñumbuqui', '2205', '22'),
('220506', 'Pinto Recodo', '2205', '22'),
('220507', 'Rumisapa', '2205', '22'),
('220508', 'San Roque de Cumbaza', '2205', '22'),
('220509', 'Shanao', '2205', '22'),
('220510', 'Tabalosos', '2205', '22'),
('220511', 'Zapatero', '2205', '22'),
('220601', 'Juanjuí', '2206', '22'),
('220602', 'Campanilla', '2206', '22'),
('220603', 'Huicungo', '2206', '22'),
('220604', 'Pachiza', '2206', '22'),
('220605', 'Pajarillo', '2206', '22'),
('220701', 'Picota', '2207', '22'),
('220702', 'Buenos Aires', '2207', '22'),
('220703', 'Caspisapa', '2207', '22'),
('220704', 'Pilluana', '2207', '22'),
('220705', 'Pucacaca', '2207', '22'),
('220706', 'San Cristóbal', '2207', '22'),
('220707', 'San Hilarión', '2207', '22'),
('220708', 'Shamboyacu', '2207', '22'),
('220709', 'Tingo de Ponasa', '2207', '22'),
('220710', 'Tres Unidos', '2207', '22'),
('220801', 'Rioja', '2208', '22'),
('220802', 'Awajun', '2208', '22'),
('220803', 'Elías Soplin Vargas', '2208', '22'),
('220804', 'Nueva Cajamarca', '2208', '22'),
('220805', 'Pardo Miguel', '2208', '22'),
('220806', 'Posic', '2208', '22'),
('220807', 'San Fernando', '2208', '22'),
('220808', 'Yorongos', '2208', '22'),
('220809', 'Yuracyacu', '2208', '22'),
('220901', 'Tarapoto', '2209', '22'),
('220902', 'Alberto Leveau', '2209', '22'),
('220903', 'Cacatachi', '2209', '22'),
('220904', 'Chazuta', '2209', '22'),
('220905', 'Chipurana', '2209', '22'),
('220906', 'El Porvenir', '2209', '22'),
('220907', 'Huimbayoc', '2209', '22'),
('220908', 'Juan Guerra', '2209', '22'),
('220909', 'La Banda de Shilcayo', '2209', '22'),
('220910', 'Morales', '2209', '22'),
('220911', 'Papaplaya', '2209', '22'),
('220912', 'San Antonio', '2209', '22'),
('220913', 'Sauce', '2209', '22'),
('220914', 'Shapaja', '2209', '22'),
('221001', 'Tocache', '2210', '22'),
('221002', 'Nuevo Progreso', '2210', '22'),
('221003', 'Polvora', '2210', '22'),
('221004', 'Shunte', '2210', '22'),
('221005', 'Uchiza', '2210', '22'),
('230101', 'Tacna', '2301', '23'),
('230102', 'Alto de la Alianza', '2301', '23'),
('230103', 'Calana', '2301', '23'),
('230104', 'Ciudad Nueva', '2301', '23'),
('230105', 'Inclan', '2301', '23'),
('230106', 'Pachia', '2301', '23'),
('230107', 'Palca', '2301', '23'),
('230108', 'Pocollay', '2301', '23'),
('230109', 'Sama', '2301', '23'),
('230110', 'Coronel Gregorio Albarracín Lanchipa', '2301', '23'),
('230111', 'La Yarada los Palos', '2301', '23'),
('230201', 'Candarave', '2302', '23'),
('230202', 'Cairani', '2302', '23'),
('230203', 'Camilaca', '2302', '23'),
('230204', 'Curibaya', '2302', '23'),
('230205', 'Huanuara', '2302', '23'),
('230206', 'Quilahuani', '2302', '23'),
('230301', 'Locumba', '2303', '23'),
('230302', 'Ilabaya', '2303', '23'),
('230303', 'Ite', '2303', '23'),
('230401', 'Tarata', '2304', '23'),
('230402', 'Héroes Albarracín', '2304', '23'),
('230403', 'Estique', '2304', '23'),
('230404', 'Estique-Pampa', '2304', '23'),
('230405', 'Sitajara', '2304', '23'),
('230406', 'Susapaya', '2304', '23'),
('230407', 'Tarucachi', '2304', '23'),
('230408', 'Ticaco', '2304', '23'),
('240101', 'Tumbes', '2401', '24'),
('240102', 'Corrales', '2401', '24'),
('240103', 'La Cruz', '2401', '24'),
('240104', 'Pampas de Hospital', '2401', '24'),
('240105', 'San Jacinto', '2401', '24'),
('240106', 'San Juan de la Virgen', '2401', '24'),
('240201', 'Zorritos', '2402', '24'),
('240202', 'Casitas', '2402', '24'),
('240203', 'Canoas de Punta Sal', '2402', '24'),
('240301', 'Zarumilla', '2403', '24'),
('240302', 'Aguas Verdes', '2403', '24'),
('240303', 'Matapalo', '2403', '24'),
('240304', 'Papayal', '2403', '24'),
('250101', 'Calleria', '2501', '25'),
('250102', 'Campoverde', '2501', '25'),
('250103', 'Iparia', '2501', '25'),
('250104', 'Masisea', '2501', '25'),
('250105', 'Yarinacocha', '2501', '25'),
('250106', 'Nueva Requena', '2501', '25'),
('250107', 'Manantay', '2501', '25'),
('250201', 'Raymondi', '2502', '25'),
('250202', 'Sepahua', '2502', '25'),
('250203', 'Tahuania', '2502', '25'),
('250204', 'Yurua', '2502', '25'),
('250301', 'Padre Abad', '2503', '25'),
('250302', 'Irazola', '2503', '25'),
('250303', 'Curimana', '2503', '25'),
('250304', 'Neshuya', '2503', '25'),
('250305', 'Alexander Von Humboldt', '2503', '25'),
('250401', 'Purus', '2504', '25');

-- --------------------------------------------------------

--
-- Table structure for table `ubigeo_peru_provinces`
--

CREATE TABLE `ubigeo_peru_provinces` (
  `id` varchar(4) NOT NULL,
  `name` varchar(45) NOT NULL,
  `department_id` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ubigeo_peru_provinces`
--

INSERT INTO `ubigeo_peru_provinces` (`id`, `name`, `department_id`) VALUES
('0101', 'Chachapoyas', '01'),
('0102', 'Bagua', '01'),
('0103', 'Bongará', '01'),
('0104', 'Condorcanqui', '01'),
('0105', 'Luya', '01'),
('0106', 'Rodríguez de Mendoza', '01'),
('0107', 'Utcubamba', '01'),
('0201', 'Huaraz', '02'),
('0202', 'Aija', '02'),
('0203', 'Antonio Raymondi', '02'),
('0204', 'Asunción', '02'),
('0205', 'Bolognesi', '02'),
('0206', 'Carhuaz', '02'),
('0207', 'Carlos Fermín Fitzcarrald', '02'),
('0208', 'Casma', '02'),
('0209', 'Corongo', '02'),
('0210', 'Huari', '02'),
('0211', 'Huarmey', '02'),
('0212', 'Huaylas', '02'),
('0213', 'Mariscal Luzuriaga', '02'),
('0214', 'Ocros', '02'),
('0215', 'Pallasca', '02'),
('0216', 'Pomabamba', '02'),
('0217', 'Recuay', '02'),
('0218', 'Santa', '02'),
('0219', 'Sihuas', '02'),
('0220', 'Yungay', '02'),
('0301', 'Abancay', '03'),
('0302', 'Andahuaylas', '03'),
('0303', 'Antabamba', '03'),
('0304', 'Aymaraes', '03'),
('0305', 'Cotabambas', '03'),
('0306', 'Chincheros', '03'),
('0307', 'Grau', '03'),
('0401', 'Arequipa', '04'),
('0402', 'Camaná', '04'),
('0403', 'Caravelí', '04'),
('0404', 'Castilla', '04'),
('0405', 'Caylloma', '04'),
('0406', 'Condesuyos', '04'),
('0407', 'Islay', '04'),
('0408', 'La Uniòn', '04'),
('0501', 'Huamanga', '05'),
('0502', 'Cangallo', '05'),
('0503', 'Huanca Sancos', '05'),
('0504', 'Huanta', '05'),
('0505', 'La Mar', '05'),
('0506', 'Lucanas', '05'),
('0507', 'Parinacochas', '05'),
('0508', 'Pàucar del Sara Sara', '05'),
('0509', 'Sucre', '05'),
('0510', 'Víctor Fajardo', '05'),
('0511', 'Vilcas Huamán', '05'),
('0601', 'Cajamarca', '06'),
('0602', 'Cajabamba', '06'),
('0603', 'Celendín', '06'),
('0604', 'Chota', '06'),
('0605', 'Contumazá', '06'),
('0606', 'Cutervo', '06'),
('0607', 'Hualgayoc', '06'),
('0608', 'Jaén', '06'),
('0609', 'San Ignacio', '06'),
('0610', 'San Marcos', '06'),
('0611', 'San Miguel', '06'),
('0612', 'San Pablo', '06'),
('0613', 'Santa Cruz', '06'),
('0701', 'Prov. Const. del Callao', '07'),
('0801', 'Cusco', '08'),
('0802', 'Acomayo', '08'),
('0803', 'Anta', '08'),
('0804', 'Calca', '08'),
('0805', 'Canas', '08'),
('0806', 'Canchis', '08'),
('0807', 'Chumbivilcas', '08'),
('0808', 'Espinar', '08'),
('0809', 'La Convención', '08'),
('0810', 'Paruro', '08'),
('0811', 'Paucartambo', '08'),
('0812', 'Quispicanchi', '08'),
('0813', 'Urubamba', '08'),
('0901', 'Huancavelica', '09'),
('0902', 'Acobamba', '09'),
('0903', 'Angaraes', '09'),
('0904', 'Castrovirreyna', '09'),
('0905', 'Churcampa', '09'),
('0906', 'Huaytará', '09'),
('0907', 'Tayacaja', '09'),
('1001', 'Huánuco', '10'),
('1002', 'Ambo', '10'),
('1003', 'Dos de Mayo', '10'),
('1004', 'Huacaybamba', '10'),
('1005', 'Huamalíes', '10'),
('1006', 'Leoncio Prado', '10'),
('1007', 'Marañón', '10'),
('1008', 'Pachitea', '10'),
('1009', 'Puerto Inca', '10'),
('1010', 'Lauricocha ', '10'),
('1011', 'Yarowilca ', '10'),
('1101', 'Ica ', '11'),
('1102', 'Chincha ', '11'),
('1103', 'Nasca ', '11'),
('1104', 'Palpa ', '11'),
('1105', 'Pisco ', '11'),
('1201', 'Huancayo ', '12'),
('1202', 'Concepción ', '12'),
('1203', 'Chanchamayo ', '12'),
('1204', 'Jauja ', '12'),
('1205', 'Junín ', '12'),
('1206', 'Satipo ', '12'),
('1207', 'Tarma ', '12'),
('1208', 'Yauli ', '12'),
('1209', 'Chupaca ', '12'),
('1301', 'Trujillo ', '13'),
('1302', 'Ascope ', '13'),
('1303', 'Bolívar ', '13'),
('1304', 'Chepén ', '13'),
('1305', 'Julcán ', '13'),
('1306', 'Otuzco ', '13'),
('1307', 'Pacasmayo ', '13'),
('1308', 'Pataz ', '13'),
('1309', 'Sánchez Carrión ', '13'),
('1310', 'Santiago de Chuco ', '13'),
('1311', 'Gran Chimú ', '13'),
('1312', 'Virú ', '13'),
('1401', 'Chiclayo ', '14'),
('1402', 'Ferreñafe ', '14'),
('1403', 'Lambayeque ', '14'),
('1501', 'Lima ', '15'),
('1502', 'Barranca ', '15'),
('1503', 'Cajatambo ', '15'),
('1504', 'Canta ', '15'),
('1505', 'Cañete ', '15'),
('1506', 'Huaral ', '15'),
('1507', 'Huarochirí ', '15'),
('1508', 'Huaura ', '15'),
('1509', 'Oyón ', '15'),
('1510', 'Yauyos ', '15'),
('1601', 'Maynas ', '16'),
('1602', 'Alto Amazonas ', '16'),
('1603', 'Loreto ', '16'),
('1604', 'Mariscal Ramón Castilla ', '16'),
('1605', 'Requena ', '16'),
('1606', 'Ucayali ', '16'),
('1607', 'Datem del Marañón ', '16'),
('1608', 'Putumayo', '16'),
('1701', 'Tambopata ', '17'),
('1702', 'Manu ', '17'),
('1703', 'Tahuamanu ', '17'),
('1801', 'Mariscal Nieto ', '18'),
('1802', 'General Sánchez Cerro ', '18'),
('1803', 'Ilo ', '18'),
('1901', 'Pasco ', '19'),
('1902', 'Daniel Alcides Carrión ', '19'),
('1903', 'Oxapampa ', '19'),
('2001', 'Piura ', '20'),
('2002', 'Ayabaca ', '20'),
('2003', 'Huancabamba ', '20'),
('2004', 'Morropón ', '20'),
('2005', 'Paita ', '20'),
('2006', 'Sullana ', '20'),
('2007', 'Talara ', '20'),
('2008', 'Sechura ', '20'),
('2101', 'Puno ', '21'),
('2102', 'Azángaro ', '21'),
('2103', 'Carabaya ', '21'),
('2104', 'Chucuito ', '21'),
('2105', 'El Collao ', '21'),
('2106', 'Huancané ', '21'),
('2107', 'Lampa ', '21'),
('2108', 'Melgar ', '21'),
('2109', 'Moho ', '21'),
('2110', 'San Antonio de Putina ', '21'),
('2111', 'San Román ', '21'),
('2112', 'Sandia ', '21'),
('2113', 'Yunguyo ', '21'),
('2201', 'Moyobamba ', '22'),
('2202', 'Bellavista ', '22'),
('2203', 'El Dorado ', '22'),
('2204', 'Huallaga ', '22'),
('2205', 'Lamas ', '22'),
('2206', 'Mariscal Cáceres ', '22'),
('2207', 'Picota ', '22'),
('2208', 'Rioja ', '22'),
('2209', 'San Martín ', '22'),
('2210', 'Tocache ', '22'),
('2301', 'Tacna ', '23'),
('2302', 'Candarave ', '23'),
('2303', 'Jorge Basadre ', '23'),
('2304', 'Tarata ', '23'),
('2401', 'Tumbes ', '24'),
('2402', 'Contralmirante Villar ', '24'),
('2403', 'Zarumilla ', '24'),
('2501', 'Coronel Portillo ', '25'),
('2502', 'Atalaya ', '25'),
('2503', 'Padre Abad ', '25'),
('2504', 'Purús', '25');

-- --------------------------------------------------------

--
-- Table structure for table `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `id_unidad_medida` int UNSIGNED NOT NULL,
  `unidad_medida` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_sunat` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `simbolo` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `unidades_medida`
--

INSERT INTO `unidades_medida` (`id_unidad_medida`, `unidad_medida`, `codigo_sunat`, `simbolo`, `estado`) VALUES
(1, 'Servicio', 'ZZ', 'SERV', 1),
(2, 'Unidad', 'NIU', 'UND', 1),
(3, 'Caja', 'BX', 'CAJA', 1),
(4, 'Galones', 'GLL', 'GAL', 1),
(5, 'Gramos', 'GRM', 'GR', 1),
(6, 'Kilos', 'KG', 'KG', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `id_role` bigint UNSIGNED NOT NULL DEFAULT '1',
  `id_sucursal` int UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `id_role`, `id_sucursal`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Superadmin', 'superadmin@gmail.com', NULL, '$2y$10$aQr48bi7hpbzz1l32dI8hucoX//pP87fAcGyuBUFBfEI.R3Jt7RqG', NULL, 1, '2023-12-16 17:31:51', '2023-12-16 17:31:51'),
(2, 2, 1, 'Administrador', 'admin@gmail.com', NULL, '$2y$10$0l0PCG.76eLWQFlSjdVbgu4NpvFpziJuxYtxLDLmz9DjqVBK3aapK', NULL, 1, '2023-12-16 17:31:52', '2023-12-16 17:31:52'),
(3, 5, 1, 'Cajero', 'cajero@gmail.com', NULL, '$2y$10$PLtkIQz1ZGOp.9x4oKu0Get8N/gD.xuCJnEE0s3wziVtcuta4I/16', NULL, 1, '2023-12-16 17:31:52', '2023-12-16 17:31:52'),
(4, 4, 1, 'Almacén', 'almacen@gmail.com', NULL, '$2y$10$frlNT9BsnP7pY7.3pnzYfu3FR.hZMvavN4D7nHYJVcKrZDCBDS.EO', NULL, 1, '2023-12-16 17:31:53', '2023-12-16 17:31:53'),
(5, 3, 1, 'Contabilidad', 'contabilidad@gmail.com', NULL, '$2y$10$hcoW.8HmzlYLiJQ63g6pBuKmswyBaNLp/zYMBE4Z9HMYZFXDVYL6S', NULL, 1, '2023-12-16 17:31:53', '2023-12-16 17:31:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `almacen_movimientos`
--
ALTER TABLE `almacen_movimientos`
  ADD PRIMARY KEY (`id_almacen_movimientos`),
  ADD KEY `Almacen_Sucursal_fk` (`id_sucursal`),
  ADD KEY `Almacen_Usuario_fk` (`id_usuario`),
  ADD KEY `Almacen_Producto_fk` (`id_producto`),
  ADD KEY `Almacen_TipoMovimiento_fk` (`id_tipo_movimiento`),
  ADD KEY `Almacen_UnidadMedida` (`id_unidad_medida`);

--
-- Indexes for table `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`id_caja`),
  ADD KEY `Caja_Sucursal_fk` (`id_sucursal`),
  ADD KEY `Caja_Usuario_fk` (`id_usuario`);

--
-- Indexes for table `caja_detalle`
--
ALTER TABLE `caja_detalle`
  ADD PRIMARY KEY (`id_caja_det`),
  ADD KEY `CajaDetalle_Caja_fk` (`id_caja`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `nro_doc` (`nro_doc`),
  ADD KEY `Cliente_TipoDocumento_fk` (`id_tipo_doc`),
  ADD KEY `Cliente_Sucursal_fk` (`id_sucursal`);

--
-- Indexes for table `cliente_direcciones`
--
ALTER TABLE `cliente_direcciones`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `ClienteDireccion_Cliente` (`id_cliente`);

--
-- Indexes for table `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `Compra_Usuario_fk` (`id_usuario`),
  ADD KEY `Compra_Sucursal_fk` (`id_sucursal`),
  ADD KEY `Compra_Proveedor_fk` (`id_proveedor`),
  ADD KEY `Compra_Moneda_fk` (`id_moneda`),
  ADD KEY `Compra_MedioPago_fk` (`id_medio_pago`),
  ADD KEY `Compra_TipoCambio_fk` (`id_tipo_cambio`),
  ADD KEY `Compra_TipoComprobante_fk` (`id_tipo_comprobante`),
  ADD KEY `Compra_CompraEstado_fk` (`id_estado`);

--
-- Indexes for table `compras_detalle`
--
ALTER TABLE `compras_detalle`
  ADD PRIMARY KEY (`id_compra_detalle`),
  ADD KEY `CompraDetalle_Compra_fk` (`id_compra`),
  ADD KEY `CompraDetalle_Producto_fk` (`id_producto`),
  ADD KEY `CompraDetalle_UnidadMedida_fk` (`id_unidad_medida`),
  ADD KEY `CompraDetalle_ListaDetalle_fk` (`id_lista_detalle`),
  ADD KEY `CompraDetalle_Lote_fk` (`lote_id`);

--
-- Indexes for table `compra_estado`
--
ALTER TABLE `compra_estado`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `compressed_tables`
--
ALTER TABLE `compressed_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comprobantes`
--
ALTER TABLE `comprobantes`
  ADD PRIMARY KEY (`id_comprobante`),
  ADD KEY `Comprobante_Cliente_fk` (`id_cliente`),
  ADD KEY `Comprobante_Usuario_fk` (`id_usuario`),
  ADD KEY `Comprobante_Sucursal_fk` (`id_sucursal`),
  ADD KEY `Comprobante_TipoComprobante_fk` (`id_tipo_comprobante`),
  ADD KEY `Comprobante_Moneda_fk` (`id_moneda`),
  ADD KEY `Comprobante_Estado_fk` (`id_estado_comprobante`),
  ADD KEY `Comprobante_MedioPago_fk` (`id_medio_pago`),
  ADD KEY `Comprobante_TipoCambio_fk` (`id_tipo_cambio`),
  ADD KEY `Comprobante_Serie_fk` (`id_serie`);

--
-- Indexes for table `comprobante_detalle`
--
ALTER TABLE `comprobante_detalle`
  ADD PRIMARY KEY (`id_comp_detalle`),
  ADD KEY `ComprobanteDetalle_Comprobante_fk` (`id_comprobante`),
  ADD KEY `ComprobanteDetalle_Producto_fk` (`id_producto`),
  ADD KEY `ComprobanteDetalle_UnidadMedida_fk` (`id_unidad_medida`),
  ADD KEY `ComprobanteDetalle_ListaDetalle_fk` (`id_lista_detalle`),
  ADD KEY `ComprobanteDetalle_Lote_fk` (`id_lote`);

--
-- Indexes for table `comprobante_estados`
--
ALTER TABLE `comprobante_estados`
  ADD PRIMARY KEY (`id_estado_comprobante`);

--
-- Indexes for table `condiciones_almacenamiento`
--
ALTER TABLE `condiciones_almacenamiento`
  ADD PRIMARY KEY (`id_condicion_alm`);

--
-- Indexes for table `deudas_compras`
--
ALTER TABLE `deudas_compras`
  ADD PRIMARY KEY (`id_deuda`),
  ADD KEY `DeudaCompra_Compra_fk` (`id_compra`),
  ADD KEY `DeudaCompra_Proveedor_fk` (`id_proveedor`);

--
-- Indexes for table `deudas_compras_pagos`
--
ALTER TABLE `deudas_compras_pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `DeudaPago_Pago_fk` (`id_deuda`);

--
-- Indexes for table `deudas_comprobantes`
--
ALTER TABLE `deudas_comprobantes`
  ADD PRIMARY KEY (`id_deuda`),
  ADD KEY `Deuda_Comprobante_fk` (`id_comprobante`),
  ADD KEY `Deuda_Cliente_fk` (`id_cliente`);

--
-- Indexes for table `deudas_comprobantes_pagos`
--
ALTER TABLE `deudas_comprobantes_pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `DeudaPago_Deuda_fk` (`id_deuda`);

--
-- Indexes for table `egresos`
--
ALTER TABLE `egresos`
  ADD PRIMARY KEY (`id_egreso`),
  ADD KEY `Egreso_Sucursal_fk` (`id_sucursal`),
  ADD KEY `Egreso_Usuario_fk` (`id_usuario`),
  ADD KEY `Egreso_Compra_fk` (`id_compra`),
  ADD KEY `Egreso_TipoEgreso_fk` (`id_tipo_egreso`),
  ADD KEY `Egreso_MotivoEgreso_fk` (`id_motivo_egreso`);

--
-- Indexes for table `egreso_motivos`
--
ALTER TABLE `egreso_motivos`
  ADD PRIMARY KEY (`id_egreso_motivo`);

--
-- Indexes for table `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`),
  ADD KEY `Empresa_FilePath_fk` (`id_file`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `file_paths`
--
ALTER TABLE `file_paths`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laboratorios`
--
ALTER TABLE `laboratorios`
  ADD PRIMARY KEY (`id_laboratorio`);

--
-- Indexes for table `lista_precios`
--
ALTER TABLE `lista_precios`
  ADD PRIMARY KEY (`id_lista_precio`);

--
-- Indexes for table `lista_precios_detalle`
--
ALTER TABLE `lista_precios_detalle`
  ADD PRIMARY KEY (`id_lista_detalle`),
  ADD KEY `ListaPDetalle_ListaPrecio_fk` (`id_lista_precio`),
  ADD KEY `ListaPDetalle_Producto_fk` (`id_producto`),
  ADD KEY `ListaPDetalle_Sucursal_fk` (`id_sucursal`);

--
-- Indexes for table `lote_productos`
--
ALTER TABLE `lote_productos`
  ADD PRIMARY KEY (`id_lote`),
  ADD KEY `Lote_Sucursal_fk` (`id_sucursal`),
  ADD KEY `Lote_Producto_fk` (`id_producto`);

--
-- Indexes for table `marcas_productos`
--
ALTER TABLE `marcas_productos`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indexes for table `medio_pago`
--
ALTER TABLE `medio_pago`
  ADD PRIMARY KEY (`id_medio_pago`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `monedas`
--
ALTER TABLE `monedas`
  ADD PRIMARY KEY (`id_moneda`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `orden_compra`
--
ALTER TABLE `orden_compra`
  ADD PRIMARY KEY (`id_orden_compra`),
  ADD KEY `OrdenCompra_Usuario_fk` (`id_usuario`),
  ADD KEY `OrdenCompra_Sucursal_fk` (`id_sucursal`),
  ADD KEY `OrdenCompra_Proveedor_fk` (`id_proveedor`),
  ADD KEY `OrdenCompra_Moneda_fk` (`id_moneda`),
  ADD KEY `OrdenCompra_MedioPago_fk` (`id_medio_pago`),
  ADD KEY `OrdenCompra_TipoCambio_fk` (`id_tipo_cambio`);

--
-- Indexes for table `orden_compra_detalle`
--
ALTER TABLE `orden_compra_detalle`
  ADD PRIMARY KEY (`id_orden_detalle`),
  ADD KEY `OrdComprDetalle_OrdenCompra_fk` (`id_orden_compra`),
  ADD KEY `OrdComprDetalle_Producto_fk` (`id_producto`),
  ADD KEY `OrdComprDetalle_UnidadMedida_fk` (`id_unidad_medida`),
  ADD KEY `OrdComprDetalle_ListaDetalle_fk` (`id_lista_detalle`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `productos_categorias`
--
ALTER TABLE `productos_categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indexes for table `productos_servicios`
--
ALTER TABLE `productos_servicios`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `Producto_Marca_fk` (`id_marca`),
  ADD KEY `Producto_Categoria_fk` (`id_categoria`),
  ADD KEY `Producto_UnidadMedida_fk` (`id_unidad_medida`),
  ADD KEY `Producto_Sucursal_fk` (`id_sucursal`),
  ADD KEY `Producto_Laboratorio_fk` (`id_laboratorio`),
  ADD KEY `Producto_CondicionAlm_fk` (`id_condicion_alm`),
  ADD KEY `Producto_TipoProducto_fk` (`id_tipo_producto`);

--
-- Indexes for table `producto_tipos`
--
ALTER TABLE `producto_tipos`
  ADD PRIMARY KEY (`id_producto_tipo`);

--
-- Indexes for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD UNIQUE KEY `nro_doc` (`nro_doc`),
  ADD KEY `Proveedor_TipoDocumento_fk` (`id_tipo_doc`),
  ADD KEY `Proveedor_Sucursal_fk` (`id_sucursal`);

--
-- Indexes for table `prv_cotizaciones`
--
ALTER TABLE `prv_cotizaciones`
  ADD PRIMARY KEY (`id_cotizacion_prv`),
  ADD KEY `Cotizacion_Usuario_fk` (`id_usuario`),
  ADD KEY `Cotizacion_Sucursal_fk` (`id_sucursal`),
  ADD KEY `Cotizacion_Proveedor_fk` (`id_proveedor`),
  ADD KEY `Cotizacion_Estado_fk` (`id_estado`);

--
-- Indexes for table `prv_cotizacion_detalle`
--
ALTER TABLE `prv_cotizacion_detalle`
  ADD PRIMARY KEY (`id_cotz_detalle_prv`),
  ADD KEY `CotizacionDetalle_Cotizacion_fk` (`id_cotizacion_prv`),
  ADD KEY `CotizacionDetalle_Producto_fk` (`id_producto`),
  ADD KEY `CotizacionDetalle_UnidadMedida_fk` (`id_unidad_medida`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `series_inv`
--
ALTER TABLE `series_inv`
  ADD PRIMARY KEY (`id_serie`),
  ADD KEY `SerieInv_Sucursal_fk` (`id_sucursal`),
  ADD KEY `SerieInv_TipoComprobante_fk` (`id_tipo_comprobante`);

--
-- Indexes for table `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id_sucursal`),
  ADD KEY `Sucursal_Empresa_fk` (`id_empresa`);

--
-- Indexes for table `tipos_comprobante`
--
ALTER TABLE `tipos_comprobante`
  ADD PRIMARY KEY (`id_tipo_comprobante`);

--
-- Indexes for table `tipos_egreso`
--
ALTER TABLE `tipos_egreso`
  ADD PRIMARY KEY (`id_tipo_egreso`);

--
-- Indexes for table `tipos_movimiento`
--
ALTER TABLE `tipos_movimiento`
  ADD PRIMARY KEY (`id_tipo_movimiento`);

--
-- Indexes for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  ADD PRIMARY KEY (`id_tipo_cambio`);

--
-- Indexes for table `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`id_tipo_doc`);

--
-- Indexes for table `ubigeo_peru_departments`
--
ALTER TABLE `ubigeo_peru_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ubigeo_peru_districts`
--
ALTER TABLE `ubigeo_peru_districts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ubigeo_peru_provinces`
--
ALTER TABLE `ubigeo_peru_provinces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`id_unidad_medida`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `Usuario_Rol_fk` (`id_role`),
  ADD KEY `Usuario_Sucursal_fk` (`id_sucursal`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `almacen_movimientos`
--
ALTER TABLE `almacen_movimientos`
  MODIFY `id_almacen_movimientos` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `caja`
--
ALTER TABLE `caja`
  MODIFY `id_caja` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `caja_detalle`
--
ALTER TABLE `caja_detalle`
  MODIFY `id_caja_det` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cliente_direcciones`
--
ALTER TABLE `cliente_direcciones`
  MODIFY `id_direccion` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `compras`
--
ALTER TABLE `compras`
  MODIFY `id_compra` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `compras_detalle`
--
ALTER TABLE `compras_detalle`
  MODIFY `id_compra_detalle` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `compra_estado`
--
ALTER TABLE `compra_estado`
  MODIFY `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `compressed_tables`
--
ALTER TABLE `compressed_tables`
  MODIFY `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comprobantes`
--
ALTER TABLE `comprobantes`
  MODIFY `id_comprobante` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `comprobante_detalle`
--
ALTER TABLE `comprobante_detalle`
  MODIFY `id_comp_detalle` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `comprobante_estados`
--
ALTER TABLE `comprobante_estados`
  MODIFY `id_estado_comprobante` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `condiciones_almacenamiento`
--
ALTER TABLE `condiciones_almacenamiento`
  MODIFY `id_condicion_alm` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `deudas_compras`
--
ALTER TABLE `deudas_compras`
  MODIFY `id_deuda` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `deudas_compras_pagos`
--
ALTER TABLE `deudas_compras_pagos`
  MODIFY `id_pago` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `deudas_comprobantes`
--
ALTER TABLE `deudas_comprobantes`
  MODIFY `id_deuda` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deudas_comprobantes_pagos`
--
ALTER TABLE `deudas_comprobantes_pagos`
  MODIFY `id_pago` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `egresos`
--
ALTER TABLE `egresos`
  MODIFY `id_egreso` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `egreso_motivos`
--
ALTER TABLE `egreso_motivos`
  MODIFY `id_egreso_motivo` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file_paths`
--
ALTER TABLE `file_paths`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laboratorios`
--
ALTER TABLE `laboratorios`
  MODIFY `id_laboratorio` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lista_precios`
--
ALTER TABLE `lista_precios`
  MODIFY `id_lista_precio` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lista_precios_detalle`
--
ALTER TABLE `lista_precios_detalle`
  MODIFY `id_lista_detalle` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lote_productos`
--
ALTER TABLE `lote_productos`
  MODIFY `id_lote` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `marcas_productos`
--
ALTER TABLE `marcas_productos`
  MODIFY `id_marca` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `medio_pago`
--
ALTER TABLE `medio_pago`
  MODIFY `id_medio_pago` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `monedas`
--
ALTER TABLE `monedas`
  MODIFY `id_moneda` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orden_compra`
--
ALTER TABLE `orden_compra`
  MODIFY `id_orden_compra` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orden_compra_detalle`
--
ALTER TABLE `orden_compra_detalle`
  MODIFY `id_orden_detalle` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `productos_categorias`
--
ALTER TABLE `productos_categorias`
  MODIFY `id_categoria` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `productos_servicios`
--
ALTER TABLE `productos_servicios`
  MODIFY `id_producto` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `producto_tipos`
--
ALTER TABLE `producto_tipos`
  MODIFY `id_producto_tipo` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `prv_cotizaciones`
--
ALTER TABLE `prv_cotizaciones`
  MODIFY `id_cotizacion_prv` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prv_cotizacion_detalle`
--
ALTER TABLE `prv_cotizacion_detalle`
  MODIFY `id_cotz_detalle_prv` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `series_inv`
--
ALTER TABLE `series_inv`
  MODIFY `id_serie` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id_sucursal` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tipos_comprobante`
--
ALTER TABLE `tipos_comprobante`
  MODIFY `id_tipo_comprobante` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tipos_egreso`
--
ALTER TABLE `tipos_egreso`
  MODIFY `id_tipo_egreso` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tipos_movimiento`
--
ALTER TABLE `tipos_movimiento`
  MODIFY `id_tipo_movimiento` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  MODIFY `id_tipo_cambio` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `id_tipo_doc` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `id_unidad_medida` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `almacen_movimientos`
--
ALTER TABLE `almacen_movimientos`
  ADD CONSTRAINT `Almacen_Producto_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos_servicios` (`id_producto`),
  ADD CONSTRAINT `Almacen_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `Almacen_TipoMovimiento_fk` FOREIGN KEY (`id_tipo_movimiento`) REFERENCES `tipos_movimiento` (`id_tipo_movimiento`),
  ADD CONSTRAINT `Almacen_UnidadMedida` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`),
  ADD CONSTRAINT `Almacen_Usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);

--
-- Constraints for table `caja`
--
ALTER TABLE `caja`
  ADD CONSTRAINT `Caja_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `Caja_Usuario_fk	` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);

--
-- Constraints for table `caja_detalle`
--
ALTER TABLE `caja_detalle`
  ADD CONSTRAINT `CajaDetalle_Caja_fk` FOREIGN KEY (`id_caja`) REFERENCES `caja` (`id_caja`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `Cliente_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `Cliente_TipoDocumento_fk` FOREIGN KEY (`id_tipo_doc`) REFERENCES `tipo_documento` (`id_tipo_doc`);

--
-- Constraints for table `cliente_direcciones`
--
ALTER TABLE `cliente_direcciones`
  ADD CONSTRAINT `ClienteDireccion_Cliente_fk` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `Compra_CompraEstado_fk` FOREIGN KEY (`id_estado`) REFERENCES `compra_estado` (`id`),
  ADD CONSTRAINT `Compra_MedioPago_fk` FOREIGN KEY (`id_medio_pago`) REFERENCES `medio_pago` (`id_medio_pago`),
  ADD CONSTRAINT `Compra_Moneda_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`),
  ADD CONSTRAINT `Compra_Proveedor_fk` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `Compra_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `Compra_TipoCambio_fk` FOREIGN KEY (`id_tipo_cambio`) REFERENCES `tipo_cambio` (`id_tipo_cambio`),
  ADD CONSTRAINT `Compra_TipoComprobante_fk` FOREIGN KEY (`id_tipo_comprobante`) REFERENCES `tipos_comprobante` (`id_tipo_comprobante`),
  ADD CONSTRAINT `Compra_Usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);

--
-- Constraints for table `compras_detalle`
--
ALTER TABLE `compras_detalle`
  ADD CONSTRAINT `CompraDetalle_Compra_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`),
  ADD CONSTRAINT `CompraDetalle_ListaDetalle_fk` FOREIGN KEY (`id_lista_detalle`) REFERENCES `lista_precios_detalle` (`id_lista_detalle`),
  ADD CONSTRAINT `CompraDetalle_Lote_fk` FOREIGN KEY (`lote_id`) REFERENCES `lote_productos` (`id_lote`),
  ADD CONSTRAINT `CompraDetalle_Producto_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos_servicios` (`id_producto`),
  ADD CONSTRAINT `CompraDetalle_UnidadMedida_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`);

--
-- Constraints for table `comprobantes`
--
ALTER TABLE `comprobantes`
  ADD CONSTRAINT `Comprobante_Cliente_fk` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `Comprobante_Estado_fk` FOREIGN KEY (`id_estado_comprobante`) REFERENCES `comprobante_estados` (`id_estado_comprobante`),
  ADD CONSTRAINT `Comprobante_MedioPago_fk` FOREIGN KEY (`id_medio_pago`) REFERENCES `medio_pago` (`id_medio_pago`),
  ADD CONSTRAINT `Comprobante_Moneda_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`),
  ADD CONSTRAINT `Comprobante_Serie_fk` FOREIGN KEY (`id_serie`) REFERENCES `series_inv` (`id_serie`),
  ADD CONSTRAINT `Comprobante_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `Comprobante_TipoCambio_fk` FOREIGN KEY (`id_tipo_cambio`) REFERENCES `tipo_cambio` (`id_tipo_cambio`),
  ADD CONSTRAINT `Comprobante_TipoComprobante_fk` FOREIGN KEY (`id_tipo_comprobante`) REFERENCES `tipos_comprobante` (`id_tipo_comprobante`),
  ADD CONSTRAINT `Comprobante_Usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);

--
-- Constraints for table `comprobante_detalle`
--
ALTER TABLE `comprobante_detalle`
  ADD CONSTRAINT `ComprobanteDetalle_Comprobante_fk` FOREIGN KEY (`id_comprobante`) REFERENCES `comprobantes` (`id_comprobante`),
  ADD CONSTRAINT `ComprobanteDetalle_ListaDetalle_fk` FOREIGN KEY (`id_lista_detalle`) REFERENCES `lista_precios_detalle` (`id_lista_detalle`),
  ADD CONSTRAINT `ComprobanteDetalle_Lote_fk` FOREIGN KEY (`id_lote`) REFERENCES `lote_productos` (`id_lote`),
  ADD CONSTRAINT `ComprobanteDetalle_Producto_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos_servicios` (`id_producto`),
  ADD CONSTRAINT `ComprobanteDetalle_UnidadMedida_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`);

--
-- Constraints for table `deudas_compras`
--
ALTER TABLE `deudas_compras`
  ADD CONSTRAINT `DeudaCompra_Compra_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`),
  ADD CONSTRAINT `DeudaCompra_Proveedor_fk` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`);

--
-- Constraints for table `deudas_compras_pagos`
--
ALTER TABLE `deudas_compras_pagos`
  ADD CONSTRAINT `DeudaPago_Pago_fk` FOREIGN KEY (`id_deuda`) REFERENCES `deudas_compras` (`id_deuda`);

--
-- Constraints for table `deudas_comprobantes`
--
ALTER TABLE `deudas_comprobantes`
  ADD CONSTRAINT `Deuda_Cliente_fk` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `Deuda_Comprobante_fk` FOREIGN KEY (`id_comprobante`) REFERENCES `comprobantes` (`id_comprobante`);

--
-- Constraints for table `deudas_comprobantes_pagos`
--
ALTER TABLE `deudas_comprobantes_pagos`
  ADD CONSTRAINT `DeudaPago_Deuda_fk` FOREIGN KEY (`id_deuda`) REFERENCES `deudas_comprobantes` (`id_deuda`);

--
-- Constraints for table `egresos`
--
ALTER TABLE `egresos`
  ADD CONSTRAINT `Egreso_Compra_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`),
  ADD CONSTRAINT `Egreso_MotivoEgreso_fk` FOREIGN KEY (`id_motivo_egreso`) REFERENCES `egreso_motivos` (`id_egreso_motivo`),
  ADD CONSTRAINT `Egreso_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `Egreso_TipoEgreso_fk` FOREIGN KEY (`id_tipo_egreso`) REFERENCES `tipos_egreso` (`id_tipo_egreso`),
  ADD CONSTRAINT `Egreso_Usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);

--
-- Constraints for table `empresas`
--
ALTER TABLE `empresas`
  ADD CONSTRAINT `Empresa_FilePath_fk` FOREIGN KEY (`id_file`) REFERENCES `file_paths` (`id`);

--
-- Constraints for table `lista_precios_detalle`
--
ALTER TABLE `lista_precios_detalle`
  ADD CONSTRAINT `ListaPDetalle_ListaPrecio_fk` FOREIGN KEY (`id_lista_precio`) REFERENCES `lista_precios` (`id_lista_precio`),
  ADD CONSTRAINT `ListaPDetalle_Producto_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos_servicios` (`id_producto`),
  ADD CONSTRAINT `ListaPDetalle_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`);

--
-- Constraints for table `lote_productos`
--
ALTER TABLE `lote_productos`
  ADD CONSTRAINT `Lote_Producto_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos_servicios` (`id_producto`),
  ADD CONSTRAINT `Lote_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orden_compra`
--
ALTER TABLE `orden_compra`
  ADD CONSTRAINT `OrdenCompra_MedioPago_fk` FOREIGN KEY (`id_medio_pago`) REFERENCES `medio_pago` (`id_medio_pago`),
  ADD CONSTRAINT `OrdenCompra_Moneda_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`),
  ADD CONSTRAINT `OrdenCompra_Proveedor_fk` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `OrdenCompra_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `OrdenCompra_TipoCambio_fk` FOREIGN KEY (`id_tipo_cambio`) REFERENCES `tipo_cambio` (`id_tipo_cambio`),
  ADD CONSTRAINT `OrdenCompra_Usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);

--
-- Constraints for table `orden_compra_detalle`
--
ALTER TABLE `orden_compra_detalle`
  ADD CONSTRAINT `OrdComprDetalle_ListaDetalle_fk` FOREIGN KEY (`id_lista_detalle`) REFERENCES `lista_precios_detalle` (`id_lista_detalle`),
  ADD CONSTRAINT `OrdComprDetalle_OrdenCompra_fk` FOREIGN KEY (`id_orden_compra`) REFERENCES `orden_compra` (`id_orden_compra`),
  ADD CONSTRAINT `OrdComprDetalle_Producto_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos_servicios` (`id_producto`),
  ADD CONSTRAINT `OrdComprDetalle_UnidadMedida_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`);

--
-- Constraints for table `productos_servicios`
--
ALTER TABLE `productos_servicios`
  ADD CONSTRAINT `Producto_Categoria_fk` FOREIGN KEY (`id_categoria`) REFERENCES `productos_categorias` (`id_categoria`),
  ADD CONSTRAINT `Producto_CondicionAlm_fk` FOREIGN KEY (`id_condicion_alm`) REFERENCES `condiciones_almacenamiento` (`id_condicion_alm`),
  ADD CONSTRAINT `Producto_Laboratorio_fk` FOREIGN KEY (`id_laboratorio`) REFERENCES `laboratorios` (`id_laboratorio`),
  ADD CONSTRAINT `Producto_Marca_fk` FOREIGN KEY (`id_marca`) REFERENCES `marcas_productos` (`id_marca`),
  ADD CONSTRAINT `Producto_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `Producto_TipoProducto_fk` FOREIGN KEY (`id_tipo_producto`) REFERENCES `producto_tipos` (`id_producto_tipo`),
  ADD CONSTRAINT `Producto_UnidadMedida_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`);

--
-- Constraints for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD CONSTRAINT `Proveedor_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `Proveedor_TipoDocumento_fk` FOREIGN KEY (`id_tipo_doc`) REFERENCES `tipo_documento` (`id_tipo_doc`);

--
-- Constraints for table `prv_cotizaciones`
--
ALTER TABLE `prv_cotizaciones`
  ADD CONSTRAINT `Cotizacion_Estado_fk` FOREIGN KEY (`id_estado`) REFERENCES `compra_estado` (`id`),
  ADD CONSTRAINT `Cotizacion_Proveedor_fk` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `Cotizacion_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `Cotizacion_Usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);

--
-- Constraints for table `prv_cotizacion_detalle`
--
ALTER TABLE `prv_cotizacion_detalle`
  ADD CONSTRAINT `CotizacionDetalle_Cotizacion_fk` FOREIGN KEY (`id_cotizacion_prv`) REFERENCES `prv_cotizaciones` (`id_cotizacion_prv`),
  ADD CONSTRAINT `CotizacionDetalle_Producto_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos_servicios` (`id_producto`),
  ADD CONSTRAINT `CotizacionDetalle_UnidadMedida_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`);

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `series_inv`
--
ALTER TABLE `series_inv`
  ADD CONSTRAINT `SerieInv_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  ADD CONSTRAINT `SerieInv_TipoComprobante_fk` FOREIGN KEY (`id_tipo_comprobante`) REFERENCES `tipos_comprobante` (`id_tipo_comprobante`);

--
-- Constraints for table `sucursales`
--
ALTER TABLE `sucursales`
  ADD CONSTRAINT `Sucursal_Empresa_fk` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `Usuario_Rol_fk` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `Usuario_Sucursal_fk` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

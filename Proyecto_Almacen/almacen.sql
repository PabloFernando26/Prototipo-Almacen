-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 10-11-2024 a las 01:52:39
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
-- /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
-- /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
-- /*!40101 SET NAMES utf8mb4 */;

-- Base de datos: `almacen`

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boleta`
--

DROP TABLE IF EXISTS `boleta`;
CREATE TABLE IF NOT EXISTS `boleta` (
  `numBoleta` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `total` int NOT NULL,
  `iva` int NOT NULL,
  `p_final` int NOT NULL,
  `metodoPago` varchar(100) NOT NULL,
  `venta_id` int DEFAULT NULL,
  PRIMARY KEY (`numBoleta`),
  KEY `venta_id` (`venta_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `boleta`
--

INSERT INTO `boleta` (`numBoleta`, `fecha`, `total`, `iva`, `p_final`, `metodoPago`, `venta_id`) VALUES
(10, '2024-11-03', 20000, 0, 0, 'Efectivo', 0),
(9, '2024-11-03', 1600, 0, 0, 'Efectivo', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`) VALUES
(1, 'Frutas'),
(2, 'Lácteos'),
(3, 'Verduras'),
(4, 'Bebidas'),
(5, 'Panadería'),
(6, 'Carnes'),
(7, 'Congelados'),
(8, 'Abarrotes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

DROP TABLE IF EXISTS `factura`;
CREATE TABLE IF NOT EXISTS `factura` (
  `id` int NOT NULL AUTO_INCREMENT,
  `detalles` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `f_neto` int NOT NULL,
  `f_iva` int NOT NULL,
  `total` int NOT NULL,
  `m_pago` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `venta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_factura_venta` (`venta_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`id`, `detalles`, `f_neto`, `f_iva`, `total`, `m_pago`, `venta_id`) VALUES
(2, 'Venta de productos', 12000, 2280, 14280, 'Crédito', 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

DROP TABLE IF EXISTS `gastos`;
CREATE TABLE IF NOT EXISTS `gastos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `monto` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id`, `monto`, `fecha`, `descripcion`) VALUES
(1, 3000.00, '2024-10-02', 'Compra de papelería');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

DROP TABLE IF EXISTS `pedido`;
CREATE TABLE IF NOT EXISTS `pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `estado` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `administrador_id` int NOT NULL,
  `proveedor_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pedido_administrador` (`administrador_id`),
  KEY `fk_pedido_proveedor` (`proveedor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `categoria_id` int NOT NULL,
  `precio` int NOT NULL,
  `fechaCaducidad` date NOT NULL,
  `stock` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_producto_categoria` (`categoria_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `categoria_id`, `precio`, `fechaCaducidad`, `stock`) VALUES
(22, 'Manzanas', 1, 200, '2025-11-01', 51),
(23, 'Bananas', 1, 150, '2025-10-01', 75),
(24, 'Leche', 2, 500, '2025-01-01', 120),
(25, 'Yogurt', 2, 300, '2025-05-01', 80),
(26, 'Tomates', 3, 250, '2025-02-01', 60),
(27, 'Zanahorias', 3, 180, '2025-03-01', 45),
(28, 'Coca Cola', 4, 800, '2025-07-01', 150),
(29, 'Agua', 4, 350, '2025-08-01', 200),
(30, 'Pan de molde', 5, 900, '2025-09-01', 50),
(31, 'Pan baguette', 5, 450, '2025-09-15', 30),
(32, 'Pechuga de pollo', 6, 1500, '2025-12-01', 70),
(33, 'Costillas de cerdo', 6, 2200, '2025-11-01', 40),
(34, 'Helado de vainilla', 7, 1200, '2025-06-01', 35),
(35, 'Helado de chocolate', 7, 1200, '2025-06-10', 30),
(36, 'Arroz', 8, 1000, '2025-04-01', 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE IF NOT EXISTS `proveedor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `contacto` varchar(255) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `telefono` int DEFAULT NULL,
  `direccion` text,
  `productos_ofrecidos` text,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id`, `nombre`, `apellido`, `contacto`, `email`, `telefono`, `direccion`, `productos_ofrecidos`, `fecha_registro`, `estado`) VALUES
(2, 'Juan', 'Ancares', 'JuanFini', 'juan.ancares@proveedor.cl', 2147483647, 'La Antera 156', 'Jugos', '2024-11-01 11:12:26', 'activo'),
(3, 'Maria', 'Lopez', 'MariaProductos', 'maria.lopez@proveedor.cl', 1234567890, 'Av. Las Palmas 432', 'Lácteos, Panadería', '2024-11-03 15:30:00', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `apellido` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `clave` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `rol` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `apellido`, `email`, `clave`, `rol`) VALUES
(3, 'Administrador', 'Principal', 'p.fernandomanriquez001@gmail.com', 'canela1606', 'Administrador'),
(6, 'Carlos', 'Covarrubias', 'admin.test@gmail.com', 'Admin7362', 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

DROP TABLE IF EXISTS `venta`;
CREATE TABLE IF NOT EXISTS `venta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `total` int NOT NULL,
  `metodoPago` varchar(100) NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` int NOT NULL,
  `boleta_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `boleta_id` (`boleta_id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`id`, `fecha`, `total`, `metodoPago`, `producto_id`, `cantidad`, `boleta_id`) VALUES
(20, '2024-11-03', 9000, 'Efectivo', 22, 4, 10),
(21, '2024-11-04', 2000, 'Crédito', 23, 10, 9);

COMMIT;

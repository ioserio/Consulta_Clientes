-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-09-2025 a las 22:57:53
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `consulta_cliente`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cartera_clientes`
--

CREATE TABLE `cartera_clientes` (
  `id` int(11) NOT NULL,
  `Codigo` varchar(50) DEFAULT NULL,
  `Nombre` varchar(100) DEFAULT NULL,
  `TipoDocIdentidad` varchar(50) DEFAULT NULL,
  `DocIdentidad` varchar(50) DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT NULL,
  `Direccion` varchar(150) DEFAULT NULL,
  `CodigoZonaVenta` varchar(50) DEFAULT NULL,
  `DescripcionZonaVenta` varchar(100) DEFAULT NULL,
  `LineaCredito` decimal(10,2) DEFAULT NULL,
  `CodigoZonaReparto` varchar(50) DEFAULT NULL,
  `DescripcionZonaReparto` varchar(100) DEFAULT NULL,
  `CategoriaCliente` varchar(50) DEFAULT NULL,
  `TipoCliente` varchar(50) DEFAULT NULL,
  `Distrito` varchar(50) DEFAULT NULL,
  `PKID` int(11) DEFAULT NULL,
  `IDCategoriaCliente` int(11) DEFAULT NULL,
  `IDZonaVenta` int(11) DEFAULT NULL,
  `CCC` varchar(50) DEFAULT NULL,
  `TamañoNegocio` varchar(50) DEFAULT NULL,
  `MixProductos` varchar(100) DEFAULT NULL,
  `MaquinaExhibidora` tinyint(1) DEFAULT NULL,
  `CortadorEmbutidos` tinyint(1) DEFAULT NULL,
  `Visicooler` tinyint(1) DEFAULT NULL,
  `CajaRegistradora` tinyint(1) DEFAULT NULL,
  `TelefonoPublico` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cartera_clientes`
--
ALTER TABLE `cartera_clientes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cartera_clientes`
--
ALTER TABLE `cartera_clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

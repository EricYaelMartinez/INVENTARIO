-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2025 a las 20:41:54
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda_inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `CategoriaID` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cortecaja`
--

CREATE TABLE `cortecaja` (
  `CorteID` int(11) NOT NULL,
  `Fecha` datetime NOT NULL,
  `MontoInicialCaja` decimal(10,2) NOT NULL,
  `TotalVentasEfectivo` decimal(10,2) NOT NULL,
  `TotalVentasCalculado` decimal(10,2) NOT NULL,
  `TotalCajaEsperado` decimal(10,2) NOT NULL,
  `TotalCajaFísico` decimal(10,2) NOT NULL,
  `Diferencia` decimal(10,2) NOT NULL,
  `UsuarioID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleentrada`
--

CREATE TABLE `detalleentrada` (
  `EntradaID` int(11) NOT NULL,
  `ProductoID` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `CostoUnitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleventa`
--

CREATE TABLE `detalleventa` (
  `VentasID` int(11) NOT NULL,
  `ProductoID` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `PrecioVendido` decimal(10,2) NOT NULL,
  `Subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradasmercancia`
--

CREATE TABLE `entradasmercancia` (
  `EntradaID` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `ProveedorID` int(11) NOT NULL,
  `TotalCosto` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `ProductoID` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `CodigoBarra` varchar(50) NOT NULL,
  `Descripcion` varchar(200) NOT NULL,
  `PrecioVenta` float NOT NULL,
  `Stock` int(11) NOT NULL,
  `StockMinimo` int(11) NOT NULL,
  `Imagen` varchar(255) NOT NULL,
  `CategoriaID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `ProveedorID` int(11) NOT NULL,
  `NombreProveedor` varchar(100) NOT NULL,
  `Contacto` varchar(100) NOT NULL,
  `Telefono` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `UsuarioID` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Correo` varchar(100) NOT NULL,
  `Pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `VentasID` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Total` int(11) NOT NULL,
  `PagoCliente` decimal(10,0) NOT NULL,
  `Cambio` decimal(10,0) NOT NULL,
  `UsuarioID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`CategoriaID`);

--
-- Indices de la tabla `cortecaja`
--
ALTER TABLE `cortecaja`
  ADD PRIMARY KEY (`CorteID`),
  ADD KEY `fk_usuario_corte` (`UsuarioID`);

--
-- Indices de la tabla `detalleentrada`
--
ALTER TABLE `detalleentrada`
  ADD PRIMARY KEY (`EntradaID`,`ProductoID`),
  ADD KEY `ProductoID` (`ProductoID`);

--
-- Indices de la tabla `detalleventa`
--
ALTER TABLE `detalleventa`
  ADD PRIMARY KEY (`VentasID`,`ProductoID`),
  ADD KEY `ProductoID` (`ProductoID`);

--
-- Indices de la tabla `entradasmercancia`
--
ALTER TABLE `entradasmercancia`
  ADD PRIMARY KEY (`EntradaID`),
  ADD KEY `ProveedorID` (`ProveedorID`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`ProductoID`),
  ADD KEY `CategoriaID` (`CategoriaID`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`ProveedorID`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`UsuarioID`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`VentasID`),
  ADD KEY `UsuarioID` (`UsuarioID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `CategoriaID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cortecaja`
--
ALTER TABLE `cortecaja`
  MODIFY `CorteID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entradasmercancia`
--
ALTER TABLE `entradasmercancia`
  MODIFY `EntradaID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `ProductoID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `ProveedorID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `UsuarioID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `VentasID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cortecaja`
--
ALTER TABLE `cortecaja`
  ADD CONSTRAINT `fk_usuario_corte` FOREIGN KEY (`UsuarioID`) REFERENCES `usuario` (`UsuarioID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalleentrada`
--
ALTER TABLE `detalleentrada`
  ADD CONSTRAINT `detalleentrada_ibfk_1` FOREIGN KEY (`EntradaID`) REFERENCES `entradasmercancia` (`EntradaID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalleentrada_ibfk_2` FOREIGN KEY (`ProductoID`) REFERENCES `productos` (`ProductoID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalleventa`
--
ALTER TABLE `detalleventa`
  ADD CONSTRAINT `detalleventa_ibfk_1` FOREIGN KEY (`VentasID`) REFERENCES `ventas` (`VentasID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalleventa_ibfk_2` FOREIGN KEY (`ProductoID`) REFERENCES `productos` (`ProductoID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradasmercancia`
--
ALTER TABLE `entradasmercancia`
  ADD CONSTRAINT `entradasmercancia_ibfk_1` FOREIGN KEY (`ProveedorID`) REFERENCES `proveedores` (`ProveedorID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`CategoriaID`) REFERENCES `categorias` (`CategoriaID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`UsuarioID`) REFERENCES `usuario` (`UsuarioID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

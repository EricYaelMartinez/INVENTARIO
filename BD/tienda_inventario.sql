-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-11-2025 a las 07:01:15
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

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`CategoriaID`, `Nombre`) VALUES
(1, 'Detergentes'),
(2, 'Refrescos'),
(5, 'Sabritas'),
(7, 'enlatados'),
(8, 'galletas'),
(9, 'papeles');

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

--
-- Volcado de datos para la tabla `cortecaja`
--

INSERT INTO `cortecaja` (`CorteID`, `Fecha`, `MontoInicialCaja`, `TotalVentasEfectivo`, `TotalVentasCalculado`, `TotalCajaEsperado`, `TotalCajaFísico`, `Diferencia`, `UsuarioID`) VALUES
(1, '2025-11-21 00:00:00', 500.00, 0.00, 0.00, 500.00, 1326.00, 826.00, 7),
(2, '2025-11-20 00:00:00', 500.00, 222.00, 222.00, 722.00, 1000.00, 278.00, 7);

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

--
-- Volcado de datos para la tabla `detalleentrada`
--

INSERT INTO `detalleentrada` (`EntradaID`, `ProductoID`, `Cantidad`, `CostoUnitario`) VALUES
(3, 6, 3, 15.50),
(3, 7, 12, 10.00),
(3, 9, 5, 20.00),
(4, 7, 5, 12.00),
(4, 8, 5, 40.00),
(5, 6, 4, 8.75),
(5, 9, 2, 28.00),
(5, 12, 5, 12.80),
(6, 6, 3, 10.17),
(6, 9, 2, 32.50),
(6, 12, 3, 12.00),
(7, 6, 3, 10.17),
(7, 9, 2, 32.50),
(7, 12, 3, 12.00);

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

--
-- Volcado de datos para la tabla `detalleventa`
--

INSERT INTO `detalleventa` (`VentasID`, `ProductoID`, `Cantidad`, `PrecioVendido`, `Subtotal`) VALUES
(9, 6, 2, 18.00, 36.00),
(9, 7, 3, 15.00, 45.00),
(9, 9, 1, 25.00, 25.00),
(10, 6, 1, 18.00, 18.00),
(10, 7, 1, 15.00, 15.00),
(10, 9, 1, 25.00, 25.00),
(11, 9, 1, 25.00, 25.00),
(12, 6, 1, 18.00, 18.00),
(12, 7, 1, 15.00, 15.00);

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

--
-- Volcado de datos para la tabla `entradasmercancia`
--

INSERT INTO `entradasmercancia` (`EntradaID`, `Fecha`, `ProveedorID`, `TotalCosto`) VALUES
(1, '2025-11-07', 1, 850),
(3, '2025-11-20', 4, 266.5),
(4, '2025-11-19', 5, 260),
(5, '2025-11-18', 1, 0),
(6, '2025-11-17', 4, 131.5),
(7, '2025-11-17', 4, 131.5);

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

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`ProductoID`, `Nombre`, `CodigoBarra`, `Descripcion`, `PrecioVenta`, `Stock`, `StockMinimo`, `Imagen`, `CategoriaID`) VALUES
(6, 'Takis fuego', '7501073839854', '60 gramos', 18, 19, 5, 'uploads/691e23ee5a910.jpg', 5),
(7, 'Coca Cola', '7501088504655', 'Cocacola Vidrio 255ml', 15, 32, 5, 'uploads/691e81d96493c.jpg', 2),
(8, 'Coca Cola 3 litros', '7502009740244', 'Coca cola Desechable 3 litros', 48, 15, 3, 'uploads/691e821c8ea3b.png', 2),
(9, 'Fabuloso Frescura Activa', '7503003406051', 'Fabuloso 1 litro Color azul cielo FRESCURA ACTIVA', 25, 38, 10, 'uploads/691e8284b593c.jpeg', 1),
(12, 'chicharrones', '7501088504659', 'chichorrenes chicos 34gramos', 18, 21, 2, 'uploads/691e8d32745d0.jpg', 5);

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

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`ProveedorID`, `NombreProveedor`, `Contacto`, `Telefono`) VALUES
(1, 'El Toro', 'Luisa Hernandez', '9511235623'),
(4, 'La soledad', 'Juan Rodolfo Perez', '9511234568'),
(5, 'Coca Cola', 'Geronimo Perez Juarez', '523698751523');

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

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`UsuarioID`, `Nombre`, `Correo`, `Pass`) VALUES
(6, 'prueba1', 'prueba1@gmail.com', '$2y$10$/ajZZRE2QlQUvP2u5raIzeJAkPI/WqBC8NY4c8ilEYc8pHZs.C.Eu'),
(7, 'admin', 'admin@gmail.com', '$2y$10$naXUdIvebYUcIqbD2/0HeuMMsSiJHqO/PVvLHZNzq.m0R4LwIC4v.');

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
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`VentasID`, `Fecha`, `Total`, `PagoCliente`, `Cambio`, `UsuarioID`) VALUES
(9, '2025-11-20', 106, 150, 44, 7),
(10, '2025-11-20', 58, 100, 42, 7),
(11, '2025-11-20', 25, 30, 5, 7),
(12, '2025-11-20', 33, 50, 17, 7);

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
  MODIFY `CategoriaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cortecaja`
--
ALTER TABLE `cortecaja`
  MODIFY `CorteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `entradasmercancia`
--
ALTER TABLE `entradasmercancia`
  MODIFY `EntradaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `ProductoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `ProveedorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `UsuarioID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `VentasID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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

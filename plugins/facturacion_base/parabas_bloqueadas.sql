-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 18-12-2018 a las 08:38:31
-- Versión del servidor: 5.6.39-cll-lve
-- Versión de PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `antojitos_z9`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parabas_bloqueadas`
--

CREATE TABLE `parabas_bloqueadas` (
  `indice` int(20) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `palabra` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `parabas_bloqueadas`
--

INSERT INTO `parabas_bloqueadas` (`indice`, `usuario`, `palabra`) VALUES
(2, 'DVEGAS', 'asds'),
(3, 'DVEGAS', 'ASDASD'),
(4, 'DVEGAS', 'DARWIN'),
(5, 'DVEGAS', 'aaaaaa'),
(6, 'DVEGAS', 'aaaaaa'),
(7, 'DVEGAS', 'aaaaaa'),
(8, 'DVEGAS', 'asdasd'),
(9, 'DVEGAS', 'asdasd');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `parabas_bloqueadas`
--
ALTER TABLE `parabas_bloqueadas`
  ADD PRIMARY KEY (`indice`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `parabas_bloqueadas`
--
ALTER TABLE `parabas_bloqueadas`
  MODIFY `indice` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

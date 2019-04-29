-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 18-12-2018 a las 08:38:59
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
-- Estructura de tabla para la tabla `sugerencias_proveedor`
--

CREATE TABLE `sugerencias_proveedor` (
  `usuario` varchar(30) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `palabra` varchar(30) NOT NULL,
  `palabra_sugerida` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sugerencias_proveedor`
--

INSERT INTO `sugerencias_proveedor` (`usuario`, `tipo`, `palabra`, `palabra_sugerida`) VALUES
('DVEGAS', 'PRODUCTOS', '5050', '11111'),
('DVEGAS', 'PROVEEDOR', 'REFRESCD', 'adsasd'),
('DVEGAS', 'PRODUCTOS', 'aaaaa', '11111111111111111111111111111'),
('DVEGAS', 'PRODUCTOS', 'aaaaa', '11111111111111111111111111111'),
('DVEGAS', 'PRODUCTOS', 'aaaaa', 'sadasdasdasda'),
('DVEGAS', 'PRODUCTOS', 'asda', 'asdasd'),
('DVEGAS', 'PRODUCTOS', 'sadasd', '11'),
('DVEGAS', 'PRODUCTOS', 'sadasd', '11'),
('DVEGAS', 'PRODUCTOS', 'asda', 'ddd'),
('DVEGAS', 'PRODUCTOS', 'adds', 'asd'),
('DVEGAS', 'PRODUCTOS', 'asdasd', '11111'),
('DVEGAS', 'PRODUCTOS', 'asdasd', '11111'),
('DVEGAS', 'PRODUCTOS', 'asdasd', '11111'),
('DVEGAS', 'PRODUCTOS', '5050', 'asdasd');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

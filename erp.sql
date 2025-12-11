-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 11-12-2025 a las 09:53:42
-- Versión del servidor: 10.6.23-MariaDB-cll-lve
-- Versión de PHP: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `erp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion`
--

CREATE TABLE `accion` (
  `id_accion` int(11) NOT NULL,
  `nom_accion` longtext NOT NULL,
  `est_accion` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `accion`
--

INSERT INTO `accion` (`id_accion`, `nom_accion`, `est_accion`) VALUES
(1, 'Ver', 1),
(2, 'Crear', 1),
(3, 'Editar', 1),
(4, 'Importar', 1),
(5, 'Anular', 1),
(6, 'Aprobar', 1),
(7, 'Verificar', 1),
(8, 'Recepcionar', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen`
--

CREATE TABLE `almacen` (
  `id_almacen` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_obra` int(11) DEFAULT NULL,
  `nom_almacen` longtext DEFAULT NULL,
  `est_almacen` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `almacen`
--

INSERT INTO `almacen` (`id_almacen`, `id_cliente`, `id_obra`, `nom_almacen`, `est_almacen`) VALUES
(1, 9, NULL, 'BASE ARCE', 1),
(2, 9, 6, 'CHANCAY ARCE', 1),
(3, 3, 6, 'CHANCAY LUZ DEL SUR', 1),
(4, 9, 1, 'ANCON ARCE', 1),
(5, 2, 1, 'ANCON PLUZ', 1),
(7, 5, 2, 'BARSI APM', 1),
(8, 9, 2, 'BARSI ARCE', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area`
--

CREATE TABLE `area` (
  `id_area` int(11) NOT NULL,
  `nom_area` varchar(100) DEFAULT NULL,
  `act_area` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `area`
--

INSERT INTO `area` (`id_area`, `nom_area`, `act_area`, `updated_at`) VALUES
(1, 'PE2025', 1, '2020-01-01 00:00:00'),
(2, 'CIP0009 OBRAS SET GENERAL', 1, '2020-01-01 00:00:00'),
(3, 'CIP0003 HSE/SIG', 1, '2020-01-01 00:00:00'),
(4, 'INGENIERIA SET - LINEAS AT', 0, '2020-01-01 00:00:00'),
(5, 'MM2025', 1, '2020-01-01 00:00:00'),
(6, 'PRUEBAS Y ENSAYOS 1', 0, '2020-01-01 00:00:00'),
(7, 'RESPONSABLE DE CALIDAD Y CONTROL DE GESTION', 0, '2020-01-01 00:00:00'),
(8, 'CIP0001 COSTOS INTERNOS', 1, '2020-01-01 00:00:00'),
(9, 'OFICINA TECNICA', 0, '2020-01-01 00:00:00'),
(10, 'JG3', 0, '2020-01-01 00:00:00'),
(11, 'INTESEL', 0, '2020-01-01 00:00:00'),
(12, 'PRUEBAS Y ENSAYOS ', 0, '2020-01-01 00:00:00'),
(13, 'MANTENIMIENTO LINEAS', 0, '2020-01-01 00:00:00'),
(14, 'SISTEMA INTEGRADO GESTION', 0, '2020-01-01 00:00:00'),
(15, '666', 0, '2020-01-01 00:00:00'),
(16, 'GERENCIA GENERAL', 0, '2020-01-01 00:00:00'),
(17, '333', 0, '2020-01-01 00:00:00'),
(18, 'OBRAS LAT CHILLóN - OQUENDO', 1, '2020-01-01 00:00:00'),
(19, 'ADMINISTRACION', 0, '2020-01-01 00:00:00'),
(20, 'GERENCIA', 0, '2020-01-01 00:00:00'),
(21, 'NC2025', 1, '2020-01-01 00:00:00'),
(22, 'CIP0013 OFICINA TECNICA', 1, '2020-01-01 00:00:00'),
(23, 'OG 446', 0, '2020-01-01 00:00:00'),
(24, '555', 0, '2020-01-01 00:00:00'),
(25, 'HSE.', 0, '2020-01-01 00:00:00'),
(26, 'SALUD OCUPACIONAL', 0, '2020-01-01 00:00:00'),
(27, 'OBRAS DISTRIBUCIÓN', 0, '2020-01-01 00:00:00'),
(28, 'ALUMBRADO PUBLICO', 0, '2020-01-01 00:00:00'),
(29, 'OBRAS Y MANTENIMIENTO SED', 0, '2020-01-01 00:00:00'),
(30, 'CIP0002 GRÚAS Y CAMIONES', 1, '2020-01-01 00:00:00'),
(31, 'LDS2025002 LINEA PACHACUTEC - UNACEM', 1, '2020-01-01 00:00:00'),
(32, 'CIP0005 COMPRAS Y ALMACEN', 1, '2020-01-01 00:00:00'),
(33, 'CIP0010 ADMINISTRACION', 1, '2020-01-01 00:00:00'),
(34, 'LDSTM2025 TRABAJOS MENORES EN SET', 1, '2020-01-01 00:00:00'),
(35, 'LDS2025001 SET SAN BARTOLO - ACTIV. COMPLEMENTARIAS', 1, '2020-01-01 00:00:00'),
(36, 'LDS2025003 LINEA CANTERA - SAN VICENTE', 1, '2020-01-01 00:00:00'),
(37, 'INGLDS2025 INGENIERIA', 1, '2020-01-01 00:00:00'),
(38, 'LDSSET202501 SET LURIN - OBRAS CIVILES DE TR RESERVA', 1, '2020-01-01 00:00:00'),
(39, 'LDSCIV25002 LINEA PACHACUTEC - CIVELMEC', 1, '2020-01-01 00:00:00'),
(40, 'TRABAJOS CON TERCEROS', 1, '2020-01-01 00:00:00'),
(41, 'CIP004 TALLER', 1, '2020-01-01 00:00:00'),
(42, 'PLUZMMAT25', 1, '2020-01-01 00:00:00'),
(43, 'LDS2025004 - SET ÑAÑA AMPLIACIÓN OOCC', 1, '2020-01-01 00:00:00'),
(44, 'SET MARIATEGUI - PLUZ', 1, '2020-01-01 00:00:00'),
(45, 'PLUZ202501', 1, '2020-01-01 00:00:00'),
(46, 'PLUZRYD25', 1, '2020-01-01 00:00:00'),
(47, 'PLUZMT25', 1, '2020-01-01 00:00:00'),
(48, 'PLUZMT25 - OBRAS SET TRABAJOS DE MEDIA TENSION', 1, '2020-01-01 00:00:00'),
(49, 'PL2503 SET CAUDIVILLA ', 1, '2025-12-02 11:06:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id_auditoria` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nom_usuario` varchar(255) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `modulo` varchar(100) NOT NULL,
  `descripcion` longtext DEFAULT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id_auditoria`, `id_usuario`, `nom_usuario`, `accion`, `modulo`, `descripcion`, `fecha`) VALUES
(1, 5, 'ROBINSON YAÑEZ', 'INICIO DE SESIÓN', 'SESIÓN', 'ROBINSON YAÑEZ', '2025-12-11 11:53:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banco`
--

CREATE TABLE `banco` (
  `id_banco` int(11) NOT NULL,
  `cod_banco` varchar(30) NOT NULL,
  `nom_banco` varchar(100) NOT NULL,
  `est_banco` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `banco`
--

INSERT INTO `banco` (`id_banco`, `cod_banco`, `nom_banco`, `est_banco`) VALUES
(1, 'IBK', 'INTERBANK', 1),
(2, 'BBVA', 'BANCO CONTINENTAL BBVA', 1),
(3, 'BCP', 'BANCO CREDITO DEL PERU', 1),
(4, 'BN', 'BANCO NACION', 1),
(5, 'CTA RECAUDADORA', 'CTA RECAUDADORA', 1),
(6, 'SCBK', 'SCOTIABANK', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo`
--

CREATE TABLE `cargo` (
  `id_cargo` int(11) NOT NULL,
  `nom_cargo` varchar(100) DEFAULT NULL,
  `act_cargo` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `cargo`
--

INSERT INTO `cargo` (`id_cargo`, `nom_cargo`, `act_cargo`, `updated_at`) VALUES
(1, 'OPERARIO', 1, '2020-01-01 00:00:00'),
(2, 'CHOFER', 1, '2020-01-01 00:00:00'),
(3, 'AYUDANTE', 1, '2020-01-01 00:00:00'),
(4, 'SUPERVISOR DE MANTENIMIENTO DE SET', 1, '2020-01-01 00:00:00'),
(5, 'SUPERVISOR LINEA', 1, '2020-01-01 00:00:00'),
(7, 'ENCARGADO', 1, '2020-01-01 00:00:00'),
(8, 'SUPERVISOR COORDINADOR CAMION GRUA', 1, '2020-01-01 00:00:00'),
(9, 'ASISTENTE', 1, '2020-01-01 00:00:00'),
(10, 'INGENIERO PRUEBAS', 1, '2020-01-01 00:00:00'),
(11, 'CHOFER OPERARIO', 1, '2020-01-01 00:00:00'),
(12, 'COORDINADOR OPERATIVO', 1, '2020-01-01 00:00:00'),
(13, 'INGENIERO CIVIL', 1, '2020-01-01 00:00:00'),
(14, 'SUPERVISOR SET\'S', 1, '2020-01-01 00:00:00'),
(15, 'OPERARIO ELECTRICISTA', 1, '2020-01-01 00:00:00'),
(16, 'SUPERVISOR OFICINA TECNICA', 1, '2020-01-01 00:00:00'),
(17, 'RESIDENTE CIVIL', 1, '2020-01-01 00:00:00'),
(18, 'INGENIERO RESIDENTE', 1, '2020-01-01 00:00:00'),
(19, 'INGENIERO REISENTE', 1, '2020-01-01 00:00:00'),
(21, 'ALMACENERO', 1, '2020-01-01 00:00:00'),
(22, 'OPERARIO GRUA', 1, '2020-01-01 00:00:00'),
(23, 'ASISTENTE CALIDAD', 1, '2020-01-01 00:00:00'),
(24, 'RESPONSABLE DE MANTENIMIENTO', 1, '2020-01-01 00:00:00'),
(25, 'SIN CARGO', 1, '2020-01-01 00:00:00'),
(26, 'OPERARIO SOLDADOR', 1, '2020-01-01 00:00:00'),
(27, 'ASISTENTE LIQUIDADOR', 1, '2020-01-01 00:00:00'),
(28, 'CHOFER RETROEXCAVADORA', 1, '2020-01-01 00:00:00'),
(29, 'ASISTENTE ADMINISTRATIVO', 1, '2020-01-01 00:00:00'),
(30, 'COORDINADOR SIG', 1, '2020-01-01 00:00:00'),
(31, 'AYUDANTE CIVIL', 1, '2020-01-01 00:00:00'),
(32, 'OPERARIO CIVIL', 1, '2020-01-01 00:00:00'),
(33, 'OFICIAL CIVIL', 1, '2020-01-01 00:00:00'),
(34, 'GERENTE DE OPERACIONES', 1, '2020-01-01 00:00:00'),
(35, 'PEON', 1, '2020-01-01 00:00:00'),
(36, 'ASISTENTE RRHH', 1, '2020-01-01 00:00:00'),
(37, 'OPERARIO MECANICO', 1, '2020-01-01 00:00:00'),
(38, 'OPERADOR AUXILIAR', 1, '2020-01-01 00:00:00'),
(39, 'COORDINADOR COMPRAS', 1, '2020-01-01 00:00:00'),
(40, 'OPERARIO 1', 1, '2020-01-01 00:00:00'),
(41, 'SUPERVISOR CALIDAD', 1, '2020-01-01 00:00:00'),
(42, 'SUPERVISOR DE PRUEBAS Y ENSAYOS', 1, '2020-01-01 00:00:00'),
(43, 'SUPERVISOR CIVIL', 1, '2020-01-01 00:00:00'),
(44, 'COORDINADOR RRHH', 1, '2020-01-01 00:00:00'),
(45, 'SUPERVISOR ELECTRICO', 1, '2020-01-01 00:00:00'),
(46, 'COORDINADOR SISTEMAS', 1, '2020-01-01 00:00:00'),
(47, 'ALMACENEROO', 0, '2020-01-01 00:00:00'),
(48, 'ASISTENTE CONTABILIDAD', 1, '2020-01-01 00:00:00'),
(49, 'COORDINADOR CONTABILIDAD', 1, '2020-01-01 00:00:00'),
(50, 'TECNICO TESORERIA', 1, '2020-01-01 00:00:00'),
(51, 'ASISTENTE TECNICO', 1, '2020-01-01 00:00:00'),
(52, 'ALMACENERO COMPRAS', 1, '2020-01-01 00:00:00'),
(53, 'ASISTENTE GENERAL', 1, '2020-01-01 00:00:00'),
(54, 'OPERARIO MAQUINARIAS', 1, '2020-01-01 00:00:00'),
(55, 'ASISTENTE COMPRAS', 1, '2020-01-01 00:00:00'),
(56, 'TOPOGRAFO ', 1, '2020-01-01 00:00:00'),
(57, 'ASISTENTE LIMPIEZA', 1, '2020-01-01 00:00:00'),
(58, 'MEDICO OCUPACIONAL', 1, '2020-01-01 00:00:00'),
(59, 'ENFERMERA OCUPACIONAL', 1, '2020-01-01 00:00:00'),
(60, 'OPERADOR DE GRÚA', 1, '2020-01-01 00:00:00'),
(63, 'OPERARIO ELECTROMECANICO', 1, '2020-01-01 00:00:00'),
(64, 'OPERADOR DE MAQUINARIA', 1, '2020-01-01 00:00:00'),
(65, 'ADMINISTRADOR DE OBRA', 1, '2020-01-01 00:00:00'),
(66, 'SUPERVISOR MLAT', 1, '2020-01-01 00:00:00'),
(67, 'OPERADOR DE EXCAVADORA', 1, '2020-01-01 00:00:00'),
(68, 'OPERADOR DE VOLQUETE', 1, '2020-01-01 00:00:00'),
(69, 'OPERADOR DE MINICARGADOR', 1, '2020-01-01 00:00:00'),
(70, 'OPERADOR DE RETROEXCAVADORA', 1, '2020-01-01 00:00:00'),
(71, 'CHOFER DE CAMIÓN', 1, '2020-01-01 00:00:00'),
(72, 'SUPERVISOR HSE', 1, '2020-01-01 00:00:00'),
(73, 'PRACTICANTE HSE', 1, '2020-01-01 00:00:00'),
(74, 'CAPATAZ', 1, '2020-01-01 00:00:00'),
(75, 'RIGGER', 1, '2020-01-01 00:00:00'),
(76, 'MONTAJISTA', 1, '2020-01-01 00:00:00'),
(77, 'PSICOLOGA OCUPACIONAL', 1, '2020-01-01 00:00:00'),
(78, 'RESPONSABLE', 1, '2020-01-01 00:00:00'),
(79, 'TECNICO ELECTRICISTA', 1, '2020-01-01 00:00:00'),
(80, 'JEFE HSE', 1, '2020-01-01 00:00:00'),
(81, 'PROYECTISTA', 1, '2020-01-01 00:00:00'),
(82, 'JEFE OBRAS SET', 1, '2020-01-01 00:00:00'),
(83, 'GERENTE GENERAL', 1, '2020-01-01 00:00:00'),
(84, 'JEFE DE CUADRILLA', 1, '2020-01-01 00:00:00'),
(85, 'OPERARIO LINIERO', 1, '2020-01-01 00:00:00'),
(86, 'LIQUIDADOR', 1, '2020-01-01 00:00:00'),
(87, 'PALETERA', 1, '2020-01-01 00:00:00'),
(88, 'OPERADOR PRINCIPAL', 1, '2020-01-01 00:00:00'),
(89, 'PLANNER MANTENIMIENTO', 1, '2020-01-01 00:00:00'),
(90, 'CHOFER AYUDANTE', 1, '2020-01-01 00:00:00'),
(91, 'RESIDENTE ELECTROMECÁNICO', 1, '2020-01-01 00:00:00'),
(92, 'ASISTENTE DE OBRA', 1, '2020-01-01 00:00:00'),
(93, 'JEFE HSEQ', 1, '2020-01-01 00:00:00'),
(94, 'ASISTENTE DE OPERACIONES', 1, '2020-01-01 00:00:00'),
(95, 'COORDINADOR HSE', 1, '2020-01-01 00:00:00'),
(96, 'JEFE OBRAS LINEAS', 1, '2020-01-01 00:00:00'),
(97, 'OPERARIO LIDER', 1, '2020-01-01 00:00:00'),
(98, 'SUPERVISOR OOEE', 1, '2020-01-01 00:00:00'),
(99, 'VIGIA', 1, '2020-01-01 00:00:00'),
(100, 'RESIDENTE LINEAS', 1, '2020-01-01 00:00:00'),
(101, 'JEFE DE ALMACÉN CENTRAL', 1, '2025-12-01 12:14:47'),
(102, 'JEFE DE RRHH', 1, '2020-01-01 00:00:00'),
(103, 'ASISTENTE HSE', 1, '2020-01-01 00:00:00'),
(104, 'SUPERVISOR SIG', 1, '2020-01-01 00:00:00'),
(105, 'INGENIERO DE SISTEMAS', 1, '2020-01-01 00:00:00'),
(110, 'JEFE DE COMPRAS', 1, '2025-12-01 11:58:55'),
(111, 'ASISTENTE DE COMPRAS', 1, '2025-12-01 11:59:14'),
(112, 'JEFE DE ALMACEN OBRAS', 1, '2025-12-01 12:15:02'),
(1000001, 'ALMACENERO21', 0, '2025-12-10 09:14:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nom_cliente` varchar(100) NOT NULL,
  `act_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nom_cliente`, `act_cliente`) VALUES
(1, 'G&C', 0),
(2, 'PLUZ', 1),
(3, 'LUZ DEL SUR', 1),
(4, 'STATKRAFT', 1),
(5, 'APM TERMINALS', 1),
(6, 'COBRA', 0),
(7, 'X RETAIL', 1),
(8, 'PLUZ GENERACION', 1),
(9, 'ARCE', 1),
(10, 'L2 DEL METRO DE LIMA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `id_compra` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL COMMENT 'creo la compra',
  `id_personal_aprueba` int(11) DEFAULT NULL COMMENT 'aprobó la compra',
  `obs_compra` longtext DEFAULT NULL COMMENT 'obsevaciones',
  `denv_compra` longtext DEFAULT NULL COMMENT 'direccion de envío',
  `plaz_compra` int(11) DEFAULT NULL,
  `port_compra` longtext DEFAULT NULL COMMENT 'Portes',
  `id_detraccion` int(11) DEFAULT NULL,
  `id_retencion` int(11) DEFAULT NULL,
  `id_percepcion` int(11) DEFAULT NULL,
  `fec_compra` date NOT NULL,
  `est_compra` int(11) DEFAULT NULL,
  `id_personal_aprueba_financiera` int(11) DEFAULT NULL COMMENT 'Aprobación financiera',
  `fecha_reg_compra` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_detalle`
--

CREATE TABLE `compra_detalle` (
  `id_compra_detalle` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_pedido_detalle` int(11) DEFAULT NULL,
  `id_producto` int(11) NOT NULL,
  `cant_compra_detalle` float(10,2) DEFAULT NULL,
  `prec_compra_detalle` float(10,2) DEFAULT NULL,
  `igv_compra_detalle` decimal(10,2) DEFAULT 0.00,
  `hom_compra_detalle` longtext DEFAULT NULL,
  `est_compra_detalle` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante`
--

CREATE TABLE `comprobante` (
  `id_comprobante` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_tipo_documento` int(11) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `monto_total_igv` decimal(10,2) NOT NULL,
  `id_detraccion` int(11) DEFAULT NULL,
  `total_pagar` decimal(10,2) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `id_medio_pago` int(11) NOT NULL,
  `archivo_pdf` varchar(255) NOT NULL,
  `archivo_xml` varchar(255) DEFAULT NULL,
  `est_comprobante` tinyint(1) DEFAULT 1,
  `fec_registro` datetime DEFAULT current_timestamp(),
  `fec_pago` datetime DEFAULT NULL,
  `id_personal` int(11) NOT NULL,
  `id_cuenta_proveedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante_pago`
--

CREATE TABLE `comprobante_pago` (
  `id_comprobante_pago` int(11) NOT NULL,
  `id_comprobante` int(11) NOT NULL,
  `id_personal_registra` int(11) NOT NULL,
  `fec_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `fec_pago` date NOT NULL,
  `vou_comprobante_pago` varchar(255) NOT NULL,
  `fg_comprobante_pago` tinyint(1) NOT NULL COMMENT '1=monto, 2=impuesto',
  `est_comprobante_pago` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=activo, 0=anulado'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detraccion`
--

CREATE TABLE `detraccion` (
  `id_detraccion` int(11) NOT NULL,
  `nombre_detraccion` varchar(100) NOT NULL,
  `cod_detraccion` varchar(50) NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  `id_detraccion_tipo` int(11) NOT NULL DEFAULT 1,
  `est_detraccion` int(11) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detraccion`
--

INSERT INTO `detraccion` (`id_detraccion`, `nombre_detraccion`, `cod_detraccion`, `porcentaje`, `id_detraccion_tipo`, `est_detraccion`) VALUES
(1, 'MADERA', '', 4.00, 1, 1),
(2, 'ARENA Y PIEDRA', '', 10.00, 1, 1),
(3, 'INTERMEDIACION LABORAL Y TERCERIZACION', '', 12.00, 1, 1),
(4, 'ARENDAMIENTO / ALQUILERES', '', 10.00, 1, 1),
(5, 'MANTENIMIENTO Y REPARACION', '', 10.00, 1, 1),
(6, 'OTROS SERVICIOS EMPRESARIALES', '', 12.00, 1, 1),
(7, 'MOVIMIENTO DE CARGA', '', 4.00, 1, 1),
(8, 'FABRICACION DE BIENES POR ENCARGO', '', 10.00, 1, 1),
(9, 'TRANSPORTE DE PERSONAL', '', 10.00, 1, 1),
(10, 'CONTRATOS DE CONSTRUCCION / OBRAS CIVILES', '', 4.00, 1, 1),
(11, 'DEMAS SERVICIOS GRAVADOS CON EL IGV', '', 12.00, 1, 1),
(12, 'RETENCION', '', 3.00, 2, 1),
(13, 'PERCEPCION', '', 2.00, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detraccion_tipo`
--

CREATE TABLE `detraccion_tipo` (
  `id_detraccion_tipo` int(11) NOT NULL,
  `nom_detraccion_tipo` varchar(50) NOT NULL,
  `est_detraccion_tipo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detraccion_tipo`
--

INSERT INTO `detraccion_tipo` (`id_detraccion_tipo`, `nom_detraccion_tipo`, `est_detraccion_tipo`) VALUES
(1, 'DETRACCION', 1),
(2, 'RETENCION', 1),
(3, 'PERCEPCION', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucion`
--

CREATE TABLE `devolucion` (
  `id_devolucion` int(11) NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL,
  `id_cliente_destino` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL COMMENT 'personal que registra',
  `obs_devolucion` longtext NOT NULL COMMENT 'observaciones',
  `fec_devolucion` datetime NOT NULL COMMENT 'fecha y hora de registro',
  `est_devolucion` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucion_detalle`
--

CREATE TABLE `devolucion_detalle` (
  `id_devolucion_detalle` int(11) NOT NULL,
  `id_devolucion` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cant_devolucion_detalle` float(10,2) NOT NULL,
  `det_devolucion_detalle` longtext DEFAULT NULL COMMENT 'detalle',
  `est_devolucion_detalle` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id_doc` int(11) NOT NULL,
  `entidad` varchar(50) NOT NULL,
  `id_entidad` int(11) NOT NULL,
  `documento` varchar(255) NOT NULL,
  `fec_subida` timestamp NULL DEFAULT current_timestamp(),
  `id_personal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso`
--

CREATE TABLE `ingreso` (
  `id_ingreso` int(11) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `id_almacen` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL COMMENT 'personal que hace el ingreso',
  `fec_ingreso` datetime DEFAULT NULL,
  `fpag_ingreso` date DEFAULT NULL COMMENT 'fecha de pago de la compra',
  `est_ingreso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_detalle`
--

CREATE TABLE `ingreso_detalle` (
  `id_ingreso_detalle` int(11) NOT NULL,
  `id_ingreso` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cant_ingreso_detalle` float(10,2) DEFAULT NULL,
  `est_ingreso_detalle` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material_tipo`
--

CREATE TABLE `material_tipo` (
  `id_material_tipo` int(11) NOT NULL,
  `nom_material_tipo` longtext DEFAULT NULL,
  `est_material_tipo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `material_tipo`
--

INSERT INTO `material_tipo` (`id_material_tipo`, `nom_material_tipo`, `est_material_tipo`) VALUES
(1, 'NA', 1),
(2, 'CONSUMIBLES', 1),
(3, 'HERRAMIENTAS', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medio_pago`
--

CREATE TABLE `medio_pago` (
  `id_medio_pago` int(11) NOT NULL,
  `nom_medio_pago` varchar(100) NOT NULL,
  `est_medio_pago` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medio_pago`
--

INSERT INTO `medio_pago` (`id_medio_pago`, `nom_medio_pago`, `est_medio_pago`) VALUES
(1, 'Cheque', 1),
(2, 'Depósito', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo`
--

CREATE TABLE `modulo` (
  `id_modulo` int(11) NOT NULL,
  `nom_modulo` longtext NOT NULL,
  `est_modulo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `modulo`
--

INSERT INTO `modulo` (`id_modulo`, `nom_modulo`, `est_modulo`) VALUES
(1, 'Dashboard', 1),
(2, 'Uso de Material', 1),
(3, 'Pedidos', 1),
(4, 'Compras', 1),
(5, 'Ingresos', 1),
(6, 'Devoluciones', 1),
(7, 'Almacen Arce', 1),
(8, 'Almacen Clientes', 1),
(9, 'Personal', 1),
(10, 'Usuarios', 1),
(11, 'Modulos', 1),
(12, 'Rol de Usuario', 1),
(13, 'Area', 1),
(14, 'Cargo', 1),
(15, 'Cliente', 1),
(16, 'Obras', 1),
(17, 'Almacen', 1),
(18, 'Ubicacion', 1),
(19, 'Producto', 1),
(20, 'Tipo de Producto', 1),
(21, 'Tipo de Material', 1),
(22, 'Unidad de Medida', 1),
(23, 'Proveedor', 1),
(24, 'Moneda', 1),
(25, 'Auditoria', 1),
(26, 'Salidas', 1),
(27, 'Movimientos', 1),
(28, 'Detraccion', 1),
(29, 'Centro de Costo', 1),
(30, 'Banco', 1),
(31, 'Tipo de Documento', 1),
(32, 'Medio de Pago', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo_accion`
--

CREATE TABLE `modulo_accion` (
  `id_modulo_accion` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_accion` int(11) NOT NULL,
  `est_modulo_accion` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `modulo_accion`
--

INSERT INTO `modulo_accion` (`id_modulo_accion`, `id_modulo`, `id_accion`, `est_modulo_accion`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1),
(3, 2, 2, 1),
(4, 2, 3, 1),
(5, 3, 1, 1),
(6, 3, 2, 1),
(7, 3, 3, 1),
(8, 3, 5, 1),
(9, 3, 6, 1),
(10, 3, 7, 1),
(11, 4, 1, 1),
(12, 4, 2, 1),
(13, 4, 3, 1),
(14, 4, 5, 1),
(15, 4, 6, 1),
(16, 5, 1, 1),
(17, 5, 2, 1),
(18, 5, 3, 1),
(19, 6, 1, 1),
(20, 6, 2, 1),
(21, 6, 3, 1),
(22, 7, 1, 1),
(23, 7, 2, 1),
(24, 7, 3, 1),
(25, 8, 1, 1),
(26, 8, 2, 1),
(27, 8, 3, 1),
(28, 9, 1, 1),
(29, 9, 2, 1),
(30, 9, 3, 1),
(31, 10, 1, 1),
(32, 10, 2, 1),
(33, 10, 3, 1),
(34, 11, 1, 1),
(35, 11, 2, 1),
(36, 11, 3, 1),
(37, 12, 1, 1),
(38, 12, 2, 1),
(39, 12, 3, 1),
(40, 13, 1, 1),
(41, 13, 2, 1),
(42, 13, 3, 1),
(43, 14, 1, 1),
(44, 14, 2, 1),
(45, 14, 3, 1),
(46, 15, 1, 1),
(47, 15, 2, 1),
(48, 15, 3, 1),
(49, 16, 1, 1),
(50, 16, 2, 1),
(51, 16, 3, 1),
(52, 17, 1, 1),
(53, 17, 2, 1),
(54, 17, 3, 1),
(55, 18, 1, 1),
(56, 18, 2, 1),
(57, 18, 3, 1),
(58, 19, 1, 1),
(59, 19, 2, 1),
(60, 19, 3, 1),
(61, 20, 1, 1),
(62, 20, 2, 1),
(63, 20, 3, 1),
(64, 21, 1, 1),
(65, 21, 2, 1),
(66, 21, 3, 1),
(67, 22, 1, 1),
(68, 22, 2, 1),
(69, 22, 3, 1),
(70, 23, 1, 1),
(71, 23, 2, 1),
(72, 23, 3, 1),
(73, 23, 4, 1),
(74, 24, 1, 1),
(75, 24, 2, 1),
(76, 24, 3, 1),
(77, 25, 1, 1),
(78, 26, 1, 1),
(79, 26, 2, 1),
(80, 26, 3, 1),
(81, 26, 5, 1),
(82, 26, 6, 1),
(83, 26, 8, 1),
(84, 27, 1, 1),
(85, 28, 1, 1),
(86, 28, 2, 1),
(87, 28, 3, 1),
(88, 29, 1, 1),
(89, 29, 2, 1),
(90, 29, 3, 1),
(91, 30, 1, 1),
(92, 30, 2, 1),
(93, 30, 3, 1),
(94, 31, 1, 1),
(95, 31, 2, 1),
(96, 31, 3, 1),
(97, 32, 1, 1),
(98, 32, 2, 1),
(99, 32, 3, 1),
(100, 2, 5, 1),
(101, 5, 5, 1),
(102, 6, 5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moneda`
--

CREATE TABLE `moneda` (
  `id_moneda` int(11) NOT NULL,
  `nom_moneda` longtext DEFAULT NULL,
  `est_moneda` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `moneda`
--

INSERT INTO `moneda` (`id_moneda`, `nom_moneda`, `est_moneda`) VALUES
(1, 'SOLES', 1),
(2, 'DOLARES', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento`
--

CREATE TABLE `movimiento` (
  `id_movimiento` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL,
  `id_orden` int(11) DEFAULT NULL COMMENT 'id de la orden',
  `id_producto` int(11) NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL,
  `tipo_orden` int(11) DEFAULT NULL COMMENT '1 = INGRESO (suma al stock).\r\n2 = SALIDA(TRASLADOS, movimiento de ubicación / 2 registros).\r\n3 = DEVOLUCION (resta al stock).\r\n4 = USO (resta al stock).\r\n',
  `tipo_movimiento` int(11) DEFAULT NULL COMMENT '1 = ingreso (suma al stock).\r\n2 = salida (resta al stock).',
  `cant_movimiento` float(10,2) DEFAULT NULL,
  `fec_movimiento` datetime DEFAULT NULL,
  `est_movimiento` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_proveedor_cuenta` int(11) NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `fec_pago` datetime DEFAULT current_timestamp(),
  `id_personal` int(11) NOT NULL,
  `enviar_correo` tinyint(1) DEFAULT 0,
  `est_pago` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL,
  `id_producto_tipo` int(11) NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL,
  `id_centro_costo` int(11) DEFAULT NULL,
  `id_personal` int(11) DEFAULT NULL COMMENT 'id del personal que solicita el pedido (el que registra)',
  `id_personal_verifica` int(11) DEFAULT NULL COMMENT 'personal que verifica',
  `cod_pedido` longtext DEFAULT NULL COMMENT 'codigo de obra + número de pedido',
  `nom_pedido` longtext DEFAULT NULL COMMENT 'nombre del pedido',
  `ot_pedido` longtext DEFAULT NULL COMMENT 'OT/LCL/LCA',
  `cel_pedido` varchar(45) DEFAULT NULL COMMENT 'Celular del contacto',
  `lug_pedido` varchar(45) DEFAULT NULL COMMENT 'Lugar de entrada del pedido',
  `acl_pedido` longtext DEFAULT NULL COMMENT 'Aclaraciones sobre la solicitud y entrega',
  `fec_req_pedido` date DEFAULT NULL COMMENT 'fecha de requerimiento',
  `fec_pedido` datetime DEFAULT NULL,
  `est_pedido` tinyint(1) NOT NULL COMMENT '0 = anulado, 1 = pendiente, 1 + id_personal_aprueba = aprobado, 2 = atendido',
  `id_obra` int(11) DEFAULT NULL,
  `id_personal_aprueba_tecnica` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalle`
--

CREATE TABLE `pedido_detalle` (
  `id_pedido_detalle` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `prod_pedido_detalle` longtext DEFAULT NULL COMMENT 'nombre del producto',
  `ot_pedido_detalle` varchar(100) DEFAULT NULL COMMENT 'OT/LCL/LCA específico',
  `cant_pedido_detalle` float(10,2) DEFAULT NULL COMMENT 'Cantidad real a pedido despues de descontar con lo que ya habia en almacén',
  `cant_oc_pedido_detalle` decimal(10,2) DEFAULT NULL,
  `cant_os_pedido_detalle` decimal(10,2) DEFAULT NULL,
  `com_pedido_detalle` longtext DEFAULT NULL COMMENT 'Comentarios y/o observaciones',
  `req_pedido` longtext DEFAULT NULL COMMENT 'Requisitos SST, MA, CA',
  `est_pedido_detalle` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalle_centro_costo`
--

CREATE TABLE `pedido_detalle_centro_costo` (
  `id_pedido_detalle_centro_costo` int(11) NOT NULL,
  `id_pedido_detalle` int(11) NOT NULL,
  `id_centro_costo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalle_documento`
--

CREATE TABLE `pedido_detalle_documento` (
  `id_pedido_detalle_documento` int(11) NOT NULL,
  `id_pedido_detalle` int(11) NOT NULL,
  `nom_pedido_detalle_documento` longtext DEFAULT NULL,
  `est_pedido_detalle_documento` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalle_personal`
--

CREATE TABLE `pedido_detalle_personal` (
  `id_pedido_detalle_personal` int(11) NOT NULL,
  `id_pedido_detalle` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `id_permiso` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_modulo_accion` int(11) NOT NULL,
  `est_permiso` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`id_permiso`, `id_rol`, `id_modulo_accion`, `est_permiso`) VALUES
(611, 1, 33, 1),
(610, 1, 32, 1),
(609, 1, 31, 1),
(608, 1, 4, 1),
(607, 1, 3, 1),
(606, 1, 2, 1),
(605, 1, 69, 1),
(604, 1, 68, 1),
(603, 1, 67, 1),
(602, 1, 57, 1),
(601, 1, 56, 1),
(600, 1, 55, 1),
(599, 1, 63, 1),
(598, 1, 62, 1),
(597, 1, 61, 1),
(596, 1, 66, 1),
(595, 1, 65, 1),
(594, 1, 64, 1),
(593, 1, 96, 1),
(592, 1, 95, 1),
(591, 1, 94, 1),
(590, 1, 83, 1),
(589, 1, 82, 1),
(588, 1, 81, 1),
(587, 1, 80, 1),
(586, 1, 79, 1),
(585, 1, 78, 1),
(584, 1, 39, 1),
(583, 1, 38, 1),
(582, 1, 37, 1),
(581, 1, 73, 1),
(580, 1, 72, 1),
(579, 1, 71, 1),
(578, 1, 70, 1),
(577, 1, 60, 1),
(576, 1, 59, 1),
(575, 1, 58, 1),
(574, 1, 30, 1),
(573, 1, 29, 1),
(572, 1, 28, 1),
(571, 1, 10, 1),
(570, 1, 9, 1),
(569, 1, 8, 1),
(568, 1, 7, 1),
(567, 1, 6, 1),
(566, 1, 5, 1),
(565, 1, 51, 1),
(564, 1, 50, 1),
(563, 1, 49, 1),
(562, 1, 84, 1),
(561, 1, 76, 1),
(560, 1, 75, 1),
(559, 1, 74, 1),
(558, 1, 36, 1),
(557, 1, 35, 1),
(556, 1, 34, 1),
(555, 1, 99, 1),
(554, 1, 98, 1),
(553, 1, 97, 1),
(552, 1, 18, 1),
(551, 1, 17, 1),
(550, 1, 16, 1),
(549, 1, 21, 1),
(548, 1, 20, 1),
(547, 1, 19, 1),
(546, 1, 87, 1),
(545, 1, 86, 1),
(544, 1, 85, 1),
(543, 1, 1, 1),
(542, 1, 15, 1),
(541, 1, 14, 1),
(540, 1, 13, 1),
(539, 1, 12, 1),
(538, 1, 11, 1),
(537, 1, 48, 1),
(536, 1, 47, 1),
(535, 1, 46, 1),
(534, 1, 90, 1),
(533, 1, 89, 1),
(532, 1, 88, 1),
(531, 1, 45, 1),
(530, 1, 44, 1),
(529, 1, 43, 1),
(528, 1, 93, 1),
(527, 1, 92, 1),
(526, 1, 91, 1),
(525, 1, 77, 1),
(524, 1, 42, 1),
(523, 1, 41, 1),
(522, 1, 40, 1),
(521, 1, 27, 1),
(520, 1, 26, 1),
(519, 1, 25, 1),
(518, 1, 24, 1),
(517, 1, 23, 1),
(516, 1, 22, 1),
(515, 1, 54, 1),
(514, 1, 53, 1),
(513, 1, 52, 1),
(707, 1, 100, 1),
(850, 2, 31, 1),
(849, 2, 4, 1),
(848, 2, 3, 1),
(847, 2, 2, 1),
(846, 2, 69, 1),
(845, 2, 68, 1),
(844, 2, 67, 1),
(843, 2, 57, 1),
(842, 2, 56, 1),
(841, 2, 55, 1),
(840, 2, 63, 1),
(839, 2, 62, 1),
(838, 2, 61, 1),
(837, 2, 66, 1),
(836, 2, 65, 1),
(835, 2, 64, 1),
(834, 2, 96, 1),
(833, 2, 95, 1),
(832, 2, 94, 1),
(831, 2, 83, 1),
(830, 2, 82, 1),
(829, 2, 81, 1),
(828, 2, 80, 1),
(827, 2, 79, 1),
(826, 2, 78, 1),
(825, 2, 39, 1),
(824, 2, 38, 1),
(823, 2, 37, 1),
(822, 2, 72, 1),
(821, 2, 71, 1),
(820, 2, 70, 1),
(819, 2, 60, 1),
(818, 2, 59, 1),
(817, 2, 58, 1),
(816, 2, 30, 1),
(815, 2, 29, 1),
(814, 2, 28, 1),
(813, 2, 10, 1),
(812, 2, 9, 1),
(811, 2, 8, 1),
(810, 2, 7, 1),
(809, 2, 6, 1),
(808, 2, 5, 1),
(807, 2, 51, 1),
(806, 2, 50, 1),
(805, 2, 49, 1),
(804, 2, 84, 1),
(803, 2, 76, 1),
(802, 2, 75, 1),
(801, 2, 74, 1),
(800, 2, 99, 1),
(799, 2, 98, 1),
(798, 2, 97, 1),
(797, 2, 101, 1),
(796, 2, 18, 1),
(795, 2, 17, 1),
(794, 2, 16, 1),
(793, 2, 102, 1),
(792, 2, 21, 1),
(791, 2, 20, 1),
(790, 2, 19, 1),
(789, 2, 87, 1),
(788, 2, 86, 1),
(787, 2, 85, 1),
(786, 2, 1, 1),
(785, 2, 15, 1),
(784, 2, 14, 1),
(783, 2, 13, 1),
(782, 2, 12, 1),
(781, 2, 11, 1),
(780, 2, 48, 1),
(779, 2, 47, 1),
(778, 2, 46, 1),
(777, 2, 90, 1),
(776, 2, 89, 1),
(775, 2, 88, 1),
(774, 2, 45, 1),
(773, 2, 44, 1),
(772, 2, 43, 1),
(771, 2, 93, 1),
(770, 2, 92, 1),
(769, 2, 91, 1),
(768, 2, 77, 1),
(767, 2, 42, 1),
(766, 2, 41, 1),
(765, 2, 40, 1),
(764, 2, 27, 1),
(763, 2, 26, 1),
(762, 2, 25, 1),
(761, 2, 24, 1),
(760, 2, 23, 1),
(759, 2, 22, 1),
(758, 2, 54, 1),
(757, 2, 53, 1),
(851, 2, 32, 1),
(755, 3, 72, 1),
(754, 3, 71, 1),
(753, 3, 70, 1),
(752, 3, 76, 1),
(751, 3, 75, 1),
(750, 3, 74, 1),
(749, 3, 99, 1),
(748, 3, 98, 1),
(747, 3, 97, 1),
(746, 3, 87, 1),
(745, 3, 86, 1),
(744, 3, 85, 1),
(743, 3, 15, 1),
(742, 3, 14, 1),
(741, 3, 13, 1),
(740, 3, 12, 1),
(739, 3, 11, 1),
(738, 3, 90, 1),
(737, 3, 89, 1),
(736, 3, 88, 1),
(735, 3, 93, 1),
(734, 3, 92, 1),
(733, 3, 91, 1),
(756, 2, 52, 1),
(708, 1, 101, 1),
(709, 1, 102, 1),
(852, 2, 33, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `id_personal` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `nom_personal` longtext DEFAULT NULL,
  `dni_personal` varchar(8) DEFAULT NULL,
  `cel_personal` varchar(9) DEFAULT NULL,
  `email_personal` longtext NOT NULL,
  `pass_personal` varchar(50) NOT NULL,
  `act_personal` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`id_personal`, `id_cargo`, `id_area`, `id_tipo`, `nom_personal`, `dni_personal`, `cel_personal`, `email_personal`, `pass_personal`, `act_personal`, `updated_at`) VALUES
(1, 1, 13, 2, 'ACOSTA CANO JUAN', '48233813', '938963691', 'juantlvrac90@gmail.com', '48233813', 1, '2020-01-01 00:00:00'),
(2, 2, 18, 2, 'ADRIANO AGUILAR SERGIO ALFREDO', '47795110', '981306740', 'sadrianoaguilar@gmail.com', '47795110', 1, '2020-01-01 00:00:00'),
(3, 3, 2, 2, 'AGUILAR QUISPE URIEL ALCIDES', '71920296', '', '', '71920296', 1, '2020-01-01 00:00:00'),
(4, 4, 3, 2, 'AGUIRRE CAYCHO GIANNINA', '72838314', '982669729', 'gaguirre@grupoinversionesgyc.pe', '72838314', 0, '2020-01-01 00:00:00'),
(5, 4, 2, 3, 'ALAMO CACHAY NELSON', '6568279', '941856017', 'nalamo@grupoinversionesgyc.pe', '6568279', 1, '2020-01-01 00:00:00'),
(6, 1, 13, 2, 'ALIAGA DELGADO JHONNY PERCY', '45690878', '968570180', 'jhonny_elgenial5@hotmail.com', '45690878', 1, '2020-01-01 00:00:00'),
(7, 37, 2, 2, 'ALIAGA ROBLES ENRIQUE MANUEL', '25818652', '993381548', 'eneiquealiagaroblea@hotmail.com', '25818652', 1, '2020-01-01 00:00:00'),
(8, 5, 4, 2, 'ALVARADO ALZAMORA MAURO MIGUEL', '7677523', '1', 'a@w', '7677523', 0, '2020-01-01 00:00:00'),
(9, 1, 5, 2, 'ANICAMA DIAZ JORDAN', '48258863', '', '', '48258863', 1, '2020-01-01 00:00:00'),
(10, 1, 13, 2, 'ARAUCO BARRANZUELO ALEJANDRO', '46299036', '968603862', 'alejandroaraucobarranzuela@gmail.com', '46299036', 1, '2020-01-01 00:00:00'),
(11, 1, 12, 2, 'ARCE GOMEZ ERNESTO', '43978478', '941795092', 'michaelarcegomez@gmail.com', '43978478', 1, '2020-01-01 00:00:00'),
(12, 15, 2, 2, 'AREVALO CUNGAL CARLOS DAVID', '46909197', '943404903', 'carlos0291arevalo@gmail.com', '46909197', 1, '2020-01-01 00:00:00'),
(13, 37, 2, 2, 'ARIAS PALOMINO ROBERTO', '09808653', '955686217', 'betoariasp@hotmail.com', '09808653', 1, '2020-01-01 00:00:00'),
(14, 4, 5, 3, 'ARROYO YATACO FREDDY', '08527784', '945367400', 'farroyo@grupoinversionesgyc.com', '08527784', 1, '2020-01-01 00:00:00'),
(15, 12, 8, 3, 'ASTUDILLO RIOS NICK', '77059686', '986943593', 'nastudillo@grupoinversionesgyc.pe', '77059686', 1, '2020-01-01 00:00:00'),
(16, 42, 1, 3, 'AVILA CABRA GROWER', '08014499', '985511578', 'gavila@arceperu.pe', '08014499', 1, '2020-01-01 00:00:00'),
(17, 84, 28, 3, 'AVILA CHANCASANAY SERGIO', '20556957', '993997375', 'sergioavilachancasanay@gmail.com', '20556957', 1, '2020-01-01 00:00:00'),
(18, 1, 18, 2, 'AVILES MANRIQUE OSCAR', '10504226', '967171800', 'oscaraviles082@gmail.com', '10504226', 1, '2020-01-01 00:00:00'),
(19, 1, 2, 2, 'AVILES ROJAS RAMON', '43260600', '920215892', 'rojasaviles9@gimail. com', '43260600', 1, '2020-01-01 00:00:00'),
(20, 100, 31, 3, 'AYALA HUARAHUACHI JOSE GERMAN', '73980722', '930453758', 'jayalajose1407@gmail.com', '73980722', 1, '2020-01-01 00:00:00'),
(21, 1, 12, 2, 'BALDEON DAVILA PEDRO', '74533922', '966151406', 'baldeondavila@hotmail.com', '74533922', 1, '2020-01-01 00:00:00'),
(22, 38, 21, 2, 'BALDEON LAVERIANO GILMER', '16017242', '945647612', 'gimerbaldeon@hotmail.com', '16017242', 1, '2020-01-01 00:00:00'),
(23, 8, 30, 2, 'BAÑES FLORES WILDER', '07869619', '941855691', 'wbanesflores@gmail.com', '07869619', 1, '2020-01-01 00:00:00'),
(24, 72, 3, 3, 'BARBARAN PARIONA JIMMY', '45349018', '942427668', 'jbarbaran@arceperu.pe', '45349018', 1, '2020-01-01 00:00:00'),
(25, 4, 2, 3, 'BAUTISTA GONZALES ANTONY', '73025315', '1', 'abautista.grupoinversionesgyc@gmail.com', '73025315', 1, '2020-01-01 00:00:00'),
(26, 12, 1, 3, 'BLAS CONDORI LUIS', '09454559', '946072433', 'lblas@grupoinversionesgyc.pe', '09454559', 1, '2020-01-01 00:00:00'),
(27, 11, 4, 2, 'BLAS SOLORZANO HECTOR PEDRO', '16005321', '1', 'a@w', '16005321', 0, '2020-01-01 00:00:00'),
(28, 4, 3, 1, 'BOCANGEL BELTRAN LESLY', '72074469', '943012190', 'lbocangel@grupoinversionesgyc.pe', '72074469', 1, '2020-01-01 00:00:00'),
(29, 1, 5, 2, 'BOZA CACERES JOSE', '16005872', '934968400', 'jboza194@gmail.com', '16005872', 1, '2020-01-01 00:00:00'),
(30, 2, 18, 2, 'BUENO REYES SANDRO MIGUEL', '43518140', '978149988', 'sandro.mbr.27@gmail.com', '43518140', 1, '2020-01-01 00:00:00'),
(31, 4, 12, 3, 'CABALLERO QUISPE PABEL', '42427100', '941869182', 'pcaballero@grupoinversionesgyc.pe', '42427100', 1, '2020-01-01 00:00:00'),
(32, 4, 3, 4, 'CABRERA HERNANDEZ JOSE', '47074960', '934550323', 'Jicahez.06@gmail.com', '47074960', 1, '2020-01-01 00:00:00'),
(33, 2, 27, 2, 'CALDERON MENESES OSCAR ALEJANDRO', '47098131', '951830519', 'oscarcalderonmeneses@gmail.com', '47098131', 1, '2020-01-01 00:00:00'),
(34, 4, 5, 3, 'CALLE YANGUA WILSON', '09602880', '941855864', 'wcallegyc@gmail.com', '09602880', 1, '2020-01-01 00:00:00'),
(35, 1, 2, 2, 'CALLUPE ESPINOZA HOBER', '20891784', '930780715', 'hobercallupeespinoza@gmail.com', '20891784', 1, '2020-01-01 00:00:00'),
(36, 1, 13, 2, 'CALVAY HUAMAN LUIS ARMANDO', '76544486', '961837102', 'milove1994.lach@gmail.com', '76544486', 1, '2020-01-01 00:00:00'),
(37, 17, 2, 3, 'CAMPODONICO ALCANTARA THALIA', '72531751', '975224598', 'tcampodonico@arceperu.pe', '72531751', 1, '2020-01-01 00:00:00'),
(38, 11, 13, 2, 'CAÑOLI COLLANTES ARNOLD', '76396560', '922257035', 'jhairc20@hotmail.com', '76396560', 1, '2020-01-01 00:00:00'),
(39, 2, 13, 2, 'CAÑOLI COLLANTES JORGE', '43138906', '989368835', 'jorgec_2030@hotmail.com', '43138906', 1, '2020-01-01 00:00:00'),
(40, 1, 12, 2, 'CAPILLO BACILIO JUAN CARLOS', '47269149', '944966479', 'jcapillo.bacilio@gmail.com', '47269149', 1, '2020-01-01 00:00:00'),
(41, 1, 5, 2, 'CASAS SEGURA FERNANDO JULES', '16120491', '920857971', 'fernandocasas971@gmai', '16120491', 1, '2020-01-01 00:00:00'),
(42, 11, 18, 2, 'CCACSA ANGELES JUNIOR ALBERTO', '71778723', '939806592', 'juniorcca1993@gmail.com', '71778723', 1, '2020-01-01 00:00:00'),
(43, 1, 13, 2, 'CCELLCCARO ACUÑA ROBERTO AGUSTO', '42501989', '951782447', 'roberto_cc2@hotmail.com', '42501989', 1, '2020-01-01 00:00:00'),
(44, 37, 2, 2, 'CESPEDES LIBERATO ALEX CARLOS', '42163747', '926848774', 'cykcespedes@gmail.com', '42163747', 1, '2020-01-01 00:00:00'),
(45, 72, 2, 3, 'CHANDUVI RUIZ ALDO', '46173323', '920275292', 'achanduvi@arceperu.pe', '46173323', 1, '2020-01-01 00:00:00'),
(46, 1, 12, 2, 'CHAUCA VALVERDE JOSE ANTONIO', '10691012', '981230030', 'jose_chauca_2306@hotmail.com', '10691012', 1, '2020-01-01 00:00:00'),
(47, 4, 2, 3, 'CHAVEZ GARRIAZO RAUL', '7328580', '937474167', 'rchavez@grupoinversionesgyc', '7328580', 1, '2020-01-01 00:00:00'),
(48, 78, 18, 3, 'CHERO ROJAS JOSE', '15447079', '1', 'jchero@grupoinversionesgyc.pe', '15447079', 1, '2020-01-01 00:00:00'),
(49, 4, 3, 1, 'CHOQUEHUAYTA GUERRA LEONELA', '44652614', '966488793', 'lchoquehuayta@grupoinversionesgyc.pe', '44652614', 1, '2020-01-01 00:00:00'),
(50, 88, 21, 3, 'CHUJANDAMA AREVALO DEYSON', '41452639', '989201745', 'deyson031115@gmail.com', '41452639', 1, '2020-01-01 00:00:00'),
(51, 17, 31, 3, 'CIELO MARINA PABLO', '41331148', '941855616', 'pcielo@grupoinversionesgyc.pe', '41331148', 1, '2020-01-01 00:00:00'),
(52, 2, 2, 2, 'CLAUDIO NU&Ntilde;EZ MANUEL GUSTAVO', '4521957', '', '', '4521957', 1, '2020-01-01 00:00:00'),
(53, 15, 5, 2, 'COLONIA SOLIS ISAIAS CASHIN', '48164054', '915929891', 'Issacol81@gmail.com', '48164054', 1, '2020-01-01 00:00:00'),
(54, 14, 5, 3, 'CONTRERAS ROSALES IVAN', '42254964', '923777594', 'ivancr010184@gmail.com', '42254964', 1, '2020-01-01 00:00:00'),
(55, 1, 5, 2, 'CORDOVA MATSUOKA WALTER MARIO', '15850619', '993498165', 'wcordova_1708@hotmail', '15850619', 1, '2020-01-01 00:00:00'),
(56, 1, 18, 2, 'COTRINA RAMIREZ REYNALDO', '40236301', '900667926', 'cotrinajaime123@gmail.com', '40236301', 1, '2020-01-01 00:00:00'),
(57, 82, 2, 3, 'CRISOSTOMO BARRIOS MIGUEL', '21887791', '941855491', 'mcrisostomo@grupoinversionesgyc.pe', '21887791', 1, '2020-01-01 00:00:00'),
(58, 4, 12, 3, 'CRUZ TORREJON RONNY', '41251951', '984395004', 'rcruz@grupoinversionesgyc.pe', '41251951', 1, '2020-01-01 00:00:00'),
(59, 37, 5, 2, 'CUADROS SUANEZ JORGE', '10726946', '960885566', 'cuadrosjorge@772gmail.comp', '10726946', 1, '2020-01-01 00:00:00'),
(60, 32, 31, 2, 'CUCHO OCHOA CARLOS CRISTINO', '25796692', '975476405', 'carlos18yei@gmail.com', '25796692', 1, '2020-01-01 00:00:00'),
(61, 11, 18, 2, 'CUELLAR RIOS JUAN', '47176373', '995378027', 'Jp.cuellar@oultook.com', '47176373', 1, '2020-01-01 00:00:00'),
(62, 4, 13, 3, 'CUEVA ESPINOZA JORGE', '45212411', '953700768', 'jcueva@grupoinversionesgyc.pe', '45212411', 1, '2020-01-01 00:00:00'),
(63, 85, 31, 2, 'CUNYAS GABRIEL FREDY', '10876047', '980301104', 'fredye.c.g.2121@gmail.com', '10876047', 1, '2020-01-01 00:00:00'),
(64, 1, 13, 2, 'CUNYAS MEJIA EDGAR ROBERTO', '10245602', '990945497', 'cunyasmejiaedgar@gmail.com', '10245602', 1, '2020-01-01 00:00:00'),
(65, 74, 31, 2, 'CUNYAS UNSIHUAY LEONARDO', '08323627', '992623273', 'a@w', '08323627', 1, '2020-01-01 00:00:00'),
(66, 16, 9, 2, 'CURILLO ARONI JUSTO LUIS', '70604963', '950424965', 'jcurillo@grupoinversionesgyc.pe', '70604963', 0, '2020-01-01 00:00:00'),
(67, 4, 27, 3, 'CURILLO CCANTO WILMAR', '44396602', '945693073', 'wcurillo@grupoinversionesgyc.pe', '44396602', 1, '2020-01-01 00:00:00'),
(68, 43, 2, 3, 'DELGADO GOMEZ WILMER', '41933172', '990876356', 'wdelgado@grupoinversionesgyc.pe', '41933172', 1, '2020-01-01 00:00:00'),
(69, 1, 1, 2, 'DELGADO PONCE CARLOS ALBERTO', '25422500', '992975466', 'carlosdelgado_2009@hotmail.com', '25422500', 1, '2020-01-01 00:00:00'),
(70, 88, 21, 3, 'DIAZ DIAZ YURI MIJAIL', '40719007', '993864727', 'elecktro.38@gmail.com', '40719007', 1, '2020-01-01 00:00:00'),
(71, 4, 13, 4, 'DONAYRE MIRANDA JUAN DIEGO', '43779991', '900752301', 'ddonayre@grupoinversionesgyc.pe', '43779991', 1, '2020-01-01 00:00:00'),
(72, 4, 5, 3, 'ENEQUE APAESTEGUI JOSE', '09945734', '941855997', 'jeneque@grupoinversionesgyc.pe', '09945734', 1, '2020-01-01 00:00:00'),
(73, 88, 21, 3, 'ESCALANTE PASTRANA RAUL MILCO', '15750291', '928311269', 'rescalantep_1221_@outlook.com', '15750291', 1, '2020-01-01 00:00:00'),
(74, 1, 5, 2, 'ESCALANTE SANCHEZ RICHARD', '16003186', '980316956', 'rescalante8@gmail.comTe', '16003186', 1, '2020-01-01 00:00:00'),
(75, 91, 2, 3, 'ESPINOZA TORRES LENIN FELIX', '08653585', '966206943', 'lespinoza_1603@hotmail.com', '08653585', 1, '2020-01-01 00:00:00'),
(76, 1, 2, 2, 'ESPINOZA VALDIVIESO JERSON AUGUSTO', '73173167', '989420173', 'aries_jerson10@outlook.com', '73173167', 1, '2020-01-01 00:00:00'),
(77, 18, 2, 4, 'ESPIRITU FLORES MIGUEL ANGEL', '8881972', '951016250', 'mespiritu@grupoinversionesgyc.pe', '8881972', 1, '2020-01-01 00:00:00'),
(78, 18, 2, 3, 'FABIAN QUISPE GIOVANNI', '41560682', '1', 'gfabian@grupoinversionesgyc.pe', '41560682', 1, '2020-01-01 00:00:00'),
(79, 1, 2, 2, 'FERREYRA GARCIA MARIO EDGAR', '54117799', '1', 'a@w', '5411779', 0, '2020-01-01 00:00:00'),
(80, 4, 2, 4, 'FLORES CORONEL ABRAHAM', '47035753', '959177876', 'aflores@grupoinversionesgyc.pe', '47035753', 1, '2020-01-01 00:00:00'),
(81, 4, 18, 3, 'GALIANO RAMOS JHON BARNABY', '22222222', '988232872', 'jgalianogyc@gmail.com', '41412649', 0, '2020-01-01 00:00:00'),
(82, 18, 27, 3, 'GARAY REYNOSO JEFFERSON', '40550629', '941855476', 'jgaray@grupoinversionesgyc.pe', '40550629', 1, '2020-01-01 00:00:00'),
(83, 18, 2, 2, 'GARCIA ARANA ROLANDO', '25409348', '1', 'rgarcia@grupoinversionesgyc.pe', '25409348', 1, '2020-01-01 00:00:00'),
(84, 1, 2, 2, 'GARCIA CASTA&Ntilde;EDA DALVER BRIEDE', '75242122', '917318581', 'dalvergarcia1997@gmail.com', '75242122', 1, '2020-01-01 00:00:00'),
(85, 63, 2, 2, 'GARCIA LUNA UDOLFO RENE', '41194800', '999224040', 'renegarcialuna@gmail.com', '41194800', 1, '2020-01-01 00:00:00'),
(86, 7, 18, 2, 'GAVILANO RAMIREZ ROBERT ANTONIO', '9838952', '975583184', 'liniero22@hotmail.com', '9838952', 0, '2020-01-01 00:00:00'),
(87, 4, 3, 3, 'GONZALES ALVAREZ SANDRA JULISSA', '45579905', '1', 'sgonzales@grupoinversionesgyc.pe', '45579905', 0, '2020-01-01 00:00:00'),
(88, 11, 18, 2, 'GONZALES FLORES JOSE', '42653968', '959959943', 'gonzalesfloresjosenoe@gmail.com', '42653968', 1, '2020-01-01 00:00:00'),
(89, 89, 5, 2, 'GONZALO VALDERRAMA HUBEL', '74292593', '992086811', 'gonzalovalderramah@gmail.comom', '74292593', 1, '2020-01-01 00:00:00'),
(90, 37, 5, 2, 'GRANADOS PAREDES VICENTE RAUL', '09979386', '901203435', 'vicentito1828@gmail.com', '09979386', 1, '2020-01-01 00:00:00'),
(91, 21, 18, 2, 'GRANADOS TOLEDO MARCO ANTONIO', '06269169', '946344848', 'granadostoledoa@hotmail.com', '06269169', 1, '2020-01-01 00:00:00'),
(92, 15, 5, 2, 'GUERRA CARREÑO BONY', '41131287', '966586299', 'orlangc40@gmail.com', '41131287', 1, '2020-01-01 00:00:00'),
(93, 1, 13, 2, 'GUPIO VELA CARLOS SEGUNDO', '42421787', '910225585', '1carlossegundo16@gmail.com', '42421787', 1, '2020-01-01 00:00:00'),
(94, 1, 2, 2, 'GUTIERREZ GRADOS KEVIN', '62138152', '910745922', 'kj594101@gmail.com', '62138152', 1, '2020-01-01 00:00:00'),
(95, 1, 2, 2, 'GUTIERREZ PERALTA GARY', '10633295', '999143985', 'obras.set@grupoinversionesgyc.pe', '10633295', 1, '2020-01-01 00:00:00'),
(96, 1, 1, 2, 'GUTIERREZ TUESTA ALEX', '44613314', '1', 'a@w', '44613314', 0, '2020-01-01 00:00:00'),
(97, 4, 18, 4, 'GUTIERREZ YACTAYO JORGE', '08398135', '923799860', 'jluis14gutierrez@gmail.com', '08398135', 1, '2020-01-01 00:00:00'),
(98, 1, 32, 2, 'HERNANDEZ GONZALEZ SERGIO', '00718974', '1', 'A@W', '718974', 1, '2020-01-01 00:00:00'),
(99, 1, 2, 2, 'HINOSTROZA GARAMENDI ELIAS', '09399606', '992453388', 'ehinostroza0523@gmail.com', '09399606', 1, '2020-01-01 00:00:00'),
(100, 72, 3, 3, 'HOYOS PORTAL EINER', '44728755', '956164975', 'ehoyos@arceperu.pe', '44728755', 1, '2020-01-01 00:00:00'),
(101, 37, 5, 2, 'HUAMAN PERALTA PABLO', '41638334', '963316607', 'huamanperaltap@gmail.com', '41638334', 1, '2020-01-01 00:00:00'),
(102, 4, 2, 3, 'HUARIPAUCAR RUIZ ANIBAL', '41324022', '950112504', 'ahuaripaucar@grupoinversionesgyc.pe', '41324022', 1, '2020-01-01 00:00:00'),
(103, 4, 5, 2, 'HUAYANAY SANCHEZ MIGUEL ORLANDO', '9638953', '', 'mhuayanay@grupoinversionesgyc.pe', '9638953', 1, '2020-01-01 00:00:00'),
(104, 85, 31, 2, 'IBALA SANCHEZ OSCAR', '22274991', '956717014', 'o_ibala@hotmail.com', '22274991', 1, '2020-01-01 00:00:00'),
(105, 94, 1, 1, 'INOCENTE SEGURA MARCO ANTONIO', '71481515', '947499123', 'marco.is.7148@gmail.com', '71481515', 1, '2020-01-01 00:00:00'),
(106, 60, 30, 2, 'JACINTO PAUCARMAYTA JORGE', '10231343', '942432713', 'ljacinto.gyc@gmail.com', '10231343', 1, '2020-01-01 00:00:00'),
(107, 43, 18, 2, 'JARA CASTILLEJO LUIS', '46928872', '983515586', 'ljara@grupoinversionesgyc.pe', '46928872', 0, '2020-01-01 00:00:00'),
(108, 74, 31, 2, 'JARA PANTOJA ENNRIQUE', '15764809', '955486814', 'enrique.jara1961@gmail.con', '15764809', 1, '2020-01-01 00:00:00'),
(109, 37, 5, 2, 'JIMENEZ DURAND JUAN', '03500199', '943298100', 'juan-nico33@hotmail.com', '03500199', 1, '2020-01-01 00:00:00'),
(110, 21, 7, 2, 'JUNCO MATA GERARDO ALEJANDRO', '16012541', '', '', '16012541', 1, '2020-01-01 00:00:00'),
(111, 41, 31, 3, 'LIZANA GONZALES LUZ VANESSA', '73644058', '946769701', 'lizanagonzalesluzvanessa@gmail.com', '73644058', 1, '2020-01-01 00:00:00'),
(112, 1, 18, 2, 'LLIMPE CORNEJO FORTUNATO', '80410368', '986919570', 'nayelyromanni69@gmail.com', '80410368', 1, '2020-01-01 00:00:00'),
(113, 1, 5, 2, 'LOLI WONG MARCO ANTONIO', '9460142', '953504531', 'loliwong2018@gmail.com', '9460142', 1, '2020-01-01 00:00:00'),
(114, 1, 18, 2, 'LUNA YUMBATO JEINER', '45800587', '924072631', 'yumbaleon@gmail.com', '45800587', 1, '2020-01-01 00:00:00'),
(115, 1, 2, 2, 'MACAHUACHI CHUJUTALLI ENDERSON', '46790350', '925895607', 'macahuachi2190@gmail.com', '46790350', 1, '2020-01-01 00:00:00'),
(116, 2, 12, 2, 'MAMANI CCORCCA IRBING RICHARD', '47831858', '984301084', 'irbingmanuelmamaniccorcca@gmail.com', '47831858', 1, '2020-01-01 00:00:00'),
(117, 78, 13, 2, 'MAMANI FLORES JUAN NESTOR', '10118920', '958424938', 'nmamani@grupoinversionesgyc.pe', '10118920', 0, '2020-01-01 00:00:00'),
(118, 85, 31, 2, 'MAMANI FLORES WALTER', '09661475', '945363681', 'mamaniwalter7@gmail.com', '09661475', 1, '2020-01-01 00:00:00'),
(119, 72, 3, 3, 'MAMANI SUAQUITA PEDRO', '30842551', '957433572', 'pmamani@arceperu.pe', '30842551', 1, '2020-01-01 00:00:00'),
(120, 12, 5, 3, 'MANRIQUE CAMPOMANES LUIS', '09311305', '946077437', 'lmanrique@arceperu.pe', '09311305', 1, '2020-01-01 00:00:00'),
(121, 4, 2, 3, 'MAQUI JUAREZ CESAR ANTONIO', '40162029', '987169207', 'cmaqui@grupoinversionesgyc.pe', '40162029', 1, '2020-01-01 00:00:00'),
(122, 1, 18, 2, 'MARIN TAIPE ERICK', '42220405', '928929204', 'gatuno.latin12@gmail.com', '42220405', 1, '2020-01-01 00:00:00'),
(123, 1, 5, 2, 'MARTINEZ ERAZO CARLOS EMILIO', '15727127', '943704533', 'carlos26martinez@hotmail.com', '15727127', 1, '2020-01-01 00:00:00'),
(124, 1, 5, 2, 'MAURY CASTRO LUIS RICARDO', '42558355', '930819180', 'luismaury63@gmail.com', '42558355', 1, '2020-01-01 00:00:00'),
(125, 4, 5, 3, 'MEDINA ADAMA ELVIS FELIX', '43627082', '940346813', 'emedina@arceperu.pe', '43627082', 1, '2020-01-01 00:00:00'),
(126, 4, 5, 3, 'MEDINA ADAMA ELMER', '21287530', '912561069', 'elmer_medina2@hotmail.com', '21287530', 1, '2020-01-01 00:00:00'),
(127, 1, 18, 2, 'MEJIDO MONTERO ELMER', '40715934', '950425854', 'mejido35@gmail.com', '40715934', 1, '2020-01-01 00:00:00'),
(128, 1, 5, 2, 'MEZA MORENO CESAR AUGUSTO', '7711912', '995149304', 'cesar_meza2011@hotail.com', '7711912', 1, '2020-01-01 00:00:00'),
(129, 84, 27, 3, 'MILLA CARRILLO JOEL JOSE', '10601326', '944529794', 'joelmilla23ari@gmail.com', '10601326', 1, '2020-01-01 00:00:00'),
(130, 2, 18, 2, 'MIRAVAL BERROSPI GERARDO', '47394800', '923710840', 'miravalberrospi@gmail.com', '47394800', 1, '2020-01-01 00:00:00'),
(131, 4, 3, 2, 'MOQUILLAZA PINO ANGEL', '21535153', '', 'amoquillaza@grupoinversionesgyc.pe', '21535153', 1, '2020-01-01 00:00:00'),
(132, 1, 1, 2, 'MORA ALVARADO VIC', '40583584', '935798470', 'mora_alvarado@hotmail.com', '40583584', 1, '2020-01-01 00:00:00'),
(133, 1, 13, 2, 'MORENO GAMBOA PERCY MARVIN', '43470175', '910776035', 'morenogamboapercy@gmail.com', '43470175', 1, '2020-01-01 00:00:00'),
(134, 82, 3, 1, 'NEYRA COLLAO WILBRANDT HENRRY', '9948580', '971449170', 'hneyra@grupoinversionesgyc.pe', '951221HNC', 1, '2020-01-01 00:00:00'),
(135, 1, 2, 2, 'NICOLAS DOMINGUEZ ARMANDO', '45323797', '953947035', 'armando_8_88@hotmail.com', '45323797', 1, '2020-01-01 00:00:00'),
(136, 1, 2, 2, 'NORIEGA PINEDO LUIS ENRIQUE', '41301208', '921864232', 'luisenoriegap@gmail.com', '41301208', 1, '2020-01-01 00:00:00'),
(137, 3, 2, 2, 'ODAR PAZ JONNY', '41847750', '968225397', 'Odarjgyc.@gmail.com', '41847750', 1, '2020-01-01 00:00:00'),
(138, 74, 31, 2, 'ONTORIA RODRIGUEZ DAVID', '00872388', '971345562', 'davidontoria77@gmail.com', '00872388', 1, '2020-01-01 00:00:00'),
(139, 42, 1, 3, 'ORBEZO UTIA ADERLING', '46084686', '968246021', 'aorbezo@arceperu.pe', '46084686', 1, '2020-01-01 00:00:00'),
(140, 4, 3, 2, 'ORTIZ TUMAYA LUIS', '44175206', '968998690', 'lortiz@grupoinversionesgyc.pe', '44175206', 0, '2020-01-01 00:00:00'),
(141, 63, 2, 2, 'OSORIO PLAS DENIS HEMBER', '42068702', '965702798', 'scorpiox_83@hotmail.com', '42068702', 1, '2020-01-01 00:00:00'),
(142, 4, 18, 3, 'PACCO LOAIZA JAVIER RAFAEL', '45102215', '942426863', 'jpacco@grupoinversionesgyc.pe', '45102215', 1, '2020-01-01 00:00:00'),
(143, 1, 2, 2, 'PACHECO GUERRERO JUAN', '25706215', '969997434', 'juanmartinpachecoguerrero', '25706215', 1, '2020-01-01 00:00:00'),
(144, 11, 5, 2, 'PACIFICO CHANG MIRKO PAUL', '46905254', '955019766', 'mirkolay1@gmail.com', '46905254', 1, '2020-01-01 00:00:00'),
(145, 1, 1, 2, 'PALACIN GOMEZ SAMUEL', '42426781', '1', 'A@W', '42426781', 0, '2020-01-01 00:00:00'),
(146, 11, 18, 2, 'PALOMINO BENDEZU JOSE LUIS', '46694633', '939695361', 'joseluispalominob@gmail.com', '46694633', 1, '2020-01-01 00:00:00'),
(147, 88, 21, 3, 'PARDO ALVINO LUIS ENRIQUE', '41387072', '914097457', 'luis.pardo0246@gmail.com', '41387072', 1, '2020-01-01 00:00:00'),
(148, 18, 18, 3, 'PAWLIKOWSKI ZAVALETA RICHARD DANNY', '25773505', '945693039', 'rpawlikowski@grupoinversionesgyc.pe', '25773505', 1, '2020-01-01 00:00:00'),
(149, 1, 5, 2, 'PE&Ntilde;A MOGOLLON JOSE ANGEL', '7318552', '', '', '7318552', 1, '2020-01-01 00:00:00'),
(150, 1, 18, 2, 'PERALTA HUAMAN ARTEMIO', '25786195', '945373119', 'germain_ar@hotmail.com', '25786195', 1, '2020-01-01 00:00:00'),
(151, 11, 2, 2, 'PEREZ RODRIGUEZ CESAR', '41584647', '972373085', 'Cesitar16082011@gmail.com', '41584647', 1, '2020-01-01 00:00:00'),
(152, 1, 13, 2, 'POCCORPACHI GONZALES ALBERTO', '43132582', '956265359', 'albertopoccorpachigonzales@gmail.com', '43132582', 1, '2020-01-01 00:00:00'),
(153, 1, 13, 3, 'QUINTO GUPIO DANIEL', '45720535', '997377023', 'danriu010590@gmail.com', '45720535', 1, '2020-01-01 00:00:00'),
(154, 1, 18, 2, 'QUINTO GUPIO JOEL', '40646038', '981297598', 'joeljota@hotmail.com', '40646038', 1, '2020-01-01 00:00:00'),
(155, 4, 2, 3, 'QUISPE AYALA HERMENIGILDO', '10244265', '941856111', 'hquispe@grupoinversionesgyc.pe', '10244265', 1, '2020-01-01 00:00:00'),
(156, 14, 2, 2, 'QUISPE TICLLA ELVIS JOEL', '45451149', '986924508', 'equispe@grupoinversionesgyc.pe', '45451149', 1, '2020-01-01 00:00:00'),
(157, 40, 2, 2, 'QUISPE ESPINO CARLOS ALBERTO', '42586473', '933050098', 'c.quispeespino.gyc@gmail.co', '42586473', 1, '2020-01-01 00:00:00'),
(158, 1, 12, 2, 'RAMIREZ TORRES JOSE DILMER', '74816219', '999223910', 'joseramireztorres98@gmail.com', '74816219', 1, '2020-01-01 00:00:00'),
(159, 4, 5, 3, 'REYES ABANTO CESAR', '08579699', '941855781', 'creyesagyc@gmail.com', '08579699', 1, '2020-01-01 00:00:00'),
(160, 37, 5, 2, 'REYES TRUJILLO VICTOR AUGUSTO', '46772715', '937550801', 'stefvick25@gmail.com', '46772715', 1, '2020-01-01 00:00:00'),
(161, 1, 12, 2, 'REYES VILLEGAS DANTE JOSE', '42993639', '940624540', 'dante007jose@hotmail.com', '42993639', 1, '2020-01-01 00:00:00'),
(162, 11, 2, 2, 'REYNOSO GONZALES ERNESTO ALEXANDER', '41193609', '949811651', 'ernestoarg123@hotmail.com', '41193609', 1, '2020-01-01 00:00:00'),
(163, 1, 2, 2, 'RICALDI CORDOVA DOMINGO', '21247005', '', '', '21247005', 1, '2020-01-01 00:00:00'),
(164, 1, 2, 2, 'RIOS ACU&Ntilde;A EMILIO GAVINO', '75868864', '', '', '75868864', 1, '2020-01-01 00:00:00'),
(165, 37, 5, 2, 'RIVERO JARAMILLO SANTIAGO', '09427662', '945515781', 'edwinylles@gmail.com', '09427662', 1, '2020-01-01 00:00:00'),
(166, 1, 12, 3, 'ROBLES NOA ELIZABETH', '40877036', '998025859', 'crobles@grupoinversionesgyc.pe', '40877036', 1, '2020-01-01 00:00:00'),
(167, 1, 2, 2, 'RODRIGUEZ AREVALO STEFANO JHULIANY', '72117010', '962308436', 'jjaarevalo92.sr@gmail.com', '72117010', 1, '2020-01-01 00:00:00'),
(168, 4, 2, 3, 'RODRIGUEZ VASQUEZ LITA ROSLIN', '47113253', '973 90213', 'lrodriguez@grupoinversionesgyc.pe', '47113253', 1, '2020-01-01 00:00:00'),
(169, 4, 5, 3, 'ROJAS ANGULO HARRY', '72700183', '981736998', 'hrojas@grupoinversionesgyc.pe', '72700183', 1, '2020-01-01 00:00:00'),
(170, 1, 12, 2, 'ROJAS ESPINOZA JUAN', '73043779', '970175417', 'juanrojasre9510@gmail.com', '73043779', 1, '2020-01-01 00:00:00'),
(171, 78, 22, 1, 'ROJAS MAYEKAWA KATHERINE YOLANDA', '70438969', '993124323', 'deyvischugnasmosqueira26@gmail.com', '70438969', 1, '2020-01-01 00:00:00'),
(172, 37, 5, 2, 'ROJAS MEDINA LUIS', '45617426', '11 19', 'Luisrojasmedina45@gmail.com', '45617426', 1, '2020-01-01 00:00:00'),
(173, 4, 2, 3, 'ROJAS MOLINA JHONNY', '41225693', '920556661', 'jrojas@grupoinversionesgyc.pe', '41225693', 1, '2020-01-01 00:00:00'),
(174, 1, 5, 2, 'ROJAS VICENTE FREDDY', '70421071', '921987008', 'capricornio45_14@hotmail.com', '70421071', 1, '2020-01-01 00:00:00'),
(175, 1, 2, 2, 'SALAZAR PADILLA PERCY', '80130345', '983467479', 'percy salazar padilla @hotmail com', '80130345', 1, '2020-01-01 00:00:00'),
(176, 1, 13, 2, 'SALOME BALTAZAR IVAN', '80039857', '960067634', 'salomejjunioor1978@gmail.c com', '80039857', 1, '2020-01-01 00:00:00'),
(177, 1, 2, 2, 'SANCHEZ HUAMANI CRISTHIAN', '44573733', '902850271', 'misterio _24_05@hotmail.com', '44573733', 1, '2020-01-01 00:00:00'),
(178, 26, 2, 2, 'SANCHEZ ORDO&Ntilde;EZ GERARDO', '16743737', '948595874', 'gerardoflorentino18@gimail.com', '16743737', 1, '2020-01-01 00:00:00'),
(179, 11, 13, 2, 'SANCHEZ QUISPE HUGOLINDO', '44822026', '952272156', 'lino_425@hotmail.com', '44822026', 1, '2020-01-01 00:00:00'),
(180, 60, 30, 3, 'SERRANO CHUNGA FIDEL', '06832858', '953227308', 'fserranochunga@gmail.com', '06832858', 1, '2020-01-01 00:00:00'),
(181, 1, 2, 2, 'SILVA QUINTANA LUIS ALBERTO', '47147100', '940656633', 'luisilva.siel@gmail.com', '47147100', 1, '2020-01-01 00:00:00'),
(182, 11, 5, 2, 'SOLANO CIEZA LUIS', '43970986', '962344106', 'luissolano_1@hotmail.com', '43970986', 1, '2020-01-01 00:00:00'),
(183, 27, 7, 2, 'SOLORZANO LOAYZA PEDRO', '9896068', '942430336', 'ysolorzano@gruxpoinversionesgyc.pe', '9896068', 1, '2020-01-01 00:00:00'),
(184, 37, 5, 2, 'SOLORZANO ZEVALLOS PABLO', '22735965', '962641493', 'moscha@gyc.com', '22735965', 1, '2020-01-01 00:00:00'),
(185, 4, 18, 3, 'SUBIA CARRILLO JUAN JOSE', '29713260', '946073173', 'jsubia@grupoinversionesgyc.pe', '29713260', 1, '2020-01-01 00:00:00'),
(186, 2, 18, 2, 'TAFUR CAJAVILCA ELVIS MANUEL', '44898599', '900152210', 'elvistafur88@gmail.com.pe', '44898599', 1, '2020-01-01 00:00:00'),
(187, 28, 18, 2, 'TANTALEAN HUAMAN FREDDY', '75954710', '954469606', 'tantaleanhuamanfredy75@gmail.com', '75954710', 1, '2020-01-01 00:00:00'),
(188, 4, 5, 3, 'TAPIA MENDOZA JUAN', '10880322', '941855889', 'jtme__18@hotmail.com', '10880322', 1, '2020-01-01 00:00:00'),
(189, 4, 2, 3, 'TARAZONA REYES IVAN DANIEL', '10748493', '988514060', 'itarazona@grupoinversionesg', '10748493', 1, '2020-01-01 00:00:00'),
(190, 1, 13, 2, 'TAYPE ENCISO LUIS FELIPE', '70304052', '922792287', 'jhas9133@gmail.com', '70304052', 1, '2020-01-01 00:00:00'),
(191, 12, 31, 3, 'TORRES QUISPE JOHN', '46231075', '990124060', 'jtorres@grupoinversionesgyc.pe', '46231075', 1, '2020-01-01 00:00:00'),
(192, 85, 27, 2, 'TORRES TARAZONA ALEX', '44091310', '913211694', 'torrestarazonaalex@gmail.com', '44091310', 1, '2020-01-01 00:00:00'),
(193, 11, 13, 2, 'TORRES TARAZONA TEODORO', '41447925', '935993453', 'Teodorojuantorrestarazona@gmail.com ', '41447925', 1, '2020-01-01 00:00:00'),
(194, 4, 1, 3, 'TRUEVAS CALLE SAMUEL', '42988154', '940413183', 'struevas@grupoinversionesgyc.pe', '42988154', 1, '2020-01-01 00:00:00'),
(195, 11, 2, 2, 'TUBILLA SANCHEZ FRANK GIORGIO', '42255170', '949872478', 'frank.tubilla.84@gmail.com', '42255170', 1, '2020-01-01 00:00:00'),
(196, 4, 3, 3, 'TURIPE RODRIGUEZ MANUEL ALEJANDRO', '1174855', '945693144', 'mturipe@grupoinversionesgyc.pe', '1174855', 0, '2020-01-01 00:00:00'),
(197, 29, 5, 3, 'VALENCIA VICENTE MILAGROS CORINA', '70103272', '976783395', 'mvalencia@grupoinversionesgyc.pe', '70103272', 1, '2020-01-01 00:00:00'),
(198, 93, 3, 1, 'VALENTIN SOLIS DENISSE', '42796248', '951427486', 'dvalentin@arceperu.pe', '42796248', 1, '2020-01-01 00:00:00'),
(199, 18, 2, 3, 'VALERA GARAY RICHARD', '72421061', '988666999', 'rvalera@grupoinversionesgyc.pe', '72421061', 1, '2020-01-01 00:00:00'),
(200, 1, 5, 2, 'VALVERDE CARRERA VICTOR ALBERTO', '15694266', '950921565', 'valverdev1970@gmail.com', '15694266', 1, '2020-01-01 00:00:00'),
(201, 4, 5, 3, 'VARGAS MANTILLA CONCEPCION', '42531261', '939320237', 'vargasmantillajuan7@gmail.com', '42531261', 1, '2020-01-01 00:00:00'),
(202, 1, 1, 2, 'VASQUEZ PAREDES JULIO ABEL', '40261954', '1', 'A@W', '40261954', 0, '2020-01-01 00:00:00'),
(203, 1, 13, 2, 'VASQUEZ SAVEDRA ROOSVELT', '41022019', '942921618', 'roosveltvasquez80@gmail.co', '41022019', 1, '2020-01-01 00:00:00'),
(204, 1, 13, 3, 'VASQUEZ SAVERO REYNALDO', '42587705', '944326283', 'reyvlady22@gmail.com', '42587705', 1, '2020-01-01 00:00:00'),
(205, 37, 2, 2, 'VELARDE RIOS MANUEL', '00094855', '943892197', 'm_velarde40@hotmail.com', '00094855', 1, '2020-01-01 00:00:00'),
(206, 1, 18, 2, 'VERA CORREA MARCO', '47307484', '991804536', 'marcos_061@hotmail.com', '47307484', 1, '2020-01-01 00:00:00'),
(207, 1, 13, 2, 'VICTORIO MEZA MARCELITO', '44061699', '933793636', 'marcelitovictorio1020@gmail.com', '44061699', 1, '2020-01-01 00:00:00'),
(208, 37, 5, 2, 'VICUÑA OYANGUREN FELIX', '80578194', '917454287', 'felixantoniovicunaoyangurensc@gmail.comcom', '80578194', 1, '2020-01-01 00:00:00'),
(209, 40, 2, 2, 'VIDAL PLAS YOVANNI ROBERT', '80302847', '938424899', 'yova@hotmail.es', '80302847', 1, '2020-01-01 00:00:00'),
(210, 1, 13, 2, 'VILCAPOMA RIQUEZ NICANOR', '16158092', '964527017', 'nicanor0eliezer@gmail.com', '16158092', 1, '2020-01-01 00:00:00'),
(211, 1, 5, 2, 'VILLALOBOS ARROYO OMAR', '42447745', '953306042', 'djomar84@hotmail.com', '42447745', 1, '2020-01-01 00:00:00'),
(212, 15, 5, 2, 'VILLEGAS RIVAS FRAN WILSON', '70550797', '939747138', 'frankvillegasrivas@gmail.com', '70550797', 1, '2020-01-01 00:00:00'),
(213, 3, 2, 2, 'YARLEQUE CHICOMA HERLIY JAVIER', '42267278', '943584154', 'javierchicoma19@gmail.com', '42267278', 1, '2020-01-01 00:00:00'),
(214, 88, 21, 3, 'YLLESCAS COBEÑAS EDWIN RAFAEL', '15297632', '957792270', 'edwinylles@gmail.com', '15297632', 1, '2020-01-01 00:00:00'),
(215, 88, 21, 3, 'YTO CALDERON EDGAR FABIAN', '15748231', '990695963', 'edgaryto1@hotmail.com', '15748231', 1, '2020-01-01 00:00:00'),
(216, 1, 2, 2, 'ZORRILLA ARELLANO YOVER', '73057812', '942680838', 'theison_aries19@hotmail.com', '73057812', 1, '2020-01-01 00:00:00'),
(217, 4, 5, 3, 'ZURITA BLANCO VICTOR', '40876605', '950559517', 'vzurita@grupoinversionesgyc.pe', '40876605', 1, '2020-01-01 00:00:00'),
(218, 32, 31, 2, 'AGUILAR NOVOA ANTHONY', '72152276', '939028313', 'anthonyaguilarnovoa@gmail.com', '72152276', 1, '2020-01-01 00:00:00'),
(219, 31, 2, 2, 'AGUILAR NOVOA JHON', '44912620', '978305191', 'ajhon4893@gmail.com', '44912620', 1, '2020-01-01 00:00:00'),
(220, 1, 18, 2, 'AMASIFUEN FALCON RUSBEL', '43622217', '925463430', 'rusbelpanda@gmail.com', '43622217', 1, '2020-01-01 00:00:00'),
(221, 31, 18, 2, 'AMAYA CABALLERO DANY', '43448212', '977869767', 'dgac1785@gmail.com', '43448212', 1, '2020-01-01 00:00:00'),
(222, 3, 31, 2, 'BALABARCA BAÑEZ JORGE', '46158722', '977195751', 'jorgebalabarca1968@gmail.com', '46158722', 1, '2020-01-01 00:00:00'),
(223, 32, 18, 2, 'BOZA PAUCAR FREDDY', '22508026', '914476867', 'fredyalbertoboza@gmai.com', '22508026', 1, '2020-01-01 00:00:00'),
(224, 32, 18, 2, 'BRAVO JARA EMILIO', '41762930', '947345414', 'bravojaraemilio@gmail.com', '41762930', 1, '2020-01-01 00:00:00'),
(225, 1, 18, 2, 'BRAVO JARA JAVIER', '41794076', '967250732', 'Lb9944701@gmail.com', '41794076', 1, '2020-01-01 00:00:00'),
(226, 32, 31, 2, 'BUENO HUAMANCHAQUI ANGEL RICARDO', '48443739', '935126465', 'angel1212bueno@hotmail.co', '48443739', 1, '2020-01-01 00:00:00'),
(227, 31, 18, 2, 'CABRERA VILLANUEVA JUAN', '43665455', '926841272', 'cabreravillanuevajuancarlos@gmail.com', '43665455', 1, '2020-01-01 00:00:00'),
(228, 31, 15, 2, 'CAYO VEGA JONATHAN', '47154139', '1', 'a@w', '47154139', 0, '2020-01-01 00:00:00'),
(229, 32, 18, 2, 'CHARA INCA FACUNDINO', '09656034', '982784317', 'chara1970.dino@gmail.com', '09656034', 1, '2020-01-01 00:00:00'),
(230, 32, 31, 2, 'CHIONG CARRERA WILLY EDUARDO', '47485730', '910640396', 'eduardochiongcarrera@gmail.com', '47485730', 1, '2020-01-01 00:00:00'),
(231, 32, 18, 2, 'CHUQUILIN DE LA CRUZ JAIME', '19210262', '958015389', 'Jaimechuquilin@gmail.com', '19210262', 1, '2020-01-01 00:00:00'),
(232, 32, 2, 2, 'CLAUDIO NUÑEZ WILDER', '40591775', '992649782', 'wclaudio683@gmail.com', '40591775', 1, '2020-01-01 00:00:00'),
(233, 31, 18, 2, 'CORONADO ZABALLA CANCIO', '28837243', '986811227', 'coronadozaballa@gmail', '28837243', 1, '2020-01-01 00:00:00'),
(234, 31, 18, 2, 'CRUZ CESPEDES AQUILINO', '8278387', '963341292', 'cruzcespedesaquilino28@com', '8278387', 0, '2020-01-01 00:00:00'),
(235, 31, 15, 2, 'DURAN MACHUCA EDISON', '73786074', '1', 'a@w', '73786074', 0, '2020-01-01 00:00:00'),
(236, 32, 31, 2, 'DURAN MARIN VICTOR', '47481379', '942200767', 'victorduranmarin1@gmail.com', '47481379', 1, '2020-01-01 00:00:00'),
(237, 31, 15, 2, 'EGOAVIL ROMERO GUILLERMO', '42723697', '1', 'a@w', '42723697', 0, '2020-01-01 00:00:00'),
(238, 31, 18, 2, 'ESCALANTE VASQUEZ JOAQUIN', '71438853', '921597769', 'joaquinernestoescalantevasquez@com.pe', '71438853', 1, '2020-01-01 00:00:00'),
(239, 32, 31, 2, 'ESCOBAR HIDALGO ARMANDO', '80432041', '925805369', 'armand.es@hotmail.com', '80432041', 1, '2020-01-01 00:00:00'),
(240, 31, 18, 2, 'FASANANDO MAZANETH FERNANDO', '1105932', '986026909', 'fernandosanandomaznet@gmail.com', '1105932', 1, '2020-01-01 00:00:00'),
(241, 97, 31, 2, 'GALLARDO CALDERON DIDIER', '44586821', '921747002', 'gallardodidier@gmali.com', '44586821', 1, '2020-01-01 00:00:00'),
(242, 31, 18, 2, 'GALVEZ VELASQUEZ ALAN', '43113192', '990724341', 'Galvezvelasquezalangabriel@gmail.com', '43113192', 1, '2020-01-01 00:00:00'),
(243, 31, 18, 2, 'GARCIA CORDOVA REYNER', '72418632', '940222405', 'gar.cor.20@hotmail.com', '72418632', 1, '2020-01-01 00:00:00'),
(244, 1, 18, 2, 'GARCIA HINOSTROZA JEAN', '78088295', '923100811', 'jeampier_dark_114@hotmail.com', '78088295', 1, '2020-01-01 00:00:00'),
(245, 32, 31, 2, 'GONZALES VASQUEZ SANTIAGO', '45846765', '950424012', 'ademargv1989@gmail.com', '45846765', 1, '2020-01-01 00:00:00'),
(246, 1, 31, 2, 'GUZMAN ROSALES GABRIEL LEON', '73504254', '978878649', 'leoguzmanrosales282@gmail.com', '73504254', 1, '2020-01-01 00:00:00'),
(247, 32, 31, 2, 'GUZMAN ROSALES JAIME', '45599411', '930763293', 'guzman@inversion', '45599411', 1, '2020-01-01 00:00:00'),
(248, 31, 2, 2, 'HIDALGO PILCO MILTON', '05206814', '936218784', 'a@w', '05206814', 1, '2020-01-01 00:00:00'),
(249, 31, 18, 2, 'HUACA ARMAS REYNALDO', '76155201', '967261736', 'reynaldoomarhuacaarmas@gmail.com', '76155201', 1, '2020-01-01 00:00:00'),
(250, 32, 31, 2, 'HUAYHUA PAYTAN ALFREDO', '40817842', '991623835', 'jhonny25al@hotmail.com', '40817842', 1, '2020-01-01 00:00:00'),
(251, 32, 31, 2, 'HUERTO CHUQUILIN JOHNNY', '48285186', '978073756', 'johnnyhuerto@gmail.com', '48285186', 1, '2020-01-01 00:00:00'),
(252, 32, 31, 2, 'HUMPIRI LAZARO ORLANDO', '46789196', '954126328', 'humpiriorlando110@gmail.com', '46789196', 1, '2020-01-01 00:00:00'),
(253, 32, 18, 2, 'JARA OCAÑA EVORCIO', '10433212', '959428991', 'evorciojara@gmail', '10433212', 1, '2020-01-01 00:00:00'),
(254, 32, 18, 2, 'JIMENEZ DURAND MIGUEL', '2848905', '942116240', 'bacedema@hormail.com', '2848905', 0, '2020-01-01 00:00:00'),
(255, 32, 31, 2, 'LINO SOLIS JOSE', '33334165', '930227731', 'joselito_6370@hotm36ail.com', '33334165', 1, '2020-01-01 00:00:00'),
(256, 31, 5, 2, 'MACAHUACHI CHUJUTALLI LEVI', '45719987', '974118459', 'lmacahuachichujutalli29@gmail.com', '45719987', 1, '2020-01-01 00:00:00'),
(257, 31, 15, 2, 'MACHUCA YAULINO GABRIEL', '45332142', '1', 'a@w', '45332142', 0, '2020-01-01 00:00:00'),
(258, 1, 31, 2, 'MARCELO NECIOSUP JUAN', '17440281', '900156946', 'jneciosupjuan9@gmail.com', '17440281', 1, '2020-01-01 00:00:00'),
(259, 32, 31, 2, 'MENDOZA PEZO PETER DANNY', '44065615', '954772581', 'petermendozap@gmail.com', '44065615', 1, '2020-01-01 00:00:00'),
(260, 32, 31, 2, 'MOGOLLON CAUSSO RICARDO', '45359197', '930409892', 'Ricardomogollon47@gmail.com', '45359197', 1, '2020-01-01 00:00:00'),
(261, 31, 2, 2, 'MONDRAGON ACUÑA AUSBERTO', '77692106', '938182993', 'aubertomondragon2@gmail.com', '77692106', 1, '2020-01-01 00:00:00'),
(262, 32, 2, 2, 'MONDRAGON ACUÑA JOSE', '43889345', '996332974', 'Jhair29@outlook.com', '43889345', 1, '2020-01-01 00:00:00'),
(263, 1, 2, 2, 'MONTALVAN TEMOCHE ORLANDO', '10152664', '989032354', 'Charly_celeste@hotmail.com', '10152664', 1, '2020-01-01 00:00:00'),
(264, 32, 31, 2, 'MORALES AMAYA JUAN CARLOS', '80432691', '945253803', 'jhoncharlles.ma@gmail.com', '80432691', 1, '2020-01-01 00:00:00'),
(265, 31, 18, 2, 'ORMEÃ‘O BENDEZU JORGE', '61832572', '967265039', 'atmjoorgee18@gmail.com', '61832572', 1, '2020-01-01 00:00:00'),
(266, 31, 18, 2, 'PASTOR LEYVA ROQUE', '47506555', '917904421', 'rypl.1992@gmail.com', '47506555', 1, '2020-01-01 00:00:00'),
(267, 1, 18, 2, 'PEÃ‘A HUATA ALEX', '44262775', '999068503', 'penahuataalex@gmail.com', '44262775', 1, '2020-01-01 00:00:00'),
(268, 32, 18, 3, 'PEREZ RENGIFO ROMER', '43820832', '955624556', 'perezrengiforomel@gmail.com', '43820832', 1, '2020-01-01 00:00:00'),
(269, 32, 2, 2, 'POSADAS CARO EDWAR', '7425830', '972245361', 'edwarposadas@gmail.com', '7425830', 1, '2020-01-01 00:00:00'),
(270, 32, 31, 3, 'QUICO PUMACHAPI PEDRO', '06174493', '980761424', 'pedrobacilioquico@gmail.com', '06174493', 1, '2020-01-01 00:00:00'),
(271, 32, 18, 2, 'QUISPE VALERIANO MARIO', '24989937', '996488561', 'marioqval@hotmail.com', '24989937', 1, '2020-01-01 00:00:00'),
(272, 31, 2, 2, 'RAMIREZ CHAVEZ JHAN', '44284624', '1', 'A@W', '44284624', 1, '2020-01-01 00:00:00'),
(273, 32, 18, 2, 'RAO MALLMA DENISS', '42181231', '973967694', 'deniss757@gamail.com', '42181231', 1, '2020-01-01 00:00:00'),
(274, 31, 15, 2, 'RETUERTO CHAUCA WILDER', '41004228', '1', 'A@W', '41004228', 0, '2020-01-01 00:00:00'),
(275, 21, 18, 2, 'REYES GARCIA ALBERTO', '09798959', '998510239', 'abetortis@gmail.com', '09798959', 1, '2020-01-01 00:00:00'),
(276, 32, 2, 2, 'ROJAS CAMARGO GUILLERMO', '42014524', '926889198', 'grc1530@gmail.com', '42014524', 1, '2020-01-01 00:00:00'),
(277, 32, 31, 2, 'ROJAS HINOSTROZA PEDRO', '25544744', '934788002', 'rojashpedro5@gmail.com', '25544744', 1, '2020-01-01 00:00:00'),
(278, 31, 18, 2, 'SALAVERRY TELLO CARLOS', '09991607', '949444191', 'carlostello21011975@gmail.c', '09991607', 1, '2020-01-01 00:00:00'),
(279, 32, 18, 2, 'SALINAS SALAZAR WILLIAM', '10483792', '951714713', 'wosscancer@hotmail.com', '10483792', 1, '2020-01-01 00:00:00'),
(280, 31, 18, 2, 'SANCHEZ MIO JOEL', '45372172', '983450031', 'sanchezmio_1988@hotmail.com', '45372172', 1, '2020-01-01 00:00:00'),
(281, 32, 18, 2, 'SANTIAGO AGUIRRE RODOLVINO', '80104489', '959770554', 'rodolvino73@gmail.com', '80104489', 1, '2020-01-01 00:00:00'),
(282, 31, 18, 2, 'SANTILLAN SANDOVAL VLADIMIR', '48842991', '934182878', 'vladimir1097sandoval@gmail.com', '48842991', 1, '2020-01-01 00:00:00'),
(283, 32, 45, 2, 'SIMBRON LUNA JORGE', '43941232', '924512885', 'jorgesimbronluna@gamail.pe', '43941232', 1, '2020-01-01 00:00:00'),
(284, 42, 27, 3, 'SOLIS TULUMBA EDWARD', '47856799', '939497781', 'Ingsoliscivil01@gmail.com', '47856799', 1, '2020-01-01 00:00:00'),
(285, 42, 27, 3, 'SOLIS TULUMBA JOEL', '45191150', '918077290', 'anthonyst0308@gmail.com', '45191150', 1, '2020-01-01 00:00:00'),
(286, 31, 18, 2, 'SOTELO SABUCO PITHER', '45696924', '933801394', 'Pitersotelosabuco@gmail.com', '45696924', 1, '2020-01-01 00:00:00'),
(287, 32, 18, 2, 'SUCA MARCHENA MARIO', '25848215', '986508456', 'raul.suca.marchena@hotmail.com', '25848215', 1, '2020-01-01 00:00:00'),
(288, 32, 31, 2, 'TABOADA RAMIREZ JIMMY ENRIQUE', '17635490', '996701606', 'elchoclitodel1977@gmail.com', '17635490', 1, '2020-01-01 00:00:00'),
(289, 32, 31, 2, 'TAVARA ODAR ELMER', '44806999', '927387583', 'elmertavara15@gmail.com', '44806999', 1, '2020-01-01 00:00:00'),
(290, 32, 2, 2, 'TAVARA ODAR JOSE', '43618133', '975597252', 'josetavara86@gmail.com', '43618133', 1, '2020-01-01 00:00:00'),
(291, 31, 18, 2, 'TORRES CUBA WALTER', '25748501', '976590992', 'waltertorrrescuba@gmail.com', '25748501', 1, '2020-01-01 00:00:00'),
(292, 32, 18, 2, 'TORRES RENGIFO PERCY', '05371882', '980174523', 'torres.especialista@05gmail.com', '05371882', 1, '2020-01-01 00:00:00'),
(293, 31, 2, 2, 'VALENTIN ONOFRE TEDDY', '72396003', '996913208', 'valentinonofret@gmail.com', '72396003', 1, '2020-01-01 00:00:00'),
(294, 32, 18, 2, 'VILA AVILEZ ABEL', '20653822', '976535307', 'vilaavilesabel@gmail.com', '20653822', 1, '2020-01-01 00:00:00'),
(295, 31, 18, 2, 'VILA MENDOZA JORGE', '48496501', '967072439', 'vilamendozajorge@gmail.com', '48496501', 1, '2020-01-01 00:00:00'),
(296, 32, 31, 2, 'VILA MENDOZA PABLO', '43803640', '967724941', 'A@W', '43803640', 1, '2020-01-01 00:00:00'),
(297, 31, 18, 2, 'VILLACORTA RAMIREZ SIDNEY', '40720549', '931827480', 'villacortaramrezsidney.@gmail.com', '40720549', 1, '2020-01-01 00:00:00'),
(298, 32, 31, 2, 'VIÑAS TORRES OSCAR ORLANDO', '70863775', '946093527', 'oscar161990@hotmail.com', '70863775', 1, '2020-01-01 00:00:00'),
(299, 32, 31, 2, 'ZORRILLA LOZANO CESAR', '42905647', '916212514', 'zorilla884@gmail.com', '42905647', 1, '2020-01-01 00:00:00'),
(300, 34, 16, 3, 'MENDOZA GARAY EDGAR', '09822167', '981464511', 'emendoza@grupoinversionesgyc.pe', '09822167', 1, '2020-01-01 00:00:00'),
(301, 105, 16, 1, 'ROBINSON YAÑEZ', '73041890', '961865852', 'rmyanezr@gmail.com', '73041890', 1, '2020-01-01 00:00:00'),
(302, 4, 12, 3, 'CALLUPE CHAVEZ JORDAN', '71740608', '968173738', 'jcallupe.grupoinversionesgyc@gmail.com', '71740608', 1, '2020-01-01 00:00:00'),
(303, 4, 13, 3, 'BARONA QUISPE EDUARDO CORNELIO', '07352859', '995940484', 'ebarona@grupoinversionesgy', '07352859', 1, '2020-01-01 00:00:00'),
(304, 4, 5, 3, 'CALLE YANGUA WILSON', '09602880', '1', 'wcallegyc@gmail.com', '09602880', 0, '2020-01-01 00:00:00'),
(305, 4, 3, 2, 'DAVALOS CASTAÑEDA DALIA ALEJANDRA MILAGROS', '47935048', '930611767', 'ingenieradavalos@gmail.com', '47935048', 0, '2020-01-01 00:00:00'),
(306, 4, 3, 2, 'MANCHA AROSTEGUI JOSE LUIS', '41559142', '956967207', 'jlma_2810@hotmail.com', '41559142', 0, '2020-01-01 00:00:00'),
(307, 95, 3, 1, 'RAMOS VILLAFRANCA CENIA NOELIA', '71539062', '944571331', 'cramos@arceperu.pe', '71539062', 1, '2020-01-01 00:00:00'),
(308, 1, 5, 2, 'LOPEZ LOPEZ PEDRO DAVID', '44651552', '976569515', 'pedrodavidlopez1986@gmail.com', '44651552', 1, '2020-01-01 00:00:00'),
(309, 1, 5, 2, 'LA ROSA GIRIBALDI RICHARD IVAN', '16005077', '959671507', 'rholamp@gmail.com', '16005077', 1, '2020-01-01 00:00:00'),
(310, 1, 18, 2, 'ROJAS ARIZAPANA BENJI DANNY', '71481210', '955130388', 'Benji.gnr@hotmail.es', '71481210', 0, '2020-01-01 00:00:00'),
(311, 1, 27, 2, 'RODRIGUEZ FLORES VICTOR', '43388862', '910771775', 'victorodriguez1083@gmail.con', '43388862', 1, '2020-01-01 00:00:00'),
(312, 37, 5, 2, 'FASANANDO ESPINOZA TONNY', '42550172', '968386177', 'tonnyfasanando08@gmail.com', '42550172', 1, '2020-01-01 00:00:00'),
(313, 37, 5, 2, 'JUAREZ CARRANZA DIOMEDES WILSON', '10745122', '945528853', 'dmjc_@hotmail.com', '10745122', 1, '2020-01-01 00:00:00'),
(314, 31, 2, 2, 'AGUILAR NOVOA LANDER BEIKER', '76294961', '963589033', 'ajhon4943@gmail.com', '76294961', 1, '2020-01-01 00:00:00'),
(315, 35, 18, 2, 'AGUILAR SAQUICORAY LINCOL', '45551035', '934599926', 'aguilarlincol@gmail.com', '45551035', 1, '2020-01-01 00:00:00'),
(316, 74, 39, 2, 'ALCA BARRA CARLOS JESUS', '09548431', '934517040', 'carlos.alca.barra@gmail.com', '09548431', 1, '2020-01-01 00:00:00'),
(317, 36, 8, 1, 'ALEGRIA LEON DELIA ELISA', '42283557', '942435337', 'dalegria@grupoinversionesgyc.pe', '42283557', 1, '2020-01-01 00:00:00'),
(318, 32, 18, 2, 'ALIAGA VILA YOSHIO ANGEL', '43323398', '991879742', 'aliagaa807@gmail.com', '43323398', 0, '2020-01-01 00:00:00'),
(319, 37, 2, 2, 'ALTAMIRANO DELGADO JOSE ANDRES', '43918972', '922971050', 'joseandreal setamiranodelgado@gmail.com', '43918972', 1, '2020-01-01 00:00:00'),
(320, 3, 18, 2, 'ALVARADO GALLARDO FLORENTINO PERCY', '77464908', '952831018', 'percycitho.geminis59@gmail.com', '77464908', 1, '2020-01-01 00:00:00'),
(321, 29, 20, 2, 'ALVARADO VALLEJOS ELSA NILDA', '46966516', '', 'ealvarado@grupoinversionesgyc.pe', '46966516', 1, '2020-01-01 00:00:00'),
(322, 38, 21, 2, 'ANDAGUA RIVERA CLAUDIO AUGUSTO', '30423524', '921760692', 'candagua@gmail.com', '30423524', 1, '2020-01-01 00:00:00'),
(323, 39, 19, 2, 'APARCANA GOMEZ CYNTHIA', '42042877', '', 'caparcana@grupoinversionesgyc.pe', '42042877', 1, '2020-01-01 00:00:00'),
(324, 40, 12, 2, 'ARTEAGA OSORIO ELIZABETH', '47935424', '933431103', 'earteagao@uni.pe', '47935424', 1, '2020-01-01 00:00:00'),
(325, 41, 4, 3, 'STEPHEN RAUL URRETA PALACIOS', '44529016', '956722465', 'surreta@grupoinversionesgyc.pe', '44529016', 1, '2020-01-01 00:00:00'),
(326, 4, 5, 3, 'BALDEON ORELLANA ROLANDO JOSE', '45726180', '902744053', 'baldeonrolando@gmail.com', '45726180', 1, '2020-01-01 00:00:00'),
(327, 35, 18, 2, 'BARRA CASTRO ESAU', '46331576', '', '', '46331576', 1, '2020-01-01 00:00:00'),
(328, 11, 5, 2, 'BECERRA MOLINA IVAN', '23992809', '991123339', 'becerramolinaivan2@gmail.co', '23992809', 1, '2020-01-01 00:00:00'),
(329, 43, 2, 2, 'BRICEÑO MORI JESUS JHONY', '46238931', '954514717', 'jhonybmori@hotmail.com', '46238931', 0, '2020-01-01 00:00:00'),
(330, 31, 2, 2, 'CARLOS SILVA WILLIAN', '48014559', '995490269', 'williancarlossilva9@gmail.com', '48014559', 1, '2020-01-01 00:00:00'),
(331, 42, 5, 3, 'CARTY LOPEZ PERCY CARLOS', '40885322', '999500966', 'pcarty@grupoinversionesgyc.pe', '40885322', 1, '2020-01-01 00:00:00'),
(332, 44, 19, 2, 'CASTILLO BARATTINI CARLOS ENRIQUE', '09659966', '', 'ccastillo@grupoinversionesgyc.pe', '09659966', 1, '2020-01-01 00:00:00'),
(333, 32, 2, 2, 'CASTRO LIZARBE DENY JHON', '45147785', '900200960', 'denycastrolizarbe@gmail.com', '45147785', 0, '2020-01-01 00:00:00'),
(334, 40, 5, 2, 'CASTRO MARQUEZ WALTER ENRIQUE', '25774584', '946377105', 'castrowmarquez@hotmail.com', '25774584', 1, '2020-01-01 00:00:00'),
(335, 32, 18, 2, 'CHACCARA POCCO ULISES', '80653629', '991709970', 'Chaccarapoccoulises@ymail.com', '80653629', 0, '2020-01-01 00:00:00'),
(336, 32, 2, 2, 'CHALLCO HUALLPAYUNCA ULISES', '42375846', '989295737', 'challcohuallpayuncau@gmail.com', '42375846', 0, '2020-01-01 00:00:00'),
(337, 4, 1, 3, 'CHIGNE VARGAS BRAYAN MARTIN', '42583925', '953722601', 'bchigne@arceperu.pe', '42583925', 1, '2020-01-01 00:00:00'),
(338, 32, 2, 2, 'CHUQUIVAL HUAYNACARI JEHU', '44306754', '925422997', 'jhonsebas.87@hotmail.com', '44306754', 1, '2020-01-01 00:00:00'),
(339, 11, 2, 2, 'CLAUDIO NUÑEZ MANUEL GUSTAVO', '45219657', '927382351', 'manuelgustavoclaudionunez@gmail.com', '45219657', 1, '2020-01-01 00:00:00'),
(340, 40, 5, 2, 'COLLAZOS SOPLOPUCO MIGUEL', '43926249', '995674668', 'miguel_86_14@hotmail.com', '43926249', 1, '2020-01-01 00:00:00'),
(341, 31, 2, 2, 'CONDE ACHAS ADAN CUPERTINO', '48475702', '970390510', 'roy_adan_cobra@hotmail.com', '48475702', 1, '2020-01-01 00:00:00'),
(342, 17, 2, 3, 'CORDOVA SUERO JOSE ALFREDO', '71455244', '901517811', 'jcordova@arceperu.pe', '71455244', 1, '2020-01-01 00:00:00'),
(343, 32, 2, 2, 'CRUZ ALVARO ABED LINDER', '73038587', '915074830', 'abellindercruzalvaro@gmail.com', '73038587', 0, '2020-01-01 00:00:00'),
(344, 32, 2, 2, 'CRUZ PEREZ NEISER', '46279597', '956659948', 'neiser6962.ncp@gmail.com', '46279597', 1, '2020-01-01 00:00:00'),
(345, 32, 18, 2, 'CRUZ SOLIS DOMINGO', '15721276', '945501898', 'domingocs36@gmail.com', '15721276', 0, '2020-01-01 00:00:00'),
(346, 32, 18, 2, 'CUBA ACUACHE MANUEL JESUS', '09124079', '987196734', 'kuba.mc@hotmail.com', '09124079', 0, '2020-01-01 00:00:00'),
(347, 35, 18, 2, 'CUNGAL NIMA VICTOR', '10520430', '968586279', 'victorcungalnima53@gmail.co', '10520430', 1, '2020-01-01 00:00:00'),
(348, 35, 17, 2, 'DAVILA GARAY BERNINE JAVIER', '44425058', '1', 'a@w', '44425058', 0, '2020-01-01 00:00:00'),
(349, 35, 18, 2, 'DIAZ SAHUARICO NIX', '44038399', '902635629', 'nixdiaz27@gmail.com', '44038399', 1, '2020-01-01 00:00:00'),
(350, 32, 27, 2, 'DURAN MARIN PABLO LEONARDO', '47481527', '927791437', 'duran_10_07@hotmail.com', '47481527', 1, '2020-01-01 00:00:00'),
(351, 31, 18, 2, 'EGOAVIL GOMEZ RODER DIEGO', '45552260', '924349610', 'egoavilgomesdiego@hotmil.com', '45552260', 1, '2020-01-01 00:00:00'),
(352, 32, 2, 2, 'ENRIQUE SANTIAGO NERI RENE', '44262355', '902707277', 'esrene30@gmail.com', '44262355', 0, '2020-01-01 00:00:00'),
(353, 43, 31, 3, 'ESPINO CASTILLO CARLOS ALFREDO', '76635391', '941606018', 'carlos.espinoc@hotmail.com', '76635391', 1, '2020-01-01 00:00:00'),
(354, 35, 18, 2, 'ESPINOZA PABLO RODMER', '41806463', '', '', '41806463', 1, '2020-01-01 00:00:00'),
(355, 45, 2, 3, 'FARFAN CHALLCO JUAN CARLOS', '10661326', '942816063', 'juanfarfanchallco@hotmail.com', '10661326', 1, '2020-01-01 00:00:00'),
(356, 46, 19, 3, 'FERNANDO ESCUDERO', '10198337', '1', 'sistemas@grupoinversionesgyc.pe', '10198337', 1, '2020-01-01 00:00:00'),
(357, 35, 17, 2, 'GALLARDO TORRES CESAR AUGUSTO', '80613812', '1', 'a@w', '80613812', 0, '2020-01-01 00:00:00'),
(358, 43, 2, 4, 'GARAY GONZALES MANUEL PEDRO', '40287180', '996880046', 'garaymanuel284@gmail.com', '40287180', 1, '2020-01-01 00:00:00'),
(359, 31, 2, 2, 'GOMEZ EUGENIO JOSUE', '42242224', '974948315', 'josuegomezeugenio84@gmail.com', '42242224', 1, '2020-01-01 00:00:00'),
(360, 1, 18, 2, 'GUTIERREZ REYES JEAN CARLOS', '76791216', '947943441', 'reyesjean286@gmail.com', '76791216', 1, '2020-01-01 00:00:00'),
(361, 32, 2, 2, 'HERDUAY GALLEGOS FREDY', '46877662', '', '', '46877662', 1, '2020-01-01 00:00:00'),
(362, 35, 17, 2, 'HERNANDEZ OLLARVES YOSMAR COROMOT', '02825870', '1', 'a@w', '02825870', 0, '2020-01-01 00:00:00'),
(363, 35, 17, 2, 'HERRERA CELIO DANIEL JACOB', '42210655', '1', 'a@w', '42210655', 0, '2020-01-01 00:00:00'),
(364, 47, 2, 2, 'HINOJOSA CHALLCO JULIO HECTOR', '08135171', '', '', '08135171', 1, '2020-01-01 00:00:00'),
(365, 32, 2, 2, 'HUAMAN CARDENAS WALTER ANTONIO', '07672472', '900186880', 'walter.conserconha@gmail.c3', '07672472', 0, '2020-01-01 00:00:00'),
(366, 48, 19, 2, 'IBARRA HUASHUAYO MARISOL', '44343004', '', 'mibarra@grupoinversionesgyc.pe', '44343004', 1, '2020-01-01 00:00:00'),
(367, 48, 19, 2, 'IBARRA HUASHUAYO ROSARIO', '43249133', '', 'ribarra@grupoinversionesgyc.pe', '43249133', 1, '2020-01-01 00:00:00'),
(368, 32, 2, 2, 'INGA TARACHEA CHRISTIAN GROVER', '41029726', '958405744', 'christian.inga.061080@gmail.com', '41029726', 0, '2020-01-01 00:00:00'),
(369, 16, 9, 2, 'IPURRE CCAYO MARIBEL', '70434892', '991980532', 'mipurre@grupoinversionesgyc.pe', '70434892', 0, '2020-01-01 00:00:00'),
(370, 35, 18, 2, 'JARA RAMOS DAVID LIBERATO', '44587307', '927888703', 'jararamosd@gmil.com', '44587307', 1, '2020-01-01 00:00:00'),
(371, 27, 19, 2, 'JUAREZ DAVILA MIRIAM JOHANA', '42094053', '', 'jjuarez@grupoinversionesgyc.pe', '42094053', 1, '2020-01-01 00:00:00'),
(372, 49, 19, 2, 'JULIO CESAR ARNAO', '', '', 'carnao@grupoinversionesgyc.pe', '', 1, '2020-01-01 00:00:00'),
(373, 50, 19, 2, 'LEO ALMERCO CRISTINA YSABEL', '45892340', '', 'cleo@grupoinversionesgyc.pe', '45892340', 1, '2020-01-01 00:00:00'),
(374, 48, 19, 2, 'LLONTOP SILVA GLADYS DAMARIS', '41482773', '', 'gllontop@grupoinversionesgyc.pe', '41482773', 1, '2020-01-01 00:00:00'),
(375, 35, 17, 2, 'LOPEZ RAMANI JESUS ANTONIO', '10528358', '1', 'a@q', '10528358', 0, '2020-01-01 00:00:00'),
(376, 32, 2, 2, 'LUIS BARRETO JUSTINO VICTOR', '15632747', '950403498', 'luis010bra@gmail.com', '15632747', 1, '2020-01-01 00:00:00'),
(377, 4, 5, 3, 'MACETAS CORDOVA MAX KENY', '71584271', '931497856', 'm.macetas.c@gmail.com', '71584271', 1, '2020-01-01 00:00:00');
INSERT INTO `personal` (`id_personal`, `id_cargo`, `id_area`, `id_tipo`, `nom_personal`, `dni_personal`, `cel_personal`, `email_personal`, `pass_personal`, `act_personal`, `updated_at`) VALUES
(378, 11, 5, 2, 'MACHACA MAMANI TEOFILO ELADIO', '25788229', '933360006', 'teoelamacmam@gmail.com', '25788229', 1, '2020-01-01 00:00:00'),
(379, 35, 17, 2, 'MANRIQUE MELENDEZ KAREN ALONDRA', '74631022', '1', 'A@W', '74631022', 0, '2020-01-01 00:00:00'),
(380, 52, 19, 2, 'MARTINEZ LOYOLA CARLOS', '08462581', '', '', '08462581', 1, '2020-01-01 00:00:00'),
(381, 52, 19, 2, 'MARTINEZ LOYOLA MARCO', '08467110', '', '', '08467110', 1, '2020-01-01 00:00:00'),
(382, 32, 2, 2, 'MARTINEZ PIZARRO LUIS FERNANDO', '21289765', '987774629', 'lmpizarro3@gmail.com', '21289765', 1, '2020-01-01 00:00:00'),
(383, 32, 2, 2, 'MEDRANO CORDOVA JOSE SANTOS', '48222057', '935854517', 'jose_chino_10@hotmail.com', '48222057', 0, '2020-01-01 00:00:00'),
(384, 32, 2, 2, 'MELENDEZ OBREGON SAMUEL NEMERSON', '22889413', '945204725', 'melendez.23.1974.@mail.com', '22889413', 1, '2020-01-01 00:00:00'),
(385, 32, 2, 2, 'MENDOZA GAONA LEONIDAS ALBERTO', '40647382', '998510045', 'betomendoza3sfd@gmail.com', '40647382', 1, '2020-01-01 00:00:00'),
(386, 53, 19, 2, 'MIGUEL NINA BARRIOS', '', '', '', '', 1, '2020-01-01 00:00:00'),
(387, 32, 2, 2, 'MISARI FIERRO ALEX', '40919487', '971329938', 'alexmisari81@gmail.com', '40919487', 0, '2020-01-01 00:00:00'),
(388, 42, 27, 3, 'MONTES QUISPE PEDRO', '46454880', '966399054', 'pedro14789@hotmail.com', '46454880', 1, '2020-01-01 00:00:00'),
(389, 37, 2, 2, 'MORALES VASQUEZ RUBEN ALONSO', '43970655', '941957059', 'morales252016@gmail.com', '43970655', 1, '2020-01-01 00:00:00'),
(390, 32, 2, 2, 'MORENO RUIZ MARTIN ERNESTO', '72260586', '950322554', 'martinernesmorenoi @gmail.co', '72260586', 1, '2020-01-01 00:00:00'),
(391, 31, 2, 2, 'MORI ALTAMIRANO JESUS ORLANDO', '75562729', '42558208', 'Jesusmorialtamirano25@gmail.com', '75562729', 0, '2020-01-01 00:00:00'),
(392, 32, 2, 2, 'MORI ROMAN FLORENCIO DAMIAN', '08027914', '996026310', 'moriflorencio@gmail.com', '08027914', 1, '2020-01-01 00:00:00'),
(393, 35, 18, 2, 'OLASCOAGA ALVA ARTIDORO', '43418662', '986490842', 'olascoagaalvaartidoro@gmail.com', '43418662', 1, '2020-01-01 00:00:00'),
(394, 32, 31, 2, 'ORE VARGAS JOSE LUIS', '07050374', '918634078', 'Joseluisorevarvas@gmail.com', '07050374', 1, '2020-01-01 00:00:00'),
(395, 35, 17, 2, 'ORELLANO CORAHUA JULIO', '41032151', '1', 'A@W', '41032151', 0, '2020-01-01 00:00:00'),
(396, 32, 2, 2, 'ORTIZ VILLALOBOS JUAN FELIX', '40467778', '986279727', 'juanortiz-79@hotmail.com', '40467778', 1, '2020-01-01 00:00:00'),
(397, 35, 17, 2, 'PADILLA AGUEDO GIAN CARLOS', '46648322', '1', 'A@W', '46648322', 0, '2020-01-01 00:00:00'),
(398, 35, 17, 2, 'PALACIOS MEDINA JAIME CECILIO', '42074753', '1', 'A@W', '42074753', 0, '2020-01-01 00:00:00'),
(399, 11, 2, 2, 'PALOMINO HUAMAN JUVENAL', '10068788', '988916109', 'palominohuamanjuvenal@gmail.com', '10068788', 1, '2020-01-01 00:00:00'),
(400, 31, 18, 2, 'PAYANO HINOSTROZA EFRAIN', '40909130', '948576534', 'efrainpayano.06@gmail.com', '40909130', 1, '2020-01-01 00:00:00'),
(401, 40, 5, 2, 'PECHE BAZAN LUIS ASUNCI?N', '45239901', '943209757', 'luis_12588@hotmail.com', '45239901', 1, '2020-01-01 00:00:00'),
(402, 47, 2, 2, 'PELAES GONZALES MANUEL EPIFANIO', '09631918', '923923150', 'mpelaes@jg3construcciones.com', '09631918', 0, '2020-01-01 00:00:00'),
(403, 32, 27, 2, 'PEÃ‘A SALVADOR JOSE LUIS', '45728627', '924972433', 'penajoseluis588@gmail.com', '45728627', 1, '2020-01-01 00:00:00'),
(404, 32, 31, 2, 'PEREZ CABRERA SEGUNDO TEDY', '47988795', '931038622', 'tedyperezcabrera@gmail.com', '47988795', 1, '2020-01-01 00:00:00'),
(405, 32, 2, 2, 'PILLACA CURI GILMAR', '41192357', '', '', '41192357', 1, '2020-01-01 00:00:00'),
(406, 32, 2, 2, 'PILLACA CURI WILBER', '10246258', '992043443', 'wilpc73@hotmail.com', '10246258', 0, '2020-01-01 00:00:00'),
(407, 35, 18, 2, 'PINEDA SANDOVAL PABLO', '07721790', '995531887', 'ppineda2011@gmail.com', '07721790', 1, '2020-01-01 00:00:00'),
(408, 31, 2, 2, 'QUEVEDO OCHAVANO VICTOR RAUL', '48208779', '943114973', 'jjackytaflor.hermosa@gmail.c', '48208779', 1, '2020-01-01 00:00:00'),
(409, 1, 45, 2, 'QUEZADA GARCIA JUAN CARLOS', '43596148', '935981366', 'juanca_19qg@hotmail.com', '43596148', 1, '2020-01-01 00:00:00'),
(410, 32, 2, 2, 'QUINTO HUARAKA DAVID', '08159549', '989334519', 'Quintohuarakadavid@gmail.co', '08159549', 1, '2020-01-01 00:00:00'),
(411, 31, 2, 2, 'QUISPE MORENO RUBEN JONATHAN', '44013096', '987846371', 'Jonathan.quispe.198706@gmail.com', '44013096', 0, '2020-01-01 00:00:00'),
(412, 31, 2, 2, 'QUISPE SALAZAR RICARDO', '74918253', '', '', '74918253', 1, '2020-01-01 00:00:00'),
(413, 35, 18, 2, 'QUISPE SOTA WILLIAM', '42731567', '935256829', 'quispesotawilliam@ gmail.com', '42731567', 1, '2020-01-01 00:00:00'),
(414, 32, 27, 2, 'RAMON PANDURO BENJAMIN SAMUEL', '41223341', '997311599', 'shoshyfive@hoymail.com', '41223341', 1, '2020-01-01 00:00:00'),
(415, 35, 17, 2, 'RIVEROS SALLUCA CRISTIAN ALVARADO', '76588754', '1', 'A@W', '76588754', 0, '2020-01-01 00:00:00'),
(416, 55, 19, 2, 'RUIZ NIETO ALEXANDRA NAHOMY', '73263788', '', 'aruiz@grupoinversionesgyc.pe', '73263788', 1, '2020-01-01 00:00:00'),
(417, 35, 18, 2, 'SALAS SANGAMA EDGAR', '40783158', '', '', '40783158', 1, '2020-01-01 00:00:00'),
(418, 40, 5, 2, 'SALCEDO CAMA DAVID', '42201905', '927184939', 'david.salcedo.chino@gmail.co', '42201905', 1, '2020-01-01 00:00:00'),
(419, 1, 5, 2, 'SALCEDO CAMA MARTIN JAIME', '41338413', '986086464', 'martinsalcedo81@hotmail.co', '41338413', 1, '2020-01-01 00:00:00'),
(420, 54, 18, 2, 'SALINAS MORENO ELDER JAIME', '42558208', '1', 'A@W', '42558208', 0, '2020-01-01 00:00:00'),
(421, 31, 2, 2, 'SANCHEZ CHAVEZ JOSELITO', '71707931', '958504631', 'Joselitosc95@gmail.com', '71707931', 0, '2020-01-01 00:00:00'),
(422, 56, 18, 2, 'SANCHEZ SORIANO GIOMAR JAVIER', '45199403', '945111102', 'giomarsanchezsoriano@gmail.com', '45199403', 0, '2020-01-01 00:00:00'),
(423, 32, 2, 2, 'SEGOVIA ARONI JOSE ANTONIO', '46921854', '958469370', 'josesegoviaaroni@gmail.com', '46921854', 1, '2020-01-01 00:00:00'),
(424, 35, 18, 2, 'SINARAHUA TUANAMA DIGMER', '48606724', '', '', '48606724', 1, '2020-01-01 00:00:00'),
(425, 31, 2, 2, 'TAVARA SILVA DAVID EDUARDO', '43785670', '996214097', 'tavarad9@gmail.com', '43785670', 1, '2020-01-01 00:00:00'),
(426, 57, 19, 2, 'TOMAYQUISPE GARCIA PLACIDA APOLINARI', '07916070', '', '', '07916070', 1, '2020-01-01 00:00:00'),
(427, 4, 1, 3, 'TORRES LUNA LUIS ALBERTO', '46805146', '935006424', 'ltorres@grupoinversionesgyc.pe', '46805146', 1, '2020-01-01 00:00:00'),
(428, 35, 18, 2, 'TORRES RAMIREZ VICTOR RAUL', '07097080', '922456', 'victor. ramirez2015@outlook.com', '07097080', 1, '2020-01-01 00:00:00'),
(429, 35, 17, 2, 'TRUJILLO HERMITAÑO MILENA BANY', '71922478', '1', 'A@W', '71922478', 0, '2020-01-01 00:00:00'),
(430, 35, 18, 2, 'TUANAMA FASABI LIMBER', '44472648', '902359360', 'limbertuanama7@gmail.com', '44472648', 1, '2020-01-01 00:00:00'),
(431, 35, 18, 2, 'TUIRO TAPIA FRANCISCO', '10434534', '', '', '10434534', 1, '2020-01-01 00:00:00'),
(432, 42, 3, 2, 'TURIPE RODRIGUEZ MANUEL ALEJANDRO', '02832042', '945693144', 'manuel.turipe@gmail.com', '02832042', 0, '2020-01-01 00:00:00'),
(433, 35, 18, 2, 'VALDERRAMA ARCE EDGAR', '70160547', '950617195', 'arcelive1994@hotmail.com', '70160547', 1, '2020-01-01 00:00:00'),
(434, 35, 18, 2, 'VALDERRAMA PEREZ LEDIWIS', '05294332', '', '', '05294332', 1, '2020-01-01 00:00:00'),
(435, 35, 18, 2, 'VALQUE CHAVEZ GEYDER', '73666833', '', '', '73666833', 1, '2020-01-01 00:00:00'),
(436, 35, 18, 2, 'VALQUE CHAVEZ JAIME EDUARDO', '47425260', '', '', '47425260', 1, '2020-01-01 00:00:00'),
(437, 35, 18, 2, 'VASQUEZ CASTAÑEDA ALEX', '48923378', '1', 'A@W', '48923378', 1, '2020-01-01 00:00:00'),
(438, 4, 18, 3, 'VASQUEZ FASABI ROBERT IVAN', '10630178', '980882973', 'rivf90@hotmail.com', '10630178', 1, '2020-01-01 00:00:00'),
(439, 55, 19, 2, 'VILLANUEVA ALTAMIRANO JOSE', '09775165', '', 'jvillanueva@grupoinversionesgyc.pe', '09775165', 1, '2020-01-01 00:00:00'),
(440, 32, 2, 2, 'YAURICASA CANCHARI LUIS ADRIAN', '44016910', '992894954', 'luis.adrianyc@hotmail.com', '44016910', 0, '2020-01-01 00:00:00'),
(441, 29, 2, 2, 'ZAVALETA RIVERA JESUS ALCIDES', '73068963', '984415183', 'jzavaleta@arceperu.pe', '73068963', 1, '2020-01-01 00:00:00'),
(442, 35, 18, 2, 'ZELADA VIVAS JOSE', '47780784', '', '', '47780784', 1, '2020-01-01 00:00:00'),
(443, 78, 18, 3, 'INOCENTE SALGUERO RAUL ALBERTO', '16120663', '985893173', 'rinocente@grupoinversionesgyc.pe', '16120663', 1, '2020-01-01 00:00:00'),
(444, 78, 26, 1, 'LUJAN MARCHAN JORGE', '44859161', '937595970', 'medico.ocupacional@grupoinversionesgyc.pe', '44859161', 1, '2020-01-01 00:00:00'),
(445, 59, 26, 2, 'DEZA ESPINOZA CYNTHIA ANGELA', '70034369', '991207285', 'cdeza@grupoinversionesgyc.pe', '70034369', 0, '2020-01-01 00:00:00'),
(446, 1, 2, 3, 'SANCHEZ ORE WILMER FRANK', '46501928', '935923604', 'wsanchez.grupoinversionesgyc@gmail.com', '46501928', 1, '2020-01-01 00:00:00'),
(447, 4, 3, 3, 'CARAZAS SEGOBIA HEBERT ALEXANDER', '43031973', '980796240', 'carazasalex@gmail.com', '43031973', 0, '2020-01-01 00:00:00'),
(448, 4, 3, 2, 'QUIROZ PEÑA CESAR AUGUSTO', '47417345', '944621984', 'cquiroz@grupoinversionesgyc.pe', '47417345', 0, '2020-01-01 00:00:00'),
(449, 42, 18, 4, 'VELASQUEZ CHAVEZ NESTOR AUGUSTO', '42498341', '948339539', 'nvelasquezch@gmail.com', '42498341', 0, '2020-01-01 00:00:00'),
(450, 1, 2, 2, 'COCA BLANCO EDILBERTO', '75255835', '942197780', 'kevincocablanco@outlook.com', '75255835', 1, '2020-01-01 00:00:00'),
(451, 45, 2, 3, 'CHAVEZ QUISPE JORGE WASHINGTON', '10524701', '999955422', 'jchavez@grupoinversionesgyc.pe', '10524701', 1, '2020-01-01 00:00:00'),
(452, 2, 2, 2, 'YARLEQUE BAYONA LIMBERG CLEVERSON', '47447599', '990302869', 'limberg.cleverson@gmail.com', '47447599', 1, '2020-01-01 00:00:00'),
(453, 17, 18, 4, 'SERNAQUE COBEÑAS JUAN CARLOS', '10743973', '992384186', 'jsernaque@grupoinversionesgyc.pe', '10743973', 1, '2020-01-01 00:00:00'),
(454, 43, 31, 3, 'PACHAS TORRES JEAN CARLO', '46614333', '999846403', 'jean_p26@hotmail.com', '46614333', 1, '2020-01-01 00:00:00'),
(455, 51, 18, 3, 'ALTAMIRANO CAICEDO JHON DENNIS', '70515930', '997246407', 'jd.altamiranoc@gmail.com', '70515930', 1, '2020-01-01 00:00:00'),
(456, 51, 18, 2, 'SUXE SANCHEZ ELITER NOEL', '77346878', '990196736', 'suxesanchezeliter@gmail.com', '77346878', 0, '2020-01-01 00:00:00'),
(457, 41, 18, 4, 'RODRIGUEZ AGUILA GINO ENRIQUE', '72406687', '945013485', 'ginoenriquerodriguezaguila@gmail.com', '72406687', 1, '2020-01-01 00:00:00'),
(458, 43, 18, 4, 'NEIRA MENDEZ MARIO ENRIQUE GERARDO', '45774957', '999056953', 'marioenriqueneira@gmail.com', '45774957', 1, '2020-01-01 00:00:00'),
(459, 43, 18, 4, 'LAIME ANCALLE GUSTAVO', '46757052', '1', 'glaime0113@gmail.com', '46757052', 1, '2020-01-01 00:00:00'),
(460, 23, 18, 2, 'VARELA CHAVEZ JHOMARE CHRISTIAN', '46827792', '972345543', 'jvarelac@uni.pe', '46827792', 1, '2020-01-01 00:00:00'),
(461, 72, 3, 3, 'CALIPUY TACANGA MILCIADES ERIBERTO', '42437793', '942240568', 'ecalipuy@arceperu.pe', '42437793', 1, '2020-01-01 00:00:00'),
(462, 32, 27, 2, 'CONDOR TORRES JAIME ROY', '10161064', '981665164', 'ctjaime8@gmail.com', '10161064', 1, '2020-01-01 00:00:00'),
(463, 21, 18, 2, 'CULLANCO VILCAPUMA MARIO', '42711705', '965354978', 'mcv504@hotmail.com', '42711705', 0, '2020-01-01 00:00:00'),
(464, 99, 31, 2, 'GONZALES DEXTRE LILA LUZ', '09545067', '994850294', 'lilaluzgondex@gmail.com', '09545067', 1, '2020-01-01 00:00:00'),
(465, 31, 27, 2, 'HINOJOZA SUCUITANA JUAN VICENTE', '71665946', '976452377', 'a@w', '71665946', 0, '2020-01-01 00:00:00'),
(466, 31, 18, 2, 'JULCA RAMOS MIGUEL ANGEL', '71539529', '965909222', 'julcaramosmiguelangel3@gmail.com', '71539529', 0, '2020-01-01 00:00:00'),
(467, 3, 18, 2, 'MAYTA LUYO ZEBEDEO JACOBO', '42435890', '920294011', 'a@w', '42435890', 0, '2020-01-01 00:00:00'),
(468, 31, 18, 2, 'MEDINA QUISPE KEVIN ARTHUR', '70322498', '960083587', 'edrianabdias@gmail.com', '70322498', 1, '2020-01-01 00:00:00'),
(469, 1, 18, 2, 'MENDOZA RIOS CRISTIAN', '78402354', '998323758', 'Cristianmendozarios6@gmail.com', '78402354', 1, '2020-01-01 00:00:00'),
(470, 31, 18, 2, 'MONTALVO OVIEDO JAIRO HERVER', '45454392', '929031793', 'montalvooviedojairoherver@hotmail.com', '45454392', 0, '2020-01-01 00:00:00'),
(471, 31, 18, 2, 'MOQUILLAZA SANTIAGO DAIBY EDISON', '75741179', '993432820', 'edisonmoquillazas@gmail.com', '75741179', 0, '2020-01-01 00:00:00'),
(472, 31, 18, 2, 'MORENO AGUIRRE GIAN ROGER', '75405586', '952003262', 'zhian65@gmail.com', '75405586', 0, '2020-01-01 00:00:00'),
(473, 31, 27, 2, 'PEREZ SANCHEZ DIOGENES IVAN', '48421087', '951325595', 'sebaspt1201@gmail.com', '48421087', 1, '2020-01-01 00:00:00'),
(474, 31, 18, 2, 'RIOS CANO MIGUEL ANGEL', '70996703', '994892805', 'sccp.mrios@gmail.com', '70996703', 0, '2020-01-01 00:00:00'),
(475, 1, 31, 2, 'ROSALES AVILA CESAR ALEX', '43400368', '971275319', 'alexrosalesavila@gmail.com', '43400368', 1, '2020-01-01 00:00:00'),
(476, 37, 18, 2, 'SUASNABAR SANTIAGO CHISTIAN HAROL', '41632180', '991325105', 'Vasago1523@gmail.com ', '41632180', 1, '2020-01-01 00:00:00'),
(477, 3, 18, 2, 'TASAYCO GARCIA MARTIN ELEODORO', '09989280', '920684012', 'tasaycogarcia68@hotmail.co', '09989280', 0, '2020-01-01 00:00:00'),
(478, 54, 18, 2, 'TINTAYO ZARATE AURELIO', '42331159', '999269888', 'Hanstz25@gmail.com', '42331159', 1, '2020-01-01 00:00:00'),
(479, 32, 31, 2, 'VALDIVIA ALUMIA MARIO', '43636877', '970525583', 'mariovaldiviaalumia@gmail.com', '43636877', 1, '2020-01-01 00:00:00'),
(480, 31, 18, 2, 'ZAMORA NAVARRO JAMES BRYAN', '77208541', '927776318', 'jamesbryanzamoranavarro1@gmail.com', '77208541', 0, '2020-01-01 00:00:00'),
(481, 47, 18, 2, 'ZUÑIGA ROJAS MIGUEL ANGEL', '70830974', '955533595', 'angel1202912@gmail.com', '70830974', 0, '2020-01-01 00:00:00'),
(482, 40, 2, 2, 'PARIONA PILLACA FREDDY', '44204618', '1', 'a@w', '44204618', 1, '2020-01-01 00:00:00'),
(483, 40, 2, 2, 'CHAVEZ VILCHEZ WILDER', '46999357', '935307278', 'wilderchavil@gmail.co', '46999357', 1, '2020-01-01 00:00:00'),
(484, 40, 2, 2, 'CARPIO VELASCO RIDDLER', '46441084', '940365585', 'ridcar@gmail.com ', '46441084', 1, '2020-01-01 00:00:00'),
(485, 40, 2, 2, 'COCHACHIN CHAVEZ JOEL', '47403129', '993826633', 'jdcochachinchavez@gmail.com', '47403129', 1, '2020-01-01 00:00:00'),
(486, 32, 2, 2, 'DAVILA VIDAURRE JUAN', '46209741', '955470763', 'Juancarlosdavilavidaurre@gmail.com', '46209741', 1, '2020-01-01 00:00:00'),
(487, 45, 2, 2, 'ESCOBAR YAMATO MARIO ANDRES', '08619974', '956771754', 'mario_wally@hotmail.com', '08619974', 1, '2020-01-01 00:00:00'),
(488, 22, 2, 2, 'LLANOS TARRILLO JUAN', '44856691', '982317333', 'jllanos1102@gmail.com', '44856691', 1, '2020-01-01 00:00:00'),
(489, 32, 2, 2, 'MACHACUAY GAMARRA EMILIO', '09501197', '922340207', 'a@w', '09501197', 1, '2020-01-01 00:00:00'),
(490, 33, 2, 2, 'PISCO AGUILAR AMADOR', '00846546', '930109937', 'amadorcito1972@hotmail.con', '00846546', 1, '2020-01-01 00:00:00'),
(491, 32, 2, 2, 'TAMAY GALLARDO LEONIDAS', '16804548', '966739360', 'leotamay22@gmail.com', '16804548', 1, '2020-01-01 00:00:00'),
(492, 2, 2, 2, 'VIDAURRE LOPEZ JULIO BELTRAN', '09965829', '966723979', 'vidaurrelopez@hotmail.com', '09965829', 1, '2020-01-01 00:00:00'),
(493, 15, 2, 2, 'SARZO DE LA CRUZ EDWIN', '46266865', '945002082', 'edwinsarzo01@gmail.com', '46266865', 1, '2020-01-01 00:00:00'),
(494, 40, 2, 2, 'RIVERA SOLARI HECTOR', '40186074', '987186238', 'riverasolari@gmail.com', '40186074', 1, '2020-01-01 00:00:00'),
(495, 31, 18, 2, 'BENDEZU BENDEZU JUAN CARLOS', '46980912', '996386829', 'a@w', '46980912', 0, '2020-01-01 00:00:00'),
(496, 31, 18, 2, 'CAMAYO DE LA CRUZ PEPE WINSTON', '15437733', '927245867', 'aries75camayodelacruz42@gmail.com', '15437733', 0, '2020-01-01 00:00:00'),
(497, 32, 18, 2, 'DAMIAN BANCES LUIS', '16784535', '956509512', 'damianjoseluis31@gmail.com', '16784535', 0, '2020-01-01 00:00:00'),
(498, 31, 18, 2, 'HIDALGO URBANO RENZO MIGUELON', '72442997', '948747943', 'hidalgourbanorenzo@gmail.com', '72442997', 0, '2020-01-01 00:00:00'),
(499, 2, 18, 2, 'HUAMANTINGO CALDERON CARLOS', '46493310', '917519626', 'carloshuamantingo28@gmail.com', '46493310', 0, '2020-01-01 00:00:00'),
(500, 2, 18, 2, 'ILIZARBE CHAMORRO JONATHAN', '47108385', '964153205', 'jkenzi.19@gmail.com', '47108385', 0, '2020-01-01 00:00:00'),
(501, 1, 1, 2, 'MARTINEZ ESPINOZA CARLOS ALBERTO', '45840227', '907039400', 'noel_181_@hotmail.com', '45840227', 1, '2020-01-01 00:00:00'),
(502, 56, 18, 2, 'PALOMINO LEON FERNANDO', '06067918', '999799261', 'topografopalomino@yahoo.es', '06067918', 1, '2020-01-01 00:00:00'),
(503, 2, 13, 2, 'ZARZOZA SARRIA DANNY ROMEL', '45221317', '942522744', 'dannyzs.dzs@gmail.com', '45221317', 1, '2020-01-01 00:00:00'),
(504, 15, 2, 2, 'TEJADA TAVARA DERMALI', '45305558', '956836278', 'tejadatavarad@gmail.com', '45305558', 1, '2020-01-01 00:00:00'),
(505, 31, 2, 2, 'TADEO GUTIERREZ ELIO AMADOR', '44604671', '942854529', 'Tadeoelio2@gmail.com', '44604671', 0, '2020-01-01 00:00:00'),
(506, 64, 2, 2, 'SERNAQUE HONORIO STALIN', '47379536', '968140173', 'stalin_m_50@hotmail.com', '47379536', 0, '2020-01-01 00:00:00'),
(507, 31, 18, 2, 'ACHO CASTILLO EDGAR AUGUSTO', '47578894', '995046830', 'sublime1522@gmail.com', '47578894', 0, '2020-01-01 00:00:00'),
(508, 31, 18, 2, 'ACHO CASTILLO ERIXANDRO', '47436995', '984296337', 'dylanacho26@gmail.com', '47436995', 0, '2020-01-01 00:00:00'),
(509, 31, 18, 2, 'AGÜERO VARA MANOLO WILLIAM', '46186660', '1', 'a@w', '46186660', 1, '2020-01-01 00:00:00'),
(510, 31, 18, 2, 'ALVARADO TARAZONA CRISTOPHER LEONEL', '74378947', '932046241', 'leonelo1995@hotmail.com', '74378947', 0, '2020-01-01 00:00:00'),
(511, 31, 18, 2, 'AVILA MEDINA LUZ VERONICA', '47798023', '983068357', 'a@w', '47798023', 0, '2020-01-01 00:00:00'),
(512, 31, 18, 2, 'CANDELA RONDON ELIZABETH', '70908955', '969731318', 'elizacandela10@gmail.com', '70908955', 0, '2020-01-01 00:00:00'),
(513, 31, 18, 2, 'CHINGUEL SANTILLAN VICTOR HUGO', '43705955', '1', 'a@w', '43705955', 1, '2020-01-01 00:00:00'),
(514, 2, 18, 2, 'CORDOVA TOCTO JOSE ARMANDO', '43579559', '914134866', 'cordovajt222@gmail.com', '43579559', 0, '2020-01-01 00:00:00'),
(515, 31, 18, 2, 'DOMINGO ACEBEDO MELINA ROSALIA', '80899207', '989413438', 'a@w', '80899207', 0, '2020-01-01 00:00:00'),
(516, 31, 18, 2, 'LOPEZ JAVIER KENEDY JOVAN', '47724384', '902341722', 'lopezjavierkennedy@gmail.com', '47724384', 1, '2020-01-01 00:00:00'),
(517, 31, 18, 2, 'NORABUENA SANCHEZ XAVIER CHARLIE', '41240293', '923733278', 'a@w', '41240293', 1, '2020-01-01 00:00:00'),
(518, 32, 18, 2, 'PAIMA AYACHI JORGE WASHINTON', '47064855', '1', 'a@w', '47064855', 1, '2020-01-01 00:00:00'),
(519, 31, 18, 2, 'RONDON BAUTISTA MIRTA LILE', '15433150', '1', 'a@w', '15433150', 0, '2020-01-01 00:00:00'),
(520, 2, 18, 2, 'SOLORZANO ROBLES ROMMEL JAVIER', '45701054', '1', 'a@w', '45701054', 1, '2020-01-01 00:00:00'),
(521, 31, 18, 2, 'SUAREZ LIZANA SAUL', '46990385', '989826351', 'a@w', '46990385', 0, '2020-01-01 00:00:00'),
(522, 31, 27, 2, 'TENORIO CARRERA BRYAN ALBERTO', '60698018', '998246915', 'Brayantcarrera15@gmail.com', '60698018', 1, '2020-01-01 00:00:00'),
(523, 1, 2, 2, 'SARZO DE LA CRUZ OMAR ISAIAS', '73795521', '912969886', 'omarsarzo132@gmail.com', '73795521', 1, '2020-01-01 00:00:00'),
(524, 65, 18, 2, 'CORONADO RUIZ PRISCILLA VERONICA', '41184766', '976349898', 'prissi2911@gmail.com', '41184766', 0, '2020-01-01 00:00:00'),
(525, 35, 2, 2, 'SOTO ANTICONA', '10806230', '952759236', 'ericedu35@gmail.com@x', '10806230', 1, '2020-01-01 00:00:00'),
(526, 4, 5, 3, 'TARAZONA REYES JORGE', '10747552', '963772148', 'Jtarazona@grupoinversionesgyc.pe', '10747552', 1, '2020-01-01 00:00:00'),
(527, 2, 18, 2, 'MONTERO ARANCEL ROBERT EDDY', '43924737', '902949993', 'monteroarancel@gmail.com', '43924737', 1, '2020-01-01 00:00:00'),
(528, 2, 13, 2, 'BARRETO CAMPOVERDE  PEDRO', '03829259', '980437864', 'x@x', '03829259', 1, '2020-01-01 00:00:00'),
(529, 100, 31, 3, 'CACHO MICHELENA RICARDO', '46253484', '940241523', 'rcachomichelena@gmail.com', '46253484', 1, '2020-01-01 00:00:00'),
(530, 9, 9, 2, 'MARIN ORTIZ HILMER', '70111375', '975324672', 'hilmerjoel@gmail.com', '70111375', 1, '2020-01-01 00:00:00'),
(531, 4, 13, 3, 'NAVARRO MORI ROBERTO', '25703476', '962105544', 'roberto.nava.mo@hotmail.co', '25703476', 1, '2020-01-01 00:00:00'),
(532, 91, 2, 3, 'RICALDI MEZA HILMER HONORATO', '20053323', '966272943', 'hricaldi@arceperu.pe', '20053323', 1, '2020-01-01 00:00:00'),
(533, 74, 31, 2, 'ACAPANA ACHIRE ALFREDO ISIDRO', '09410758', '986170168', 'alfredoacapana@gmail.com', '09410758', 0, '2020-01-01 00:00:00'),
(534, 32, 31, 2, 'AYALA RAMOS ERICK VIDAL', '43869082', '941577305', 'erickayala271@gmail.com', '43869082', 1, '2020-01-01 00:00:00'),
(535, 32, 18, 2, 'BENITES FLORES JESUS MARCELINO', '41149319', '1', 'a@w', '41149319', 1, '2020-01-01 00:00:00'),
(536, 32, 31, 2, 'CANDIA OVIEDO FERNANDO CELSO', '24287137', '958875948', 'fernandocandiaoviedo@gmail.com', '24287137', 1, '2020-01-01 00:00:00'),
(537, 43, 18, 2, 'CARUAJULCA BRAVO CELSO', '27425285', '980963576', 'Celsocaruajulcabravo@gmail.com', '27425285', 0, '2020-01-01 00:00:00'),
(538, 31, 18, 2, 'NOREÑA PEÑA ISAAC', '47318700', '928771112', 'Isaac.np1991@gmail.com', '47318700', 0, '2020-01-01 00:00:00'),
(539, 32, 18, 2, 'PANDURO HERRERA JOSE ALBERTO', '80374966', '994197796', 'a@w', '80374966', 0, '2020-01-01 00:00:00'),
(540, 32, 18, 2, 'PEÃ‘A SANTA CRUZ JOHN WEBSTER', '48210473', '921040201', 'johnpsc.17@gmail.com', '48210473', 0, '2020-01-01 00:00:00'),
(541, 32, 27, 2, 'SANTIAGO RUIZ JUAN JOSE', '44293244', '957042740', 'a@w', '44293244', 1, '2020-01-01 00:00:00'),
(542, 32, 18, 2, 'TAPIA GONZALES MILTON', '41477040', '1', 'miltontapiagonzales37@gmail.com', '41477040', 0, '2020-01-01 00:00:00'),
(543, 4, 3, 2, 'JOCELYN DIANELLA TORRES GUERRA', '72132852', '965935326', 'jguerra@grupoinversionesgyc.pe', 'CEMENTERIO', 0, '2020-01-01 00:00:00'),
(544, 42, 2, 3, 'YENY YOVANA RIOS QUISPE', '46251242', '972708238', 'yenirios.q@gmail.com', '46251242', 0, '2020-01-01 00:00:00'),
(545, 42, 2, 2, 'TIPACTI GUTIERREZ CESAR AUGUSTO ', '46664676', '940347295', 'ctipacti@grupoinversionesgyc.pe', '46664676', 0, '2020-01-01 00:00:00'),
(546, 42, 3, 2, 'ROJAS PECHO VICTOR RAUL', '43486738', '942785413', 'victor1986.vrp@gmail.com', '43486738', 0, '2020-01-01 00:00:00'),
(547, 96, 31, 1, 'ROSA MUCHA YALO', '42132903', '997599712', 'rmucha@grupoinversionesgyc.pe', '42132903', 1, '2020-01-01 00:00:00'),
(548, 2, 2, 2, 'GOMEZ PINEDO MANUEL CRECENCIO', '10161414', '956057455', '956057455manuelgomezpinedo@gmail.com', '10161414', 0, '2020-01-01 00:00:00'),
(549, 42, 18, 2, 'PINO HUAMAN LESLY MILDRED', '72421109', '993320254', 'lpino@grupoinversionesgyc.pe', '72421109', 0, '2020-01-01 00:00:00'),
(550, 42, 18, 4, 'DAZA ALVAREZ JOSE ENRIQUE', '41790124', '940346906', 'jdaza1223@gmail.com', '41790124', 1, '2020-01-01 00:00:00'),
(551, 42, 18, 4, 'AGUILAR GIL JOSE MIGUEL', '45798292', '994772191', 'Jaguilar@civelmec.com.pe', '45798292', 1, '2020-01-01 00:00:00'),
(552, 17, 18, 4, 'CASTILLA MAYORGA ELISA CRISTINA', '09426870', '939912426', 'ecastilla@jg3construcciones.c', '09426870', 1, '2020-01-01 00:00:00'),
(553, 1, 18, 4, 'SALAZAR OBREGON RAUL', '09557108', '902523476', 'Salazarobregonraul7@gmail.com', '09557108', 1, '2020-01-01 00:00:00'),
(554, 3, 18, 2, 'DIAZ CHAVEZ JEAN CARLOS', '47978458', '925367995', 'jeandiaz4797@gmail.com', '47978458', 0, '2020-01-01 00:00:00'),
(555, 64, 18, 2, 'CAMPOS GARCIA LELIS', '42221307', '968172665', 'Lcg-campos-23@gimail.com', '42221307', 0, '2020-01-01 00:00:00'),
(556, 1, 18, 2, 'AYALA CHATE JHON PEDRO', '40583874', '970933441', 'jhonayala2478@gmail.com', '40583874', 0, '2020-01-01 00:00:00'),
(557, 67, 18, 2, 'BUJAICO QUINTANA NOE', '47619115', '1', 'A@Q', '47619115', 0, '2020-01-01 00:00:00'),
(558, 68, 18, 2, 'CHAVEZ SARMIENTO LUCIO WILFREDO', '08985353', '1', 'A@Q', '08985353', 1, '2020-01-01 00:00:00'),
(559, 68, 18, 2, 'CHUCHON BERROCAL DAVID', '80231384', '944450031', 'A@Q', '80231384', 1, '2020-01-01 00:00:00'),
(560, 68, 18, 2, 'HUACACHI HUAYTA JUAN JOSÉ', '09843178', '1', 'A@Q', '09843178', 1, '2020-01-01 00:00:00'),
(561, 68, 18, 2, 'MEZA AMADO NELDO CUELLER', '10001750', '949508923', 'neldomeza75@gmail.com', '10001750', 1, '2020-01-01 00:00:00'),
(562, 68, 18, 2, 'ROMERO QUISPE JHOSEP DENNIS', '46038993', '1', 'A@Q', '46038993', 0, '2020-01-01 00:00:00'),
(563, 69, 18, 2, 'TIPO LOPEZ OMAR SALVADOR', '43550123', '1', 'A@Q', '43550123', 1, '2020-01-01 00:00:00'),
(564, 70, 18, 2, 'DIAZ GUERRA ELMER', '45087407', '1', 'A@Q', '45087407', 1, '2020-01-01 00:00:00'),
(565, 70, 18, 2, 'ESPINOZA BERROCAL GILBERT ANTONIO', '10008378', '922849057', 'A@Q', '10008378', 1, '2020-01-01 00:00:00'),
(566, 32, 18, 2, 'CUEVA VERA JESUS JUSTO', '40032178', '949152742', 'jcv_15_sagirario@hotmail.com', '40032178', 0, '2020-01-01 00:00:00'),
(567, 32, 18, 2, 'ENRIQUEZ CASTAÑEDA JORGE LUIS', '46135288', '972123353', 'Jorgekoky1990@gmail', '46135288', 0, '2020-01-01 00:00:00'),
(568, 32, 18, 2, 'GONZALES ORTIZ JORGE MICHEL', '29681632', '941087951', 'michelgonzaleso@gmail.com', '29681632', 0, '2020-01-01 00:00:00'),
(569, 31, 18, 2, 'HUAMANI CACERES CARLOS ENRIQUE', '29656651', '960625853', 'Carloenrrique29@gmail.com', '29656651', 0, '2020-01-01 00:00:00'),
(570, 32, 31, 2, 'JAUREGUI ACAPANA LUIS ALBERTO', '41961242', '991856206', 'luisjaureguiacapana@gmail.com', '41961242', 1, '2020-01-01 00:00:00'),
(571, 32, 18, 2, 'PALOMINO ZEVALLOS TONI JORGE', '0925975', '1', 'a@q', '09259758', 1, '2020-01-01 00:00:00'),
(572, 40, 18, 2, 'ALIAGA VILA ALEX', '41689957', '1', 'a@q', '41689957', 0, '2020-01-01 00:00:00'),
(573, 32, 18, 2, 'CRUZADO ALZUGARAY ROBERT', '25715226', '923009724', 'Roberto.cruzado@gmail.com', '25715226', 0, '2020-01-01 00:00:00'),
(574, 32, 18, 2, 'CUBA GOMEZ JOHN EDWARD', '40653380', '955192911', 'Cubagomezj@gmail.com', '40653380', 0, '2020-01-01 00:00:00'),
(575, 32, 18, 2, 'MOCARRO PIZARRO JULIO GILBERTO', '10077091', '993785573', 'roxana.mh4@hotmail.com', '10077091', 0, '2020-01-01 00:00:00'),
(576, 32, 2, 2, 'RIVERA ESCOVEDO MICHAEL JACKSON', '45896090', '900484933', 'Mjre0877@gmail.com', '45896090', 0, '2020-01-01 00:00:00'),
(577, 32, 18, 2, 'DÃAZ DAVILA OMAR CRISTIAN', '44687281', '910156431', 'omardiazdavila0@gmail.com', '44687281', 0, '2020-01-01 00:00:00'),
(578, 43, 18, 4, 'DE LA CRUZ TASAYCO RICHARD', '46328395', '972822310', 'richard.delacruz@civelmec.com.pe', '46328395', 1, '2020-01-01 00:00:00'),
(579, 7, 18, 2, 'ESPINOZA CAMONES LIZARDO', '42281379', '962507743', 'Lizardocamones@gmail.com', '42281379', 0, '2020-01-01 00:00:00'),
(580, 32, 18, 2, 'FURUGEN MATOS ANGELO JOEL', '48790504', '994013989', 'Angelojoelfurugenmartos@gmail.com', '48790504', 0, '2020-01-01 00:00:00'),
(581, 32, 18, 2, 'PAIMA MACAHUACHI JULIO CESAR', '0019143', '1', 'a@q', '0019143', 1, '2020-01-01 00:00:00'),
(582, 32, 18, 2, 'PAIMA MACAHUACHI CARLOS JAVIER', '10208124', '995940279', 'Paimacarlos111@gmail.com', '10208124', 0, '2020-01-01 00:00:00'),
(583, 32, 36, 2, 'PARDAVE MALLMA MIGUEL ANGEL', '10562202', '927034649', 'miguelparave10@gmail.com', '10562202', 1, '2020-01-01 00:00:00'),
(584, 32, 18, 2, 'GARABITO NEVADO CESAR AUGUSTO', '16714397', '991602301', 'a@w', '16714397', 0, '2020-01-01 00:00:00'),
(585, 32, 18, 2, 'RODRIGUEZ LLACTAHUAMAN SIDNEY MAGYER', '46951010', '996911719', 'a@q', '46951010', 0, '2020-01-01 00:00:00'),
(586, 1, 45, 2, 'VÃSQUEZ GUTIERREZ JHON FREDDY', '48649689', '921508663', 'jhonv2461@gmail.com', '48649689', 1, '2020-01-01 00:00:00'),
(587, 1, 27, 2, 'RIVERA ZURITA JOSE LUIS', '70245192', '921792515', 'riverazuritajoseluisvalentin@gmail.com', '70245192', 1, '2020-01-01 00:00:00'),
(588, 31, 18, 2, 'ANTAHURCO ESPINOZA EDSON ALCINDO', '75854015', '928420921', 'antahurcoespinozaedson62@gmail.com', '75854015', 0, '2020-01-01 00:00:00'),
(589, 32, 18, 2, 'PINEDO TRUJILLO ANDY GUALBERTO', '07630004', '998321457', 'pinedoandy364@gmail.com', '07630004', 0, '2020-01-01 00:00:00'),
(590, 32, 18, 2, 'CASTILLO LEON SIDNEY GEORGE', '32125624', '984676778', 'Sidney.castillo.leon@gmail.co', '32125624', 0, '2020-01-01 00:00:00'),
(591, 31, 18, 2, 'FLORES VILCHEZ JHONY', '45624478', '902040857', 'Jhonnyfloresvilchez88@gmail.com', '45624478', 0, '2020-01-01 00:00:00'),
(592, 31, 18, 2, 'CARDENAS CHIROQUE PERCY MAX', '40783642', '1', 'a@q', '40783642', 0, '2020-01-01 00:00:00'),
(593, 31, 18, 2, 'LOPEZ ROJAS ROXINALDO', '76865571', '1', 'a@q', '76865571', 0, '2020-01-01 00:00:00'),
(594, 3, 18, 2, 'MIO BARRERA JUAN JOSE JOEL', '73109383', '981025470', 'aqu', '73109383', 0, '2020-01-01 00:00:00'),
(595, 3, 18, 2, 'YANAMA TUCNO EDMER', '44180427', '1', 'a@q', '44180427', 0, '2020-01-01 00:00:00'),
(596, 32, 27, 2, 'PALOMINO RENGIFO FRANK ERICK', '47067838', '922756584', 'Fk.palomino31@gmail.com', '47067838', 1, '2020-01-01 00:00:00'),
(597, 32, 28, 2, 'TORRES BAUTISTA MIGUEL ANGEL', '48496128', '974451945', 'torresbautistam490@gmail.com', '48496128', 1, '2020-01-01 00:00:00'),
(598, 32, 31, 2, 'ODAR PERES CARLOS GREGORY', '72505767', '906774186', 'a@q', '72505767', 1, '2020-01-01 00:00:00'),
(599, 40, 18, 2, 'LIZANA LUQUE PEDRO', '09661800', '1', 'a@q', '09661800', 0, '2020-01-01 00:00:00'),
(600, 40, 18, 2, 'MORALES OLIVERA JHOEL MISAEL', '43118844', '1', 'a@q', '43118844', 0, '2020-01-01 00:00:00'),
(601, 40, 18, 2, 'ESPINOZA VERAMENDI DANIEL ALEJANDRO', '46322846', '1', 'a@q', '46322846', 0, '2020-01-01 00:00:00'),
(602, 68, 18, 2, 'JOYO MATAMOROS FREDY', '10607517', '1', 'a@q', '10607517', 1, '2020-01-01 00:00:00'),
(603, 68, 18, 2, 'VALDIVIA LUJAN ANDRES', '10288906', '1', 'a@w', '10288906', 1, '2020-01-01 00:00:00'),
(604, 71, 18, 2, 'ARCO TAYPE SANTOSA MERCED', '0685369', '1', 'A@Q', '06853696', 1, '2020-01-01 00:00:00'),
(605, 69, 18, 2, 'ARAPA ARAPA WILBER ARMANDO', '45493506', '923820858', 'a@q', '45493506', 1, '2020-01-01 00:00:00'),
(606, 31, 18, 2, 'APAGUEÑO ASPAJO JACKSON ARMANDO', '41799729', '960269631', 'Armandoapagueno133@gmail.com', '41799729', 0, '2020-01-01 00:00:00'),
(607, 32, 18, 2, 'FLORES JUARES WILMER MARTIN', '44913471', '939816588', 'Wilmerfloresjuarez@gmail.com', '44913471', 0, '2020-01-01 00:00:00'),
(608, 32, 18, 2, 'QUISPE CHICNES GUZMAN', '10330888', '993397391', 'a@q', '10330888', 0, '2020-01-01 00:00:00'),
(609, 32, 18, 2, 'LOZANO VELIZ EDGAR JINO', '43957726', '1', 'a@q', '43957726', 0, '2020-01-01 00:00:00'),
(610, 68, 18, 2, 'CASAVERDE MENDEZ WILLIAM', '28299363', '1', 'a@q', '28299363', 1, '2020-01-01 00:00:00'),
(611, 72, 18, 4, 'CAMPOS MARTINEZ MYRIAM LAURA', '46113822', '1', 'mcampos@civelmec.com.pe', '46113822', 1, '2020-01-01 00:00:00'),
(612, 4, 18, 4, 'MAYTA POSADA JOAN SEBASTIAN', '47946336', '1', 'A@Q', '47946336', 1, '2020-01-01 00:00:00'),
(613, 67, 18, 2, 'ASTUPIÑAN DEL VALLE JEREMIAS', '74553169', '1', 'A@Q', '74553169', 1, '2020-01-01 00:00:00'),
(614, 40, 18, 2, 'RAUL MAIZ REQUENO', '43177534', '983224095', 'Raul.maiz2019@gmail.com', '43177534', 1, '2020-01-01 00:00:00'),
(615, 40, 13, 2, 'PARDO CHURASI WILBERT ANTHONY', '47932650', '936053966', 'Antonypch@', '47932650', 1, '2020-01-01 00:00:00'),
(616, 40, 13, 2, 'GIRALDEZ PAREDES JOEL JOSE', '41909359', '932476673', 'Josegiraldezz@hotmail.com', '41909359', 1, '2020-01-01 00:00:00'),
(617, 5, 18, 4, 'HUAROTO PARDO LUIS ALFREDO', '70068140', '940164932', 'luis_alfredo1189@hotmail.com', '70068140', 1, '2020-01-01 00:00:00'),
(618, 3, 2, 2, 'CARHUARICRA ESPINOZA JONATHAN', '74123718', '972493978', 'Jonavii060@gmail.com', '74123718', 0, '2020-01-01 00:00:00'),
(619, 2, 2, 2, 'VILLANUEVA GONZALES, MOISES', '44444444', '992930756', 'nitles.freestyle@gmail.com', '4444444444', 0, '2020-01-01 00:00:00'),
(620, 72, 18, 4, 'RAMOS SICHA JOSE LUIS', '44544018', '945586291', 'jl.ramos766@gmail.com', '44544018', 1, '2020-01-01 00:00:00'),
(621, 17, 2, 4, 'GALLARDO PINEDO DIEGO ENRIQUE', '47566118', '957679250', 'dgallardo@grupoinversionesgyc.pe', '47566118', 1, '2020-01-01 00:00:00'),
(622, 40, 18, 2, 'ESPINOZA PABLO TIBERIO', '07989416', '1', 'a@w', '07989416', 0, '2020-01-01 00:00:00'),
(623, 40, 18, 2, 'MULLO SANCHEZ MARIO ALFONSO', '28237416', '999339566', 'Mariomullos@gmail.com', '28237416', 0, '2020-01-01 00:00:00'),
(624, 2, 18, 2, 'CAYO GONZALES CARLOS ALBERTO', '08669444', '1', 'a@w', '08669444', 0, '2020-01-01 00:00:00'),
(625, 32, 18, 2, 'PAIMA MATAHUASI JULIO CESAR', '00191430', '193972732', 'Paimajulio0615@gmail.com', '00191430', 0, '2020-01-01 00:00:00'),
(626, 3, 18, 2, 'CASTILLO URBANO JHOANS', '70598208', '983983446', 'A@Q', '70598208', 0, '2020-01-01 00:00:00'),
(627, 40, 18, 2, 'TELLO CHUPILLON ELMER', '41862320', '929523400', 'Elmer_tello125@hotmail.com', '41862320', 0, '2020-01-01 00:00:00'),
(628, 40, 18, 2, 'SANCHEZ REQUEJO JOSE CARLOS', '45850308', '995857919', 'Jcsanchezrequejo@gmail.com', '45850308', 0, '2020-01-01 00:00:00'),
(629, 40, 18, 2, 'MORALES OLIVERA LUIS RUFINO', '41103578', '925398053', 'A@W', '41103578', 0, '2020-01-01 00:00:00'),
(630, 3, 18, 2, 'VASQUEZ FLORES GUILLERMO', '44532793', '958663950', 'vasquezfloresguillermo61@gmail.com', '44532793', 0, '2020-01-01 00:00:00'),
(631, 16, 19, 2, 'GABRIELA ASHCALLAY', '48164679', '993451291', 'gabriela.ashcallay140394@gmail.com', '48164679', 1, '2020-01-01 00:00:00'),
(632, 29, 25, 2, 'RIOS COLLAO MIKHAEL ANDERSON', '45590427', '965973268', 'arios.gyc@gmail.com', '45590427', 0, '2020-01-01 00:00:00'),
(633, 32, 18, 2, 'ALVAREZ QUINTO HENRY ABEL', '09764107', '991118912', 'h3nry_ab3l@hotmail.com', '09764107', 0, '2020-01-01 00:00:00'),
(634, 32, 18, 2, 'ABREGO VEGA JOSE SANTOS', '25814399', '936544453', 'Abregovega1976@gmail.com', '25814399', 0, '2020-01-01 00:00:00'),
(635, 32, 18, 2, 'CARCAUSTO ALVARADO ALFREDO DAVID', '44866616', '933741208', 'Davidcarcausto@gmail.com.pe', '44866616', 0, '2020-01-01 00:00:00'),
(636, 31, 18, 2, 'CASTILLO ANDRADE PABLO FERNANDO', '47750031', '912579736', 'pablosaid58@gmail.com', '47750031', 1, '2020-01-01 00:00:00'),
(637, 31, 18, 2, 'CHAVEZ CUTIPA BENJAMIM RODRIGO', '73415842', '959735638', 'Mikael.2016.24@gmail.com', '73415842', 0, '2020-01-01 00:00:00'),
(638, 31, 18, 2, 'PIZANGO VALLES NESTOR', '23014352', '1', 'a@w', '23014352', 0, '2020-01-01 00:00:00'),
(639, 32, 18, 2, 'TINCO LEO FREDDY ERNESTO', '46168150', '1', 'a@w', '46168150', 0, '2020-01-01 00:00:00'),
(640, 32, 18, 2, 'VILCA VARGAS YONEY', '10008505', '1', 'a@w', '10008505', 0, '2020-01-01 00:00:00'),
(641, 40, 18, 2, 'FALCON PELAEZ ELTON GIANCARLO', '44120949', '930888498', 'a@w', '44120949', 0, '2020-01-01 00:00:00'),
(642, 38, 21, 2, 'MARTINEZ ESPINOZA JORDI JUNIOR', '48216742', '937804435', 'jordijuniormartinez@gmail.com', '48216742', 1, '2020-01-01 00:00:00'),
(643, 32, 18, 2, 'CHAVEZ SIPIRAN IVAN', '40860920', '927106194', 'a@w', '40860920', 0, '2020-01-01 00:00:00'),
(644, 2, 18, 2, 'NINA CONDORI FREDY', '09724228', '1', 'a@q', '09724228', 1, '2020-01-01 00:00:00'),
(645, 72, 18, 4, 'SANTOS PORRAS JHONATAN', '43832009', '948082522', 'Jhonatan6466@gmail.com', '43832009', 1, '2020-01-01 00:00:00'),
(646, 2, 18, 2, 'SOTO CABALLERO NIXON RUBEN', '46617361', '928926171', 'Nixoscab@gmail.com', '46617361', 1, '2020-01-01 00:00:00'),
(647, 31, 2, 2, 'ALIAGA MANRIQUE MIGUEL ANGEL', '74284635', '916691643', 'miguelaliaga2441998@gmail.com', '74284635', 0, '2020-01-01 00:00:00'),
(648, 31, 2, 2, 'PERALTA TERRONES JOE ALEXANDER', '42332496', '963479610', 'Joe_petalta34@hotmail.com', '42332496', 0, '2020-01-01 00:00:00'),
(649, 32, 2, 2, 'MARTINEZ PIZARRO JULIO CESAR', '40096123', '925900528', 'Csar_788@hotmail.com', '40096123', 0, '2020-01-01 00:00:00'),
(650, 64, 2, 2, 'MUNAYCO CASTRO ELI DANIEL', '40449672', '981453404', 'macajumunayco@gmail.com', '40449672', 0, '2020-01-01 00:00:00'),
(651, 31, 18, 2, 'AGUILAR MEZA MEYLAN DEL ROSARIO', '81400583', '1', 'a@q', '81400583', 0, '2020-01-01 00:00:00'),
(652, 31, 18, 2, 'ATENCIO MARQUINA MARIA ISABEL ALEXANDRA', '46104383', '1', 'a@w', '46104383', 0, '2020-01-01 00:00:00'),
(653, 64, 18, 2, 'CAMPOS GARCIA JOSE', '41626916', '1', 'a@q', '41626916', 0, '2020-01-01 00:00:00'),
(654, 32, 18, 2, 'CHINCHAY LLIUYA ALDO BASILIO', '44761011', '1', 'a@w', '44761011', 0, '2020-01-01 00:00:00'),
(655, 31, 18, 2, 'MANRIQUE TORRES EDGAR', '40004595', '1', 'a@w', '40004595', 0, '2020-01-01 00:00:00'),
(656, 31, 18, 2, 'URBANO GOYA JUAN RODRIGO', '74496355', '1', 'a@w', '74496355', 0, '2020-01-01 00:00:00'),
(657, 31, 18, 2, 'YACTAYO APOLINARIO YESHENYA MEILY', '76519332', '1', 'a@w', '76519332', 0, '2020-01-01 00:00:00'),
(658, 31, 18, 2, 'AGUILAR HUAMÁN JUAN CARLOS', '41304518', '900728007', 'aguilarhuamanj@gmail.com', '41304518', 1, '2020-01-01 00:00:00'),
(659, 31, 18, 2, 'BECERRA VALDEZ JULIO BRAYAN', '74733599', '1', 'a@w', '74733599', 1, '2020-01-01 00:00:00'),
(660, 68, 18, 2, 'BULEJE CORDOVA BENJAMIN MANUEL', '09395618', '1', 'a@w', '09395618', 1, '2020-01-01 00:00:00'),
(661, 68, 18, 2, 'BUSTAMANTE CONDORI URBANO', '42970250', '1', 'a@w', '42970250', 1, '2020-01-01 00:00:00'),
(662, 31, 18, 2, 'CARHUANCHO PAUCAR OSCAR JESUS', '08954952', '988953494', 'Oscar_manchitas@hotmail', '08954952', 1, '2020-01-01 00:00:00'),
(663, 31, 18, 2, 'CHULLUNQUIA PAICO EDUARDO', '24873845', '978963828', 'eduardochullunquiasaico@gm', '24873845', 1, '2020-01-01 00:00:00'),
(664, 71, 18, 2, 'CORONEL DIAS JUAN DAYMI', '41847203', '965462101', 'Coroneldiazjuandaymi@gmail', '41847203', 1, '2020-01-01 00:00:00'),
(665, 3, 27, 2, 'GOMEZ ORTOGORIN OSCAR', '09774588', '973139578', 'oscargomez290572@hotmail.com', '09774588', 1, '2020-01-01 00:00:00'),
(666, 68, 18, 2, 'GONZALES SAYAGO RICARDO', '09491991', '1', 'a@w', '09491991', 1, '2020-01-01 00:00:00'),
(667, 31, 18, 2, 'GRANDEZ NAPAN CELSO', '76846759', '76846759', 'Grandezcelso@gmail.com', '76846759', 1, '2020-01-01 00:00:00'),
(668, 32, 18, 2, 'GUTIERREZ GAGO RUDY', '40360908', '976960683', 'rudygutierrez.g@hotmail.com', '40360908', 1, '2020-01-01 00:00:00'),
(669, 3, 27, 2, 'LLANOS ASCANIO MIGUEL ANGEL', '77543255', '932347054', 'm77543255@gmail.com', '77543255', 1, '2020-01-01 00:00:00'),
(670, 31, 18, 2, 'LLANOS FERNANDEZ CARLOS EDUARDO', '72148002', '1', 'a@w', '72148002', 0, '2020-01-01 00:00:00'),
(671, 31, 18, 2, 'MENDOZA GALINDO MIGUEL ANGEL', '10118384', '943474885', 'a@w', '10118384', 1, '2020-01-01 00:00:00'),
(672, 68, 18, 2, 'MENDOZA GONZALES JUAN CARLOS', '44510672', '1', 'a@w', '44510672', 1, '2020-01-01 00:00:00'),
(673, 31, 18, 2, 'NAPAN SANCHEZ MITCHELL HERNAN', '77066438', '1', 'a@w', '77066438', 0, '2020-01-01 00:00:00'),
(674, 67, 18, 2, 'OBALDO MEJIA ERNESTO', '43226940', '1', 'a@w', '43226940', 1, '2020-01-01 00:00:00'),
(675, 31, 18, 2, 'PARIONA INCHI LUIS BRYAN', '77051834', '917980757', 'Luisalonso9788@gmail.com', '77051834', 0, '2020-01-01 00:00:00'),
(676, 31, 18, 2, 'QUIROZ FAJARDO ERICK', '72894059', '923904914', 'erickquiroz1416@gmail.com', '72894059', 1, '2020-01-01 00:00:00'),
(677, 31, 18, 2, 'QUISPE FERNANDEZ ROBER', '47258197', '956252621', 'roberquispefernandez91@gmail.com', '47258197', 1, '2020-01-01 00:00:00'),
(678, 32, 18, 2, 'ROMAYNA BARBOZA CARLOS', '00113041', '971612550', 'Amixlindo.1974@gmail.com', '00113041', 1, '2020-01-01 00:00:00'),
(679, 72, 18, 4, 'SANCHEZ RAMOS LUIS ALEJANDRO', '46434401', '1', 'lsanchez@civelmec.com.pe', '46434401', 1, '2020-01-01 00:00:00'),
(680, 32, 18, 2, 'VASQUEZ ECHEA BRANDON MAYK', '75243504', '993457031', 'a@w', '75243504', 0, '2020-01-01 00:00:00'),
(681, 30, 3, 1, 'BALVIN TOLEDO DAYSSI IVETH', '47654048', '935115035', 'dbalvin@arceperu.pe', '47654048', 1, '2020-01-01 00:00:00'),
(682, 2, 27, 2, 'CUNYAS GABRIEL PERCY', '41825933', '930155970', 'Percycg_09@hotmail.com ', '41825933', 1, '2020-01-01 00:00:00'),
(683, 2, 18, 2, 'ABANTO SOTO LUIS', '41418175', '922209375', 'A@W', '41418175', 1, '2020-01-01 00:00:00'),
(684, 1, 18, 2, 'PALOMINO ZEBALLOS TONI JORGE', '09259758', '1', 'A@W', '09259758', 0, '2020-01-01 00:00:00'),
(685, 1, 18, 2, 'GARCIA PEREDA JUAN AUSBERTO', '09122308', '1', 'A@W', '09122308', 0, '2020-01-01 00:00:00'),
(686, 1, 18, 2, 'LLANOS PEREGRINO JOSE', '26679529', '1', 'A@W', '26679529', 0, '2020-01-01 00:00:00'),
(687, 31, 18, 2, 'MORI ALTAMIRANO EDGAR', '75562728', '1', 'A@W', '75562728', 0, '2020-01-01 00:00:00'),
(688, 68, 18, 2, 'BENITES LAZO GABRIEL', '10683112', '1', 'A@W', '10683112', 0, '2020-01-01 00:00:00'),
(689, 68, 18, 2, 'CALDERON RIVERA LUIS ANTONIO', '44302774', '1', 'A@W', '44302774', 0, '2020-01-01 00:00:00'),
(690, 68, 18, 2, 'CALDERON VALDIVIEZO LUIS ANTONIO', '25648894', '1', 'A@W', '25648894', 0, '2020-01-01 00:00:00'),
(691, 68, 18, 2, 'HUAMANI GONZALES RAUL', '10649071', '1', 'A@W', '10649071', 0, '2020-01-01 00:00:00'),
(692, 68, 18, 2, 'MALPARTIDA VILLAVICENCIO LIDER BENIGNO', '08349544', '1', 'A@W', '08349544', 0, '2020-01-01 00:00:00'),
(693, 68, 18, 2, 'ODAR MURILLO ANGEL', '03373427', '1', 'A@W', '03373427', 0, '2020-01-01 00:00:00'),
(694, 68, 18, 2, 'SALAZAR BANDA RICARDO EUGENIO', '16621135', '1', 'A@W', '16621135', 0, '2020-01-01 00:00:00'),
(695, 68, 18, 2, 'TUNQUIPA CONDORI JULIAN', '40180248', '930414157', 'Juliantunquipa079@gmail.com', '40180248', 0, '2020-01-01 00:00:00'),
(696, 68, 18, 2, 'VEGA CATPO LUIS ALBERTO', '76676960', '1', 'A@W', '76676960', 0, '2020-01-01 00:00:00'),
(697, 68, 18, 2, 'VEGA DETT HITLER', '47137375', '1', 'A@W', '47137375', 0, '2020-01-01 00:00:00'),
(698, 68, 18, 2, 'VEGA VASQUEZ CLEIVER MARTIN', '73365988', '1', 'A@W', '73365988', 0, '2020-01-01 00:00:00'),
(699, 1, 13, 2, 'CHURASI HUAMANI JUAN ZENON', '06878125', '947785092', 'churasi01.mantenimiento@gmail.com', '06878125', 1, '2020-01-01 00:00:00'),
(700, 1, 2, 2, 'BALDEON QUISPE JESUS MISAEL', '41270355', '989649075', 'Jesus_angel_6@hitmail.com', '41270355', 0, '2020-01-01 00:00:00'),
(701, 1, 2, 2, 'APAZA TAIPE MARCOS CESAR', '43009412', '961195260', 'marcoscesarapazataipe@gmail.com', '43009412', 0, '2020-01-01 00:00:00'),
(702, 3, 18, 2, 'GOMEZ OROTGORIN OSCAR', '09377458', '1', 'A@W', '09377458', 0, '2020-01-01 00:00:00'),
(703, 3, 2, 2, 'TANTALEAN QUIROZ MAXIMO', '43391986', '964306081', 'tantaleanquirozjosemaximo@gmail.com', '43391986', 1, '2020-01-01 00:00:00'),
(704, 1, 31, 2, 'DIAZ CHAVEZ LUIS ALBERTO', '75608554', '900282740', 'Luischavezdiaz98@gmail.com', '75608554', 1, '2020-01-01 00:00:00'),
(705, 1, 2, 2, 'VASQUEZ GUEVARA HOLDER PELAYO', '45619327', '902255499', 'hildervasquez98@gmeil.com', '45619327', 1, '2020-01-01 00:00:00'),
(706, 72, 2, 3, 'SARAVIA ULARTE ALEXANDRA MILAGROS', '41254099', '974624232', 'asaravia@grupoinversionesgyc.pe', '41254099', 0, '2020-01-01 00:00:00'),
(707, 70, 18, 2, 'BUJAICO QUINTANA NOE FABIO', '47461915', '982090392', 'A@W', '47461915', 1, '2020-01-01 00:00:00'),
(708, 3, 18, 2, 'PEREZ FLORES BRANDON LEE', '47843268', '944225135', 'brandon22pf@gmail.com', '47843268', 1, '2020-01-01 00:00:00'),
(709, 1, 18, 2, 'DEPINHO DIAZ OLGER', '40894166', '947281351', 'Olger_79depinho@hotmail.co', '40894166', 1, '2020-01-01 00:00:00'),
(710, 1, 18, 2, 'DAVILA YUNYER FRANKLIN RAMON', '46501560', '943706109', 'yunyerrd@gmail.com', '46501560', 1, '2020-01-01 00:00:00'),
(711, 3, 18, 2, 'RIOS LINARES RUDY ACCEL', '47516060', '995946838', 'Rudyriosminares@gmail.com', '47516060', 1, '2020-01-01 00:00:00'),
(712, 95, 3, 1, ' SILVA ARMAS JAMIS FRANK', '10383498', '955746091', 'jsilva@arceperu.pe', '10383498', 1, '2020-01-01 00:00:00'),
(713, 1, 2, 2, 'OLIVEIRA SINTI MAYSON JARRY', '44563565', '933630037', 'enocoliveira529@gmail.com', '44563565', 1, '2020-01-01 00:00:00'),
(714, 15, 5, 2, 'CORONADO ROJAS HECTOR JAVIER', '09981423', '997095410', 'hectorcoronadorojas@gmail.com', '09981423', 1, '2020-01-01 00:00:00'),
(715, 2, 18, 2, 'ADRIANO AGUILAR MIGUEL ANGEL', '76312097', '930658630', 'Miguel_adriano96@hotmail.es', '76312097', 1, '2020-01-01 00:00:00'),
(716, 31, 18, 2, 'GOMEZ TEJADA LUIS DAVID', '70516710', '917907144', 'davidgomez08_tejada@hotmail.com ', '70516710', 1, '2020-01-01 00:00:00'),
(717, 31, 18, 2, 'HUERTO CHUQUILIN YORDY MENLY', '48755665', '922226989', 'Yordyhuerto95@gmail.com', '48755665', 1, '2020-01-01 00:00:00'),
(718, 33, 31, 2, 'RIOS MACAHUACHI ARON LEE', '61438441', '973081851', 'Aronrios1808@gmail.com', '61438441', 1, '2020-01-01 00:00:00'),
(719, 31, 18, 2, 'CHIQUIBAL HUAYNACARI JUAN MANUEL', '47232383', '951741866', 'A@W', '47232383', 1, '2020-01-01 00:00:00'),
(720, 31, 18, 2, 'APONTE ALBERCA JUAN CARLOS', '44947935', '938357254', 'Juancarlosaponteal juanapontealverca@gmail.com', '44947935', 1, '2020-01-01 00:00:00'),
(721, 11, 2, 2, 'YUCGRA APOLINARIO CESAR', '45446570', '980437247', 'cesaryucgra@gmail.com', '45446570', 1, '2020-01-01 00:00:00'),
(722, 72, 18, 3, 'FLORES ZUÑIGA JORGE LUIS	', '07472108', '940346426', 'jflores@grupoinversionesgyc.pe', '07472108', 1, '2020-01-01 00:00:00'),
(723, 72, 18, 3, 'CHACON SEVILLANO DIANNE ERIKA', '47324730', '979322646', 'diannechacons@hotmail.com', '47324730', 0, '2020-01-01 00:00:00'),
(724, 72, 18, 2, 'TIRADO QUIROZ STEFANY MASSIEL	', '72869636', '966369056', 'Stefany131993@gmail.com', '72869636', 0, '2020-01-01 00:00:00'),
(725, 43, 18, 3, 'HUAMANI LEDESMA GERMAN	', '15425960', '998227512', 'german.huamani@nagasco.com.pe', '15425960', 1, '2020-01-01 00:00:00'),
(726, 33, 18, 2, 'ALVA SUAREZ WILMER YOHEL	', '47675500', '923783012', 'anderalvasu@gmail.com', '47675500', 1, '2020-01-01 00:00:00'),
(727, 33, 18, 2, 'ARANZABAL SICUS PAULINO', '43602918', '918863418', 'aranzabalpaulino64@gmail.co', '43602918', 1, '2020-01-01 00:00:00'),
(728, 2, 18, 3, 'AVALOS SATALAYA MARIO HUGO', '09314533', '940 831 3', 'mariohugoasatalaya@hotmail.com', '09314533', 1, '2020-01-01 00:00:00'),
(729, 33, 18, 2, 'CARDENAS RODRIGUEZ ANGEL RENAN', '20702767', '917334742', 'Angelrenan56@gmail.com', '20702767', 1, '2020-01-01 00:00:00'),
(730, 64, 18, 2, 'CASAS GARCIA JORGE LUIS', '40112716', '988963809', 'jorgecasas1@hotmail.com', '40112716', 1, '2020-01-01 00:00:00'),
(731, 33, 18, 2, 'CHIROQUE CCORISONCCO JOSE ALEXANDER', '71252747', '925153408', 'alexanderchiroque561@gmail.com', '71252747', 1, '2020-01-01 00:00:00'),
(732, 2, 18, 2, 'FLORES CANALES FREDY MARIO', '41924274', '959093874', 'a@w', '41924274', 1, '2020-01-01 00:00:00'),
(733, 31, 18, 2, 'LOZANO  COTRINA ENZO ALEJANDRO', '70517267', '970639100', 'Enzo.lozano.22.07@gmail.com', '70517267', 1, '2020-01-01 00:00:00'),
(734, 32, 18, 2, 'LOZANO LARA HUGO RICHARD', '10479417', '990906180', 'Hugolozanolara@gmail.com ', '10479417', 1, '2020-01-01 00:00:00'),
(735, 31, 18, 2, 'MINAYA ASTONITAS JUAN CARLOS', '41549936', '960238422', 'juanka17minas@gmail.com ', '41549936', 1, '2020-01-01 00:00:00'),
(736, 33, 18, 2, 'NIETO HILARIO  MANUEL', '48097370', '925020210', 'ximenameltroza@gamail.com', '48097370', 1, '2020-01-01 00:00:00'),
(737, 33, 18, 2, 'RISCO LEZAMA ORLANDO JESUS', '09801021', '993416397', 'jesusrisco.leo@gmail.com', '09801021', 1, '2020-01-01 00:00:00'),
(738, 32, 18, 2, 'RODAS MONTENEGRO ALFREDO', '10441839', '990372524', 'Alfredorodasmontenegro@gmail.com', '10441839', 1, '2020-01-01 00:00:00'),
(739, 33, 18, 2, 'TORPOCO SOLIS RONY', '44251771', '951997067', 'Khalesi0205@gmail.com', '44251771', 1, '2020-01-01 00:00:00'),
(740, 7, 18, 2, 'VALVERDE RUIZ GERARDO', '09021242', '943559953', 'a@w', '09021242', 1, '2020-01-01 00:00:00'),
(741, 31, 18, 2, 'ASPAJO CUMAPA RODOLFO', '43369951', '974671239', 'aspajocumaparaya@gmail.co', '43369951', 1, '2020-01-01 00:00:00'),
(742, 72, 3, 3, 'LOPEZ TARAZONA PLATON', '10166877', '1', 'A@W', '10166877', 1, '2020-01-01 00:00:00'),
(743, 23, 18, 2, 'RODRIGUEZ ROSALES JUAN CARLOS', '72645301', '941476907', 'jcrodriguez.0221@gmail.com', '72645301', 1, '2020-01-01 00:00:00'),
(744, 23, 18, 2, 'CABANILLAS MEDINA JEAN PIERRE', '48249786', '971071414', 'Jean.pierr.cabanillas@gmail.com', '48249786', 1, '2020-01-01 00:00:00'),
(745, 23, 18, 2, 'CRISOL CABRERA JEAN PIER', '47336608', '1', 'A@W', '47336608', 1, '2020-01-01 00:00:00'),
(746, 3, 18, 2, 'QUIÃ‘ONEZ ARONES JONATHAN ARTURO', '46189969', '1', 'A@W', '46189969', 1, '2020-01-01 00:00:00'),
(747, 3, 18, 2, 'PACCO CONTRERAS ISMAEL', '47780878', '1', 'A@W', '47780878', 0, '2020-01-01 00:00:00'),
(748, 1, 18, 2, 'ILLANES SOVIA ALEJANDRO', '10554564', '1', 'A@W', '10554564', 1, '2020-01-01 00:00:00'),
(749, 2, 18, 2, 'HUAMANTINCO CALDERON JUAN JOSE', '45097930', '1', 'a@w', '45097930', 1, '2020-01-01 00:00:00'),
(750, 78, 19, 2, 'MENDEZ SUAREZ CARLOS FERNANDO', '49068040', '941855684', 'cmendez@grupoinversionesgyc.pe', '49068040', 1, '2020-01-01 00:00:00'),
(751, 72, 18, 3, 'CARRANZA ZARATE HANS', '41859130', '940347003', 'hansheat848@hotmail.com', '41859130', 1, '2020-01-01 00:00:00'),
(752, 2, 2, 2, 'TUCTO BARBOZA MANUEL', '10740381', '946457527', 'manueltb_1977@hotmail.com', '10740381', 1, '2020-01-01 00:00:00'),
(753, 1, 2, 2, 'NEIRA GONZALEZ BELTRAN HUGO', '76542407', '995109976', 'bhng94@gmail.com', '76542407', 1, '2020-01-01 00:00:00'),
(754, 38, 5, 2, 'GARCIA GARCIA NICANOR', '15762715', '992885332', 'fredy.837363737@gmail.com', '15762715', 1, '2020-01-01 00:00:00'),
(755, 9, 18, 2, 'VERA MANRIQUE VERONICA', '46711407', '928094335', 'vera.manrique23@gmail.com', '46711407', 1, '2020-01-01 00:00:00'),
(756, 32, 18, 2, 'RODRIGUEZ GUTIERREZ PILAR', '43094216', '963104160', 'A@W', '43094216', 1, '2020-01-01 00:00:00'),
(757, 32, 18, 2, 'ZAMBRANO RODRIGUEZ FREDDY', '40990011', '977999137', '40990011virgo@gmail.com ', '40990011', 1, '2020-01-01 00:00:00'),
(758, 32, 18, 2, 'ZAMBRANO RODRIGUEZ FABRICIANO', '30503854', '987477263', 'A@W', '30503854', 1, '2020-01-01 00:00:00'),
(759, 32, 31, 2, 'UZURIAGA JESUS FRANK JONATHAN', '41720380', '980814019', 'Cualquiercorreo1@hotmail.com', '41720380', 1, '2020-01-01 00:00:00'),
(760, 32, 18, 2, 'SILVANO MANIHUARI ADLER', '40381101', '944438218', 'silvanoprospero_1976@hotma', '40381101', 1, '2020-01-01 00:00:00'),
(761, 31, 18, 2, 'SALAZAR ARANGO LUIS', '45345780', '955342505', 'A@W', '45345780', 1, '2020-01-01 00:00:00'),
(762, 31, 18, 2, 'PULLO PILLACA JOSE', '41412103', '994347667', 'A@W', '41412103', 1, '2020-01-01 00:00:00'),
(763, 31, 18, 2, 'NEIRA PEREZ JORGE', '44844926', '992027944', 'Josimarneiraperez@gmail.com', '44844926', 1, '2020-01-01 00:00:00'),
(764, 31, 18, 2, 'MICHUE DAVILA PEDRO', '10243471', '917619167', 'A@W', '10243471', 1, '2020-01-01 00:00:00'),
(765, 31, 18, 2, 'GARCIA FELIX ELVER', '71315936', '974494875', 'garciafelixelver@gmail.com', '71315936', 1, '2020-01-01 00:00:00'),
(766, 31, 18, 2, 'CASTILLO BRAVO CARLOS', '73252194', '1', 'A@W', '73252194', 1, '2020-01-01 00:00:00'),
(767, 31, 18, 2, 'CHUJUTALLI CHUJUTALLI HERGES', '42905318', '922854506', 'herges.ch17@gmail.com', '42905318', 1, '2020-01-01 00:00:00');
INSERT INTO `personal` (`id_personal`, `id_cargo`, `id_area`, `id_tipo`, `nom_personal`, `dni_personal`, `cel_personal`, `email_personal`, `pass_personal`, `act_personal`, `updated_at`) VALUES
(768, 33, 18, 2, 'LUIS CARRASCO MARTIN', '09470520', '991240402', 'Martinluiscarrasco@gmail.com', '09470520', 1, '2020-01-01 00:00:00'),
(769, 32, 18, 2, 'ARCE TORRES ROMMEL', '05404057', '975490293', 'rommel.arce74@gmail.com', '05404057', 1, '2020-01-01 00:00:00'),
(770, 33, 18, 2, 'TORO HEREDIA ELEUTERIO', '09528191', '967482456', 'etorohe@gmail.com', '09528191', 1, '2020-01-01 00:00:00'),
(771, 31, 18, 2, 'RUIZ CAPILLO NIFLIN', '47250616', '970654296', 'niflinruizcapillo@gmail.com', '47250616', 1, '2020-01-01 00:00:00'),
(772, 33, 18, 2, 'AGUERO SANTOS HUMBERTO', '42284018', '1', 'A@W', '42284018', 1, '2020-01-01 00:00:00'),
(773, 2, 18, 2, 'ESPINOZA ATANACIO JOSUE', '22662078', '1', 'a@w', '22662078', 1, '2020-01-01 00:00:00'),
(774, 32, 18, 2, 'CAMARGO OLIVARES JOSE FELIX', '21274508', '955092153', 'felixcamargo191975@gmail.c', '21274508', 1, '2020-01-01 00:00:00'),
(775, 32, 18, 2, 'CHUJUTALLI SANGAMA FELIX', '46956828', '97547593', 'Fechusa86@hotmail.com', '46956828', 1, '2020-01-01 00:00:00'),
(776, 32, 18, 2, 'DE LA CRUZ GALICIO RAUL', '42852897', '969747568', 'rauldelacruzgalicio@gmail.co', '42852897', 1, '2020-01-01 00:00:00'),
(777, 84, 27, 3, 'MELGAR SAAVEDRA JUAN CARLOS', '10454825', '945602850', 'Jcmelgarsaavedra40@gmail.com', '10454825', 1, '2020-01-01 00:00:00'),
(778, 31, 18, 2, 'POLANCO CLAUDIO KEVIN MIGUEL', '61208308', '934 262 0', 'polancokevin138@gmail.com', '61208308', 1, '2020-01-01 00:00:00'),
(779, 32, 18, 2, 'RUIZ CABANILLAS ROGER VALDEMAR', '42507660', '920189775', 'rruizc_valdemar@hotmail.com ', '42507660', 1, '2020-01-01 00:00:00'),
(780, 3, 18, 2, 'BARBOZA GREGORIO JUAN NELSON', '76752822', '1', 'a@w', '76752822', 0, '2020-01-01 00:00:00'),
(781, 1, 18, 2, 'VASQUEZ CHUQUIMANGO MARCO ANTONIO', '73832337', '1', 'a@w', '73832337', 0, '2020-01-01 00:00:00'),
(782, 73, 2, 2, 'CRUZ FLORES CAROLINE', '48105854', '949272073', 'Carolinecf29@gmail.com', '48105854', 1, '2020-01-01 00:00:00'),
(783, 9, 2, 2, 'DUEÑAS DE LA CRUZ IRIS SARAI', '60609224', '922081354', 'irisduenas18@gmail.com', '60609224', 0, '2020-01-01 00:00:00'),
(784, 40, 18, 2, 'CUCHO CIPRIANO HECTOR', '10015419', '945535003', 'jcuchociprian10@gmail.com', '10015419', 1, '2020-01-01 00:00:00'),
(785, 1, 27, 2, 'RAMOS HUAMAN CESAR LUIS', '42037405', '985511361', 'cr4042287@gmail.com', '42037405', 1, '2020-01-01 00:00:00'),
(786, 3, 2, 2, 'ALFARO MANRIQUE BRYAN EDUARDO', '62032952', '902202506', 'Bryanalfaro020@gmail.com', '62032952', 0, '2020-01-01 00:00:00'),
(787, 40, 2, 2, 'APARICIO ROGER KENNER', '43125884', '925419273', 'roger250481@gmail.com', '43125884', 0, '2020-01-01 00:00:00'),
(788, 40, 2, 2, 'CHAVEZ CORMAN SAMUEL CORNELIO', '44954121', '960077574', 'samuelcc582@gmail.com', '44954121', 0, '2020-01-01 00:00:00'),
(789, 3, 2, 2, 'CHAVEZ FERNANDEZ DAVILA LEONARDO ANTONIO', '48092460', '923175924', 'Leonardochavez120890@gmail.com', '48092460', 0, '2020-01-01 00:00:00'),
(790, 40, 2, 2, 'CHURATA CHIQUIJA ELISEO', '10646296', '1', 'A@W', '10646296', 0, '2020-01-01 00:00:00'),
(791, 3, 2, 2, 'GAMARRA CHAVEZ BETO MAYCON', '47212163', '1', 'A@W', '47212163', 0, '2020-01-01 00:00:00'),
(792, 17, 2, 3, 'SOTO VALDEZ MANUEL EUGENIO', '70240589', '1', 'A@W', '70240589', 0, '2020-01-01 00:00:00'),
(793, 72, 2, 3, 'FLORES VASQUEZ BARRY', '10553244', '1', 'A@W', '10553244', 0, '2020-01-01 00:00:00'),
(794, 40, 2, 2, 'BUGUÑA ABANTO ANGEL MARIO', '43135119', '1', 'A@W', '43135119', 1, '2020-01-01 00:00:00'),
(795, 40, 2, 2, 'CHAMBI TOQUE YOEL', '46269077', '1', 'A@W', '46269077', 0, '2020-01-01 00:00:00'),
(796, 40, 2, 2, 'DIESTRA GONZALES LUIS ALBERTO', '47047311', '1', 'A@W', '47047311', 0, '2020-01-01 00:00:00'),
(797, 40, 2, 2, 'MASIAS CORTEZ JHOAN USA', '75823832', '1', 'A@W', '75823832', 0, '2020-01-01 00:00:00'),
(798, 74, 2, 2, 'QUISPE CCASA ISIDRO', '46649223', '1', 'A@W', '46649223', 0, '2020-01-01 00:00:00'),
(799, 40, 2, 2, 'ROSAS ECHEVARRIA DELFI', '42318594', '1', 'A@W', '42318594', 1, '2020-01-01 00:00:00'),
(800, 75, 2, 2, 'SALAZAR VERA JOSE FERNANDO', '41252852', '1', 'A@W', '41252852', 0, '2020-01-01 00:00:00'),
(801, 1, 2, 2, 'UPIACHIHUA SOLANO PABLO', '01124737', '1', 'A@W', '01124737', 0, '2020-01-01 00:00:00'),
(802, 1, 2, 2, 'YOMOC ROJAS ALEXIS GERARDO', '45397208', '1', 'A@W', '45397208', 0, '2020-01-01 00:00:00'),
(803, 2, 2, 2, 'ACUÑA VILLALOBOS  FERNANDO ', '80090691', '1', 'A@W', '80090691', 1, '2020-01-01 00:00:00'),
(804, 1, 2, 2, 'AGURTO  MENDOZA SAMUEL DAVID', '09473676', '1', 'A@W', '09473676', 1, '2020-01-01 00:00:00'),
(805, 1, 2, 2, 'ALIAGA   VILA WILSON', '40799469', '1', 'A@W', '40799469', 0, '2020-01-01 00:00:00'),
(806, 1, 2, 2, 'BENDEZU CISNEROS CARLOS ROJACIANO', '80652152', '1', 'A@W', '80652152', 0, '2020-01-01 00:00:00'),
(807, 37, 2, 2, 'BRITO RAMIREZ JOHAN JOSE', '02858920', '1', 'A@W', '02858920', 1, '2020-01-01 00:00:00'),
(808, 76, 2, 2, 'BUGUÑA ABANTO ANGEL MARLO', '43135729', '1', 'A@W', '43135729', 1, '2020-01-01 00:00:00'),
(809, 3, 2, 2, 'CHAMBA TOLENTINO RICHARD', '47785713', '1', 'A@W', '47785713', 1, '2020-01-01 00:00:00'),
(810, 3, 2, 2, 'CHAMBA TOLENTINO IMER', '47751821', '1', 'A@W', '47751821', 1, '2020-01-01 00:00:00'),
(811, 2, 2, 2, 'DAVALOS MITMA MIGUEL ANGEL', '40506720', '1', 'A@W', '40506720', 0, '2020-01-01 00:00:00'),
(812, 3, 2, 2, 'GARAY  BOLIVAR CESAR UBERTI', '47113221', '1', 'A@W', '47113221', 0, '2020-01-01 00:00:00'),
(813, 42, 2, 4, 'LA ROSA GIRIBALDI FABIAN', '16027316', '1', 'A@W', '16027316', 1, '2020-01-01 00:00:00'),
(814, 60, 2, 2, 'LOZANO  INFANTE  MANUEL ALEXANDER', '43204331', '1', 'A@W', '43204331', 0, '2020-01-01 00:00:00'),
(815, 3, 2, 2, 'MANRIQUE BOZA JOAN PATRIC', '47044571', '1', 'A@W', '47044571', 1, '2020-01-01 00:00:00'),
(816, 2, 2, 2, 'MUÃ‘OZ  GARCIA  RUBEN JESUS', '22298391', '1', 'A@W', '22298391', 1, '2020-01-01 00:00:00'),
(817, 1, 2, 2, 'ORTEGA AGURTO JOSUE DANIEL', '48228995', '1', 'A@W', '48228995', 1, '2020-01-01 00:00:00'),
(818, 76, 2, 2, 'ROSAS  ECHEVARRIA  DELFI', '42319594', '1', 'A@W', '42319594', 1, '2020-01-01 00:00:00'),
(819, 1, 2, 2, 'TINCO LEO FREDDY ERNESTO', '47168150', '1', 'A@W', '47168150', 1, '2020-01-01 00:00:00'),
(820, 3, 2, 2, 'VALLE  NARCISO  JUAN ROBERTO', '10160321', '1', 'A@W', '10160321', 1, '2020-01-01 00:00:00'),
(821, 43, 18, 3, 'QUISPE HUAMAN WILFREDO ', '71550212', '917088179', 'wilfre1692@gmail.com', '71550212', 1, '2020-01-01 00:00:00'),
(822, 9, 2, 2, 'BRITO MOYA CARLOS ESTEBAN', '3085790', '1', 'a@w', '3085790', 0, '2020-01-01 00:00:00'),
(823, 1, 2, 2, 'CRISTHIAN IVAN SALAS CANO', '43477861', '920869652', 'Salasc.gyc@gmail.com', '43477861', 1, '2020-01-01 00:00:00'),
(824, 72, 18, 3, 'PAREDES BACA MARCO ANTONIO', '47146497', '992657628', 'marcosxxxas@gmail.com', '47146497', 1, '2020-01-01 00:00:00'),
(825, 56, 18, 2, 'GIRON MOGOLLON LUIS ALBERTO', '25706633', '1', 'a@w', '25706633', 1, '2020-01-01 00:00:00'),
(826, 31, 18, 2, 'ROJAS BRICEÑO ALDAIR', '45793319', '1', 'a@w', '45793319', 1, '2020-01-01 00:00:00'),
(827, 1, 18, 2, 'ESTREMADOYRO QUINTANA ANGEL', '71592558', '929598579', 'angelestremadoyroquintana@gmail.com', '71592558', 1, '2020-01-01 00:00:00'),
(828, 2, 18, 2, 'ESPINOZA MEDRANO NEMIAS', '48014468', '926337096', 'nemiasespinoza.medrano@gmail.com', '48014468', 1, '2020-01-01 00:00:00'),
(829, 3, 18, 2, 'PINEDO VILLACORTA JUAN CARLOS', '05416490', '1', 'A@W', '05416490', 0, '2020-01-01 00:00:00'),
(830, 1, 5, 2, 'FERREYRA GARCIA MARIO EDGAR', '05411779', '945047547', 'marioedgarferreyragarcia@gmail.com', '05411779', 1, '2020-01-01 00:00:00'),
(831, 3, 18, 2, 'BRAVO PALOMINO ANGEL EDUARDO', '41839370', '1', 'a@w', '41839370', 1, '2020-01-01 00:00:00'),
(832, 3, 2, 2, 'PINTO HUAMANI MARCIO YASIN', '74262999', '1', 'a@w', '74262999', 1, '2020-01-01 00:00:00'),
(833, 77, 25, 3, 'MORALES ARRASCO AKEMI PAMELA ', '46752322', '966272943', 'psicologia@grupoinversionesgyc.pe', '46752322', 0, '2020-01-01 00:00:00'),
(834, 72, 18, 4, 'GONZALES RONCAL RENSO MOISES', '10660353', '1', 'rensogonzalesr@gmail.com', '10660353', 1, '2020-01-01 00:00:00'),
(835, 40, 5, 2, 'MAYTA HUAYLLAQUISPE JULIO ISAAC', '80270914', '997772608', 'maytajulio5@gmail.com ', '80270914', 1, '2020-01-01 00:00:00'),
(836, 40, 2, 2, 'CASTAÑEDA ANTICONA ANGEL EDICSON', '73755529', '1', 'a@w', '73755529', 1, '2020-01-01 00:00:00'),
(837, 40, 2, 2, 'ESCOBAR SOTO FREDDY', '46753604', '1', 'a@w', '46753604', 1, '2020-01-01 00:00:00'),
(838, 40, 2, 2, 'GONZALES  PINEDO MAURO', '42365312', '1', 'a@w', '42365312', 1, '2020-01-01 00:00:00'),
(839, 40, 2, 2, 'ACOSTA AZOCAR LUIS ENRIQUE', '47273509', '1', 'a@w', '47273509', 1, '2020-01-01 00:00:00'),
(840, 40, 2, 2, 'ACOSTA AZOCAR JUAN CARLOS', '14022484', '1', 'a@w', '14022484', 1, '2020-01-01 00:00:00'),
(841, 7, 2, 2, 'GONZALES SILVA LUIS GABRIEL', '71894770', '99999999', 'C@C', '71894770', 1, '2020-01-01 00:00:00'),
(842, 1, 2, 2, 'CORZO ESPINOZA MISAEL SAMUEL', '73526734', '9999999', 'C@C', '73526734', 1, '2020-01-01 00:00:00'),
(843, 3, 2, 2, 'HERRERA CAPA VICENTE', '45979342', '999999', 'C@C', '45979342', 1, '2020-01-01 00:00:00'),
(844, 3, 2, 2, 'SAAVEDRA ESPINOZA RAUL ANTONIO', '43578697', '9999999', 'C@D', '43578697', 1, '2020-01-01 00:00:00'),
(845, 3, 2, 2, 'ALVARADO SANCHEZ MARIO', '24712228', '99999999', 'C@C', '24712228', 1, '2020-01-01 00:00:00'),
(846, 3, 2, 2, 'AHUANARI CHOTA HITLER', '45908955', '9999999', 'C@S', '45908955', 1, '2020-01-01 00:00:00'),
(847, 3, 2, 2, 'AHUANARI SILVANO LUIS', '45727874', '222222222', 'D@X', '45727874', 1, '2020-01-01 00:00:00'),
(848, 3, 2, 2, 'DE LA TORRE SANCHEZ LUIS', '10594023', '333333333', 'S@D', '10594023', 1, '2020-01-01 00:00:00'),
(849, 3, 2, 2, 'FLORES NUÑEZ MANUEL YURI', '45711784', '45711784', 'SS@D', '45711784', 1, '2020-01-01 00:00:00'),
(850, 1, 2, 2, 'TORO LEON MARCOS ', '00133576', '1111111', 'C@D', '001335760', 1, '2020-01-01 00:00:00'),
(851, 3, 2, 2, 'RAMIREZ URBANO MARTIN', '09683485', '11111111', 'S@D', '09683485', 1, '2020-01-01 00:00:00'),
(852, 2, 2, 2, 'FERNANDEZ GONZALES WILIAM FELIPE', '15765041', '2314345', 'SF@CC', '15765041', 1, '2020-01-01 00:00:00'),
(853, 64, 18, 2, 'MELENDEZ ESPINOZA MARCOS PEPE', '46540988', '926939701', 'Sammy.melendez.13@gmail.com', '46540988', 1, '2020-01-01 00:00:00'),
(854, 56, 18, 2, 'HEREDIA VELIZ WALDO FRANK', '44737353', '944608808', 'frankhv.topografia@gmail.com ', '44737353', 1, '2020-01-01 00:00:00'),
(855, 43, 18, 4, 'VILLAFUERTE LORENZO JHONY GUZMAN', '09904435', '1', 'a@w', '09904435', 0, '2020-01-01 00:00:00'),
(856, 3, 18, 2, 'GARRO PABLO WILMER ALFREDO', '41118672', '1', 'a@w', '41118672', 1, '2020-01-01 00:00:00'),
(857, 72, 25, 3, 'AVALOS CRUZ RICHARD HENNRY', '43197867', '1', 'a@w', '43197867', 0, '2020-01-01 00:00:00'),
(858, 60, 2, 2, 'LAURA CUZCANO LUIS', '09887786', '1', 'a@w', '09887786', 1, '2020-01-01 00:00:00'),
(859, 3, 18, 2, 'JARA ALVA LUIS ANTHONY', '73041548', '992184006', 'Nnnn', '73041548', 1, '2020-01-01 00:00:00'),
(860, 51, 13, 2, 'SEMINARIO PORTILLO FELIX FRANCCESCO', '73256446', '969325453', 'felix.franccesco.sp@gmail.com ', '73256446', 1, '2020-01-01 00:00:00'),
(861, 40, 2, 2, 'TARACHE LOPEZ CARLOS ALBERTO', '80095567', '90213522', 'A@W', '80095567', 1, '2020-01-01 00:00:00'),
(862, 72, 18, 4, 'ALIAGA LAZARO DAVID', '47137818', '1', 'david23.dal@gmail.com', '47137818', 1, '2020-01-01 00:00:00'),
(863, 72, 18, 4, 'CARRION QUISPE JAVIER OMAR', '41392723', '1', 'omarcarrion@gmail.com', '41392723', 1, '2020-01-01 00:00:00'),
(864, 35, 1, 2, 'DIEGO AGUSTIN REBATTA GOMEZ', '46246400', '961810172', 'D@gmail.com', '46246400', 1, '2020-01-01 00:00:00'),
(865, 23, 21, 2, 'GARAY TREJO KATHERINE FIORELLA', '74810624', '980777267', 'garaytrejokaty@gmail.com', '74810624', 1, '2020-01-01 00:00:00'),
(866, 4, 3, 1, 'LESLY S BOCANGEL BELTRAN', '72074460', '993435555', 'lbocangel@grupoinversionesgyc.pe', '72074460', 1, '2020-01-01 00:00:00'),
(867, 40, 2, 2, 'AYALA TICLLA ANDERSON FRANK', '72573648', '946511708', 'anderayala21@gmail.com', '72573648', 0, '2020-01-01 00:00:00'),
(868, 72, 25, 3, 'MARJORI MARCELA CHAVEZ COTERA', '71490505', '920418830', 'mchavez.gyc@gmail.com', '71490505', 1, '2020-01-01 00:00:00'),
(869, 72, 25, 2, 'LUIS RODRIGO CASTRO HERRERA', '73790223', '936311709', 'rcastro.gyc@gmail.com', '73790223', 0, '2020-01-01 00:00:00'),
(870, 36, 19, 2, 'MARIEL ALESSANDRA MENDIVIL BARRANTES', '73009840', '992715031', 'aless2618@gmail.com', '73009840', 1, '2020-01-01 00:00:00'),
(871, 4, 5, 3, 'SAMAME PERALTA ALEX', '45045427', '939220372', 'alexsamame54@gmail.com', '45045427', 1, '2020-01-01 00:00:00'),
(872, 77, 25, 2, 'ROCIO DEL PILAR', '44241072', '953738618', 'rmagallanes2013@gmail.com', '44241072', 0, '2020-01-01 00:00:00'),
(873, 2, 12, 3, 'EVERT CERVANTES RODRIGUEZ', '06309131', '946567177', 'evertcervantes17@gmail.com', '06309131E', 1, '2020-01-01 00:00:00'),
(874, 72, 3, 3, 'EDWAR KEVIN ORTEGA GARAY', '47804262', '994321316', 'ortega.gyc@gmail.com', '47804262', 0, '2020-01-01 00:00:00'),
(875, 37, 2, 2, 'JESUS ARTURO', '41672322', '953591683', 'a@gmail.com', '41672322', 1, '2020-01-01 00:00:00'),
(876, 38, 21, 2, 'LEON ARIZA VICTOR ALEXIS', '43765150', '963886766', 'victoralexisleo@gmail.com', '43765150', 1, '2020-01-01 00:00:00'),
(877, 38, 21, 2, 'GUERRA SANCHEZ CRUZ TEODOMIRO', '42363314', '956373362', 'cruzteodomiroguerrasanchez@gmail.com', '42363314', 1, '2020-01-01 00:00:00'),
(878, 1, 1, 2, 'SINTI VEGA MARCIO FELIX', '75366249', '1', 'a@w', '75366249', 1, '2020-01-01 00:00:00'),
(879, 38, 5, 3, 'HUAYANAY SANCHEZ MIGUEL', '09638953', '991015940', 'actualizar@gmail.com', '09638953', 1, '2020-01-01 00:00:00'),
(880, 38, 5, 2, 'REYES DIAZ RICARDO ENRIQUE', '43606996', '969346341', 'Enriquereyesdiaz@hotmail.com ', '43606996', 1, '2020-01-01 00:00:00'),
(881, 72, 3, 2, 'CAVALIER SANTILLAN JOEL ALFONSO', '76622751', '910733145', 'gyc.joelcs@gmail.com', '76622751', 0, '2020-01-01 00:00:00'),
(882, 59, 26, 2, 'MELGAREJO LEYVA LIZET', '75181587', '930820726', 'Lizmelgarejo2001@gmail.com', '75181587', 1, '2020-01-01 00:00:00'),
(883, 4, 4, 3, 'MUJICA TEJADA LUIS ALBERTO', '07945136', '999015770', 'lmujica21@gmail.com', '07945136', 1, '2020-01-01 00:00:00'),
(884, 77, 26, 2, 'CUTI RUIZ TATIANA', '47271770', '987717870', 'Tatiana.cuti.ruiz@gmail.com', '47271770', 0, '2020-01-01 00:00:00'),
(885, 72, 3, 3, 'VELASQUE WESTREICHER HERLINTON MAURICIO ', '41498661', '950698235', 'mvelasque@arceperu.pe', '41498661', 1, '2020-01-01 00:00:00'),
(886, 1, 18, 2, 'PALACIOS ESPINOZA RONALD ANTONY', '72485921', '987686923', 'ronaldantonypalaciosespinoza@gmail.com', '72485921', 1, '2020-01-01 00:00:00'),
(887, 1, 18, 2, 'ZAVALA HUAMAN FREDY', '40003665', '949769522', 'fredyzavalahuaman7@gmail.com', '40003665', 1, '2020-01-01 00:00:00'),
(888, 72, 3, 3, 'GUERRERO CHALE CESAR EDUARDO', '45464714', '953139526', 'cguerrero@arceperu.pe', '45464714', 1, '2020-01-01 00:00:00'),
(889, 80, 3, 3, 'CESAR QUIROZ PEÑA', '47417340', '944621998', 'cquiroz@grupoinversionesgyc.pe', '47417340', 0, '2020-01-01 00:00:00'),
(890, 32, 18, 2, 'VASQUEZ HUAMAN MARCELINO', '09423384', '993159460', 'Marcelinovasqueshuaman@gmail.com', '09423384', 1, '2020-01-01 00:00:00'),
(891, 42, 18, 3, 'ESPINOZA CASAS MOISES VICENTE', '25657543', '987309569', 'moisesvec@yahoo.es', '25657543', 1, '2020-01-01 00:00:00'),
(892, 12, 18, 3, 'ASTOCAZA ROJAS ALEX JUAN', '21489100', '1', 'a@w', '21489100', 1, '2020-01-01 00:00:00'),
(893, 5, 31, 3, 'GARRIAZO NUÑEZ RAUL JHONATTAN', '47254808', '986194958', 'ganu5691@gmail.com', '47254808', 1, '2020-01-01 00:00:00'),
(894, 2, 18, 2, 'SAMANIEGO CHAMBA GARY', '10337943', '99210394', 'samaniegochamba@gmail.com', '10337943', 1, '2020-01-01 00:00:00'),
(895, 42, 4, 3, 'MENDOZA GARAY WALTER CLAUDIO', '40721450', '986651719', 'wmendoza@grupoinversionesgyc.pe', '40721450', 1, '2020-01-01 00:00:00'),
(896, 1, 4, 2, 'SAMANIEGO MENDOZA ALEX DENILSON ', '72617207', '922680381', 'alexdenism.2002@gmail.com', '72617207', 1, '2020-01-01 00:00:00'),
(897, 1, 1, 2, 'VICENTE CHUQUIHUANGA NEYKIN ENRIQUE', '48551759', '902717745', 'neykinvicente95@gmail.com', '48551759', 1, '2020-01-01 00:00:00'),
(898, 1, 1, 2, 'RIVERA BERMUDEZ JONATHAN JAVIER', '44563587', '939999546', 'jonatrivera20@gmail.com', '44563587', 1, '2020-01-01 00:00:00'),
(899, 81, 1, 2, 'JERRY ESPINOZA VENTURA', '45662934', '0', 'j.oscanoa.ceisac@gmail.com', '45662934', 1, '2020-01-01 00:00:00'),
(900, 13, 18, 2, 'KEVIN ESPINOZA VENTURA', '71883540', '0', 'j.oscanoa.ceisac@gmail.com', '71883540', 1, '2020-01-01 00:00:00'),
(901, 32, 18, 2, 'TABOADA QUISPE ROBERTO CARLOS', '09509236', '912225985', 'roberocarlos22virgo@gmail.com', '09509236', 1, '2020-01-01 00:00:00'),
(902, 31, 18, 2, 'ISUIZA TANCHIVA JOINER LUIS', '41826547', '942939986', 'jlisuiza@hotmail.com', '41826547', 1, '2020-01-01 00:00:00'),
(903, 31, 27, 2, 'CONTRERAS QUISPE EMIL SANTIAGO', '76383640', '982573328', 'emilcontrerasquispe@gmail.com', '76383640', 1, '2020-01-01 00:00:00'),
(904, 31, 27, 2, 'GARCIA OSORIO VRITNER ORLANDO', '70322500', '912008783', 'vritner12@gmail.com', '70322500', 1, '2020-01-01 00:00:00'),
(905, 31, 27, 2, 'FLORES ROQUE MARCO ANTONIO', '73383671', '912631460', 'floresroquelleyco@gmail.com', '73383671', 1, '2020-01-01 00:00:00'),
(906, 31, 27, 2, 'LOPEZ JAVIER JHON MELSI', '70981545', '922755510', 'jhon94lj@gmail.com', '70981545', 1, '2020-01-01 00:00:00'),
(907, 31, 27, 2, 'EGOAVIL GOMEZ FELIX MANUEL', '20740199', '995365719', 'felixmanuelegoavilgomez1@gmail.com', '20740199', 1, '2020-01-01 00:00:00'),
(908, 1, 18, 2, 'LUIS ORLANDO HUAMAN GOMEZ', '44412211', '965965965', '1@H', '44412211', 1, '2020-01-01 00:00:00'),
(909, 3, 18, 2, 'TUCTO BREÑA MANUEL ANDERSSON', '72896687', '916556731', 'anderssontucto5@gmail.com', '72896687', 1, '2020-01-01 00:00:00'),
(910, 3, 18, 2, 'YUPANQUI SABOYA ELIO SAUL', '47209411', '913185548', 'eliosaul.yuse@gmail.com', '47209411', 1, '2020-01-01 00:00:00'),
(911, 3, 18, 2, 'FELIX TOSCANO EDUARDO', '46640324', '971504449', 'cristianfelixtoscano@gmail.com', '46640324', 1, '2020-01-01 00:00:00'),
(912, 3, 31, 2, 'MORI SOLIS ROGER MERCEDES', '40458864', '955029637', 'morisolisroger@gmail.com', '40458864', 1, '2020-01-01 00:00:00'),
(913, 3, 18, 2, 'ESPINOZA GONZALES NOEL', '48910671', '936425289', 'noelespi24@gmail.com', '48910671', 1, '2020-01-01 00:00:00'),
(914, 3, 27, 2, 'HUAMAN MEDINA SHEIVER YHOVANY', '74624681', '913436276', 'sheiver21@gmail.com', '74624681', 1, '2020-01-01 00:00:00'),
(915, 3, 27, 2, 'ORDOÑEZ HINOSTROZA ALEXANDER MIKE', '47693254', '951712877', 'oalexandermike23@gmail.co', '47693254', 1, '2020-01-01 00:00:00'),
(916, 3, 27, 2, 'VILCHEZ CASTRO SEGUNDO PASCUAL', '73677547', '923172915', 'segundopascualvilchezcastro@gmail.com', '73677547', 1, '2020-01-01 00:00:00'),
(917, 2, 27, 2, 'GOMEZ PASAPERA JUAN MANUEL', '10626617', '910220479', 'Juangomezp72@gmail.com', '10626617', 1, '2020-01-01 00:00:00'),
(918, 2, 27, 2, 'VILLACORTA MACEDO BEDER', '05348533', '984696286', 'bedervillacortamacedo@gmail.com', '05348533', 1, '2020-01-01 00:00:00'),
(919, 3, 4, 2, 'JAIME JOSFE PAULINO FLORES', '74144722', '980727882', 'jaimejosfepf@gmail.com', '74144722', 1, '2020-01-01 00:00:00'),
(920, 29, 18, 2, 'YESSENIA MARGOT ZUÑIGA ROJAS ', '45594624', '965354979', 'yesmizr@gmail.com', '45594624', 1, '2020-01-01 00:00:00'),
(921, 2, 27, 2, 'RODOLFO FRANSISCO GAMERO ORMEÑO', '25857327', '951730782', 'rofgameor@hotmail.com', '25857327', 1, '2020-01-01 00:00:00'),
(922, 12, 27, 3, 'TAPIA DURAN RICHARD FENANDO', '46336911', '968998667', 'rtapia@grupoinversionesgyc.pe', '46336911', 1, '2020-01-01 00:00:00'),
(923, 42, 27, 3, 'COLMENARES QUISPE EVER ANTONIO', '42108704', '1', 'ing.ecolmenares@gmail.com', '42108704', 1, '2020-01-01 00:00:00'),
(924, 32, 27, 2, 'ADANAQUE MARQUEZ SEGUNDO NESTOR ', '03381167', '927749929', 'adanaquemarqueznestor@gm', '03381167', 1, '2020-01-01 00:00:00'),
(925, 32, 27, 2, 'ANDRADE LOARTE NICHOLL DUF', '43927914', '1', 'A@W', '43927914', 1, '2020-01-01 00:00:00'),
(926, 32, 27, 2, 'BERMUDEZ QUISPE EDDY', '08987076', '928008744', 'eddyjunior5@hotmail.com', '08987076', 1, '2020-01-01 00:00:00'),
(927, 32, 27, 2, 'CASTAÑEDA AVALOS CARLOS JESUS', '47344442', '964891602', 'carlosjesuscastanedaavalos@gmail.com', '47344442', 1, '2020-01-01 00:00:00'),
(928, 32, 27, 2, 'GAMARRA ALVARADO ALBERTO LUCIANO', '41395620', '931467787', 'albertolucianogamarra1022@gmail.com', '41395620', 1, '2020-01-01 00:00:00'),
(929, 32, 27, 2, 'ZABALETA FLORES DIXON', '45511015', '977374067', 'D ZABALETA@GMAIL.COM', '45511015', 1, '2020-01-01 00:00:00'),
(930, 31, 27, 2, 'APOLITANO TORRES MAURICIO SALOMON ', '45032553', '939415615', 'apolitanotorresmauricio@gm', '45032553', 1, '2020-01-01 00:00:00'),
(931, 31, 27, 2, 'CHAVEZ QUISPE FRANCISCO JAVIER', '75641023', '976449159', 'Anderson.Javier.20011@gmail.com', '75641023', 1, '2020-01-01 00:00:00'),
(932, 31, 27, 2, 'PLASENCIA MANCILLA HERNAN', '70905802', '1', 'A@W', '70905802', 1, '2020-01-01 00:00:00'),
(933, 31, 27, 2, 'SANCHEZ ROJAS KEVIN ISAY', '75277011', '983590103', 'adrilly4231@gmail.com', '75277011', 1, '2020-01-01 00:00:00'),
(934, 31, 27, 2, 'VASQUEZ CACHIQUE LUIS ANTONIO', '47394606', '1', 'ecopapa600@gmail.com', '47394606', 1, '2020-01-01 00:00:00'),
(935, 31, 27, 2, 'VASQUEZ VELA JESUS ANTONIO', '76274581', '930760252', 'jesusvasquezvels@gmail.com', '76274581', 1, '2020-01-01 00:00:00'),
(936, 86, 27, 2, 'FELIPA TORRES ALEXANDER FRANCISCO', '44057593', '953328398', 'afelipa@grupoinversionesgyc.pe', '44057593', 1, '2020-01-01 00:00:00'),
(937, 70, 27, 2, 'ANTON NOLE JOSE GILBERTO', '47439802', '1', 'A@W', '47439802', 1, '2020-01-01 00:00:00'),
(938, 69, 27, 2, 'LABAN LIZANA CESAR JOSE', '03245721', '1', 'A@W', '03245721', 1, '2020-01-01 00:00:00'),
(939, 2, 27, 2, 'FIGUEROA VALLADARES MANUEL HILARIO', '71869710', '988926451', 'manuelfigueroa052@gmail.com', '71869710', 1, '2020-01-01 00:00:00'),
(940, 31, 27, 2, 'ORTEGA OLIVARES EDDIE LEOPOLDO', '25513961', '996526389', 'eddiel ortegao. @ gmail com', '25513961', 1, '2020-01-01 00:00:00'),
(941, 2, 27, 2, 'REMI SOTO LINO', '44238097', '948981624', 'remisotolino33@gmail.com', '44238097', 1, '2020-01-01 00:00:00'),
(942, 32, 27, 2, 'DARCO ORBE VILLACORTA', '62904974', '953539901', 'darcovi1011@gmail.com', '62904974', 1, '2020-01-01 00:00:00'),
(943, 32, 27, 2, 'JUAN CARLOS PINEDO VILLACORTA', '05416940', '902120114', 'pinedo05416490@gmail.com', '05416940', 1, '2020-01-01 00:00:00'),
(944, 85, 27, 2, 'EDER DOMINGUEZ TAPULLIMA', '47300069', '950674036', 'domingueztapullimaeder464@gmail.com', '47300069', 1, '2020-01-01 00:00:00'),
(945, 72, 3, 3, 'MESIAS EVANGELISTA ANAIS MANUELA ', '71339287', '985512938', 'anais.mesias1998@gmail.com', '71339287', 1, '2020-01-01 00:00:00'),
(946, 84, 27, 2, 'NILTON JUAN HUACACHE ALVARADO', '16170270', '993417308', 'huacachenilton@gmail.com', '16170270', 1, '2020-01-01 00:00:00'),
(947, 31, 27, 2, 'PERCY NEYRA VALENZUELA', '73867496', '934250416', 'valenzuelapercy30@gmail.com', '73867496', 1, '2020-01-01 00:00:00'),
(948, 72, 3, 2, 'REY ADRIAN ALVAREZ SUAREZ', '72215260', '931279250', 'mf_alvarez18@hotmail.com', '72215260', 1, '2020-01-01 00:00:00'),
(949, 31, 27, 2, 'CULQUE NAPUCHE CARLOS JAVIER ', '44557911', '939293393', 'javierculque17@gmail.com', '44557911', 1, '2020-01-01 00:00:00'),
(950, 32, 31, 2, 'MEZARINA SAENZ MANUEL MARTIN', '48132195', '910121540', 'mezarinasaenzmanuelmartin@gmail.com', '48132195', 1, '2020-01-01 00:00:00'),
(951, 1, 27, 2, 'RONOEL ICHACCAYA PEREZ', '42032306', '958786732', 'ronoel20@hotmail.com', '42032306', 1, '2020-01-01 00:00:00'),
(952, 32, 27, 2, 'JORGE ANTONIO LLIHUA HINOJOSA ', '44838814', '924773066', 'jorgellihua_110@hotmail.com', '44838814', 1, '2020-01-01 00:00:00'),
(953, 3, 27, 2, 'POOL CAPCHA ESCOBAR', '42076751', '971627565', 'paulcapcha@hotmail.com', '42076751', 1, '2020-01-01 00:00:00'),
(954, 3, 27, 2, 'GUIMEL CIPRIANO ANTONIO', '76084588', '927049613', 'gcipriano.zg7@gmail.com', '76084588', 1, '2020-01-01 00:00:00'),
(955, 3, 27, 2, 'RAMIRO RIVERA SIGUEÃ±AS', '43201420', '910917319', 'ramiro.1785@hotmail.com', '43201420', 1, '2020-01-01 00:00:00'),
(956, 1, 31, 2, 'AROCUTIPA SALAZAR JOSÉ LUIS', '40631447', '904707396', 'pataaro@hotmail.com', '40631447', 1, '2020-01-01 00:00:00'),
(957, 84, 27, 2, 'INOCENCIO LAUREANO MELQUIADES HUBER', '16175278', '954928092', 'R@T', '16175278', 1, '2020-01-01 00:00:00'),
(958, 31, 27, 2, 'OLAZABAL REYES BRYAN', '77653397', '938366965', 'H@H', '77653397', 1, '2020-01-01 00:00:00'),
(959, 31, 27, 2, 'OLAZABAL REYES ABRAHAM', '77653410', '937104027', 'H@H1', '77653410', 1, '2020-01-01 00:00:00'),
(960, 31, 27, 2, 'SOTO FERNANDEZ PHOOL DAVID', '62040949', '997695358', 'H@H2', '62040949', 1, '2020-01-01 00:00:00'),
(961, 32, 27, 2, 'LINIAN VELASQUEZ EDERSON ORLANDO', '71859052', '903083467', 'edlive_1996@hotmail.com', '71859052', 1, '2020-01-01 00:00:00'),
(962, 3, 27, 2, 'TENORIO CARRERA JUAN SEBASTIAN', '60698019', '980914049', 'sebastiantenoriocarrera@gmail.com', '60698019', 1, '2020-01-01 00:00:00'),
(963, 3, 27, 2, 'ROSALES HUAMAN MARCO ANTONIO', '15751519', '927549378', 'marcorosales28.09@gmail.com', '15751519', 1, '2020-01-01 00:00:00'),
(964, 32, 31, 2, 'GUTIERREZ CARDENAS SLEYTHER SMITH', '71237980', '934908757', 'sleyther.17.1000@gmail.com', '71237980', 1, '2020-01-01 00:00:00'),
(965, 72, 3, 3, 'JORDAN JOEL SERNA VALLADARES', '48073340', '914355134', 'jordanserna726@gmail.com', '48073340', 1, '2020-01-01 00:00:00'),
(966, 72, 27, 3, 'YOLI BECERRA JIMENEZ', '78114960', '952320055', 'Ybecerra165@gmail.com', '78114960', 1, '2020-01-01 00:00:00'),
(967, 4, 18, 3, 'TAMAYO MATICORENA MAURICIO', '40899593', '972783974', 'mauriciotamayomaticorena@gmail.com', '40899593', 0, '2020-01-01 00:00:00'),
(968, 42, 18, 3, 'BLAS CONDORI LUIS ENRIQUE', '09454559', '998822275', 'lblas@grupoinversionesgyc.pe', '09454559 ', 0, '2020-01-01 00:00:00'),
(969, 1, 18, 2, 'VARGAS QUISPE ROLLY', '42943383', '918454975', 'Rollysthon289@gmail.com', '42943383', 1, '2020-01-01 00:00:00'),
(970, 1, 18, 2, 'JIMENEZ DURAND MIGUEL ENRIQUE ', '02848905', '942116240', 'miguelenriquejimenezdurand@gmail.com', '02848905', 1, '2020-01-01 00:00:00'),
(971, 32, 31, 2, 'MENDOZA PEZO JACK ANDERSON', '42947432', '941229625', 'jackmp1020@gmail.com', '42947432', 1, '2020-01-01 00:00:00'),
(972, 1, 18, 2, 'LOPEZ SILVA NEMIAS', '43232945', '990973533', 'A@L', '43232945', 1, '2020-01-01 00:00:00'),
(973, 1, 18, 2, 'VALENCIA ZARE CARLOS GERMAN', '40494429', '986508524', 'carlosvalenciazare@hotmail.com', '40494429', 1, '2020-01-01 00:00:00'),
(974, 32, 31, 2, 'ARTEAGA RIVAS SANTOS VICENTE', '10657261', '993427807', 'santosvicentearteagarivas@gmail.com', '10657261', 1, '2020-01-01 00:00:00'),
(975, 3, 18, 2, 'RIVERA QUISPE JHONATHAN', '76958646', '955204536', 'jhonathan18r@gmail.com', '76958646', 1, '2020-01-01 00:00:00'),
(976, 3, 18, 2, 'DIAZ ORBE JOE ERNESTO', '71594593', '933111791', 'jd8720839@gmail.com', '71594593', 1, '2020-01-01 00:00:00'),
(977, 3, 18, 2, 'PEREZ LUJAN JHONNY PERCY', '80331896', '977165757', 'percyperezlujan123@gmail.com', '80331896', 1, '2020-01-01 00:00:00'),
(978, 3, 18, 2, 'VENTURO MANUEL MAYCOL', '73067814', '934219657', 'venturomanuelm@gmail.com', '73067814', 1, '2020-01-01 00:00:00'),
(979, 3, 18, 2, 'CORTEZ QUISPE FRANKLIN LUIS', '70327431', '932718428', 'franklinluiscortezquispe@gmail.com', '70327431', 1, '2020-01-01 00:00:00'),
(980, 32, 31, 2, 'RAMOS SANCHEZ JAIRO ALDAIR', '76211100', '942499818', 'jairo12.rs97@gmail.com', '76211100', 1, '2020-01-01 00:00:00'),
(981, 2, 18, 2, 'GUZMAN LIZANA ARNOLD ANTONY GASPAR', '47447867', '924057897', 'antonyguzman92@hotmail.com', '47447867', 1, '2020-01-01 00:00:00'),
(982, 84, 28, 2, 'WILFREDO CANCHERI', '20657683', '967115858', 'WILFREDOCANCHERI60@gmail.com', '20657683', 1, '2020-01-01 00:00:00'),
(983, 1, 28, 2, 'ORELLANA SARAVIA JIMMI', '45784600', '900481399', 'JIMMIORELLANA5@gmail.com', '45784600', 1, '2020-01-01 00:00:00'),
(984, 1, 28, 2, 'SANDOVAL HUERTAS JORGE', '45956128', '935960248', 'huertasjorge0631@gmail.com', '45956128', 1, '2020-01-01 00:00:00'),
(985, 1, 28, 2, 'CARLOS SALA ', '80289152', '943576567', 'carlossala42@hotmail.com', '80289152', 1, '2020-01-01 00:00:00'),
(986, 1, 28, 2, 'OSMAR DIAZ DELGADO', '48743784', '912208224', 'osmardiazdelgado@gmail.com', '48743784', 1, '2020-01-01 00:00:00'),
(987, 1, 28, 2, 'GADIEL VASQUEZ', '41052795', '991974186', 'furiosoamable@gmail.com', '41052795', 1, '2020-01-01 00:00:00'),
(988, 1, 28, 2, 'RAUL MANUEL ', '62457431', '914206166', 'raulimpe12@gmail.com', '62457431', 1, '2020-01-01 00:00:00'),
(989, 1, 28, 2, 'MIGUEL SAAVEDRA ', '10672220', '933610112', 'A@M', '10672220', 1, '2020-01-01 00:00:00'),
(990, 1, 28, 2, 'HUNCAYA LUCIO ', '06792023', '929532749', 'a@h', '06792023', 1, '2020-01-01 00:00:00'),
(991, 3, 28, 2, 'DURAND RUCANA JAIRO ', '72124251', '931642968', 'a@d', '72124251', 1, '2020-01-01 00:00:00'),
(992, 1, 28, 2, 'EDGAR BERROCAL ROJAS ', '44152919', '976354204', 'berrocalrojas86@gmail.com', '44152919', 1, '2020-01-01 00:00:00'),
(993, 1, 28, 2, 'JOHN BERROCAL ROJAS', '42683546', '93055834', 'johnalexberrocal07@gmail.com', '42683546', 1, '2020-01-01 00:00:00'),
(994, 3, 28, 2, 'QUIROZ SANCHEZ, ABEL JESUS', '70192383', '922612050', 'abeljesus443@gmail.com', '70192383', 1, '2020-01-01 00:00:00'),
(995, 3, 28, 2, 'MIGUEL ACUÑA SANTOS ', '80822767', '912985660', 'a@m', '80822767', 1, '2020-01-01 00:00:00'),
(996, 3, 28, 2, 'RIOS VALDIVIESO', '07140733', '986810550', 'godoru1962@gmail.com', '07140733', 1, '2020-01-01 00:00:00'),
(997, 1, 28, 2, 'GUILLERMO APAZA CHOQUEHUANCA', '44685986', '962206477', 'GUILLERMO-225@gmail.com', '44685986', 1, '2020-01-01 00:00:00'),
(998, 1, 28, 2, 'CARRERA JORGE CARLOS ARMANDO ', '71746950', '996883597', 'carrera17cj@gmail.com', '71746950', 1, '2020-01-01 00:00:00'),
(999, 84, 28, 2, 'DIEGO CERJHY APOLINARIO QUIROZ', '77147170', '910161329', 'diego.apolinario.quiroz@gmail.com', '77147170', 1, '2020-01-01 00:00:00'),
(1000, 2, 28, 2, 'NILTON JAIME GASPAR MEZA', '76241021', '975793357', 'xina171522@gmail.com', '76241021', 1, '2020-01-01 00:00:00'),
(1001, 72, 3, 3, 'ISASI NEYRA ALI BIL', '70002659', '971004822', 'isasi.neyra28@gmail.com', '70002659', 1, '2020-01-01 00:00:00'),
(1002, 97, 45, 2, 'PERCY HILDEBRANDO SALVADOR SALVADOR', '06063106', '993335298', 'psalvador24@hotmail.com', '06063106', 1, '2020-01-01 00:00:00'),
(1003, 2, 28, 2, 'QUEREVALU QUEREVALU JUAN GIANMARCO', '70871113', '981232053', 'Juanquerevalu240993@gmail.com', '70871113', 1, '2020-01-01 00:00:00'),
(1004, 2, 28, 2, 'CAMPOS CHUNGA WILLIAM NILIBERTO', '80106302', '912010900', 'camposchungaw30@gmail.com', '80106302', 1, '2020-01-01 00:00:00'),
(1005, 31, 28, 2, 'GARCIA MAMANI RUBEN RICARDO', '45255288', '941518769', 'rg0193078@gamial.com', '45255288', 1, '2020-01-01 00:00:00'),
(1006, 1, 31, 2, 'TRINIDAD MANAYAY OSMAR DANY ', '73935302', '912183419', 'osmardanytrinidadmanayay@gmail.com', '73935302', 1, '2020-01-01 00:00:00'),
(1007, 32, 28, 2, 'PERALTA PEREZ JOHAN LEWIN', '41883487', '41883487', 'alewinperez3@gmail.com', '41883487', 1, '2020-01-01 00:00:00'),
(1008, 1, 28, 2, 'GONZALEZ HEREDIA DEINER', '71654126', '929449165', 'deinergonzalezheredia19@gmail.com', '71654126', 1, '2020-01-01 00:00:00'),
(1009, 1, 28, 2, 'ORTEGA FASANANDO JHONATTAN MAYCOL', '73780413', '73780413', '73780413@gmail.com', '73780413', 1, '2020-01-01 00:00:00'),
(1010, 84, 28, 3, 'FRUCTUS DURAND WILLIAN', '40933680', '991745064', 'willifructus@gmail.com', '40933680', 1, '2020-01-01 00:00:00'),
(1011, 84, 28, 2, 'CARRASCO ECHEVARRIA ROLANDO', '06101313', '940194213', '06101313@gmail.com', '06101313', 1, '2020-01-01 00:00:00'),
(1012, 84, 28, 3, 'LEON VARGAS JAVIER', '09694591', '933929614', 'javierleonvargas156@gmail.com', '09694591', 1, '2020-01-01 00:00:00'),
(1013, 4, 28, 3, 'TAPIA GUIMARAY CARLOS HERNANDO', '06818068', '922502348', 'ctapiaguimaray17@gmail.com', '06818068', 1, '2020-01-01 00:00:00'),
(1014, 12, 28, 3, 'LAINEZ CHACON RENZO ALFONSO', '09953295', '991678910', 'rlainez@cip.org.pe', '09953295', 1, '2020-01-01 00:00:00'),
(1015, 4, 18, 3, 'LEE CHUNG JU VINCENT', '38794600', '38794600', '38794600@GMAIL.COM', '38794600', 1, '2020-01-01 00:00:00'),
(1016, 4, 18, 3, 'ROBLES CAMPO CARLOS', '0985377', '0985377', '0985377@GMAIL.COM', '0985377', 1, '2020-01-01 00:00:00'),
(1017, 2, 27, 2, 'ARANGO RAMOS ENRIQUE', '10601094', '986724890', 'enriquearangoramos@gmail.com', '10601094', 1, '2020-01-01 00:00:00'),
(1018, 32, 28, 2, 'ROSALES SOLORZANO BETO', '45197441', '947311691', '.....@gmail.com', '45197441', 1, '2020-01-01 00:00:00'),
(1019, 3, 28, 2, 'RAMOS GUERRA LIBORIO', '43295971', '43295971', 'ramosliborio83@gmail.com', '43295971', 1, '2020-01-01 00:00:00'),
(1020, 3, 28, 2, 'LOPEZ ROJAS MARCO', '74537436', '927163143', 'danielrojas270397@hotmail.com', '74537436', 1, '2020-01-01 00:00:00'),
(1021, 3, 28, 2, 'RIOS VALDIVIESO GODOFREDO', '07140735', '......', '......@gmail.com', '07140735', 1, '2020-01-01 00:00:00'),
(1022, 3, 18, 2, 'SOTO SOTO BARBADICO YVAN ', '41237002', '925577903', 'yvansotosoto@gmail.com', '41237002', 1, '2020-01-01 00:00:00'),
(1023, 32, 18, 2, 'MEDINA CHUQUISACA JULIAN', '42258414', '994946399', 'julio82_09@hotmail.com', '42258414', 1, '2020-01-01 00:00:00'),
(1024, 2, 18, 2, 'ALEJOS ECHEGARAY ORLANDO', '09667011', '982447060', 'Orlando.ae67@gmail.com', '09667011', 1, '2020-01-01 00:00:00'),
(1025, 32, 18, 2, 'RODRIGUEZ PITA JOSE NEMECIO', '17446762', '960303691', 'josenemecio.r.1975@gamil.com', '17446762', 1, '2020-01-01 00:00:00'),
(1026, 84, 18, 2, 'MANUEL CHUMAN IMAN', '17449628', '970496094', 'antoniochuman@outlook.com', '17449628', 1, '2020-01-01 00:00:00'),
(1027, 32, 18, 2, 'SOTO POZO ALBERTO', '43398609', '976430599', 'albertosoto1488@gmail.com', '43398609', 1, '2020-01-01 00:00:00'),
(1028, 32, 18, 2, 'VASQUEZ DA CRUZ LUIS', '45302610', '45302610', '45302610@GMAIL.COM', '45302610', 1, '2020-01-01 00:00:00'),
(1029, 3, 18, 2, 'CABANILLAS VARGAS JORDAN ', '70404550', '932011558', 'yordancabanillas32@gmail.com', '70404550', 1, '2020-01-01 00:00:00'),
(1030, 84, 18, 2, 'TACUCHE SILVA FRANCISCO ', '09777993', '902295367', 'tacuchesilvafrancisco087@gmail.com', '09777993', 1, '2020-01-01 00:00:00'),
(1031, 32, 18, 2, 'TACUCHE PUMACAYO NEILS KENEDY', '45882935', '994669991', 'keni.te22@gmail.com', '45882935', 1, '2020-01-01 00:00:00'),
(1032, 33, 18, 2, 'LIMPE ALFARO RAUL', '62454701', '914206166', 'raulimp12@gmail.com', '62454701', 1, '2020-01-01 00:00:00'),
(1033, 3, 18, 2, 'FALCON CHINCHAY DEYLOR EMERSON', '76030413', '952112802', 'deylorfalcon2001@gmail.com', '76030413', 1, '2020-01-01 00:00:00'),
(1034, 3, 18, 2, 'TACUCHE ROJAS ANTHONI ', '72776337', '969114955', 'atacuches@gmail.com', '72776337', 1, '2020-01-01 00:00:00'),
(1035, 32, 18, 2, 'CASTILLON YANCCE HARINZON', '77422156', '994735336', 'harrinzonc4@gmail.com', '77422156', 1, '2020-01-01 00:00:00'),
(1036, 32, 18, 2, 'HUARCAYA CARHUALLA LUCIO', '6792023', '936382740', 'huarcayacarhualla@gmail.com', '6792023', 1, '2020-01-01 00:00:00'),
(1037, 2, 18, 2, 'LANDEO VARGAS RAFAEL', '9844295', '974298027', 'rafaellandeovargas39@gmail.com', '9844295', 1, '2020-01-01 00:00:00'),
(1038, 2, 18, 2, 'ORIHUELA VALDEZ OSCAR', '19970295', '999408153', 'wilosc.72@gmail.com', '19970295', 1, '2020-01-01 00:00:00'),
(1039, 32, 28, 2, 'ARAUJO ESPINO JUNIOR', '47381825', '970873214', 'ELANCITO_ELUNICO@HOTMAIL.COM', '47381825', 1, '2020-01-01 00:00:00'),
(1040, 3, 28, 2, 'REJAS ALBUQUERQUE WALDIR', '47889625', '931395242', 'REJASWALDIR@GMAIL.COM', '47889625', 1, '2020-01-01 00:00:00'),
(1041, 3, 28, 2, 'MANANITA OTOYA MANUEL', '71107497', '933672740', 'MANA_FOX09@HOTMAIL.COM', '71107497', 1, '2020-01-01 00:00:00'),
(1042, 3, 28, 2, 'HEREDIA UPARI DARVIN', '45806750', '992346826', 'NOTIENE@GMAIL.COM', '45806750', 1, '2020-01-01 00:00:00'),
(1043, 32, 28, 2, 'CORONADO BARROSO JEINER', '43117159', '900462540', 'CORONADO.JEINER@GMAIL.COM', '43117159', 1, '2020-01-01 00:00:00'),
(1044, 32, 28, 2, 'MEDINA ZAMORA MESAC', '40748189', '986996819', 'MESACMZ2000@HOTMAIL.COM', '40748189', 1, '2020-01-01 00:00:00'),
(1045, 1, 18, 2, 'DAMIAN CONTRERAS LUIS MIGUEL', '70446605', '9', 'a@l', '70446605', 1, '2020-01-01 00:00:00'),
(1046, 63, 18, 2, 'PARDO AGUILAR DAVID HILDEBRANDO', '47228580', '9', 'a@h', '47228580', 1, '2020-01-01 00:00:00'),
(1047, 63, 18, 2, 'SUAREZ ROMERO JOSSIR JOEL ', '47401912', '9', 'a@h', '47401912', 1, '2020-01-01 00:00:00'),
(1048, 11, 18, 2, 'VALLES MURRIETA JUAN RAFAEL', '05364773', '9', 'a@j', '05364773', 1, '2020-01-01 00:00:00'),
(1049, 3, 18, 2, 'ALCANTARA MOYA ARNULFO FERMIN', '75663012', '9', 'a@k', '75663012', 1, '2020-01-01 00:00:00'),
(1050, 32, 31, 2, 'RAMOS PEDROZO MARTIN', '25477369', '965091110', '000@0000', '25477369', 1, '2020-01-01 00:00:00'),
(1051, 32, 28, 2, 'VALENCIA CASTILLO WILLIAM', '45105548', '00000', '00000@ppp', '45105548', 1, '2020-01-01 00:00:00'),
(1052, 32, 28, 2, 'CORONADO BARROSO LENNIN', '46334613', '0000', '0000@000', '46334613', 1, '2020-01-01 00:00:00'),
(1053, 84, 28, 3, 'MONTENEGRO GUEVARA ANGEL GREGORIO ', '16619443', '990209807', 'a.montenegro321@hotmail.com', '16619443', 1, '2020-01-01 00:00:00'),
(1054, 32, 28, 2, 'PEDRO AVILA ESTRADA', '16626463', '952808482', 'nnnn@hhhhh', '16626463', 1, '2020-01-01 00:00:00'),
(1055, 15, 28, 2, 'ROSALES BRAVO ROMMEL CRISTIAN', '73782933', '997782317', 'rosalesbravorommelcristian@gmail.com', '73782933', 1, '2020-01-01 00:00:00'),
(1056, 3, 28, 2, 'RODRIGUEZ ANDUAGA ALDAIR CESAR', '76727402', '978349948', 'rodriguezaldairc@gmail.com', '76727402', 1, '2020-01-01 00:00:00'),
(1057, 15, 28, 2, 'ARACA OGOSI ALEXANDER', '48753853', '925482038', 'nnn@nnnn', '48753853', 1, '2020-01-01 00:00:00'),
(1058, 15, 28, 2, 'CHAVEZ VASQUEZ MIGUEL ANGEL', '74403342', '999999', 'mmmmm@mmmm', '74403342', 1, '2020-01-01 00:00:00'),
(1059, 84, 28, 4, 'EDWAR JULCA SOLANO', '78109374', '936721532', 'ricardojulca1997@gmail.com', '78109374', 1, '2020-01-01 00:00:00'),
(1060, 2, 28, 2, 'EMERSON PACHECO AÑAUPA', '46782024', '99999999', 'nnnn@mmmm', '46782024', 1, '2020-01-01 00:00:00'),
(1061, 32, 28, 2, 'EULOGIO ALEX ALVAREZ CABELLO', '75281352', '99999999', 'nnnn@nnnn', '75281352', 1, '2020-01-01 00:00:00'),
(1062, 3, 28, 2, 'RAUL RIVAS PARANO', '72254199', '9999999', 'nnn@nnnn', '72254199', 1, '2020-01-01 00:00:00'),
(1063, 2, 28, 2, 'MARTIN PASCUAL YOVERA YOVERA', '42960045', '9999999', 'nnn@nnnn', '42960045', 1, '2020-01-01 00:00:00'),
(1064, 63, 28, 2, 'HECTOR LOPEZ GONZALES', '46928936', '9999999', 'nnnn@nnn', '46928936', 1, '2020-01-01 00:00:00'),
(1065, 15, 28, 2, 'LUIS ÃLVAREZ CORDOVA', '42715987', '99999', 'nnn@nnnn', '42715987', 1, '2020-01-01 00:00:00'),
(1066, 32, 28, 2, 'GERTHON JUNIOR ABISRROR PAREDES', '70943947', '999999', 'nnn@nnnn', '70943947', 1, '2020-01-01 00:00:00'),
(1067, 32, 28, 2, 'JUAN CARLOS QUISIYUPANQUI BRAVO', '46029715', '999999', 'nnn@nnn', '46029715', 1, '2020-01-01 00:00:00'),
(1068, 2, 28, 2, 'ROY TERRONES SARMIENTO', '40029696', '999999', 'nnnn@nnn', '40029696', 1, '2020-01-01 00:00:00'),
(1069, 84, 28, 3, 'DE LA CRUZ GARAY ALAIN', '10238565', '970490708', 'alaine7673@hotmail.com', '10238565', 1, '2020-01-01 00:00:00'),
(1070, 4, 28, 4, 'SERGIO PEÑA BORDON', '75224240', '972711251', 'spena@grupoinversionesgyc.pe', '75224240', 1, '2020-01-01 00:00:00'),
(1071, 84, 28, 3, 'RAMOS PALOMINO CHRISTIAN', '47231285', '972315759', 'christiaramos9@gmail.com', '47231285', 1, '2020-01-01 00:00:00'),
(1072, 32, 28, 2, 'NARREA ALMENARA JONATAN', '70304644', '99999', 'ccvvv@ghjnn', '70304644', 1, '2020-01-01 00:00:00'),
(1073, 15, 28, 2, 'ROBLES MAYTAHUARI DAVID BENJAMIN', '71186233', '99999999', 'mnnnnnn@nnnnn', '71186233', 1, '2020-01-01 00:00:00'),
(1074, 3, 28, 2, 'TERAN QUISPE JUNIOR ALEXIS', '72693464', '9999999', 'nnnnn@nnnn', '72693464', 1, '2020-01-01 00:00:00'),
(1075, 3, 28, 2, 'LAVADO VEGA DIEGO ANTONIO', '74638631', '99999999', 'nnnnnnnn@nnnnnnn', '74638631', 1, '2020-01-01 00:00:00'),
(1076, 2, 28, 2, 'BEDOYA BRANCACHO JONNY', '46713130', '99999999', 'nnnnn@nnnnnn', '46713130', 1, '2020-01-01 00:00:00'),
(1077, 2, 28, 2, 'CAPILLO ARCOS JULIO CESAR', '42966908', '9999999', 'nnnnn@nnnnn', '42966908', 1, '2020-01-01 00:00:00'),
(1078, 3, 28, 2, 'RODRIGUEZ JAURE ALEXIS', '78551683', '99999999', 'nnnnnn@nnnn', '78551683', 1, '2020-01-01 00:00:00'),
(1079, 3, 28, 2, 'FRANCO RAMÍ­REZ JUNIOR', '74388586', '99999999', 'nnnnn@nnnnn', '74388586', 1, '2020-01-01 00:00:00'),
(1080, 1, 28, 2, 'MURRUGARRA CORTEZ CESAR', '80523178', '9999999', 'nnnnn@nnnnnn', '80523178', 1, '2020-01-01 00:00:00'),
(1081, 32, 28, 2, 'FRANCO CARRILLO EDGAR', '08664247', '9999999', 'nnnnn@nnnnn', '08664247', 1, '2020-01-01 00:00:00'),
(1082, 1, 28, 2, 'GUZMÁN SANCHEZ EDUARDO', '09032459', '9999999', 'nnnnn@nnnnn', '09032459', 1, '2020-01-01 00:00:00'),
(1083, 32, 28, 2, 'DUEÑAS ARRATEA DAVID', '48488736', '999999', 'nnnn@nnnn', '48488736', 1, '2020-01-01 00:00:00'),
(1084, 3, 28, 2, 'YVAN DANTE ARQUEROS GAMBOA', '41987342', '41987342', '41987342@GMAIL.COM', '41987342', 1, '2020-01-01 00:00:00'),
(1085, 3, 28, 2, 'RONALD ARROYO YATACO', '76028937', '76028937', '76028937@GMAIL.COM', '76028937', 1, '2020-01-01 00:00:00'),
(1086, 3, 28, 2, 'PAULINO ESPINOZA MORENO ', '23100139', '23100139', '23100139@GMAIL.COM', '23100139', 1, '2020-01-01 00:00:00'),
(1087, 3, 28, 2, 'FIDARDO ALEX GALARZA MALLQUI', '41093398', '41093398', '41093398@GMAIL.COM', '41093398', 1, '2020-01-01 00:00:00'),
(1088, 3, 28, 2, 'FRANK GALARZA RAMOS ', '74568417', '74568417', '74568417@GMAIL.COM', '74568417', 1, '2020-01-01 00:00:00'),
(1089, 3, 28, 2, 'HUAYCHA CABANA ALEJANDRO ', '43239448', '43239448', '43239448@GMAIL.COM', '43239448', 1, '2020-01-01 00:00:00'),
(1090, 3, 28, 2, 'LUIS ANGEL NICASIO SANCHEZ', '76081221', '76081221', '76081221@GMAIL.COM', '76081221', 1, '2020-01-01 00:00:00'),
(1091, 3, 28, 2, 'JOSE ANTONIO PACHA SALVADOR', '42278950', '42278950', '42278950@GMAIL.COM', '42278950', 1, '2020-01-01 00:00:00'),
(1092, 3, 28, 2, 'PETER VILLEGAS MENDRO', '42506376', '42506376', '42506376@GMAIL.COM', '42506376', 1, '2020-01-01 00:00:00'),
(1093, 84, 28, 4, 'JORGE DELFIN LIZARBE CHAVEZ', '09788142', '09788142', '09788142@GMAIL.COM', '09788142', 1, '2020-01-01 00:00:00'),
(1094, 3, 28, 2, 'ARMAS SANANCINA RENAN ', '40267596', '40267596', '40267596@gmail.com', '40267596', 1, '2020-01-01 00:00:00'),
(1095, 3, 28, 2, 'ALVA VICTORIO OSCAR DANIEL ', '77147163', '77147163', '77147163@gmail.com', '77147163', 1, '2020-01-01 00:00:00'),
(1096, 3, 28, 2, 'HUAMAN MERINO WILLY BASILIO ', '10342246', '10342246', '10342246@gmail.com', '10342246', 1, '2020-01-01 00:00:00'),
(1097, 3, 28, 2, 'FLORES VITALIANO CHRISTIAN JONATHAN', '44403981', '44403981', '44403981@gmail.com', '44403981', 1, '2020-01-01 00:00:00'),
(1098, 3, 28, 2, 'PINTADO FLORES GERARDO', 'O6877113', 'O6877113', 'O6877113@gmail.com', 'O6877113', 1, '2020-01-01 00:00:00'),
(1099, 3, 28, 2, 'ZEGARRA MARIÑOS ELEODORO SAMIR', '60327631', '60327631', '60327631@gmail.com', '60327631', 1, '2020-01-01 00:00:00'),
(1100, 1, 28, 2, 'PIINTADO FLORES GERARDO', '06877113', '999999999', 'nnnnn@hhhhh', '06877113', 1, '2020-01-01 00:00:00'),
(1101, 1, 28, 2, 'VARGAS SANCHEZ ELVIS ', '43907762', '988888888', 'NNNNN@GGGGG', '43907762', 1, '2020-01-01 00:00:00'),
(1102, 2, 29, 2, 'TUBILLA SANCHEZ GABRIEL ALBERTO ', '46261020', '999999999', 'FDAFDF@uni.pe', '46261020', 1, '2020-01-01 00:00:00'),
(1103, 15, 5, 2, 'ARIAS PALOMINO ROBERTO', '09808653', '99999999', 'betoariasp33@gmail.com', '09808653', 0, '2020-01-01 00:00:00'),
(1104, 1, 28, 2, 'JIMENEZ PROLEON GIOVANNY', '48274633', '898888', 'nnnn@nnnnn', '48274633', 1, '2020-01-01 00:00:00'),
(1105, 3, 28, 2, 'BOLAÑOS BRIONES ITALO', '42333146', '987656666', 'skskskks@hotmail.com', '42333146', 1, '2020-01-01 00:00:00'),
(1106, 1, 28, 2, 'HUAYHUA CAMPOSANO ANGEL JOAQUIN', '73857244', '966666666', 'JJJJA@HOTMAIL.COM', '73857244', 1, '2020-01-01 00:00:00'),
(1107, 1, 28, 2, 'LAURIANO GIRON RODRIGO FRANK', '77435362', '911111111', 'AAA@GMAIL.COM', '77435362', 1, '2020-01-01 00:00:00'),
(1108, 2, 28, 2, 'LETONA MELGAREJO CIRILO', '08667397', '933333333', 'LJEIEI@HOTMAIL.COM', '08667397', 1, '2020-01-01 00:00:00'),
(1109, 2, 28, 2, 'ÑIQUEN LIZA JOSE JAVIER', '44075248', '977777777', 'AAAA@HOTMAIL.COM', '44075248', 1, '2020-01-01 00:00:00'),
(1110, 3, 28, 2, 'REATEGUI NUBE LEYCER PAOLO', '78969154', '944444444', 'ZZZ@HOTMAIL.COM', '78969154', 1, '2020-01-01 00:00:00'),
(1111, 67, 28, 2, 'SAAVEDRA LLAMOCTANA DAVID', '43728269', '9333333', 'WWWW@SSS', '43728269', 1, '2020-01-01 00:00:00'),
(1112, 1, 28, 2, 'TAPIA SOTELO ZOSIMO FERNANDO ', '80185928', '93333333', 'WSSS@SS', '80185928', 1, '2020-01-01 00:00:00'),
(1113, 32, 28, 2, 'VELASCO VASQUEZ DEYVI ALAIN', '46396977', '93333323', 'ASSS@AA', '46396977', 1, '2020-01-01 00:00:00'),
(1114, 72, 3, 3, 'SANDOVAL ROMERO LUIS HUMBERTO', '47432725', '926284063', 'lsandoval@arceperu.pe', '47432725', 1, '2020-01-01 00:00:00'),
(1115, 32, 31, 2, 'AYAMBO MOZOMBITE EDUARD ISRAEL', '62057408', '927989361', 'eduaraya741@gmail.comom', '62057408', 1, '2020-01-01 00:00:00'),
(1116, 86, 28, 3, 'BAÑARES QUIÑONES CARLOS ALBERTO', '73240822', '968999117', 'cbanares@grupoinversionesgyc.pe', '73240822', 1, '2020-01-01 00:00:00'),
(1117, 83, 19, 3, 'RODELGO FERNANDEZ ALBERTO', '00073498', '994214772', 'arodelgo@grupoinversionesgyc.pe', '00073498', 1, '2020-01-01 00:00:00'),
(1118, 1, 28, 2, 'ARACCA GUTIERREZ MANSUETO', '20591794', '999999999', 'NN@NNNN', '20591794', 1, '2020-01-01 00:00:00'),
(1119, 1, 28, 2, 'ARIAS VASQUEZ GABRIEL ROBINSON', '47179739', '999999999', 'CCC@CCCC', '47179739', 1, '2020-01-01 00:00:00'),
(1120, 1, 28, 2, 'SANCHEZ CAPILLO EPIFANIO JULIO', '43383402', '999999999', 'BVHH@BNK', '43383402', 1, '2020-01-01 00:00:00'),
(1121, 1, 28, 2, 'TORIBIO LOYOLA ESTEBAN', '09568245', '222222222', 'NNNN@HHHH', '09568245', 1, '2020-01-01 00:00:00'),
(1122, 1, 28, 2, 'VILLANO HUAMAN FRANCUIS', '73318211', '777777777', 'SSS@NNNN', '73318211', 1, '2020-01-01 00:00:00'),
(1123, 1, 28, 2, 'COKI CESAR ARTEAGA ALMERCO', '80631161', '999999999', 'cccc@hhhhh', '80631161', 1, '2020-01-01 00:00:00'),
(1124, 2, 2, 2, 'JUDDER MARTIN PAJUELO CHAVEZ', '25761843', '960437696', 'judderpajuelo@gmail.com', '25761843', 1, '2020-01-01 00:00:00'),
(1125, 4, 1, 3, 'CASTILLO DAMIAN ELVER FLAVIO', '75698311', '946516936', 'ecastillo@arceperu.pe', '75698311', 1, '2020-01-01 00:00:00'),
(1126, 1, 1, 2, 'GONZALES MALDONADO JOHON NESKEN', '41120979', '980536071', 'johongonzales755@g9mail.com', '41120979', 1, '2020-01-01 00:00:00'),
(1127, 38, 21, 2, 'BLANCO CRUZADO LUIS ALBERTO', '47960503', '921310476', 'lblancoc93@gmail.com', '47960503', 1, '2020-01-01 00:00:00'),
(1128, 1, 1, 2, 'BLAS ROJAS  JESUS STUARD', '47626194', '939523440', 'Blaro1920@gmail.com', '47626194', 1, '2020-01-01 00:00:00'),
(1129, 37, 5, 2, 'BRAVO RODRIGUEZ  MARCIAL EDGAR', '05362178', '927392873', 'SDASD@CASCA', '05362178', 1, '2020-01-01 00:00:00'),
(1130, 1, 1, 2, 'CASTILLO SALINAS GILMER RAI', '73008518', '5646486', 'SDASD@DSASD', '73008518', 1, '2020-01-01 00:00:00'),
(1131, 90, 1, 2, 'CONCHA VILLANUEVA  WILDER ROBERTO', '19235033', '928136911', 'robertovilla202311@gmail.com', '19235033', 1, '2020-01-01 00:00:00'),
(1132, 1, 31, 2, 'LAZO LAZARO MILTON', '45436121', '993516882', 'miltom2949@gmail.com', '45436121', 1, '2020-01-01 00:00:00'),
(1133, 11, 5, 2, 'PANTIGOSO ANDONAYRE WALTER GUILLERMO', '09892996', '864644', 'DFSDFSADF@ASD', '09892996', 1, '2020-01-01 00:00:00'),
(1134, 4, 5, 2, 'REYES INTUSCCA EDSON FRANCO', '70578439', '8564646', 'DSFASF@XDFS', '70578439', 1, '2020-01-01 00:00:00'),
(1135, 1, 5, 3, 'KATHERIN TIPISMANA VENTOCILLA ', '42965292', '5414556', 'DFASDF@DDS', '42965292', 1, '2020-01-01 00:00:00'),
(1136, 37, 5, 2, 'JUAN CARLOS YARLEQUE MORI ', '40733819', '5272727', 'DASDAS@DFSD', '40733819', 1, '2020-01-01 00:00:00'),
(1137, 42, 1, 3, 'ZAMUDIO MONTALVO  LEONARDO', '72569688', '970193505', 'leozm1994@gmail.com', '72569688', 1, '2020-01-01 00:00:00'),
(1138, 72, 3, 3, 'MORIYA CARPIO ALBERTO ICHIRO', '77136777', '914694014', 'amoriya@arceperu.pe', '77136777', 1, '2020-01-01 00:00:00'),
(1139, 72, 3, 3, 'VIZCARRA VALLADARES JORGE ANDRES', '75123338', '960064120', 'jvizcarra@arceperu.pe', '75123338', 1, '2020-01-01 00:00:00'),
(1140, 74, 31, 2, 'GALIANO RAMOS JHON', '41412649', '988232872', 'jgaliano929@gmail.com', '41412649', 1, '2020-01-01 00:00:00'),
(1141, 40, 5, 2, 'FREDDY HUMBERTO ARROYO YATACO', '08527784', '63918273', 'hdkabdk@gmail.com', '08527784', 0, '2020-01-01 00:00:00'),
(1142, 31, 31, 2, 'CRUZ CESPEDES  AQUILO', '08278387', '124323', 'hskshf@gmail.com', '08278387', 1, '2020-01-01 00:00:00'),
(1143, 11, 2, 2, 'DELGADO PONCE  JORGE LUIS', '25562746', '902411127', 'jdelgadopo@gmail.com', '25562746', 1, '2020-01-01 00:00:00'),
(1144, 40, 5, 2, 'LENIN FELIX ESPINOZA TORRES ', '08653585', '72748281', 'hdjahdi@gmail.com', '08653585', 0, '2020-01-01 00:00:00'),
(1145, 15, 2, 3, 'MORALES VASQUEZ  ALDO PAOLO', '72386166', '997709119', 'amorales@arceperu.pe', '72386166', 1, '2020-01-01 00:00:00'),
(1146, 92, 2, 3, 'QUISPE TICLLA  FRANK JUNIOR', '72549534', '993696858', 'fquispe@arceperu.pe', '72549534', 1, '2020-01-01 00:00:00'),
(1147, 15, 2, 2, 'SASAI PIZANGO  DAVID ALBERTO', '43784474', '925966153', 'dsasai.gtr@gmail.com', '43784474', 1, '2020-01-01 00:00:00'),
(1148, 9, 3, 1, 'VICUÑA SALAZAR BRIGGITTHE MARIKLER ', '73023359', '993435210', 'asistenñlllle@arceperu.pe', '73023359', 1, '2020-01-01 00:00:00'),
(1149, 40, 5, 2, 'LUIS ALBERTO AHUANARI NOLORVE ', '71974654', '52422', 'hdksbdk@gmail.com', '71974654', 1, '2020-01-01 00:00:00'),
(1150, 40, 5, 2, 'MAURO AYMA QUISPE ', '23945844', '837282', 'vdkajwj@gmail.com', '23945844', 1, '2020-01-01 00:00:00'),
(1151, 40, 5, 2, 'JORGE LUIS BARBA VALDIVIEZO ', '9857141', '372928', 'dhai@gmail.com', '9857141', 1, '2020-01-01 00:00:00'),
(1152, 40, 5, 2, 'JOAQUIN BECERRA GARCIA ', '40285698', '3738272', 'jdksb@gmail.com', '40285698', 1, '2020-01-01 00:00:00'),
(1153, 40, 5, 2, 'FAUSTO DAVID LEYVA CHAVARRI ', '09638308', '739273828', 'hskajv@gmail.com', '09638308', 1, '2020-01-01 00:00:00'),
(1154, 40, 5, 2, 'HERNAN CARLOS MUñOZ CUBA ', '80559971', '7382533', 'jdusgdh@gmail.com', '80559971', 1, '2020-01-01 00:00:00'),
(1155, 40, 5, 2, 'CLODOALDO NAVEROS FERRO ', '43692562', '4728227', 'hdjwj@gmail.com', '43692562', 1, '2020-01-01 00:00:00'),
(1156, 40, 5, 2, 'GINNO SCOTT PIZARRóN INGA ', '70192354', '2423', 'hdkak@gmail.com', '70192354', 1, '2020-01-01 00:00:00'),
(1157, 40, 5, 2, 'JULIO QUISPE MUñIZ ', '45137453', '648272', 'jaosj@gmail.com', '45137453', 1, '2020-01-01 00:00:00');
INSERT INTO `personal` (`id_personal`, `id_cargo`, `id_area`, `id_tipo`, `nom_personal`, `dni_personal`, `cel_personal`, `email_personal`, `pass_personal`, `act_personal`, `updated_at`) VALUES
(1158, 40, 5, 2, 'EDWIN DANIEL RAMóN MENA ', '10133658', '748282', 'hduau@gmail.com', '10133658', 1, '2020-01-01 00:00:00'),
(1159, 40, 5, 2, 'SANDRA ISMAEL SALDADA MENDOZA ', '43694414', '748282', 'jdlw@gmail.com', '43694414', 1, '2020-01-01 00:00:00'),
(1160, 40, 5, 2, 'LUIS SANTANA AQUINO ', '75014550', 'Y3927', 'hdiwi@gmail.com', '75014550', 1, '2020-01-01 00:00:00'),
(1161, 2, 31, 2, 'VIRHUEZ SOTO EFRAIN AUGUSTO', '15856652', '985614112', 'efrainvirhuez1968@gmail.com', '15856652', 1, '2020-01-01 00:00:00'),
(1162, 11, 1, 2, 'LEON SONAPO CESAR EDUARDO', '44855861', '985978146', 'cesarm_9288@hotmail.com', '44855861', 1, '2020-01-01 00:00:00'),
(1163, 90, 5, 2, 'CARDENAS FARFAN GUDMAR JOHAN', '41862376', '996466107', 'gjohancf@gmail.com', '41862376', 1, '2020-01-01 00:00:00'),
(1164, 32, 31, 2, 'CHAHUA JACINTO EDELBERTO', '44302922', '901072426', 'edilbertho.63@gmail.com', '44302922', 1, '2020-01-01 00:00:00'),
(1165, 32, 31, 2, 'CERNA TORRES HENRY', '41994713', '950907755', 'hct83@outlook.es', '41994713', 1, '2020-01-01 00:00:00'),
(1166, 32, 31, 2, 'VILLANUEVA HURTADO YERSON ELVIS', '44987911', '111111111', 'A@W', '44987911', 1, '2020-01-01 00:00:00'),
(1167, 31, 31, 2, 'FLORES GAMARRA RIGOBERTO VERSI', '80012629', '976546928', 'versi.21flores@gmail.com', '80012629', 1, '2020-01-01 00:00:00'),
(1168, 31, 31, 2, 'SANTILLAN TORRES RONNY ERICK', '73978831', '910997693', '10santillan11@gmail.com', '73978831', 1, '2020-01-01 00:00:00'),
(1169, 32, 31, 2, 'BARDALES PIZANGO JAIRO', '42861283', '974549934', 'jairobardales29@gmail.com', '42861283', 1, '2020-01-01 00:00:00'),
(1170, 36, 31, 1, 'ARTEAGA VIZURRAGA VALERIA', '62358806', '111111111', 'A@W', '62358806', 1, '2020-01-01 00:00:00'),
(1171, 5, 31, 3, 'VIGURIA CACERES MANUEL', '40658978', '968431652', 'A@W', '40658978', 1, '2020-01-01 00:00:00'),
(1172, 72, 31, 3, 'CASTRO CHONLON ANA ROSA', '45907513', '973800171', 'A@W', '45907513', 1, '2020-01-01 00:00:00'),
(1173, 23, 31, 2, 'ESPINOZA VILLANUEVA ERICK', '76534908', '924171444', 'eespinozavillanueva3@gmail.com', '76534908', 1, '2020-01-01 00:00:00'),
(1174, 23, 31, 2, 'MACHUCA ARELLANO CARLOS ALBERTO', '75390707', '945370685', 'machucacarlosbeto@gmail.com', '75390707', 1, '2020-01-01 00:00:00'),
(1175, 23, 31, 2, 'NICASIO CARRION JIMMY FRAN', '46403345', '942628202', 'jimmyfk_41@hotmail.com', '46403345', 1, '2020-01-01 00:00:00'),
(1176, 32, 31, 2, 'RIVAS DURVIN JOSE', '2421418', '921059038', 'durvinjoserivas@gmail.com', '2421418', 1, '2020-01-01 00:00:00'),
(1177, 3, 31, 2, 'AZUAJE PEREZ JUNIOR JOSE', '06794263', '903204059', 'juniorazuaje30267@gmail.com', '06794263', 1, '2020-01-01 00:00:00'),
(1178, 3, 31, 2, 'MONTES MENA RUDDY JOSE', '07369516', '924195800', 'salatecnicacas@gmail.com', '07369516', 1, '2020-01-01 00:00:00'),
(1179, 32, 31, 2, 'ENRIQUEZ ZACATA EDGARD', '48674646', '902671856', 'lamenacho1993@hotmail.com', '48674646', 1, '2020-01-01 00:00:00'),
(1180, 32, 31, 2, 'BRAVO HUAMANCOLE RICHARD JESUS', '09834236', '992680308', 'richar-rmjbh@hotmail.com', '09834236', 1, '2020-01-01 00:00:00'),
(1181, 32, 31, 2, 'QUINTANA PAUCAR JOSE GERARDO', '44004557', '922655283', 'quintanapaucarjose@gmail.com', '44004557', 1, '2020-01-01 00:00:00'),
(1182, 32, 31, 2, 'MURILLO SALAZAR DANIEL', '01300727', '924634318', 'daniel89641@hotmail.com', '01300727', 1, '2020-01-01 00:00:00'),
(1183, 32, 31, 2, 'FLORES SALAZAR RAUL', '07763421', '913884391', 'sararoberto100@hotmail.com', '07763421', 1, '2020-01-01 00:00:00'),
(1184, 32, 31, 2, 'HUERTO BOZA JAVIER', '46610275', '943466887', 'xhuertoboza@gmail.com', '46610275', 1, '2020-01-01 00:00:00'),
(1185, 32, 31, 2, 'PABLO MARTIN FRANKLIN EDISON', '45570472', '933690537', 'pablomartinfranklin@gmail.com', '45570472', 1, '2020-01-01 00:00:00'),
(1186, 32, 31, 2, 'LOPEZ COLINA FELIPE BRAULIO', '46800489', '963171169', 'Felipecolina19.12@gmail.com', '46800489', 1, '2020-01-01 00:00:00'),
(1187, 3, 31, 2, 'TORRES BECERRA YOMAR GIAN POUL', '72865881', '936108918', 'yomarbecerra123@gmail.com', '72865881', 1, '2020-01-01 00:00:00'),
(1188, 3, 31, 2, 'ASPAJO NORIEGA ALEX JAIRZINHO', '60954567', '900055622', 'noriegalex17@gmail.com', '60954567', 1, '2020-01-01 00:00:00'),
(1189, 3, 31, 2, 'ESPINOZA CHINCHAY KATHERINE TATIANA', '74592737', '929944066', 'katherin.espinoza.chinchay123456@gmail.com', '74592737', 1, '2020-01-01 00:00:00'),
(1190, 3, 31, 2, 'TORRES BECERRA TIFANY ANDREIT', '72945298', '997486102', 'tifanybecerra123@gmail.com', '72945298', 1, '2020-01-01 00:00:00'),
(1191, 32, 31, 2, 'ALANIA PECHO ALFREDO PEREGRINO', '71267889', '907404814', 'alaniaalfredo974@gmail.com', '71267889', 1, '2020-01-01 00:00:00'),
(1192, 2, 31, 2, 'PISCO BUITRON RAFAEL ALBERTO', '15760305', '935330915', 'piscobuitronr@gmail.com', '15760305', 1, '2020-01-01 00:00:00'),
(1193, 2, 31, 2, 'MARCHENA RAMOS VLADIMIR MARIO JACK', '72511643', '933228764', 'A@W', '72511643', 1, '2020-01-01 00:00:00'),
(1194, 2, 31, 2, 'LIZANA MENDOZA PABLO ANDRES', '45845619', '917265192', 'Pablolizana08@hotmail.com', '45845619', 1, '2020-01-01 00:00:00'),
(1195, 2, 31, 2, 'TOLENTINO ALVAREZ JESUS ANDRES', '42774006', '927774273', 'Jesusandrestolentinoalvarez@gmail.com', '42774006', 1, '2020-01-01 00:00:00'),
(1196, 41, 31, 3, 'INGA GARGATE ALICIA MARÍA', '44028784', '940260958', 'aliciaig_1512@hotmail.com', '44028784', 1, '2020-01-01 00:00:00'),
(1197, 72, 3, 3, 'CASTELO VEGA SAUL ALDO', '70440773', '980619538', 'scastelo@arceperu.pe', '70440773', 1, '2020-01-01 00:00:00'),
(1198, 56, 31, 2, 'FLORES CRISTOBAL FRANCO ANTONIO', '41062341', '928143757', 'franxto81@gmail.com', '41062341', 1, '2020-01-01 00:00:00'),
(1199, 2, 31, 2, 'CHAPA VASQUEZ HENDIR ALEJANDRO', '10590315', '981343060', 'alexhendir@hotmail.com', '10590315', 1, '2020-01-01 00:00:00'),
(1200, 32, 31, 2, 'LOAYZA HONORES IVAN MERCEDES', '41518999', '982461968', 'loayzaivan473@gmail.com', '41518999', 1, '2020-01-01 00:00:00'),
(1201, 32, 31, 2, 'MALLQUI ROMERO ORLANDO', '80104115', '929142204', 'orlandomallquiromero1@gmail.com', '80104115', 1, '2020-01-01 00:00:00'),
(1202, 32, 31, 2, 'MESTANZA ORDOÑEZ JOSE MERCEDES', '26720341', '994381563', 'mestanzaj70@gmail.com', '26720341', 1, '2020-01-01 00:00:00'),
(1203, 32, 31, 2, 'ROBLES ROMAN ZENON', '45459022', '934127238', 'zenonroblesroman@gmail.com', '45459022', 1, '2020-01-01 00:00:00'),
(1204, 32, 31, 2, 'FALCON ROQUE CESAR GUILLERMO', '76769042', '901211951', 'cesarguillermofalconroque@gmail.com', '76769042', 1, '2020-01-01 00:00:00'),
(1205, 69, 31, 2, 'CAPCHA FUENTES RIVERA ALFREDO', '10254249', '952024006', 'alfredo-samael-@hotmail.com', '10254249', 1, '2020-01-01 00:00:00'),
(1206, 3, 31, 2, 'OLIVERA PANDURO ERICK RODOLFO', '41094214', '982341409', 'olivera4109@gmail.com', '41094214', 1, '2020-01-01 00:00:00'),
(1207, 3, 31, 2, 'BASILIO CASIO FORTUNATO', '48275933', '991120267', 'fortunatobc93@gmail.com', '48275933', 1, '2020-01-01 00:00:00'),
(1208, 3, 31, 2, 'BOCANEGRA HUALINGA JUAN DIEGO', '46713353', '944394492', 'jdbocanegra27@gmail.com', '46713353', 1, '2020-01-01 00:00:00'),
(1209, 3, 31, 2, 'FALCON ROQUE JOSE LUIS', '60208838', '935132970', 'lf557484@gmail.com', '60208838', 1, '2020-01-01 00:00:00'),
(1210, 3, 31, 2, 'FALCON ROQUE MAYCOL CALIXTO', '74603739', '901593336', 'maycolcalixtofalconroque@gmail.com', '74603739', 1, '2020-01-01 00:00:00'),
(1211, 3, 31, 2, 'RAMOS MORAN DIOGENES', '08492223', '996607420', 'pichinelunico@gmail.com', '08492223', 1, '2020-01-01 00:00:00'),
(1212, 2, 31, 2, 'AVENDAÑO ORUE ABEL FERNANDO', '40218305', '900933505', 'afer_69@hotmail.com', '40218305', 1, '2020-01-01 00:00:00'),
(1213, 32, 31, 2, 'CHUQUIHUARA BASILIO ANDERSON POOL', '71777218', '959262432', 'andersochb@gmail.com', '71777218', 1, '2020-01-01 00:00:00'),
(1214, 3, 31, 2, 'DE LA CRUZ LINO JEAN PIERRE', '60653057', '918470568', 'delacruzjp.24@gmail.com', '60653057', 1, '2020-01-01 00:00:00'),
(1215, 32, 31, 2, 'ALANIA PECHO RONOEL ALEJANDRO', '47647366', '991554181', 'alejandro.12alania@gmail.com', '47647366', 1, '2020-01-01 00:00:00'),
(1216, 1, 1, 2, 'FERNANDEZ CHUQUIMAJO JOSE ISAAC', '75513849', '1111111', 'A@W', '75513849', 1, '2020-01-01 00:00:00'),
(1217, 102, 33, 1, 'BAUTISTA LEVANO PEDRO MIGUEL', '42255843', '965797893', 'pbautista@arceperu.pe', '42255843', 1, '2020-01-01 00:00:00'),
(1218, 83, 16, 4, 'DANIEL QUIROS MORAN', 'PAQ13366', '940346426', 'dquiros@arceperu.pe', 'PAQ13366', 1, '2020-01-01 00:00:00'),
(1219, 103, 3, 1, 'BUIZA REYES EVELYN', '75606439', '1111', 'A@W', '75606439', 1, '2020-01-01 00:00:00'),
(1220, 102, 19, 1, 'MALAVER ANDIA VICTOR HUGO', '43213084', '935387992', 'vmalaver17@gmail.com', '43213084', 1, '2020-01-01 00:00:00'),
(1221, 54, 31, 2, 'LUIS ANGEL PALACIOS BLANCO ', '70859762', '945149828', 'luisangelpalaciosblanco@gmail.com', '70859762', 1, '2020-01-01 00:00:00'),
(1222, 70, 31, 2, 'DEYVIS JHONY CHUGNAS	MOSQUEIRA', '73271968', '993124323', 'deyvischugnasmosqueira26@gmail.com', '73271968', 1, '2020-01-01 00:00:00'),
(1223, 103, 3, 1, 'GOMEZ TORO DAYANA ', '71977515', '959540574', 'dayanagomez.2407@gmail.com', '71977515', 1, '2020-01-01 00:00:00'),
(1224, 2, 31, 2, 'VICTOR FERNANDO PALOMINO', '49013006', '922397177', 'Victorpalomino1981@gmail.com', '49013006', 1, '2020-01-01 00:00:00'),
(1225, 74, 31, 2, 'JULIAN ALMIRON	HUAMAN', '24390188', '961226610', 'almironjulian18@gmail.com', '24390188', 1, '2020-01-01 00:00:00'),
(1226, 1, 31, 2, 'ALMIRON QUISPE WILIAN', '75869380', '936593185', 'wilianalmiron20@gmail.com', '75869380', 1, '2020-01-01 00:00:00'),
(1227, 3, 31, 2, 'RODRIGUEZ CAIRAPOMPA ARNALDO JESUS', '46702955', '948338753', 'rodriguezcairampomajesus@gmail.com', '46702955', 1, '2020-01-01 00:00:00'),
(1228, 3, 31, 2, 'MIRANDA SABOYA JUAN MANUEL', '73748070', '901428985', 'juamiranda64@gmail.com', '73748070', 1, '2020-01-01 00:00:00'),
(1229, 3, 31, 2, 'ALOR BRAVO JHUNIOR RODRIGO', '73599484', '946099058', 'brego910@gmail.com', '73599484', 1, '2020-01-01 00:00:00'),
(1230, 3, 31, 2, 'BRAVO MAGUIÑA LEONARDO JAVIER', '70451204', '922362529', 'leonardomaguina77@gmail.com', '70451204', 1, '2020-01-01 00:00:00'),
(1231, 99, 31, 2, 'ALBINA ATAUCUSI SOLIS', '41644076', '918826605', 'Hola@sitioincreible.com', '41644076', 1, '2020-01-01 00:00:00'),
(1232, 85, 31, 2, 'MIGUEL ANGEL RICALDI MENESES', '25817750', '992032777', 'tactizeus_24@hotmail.com', '25817750', 1, '2020-01-01 00:00:00'),
(1233, 1, 1, 2, 'MORENO MENDOZA JONATAN', '72433397', '982042326', 'morenomendozaj44@gmail.com', '72433397', 1, '2020-01-01 00:00:00'),
(1234, 1, 39, 2, 'OSWALDO NARVAEZ VALENZUELA', '45222509', '997688793', 'oswaldonarvaezvalenzuela@gmail.com', '45222509', 1, '2020-01-01 00:00:00'),
(1235, 2, 31, 2, 'LUIS ORLANDO CHUGNAS QUILICHE', '45452693', '916047919', 'luijhi.171288@gmail.com', '45452693', 1, '2020-01-01 00:00:00'),
(1236, 43, 39, 1, 'SAUL ISAIAS RAMIREZ PONCE ', '33432773', '999227764', 'siramirez4@yahoo.es', '33432773', 1, '2020-01-01 00:00:00'),
(1237, 3, 39, 2, 'CALLE VALLEJOS ALEX JHON', '41571732', '979350688', 'alexcallevallejos@gmail.com', '41571732', 1, '2020-01-01 00:00:00'),
(1238, 64, 31, 2, 'GONZALES GONZALES YONI', '44462547', '937569790', 'gonzalesgyoni@gmail.com', '44462547', 1, '2020-01-01 00:00:00'),
(1239, 3, 31, 2, 'VARAS LAZARO TEODULFO CONSTATE', '18129559', '980097166', 'constantevaraslazaro@gmail.com', '18129559', 1, '2020-01-01 00:00:00'),
(1240, 3, 31, 2, 'MATAMOROS	TAYPE JUAN JOSE', '46048186', '967337981', 'juanjomt06@gmail.com', '46048186', 1, '2020-01-01 00:00:00'),
(1241, 3, 31, 2, 'GUZMAN HUAMAN JOSUE', '76131768', '932257063', 'guzmanhuamanjosue@gmail.com', '76131768', 1, '2020-01-01 00:00:00'),
(1242, 3, 31, 2, 'TUPIA LIMACO HENRY', '76319871', '976237093', 'HENRYTUPIA828@GMAIL.COM', '76319871', 1, '2020-01-01 00:00:00'),
(1243, 23, 39, 2, 'FABIO WILDER TANGO PEREZ', '72857504', '985772796', 'fabio.tangoa@urp.edu.pe', '72857504', 1, '2020-01-01 00:00:00'),
(1244, 45, 2, 3, 'MORENO DONAYRE ROBERTO AUGUSTO', '45300493', '912928062', 'moredonay@gmail.com', '45300493', 1, '2020-01-01 00:00:00'),
(1245, 91, 2, 3, 'URRUTIA PAUCAR OSCAR', '10113636', '960488038', 'oscar.urrutia.p@gmail.com', '10113636', 1, '2020-01-01 00:00:00'),
(1246, 1, 39, 2, 'MARTHANS CAHUANA	HERCULES ROSENDO', '41817640', '955294813', 'herculesrosendomarthanscahuana@gmail.com', '41817640', 1, '2020-01-01 00:00:00'),
(1247, 1, 39, 2, 'HURTADO CAJAHUANCA ROBERT HENRY', '42826800', '904424644', 'hurtadorobert458@gmail.com', '42826800', 1, '2020-01-01 00:00:00'),
(1248, 1, 39, 2, 'MAYON CHUQUIYAURI JUAN CARLOS', '45793297', '917878882', 'mayonchuquiyaurij@gmail.com', '45793297', 1, '2020-01-01 00:00:00'),
(1249, 1, 39, 2, 'RAMIREZ TABOADA EDWAR', '41046577', '989003309', 'EDU_RAMIREZ09@HOTMAIL.COM', '41046577', 1, '2020-01-01 00:00:00'),
(1250, 3, 39, 2, 'ARIZA CAMPOS	ELVIS', '71642004', '921488401', 'Elvisarizacampos@gmail.com', '71642004', 1, '2020-01-01 00:00:00'),
(1251, 1, 39, 2, 'FERNANDEZ VASQUEZ JAIME', '44199981', '982949678', 'Jaifervaz912@gmail.com', '44199981', 1, '2020-01-01 00:00:00'),
(1252, 3, 39, 2, 'AVALOS LUNA JOSE', '72959855', '916456784', 'joseavalosluna1995@gmail.com', '72959855', 1, '2020-01-01 00:00:00'),
(1253, 3, 39, 2, 'SANTIAGO	ESPINOZA	EDUARDO', '74223562', '930665736', 'santiagoespinozaeduardo6e@gmail.com', '74223562', 1, '2020-01-01 00:00:00'),
(1254, 1, 39, 2, 'HUAMAN SIHUINCHA	MIGUEL', '40411949', '921624290', 'miguelsihuincha390@gmail.com', '40411949', 1, '2020-01-01 00:00:00'),
(1255, 1, 39, 2, 'ALVARADO	ORIZANO REYNALDO', '47796786', '925481914', 'Reynaldoalva01@gmail.com', '47796786', 1, '2020-01-01 00:00:00'),
(1256, 74, 39, 2, 'SáNCHEZ	ESPILCO LEONIDAS', '26711583', '992206278', 'Leosanchez.1504@gmail.com', '26711583', 1, '2020-01-01 00:00:00'),
(1257, 3, 31, 2, 'QUIROZ	BUSTAMANTE ELMER', '71237521', '993844236', 'Quirozelmer.qb18@gmail.com', '71237521', 1, '2020-01-01 00:00:00'),
(1258, 1, 39, 2, 'QUIÑONES SALINAS AURELIO', '70937438', '949104866', 'aurelioquinonessalinas@gmail.com', '70937438', 1, '2020-01-01 00:00:00'),
(1259, 91, 31, 3, 'SOTELO CARDENAS ERIK IVAN', '46365077', '934868321', 'esotelo@arceperu.pe', '46365077', 1, '2020-01-01 00:00:00'),
(1260, 2, 31, 2, 'MONTALVAN GOMEZ LUIS ALEJANDRO', '42133924', '993451119', 'macuchada108@hotmail.com', '42133924', 1, '2020-01-01 00:00:00'),
(1261, 2, 31, 2, 'CONDORI MORALES MARIO', '29349331', '957762400', '7.mariocondorimorales@gmail.com', '29349331', 1, '2020-01-01 00:00:00'),
(1262, 1, 31, 2, 'ALMIRON QUISPE JHON', '74979931', '930722806', 'jhon.almiron1994@gmail.com', '74979931', 1, '2020-01-01 00:00:00'),
(1263, 2, 31, 2, 'MENDEZ ROSALES VICTOR ANTONIO', '41976978', '992239869', 'vicmenrosales@gmail.com', '41976978', 1, '2020-01-01 00:00:00'),
(1264, 3, 31, 2, 'PUENTE ESPINOZA CRESENCIO', '42493424', '990800210', 'cresenciopuente12@hotmail.com', '42493424', 1, '2020-01-01 00:00:00'),
(1265, 64, 39, 2, 'SANCHEZ ACUÑA CRISTHIAN EDILBERTO', '72878567', '975732417', 'maquinariassanchezsac@hotmail.com', '72878567', 1, '2020-01-01 00:00:00'),
(1266, 32, 31, 2, 'RODRIGUEZ PEREZ JESUS ROBERTO', '73231669', '946734040', 'jrodriguezperez354@gmail.com', '73231669', 1, '2020-01-01 00:00:00'),
(1267, 64, 39, 2, 'WILMER MAX QUISPE CCANTO', '74133303', '947385865', '172quispe@gmail.com', '74133303', 1, '2020-01-01 00:00:00'),
(1268, 64, 31, 2, 'MAZA SOPLAPUCO LUIS ALEXANDER', '46590328', '929329202', 'luals.1703@gmail.com', '46590328', 1, '2020-01-01 00:00:00'),
(1269, 64, 39, 2, 'SANCHEZ QUISPE	EDILBERTO', '20037704', '986100865', 'sanchezquispeedilberto12@gmail.com', '20037704', 1, '2020-01-01 00:00:00'),
(1270, 2, 36, 2, 'GALLARDO TELLO HAROLD WILDER', '42051864', '921047126', 'jared77set@gmail.com', '42051864', 1, '2020-01-01 00:00:00'),
(1271, 1, 31, 2, 'SAENZ COSME DANY WILLIANS', '71769622', '983843851', 'danysaenzcosme1990@gmail.com', '71769622', 1, '2020-01-01 00:00:00'),
(1272, 85, 31, 2, 'SOTOMAYOR	ANAMPA	ENRIQUE AMADEO', '47034107', '976571179', 'Enriquetlv1992@gmail.com', '47034107', 1, '2020-01-01 00:00:00'),
(1273, 3, 31, 2, 'MONTENEGRO SOLIS ADRIAN LEONEL', '45883333', '991702265', 'Leonel.montenegro.solis@gmail.com', '45883333', 1, '2020-01-01 00:00:00'),
(1274, 73, 3, 1, 'PAREDES ESPIRITU XIMENA', '73875654', '945131293', 'xparedes@arceperu.pe', '73875654', 1, '2020-01-01 00:00:00'),
(1275, 1, 39, 2, 'SERNA MILLONES YONATHAN JESúS DAVID', '43950151', '937652196', 'Jonathanserna831@gmail.com', '43950151', 1, '2020-01-01 00:00:00'),
(1276, 43, 39, 3, 'PELAEZ SANTILLAN MANUEL ', '10790633', '944538994', 'mpelaeztrabajos@gmail.com', '10790633', 1, '2020-01-01 00:00:00'),
(1277, 1, 39, 2, 'MARTINEZ SALAZAR DAVID MARTIN', '43628082', '991153227', 'martinezdavud49@gmail.com', '43628082', 1, '2020-01-01 00:00:00'),
(1278, 1, 39, 2, 'FERNANDEZ VALLEJOS EDWIN', '43836503', '983773586', 'fernandezvallejosedwin9@gmail.com', '43836503', 1, '2020-01-01 00:00:00'),
(1279, 31, 36, 2, 'VILCA ALIAGA ABEL RICARDO', '72590742', '929916344', 'abelvilcaaliaga@gmail.com', '72590742', 1, '2020-01-01 00:00:00'),
(1280, 2, 36, 2, 'PULIDO CONDOR DEYVIS ARNOLD ', '73030676', '970516803', 'deyvis1911s@gmail.com', '73030676', 1, '2020-01-01 00:00:00'),
(1281, 31, 36, 2, 'VASQUEZ PAUCAR JOSE HUGO', '10234909', '995582595', 'johu.01.3125@gmail.com', '10234909', 1, '2020-01-01 00:00:00'),
(1282, 41, 36, 3, 'PULGAR LUCAS MARCELO GERARDO', '77553837', '902183283', 'marcelopulgarlucas@gmail.com', '77553837', 1, '2020-01-01 00:00:00'),
(1283, 2, 36, 2, 'EUSEBIO GOMEZ RICHARD', '10557678', '924445268', 'march_0226@hotmail.com', '10557678', 1, '2020-01-01 00:00:00'),
(1284, 32, 31, 2, 'FRANKLIN', '41363281', '956164292', 'leonhumanifrankilin2@gmail.com', '41363281', 1, '2020-01-01 00:00:00'),
(1285, 70, 31, 2, 'YOMAR', '48442567', '927306711', 'yhomercorrea94@gmail.com', '48442567', 1, '2020-01-01 00:00:00'),
(1286, 31, 31, 2, 'MIGUEL ANGEL', '75267621', '917955466', 'angel.994272159@gmail.com', '75267621', 1, '2020-01-01 00:00:00'),
(1287, 31, 31, 2, 'ANDERSON ROMULO', '48505003', '946441587', 'anunezvarillas@gmail.com', '48505003', 1, '2020-01-01 00:00:00'),
(1288, 36, 33, 1, 'XIOMARA  BELLEN AMPUERO MACHACA', '76528528', '992227639', 'XAMPUERO@ARCEPERU.PE', '76528528', 1, '2020-01-01 00:00:00'),
(1289, 5, 31, 3, 'CAMPOS MENDO	XIOMARA GERALDINE', '72857952', '984126934', 'camposmendoxiomara@gmail.com', '72857952', 1, '2020-01-01 00:00:00'),
(1290, 2, 31, 2, 'HINOSTROZA HUAMANCHAGUA	WILIANS CELESTINO', '44477242', '966417502', 'wilianshinostrozahh@gmail.com', '44477242', 1, '2020-01-01 00:00:00'),
(1291, 3, 31, 2, 'AGUILAR SANTUR JAIME', '43954398', '925571026', 'aguilarsantur6@gmail.com', '43954398', 1, '2020-01-01 00:00:00'),
(1292, 3, 31, 2, 'NORIEGA REATEGUI LUIS', '61150132', '910790918', 'luisreategui65@gmail.com', '61150132', 1, '2020-01-01 00:00:00'),
(1293, 43, 31, 2, 'DE LA CRUZ SALAZAR JOSE EDUARDO', '10192394', '995356600', 'jedelacruz1974@gmail.com', '10192394', 1, '2020-01-01 00:00:00'),
(1294, 3, 31, 2, 'TAPAHUASCO LUDEÑA JIMMY JEAN', '73809068', '965042076', 'bboy-22-02@hotmail.com', '73809068', 1, '2020-01-01 00:00:00'),
(1295, 69, 31, 2, 'MENDOZA TECSI RUBEN', '10750223', '923751860', 'ttecsi7719@gmail.com', '10750223', 1, '2020-01-01 00:00:00'),
(1296, 3, 31, 2, 'AYALA ESPINOZA YEFERSON YOHAN', '72855428', '947144238', 'Yefrix7@gmail.com', '72855428', 1, '2020-01-01 00:00:00'),
(1297, 2, 2, 2, 'ABRAHAN CESAR DE LA TORRE LOPEZ', '43965998', '955591944', 'delatorrelopezcesarabrahan@gmail.com', '43965998', 1, '2020-01-01 00:00:00'),
(1298, 3, 45, 2, 'INFANTE	GUEVARA ERICK SAMIR', '78022478', '981441265', 'Erickinfanteguavara@gmail.com', '78022478', 1, '2020-01-01 00:00:00'),
(1299, 3, 45, 2, 'PEREZ CABRERA ROEL ARGEL', '74978280', '926128490', 'Roelargel30abril@icloud.com', '74978280', 1, '2020-01-01 00:00:00'),
(1300, 85, 45, 2, 'VASQUEZ LOPEZ JULIO CESAR', '43059239', '914601476', 'juliocesarvasquez280485@gmail.com', '43059239', 1, '2020-01-01 00:00:00'),
(1301, 1, 45, 2, 'PEÑA PIMENTEL DIONIL WILLIAM', '15757563', '908733284', 'dionilwilliampenapimentel@gmail.com', '15757563', 1, '2020-01-01 00:00:00'),
(1302, 1, 45, 2, 'TALENAS VALDIVIA JAVIER', '47885796', '919009371', 'talenasvaldiviajavier@gmail.com', '47885796', 1, '2020-01-01 00:00:00'),
(1303, 74, 45, 2, 'LUCIANO ESCOBAL ESTEBAN RIQUEL', '42435564', '936591581', 'estebanriquellucianoescobal@gmail.com', '42435564', 1, '2020-01-01 00:00:00'),
(1304, 1, 45, 2, 'VILLANUEVA	HURTADO ALEX', '45760334', '906974456', 'alexvillanuevahurtado15@gmail.com', '45760334', 1, '2020-01-01 00:00:00'),
(1305, 1, 45, 2, 'GUEVARA SALAS ORLANDO', '42680236', '912792308', 'Orlandoguevarasalas2@gmail.com', '42680236', 1, '2020-01-01 00:00:00'),
(1306, 1, 45, 2, 'CHOQUEHUAYTA	HUAYRA	EDGAR LUZGARDO', '80294357', '916976879', 'eddarjaque07@gmail.com', '80294357', 1, '2020-01-01 00:00:00'),
(1307, 85, 45, 2, 'QUISPE	HILASACA JULIO CESAR', '43780234', '946255242', 'julio.quispe.hilasaca@gmail.com', '43780234', 1, '2020-01-01 00:00:00'),
(1308, 3, 45, 2, 'DIAZ GARCIA	URIVE', '42999143', '900684518', 'diazurive1@gmail.com', '42999143', 1, '2020-01-01 00:00:00'),
(1309, 41, 45, 3, 'YRAOLA	RIOS TERESA', '73171660', '937760346', 'teresaluz96@gmail.com', '73171660', 1, '2020-01-01 00:00:00'),
(1310, 1, 45, 2, 'ROJAS CALDERÓN FREDY HEMERSON', '46212617', '930190988', 'fredhammerz10@gmail.com', '46212617', 1, '2020-01-01 00:00:00'),
(1311, 1, 45, 2, 'SALCEDO CHALLCO JUAN JAVIER', '44008874', '918469662', 'Jsalcedoc77@gmail.com', '44008874', 1, '2020-01-01 00:00:00'),
(1312, 1, 31, 2, 'GALLARDO MARIN JOSE HUMBERTO', '80359046', '920442356', 'cayalticity1977@gmail.com', '80359046', 1, '2020-01-01 00:00:00'),
(1313, 3, 31, 2, 'PULLCH	TAGLE JANS ETHIEL', '74489265', '960936223', 'ethiel0403@hotmail.com', '74489265', 1, '2020-01-01 00:00:00'),
(1314, 72, 3, 3, 'GIANCARLO ALBINCO FLORES', '42913145', '916947677', 'galbinco@arceperu.pe', '42913145', 1, '2020-01-01 00:00:00'),
(1315, 99, 45, 2, 'PEÑA YUCRA	GEANFRANCO  WILLIAN', '71307903', '955493027', 'geanfranco27tlv@gmail.com', '71307903', 1, '2020-01-01 00:00:00'),
(1316, 1, 1, 2, 'LUIS ALEJANDRO CARDENAS ROQUE', '47619270', '928574198', '578534@gmail.com', '47619270', 1, '2020-01-01 00:00:00'),
(1317, 1, 1, 2, 'FAUSTINO FRANCO CHAVEZ', '75515654', '942626678', 'faustino96.f23@gmail.com', '75515654', 1, '2020-01-01 00:00:00'),
(1318, 1, 1, 2, 'RUSBEL ALDAHIR CASTILLO DAMIAN', '75698313', '954777028', 'aldahircastillodamian@gmail.com', '75698313', 1, '2020-01-01 00:00:00'),
(1319, 1, 39, 2, 'LLERENA MANUYAMA	ERICK ETZON', '47549261', '974981514', 'eretllema@gmail.com', '47549261', 1, '2020-01-01 00:00:00'),
(1320, 3, 39, 2, 'NANGO VELA	DEISER MAURICIO', '74614131', '949840365', 'mauricionangovela@gmail.com', '74614131', 1, '2020-01-01 00:00:00'),
(1321, 2, 45, 2, 'PULIDO LEON EDWIN AURELIO', '10580523', '922250073', 'Edwin.pulido.leon@gmail.com', '10580523', 1, '2020-01-01 00:00:00'),
(1322, 1, 46, 2, 'CARNERO GORDILLO ALEX RAMON', '08680484', '993793920', 'carneroalex12@gmail.com', '08680484', 1, '2020-01-01 00:00:00'),
(1323, 43, 46, 3, 'GALAN PAIVA	CRISTHIAN WILFREDO', '73691556', '937674423', 'cristiangalan.945@gmail.com', '73691556', 1, '2020-01-01 00:00:00'),
(1324, 41, 46, 2, 'TORRES	OLORTEGUI	JEAN PIERRE', '73035890', '959438914', 'torresjuanpedro01@gmail.com', '73035890', 1, '2020-01-01 00:00:00'),
(1325, 72, 46, 2, 'MIRANDA LUJAN HUGO HERBERT', '45013292', '914982179', 'Latinhugh@gmail.com', '45013292', 1, '2020-01-01 00:00:00'),
(1326, 1, 46, 2, 'FERNANDEZ	TICLLACURI	ALBERTO', '19828938', '935511014', 'aft211160@gmail.com', '19828938', 1, '2020-01-01 00:00:00'),
(1327, 3, 46, 2, 'ALTAMIRANO	SAYAVERDE	ELY', '74893830', '995036256', 'altamiranosayaverde@gmail.com', '74893830', 1, '2020-01-01 00:00:00'),
(1328, 3, 46, 2, 'PAREDES TORRES VICTOR RAUL', '16803496', '930631980', 'victor_blue78@outlook.com', '16803496', 1, '2020-01-01 00:00:00'),
(1329, 2, 46, 2, 'QUISPE ROSEL FREDDY RONALD', '10001144', '983192225', 'ronaldquisperosel@gmail.com', '10001144', 1, '2020-01-01 00:00:00'),
(1330, 103, 46, 2, 'BERMUDO FLORES NERBIN DAIVES', '48850449', '956405210', 'nerbin99@gmail.com', '48850449', 1, '2020-01-01 00:00:00'),
(1331, 1, 46, 2, 'ACUÑA GUEVARA	CESAR ABAD', '44299231', '921771535', 'piscis251921@gmail.com', '44299231', 1, '2020-01-01 00:00:00'),
(1332, 3, 46, 2, 'CLAROS	INOCENTE	GILVER', '73955617', '919189560', 'infante2018comando@gmail.com', '73955617', 1, '2020-01-01 00:00:00'),
(1333, 1, 46, 2, 'ESPINOZA VALDERA JAIME ROY', '47340304', '921317039', '16roy17@gmail.com', '47340304', 1, '2020-01-01 00:00:00'),
(1334, 3, 46, 2, 'ENRIQUEZ AVENDAÑO	RIVALDO JASSON', '76405096', '931629602', 'rivaldoenrikez03@gmail.com', '76405096', 1, '2020-01-01 00:00:00'),
(1335, 3, 46, 2, 'SANDOVAL SANCHEZ	LUIS GUSTAVO', '46196335', '967581744', 'luisgustavosandovalsanchez34@gmail.com', '46196335', 1, '2020-01-01 00:00:00'),
(1336, 3, 46, 2, 'FLORES	CHIVES	LUIS OSCAR', '60066264', '941857910', 'floreschivesluis@gmail.com', '60066264', 1, '2020-01-01 00:00:00'),
(1337, 3, 46, 2, 'APARCO	HINOJOSA JORGE LUIS', '62088603', '925075747', 'luisaparcohinojosa@gmail.com', '62088603', 1, '2020-01-01 00:00:00'),
(1338, 3, 46, 2, 'NARCIZO LLANCO	JONATHAN', '47675569', '918880632', 'arianjonathanllanco@gmail.com', '47675569', 1, '2020-01-01 00:00:00'),
(1339, 3, 46, 2, 'LOPEZ	SOLARI	ERIC ISAAC', '41143626', '987491702', 'ericyuli2016@hotmail.com', '41143626', 1, '2020-01-01 00:00:00'),
(1340, 1, 46, 2, 'CAHUANA TICLLASUCA	ZENON EDGAR', '23269633', '990336672', 'cahuana_130@hotmail.com', '23269633', 1, '2020-01-01 00:00:00'),
(1341, 3, 46, 2, 'CAHUANA TICLLASUCA JOSE AMADOR', '45641839', '986755477', 'cahuanajose0105@gmail.com', '45641839', 1, '2020-01-01 00:00:00'),
(1342, 1, 19, 2, 'CIGUEÑAS MORI	MARIO', '44833152', '961760080', 'juniorsandovalc08@gmail.com', '44833152', 1, '2020-01-01 00:00:00'),
(1343, 1, 46, 2, 'VERGARA VASQUEZ	AVELINO', '46924044', '994461429', 'avelinovergara774@gmail.com', '46924044', 1, '2020-01-01 00:00:00'),
(1344, 1, 46, 2, 'VERGARA CARRANZA	RONI NELVER', '76556293', '925296674', 'Vergaracarranzaronny@gmail.com', '76556293', 1, '2020-01-01 00:00:00'),
(1345, 1, 46, 2, 'PANDO PAREDES	FRANKLIN FLORENCIO', '46466388', '907398828', 'pandoparedesfranklin@gmail.com', '46466388', 1, '2020-01-01 00:00:00'),
(1346, 1, 46, 2, 'VERGARA MORETO	FERNANDO', '43514307', '955242332', 'Fernandovergaramoreto@gmail.com', '43514307', 1, '2020-01-01 00:00:00'),
(1347, 1, 45, 2, 'CHIROQUE	SANTILLANA JOSé CARLOS', '48568706', '998010222', 'omarsant.1991@gmail.com', '48568706', 1, '2020-01-01 00:00:00'),
(1348, 99, 46, 2, 'DIOSES	ZAMBRANO	MIA SHIRLEY', '75369474', '974566046', 'Miadioseszambrano@gmail.com', '75369474', 1, '2020-01-01 00:00:00'),
(1349, 99, 45, 2, 'SANCHEZ BRICEÑO KAREN', '72381277', '946761056', 'karenbriceno358@gmail.com', '72381277', 1, '2020-01-01 00:00:00'),
(1350, 1, 31, 2, 'HUERTO	VENTURA NEHEMIAS DAVID', '09986775', '943180511', 'nehemiashuertoventura@gmail.com', '09986775', 1, '2020-01-01 00:00:00'),
(1351, 1, 45, 2, 'CABRERA IRIGOIN RAMIRO CLEDER', '43800642', '958137677', 'cleder_r@hotmail.com', '43800642', 1, '2020-01-01 00:00:00'),
(1352, 1, 45, 2, 'ARTEAGA RIVAS PUPILIO MIKE', '22307289', '955536788', 'mikearteagarivas@gmail.com', '22307289', 1, '2020-01-01 00:00:00'),
(1353, 3, 45, 2, 'GUTIERREZ	SALAZAR LUCAS', '47184274', '900071309', 'lucgutlima@gmail.com', '47184274', 1, '2020-01-01 00:00:00'),
(1354, 3, 45, 2, 'PARCCO	ÑAHUIRIMA	JULIAN', '44381420', '968301735', 'Parccojulian51@gmail.com', '44381420', 1, '2020-01-01 00:00:00'),
(1355, 3, 31, 2, 'ARTEAGA VASQUEZ MIKE JONATHAN', '70830250', '916540049', 'Arteagavasquez2000@icloud.com', '70830250', 1, '2020-01-01 00:00:00'),
(1356, 3, 45, 2, 'BOZA TAIPE SAMUEL', '46063993', '940062610', 'samuelbozataipe361@gmail.com', '46063993', 1, '2020-01-01 00:00:00'),
(1357, 1, 45, 2, 'GOMEZ	ESPINOZA YONATAN', '46288174', '933026643', 'yonatange02@gmail.com', '46288174', 1, '2020-01-01 00:00:00'),
(1358, 3, 45, 2, 'ABREGO CRUZ JORGE LUIS', '77343215', '994196316', 'carlosabregoo72@gmail.com', '77343215', 1, '2020-01-01 00:00:00'),
(1359, 3, 45, 2, 'CHINCHAY PONCIANO PAHOLO CESAR', '46385077', '941142609', 'paholo.chinchay90@gmail.com', '46385077', 1, '2020-01-01 00:00:00'),
(1360, 85, 45, 2, 'SALAZAR VALDIVIESO	JONAS GILMER', '61280330', '954977068', 'salazarvaldiviesojonas35@gmail.com', '61280330', 1, '2020-01-01 00:00:00'),
(1361, 31, 2, 2, 'STEFAN JHOSEPH MENDOZA ROJAS', '75321279', '900474201', 'dominusdg999@gmail.com', '75321279', 1, '2020-01-01 00:00:00'),
(1362, 37, 2, 2, 'JACKSON MARIN RAMIREZ', '41515708', '957738825', 'Jacksonmarinramirez38@gmail.com', '41515708', 1, '2020-01-01 00:00:00'),
(1363, 1, 45, 2, 'CHERO LIBERATO	WILFREDO IVAN', '09738703', '902382995', 'cheroliberatowilfredo@gmail.com', '09738703', 1, '2020-01-01 00:00:00'),
(1364, 41, 31, 2, 'GOMEZ	AREVALO MAGGIESTHEL RUBBY', '75572511', '945400238', '12magomez12@gmail.com', '75572511', 1, '2020-01-01 00:00:00'),
(1365, 1, 45, 2, 'CHOQUE  ARMOTO JIMMY', '10658171', '961538143', 'Choque.jimmy.armoto@gmail.com', '10658171', 1, '2025-12-02 09:31:41'),
(1366, 1, 39, 2, 'CASTILLO LINO SANTIAGO', '76469506', '932086354', 'santiagocastillolino65@gmail.com', '76469506', 1, '2020-01-01 00:00:00'),
(1367, 3, 39, 2, 'ALBáN ARCA MANUEL EDUARDO', '76398959', '923470842', 'manueleduardoalbanarca@gmail.com', '76398959', 1, '2020-01-01 00:00:00'),
(1368, 74, 31, 2, 'LOPEZ DE LA PAZ	JORGE ENRIQUE', '10131014', '936437851', 'Jorgedelapaz1975@gmail.com', '10131014', 1, '2020-01-01 00:00:00'),
(1369, 1, 2, 2, 'OSCAR GARCIA MORENO', '00112583', '918833753', 'detapascongervy@gmail.com', '00112583', 1, '2020-01-01 00:00:00'),
(1370, 38, 21, 2, 'HUERTA ESCOBAR VICTOR HERNAN', '44946868', '951729214', 'hernanhuert0@gmail.com', '44946868', 1, '2020-01-01 00:00:00'),
(1371, 83, 33, 2, 'IGNACIO HERAS', '12345678', '9', 'ignacioherasgarcia@gmail.com', '12345678', 1, '2020-01-01 00:00:00'),
(1372, 29, 8, 1, 'DANIEL JESúS MACHACA PAREDES', '74702119', '934413787', 'dmachaca@arceperu.pe', '74702119', 1, '2020-01-01 00:00:00'),
(1373, 72, 3, 3, 'VALERI MARTIN SILVA PERALES', '10881012', '908537066', 'nn@nnnn.com', '10881012', 1, '2020-01-01 00:00:00'),
(1374, 25, 17, 3, 'LUIS MENDOZA', '25433630', '955582256', 'nnn.nnn@vvvv.com', '25433630', 1, '2020-01-01 00:00:00'),
(1375, 90, 2, 2, 'MALAGA  VASQUEZ DANNY MAYKOL', '42502231', '991252789', 'danny.maycol.malaga.vasquez@gmail.com', '42502231', 1, '2020-01-01 00:00:00'),
(1376, 103, 46, 2, 'RAMOS MARCA LUZ YODINA', '70910211', '986143782', 'Luzramosmarca@gmail.com', '70910211', 1, '2020-01-01 00:00:00'),
(1377, 32, 45, 2, 'DAVILA SILVA	RICHAR PAUL', '16663816', '925220233', 'Richar.p.davila@gmail.com', '16663816', 1, '2020-01-01 00:00:00'),
(1378, 32, 31, 2, 'LIMACHE ROMERO PEDRO MARCELINO', '20069175', '956869870', 'Pedro2@gmail.com', '20069175', 1, '2020-01-01 00:00:00'),
(1379, 32, 31, 2, 'RIOJA BRAVO EDER JORGINHO', '76528146', '923999697', 'eder.2394.eder@gmail.com', '76528146', 1, '2020-01-01 00:00:00'),
(1380, 32, 31, 2, 'SOTELO	SABUCO	JIMMY DAVID', '47547341', '923667013', 'Jimmyss.825@gmail.com', '47547341', 1, '2020-01-01 00:00:00'),
(1381, 2, 31, 2, 'RODRIGUEZ	PAREDES HECTOR', '46633053', '991477768', 'Hector_rp_125@hotmail.com', '46633053', 1, '2020-01-01 00:00:00'),
(1382, 5, 36, 2, 'VILLAR VERA	JORGE HUBER', '20047035', '947322198', 'jvvtecsur20047035@gmail.com', '20047035', 1, '2020-01-01 00:00:00'),
(1383, 32, 45, 2, 'PAREDES CISNEROS JUAN DIEGO', '76061388', '935670559', 'Juandiegoparedescisneros7@gmail.com', '76061388', 1, '2020-01-01 00:00:00'),
(1384, 2, 45, 2, 'DIAZ BAZAN RENATO', '16662872', '986137806', 'Igordebazan@gmail.com', '16662872', 1, '2020-01-01 00:00:00'),
(1385, 2, 45, 2, 'ERIK ORTIZ CACERES', '45303890', '921 980 6', 'ortiscacereserick@gmail.com', '45303890', 1, '2020-01-01 00:00:00'),
(1386, 43, 18, 3, 'LUIS ALBERTO FLORES VIERA', '03687267', '993490082', 'nnnnn@mmmm.kkl', '03687267', 1, '2020-01-01 00:00:00'),
(1387, 2, 45, 2, 'CASTAÑEDA	SAAVEDRA MARCO ANTONIO', '25831117', '924061365', 'Castanedamarcoantonio307@gmail.com', '25831117', 1, '2020-01-01 00:00:00'),
(1388, 2, 45, 2, 'AGUILAR MARQUINA JUAN CARLOS', '44875811', '993669957', 'juanca_16_872@hotmail.com', '44875811', 1, '2020-01-01 00:00:00'),
(1389, 41, 36, 3, 'GAONA MANOSALVA LUIS ALBERTO', '45726184', '928675576', 'lgaona@arceperu.pe', '45726184', 1, '2020-01-01 00:00:00'),
(1390, 99, 45, 2, 'YUMBATO CELIS ANNIE GLORIA', '71696541', '929687794', 'anniegloriayumbato@gmail.com', '71696541', 1, '2020-01-01 00:00:00'),
(1391, 32, 45, 2, 'PINTO POMAJAMBO JULIO CESAR', '71729732', '902956283', 'juliopomajambo1991@gmail.com', '71729732', 1, '2020-01-01 00:00:00'),
(1392, 9, 31, 2, 'CHAPA LEON ANTONY ALEJANDRO', '74927842', '944533898', 'antonyalejandro1305@gmail.com', '74927842', 1, '2020-01-01 00:00:00'),
(1393, 72, 3, 3, 'DAVID YUL LUI ROMERO DIAZ', '46839171', '986889793', 'drd2210901120@gmail.com', '46839171', 1, '2020-01-01 00:00:00'),
(1394, 72, 3, 3, 'JUAN ALBERTO OLMEDA VARA', '41867042', '902879368', 'jaov.olmeda@gmail.com', '41867042', 1, '2020-01-01 00:00:00'),
(1395, 72, 3, 3, 'VALENTIN MATTO VIL ARNOLD', '72363311', '925072505', 'valentinmattoarnold@gmail.com', '72363311', 1, '2020-01-01 00:00:00'),
(1396, 72, 3, 3, 'JOSé ANTONIO SALCEDO PALOMINO', '71715026', '981744184', 'josesalcedopalomino@gmail.com', '71715026', 1, '2020-01-01 00:00:00'),
(1397, 99, 45, 2, 'VIZCARRA RIVERA CINTIA MARIBEL', '42441488', '916116442', 'lilianavizcarrarivera@gmail.com', '42441488', 1, '2020-01-01 00:00:00'),
(1398, 1, 31, 2, 'CHIPANA QUISPE	ELGAR', '40531183', '975576973', 'elgar18ch@gmail.com', '40531183', 1, '2020-01-01 00:00:00'),
(1399, 1, 45, 2, 'NAPUCHI CACHIQUE RAMON', '44210551', '943658819', 'rnapuchicachique@gmail.com', '44210551', 1, '2020-01-01 00:00:00'),
(1400, 1, 31, 2, 'GUILLEN	GRANDEZ DIEGO ROBERTO', '74130153', '902461466', 'Guillendiego989@gmail.com', '74130153', 1, '2020-01-01 00:00:00'),
(1401, 1, 31, 2, 'GLASTON ALVEZ ABRAHAM', '48634519', '906983222', 'Abrahamglastonalvez5@gmail.com', '48634519', 1, '2020-01-01 00:00:00'),
(1402, 3, 45, 2, 'CHAVEZ	OCHOA	JOSE ANTONIO', '43261798', '902924221', 'joschavochoa100@gmail.com', '43261798', 1, '2020-01-01 00:00:00'),
(1403, 64, 31, 2, 'LEON CUJES	ANA MARIA', '10095443', '943474686', 'analeoncujes8@gmail.com', '10095443', 1, '2020-01-01 00:00:00'),
(1404, 3, 46, 2, 'MAYMA	VALDIVIA	JOSUE CRISTOFER', '71186234', '904459575', 'damarismayma08@gmail.com', '71186234', 1, '2020-01-01 00:00:00'),
(1405, 3, 46, 2, 'GAMBOA	LLACTAHUAMAN	EDISON', '76862233', '972942723', 'edison181097@gmail.com', '76862233', 1, '2020-01-01 00:00:00'),
(1406, 1, 46, 2, 'CARLOS	MANRIQUE	EDILBERTO NEMESIO', '32521951', '914217203', 'Carlosmanriqueedilberto@gmail.com', '32521951', 1, '2020-01-01 00:00:00'),
(1407, 1, 46, 2, 'BUSTAMANTE	INGA	RAMIRO', '03497430', '968185953', 'bustamanteingaramiro@gmail.com', '03497430', 1, '2020-01-01 00:00:00'),
(1408, 1, 46, 2, 'GARCIA	FERNANDEZ	JUAN SANTIAGO', '70913801', '970348457', 'samiimport.peru17@gmail.com', '70913801', 1, '2020-01-01 00:00:00'),
(1409, 2, 46, 2, 'COMUN	ROMO	CESAR JAIME', '40847944', '940010251', 'ccomun1981@hotmail.com', '40847944', 1, '2020-01-01 00:00:00'),
(1410, 85, 45, 2, 'VALDIVIA	ARANDA	LUIS ALBERTO', '41148969', '993372394', 'luchovaldivia40@gmail.com', '41148969', 1, '2020-01-01 00:00:00'),
(1411, 99, 45, 2, 'DIOSES	ZAMBRANO	IARA KIMBERLY', '75369473', '935908529', 'iaritakimberlyd@gmail.com', '75369473', 1, '2020-01-01 00:00:00'),
(1412, 3, 45, 2, 'PEREZ	ISUIZA	FRANSISCO', '46535142', '994260922', 'Francisco45089@gmail.com', '46535142', 1, '2020-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `id_producto_tipo` int(11) NOT NULL,
  `id_material_tipo` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `cod_material` longtext DEFAULT NULL,
  `nom_producto` longtext DEFAULT NULL,
  `nser_producto` longtext DEFAULT NULL COMMENT 'Numero de serie',
  `mod_producto` longtext DEFAULT NULL,
  `mar_producto` longtext DEFAULT NULL COMMENT 'Modelo',
  `det_producto` longtext DEFAULT NULL COMMENT 'detalle',
  `hom_producto` longtext DEFAULT NULL,
  `fuc_producto` date DEFAULT NULL COMMENT 'fecha de ultimo calibrado (MATERIALES)',
  `fpc_producto` date DEFAULT NULL COMMENT 'fecha de proximo calibrado (MATERIALES)',
  `dcal_producto` longtext DEFAULT NULL COMMENT 'documento justifiante de calibrado',
  `fuo_producto` date DEFAULT NULL COMMENT 'fecha de ultimo operatividad (MATERIALES)',
  `fpo_producto` date DEFAULT NULL COMMENT 'fecha de proximo operatividad (MATERIALES)',
  `dope_producto` longtext DEFAULT NULL COMMENT 'documento justifiante de operatividad',
  `est_producto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `id_producto_tipo`, `id_material_tipo`, `id_unidad_medida`, `cod_material`, `nom_producto`, `nser_producto`, `mod_producto`, `mar_producto`, `det_producto`, `hom_producto`, `fuc_producto`, `fpc_producto`, `dcal_producto`, `fuo_producto`, `fpo_producto`, `dope_producto`, `est_producto`) VALUES
(1, 1, 2, 1, '105010001', 'MOSQUETON DOBLE SEGURO, 25KN', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(2, 1, 3, 1, '105010002', 'CUERDA SEMIESTATICA DE 11MM X 40MT', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(3, 1, 3, 1, '105010003', 'LINEA DE CONEXI?N DE 0.25MM (XN025) Y FRENO FIJO PARA CUERDA DE 9 A 13MM (FA913F)', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(4, 1, 3, 1, '105010004', 'FRENO DE ALUMINIO PARA SOGA DE 9MM', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(5, 1, 3, 1, '105010005', 'CUERDA SEMIESTATICA TIPO A 100 MT', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(6, 1, 3, 1, '105010006', 'LINEA DE CONEXI?N CON GANCHO DE 3/4\" Y FRENO ? 9 - 13MM  MOD IN 8093', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(7, 1, 3, 1, '105010007', 'CUERDA SEMIESTATICA DE 11MM X 50MT', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(8, 1, 3, 1, '105010008', 'ARNES DE LINIERO TIPO X 4 ARGOLLAS', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(9, 1, 3, 1, '105010009', 'ESLINGA DE POSICIONAMIENTO DOBLE EN CINTA MODELO IN 8042', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(10, 1, 3, 1, '105010010', 'ESLINGA DE POSICIONAMIENTO EN CINTA REGULABLE MODELO  IN 8041-R', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(11, 1, 3, 1, '105010011', 'CONECTOR DE ANCLAJE 2 ARGOLLAS L=1.6M', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(12, 1, 3, 1, '105010012', 'ESTROBO DE POSICIONAMIENTO DOBLE EN CINTA', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(13, 1, 3, 1, '105010013', 'POLEA ALUMINIO SIMPLE MOVIL P/SOGA HASTA 5/8\" 6750LB (30KN)', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(14, 1, 3, 1, '105010014', 'ESTROBO DE POSICIONAMIENTO EN CUERDA 14MM CON FRENO DE 3.5M', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(15, 1, 3, 1, '105010015', 'ARNES DE LINIERO TIPO H 4 ARGOLLAS Y SOPORTE LUMBAR', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(16, 1, 3, 1, '105010016', 'CONECTOR DE ANCLAJE 1 ARGOLLA L=1.80M', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(17, 1, 3, 1, '105010017', 'DESCENSOR AUTOBLOQUEANTE RIG', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(18, 1, 3, 1, '105010018', 'ANTICAIDAS RETRACTIL 1.8 MT', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(19, 1, 3, 1, '105010019', 'ESTROBO DE POSICIONAMIENTO EN CINTA REGULABLE L=1.80', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(20, 1, 3, 1, '105010020', 'LINEA DE CONEXION TIPO Y SIN AMORTIGUADOR DE CAIDA', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(21, 1, 3, 1, '105010021', 'ANTICAIDA RETRACTIL 6.1 MT', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(22, 1, 3, 1, '105010022', 'CONECTOR DE ANCLAJE DE 2 ANILLAS DE 3.20 M', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(23, 1, 3, 1, '105010023', 'CONECTOR DE ANCLAJE 1 ARGOLLA 1.60M', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(24, 1, 3, 1, '105020001', 'ESLINGA DE POLIESTER 2\"X1MTS X 2.8TN', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(25, 1, 3, 1, '105020002', 'ESLINGA DE POLIESTER 2\"X2MTS X 2.8TN', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(26, 1, 3, 1, '105020003', 'ESLINGA DE POLIESTER 3\"X2MTS X 4.2TN', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(27, 1, 3, 1, '105020004', 'ESLINGA DE POLIESTER 3\"X3MTS X 4.2TN', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(28, 1, 3, 1, '105020005', 'ESLINGA DE POLIESTER 3\"X4MTS X 4.2TN', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(29, 1, 3, 1, '105020006', 'ESLINGA DE POLIESTER 3\"X5MTS X 4.2TN', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(30, 1, 3, 1, '105020007', 'ESLINGA DE POLIESTER 3\"X8MTS X 4.2TN', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(31, 1, 3, 1, '105020008', 'FAJA RACHET DE 2\"X9M X 4.5TN', '', 'ACTIVO', 'EQUIPO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(32, 1, 3, 1, '104010001', 'LAPTOP', '', 'ACTIVO', 'EQUIPO DE TECNOLOG?A', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(33, 1, 3, 1, '104030001', 'IMPRESORA', '', 'ACTIVO', 'EQUIPO DE TECNOLOG?A', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(34, 1, 3, 1, '104040001', 'TELEVISOR', '', 'ACTIVO', 'EQUIPO DE TECNOLOG?A', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(35, 1, 3, 1, '102010001', 'ALCOHOLIMETRO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(36, 1, 3, 1, '102010002', 'MULTIMETRO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(37, 1, 3, 1, '102010003', 'PINZA AMPERIMETRICA', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(38, 1, 3, 1, '102010004', 'DETECTOR DE CABLES SUBTERRANEOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(39, 1, 3, 1, '102010005', 'CAMARA TERMOGRAFICA', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(40, 1, 1, 1, '102010006', 'CUBRE BOTAS DIELECTRICAS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(41, 1, 1, 1, '102010007', 'GUANTES DIELECTRICOS CLASE 0', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(42, 1, 1, 1, '102010008', 'GUANTES DIELECTRICOS CLASE 1', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(43, 1, 1, 1, '102010009', 'GUANTES DIELECTRICOS CLASE 2', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(44, 1, 1, 1, '102010010', 'GUANTES DIELECTRICOS CLASE 3', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(45, 1, 1, 1, '102010011', 'GUANTES DIELECTRICOS CLASE 4', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(46, 1, 1, 1, '102010012', 'ALFOMBRA AISLANTE CLASE 0', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(47, 1, 1, 1, '102010013', 'ALFOMBRA AISLANTE CLASE 3', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(48, 1, 1, 1, '102010014', 'ALFOMBRA AISLANTE CLASE 4', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(49, 1, 3, 1, '102010015', 'PERTIGA DE MANIOBRA', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(50, 1, 3, 1, '102010016', 'PERTIGA TELESCOPICA 08 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(51, 1, 3, 1, '102010017', 'PERTIGA EMBONABLE 05 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(52, 1, 3, 1, '102010018', 'TORQUIMETRO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(53, 1, 3, 1, '102010019', 'REVELADOR DE TENSION', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(54, 1, 3, 1, '102010020', 'PERTIGA TELESCOPICA 04 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(55, 1, 1, 1, '102010021', 'EXTINTOR 06 KG', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(56, 1, 1, 1, '102010022', 'EXTINTOR 12 KG', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(57, 1, 1, 1, '102010023', 'EXTINTOR 10 LBS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(58, 1, 1, 1, '102010024', 'EXTINTOR 09 KG', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(59, 1, 3, 1, '102010025', 'PERTIGA TELESCOPICA 07 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(60, 1, 3, 1, '102010026', 'NIVEL AUTOMATICO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(61, 1, 3, 1, '102010027', 'ALARMA INDIVIDUAL DE TENSION 10KV-69KV', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(62, 1, 3, 1, '102010028', 'ANALIZADOR DE INTERRUPTORES', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(63, 1, 3, 1, '102010029', 'TELUROMETRO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(64, 1, 3, 1, '102010030', 'PAT UNIPOLAR AT 220 KV', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(65, 1, 3, 1, '102010031', 'PAT TRIPOLAR AT 60KV', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(66, 1, 3, 1, '102010032', 'PAT TRIPOLAR MT 10KV', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(67, 1, 3, 1, '102010033', 'EQUIPO PRUEBAS PARA RELE DE PROTECCION MONOFASICO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(68, 1, 3, 1, '102010034', 'MEGOMETRO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(69, 1, 3, 1, '102010035', 'MALETA AMPLIFICADORA DE CORRIENTE PARA PRUEBAS PRIMARIAS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(70, 1, 3, 1, '102010036', 'EQUIPO DE PRUEBAS PRIMARIAS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(71, 1, 3, 1, '102010037', 'AMPLIFICADOR DE CORRIENTE', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(72, 1, 3, 1, '102010038', 'PERTIGA TELESCOPICA 05 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(73, 1, 3, 1, '102010039', 'EQUIPO PRUEBAS PARA RELE DE PROTECCION SECUNDARIO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(74, 1, 3, 1, '102010040', 'LUXOMETRO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(75, 1, 3, 1, '102010041', 'FASIMETRO ( DETECTOR DE FASES)', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(76, 1, 3, 1, '102010042', 'SECUENCIMETRO DIGITAL', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(77, 1, 3, 1, '102010043', 'DESTORNILLADORES DINAMOMETRICOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(78, 1, 3, 1, '102010044', 'TIERRA UNIPOLAR 10KV', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(79, 1, 3, 1, '102010045', 'PERTIGA EMBONABLE 03 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(80, 1, 3, 1, '102010046', 'ANALIZADOR DE BATERIAS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(81, 1, 3, 1, '102010047', 'MEGOMETRO 01 KV', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(82, 1, 3, 1, '102010048', 'PAT PARA CUADROS BAJA TENSION', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(83, 1, 3, 1, '102010049', 'PERTIGA 01 CUERPO TP ESCOPETA', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(84, 1, 3, 1, '102010050', 'PERTIGA EMBONABLE 02 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(85, 1, 3, 1, '102010051', 'PERTIGA TELESCOPICA 02 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(86, 1, 3, 1, '102010052', 'PERTIGA TELESCOPICA 03 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(87, 1, 3, 1, '102010053', 'PERTIGA TELESCOPICA 06 CUERPOS', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(88, 1, 3, 1, '102010054', 'TERMOHIGROMETRO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(89, 1, 3, 1, '102010055', 'TERMOMETRO INFRAROJO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(90, 1, 3, 1, '102010058', 'DETECTOR DE VOLTAJE 110V-11.4 KV', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(91, 1, 3, 1, '102010059', 'DINAMOMETRO 05 TN', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(92, 1, 3, 1, '102010060', 'MICROMETRO', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(93, 1, 3, 1, '102010061', 'VIROBULO-BRAZO SOPORTE', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(94, 1, 3, 1, '102010062', 'JUMPER PARA PAT 60 KV', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(95, 1, 3, 1, '102010063', 'ACCESORIO PARA MEDICION DE RESISTENCIA ESTATICA DINAMICA DE INTERRUPTORES', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(96, 1, 3, 1, '102010064', 'SISTEMA DE PRUEBAS DE TRANSFORMADORES Y SUBESTACIONES MULTIFUNCIONAL TRAX', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(97, 1, 3, 1, '102010065', 'ACCESORIO DE TRAX DE AT', '', 'ACTIVO', 'EQUIPOS PRUEBAS Y ENSAYOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(98, 1, 3, 1, '101010001', 'HIDROLAVADORA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(99, 1, 3, 1, '101010002', 'ELECTROBOMBA CENTRIFUGA 1HP CPM620', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(100, 1, 3, 1, '101010003', 'VIBRADOR DE CONCRETO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(101, 1, 3, 1, '101010004', 'COMPACTADORA TP. SALTARINA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(102, 1, 3, 1, '101010005', 'MARTILLO ELECTRICO DEMOLEDOR', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(103, 1, 3, 1, '101010006', 'GRUPO ELECTROGENO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(104, 1, 3, 1, '101010007', 'VIBROAPISONADOR 70 KG', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(105, 1, 3, 1, '101010008', 'ALZABOBINA TP GATA 5TN', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(106, 1, 3, 1, '101010009', 'COMPACTADORA TP PLANCHA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(107, 1, 3, 1, '101010010', 'WINCHE DE MONTAJE 240KG', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(108, 1, 3, 1, '101010011', 'COMPRESORA DE AIRE', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(109, 1, 3, 1, '101010012', 'LLAVE IMPACTO INHALAMBRICA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(110, 1, 3, 1, '101010013', 'MAQUINA DE FRENO PARA CABLE AT', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(111, 1, 3, 1, '101010014', 'MAQUINA PODADORA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(112, 1, 3, 1, '101010015', 'MAQUINA DE SOLDAR', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(113, 1, 3, 1, '101010016', 'WINCHE DE TIRO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(114, 1, 3, 1, '101010017', 'MOTOSIERRA GASOLINA 2 TIEMPOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(115, 1, 3, 1, '101010018', 'TALADRO INALAMBRICO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(116, 1, 3, 1, '101010019', 'SIERRA SABLE INALAMBRICA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(117, 1, 3, 1, '101020001', 'SIERRA CIRCULAR DE 9 1/4\"', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(118, 1, 3, 1, '101020002', 'SIERRA CIRCULAR DE 7.1/4\"', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(119, 1, 3, 1, '101020003', 'AMOLADORA 9\"', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(120, 1, 3, 1, '101020004', 'TALADRO MANUAL', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(121, 1, 3, 1, '101020005', 'CALADORA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(122, 1, 3, 1, '101020006', 'TRONZADORA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(123, 1, 3, 1, '101020007', 'CORTADORA DE CONCRETO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(124, 1, 3, 1, '101020008', 'MEZCLADORA DE CONCRETO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(125, 1, 3, 1, '101020009', 'ESMERILADORA RECTA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(126, 1, 3, 1, '101020010', 'TALADRO ATORNILLADOR', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(127, 1, 3, 1, '101020011', 'AMOLADORA 4 1/2\"', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(128, 1, 3, 1, '101020012', 'AMOLADORA 7\"', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(129, 1, 3, 1, '101020013', 'AMOLADORA INHALAMBRICA 4 1/2', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(130, 1, 3, 1, '101020014', 'ESMERIL DE BANCO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(131, 1, 3, 1, '101020015', 'PISTOLA DE CLAVOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(132, 1, 3, 1, '101020016', 'PRENSA HIDRAULICA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(133, 1, 3, 1, '101020017', 'ROTOMARTILLO INHALAMBRICO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(134, 1, 3, 1, '101020018', 'ROTOMARTILLO MANUAL', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(135, 1, 3, 1, '101020019', 'SIERRA CIRCULAR INHALAMBRICA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(136, 1, 3, 1, '101020020', 'SIERRA SABLE', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(137, 1, 3, 1, '101020021', 'TALADRO ANGULAR', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(138, 1, 3, 1, '101020022', 'TALADRO MAGNETICO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(139, 1, 3, 1, '101030001', 'APLICADOR DE SILICONA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(140, 1, 3, 1, '101030002', 'WINCHA PASACABLE', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(141, 1, 3, 1, '101030003', 'ESCALERA FV TP. TIJERA 06 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(142, 1, 3, 1, '101030004', 'ESCALERA FV TP. TIJERA 08 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(143, 1, 3, 1, '101030005', 'CARRETILLA HIDRULICA (STOCKA)', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(144, 1, 3, 1, '101030006', 'ESCALERA FV TP. TIJERA 12 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(145, 1, 3, 1, '101030007', 'ESCALERA FV SIMPLE 10 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(146, 1, 3, 1, '101030008', 'ESCALERA FV SIMPLE 12 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(147, 1, 3, 1, '101030009', 'ESCALERA FV SIMPLE 14 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(148, 1, 3, 1, '101030010', 'ESCALERA FV TP. TIJERA 10 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(149, 1, 3, 1, '101030011', 'ESCALERA FV TELESCOPICA 28 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(150, 1, 3, 1, '101030012', 'ESCALERA FV TELESCOPICA 32 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(151, 1, 3, 1, '101030013', 'MEDIDOR DE DISTANCIA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(152, 1, 3, 1, '101030014', 'ESCALERA FV SIMPLE 08 PASOS', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(153, 1, 3, 1, '101030015', 'PUNTALES METALICOS 0.30 MT CERRADO 0.60 MT ABIERTO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(154, 1, 3, 1, '101030016', 'PUNTALES METALICOS 4.50 MT CERRADO 8 MT ABIERTO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(155, 1, 3, 1, '101030017', 'PUNTALES METALICOS 1.10 MT CERRADO 1.8 MT ABIERTO', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(156, 1, 3, 1, '101030018', 'FUMIGADORA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(157, 1, 3, 1, '101050001', 'BATERIAS B 22-85', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(158, 1, 3, 1, '101050002', 'BATERIAS B 22-255', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(159, 1, 3, 1, '101050003', 'CARGADOR DE BATERIA', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(160, 1, 3, 1, '101050004', 'CONECTOR DE ANCLAJE 1.50 M', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(161, 1, 3, 1, '101050005', 'CARGADOR DE BATERIA DOBLE', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(162, 1, 3, 1, '101050006', 'BATERIA RECARGABLE', '', 'ACTIVO', 'EQUIPOS Y HERRAMIENTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(163, 1, 3, 1, '106010001', 'CARPAS PLEGABLE 3X3', '', 'ACTIVO', 'MODULO DE OBRA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(164, 1, 3, 1, '106010002', 'CASETA MADERA 2.50 X 3.50 m', '', 'ACTIVO', 'MODULO DE OBRA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(165, 1, 3, 1, '106010003', 'CASETA MADERA 2.50 X 3.00 m', '', 'ACTIVO', 'MODULO DE OBRA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(166, 1, 3, 1, '103010001', 'ESCRITORIO EN L DE MELAMINA', '', 'ACTIVO', 'MUEBLES Y ENSERES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(167, 1, 3, 1, '103010002', 'SILLA GIRATORIA', '', 'ACTIVO', 'MUEBLES Y ENSERES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(168, 1, 3, 1, '103010003', 'RACK PEDESTAL RODANTE 32\"-70 PULGADAS', '', 'ACTIVO', 'MUEBLES Y ENSERES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(169, 1, 3, 1, '103010004', 'MESA 1.60 M LARGO X 0.70 M ANCH X .75 M ALT', '', 'ACTIVO', 'MUEBLES Y ENSERES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(170, 1, 3, 1, '103010005', 'LOCKER 3 X 4 METALICO 3 CUERPOS 12 PUERTAS', '', 'ACTIVO', 'MUEBLES Y ENSERES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(171, 1, 3, 1, '103010006', 'MESA REDONDA', '', 'ACTIVO', 'MUEBLES Y ENSERES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(172, 1, 3, 1, '103020001', 'EXTRACTOR AXIAL', '', 'ACTIVO', 'MUEBLES Y ENSERES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(173, 1, 3, 1, '103020002', 'HORNO MICROONDAS', '', 'ACTIVO', 'MUEBLES Y ENSERES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(174, 1, 3, 1, '103020003', 'VENTILADOR DE PEDESTAL', '', 'ACTIVO', 'MUEBLES Y ENSERES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(175, 1, 2, 1, '701010001', 'BOMBA CONCRETERA', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(176, 1, 2, 1, '701010002', 'CONCRETO 100 KG/CM2 H67 S/BM 4\"-6\"', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(177, 1, 2, 1, '701010003', 'CONCRETO 280 KG/CM2 H67 S/BM 4\"-6\"', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(178, 1, 2, 1, '701010004', 'FACTORFC', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(179, 1, 2, 1, '701010005', 'CONCRETO 280 KG/CM2 H67 S/BM 6\"- 8\"', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(180, 1, 2, 1, '701010006', 'MINIMO DE BOMBA', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(181, 1, 2, 1, '701010007', 'CONCRETO 350 KG/CM2 H67 S/BM 4\"-6\"', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(182, 1, 2, 1, '701010008', 'RECARGO MINIMO DE CONCRETO', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(183, 1, 2, 1, '701010009', 'CONCRETO 210 KG/CM2 H67 S/BM 4\"- 6\"', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(184, 1, 2, 1, '701010010', 'CONCRETO 350 KG/CM2 H67 S/BM 4\"-6\" A 1D', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(185, 1, 2, 1, '701010011', 'CONCRETO 210 KG/CM2 H67 S/BM 4\"- 6\" 7D', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(186, 1, 2, 1, '701010012', 'CONCRETO 175 KG/CM2 H67 S/BM 4\"-6\" TIPO I 3 D', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(187, 1, 2, 1, '701020001', 'MEDIA LOZA CONCRETO', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(188, 1, 2, 1, '701020002', 'POSTE CONCRETO', '', 'CONCRETO', 'MEZCLA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(189, 1, 1, 1, '403030001', 'DISCOS SOLIDOS DE 480 GB', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(190, 1, 1, 1, '403030002', 'MEMORIA DE 8GB - DDR4', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(191, 1, 1, 1, '403030003', 'MEMORIA DE 4GB - DDR3', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(192, 1, 1, 1, '403030004', 'MEMORIA DE 8GB - DDR4 2666 HZ', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(193, 1, 1, 1, '403030005', 'MEMORIA DE 8GB - KVR 1333 10600', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(194, 1, 1, 1, '403030006', 'MEMORIA DE 4GB - DDR3  128000 CON DISIPADOR', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(195, 1, 1, 1, '403030007', 'MEMORIA DE 4GB - DDR3  128000 SIN DISIPADOR', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(196, 1, 1, 1, '403030008', 'MEMORIA DE 8GB - DDR3  10600', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(197, 1, 1, 1, '403030009', 'DISCO SOLIDO - SSD 1TB', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(198, 1, 1, 1, '403030010', 'CINTA PARA IMPRESORA', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(199, 1, 1, 1, '403030011', 'ACCES POINT DAP-2682', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(200, 1, 1, 1, '403030012', 'USB - ADECUACION SISTEMA CONTABLE 2023.3', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(201, 1, 1, 1, '403030013', 'MAKPAC 2 BATERIAS 5.0AH 18V LXT', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(202, 1, 1, 1, '403030014', 'MEMORIA DE 16GB', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(203, 1, 1, 1, '403030015', 'MOUSE USB', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(204, 1, 1, 1, '403030016', 'DISCO SOLIDO DE 960 GB', '', 'ECONOMATO', 'ACCESORIOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(205, 1, 2, 1, '402010001', 'TINTA COLOR AMARILLO', '', 'ECONOMATO', 'TINTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(206, 1, 2, 1, '402010002', 'TINTA COLOR MAGENTA', '', 'ECONOMATO', 'TINTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(207, 1, 2, 1, '402010003', 'TINTA COLOR NEGRO', '', 'ECONOMATO', 'TINTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(208, 1, 2, 1, '402010004', 'TINTA COLOR CYAN', '', 'ECONOMATO', 'TINTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(209, 1, 2, 1, '402010005', 'TONER IMPRESORA', '', 'ECONOMATO', 'TINTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(210, 1, 2, 1, '402010006', 'TONER ORIGINAL', '', 'ECONOMATO', 'TINTAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(211, 1, 2, 1, '401010001', 'SELLOS AUTOMATICOS', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(212, 1, 2, 1, '401010002', 'HOJAS BOND A3', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(213, 1, 2, 1, '401010003', 'HOJAS BOND A4', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(214, 1, 2, 1, '401010004', 'PLUMON PARA PIZARRA COLOR AZUL', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(215, 1, 2, 1, '401010005', 'PLUMON PARA PIZARRA COLOR ROJO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(216, 1, 2, 1, '401010006', 'TABLERO DE MADERA', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(217, 1, 2, 1, '401010007', 'TIZA DE COLORES', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(218, 1, 2, 1, '401010008', 'PIONER A4  2 ANILLOS', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(219, 1, 2, 1, '401010009', 'FOLDER D/TAPA A4 C/GUSANO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(220, 1, 2, 1, '401010010', 'LAPICERO AZUL', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(221, 1, 2, 1, '401010011', 'LAPICERO NEGRO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(222, 1, 2, 1, '401010012', 'LAPICERO ROJO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(223, 1, 2, 1, '401010013', 'LAPIZ 2B', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(224, 1, 2, 1, '401010014', 'CORRECTOR', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(225, 1, 2, 1, '401010015', 'CUADERNO D/RING CUADRIC A5', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(226, 1, 2, 1, '401010016', 'NOTAS ADH. 3 X 3 POST IT', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(227, 1, 2, 1, '401010017', 'BANDEJA DE METAL 3 PISOS NEGRO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(228, 1, 2, 1, '401010018', 'PORTALAPICES DE METAL', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(229, 1, 2, 1, '401010019', 'ORGANIZADOR DE ESCRITORIO 4 DIVISIONES', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(230, 1, 2, 1, '401010020', 'GRAPAS 26/6', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(231, 1, 2, 1, '401010021', 'ENGRAPADOR C/SACAGRAPAS', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(232, 1, 2, 1, '401010022', 'PERFORADOR', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(233, 1, 2, 1, '401010023', 'CLIPS COLORES PLASTIFICADO 33MM', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(234, 1, 2, 1, '401010024', 'PORTA PAPEL TRANSP A4', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(235, 1, 2, 1, '401010025', 'ESTUCHE C/6 COLORES RESALTADOR', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(236, 1, 2, 1, '401010026', 'MOTA PARA PIZARRA', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(237, 1, 2, 1, '401010027', 'PIZARRA BLANCA 120CM X 80CM', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(238, 1, 2, 1, '401010028', 'PLUMON PARA PIZARRA X4 UND', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(239, 1, 2, 1, '401010029', 'PLUMON PARA PIZARRA COLOR NEGRO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(240, 1, 2, 1, '401010030', 'MICAS', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(241, 1, 2, 1, '401010031', 'SOBRE MANILA', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(242, 1, 2, 1, '401010032', 'TABLERO PLASTICO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(243, 1, 2, 1, '401010033', 'FOLDER MANILA', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(244, 1, 2, 1, '401010034', 'PLUM?N INDELEBLE JUMBO NEGRO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(245, 1, 2, 1, '401010035', 'PLUM?N INDELEBLE JUMBO AZUL', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(246, 1, 2, 1, '401010036', 'FASTENER METALICO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(247, 1, 2, 1, '401010037', 'CLIPS CROMADOS 33MM', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(248, 1, 2, 1, '401010038', 'RESALTADOR AMARILLO', '', 'ECONOMATO', 'UTILES DE OFICINA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(249, 1, 2, 1, '301010001', 'STICKERS PRODUCTOS QUIMICOS - 10 X 10 CM', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(250, 1, 2, 1, '301010002', 'STICKERS PRODUCTOS QUIMICOS - 20 X 20 CM', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(251, 1, 2, 1, '301010003', 'STICKERS ZONA DE TRABAJO - A4', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(252, 1, 2, 1, '301010004', 'ADHESIVOS DE LOGO ARCE 4 X  5 CM', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(253, 1, 2, 1, '301010005', 'ADHESIVOS DE LOGO ARCE 6 X  5 CM', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(254, 1, 2, 1, '301010006', 'ADHESIVOS DE RESIDUOS CON LOGO ARCE 20 X 30 CM', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(255, 1, 2, 1, '301010007', 'ETIQUETA CANDADO LOGO ARCE', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(256, 1, 2, 1, '301010008', 'BANNER 1.20 X 1.80 CM', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(257, 1, 2, 1, '301010009', 'ADHESIVOS DE LOGO ARCE  40 X 25 CM', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(258, 1, 2, 1, '301010010', 'ADHESIVOS DE RESIDUOS PELIGROSOS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(259, 1, 2, 1, '301010011', 'ADHESIVO DE AGUA POTABLE A4', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(260, 1, 2, 1, '301010012', 'ETIQUETA DE ESCALERAS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(261, 1, 2, 1, '301010013', 'ETIQUETA DE BRIGADISTA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(262, 1, 2, 1, '301010014', 'ADHESIVOS DE LOGO ARCE 50 X 20 CM', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(263, 1, 2, 1, '301010015', 'ADHESIVOS DE LOGO ARCE 13 X 5 CM', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(264, 1, 2, 1, '301010016', 'IMANTADOS 50 X 20 CM CON LOGO ARCE', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(265, 1, 2, 1, '301010017', 'STICKERS 11.5 CM X 8.5 CM LAMINA AUTOADHESIVA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(266, 1, 2, 1, '301010018', 'STICKERS 9 CM X 9 CM LAMINA AUTOADHESIVA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(267, 1, 2, 1, '301010019', 'STICKERS 30 CM X 20.5 CM LAMINA AUTOADHESIVA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(268, 1, 2, 1, '301010020', 'ADHESIVO DE AGUA NO POTABLE A4', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(269, 1, 2, 1, '301010021', 'ADHESIVO DE AGUA NO POTABLE A5', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(270, 1, 2, 1, '301020001', 'FORMATO TARJETA DE SEGURIDAD DEL PERSONAL', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(271, 1, 2, 1, '301020002', 'TARJETA DE LIBERACION DE CIRCUITO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(272, 1, 2, 1, '301020003', 'FORMATO CHECK LIST DE BOTIQUIN', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(273, 1, 2, 1, '301020004', 'FORMATO CHECK LIST DE EXTINTORES', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(274, 1, 2, 1, '301020005', 'FORMATO CHECK LIST DE VEH?CULOS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(275, 1, 2, 1, '301020006', 'FORMATO CHECK LIST KIT ANTIDERRAME', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(276, 1, 2, 1, '301020007', 'FORMATO CAPACITACIONES', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(277, 1, 2, 1, '301020008', 'FORMATO INDUCCION', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(278, 1, 2, 1, '301020009', 'VALE DE DEVOLUCION', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(279, 1, 2, 1, '301020010', 'VALE DE HERRAMIENTAS Y EQUIPOS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(280, 1, 2, 1, '301020011', 'VALE DE SALIDA MATERIALES', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(281, 1, 2, 1, '301020012', 'FORMATO CHECK LIST DE CAMION CON CESTA ELEVADORA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(282, 1, 2, 1, '301020013', 'FORMATO CHECK LIST DIARIO DE EQUIPO DE IZAJE DE CARGAS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(283, 1, 2, 1, '301020014', 'FORMATO CHECK LIST ELEMENTOS DE IZAJE DE CARGA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(284, 1, 2, 1, '301020015', 'FORMATO PERMISO PARA TRABAJOS DE IZAJE', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(285, 1, 2, 1, '301020016', 'FORMATO CHARLA PRE OPERACIONAL', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1);
INSERT INTO `producto` (`id_producto`, `id_producto_tipo`, `id_material_tipo`, `id_unidad_medida`, `cod_material`, `nom_producto`, `nser_producto`, `mod_producto`, `mar_producto`, `det_producto`, `hom_producto`, `fuc_producto`, `fpc_producto`, `dcal_producto`, `fuo_producto`, `fpo_producto`, `dope_producto`, `est_producto`) VALUES
(286, 1, 2, 1, '301020017', 'FORMATO PERMISO DE INGRESO A ESPACIO CONFINADO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(287, 1, 2, 1, '301020018', 'VERIFICACION DE EQUIPOS DE PROTECCION PARA TRABAJOS EN ALTURA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(288, 1, 2, 1, '301020019', 'FORMATO CHECK LIST DE MINICARGADOR', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(289, 1, 2, 1, '301020020', 'FORMATO CHECK LIST PULLING DE CADENA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(290, 1, 2, 1, '301020021', 'FORMATO INSPECCION DE WINCHE Y FRENO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(291, 1, 2, 1, '301020022', 'FORMATO CHECKLIST RETROEXCAVADORA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(292, 1, 2, 1, '301020023', 'FORMATO CHECK LIST DE RODILLO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(293, 1, 2, 1, '301020024', 'FORMATO CHECK LIST COMPRESORA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(294, 1, 2, 1, '301020025', 'FORMATO INSPECCI?N DE HERRAMIENTAS MANUALES OC', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(295, 1, 2, 1, '301020026', 'FORMATO INSPECCI?N DE HERRAMIENTAS DE PODER', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(296, 1, 2, 1, '301020027', 'FORMATO INSPECCION DE EXTINTORES Y SE?ALES DE EMERGENCIA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(297, 1, 2, 1, '301020028', 'FORMATO CHECK LIST DE CORTADORA DE PAVIMENTO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(298, 1, 2, 1, '301020029', 'FORMATO CHECK LIST EQUIPO DE OXICORTE', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(299, 1, 2, 1, '301020030', 'FORMATO CHECK LIST DE MAQUINA DE SOLDAR AL ARCO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(300, 1, 2, 1, '301020031', 'FORMATO LISTA DE VERIFICACI?N DE ANDAMIOS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(301, 1, 2, 1, '301020032', 'FORMATO PERMISO DE TRABAJO EN CALIENTE', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(302, 1, 2, 1, '301020033', 'FORMATO PERMISO PARA EXCAVACION', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(303, 1, 2, 1, '301020034', 'FORMATO PERMISO PARA INGRESO A EXCAVACIONES CON PROFUNDIDAD 1.5METROS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(304, 1, 2, 1, '301020035', 'FORMATO PERMISO IZAJE DE CARGA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(305, 1, 2, 1, '301020036', 'FORMATO CHECK LIST APISONADOR TIPO CANGURO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(306, 1, 2, 1, '301020037', 'FORMATO PERMISO DE TRABAJOS EN ALTURA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(307, 1, 2, 1, '301020038', 'FORMATO PERMISO DE TRABAJO CRITICO PROXIMIDAD', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(308, 1, 2, 1, '301020039', 'FORMATO INSPECCION DE EQUIPO E INSTALACION CRITICA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(309, 1, 2, 1, '301020040', 'FORMATO LISTA DE VERIFICACION PARA ESCALERAS DE FV', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(310, 1, 2, 1, '301020041', 'FORMATO REGISTRO DE MONITOREO DE INSTALACIONES SUBTERRANEAS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(311, 1, 2, 1, '301020042', 'FORMATO INSPECCION LAVAOJOS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(312, 1, 2, 1, '301020043', 'FORMATO CHECK LIST DE PRENSA HIDRAULICA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(313, 1, 2, 1, '301020044', 'FORMATO CHECK LIST VIBRADOR DE CONCRETO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(314, 1, 2, 1, '301020045', 'FORMATO CHECK LIST SOLDURA EXOTERMICA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(315, 1, 2, 1, '301020046', 'FORMATO CHECK LIST TROMPO MEZCLADOR DE CONCRETO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(316, 1, 2, 1, '301020047', 'FORMATO CHECK LIST DE PLATAFORMA ELEVADORA MOVIL DE PERSONA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(317, 1, 2, 1, '301020048', 'FORMATO CHECK LIST DEL SOPLETE PORTATIL A GAS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(318, 1, 2, 1, '301020049', 'FORMATO INSPECCION DE HERRAMIENTAS DE MANIOBRA Y TENDIDO DE LINEA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(319, 1, 2, 1, '301020050', 'FORMATO CHECK LIST DE HERRAMIENTAS MANUALES OOEE', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(320, 1, 2, 1, '301020051', 'FORMATO REPORTE DE RESIDUOS GENERADOS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(321, 1, 2, 1, '301020052', 'FORMATO INSPECCION DE ALZA BOBINAS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(322, 1, 2, 1, '301020053', 'FORMATO VERIFICACION PREVIA DE TRABAJOS EN ALTURA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(323, 1, 2, 1, '301020054', 'FORMATO INSPECCION DE PROTECCION', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(324, 1, 2, 1, '301020055', 'FORMATO CHECK LIST EXCAVADORA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(325, 1, 2, 1, '301020056', 'FORMATO CHECK LIST DE RODILLO VIBRATORIO AUTOPROPULSADO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(326, 1, 2, 1, '301020057', 'FORMATO CHECK LIST COMPACTADOR TIPO PLANCHA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(327, 1, 2, 1, '301020058', 'FORMATO USO SEGURO DE LA MAQUINA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(328, 1, 2, 1, '301020059', 'FORMATO SE?ALIZACI?N DE ZONA DE TRABAJO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(329, 1, 2, 1, '301020060', 'FORMATO INSTRUCCION PREVIA EN CAMPO IPC GRUPAL', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(330, 1, 2, 1, '301020061', 'FORMATO CHECK LIST DE DETECTOR DE FLUJO DE TENSI?N AMPROBE AT-3500', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(331, 1, 2, 1, '301020062', 'FORMATO CHECK LIST DE DETECTOR DE FLUJO DE TENSI?N VLOC3-PRO', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(332, 1, 2, 1, '301020063', 'FORMATO CONTROL DE ALCOTEST', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(333, 1, 2, 1, '301020064', 'FORMATO INSPECCION PREVIA DE SET', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(334, 1, 2, 1, '301020065', 'FORMATO INSPECCION PREVIA RED SUBTERRANEA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(335, 1, 2, 1, '301020066', 'FORMATO INSPECCION PREVIA RED AEREA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(336, 1, 2, 1, '301020067', 'FORMATO INSPECCI?N PREVIA EN CAMPO OBRA CIVIL', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(337, 1, 2, 1, '301020068', 'FORMATO CHECK LIST DE TABLEROS ELECTRICOS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(338, 1, 2, 1, '301020069', 'FORMATO CONTROL DE CONSUMO DE AGUA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(339, 1, 2, 1, '301020070', 'FORMATO INSPECCION DE EPP', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(340, 1, 2, 1, '301020071', 'VALE SALIDA MATERILAES-OBRAS', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(341, 1, 2, 1, '301020072', 'REGISTRO DE ENTREGA DE EPP O EMERGENCIA', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(342, 1, 2, 1, '301030001', 'PLACAS DE ALUMINIO DE 0.5x40x20mm', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(343, 1, 2, 1, '301030002', 'PLACAS DE ALUMINIO DE 0.5x80x40mm', '', 'IMPRESIONES Y GRABADOS', 'IMPRESIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(344, 1, 2, 1, '608010001', 'VENTANA 5.600 x 1.710', '', 'INSUMOS Y MATERIALES', 'ALUMINIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(345, 1, 2, 1, '608010002', 'VENTANA 2.785 x 1.705', '', 'INSUMOS Y MATERIALES', 'ALUMINIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(346, 1, 2, 1, '608010003', 'VENTANA 2.735 x 1.695', '', 'INSUMOS Y MATERIALES', 'ALUMINIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(347, 1, 2, 1, '608010004', 'VENTANA 5.610 x 1.710', '', 'INSUMOS Y MATERIALES', 'ALUMINIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(348, 1, 2, 1, '608010005', 'VENTANA 5.605 x 1.710', '', 'INSUMOS Y MATERIALES', 'ALUMINIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(349, 1, 2, 1, '608010006', 'VENTANA 2.715 x 1.705', '', 'INSUMOS Y MATERIALES', 'ALUMINIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(350, 1, 2, 1, '605010001', 'ASERRIN', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(351, 1, 2, 1, '605010002', 'BASTIDORES DE 2 X 3 X 3.0 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(352, 1, 2, 1, '605010003', 'PANEL FENOLICO 18MM', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(353, 1, 2, 1, '605010004', 'BASTIDORES DE 2 X 2 X 2.5 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(354, 1, 2, 1, '605010005', 'BASTIDORES DE 3 X 2 X 3.0 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(355, 1, 2, 1, '605010006', 'PLANCHA OSB DE 12MM', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(356, 1, 2, 1, '605010007', 'SOLERA DE 3 X 2 X 8\"', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(357, 1, 2, 1, '605010008', 'TRIPLAY DE 18MM', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(358, 1, 2, 1, '605010009', 'TRIPLAY FENOLICO DOBLE FILM DE 18MM', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(359, 1, 2, 1, '605010010', 'BASTIDORES DE 3 X 3 X 3.0 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(360, 1, 2, 1, '605010011', 'BASTIDORES DE 4 X 3 X 5.0 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(361, 1, 2, 1, '605010012', 'SOLERA DE 3 X 3 X 8\"', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(362, 1, 2, 1, '605010013', 'BASTIDORES DE 3  X 4 X 3 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(363, 1, 2, 1, '605010014', 'TABLONES DE MADERA DE 1 1/2\"X12\"X3 MT', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(364, 1, 2, 1, '605010015', 'BASTIDORES DE 2 X 3 X 2.5 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(365, 1, 2, 1, '605010016', 'PLANCHA OSB DE 11MM', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(366, 1, 2, 1, '605010017', 'PALO EUCALIPTO 3X3', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(367, 1, 2, 1, '605010018', 'BASTIDORES DE 2 X 2 X 3 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(368, 1, 2, 1, '605010019', 'BASTIDORES DE 2 X 2 X 2.4 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(369, 1, 2, 1, '605010020', 'BASTIDORES DE 3 X 4 X 2.5 MTS', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(370, 1, 2, 1, '605010021', 'SOLERA DE 2 X 3 X 8\"', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(371, 1, 2, 1, '605010022', 'SOLERA DE 2 X 3 X 10\"', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(372, 1, 2, 1, '605010023', 'TRIPLAY FENOLICO  DE 14MM', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(373, 1, 2, 1, '605010024', 'SOLERA DE 2 X 2 X 8\"', '', 'INSUMOS Y MATERIALES', 'MADERA', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(374, 1, 2, 1, '602010001', 'SECCIONADOR DE PUESTA TIERRA-60KV(CUCHILLA)', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(375, 1, 1, 1, '602010002', 'ESTABILIZADOR SOLIDO TRIFASICO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(376, 1, 2, 1, '602010003', 'LUMINARIA P/PASTORAL 150W', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(377, 1, 2, 1, '602010004', 'LUMINARIA PLAFON LED CIRCULAR  15W-240VAC EMP/ADOS', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(378, 1, 2, 1, '602010005', 'PILOTO AMARILLO LED 110/130VDC 22MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(379, 1, 2, 1, '602010006', 'PILOTO ROJO LED 110/130VDC 22MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(380, 1, 2, 1, '602010007', 'PILOTO VERDE LED 110/130VDC 22MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(381, 1, 2, 1, '602010008', 'REFLECTOR 150W', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(382, 1, 2, 1, '602010009', 'TABLERO ADOSABLE 300 X 250 X 140MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(383, 1, 2, 1, '602010010', 'CONTACTO AUXILIAR', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(384, 1, 2, 1, '602010011', 'INTERRUPTOR TERMOMAGNETICO DE 1X 6A DC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(385, 1, 2, 1, '602010012', 'INTERRUPTOR TERMOMAGNETICO DE 2X 16A DC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(386, 1, 2, 1, '602010013', 'INTERRUPTOR TERMOMAGNETICO DE 2X 1A DC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(387, 1, 2, 1, '602010014', 'INTERRUPTOR TERMOMAGNETICO DE 2X 2A DC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(388, 1, 2, 1, '602010015', 'INTERRUPTOR TERMOMAGNETICO DE 2X 6A DC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(389, 1, 2, 1, '602010016', 'INTERRUPTOR TERMOMAGNETICO DE 3X 16A IC60', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(390, 1, 2, 1, '602010017', 'CEMENTO CONDUCTIVO DE 25KG', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(391, 1, 2, 1, '602010018', 'VARILLA DE COBRE 3/4 X2.40', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(392, 1, 2, 1, '602010019', 'ARRANCADOR - S-10 P/FLUORESCENTE 4-65W', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(393, 1, 2, 1, '602010020', 'EQUIPOS LED HERMETICO P/FLUORESCENTE', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(394, 1, 2, 1, '602010021', 'TRANSFORMADOR DISTRIBUCI?N TRIF?SICO BAJA TENSION ACEITE ELEVADOR 220/600VCA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(395, 1, 2, 1, '602010022', 'TRANSFORMADOR DISTRIBUCI?N TRIF?SICO SECO  REDUCTOR 600/220VCA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(396, 1, 2, 1, '602010023', 'CONTACTO AUXILIAR - LATERAL 1NA+1NC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(397, 1, 2, 1, '602010024', 'CONTACTO AUXILIAR LATERAL S200', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(398, 1, 2, 1, '602010025', 'CONTACTOR 3P, 52A, 100-250VAC/DC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(399, 1, 2, 1, '602010026', 'INTERRUPTOR DE FUERZA REG. 112 - 160A, 3P, 25KA/230VAC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(400, 1, 2, 1, '602010027', 'INTERRUPTOR RIEL 2P, 2A, 20KA/230VAC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(401, 1, 2, 1, '602010028', 'INTERRUPTOR RIEL 3P, 2A, 20KA/230VAC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(402, 1, 2, 1, '602010029', 'INTERRUPTOR SIMPLE 10 A  DOMINO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(403, 1, 2, 1, '602010030', 'TABLERO ADOSABLE 60 X 60 X 22', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(404, 1, 2, 1, '602010031', 'TERMINACION TERMOCONTRAIBLE CLASE 36kV P/CABLE 3-1x500mm2 USO EXTERIOR', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(405, 1, 2, 1, '602010032', 'TERMINAL TERMOCONTRAIBLE CLASE 36kV P/CABLE 3 - 1x500mm2 USO INTERIOR', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(406, 1, 2, 1, '602010033', 'TERMINAL PORTAFUSIBLE PARA CABLE 500mm2 OJAL 17mm', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(407, 1, 2, 1, '602010034', 'TERMINAL PORTAFUSIBLE PARA CABLE 500mm2 OJAL 17mm', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(408, 1, 2, 1, '602010035', 'CINTA TERMOCONTRAIBLE C/ADHESIVO 5-15 KV 50 MM X 7.5 M', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(409, 1, 2, 1, '602010036', 'CAJA DE DISTRIBUCION', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(410, 1, 2, 1, '602010037', 'REFLECTOR 500W LED', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(411, 1, 2, 1, '602010038', 'INTERRUPTOR TERMOMAGNETICO DE 3X32 AMP', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(412, 1, 2, 1, '602010039', 'CONTACTOR AUXILIAR IOF', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(413, 1, 2, 1, '602010040', 'TABLERO ELECTRICO  MONOFASICO 220V', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(414, 1, 2, 1, '602010041', 'INTERRUPTOR RIEL 2P, 10A, 20KA/230VAC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(415, 1, 2, 1, '602010042', 'MANGA TERMOCONTRAIBLE PARA PLETINA DE 100 X 60MM2', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(416, 1, 2, 1, '602010043', 'INTERRUPTOR TERMOMAGNETICO 2 X 32 A', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(417, 1, 2, 1, '602010044', 'INTERRUPTOR SIMPLE', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(418, 1, 2, 1, '602010045', 'REFLECTOR LED 200 W', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(419, 1, 2, 1, '602010046', 'INTERRUPTOR DOBLE', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(420, 1, 2, 1, '602010047', 'LUMINARIA HERMETICA LED', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(421, 1, 2, 1, '602010048', 'INTERRUPTOR DIFERENCIAL 2P 25A 30mA AC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(422, 1, 2, 1, '602010049', 'INTERRUPTOR GENERAL 3X50A TIPO RIEL DIN', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(423, 1, 2, 1, '602010050', 'INTERRUPTOR RIEL DIN 2P 16A, 10kA/220VAC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(424, 1, 2, 1, '602010051', 'INTERRUPTOR RIEL DIN 2P 20A, 10kA/220VAC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(425, 1, 2, 1, '602010052', 'TABLERO EMPOTRADO TRIFASICO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(426, 1, 2, 1, '602010053', 'TABLERO EMPOTRADO 600X450X150 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(427, 1, 2, 1, '602010054', 'INTERRUPTOR TERMOMAGNETICO 3 X 63 AMP', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(428, 1, 2, 1, '602010055', 'CONTROLADOR DE TEMPERATURA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(429, 1, 2, 1, '602010056', 'INTERRUPTOR RIEL 2P, 6A, 20KA/230VAC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(430, 1, 2, 1, '602010057', 'CINTA TERMOCONTRAIBLE C/ADHESIVO 20kV', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(431, 1, 2, 1, '602010058', 'INTERRUPTOR RIEL 2P 6A 10KA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(432, 1, 2, 1, '602010059', 'TABLERO ADOSABLE 500 X 500 X 370MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(433, 1, 2, 1, '602010060', 'CONTACTO AUXILIAR NA NC P. S200 10A', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(434, 1, 2, 1, '602010061', 'RELE INTERF CAPS 8PINES ENCHUF 2 CONT CONMUT', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(435, 1, 2, 1, '602010062', 'BASE LOGICA PARA RELE CAPS 8PINES', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(436, 1, 2, 1, '602010063', 'PILOTO LUMINOSO ROJO CON LED DE 24 VDC DE 22MM DE IP65', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(437, 1, 2, 1, '602010064', 'PULSADOR COMPLETO RASANTE ROJO DE 22 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(438, 1, 2, 1, '602010065', 'PULSADOR COMPLETO RASANTE VERDE DE 22 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(439, 1, 2, 1, '602010066', 'PULSADOR COMPLETO RASANTE NEGRO DE 22 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(440, 1, 2, 1, '602010067', 'MARCADOR DE BORNES DE 1-10', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(441, 1, 2, 1, '602010068', 'MARCADOR DE BORNES DE 11-20', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(442, 1, 2, 1, '602010069', 'MARCADOR DE BORNES DE 21-30', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(443, 1, 2, 1, '602010070', 'MARCADOR DE BORNES DE 31-40', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(444, 1, 2, 1, '602010071', 'MARCADOR DE BORNES DE 41-50', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(445, 1, 2, 1, '602010072', 'BORNES SECCIONABLES DE PRUEBA PCTK6', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(446, 1, 2, 1, '602010073', 'TAPA FINAL D-PCTK6', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(447, 1, 2, 1, '602010074', 'CANALETA RANURADA 40x60', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(448, 1, 2, 1, '602010075', 'TERMINAL TUBULAR ROJO DOBLE PARA 1.5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(449, 1, 2, 1, '602010076', 'BORNE DE PASO DC6', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(450, 1, 2, 1, '602010077', 'SW PARA MEDICION DE TP?s Y TC?s PARA TRAX', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(451, 1, 2, 1, '602010078', 'SW PARA MEDICION EN SUB ESTACIONES PARA TRAX', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(452, 1, 2, 1, '602010079', 'SET CABLES PARA PRUEBA DE TIEMPOS EN TRAX', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(453, 1, 2, 1, '602010080', 'BLOQUEO PARA INTERRUPTOR MINIATURA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(454, 1, 2, 1, '602010081', 'CANDADO DE NYLON 1\"', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(455, 1, 2, 1, '602010082', 'CANDADO DE NYLON 3\"', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(456, 1, 2, 1, '602010083', 'INTERRUPTOR TERMICO 2 X 25A', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(457, 1, 2, 1, '602010084', 'TABLERO ELECTRICO PARA BOMBA DE 1HP', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(458, 1, 2, 1, '602010085', 'INTERRUPTOR RIEL 2P,6A,25KA/230VAC-S202M-C6', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(459, 1, 2, 1, '602010086', 'BLOQUEO PARA INTERRUPTORES TERMOMAGNETICO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(460, 1, 2, 1, '602010087', 'FOTOCELDA CON BASE PARA ALUMBRADO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(461, 1, 2, 1, '602010089', 'CONTACTOR TESYS 40A', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(462, 1, 2, 1, '602020001', 'FOCO LED 8-10W-220VAC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(463, 1, 2, 1, '602020002', 'FOCO LED E27 - 120 VDC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(464, 1, 2, 1, '602020003', 'LAMPARA ARMARIO LED PEQUE?A UNIT 220VAC 20W', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(465, 1, 2, 1, '602020004', 'LAMPARA TUBULAR 220VAC 15W E14', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(466, 1, 2, 1, '602020005', 'CABLE N?12 THW NEGRO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(467, 1, 2, 1, '602020006', 'BASE PARA RELE TIPO FN-DE IP10', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(468, 1, 2, 1, '602020007', 'BORNERA SAK 10 TIPO G', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(469, 1, 2, 1, '602020008', 'BORNERA SAK 4 TIPO G', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(470, 1, 2, 1, '602020009', 'BORNERA SAKA 10 TIPO G', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(471, 1, 2, 1, '602020010', 'CABLE N?14 THW NEGRO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(472, 1, 2, 1, '602020011', 'CABLE N?16 GPT NEGRO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(473, 1, 2, 1, '602020012', 'MARCADOR DE BORNES DE 1-100', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(474, 1, 2, 1, '602020013', 'RELE INSTANTANEO RF-4, 125 VDC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(475, 1, 2, 1, '602020014', 'TERMINAL TUBULAR P/ 1.5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(476, 1, 2, 1, '602020015', 'TERMINAL TUBULAR P/ 2.5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(477, 1, 2, 1, '602020016', 'TERMINAL TUBULAR P/ 4.0 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(478, 1, 2, 1, '602020017', 'TERMINAL U?A P/ 2.5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(479, 1, 2, 1, '602020018', 'CABLE 1X50 MM2 ALUMINIO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(480, 1, 2, 1, '602020019', 'CONDUCTOR DESNUDO DE 35MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(481, 1, 2, 1, '602020020', 'CAJA DE PASE 15X15 FG', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(482, 1, 2, 1, '602020021', 'CINTA AISLANTE 165 COLOR NEGRO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(483, 1, 2, 1, '602020022', 'TERMINAL OJAL DE 10MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(484, 1, 2, 1, '602020023', 'TERMINAL OJAL DE 50 MM2 - 10 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(485, 1, 2, 1, '602020024', 'CABLE VULCANIZADO 3X12 MM2', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(486, 1, 2, 1, '602020025', 'FLUORESCENTE TUBULAR -  LED T-8 18W', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(487, 1, 2, 1, '602020026', 'FOCO LED 20W - 220VAC', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(488, 1, 2, 1, '602020027', 'LAMPARA ARMARIO LED PEQUE?A UNIT 220VAC 8W A 10W', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(489, 1, 2, 1, '602020028', 'LAMPARA TUBULAR 125VAC 15W E14', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(490, 1, 2, 1, '602020029', 'ADAPTADOR DE MENEKE', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(491, 1, 2, 1, '602020030', 'CINTA AISLANTE 165 COLOR AZUL', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(492, 1, 2, 1, '602020031', 'CINTA AISLANTE 165 COLOR BLANCO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(493, 1, 2, 1, '602020032', 'CINTA AISLANTE 165 COLOR ROJO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(494, 1, 2, 1, '602020033', 'CINTA AISLANTE 165 COLOR VERDE', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(495, 1, 2, 1, '602020034', 'CINTA AISLANTE 165, COLOR AMARILLO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(496, 1, 2, 1, '602020035', 'CABLE NH90 35MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(497, 1, 2, 1, '602020036', 'TERMINAL HEMBRA AZUL', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(498, 1, 2, 1, '602020037', 'TERMINAL OJAL', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(499, 1, 2, 1, '602020038', 'TERMINAL OJAL DE 35 MM2', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(500, 1, 2, 1, '602020039', 'CABLE 1X35 MM2 ALUMINIO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(501, 1, 2, 1, '602020040', 'CABLE 1X50 MM2 ALUMINIO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(502, 1, 2, 1, '602020041', 'CABLE 3X70MM2 ALUMINIO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(503, 1, 2, 1, '602020042', 'CONECTOR BIMETALICO 70MM2', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(504, 1, 2, 1, '602020043', 'CONECTOR HERMETICO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(505, 1, 2, 1, '602020044', 'TUBO CONDUIT 2\"', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(506, 1, 2, 1, '602020045', 'CABLE DE ALUMINIO AISALADO DESNUDO 35MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(507, 1, 2, 1, '602020046', 'CINTA VULCANIZANTE COLOR NEGRO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(508, 1, 2, 1, '602020047', 'CONECTORES PARA CONDUCTOR Y VARILLA 3/4', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(509, 1, 2, 1, '602020048', 'TERMINAL OJAL DE 70 MM2 - 10 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(510, 1, 2, 1, '602020049', 'TERMINAL OJAL DE 70 MM2', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(511, 1, 2, 1, '602020050', 'CABLE N?16 THW NEGRO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(512, 1, 2, 1, '602020051', 'EXTENSIONES CON MENEQUES CABLE VULCANIZADO #12 X 3', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(513, 1, 2, 1, '602020052', 'TOMA AEREO  IP67 16A 2P + T 250V', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(514, 1, 2, 1, '602020053', 'ENCHUFE 16A 3P + T250V', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(515, 1, 2, 1, '602020054', 'TOMA AEREO 16A 23P + T250V', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(516, 1, 2, 1, '602020055', 'ENCHUFE 32A 2P+T 250V', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(517, 1, 2, 1, '602020056', 'ENCHUFE 16A 2P + T250V', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(518, 1, 2, 1, '602020057', 'CAJA OCTAGONAL', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(519, 1, 2, 1, '602020058', 'CAJAS MODULARES', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(520, 1, 2, 1, '602020059', 'FOCOS LED DE 16W', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(521, 1, 2, 1, '602020060', 'SOQUET', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(522, 1, 2, 1, '602020061', 'TAPACIEGA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(523, 1, 2, 1, '602020062', 'TOMACORRIENTE SIMPLE CON LINEA TIERRA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(524, 1, 2, 1, '602020063', 'TOMA AEREO 16A 2P+T 250V', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(525, 1, 2, 1, '602020064', 'TOMA INDUSTRIAL TP PULPO 16 AMP. 2P+T', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(526, 1, 2, 1, '602020065', 'CAJA DE PASE 10 X 10', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(527, 1, 2, 1, '602020066', 'CAJA RECTANGULAR', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(528, 1, 2, 1, '602020067', 'MENEKE MACHO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(529, 1, 2, 1, '602020068', 'CABLE NH90 2.5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(530, 1, 2, 1, '602020069', 'CABLE NH90 4  MM  NEGRO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(531, 1, 2, 1, '602020070', 'CABLE NH90 4 MM AMARILLO/VERDE', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(532, 1, 2, 1, '602020071', 'CABLE NH90 4 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(533, 1, 2, 1, '602020072', 'CABLE NH90 2.5 MM  BLANCO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(534, 1, 2, 1, '602020073', 'TOMACORRIENTE DOBLE UNIVERSAL', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(535, 1, 2, 1, '602020074', 'MENEKE HEMBRA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(536, 1, 2, 1, '602020075', 'MENEKE PULPO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(537, 1, 2, 1, '602020076', 'BANDEJA ESCALERA 300 X100 X 2400 MM CON TAPA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(538, 1, 2, 1, '602020077', 'BANDEJA ESCALERA 300 X 100 X 2400 MM SIN TAPA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(539, 1, 2, 1, '602020078', 'CURVA HORIZONTAL 90? 300 X 300 X 100 MM R', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(540, 1, 2, 1, '602020079', 'CABLE PUESTA TIERRA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(541, 1, 2, 1, '602020080', 'CABLE VULCANIZADO 3 X 14 MM2', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(542, 1, 2, 1, '602020081', 'CABLE DE CONTROL ENUMERADO CCT-B', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(543, 1, 2, 1, '602020082', 'CABLE TRIFASICO 3X6MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(544, 1, 2, 1, '602020083', 'ALICATE AISLADO T- PUNTA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(545, 1, 2, 1, '602020084', 'TERMINAL TUBULAR ROJO DE 1.5MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(546, 1, 2, 1, '602020085', 'TERMINAL TUBULAR AZUL DE 2.5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(547, 1, 2, 1, '602020086', 'TERMINAL U?A ROJO 1.5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(548, 1, 2, 1, '602020087', 'TERMINAL U?A AZUL 2,5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(549, 1, 2, 1, '602020088', 'TERMINAL TUBULAR  AZUL DOBLE PARA 2,5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1);
INSERT INTO `producto` (`id_producto`, `id_producto_tipo`, `id_material_tipo`, `id_unidad_medida`, `cod_material`, `nom_producto`, `nser_producto`, `mod_producto`, `mar_producto`, `det_producto`, `hom_producto`, `fuc_producto`, `fpc_producto`, `dcal_producto`, `fuo_producto`, `fpo_producto`, `dope_producto`, `est_producto`) VALUES
(550, 1, 2, 1, '602020089', 'CINTILLOS DE 2.5 MM X 100 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(551, 1, 2, 1, '602020090', 'CINTILLOS DE 3.6 MM X 200 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(552, 1, 2, 1, '602020091', 'CINTILLOS DE 3.6 MM X 300 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(553, 1, 2, 1, '602020092', 'TAPA FINAL D-DC6', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(554, 1, 2, 1, '602020093', 'BORNE DE TIERRA DCE-PE', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(555, 1, 2, 1, '602020094', 'TERMINAL TUBULAR NEGRO DOBLE PARA 1.5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(556, 1, 2, 1, '602020095', 'TERMINAL TUBULAR GRIS DOBLE PARA 2,5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(557, 1, 2, 1, '602020096', 'CONDUCTOR ELECTRICO 35 MM2', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(558, 1, 2, 1, '602020097', 'CABLE N?12 AWG NEGRO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(559, 1, 2, 1, '602020098', 'CABLE N?12 AWG ROJO', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(560, 1, 2, 1, '602020099', 'FOCO LED DE 18W', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(561, 1, 2, 1, '602020100', 'CABLE VULCANIZADO 2X14AWG', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(562, 1, 2, 1, '602030001', 'MORDAZA', '', 'INSUMOS Y MATERIALES', 'MATERIAL ELECTRICO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(563, 1, 2, 1, '601010001', 'BENTONITA DE 30KG', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(564, 1, 2, 1, '601010002', 'ARENA GRUESA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(565, 1, 2, 1, '601010003', 'PIEDRA CHANCADA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(566, 1, 2, 1, '601010004', 'TIERRA DE CHACRA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(567, 1, 2, 1, '601010005', 'ARENA FINA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(568, 1, 2, 1, '601010006', 'AFIRMADO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(569, 1, 2, 1, '601020001', 'ALAMBRE DE PUAS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(570, 1, 2, 1, '601020002', 'MALLA OLIMPICA PLASTIFICADA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(571, 1, 2, 1, '601020003', 'SILICONA TRANSPARENTE', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(572, 1, 2, 1, '601020004', 'ABRAZADERA DE 3/4 CON 1 OREJA SAP', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', 'homologacion_20251014092950_68ee5e5e5706f.pdf', '2025-10-07', '2025-10-31', '', '2025-10-07', '2025-10-31', '', 1),
(573, 1, 2, 1, '601020005', 'PEGAMENTO PARA TUBO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(574, 1, 2, 1, '601020006', 'PLASTICO AZUL', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(575, 1, 2, 1, '601020007', 'TARUGO VERDE', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(576, 1, 2, 1, '601020008', 'ALAMBRE GALVANIZADA 16', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(577, 1, 2, 1, '601020009', 'CONCRELISTO 175 TIPO I', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(578, 1, 2, 1, '601020010', 'CONCRELISTO 210 TIPO I', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(579, 1, 2, 1, '601020011', 'CEMENTO NACIONAL TP HS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(580, 1, 2, 1, '601020012', 'LADRILLOS KING KONG 18 H', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(581, 1, 2, 1, '601020013', 'TECNOPOR DE 1\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(582, 1, 2, 1, '601020014', 'SEPARADOR DE CONCRETO PREMIUM 2,5CM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(583, 1, 2, 1, '601020015', 'SEPARADOR DE CONCRETO PREMIUM 3,0 CM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(584, 1, 2, 1, '601020016', 'VARILLA DE F? CORRUGADO 1/2\" - AA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(585, 1, 2, 1, '601020017', 'VARILLA DE F? CORRUGADO 1/4\" - AA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(586, 1, 2, 1, '601020018', 'VARILLA DE F? CORRUGADO 3/8\" - AA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(587, 1, 2, 1, '601020019', 'CEMENTO  X 42.5KG', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(588, 1, 2, 1, '601020020', 'BISAGRA DE 4\" X 2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(589, 1, 2, 1, '601020021', 'BISAGRA DE 3\" X 3\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(590, 1, 2, 1, '601020022', 'ALAMBRE N? 16', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(591, 1, 2, 1, '601020023', 'VARILLA DE F? CORRUGADO 5/8\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(592, 1, 2, 1, '601020024', 'SEPARADOR DE CONCRETO PREMIUM  4,0CM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(593, 1, 2, 1, '601020025', 'SEPARADOR DE CONCRETO PREMIUM  7,5CM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(594, 1, 2, 1, '601020026', 'ESPARRAGO DE 5/8 \" X 1.50M', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(595, 1, 2, 1, '601020027', 'ESPARRAGO DE 5/8\". X 1.00M', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(596, 1, 2, 1, '601020028', 'LADRILLOS TECHO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(597, 1, 2, 1, '601020029', 'PLASTICO AZUL DOBLE CARA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(598, 1, 2, 1, '601020030', 'LADRILLO PASTELERO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(599, 1, 2, 1, '601020031', 'YESO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(600, 1, 2, 1, '601020032', 'ELECTRODO INDURA 6011 1/8\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(601, 1, 2, 1, '601020033', 'ELECTRODO INDURA 7018 1/8\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(602, 1, 2, 1, '601020034', 'SOLDADURA DE PLATA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(603, 1, 2, 1, '601020035', 'BARRA REDONDO LISO 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(604, 1, 2, 1, '601020036', 'TARUGO 5/16 AZUL', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(605, 1, 2, 1, '601020037', 'BARRA REDONDO LISO 1\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(606, 1, 2, 1, '601020038', 'CORDON DE RESPALDO POLYROD-BACKER ROD', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(607, 1, 2, 1, '601020039', 'ALAMBRE N? 08', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(608, 1, 2, 1, '601020040', 'TARRAJEO 40KG', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(609, 1, 2, 1, '601020041', 'MORTERO 40 KG', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(610, 1, 2, 1, '601020042', 'TECNOPOR DE 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(611, 1, 2, 1, '601020043', 'OCRE', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(612, 1, 2, 1, '601020044', 'CINTA TEFLON 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(613, 1, 2, 1, '601020045', 'TECNOPOR DE 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(614, 1, 2, 1, '601020046', 'SEPARADOR DE CONCRETO PREMIUM 7,0 CM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(615, 1, 2, 1, '601020047', 'SEPARADOR DE CONCRETO PREMIUM 2,0 CM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(616, 1, 2, 1, '601020048', 'CONCRELISTO 350 TIPO I', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(617, 1, 2, 1, '601020049', 'SEPARADOR DE CONCRETO 5,0 CM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(618, 1, 2, 1, '601020050', 'LLAVE DE PASO DE BRONCE 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(619, 1, 2, 1, '601020051', 'CODO DE BRONCE 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(620, 1, 2, 1, '601020052', 'CONCRELISTO 280 TIPO I', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(621, 1, 2, 1, '601020053', 'VALVULA CHECK 1\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(622, 1, 2, 1, '601020054', 'TEE GALVANIZADO DE 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(623, 1, 2, 1, '601020055', 'TAPON GALVANIZADO DE 3/4 MACHO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(624, 1, 2, 1, '601020056', 'LLAVE DE PASO 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(625, 1, 2, 1, '601020057', 'ELECTROBOMA SINTRIFUGA DE 1HP', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(626, 1, 2, 1, '601030001', 'CONECTOR PVC DE 3/4', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(627, 1, 2, 1, '601030002', 'CURVA PVC SAP 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(628, 1, 2, 1, '601030003', 'TUBO PVC SAP 3/4', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(629, 1, 2, 1, '601030004', 'CODO PVC 3/4', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(630, 1, 2, 1, '601030005', 'CURVA PVC SAP 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(631, 1, 2, 1, '601030006', 'CURVA PVC SAP 3\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(632, 1, 2, 1, '601030007', 'TUBO PVC SAP 4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(633, 1, 2, 1, '601030008', 'TUBO PVC SAP 3\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(634, 1, 2, 1, '601030009', 'TUBO SEL DE 3/4', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(635, 1, 2, 1, '601030010', 'CURVA PVC SAP 1\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(636, 1, 2, 1, '601030011', 'TUBO PVC SAP 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(637, 1, 2, 1, '601030012', 'TUBO PVC SAP 1\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(638, 1, 2, 1, '601030013', 'TUBO PVC SAP 1/2\" TUBOPLAST', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(639, 1, 2, 1, '601030014', 'CAPUCHON DE PLASTICO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(640, 1, 2, 1, '601030015', 'UNION 4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(641, 1, 2, 1, '601030016', 'SOMBRERO 3/4', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(642, 1, 2, 1, '601030017', 'CAPUCHONES 5/8\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(643, 1, 2, 1, '601030018', 'TUBO PVC 2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(644, 1, 2, 1, '601030019', 'CODO DE 90? PVC  2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(645, 1, 2, 1, '601030020', 'CODO DE 45? PVC 2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(646, 1, 2, 1, '601030021', 'TEE PVC 2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(647, 1, 2, 1, '601030022', 'UNION 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(648, 1, 2, 1, '601030023', 'ADAPTADORES PVC DE 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(649, 1, 2, 1, '601030024', 'NIPLES DE 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(650, 1, 2, 1, '601030025', 'UNI?N DE 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(651, 1, 2, 1, '601030026', 'CODO PVC 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(652, 1, 2, 1, '601030027', 'CODO DE 90? PVC 4\" A 2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(653, 1, 2, 1, '601030028', 'TEE PVC 4\" A 2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(654, 1, 2, 1, '601030029', 'REDUCCION DE 3/4 A 1/2', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(655, 1, 2, 1, '601030030', 'ADAPTADOR PVC DE 1\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(656, 1, 2, 1, '601030031', 'UNION 1\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(657, 1, 2, 1, '601030032', 'UNION DE 3/4\" SIN ROSCA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(658, 1, 2, 1, '601030033', 'REDUCCION DE 1 A 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(659, 1, 2, 1, '601030034', 'ADAPTADOR PVC 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(660, 1, 2, 1, '601030035', 'NIPLES DE 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(661, 1, 2, 1, '601030036', 'UNION DE 1\" SIN ROSCA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(662, 1, 2, 1, '601040001', 'SIKADUR 31', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(663, 1, 2, 1, '601040002', 'SIKADUR 32', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(664, 1, 2, 1, '601040003', 'SIKA IGOLFLEX', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(665, 1, 2, 1, '601040004', 'SIKA SEPAROL', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(666, 1, 2, 1, '601040005', 'SIKA WALL', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(667, 1, 2, 1, '601040006', 'SIKACEM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(668, 1, 2, 1, '601040007', 'SIKA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(669, 1, 2, 1, '601040008', 'SIKAFLEX 11FC GRIS TUBO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(670, 1, 2, 1, '601040009', 'SIKAFLEX 11FC NEGRO TUBO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(671, 1, 2, 1, '601040010', 'SIKA GROUT 110 X 30 KG', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(672, 1, 2, 1, '601040011', 'SIKAFLEX-221', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(673, 1, 2, 1, '601040012', 'SIKA ANTISOL CURADOR DE CONCRETO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(674, 1, 2, 1, '601050001', 'BROCHA  1\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(675, 1, 2, 1, '601050002', 'BROCHA  2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(676, 1, 2, 1, '601050003', 'RODILLO 9\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(677, 1, 2, 1, '601050004', 'RODILLO 9\" TP PELUCHE', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(678, 1, 3, 1, '601050005', 'PALA  ANTICHISPAS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(679, 1, 3, 1, '601050006', 'PICO ANTICHISPAS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(680, 1, 2, 1, '601050007', 'DISCO PARA PULIR 7 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(681, 1, 2, 1, '601050008', 'RODILLO 4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(682, 1, 2, 1, '601050009', 'LIJA DE FIERRO G-80', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(683, 1, 2, 1, '601050010', 'DISCO DE CORTE 7\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(684, 1, 2, 1, '601050011', 'DISCO DE CORTE  9\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(685, 1, 2, 1, '601050012', 'HOJA DE SIERRA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(686, 1, 2, 1, '601050013', 'SOLDADURA PUNTO AZUL', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(687, 1, 2, 1, '601050014', 'BROCHA  4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(688, 1, 2, 1, '601050015', 'BROCHA 3\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(689, 1, 2, 1, '601050016', 'RODILLO 9\" TORO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(690, 1, 2, 1, '601050017', 'COMBA DE 05 LB', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(691, 1, 2, 1, '601050018', 'DISCO - P/ SIERRA CIRCULAR (RADIAL)', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(692, 1, 2, 1, '601050019', 'DISCO DE CORTE 18\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(693, 1, 2, 1, '601050020', 'DISCO DE CORTE TP 41 DE 4 1/2\" METAL', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(694, 1, 2, 1, '601050021', 'DISCO DE CORTE TP 41 DE 4 1/2\"  X 1.2  CORTE FINO METAL', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(695, 1, 2, 1, '601050022', 'DISCO DE CORTE P/MADERA DE 7 1/4\" -  24 DIENTES', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(696, 1, 2, 1, '601050023', 'DISCO DE CORTE P/MADERA DE 7 1/4\" - 40 DIENTES', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(697, 1, 2, 1, '601050024', 'DISCO DE DESBASTE DE 7\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(698, 1, 2, 1, '601050025', 'HOJAS PARA CALADORA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(699, 1, 2, 1, '601050026', 'LIJA PARA MADERA #120', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(700, 1, 2, 1, '601050027', 'CUCHARON DE ALUMINIO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(701, 1, 2, 1, '601050028', 'PICO CON MANGO DE MADERA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(702, 1, 2, 1, '601050029', 'REGLAS DE ALUMINIO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(703, 1, 2, 1, '601050030', 'TORTOLES', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(704, 1, 2, 1, '601050031', 'BADILEJO 9\" CUADRADO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(705, 1, 2, 1, '601050032', 'CARRETILLA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(706, 1, 2, 1, '601050033', 'CINCEL PUNTA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(707, 1, 2, 1, '601050034', 'CINCEL PUNTA PLANA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(708, 1, 2, 1, '601050035', 'COMBA OCTAGONAL 12 LB', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(709, 1, 2, 1, '601050036', 'COMBA OCTOGONAL 8 LB', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(710, 1, 2, 1, '601050037', 'ESCUADRA METAL 8\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(711, 1, 2, 1, '601050038', 'FROTACHO LARGO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(712, 1, 2, 1, '601050039', 'HILO DE NYLON O PESCA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(713, 1, 2, 1, '601050040', 'HILO PABILO #10', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(714, 1, 2, 1, '601050041', 'MARTILLO TIPO U?A', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(715, 1, 2, 1, '601050042', 'NIVEL DE MANO  18\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(716, 1, 2, 1, '601050043', 'NIVEL DE MANO  24\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(717, 1, 2, 1, '601050044', 'PALAS PUNTA REDONDA (LAMPA)', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(718, 1, 2, 1, '601050045', 'TIRALINEA PLASTICO DE 30MTS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(719, 1, 2, 1, '601050046', 'WINCHA DE 5 MT', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(720, 1, 2, 1, '601050047', 'BAUL PORTA HERRAMIENTAS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(721, 1, 2, 1, '601050048', 'CAJA PORTA HERRAMIENTAS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(722, 1, 2, 1, '601050049', 'MALETA PORTA HERRAMIENTAS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(723, 1, 2, 1, '601050050', 'BOLSA PORTA HERRAMIENTA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(724, 1, 2, 1, '601050051', 'SOLDADURA SUPERCITO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(725, 1, 2, 1, '601050052', 'SOLDADURA CELLORCORD', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(726, 1, 3, 1, '601050053', 'TENAZA PROTA ELECTRODO 500A 14233 + TENAZA PARA TIERRA 500A 14235', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(727, 1, 3, 1, '601050054', 'MOLDE DE METAL PARA TESTIGO DE CONCRETO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(728, 1, 3, 1, '601050055', 'CONO DE ABRAMS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(729, 1, 3, 1, '601050056', 'MOCHILA PARA FUMIGAR', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(730, 1, 3, 1, '601050057', 'BROCA TE-YX 1\"-36\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(731, 1, 3, 1, '601050058', 'BROCA TE-YX 26/52 1\"-21\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(732, 1, 3, 1, '601050059', 'CINCEL PUNTERO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(733, 1, 3, 1, '601050060', 'CEPILLO PARA CONCRETO TP RASTRILLO DE 48\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(734, 1, 3, 1, '601050061', 'ARCO DE SIERRA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(735, 1, 3, 1, '601050062', 'BROCA DE 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(736, 1, 3, 1, '601050063', 'BROCA DE 1/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(737, 1, 3, 1, '601050064', 'BROCA DE 3/8', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(738, 1, 3, 1, '601050065', 'BROCA DE 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(739, 1, 3, 1, '601050066', 'DISCO DE CORTE 7 1/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(740, 1, 3, 1, '601050067', 'ESCUADRA METAL', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(741, 1, 3, 1, '601050068', 'FROTACHO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(742, 1, 3, 1, '601050069', 'LAMPA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(743, 1, 2, 1, '601050070', 'LIJA DE FIERRO G-100', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(744, 1, 2, 1, '601050071', 'LIJA DE FIERRO G-120', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(745, 1, 2, 1, '601050072', 'LIJA DE FIERRO G-60', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(746, 1, 2, 1, '601050073', 'MANGOS DE GOMAS PARA CARRETILLA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(747, 1, 2, 1, '601050074', 'PEGAMENTO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(748, 1, 3, 1, '601050075', 'PLOMADA CILINDRICA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(749, 1, 3, 1, '601050076', 'PLOMADA PUNTA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(750, 1, 3, 1, '601050077', 'ESPATULA PLASTICO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(751, 1, 3, 1, '601050078', 'DISCO DE CORTE 14\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(752, 1, 3, 1, '601050079', 'ESCOBILLA DE FE', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(753, 1, 3, 1, '601050080', 'ESCOBILLA DE MADERA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(754, 1, 3, 1, '601050081', 'BROCA 5/8\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(755, 1, 3, 1, '601050082', 'BROCA PALETA 3/4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(756, 1, 2, 1, '601050083', 'BATEA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(757, 1, 2, 1, '601050084', 'LAPIZ CARPINTERO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(758, 1, 2, 1, '601050085', 'DISCO DE DESBASTE DE 4 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(759, 1, 2, 1, '601050086', 'ESPATULA DE 2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(760, 1, 2, 1, '601050087', 'ESPATULA DE 3\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(761, 1, 2, 1, '601050088', 'ESPATULA DE 4\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(762, 1, 2, 1, '601050089', 'EXTENSION P/PINTURA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(763, 1, 2, 1, '601050090', 'MALLA PARA COLAR PINTURA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(764, 1, 2, 1, '601050091', 'TACO LIJADOR DE MADERA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(765, 1, 3, 1, '601050092', 'DISCO DE CORTE 4 1/2\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(766, 1, 3, 1, '601050093', 'LIMA PLANA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(767, 1, 3, 1, '601050094', 'BROCA HSS DE 10MM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(768, 1, 3, 1, '601050095', 'PISTOLA APLICADORA CALAFATERA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(769, 1, 3, 1, '601050096', 'PISTOLA PARA PINTAR BAJA PRESION', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(770, 1, 3, 1, '601050097', 'DESTORNILLADORES AISLADOS 07 PZAS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(771, 1, 3, 1, '601050098', 'ALICATES AISLADOS 03 PZAS', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(772, 1, 3, 1, '601050099', 'CUCHILLA RETRACTIL (CUTER)', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(773, 1, 3, 1, '601050100', 'PRENSA TERMINAL OJAL', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(774, 1, 3, 1, '601050101', 'PRENSA TERMINAL TUBULAR', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(775, 1, 3, 1, '601050102', 'PELACABLE AUTOMATICO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(776, 1, 3, 1, '601050103', 'APLICADOR DE SILICONA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(777, 1, 3, 1, '601050104', 'WINCHA PASACABLE', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(778, 1, 3, 1, '601050105', 'PATA DE CABRA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(779, 1, 3, 1, '601050106', 'LIJA DE AGUA G-80', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(780, 1, 3, 1, '601050107', 'BROCHA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(781, 1, 3, 1, '601050108', 'WINCHA DE 8MT', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(782, 1, 3, 1, '601050109', 'BROCA DE 3/16\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(783, 1, 3, 1, '601050110', 'BROCA DE 5/16\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(784, 1, 3, 1, '601050111', 'BADILEJO 8\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(785, 1, 3, 1, '601050112', 'BARRETA AISLADA 1 X 1.80 MT', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(786, 1, 3, 1, '601050113', 'BANDEJA METALICA 1X1', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(787, 1, 3, 1, '601050114', 'HILO PABILO #20', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(788, 1, 3, 1, '601050115', 'DISCO DE CORTE 7\" X 1.5 MM', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(789, 1, 3, 1, '601050116', 'RODILLO 3\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(790, 1, 3, 1, '601050117', 'BROCHA 7\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(791, 1, 3, 1, '601050118', 'FROTACHO TP.  ESPADA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(792, 1, 3, 1, '601050119', 'CINCEL PLANO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(793, 1, 3, 1, '601050120', 'PLANCHA DE BATIR', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(794, 1, 3, 1, '601050121', 'RODILLO 7\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(795, 1, 3, 1, '601050122', 'PALA DE JARDINERO 6\"', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(796, 1, 3, 1, '601050123', 'DISCO DE CORTE DE PAVIMENTO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(797, 1, 3, 1, '601050124', 'BROCA DE CONCRETO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(798, 1, 3, 1, '601060001', 'MANGUERA VIBRADORA DE CONCRETO', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(799, 1, 3, 1, '601060002', 'BATERIA', '', 'INSUMOS Y MATERIALES', 'MATERIALES DE CONSTRUCCI?N', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(800, 1, 2, 1, '606010001', 'AGUA DE MESA - X 20 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(801, 1, 2, 1, '606010002', 'HOJA REPUESTOS CUTER', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(802, 1, 1, 1, '606010003', 'TACHO PARA DEPOSITO DE AGUA 2500L', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(803, 1, 1, 1, '606010004', 'BALDE X 1 GLN', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(804, 1, 1, 1, '606010005', 'BALDE X 5 GLN', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(805, 1, 1, 1, '606010006', 'PINZA PORTA ELECTRODO DE 500', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(806, 1, 1, 1, '606010007', 'GALONERA X 20 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(807, 1, 1, 1, '606010008', 'MESA DE TRABAJO METALICA 120CM L x 100CM Ax 80CM H', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(808, 1, 1, 1, '606010009', 'TACHO PARA DEPOSITO DE AGUA 1000L', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(809, 1, 2, 1, '606010010', 'PLANCHA DE LAMINA DE CAUCHO NITRILO', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(810, 1, 2, 1, '606010011', 'COSTALES DE POLIETILENO (SACO)', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(811, 1, 3, 1, '606010012', 'PUNTA DE ROTOMARTILLO', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(812, 1, 3, 1, '606010013', 'BANCO 02 PASOS D/PLASTICO', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(813, 1, 3, 1, '606010014', 'LLAVE MIXTA 17', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(814, 1, 3, 1, '606010015', 'LLAVE MIXTA 19', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(815, 1, 2, 1, '606010016', 'AGUA DE MESA - X 20 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(816, 1, 2, 1, '606010017', 'KIT DE ACSESORIO PARA TANQUE DE INODORO', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1);
INSERT INTO `producto` (`id_producto`, `id_producto_tipo`, `id_material_tipo`, `id_unidad_medida`, `cod_material`, `nom_producto`, `nser_producto`, `mod_producto`, `mar_producto`, `det_producto`, `hom_producto`, `fuc_producto`, `fpc_producto`, `dcal_producto`, `fuo_producto`, `fpo_producto`, `dope_producto`, `est_producto`) VALUES
(817, 1, 2, 1, '606010018', 'CA?O PARA LAVAMANO', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(818, 1, 2, 1, '606010019', 'TAPA PARA INODORO', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(819, 1, 2, 1, '606010020', 'BOYA Y VALVULA 1\" TANQUE DE AGUA', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(820, 1, 2, 1, '606010021', 'CONTROL DE NIVEL DE AGUA 15A', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(821, 1, 2, 1, '606010022', 'TUBERIA DE ABASTO', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(822, 1, 1, 1, '606010023', 'MALETA ORGANIZADORA D/HERRAMIENTAS/26 BOLSILLOS', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(823, 1, 1, 1, '606010024', 'MOCHILA TRANSPORTE/EQUIPOS', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(824, 1, 1, 1, '606010025', 'BATERIA RECARGABLE', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(825, 1, 2, 1, '606020001', 'TOALLA MICROFIBRA - 30 X30 CM', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(826, 1, 2, 1, '606020002', 'TOCUYO', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(827, 1, 2, 1, '606020003', 'TRAPO INDUSTRIAL', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(828, 1, 2, 1, '606020004', 'BOLSA PARA BASURA 75 L.', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(829, 1, 2, 1, '606020005', 'TOALLA MICROFIBRA - 40 X70 CM', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(830, 1, 2, 1, '606020006', 'BOLSA NEGRA 50 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(831, 1, 2, 1, '606020007', 'BOLSA ROJA 50 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(832, 1, 2, 1, '606020008', 'PA?OS ABSORBENTES', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(833, 1, 2, 1, '606020009', 'JABON LIQUIDO', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(834, 1, 2, 1, '606020010', 'BOLSA AMARILLO 75 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(835, 1, 2, 1, '606020011', 'BOLSA AZUL 75 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(836, 1, 2, 1, '606020012', 'BOLSA BLANCA 75 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(837, 1, 2, 1, '606020013', 'BOLSA NEGRA 75 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(838, 1, 2, 1, '606020014', 'BOLSA ROJA 75 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(839, 1, 2, 1, '606020015', 'BOLSA VERDE 75 LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(840, 1, 2, 1, '606020016', 'ESCOBILLONES', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(841, 1, 2, 1, '606020017', 'DISPENSADOR PARA JAB?N', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(842, 1, 2, 1, '606020018', 'DISPENSADOR TIPO PERA', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(843, 1, 2, 1, '606020019', 'ESCOBA', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(844, 1, 2, 1, '606020020', 'BOLSA NEGRA DE 70LT', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(845, 1, 2, 1, '606020021', 'RECOGEDOR', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(846, 1, 2, 1, '606020022', 'BOLSA NEGRA 35 CM X 46 CM', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(847, 1, 2, 1, '606020023', 'ESPONJA VERDE', '', 'INSUMOS Y MATERIALES', 'OTROS SUMINISTROS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(848, 1, 2, 1, '604010001', 'PLANCHA ESTRIADA 3/16\"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(849, 1, 2, 1, '604010002', 'PLATINA DE COBRE 10 X 60 X 6 MTS', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(850, 1, 2, 1, '604010003', 'ANGULO 3/16 \" x  1 1/2 \"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(851, 1, 2, 1, '604010004', 'ANGULO 1/4 X 3 X 6 MTS', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(852, 1, 2, 1, '604010005', 'CANAL ESTRUCTURAL \"U\" 3\"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(853, 1, 2, 1, '604010006', 'CANAL ESTRUCTURAL \"U\" 4\"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(854, 1, 2, 1, '604010007', 'CANAL ESTRUCTURAL \"U\" 6\"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(855, 1, 2, 1, '604010008', 'PLANCHA LAC  5/16\" x 4 x 8', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(856, 1, 2, 1, '604010009', 'PLANCHA LAC  5/16\"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(857, 1, 2, 1, '604010010', 'PLATINA DE FE 1/4\" X 1.1/2\"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(858, 1, 2, 1, '604010011', 'PLATINA DE FE 3/16 x  1 1/2\"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(859, 1, 2, 1, '604010012', 'TUBO CUADRADO LAC 3 x 4 x 6.00', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(860, 1, 2, 1, '604010013', 'VIGA H DE 6\" x 20 LBS  X 6.0 MT', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(861, 1, 2, 1, '604010014', 'PLATINA DE BRONCE DE 1 1/2\" X 3/16\" X 1.5 MT', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(862, 1, 2, 1, '604010015', 'ANGULO 3/16 \" x  2\"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(863, 1, 2, 1, '604010016', 'PLANCHA LAC 3/8\" x 4 x 8', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(864, 1, 2, 1, '604010017', 'TUBO REDONDO LAC 2\" x 2.5 x 6.40 MT', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(865, 1, 2, 1, '604010018', 'PLANCHA DE ALUMINIO ESPESOR 2MM', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(866, 1, 2, 1, '604010019', 'PLANCHA 2.3 MM', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(867, 1, 2, 1, '604010020', 'TUBO RECTANGULAR LAC 150 X 100 X 4.5MM X 6MT', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(868, 1, 2, 1, '604010021', 'BARRA REDONDA LISA 5/8\"', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(869, 1, 2, 1, '604010022', 'PLANCHA DE 3/4', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(870, 1, 2, 1, '604010023', 'PLANCHA DE 1/2\" X 38 MM', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(871, 1, 2, 1, '604010024', 'PLATINA DE BRONCE DE 1 1/2\" X 3/16\" X 3.0MT', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(872, 1, 2, 1, '604010025', 'PLANCHA 9.00 X 600MM X 1200MM', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(873, 1, 2, 1, '604010026', 'VIGA H DE 6\" X 9LBS/PIE X 20\'', '', 'INSUMOS Y MATERIALES', 'PERFIL ESTRUCTURAL', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(874, 1, 2, 1, '603010001', 'PERNO DE 3/16X1', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(875, 1, 2, 1, '603010002', 'PERNOS DE 1/4 X1\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(876, 1, 2, 1, '603010003', 'PERNOS DE 3/8 X1.5', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(877, 1, 2, 1, '603010004', 'TORNILLO AUTORROSCANTE 1/4 X 1\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(878, 1, 2, 1, '603010005', 'PERNO AUTOPERFORANTE DE 3/8\" X 1\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(879, 1, 2, 1, '603010006', 'PERNOS DE ANCLAJE', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(880, 1, 2, 1, '603010007', 'PERNO DE EXPANSI?N 1/2\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(881, 1, 2, 1, '603010008', 'PERNO INOX 3/8 X 2\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(882, 1, 2, 1, '603010009', 'ARANDELA PLANA INOX 304 1/2\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(883, 1, 2, 1, '603010010', 'ARANDELA PLANA INOX 304 3/8\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(884, 1, 2, 1, '603010011', 'PERNO HEX INOX 304 1/2\" X 2\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(885, 1, 2, 1, '603010012', 'PERNO HEX INOX 304 3/8\" X 2\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(886, 1, 2, 1, '603010013', 'TUERCA HEXAGONAL UNC INOX 304 1/2\" DF', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(887, 1, 2, 1, '603010014', 'TUERCA HEXAGONAL UNC INOX 304 3/8\" AP', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(888, 1, 2, 1, '603010015', 'TORNILLO DE 6 X38MM CON ARANDELA', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(889, 1, 2, 1, '603010016', 'TORNILLO DE 6 X38MM', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(890, 1, 2, 1, '603010017', 'PERNO HEX INOX 304 1/4\" X 3/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(891, 1, 2, 1, '603010018', 'ARANDELA HEX INOX 304 1/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(892, 1, 2, 1, '603010019', 'TUERCA HEXAGONAL UNC INOX 304 1/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(893, 1, 2, 1, '603010020', 'TORNILLO SHIPBOARD', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(894, 1, 2, 1, '603010021', 'VARILLA ROSCADA 3/4\" x 12\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(895, 1, 2, 1, '603010022', 'TUERCA HEX. O3/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(896, 1, 2, 1, '603010023', 'ARANDELA PLANA O3/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(897, 1, 2, 1, '603010024', 'ARANDELA DE PRESION O3/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(898, 1, 2, 1, '603010025', 'VARILLA DE ANCLAJE HAS-E-55 3/4\"x10\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(899, 1, 2, 1, '603010026', 'VARILLA ROSCADA DE 1/2\" X 300 MM', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(900, 1, 2, 1, '603010027', 'VARILLA ROSCADA M10 X 1MT', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(901, 1, 2, 1, '603010028', 'ARANDELA PRESION GALVANIZADO 1/2', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(902, 1, 2, 1, '603010029', 'ARANDELA PLANA GALVANIZADO 1/2', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(903, 1, 2, 1, '603010030', 'TUERCA HEXAGONAL GALVANIZADO 1/2', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(904, 1, 2, 1, '603010031', 'PERNO HEX GALV 1/2 X 2 1/2', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(905, 1, 2, 1, '603010032', 'PERNO HEX GALV 1/2 X 1 1/2', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(906, 1, 2, 1, '603020001', 'TIRAFON 2\" x 1/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(907, 1, 2, 1, '603020002', 'CLAVO X 2\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(908, 1, 2, 1, '603020003', 'CLAVO X 3\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(909, 1, 2, 1, '603020004', 'CLAVO X 4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(910, 1, 2, 1, '603020005', 'CLAVO X 1\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(911, 1, 2, 1, '603020006', 'TIRAFON 3/16', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(912, 1, 2, 1, '603020007', 'AMPOLLA QUIMICA HVU2 3/4\" x 6 5/8', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(913, 1, 2, 1, '603030001', 'CINTILLO DE AMARRE DE 150', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(914, 1, 2, 1, '603030002', 'CINTILLO DE AMARRE DE 100 x 2.5 MM', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(915, 1, 2, 1, '603030003', 'CINTILLO DE AMARRE DE 150 x 3.6 MM', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(916, 1, 2, 1, '603030004', 'CINTILLO DE AMARRE DE 200 x 3.6 MM', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(917, 1, 2, 1, '603030005', 'CINTILLO DE AMARRE DE 300', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(918, 1, 2, 1, '603030006', 'CINTA MASKING TAPE 3/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(919, 1, 2, 1, '603030007', 'CINTA BANDIT 3/4', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(920, 1, 2, 1, '603030008', 'CINTA EMBALAJE', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(921, 1, 2, 1, '603030009', 'STRECHT FILM DE 20\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(922, 1, 2, 1, '603030010', 'SOGA TRENZADA POLIPROPILENO DE 3/8\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(923, 1, 2, 1, '603030011', 'SOGUILLA', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(924, 1, 2, 1, '603030012', 'CINTA MASKING TAPE 2\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(925, 1, 2, 1, '603030013', 'CANCAMO DE ANCLAJE DE 1 1/2\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(926, 1, 2, 1, '603030014', 'CANCAMO DE ANCLAJE DE 3/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(927, 1, 2, 1, '603030015', 'STRECH FILM 18\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(928, 1, 2, 1, '603030016', 'SOGA DRIZA DE 3/8\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(929, 1, 2, 1, '603030017', 'CINTA MASKING TAPE ROJO', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(930, 1, 2, 1, '603030018', 'CINTA MASKING TAPE VERDE', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(931, 1, 2, 1, '603030019', 'RAC TV 50-60\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(932, 1, 2, 1, '603030020', 'SOGA DRIZA DE 3/4\"', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(933, 1, 2, 1, '603030021', 'CINTILLO DE 1020 X 9MM', '', 'INSUMOS Y MATERIALES', 'SUJECION Y FIJACION', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(934, 1, 2, 1, '607020001', 'CRISTAL TEMPLADO DE 6MM ESPESOR - 380 x 580', '', 'INSUMOS Y MATERIALES', 'VIDRIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(935, 1, 2, 1, '607020002', 'CRISTAL TEMPLADO DE 6MM ESPESOR - 390 x 590', '', 'INSUMOS Y MATERIALES', 'VIDRIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(936, 1, 2, 1, '607020003', 'CRISTAL TEMPLADO DE 6MM ESPESOR - 393 x 585', '', 'INSUMOS Y MATERIALES', 'VIDRIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(937, 1, 2, 1, '607020004', 'CRISTAL TEMPLADO DE 6MM ESPESOR - 523 x 525', '', 'INSUMOS Y MATERIALES', 'VIDRIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(938, 1, 2, 1, '502010001', 'AFLOJA TODO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(939, 1, 2, 1, '502010002', 'GRASA GRAFITADA', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(940, 1, 2, 1, '502010003', 'THORGEL - DOSIS QUIMICA DE 5KG', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(941, 1, 2, 1, '502010004', 'AGUA DESIONIZADA X 20 LT', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(942, 1, 2, 1, '502010005', 'MASILLA METALICA', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(943, 1, 2, 1, '502010006', 'SODA CAUSTICA', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(944, 1, 2, 1, '502010007', 'DESENGRASANTE INDUSTRIAL', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(945, 1, 2, 1, '502010008', 'ACIDO MURIATICO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(946, 1, 2, 1, '502010009', 'ACEITE 3 EN 1', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(947, 1, 2, 1, '502010010', 'Z FLEX POLIURETANO MANGAS 600 ML', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(948, 1, 2, 1, '502010011', 'ANCLAJE CON EPOXI HIT-RE 500 V3', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(949, 1, 2, 1, '502010012', 'GRASA VISTONY', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(950, 1, 2, 1, '502010013', 'ALKAN CU', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(951, 1, 2, 1, '502010014', 'SILICONA SICASIL GRIS', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(952, 1, 2, 1, '502010015', 'FORMADOR DE EMPAQUETADURA 143G', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(953, 1, 2, 1, '502010016', 'MASILLA PLASTICA BONFLEX', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(954, 1, 2, 1, '502010017', 'WASH PRIMER', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(955, 1, 2, 1, '502010018', 'PASTA CONDUCTORA COBREADA LAT X 1 LB.', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(956, 1, 2, 1, '502010019', 'PASTA CONDUCTORA BIMETALICA LAT X 1 LB.', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(957, 1, 2, 1, '502010020', 'OXIGENO INDUSTRIAL', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(958, 1, 2, 1, '502010021', 'ACETILENO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'OTROS QUIMICOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(959, 1, 2, 1, '501010001', 'PINTURA LATEX AZUL', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(960, 1, 2, 1, '501010002', 'PINTURA LATEX GRIS', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(961, 1, 2, 1, '501010003', 'THINER ACRILICO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(962, 1, 2, 1, '501010004', 'SOLVO 50 X 5 GLN', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(963, 1, 2, 1, '501010005', 'ESMALTE EPOXICO GRIS NIEBLA', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(964, 1, 2, 1, '501010006', 'LATEX BLANCO OSTRA', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(965, 1, 2, 1, '501010007', 'PINTURA LATEX BLANCO HUMO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(966, 1, 2, 1, '501010008', 'PINTURA TRAFICO AMARILLO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(967, 1, 2, 1, '501010009', 'PINTURA ALUMINIO SPRAY', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(968, 1, 2, 1, '501010010', 'REMOVEDOR DE OXIDO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(969, 1, 2, 1, '501010011', 'PINTURA MARRON MATE', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(970, 1, 2, 1, '501010012', 'QUITASARRO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(971, 1, 2, 1, '501010013', 'ACONDICIONADOR DE METALES 3.5 LT', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(972, 1, 2, 1, '501010014', 'BASE ZINCROMATO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(973, 1, 2, 1, '501010015', 'PINTURA ESMALTE SUPER GLOSS ROJO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(974, 1, 2, 1, '501010016', 'PINTURA LATEX AZUL ELECTRICO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(975, 1, 2, 1, '501010017', 'CHEMALAC EXTRA + SOLVENTE', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(976, 1, 2, 1, '501010018', 'PINTURA ESMALTE BLANCO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(977, 1, 2, 1, '501010019', 'PINTURA ESMALTE ROJO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(978, 1, 2, 1, '501010020', 'PINTURA ESMALTE NARANJA', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(979, 1, 2, 1, '501010021', 'PINTURA ESMALTE NEGRO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(980, 1, 2, 1, '501010022', 'IMPRIMANTE', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(981, 1, 2, 1, '501010023', 'PINTURA ESMALTE GLOSS BLANCO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(982, 1, 2, 1, '501010024', 'CHEMA TECHO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(983, 1, 2, 1, '501010025', 'PINTURA LATEX BLANCO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(984, 1, 2, 1, '501010026', 'TEMPLE', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(985, 1, 2, 1, '501010027', 'GALVANOX EN FRIO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(986, 1, 2, 1, '501010028', 'PINTURA ESMALTE GLOSS NEGRO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(987, 1, 2, 1, '501010029', 'SELLADOR X 1/4 GLN', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(988, 1, 2, 1, '501010030', 'PINTURA TRAFICO BLANCO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(989, 1, 2, 1, '501010031', 'PINTURA LATEX ARTICO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(990, 1, 2, 1, '501010032', 'PINTURA ESMALTE AZUL', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(991, 1, 2, 1, '501010033', 'PINTURA ESMALTE GRIS', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(992, 1, 2, 1, '501010034', 'SPRAY GRIS NIEBLA', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(993, 1, 2, 1, '501010035', 'PINTURA TRAFICO NARANJA', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(994, 1, 2, 1, '501010036', 'PINTURA TRAFICO NEGRO', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(995, 1, 2, 1, '501010037', 'PINTURA ESMALTE ROJO BERMELLON', '', 'PRODUCTOS QUIMICOS Y DERIVADOS', 'PINTURAS Y SOLVENTES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(996, 1, 2, 1, '201010001', 'CALZADO DIELECTRICO COD-S08-1 T- 35', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(997, 1, 2, 1, '201010002', 'CALZADO DIELECTRICO COD-S08-1 T- 36', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(998, 1, 2, 1, '201010003', 'CALZADO DIELECTRICO COD-S08-1 T- 37', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(999, 1, 2, 1, '201010004', 'CALZADO DIELECTRICO COD-S08-1 T- 38', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1000, 1, 2, 1, '201010005', 'CALZADO DIELECTRICO COD-S08-1 T- 39', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1001, 1, 2, 1, '201010006', 'CALZADO DIELECTRICO COD-S08-1 T- 40', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1002, 1, 2, 1, '201010007', 'CALZADO DIELECTRICO COD-S08-1 T- 41', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1003, 1, 2, 1, '201010008', 'CALZADO DIELECTRICO COD-S08-1 T- 42', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1004, 1, 2, 1, '201010009', 'CALZADO DIELECTRICO COD-S08-1 T- 43', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1005, 1, 2, 1, '201010010', 'CALZADO DIELECTRICO COD-S08-1 T- 44', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1006, 1, 2, 1, '201010011', 'CALZADO DIELECTRICO COD-S08-1 T- 45', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1007, 1, 2, 1, '201010012', 'CALZADO DIELECTRICO SEMPERT 1030 T- 43', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1008, 1, 2, 1, '201010013', 'CALZADO DIELECTRICO SEMPERT 1030 T- 44', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1009, 1, 2, 1, '201010014', 'BOTA DE JEBE T- 40', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1010, 1, 2, 1, '201010015', 'BOTA DE JEBE T- 41', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1011, 1, 2, 1, '201010016', 'BOTA DE JEBE T- 42', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1012, 1, 2, 1, '201010017', 'PROTECTOR METATARSAL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1013, 1, 2, 1, '201010018', 'CUBRE BOTAS DIELECTRICA 20KV', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1014, 1, 2, 1, '201010019', 'BOTA DE JEBE T- 43', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1015, 1, 2, 1, '201010020', 'BOTA DE JEBE T- 39', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1016, 1, 2, 1, '201010021', 'CALZADO DIELECTRICO COD N44-16 T- 38', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1017, 1, 2, 1, '201010022', 'CALZADO DIELECTRICO COD N44-16 T- 39', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1018, 1, 2, 1, '201010023', 'CALZADO DIELECTRICO COD N44-16 T- 40', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1019, 1, 2, 1, '201010024', 'CALZADO DIELECTRICO COD N44-16 T- 41', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1020, 1, 2, 1, '201010025', 'CALZADO DIELECTRICO COD N44-16 T- 42', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1021, 1, 2, 1, '201010026', 'CALZADO DIELECTRICO COD N44-16 T- 43', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1022, 1, 2, 1, '201010027', 'CALZADO DIELECTRICO COD N44-16 T- 44', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1023, 1, 2, 1, '201010028', 'CALZADO DIELECTRICO COD N44-16 T- 45', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1024, 1, 2, 1, '201010029', 'BOTA DE JEBE T- 44', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1025, 1, 2, 1, '201020001', 'BARBIQUEJO IGNIFUGO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1026, 1, 2, 1, '201020002', 'CAMISA IGNIFUGA 30 CAL AZUL MARINO T-L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1027, 1, 2, 1, '201020003', 'CUBRE NUCA 27.7 CAL COLOR GRIS', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1028, 1, 2, 1, '201020004', 'PANTALON IGNIFUGA 30 CAL AZUL MARINO T-30', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1029, 1, 2, 1, '201020005', 'PANTALON IGNIFUGA 30 CAL AZUL MARINO T-32', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1030, 1, 2, 1, '201020006', 'CAMISA IGNIFUGA 30 CAL AZUL MARINO T-M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1031, 1, 2, 1, '201020007', 'CAMISA IGNIFUGA 30 CAL AZUL MARINO T- S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1032, 1, 2, 1, '201020008', 'CAMISA IGNIFUGA 30 CAL AZUL MARINO T-XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1033, 1, 2, 1, '201020009', 'CAMISA IGNIFUGA 30 CAL AZUL MARINO T-XXL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1034, 1, 2, 1, '201020010', 'CUBRE NUCA  27.7 CAL AZUL MARINO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1035, 1, 2, 1, '201020011', 'PANTALON IGNIFUGA 30 CAL AZUL MARINO T-28', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1036, 1, 2, 1, '201020012', 'PANTALON IGNIFUGA 30 CAL AZUL MARINO T-34', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1037, 1, 2, 1, '201020013', 'PANTALON IGNIFUGA 30 CAL AZUL MARINO T-36', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1038, 1, 2, 1, '201020014', 'PANTALON IGNIFUGA 30 CAL AZUL MARINO T-38', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1039, 1, 2, 1, '201020015', 'CAPUCHA IGNIFUGA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1040, 1, 2, 1, '201020016', 'CARETA IGNIFUGA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1041, 1, 2, 1, '201020017', 'CAMISA IGNIFUGA 27.7 CAL NARANJA/PLOMO T-L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1042, 1, 2, 1, '201020018', 'CAMISA IGNIFUGA 27.7 CAL NARANJA/PLOMO T-M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1043, 1, 2, 1, '201020019', 'PANTALON IGNIFUGA 27.7 CAL AZUL MARINO T-28', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1044, 1, 2, 1, '201020020', 'PANTALON IGNIFUGA 27.7 CAL PLOMO T-30', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1045, 1, 2, 1, '201020021', 'PANTALON IGNIFUGA 27.7 CAL PLOMO T-32', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1046, 1, 2, 1, '201020022', 'PANTALON IGNIFUGA 27.7 CAL PLOMO T-34', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1047, 1, 2, 1, '201020023', 'PANTALON IGNIFUGA 27.7 CAL PLOMO T-36', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1048, 1, 2, 1, '201020024', 'CAMISA IGNIFUGA 27.7 CAL NARANJA/PLOMO T-S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1049, 1, 2, 1, '201020025', 'CAMISA IGNIFUGA 27.7 CAL NARANJA/PLOMO T-XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1050, 1, 2, 1, '201020026', 'PANTALON IGNIFUGA 27.7 CAL PLOMO T-28', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1051, 1, 2, 1, '201020027', 'BALACLAVA IGNIFUGA 34 CAL AZUL NAVAL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1052, 1, 2, 1, '201020028', 'BLUSA IGNIFUGA 27.7 CAL NARANJA/PLOMO T-M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1053, 1, 2, 1, '201020029', 'PANTALON IGNIFUGA 27.7 CAL DAMA PLOMO  T-32', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1054, 1, 2, 1, '201020030', 'CAMISA IGNIFUGA 27.7 CAL NARANJA/PLOMO T-XXL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1055, 1, 2, 1, '201020031', 'PANTALON IGNIFUGA 27.7 CAL PLOMO  T-38', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1056, 1, 2, 1, '201020032', 'CAMISA IGNIFUGA 27.7 CAL NARANJA/PLOMO - A MEDIDA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1057, 1, 2, 1, '201020033', 'PANTALON IGNIFUGA 27.7 CAL PLOMO - A MEDIDA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1058, 1, 2, 1, '201030001', 'TRAJE LAVABLE T-L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1059, 1, 2, 1, '201030002', 'TRAJE LAVABLE T - XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1060, 1, 2, 1, '201030003', 'CORREA DIELECTRICO T-28', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1061, 1, 2, 1, '201030004', 'CORREA DIELECTRICO T-30', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1062, 1, 2, 1, '201030005', 'CORREA DIELECTRICO T-32', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1063, 1, 2, 1, '201030006', 'CORREA DIELECTRICO T-34', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1064, 1, 2, 1, '201030007', 'CORREA DIELECTRICO T-36', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1065, 1, 2, 1, '201030008', 'CORREA DIELECTRICO T-38', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1066, 1, 2, 1, '201030009', 'POLO COLOR AZUL MANGA LARGA T-S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1067, 1, 2, 1, '201030010', 'POLO COLOR AZUL MANGA LARGA T-M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1068, 1, 2, 1, '201030011', 'POLO COLOR AZUL MANGA LARGA T-L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1069, 1, 2, 1, '201030012', 'POLO COLOR AZUL MANGA LARGA T-XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1070, 1, 2, 1, '201030013', 'POLO COLOR AZUL MANGA LARGA T-XXL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1071, 1, 2, 1, '201030014', 'TRAJE DESECHABLE TALLA XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1072, 1, 2, 1, '201030015', 'TRAJE DESECHABLE TALLA L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1073, 1, 2, 1, '201030016', 'CHALECO ANARANJADO C/CINTA REFLECTIVA T-L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1074, 1, 2, 1, '201030017', 'CHALECO ANARANJADO C/CINTA REFLECTIVA T-XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1075, 1, 2, 1, '201030018', 'CHALECO ANARANJADO C/CINTA REFLECTIVA T-M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1076, 1, 2, 1, '201030019', 'CAMISA CELESTE T- L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1077, 1, 2, 1, '201030020', 'CAMISA CELESTE T- M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1078, 1, 2, 1, '201030021', 'CAMISA CELESTE T- S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1079, 1, 2, 1, '201030022', 'PANTALON JEAN T- 30', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1080, 1, 2, 1, '201030023', 'PANTALON JEAN T- 32', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1081, 1, 2, 1, '201030024', 'PANTALON JEAN T- 34', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1082, 1, 2, 1, '201030025', 'POLO COLOR PLOMO MANGA LARGA T-L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1083, 1, 2, 1, '201030026', 'POLO COLOR PLOMO MANGA LARGA T-M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1084, 1, 2, 1, '201030027', 'POLO COLOR PLOMO MANGA LARGA T-XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1085, 1, 2, 1, '201030028', 'PANTALON JEAN T- 36', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1086, 1, 2, 1, '201030029', 'POLO COLOR BLANCO MANGA LARGA T- S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1087, 1, 2, 1, '201030030', 'POLO COLOR BLANCO MANGA LARGA T- M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1088, 1, 2, 1, '201030031', 'POLO COLOR BLANCO MANGA LARGA T- L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1089, 1, 2, 1, '201030032', 'POLO COLOR BLANCO MANGA LARGA T- XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1090, 1, 2, 1, '201030033', 'POLO COLOR PLOMO MANGA LARGA T-S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1091, 1, 2, 1, '201030034', 'PANTALON JEAN T- 28', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1092, 1, 2, 1, '201030035', 'CHALECO ANARANJADO C/CINTA REFLECTIVA T-S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1093, 1, 2, 1, '201030036', 'POLO COLOR PLOMO MANGA LARGA T-XXL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1094, 1, 2, 1, '201030037', 'CHOMPA TP JORGE CHAVEZ TALLA - S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1095, 1, 2, 1, '201030038', 'CHOMPA TP JORGE CHAVEZ TALLA - M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1);
INSERT INTO `producto` (`id_producto`, `id_producto_tipo`, `id_material_tipo`, `id_unidad_medida`, `cod_material`, `nom_producto`, `nser_producto`, `mod_producto`, `mar_producto`, `det_producto`, `hom_producto`, `fuc_producto`, `fpc_producto`, `dcal_producto`, `fuo_producto`, `fpo_producto`, `dope_producto`, `est_producto`) VALUES
(1096, 1, 2, 1, '201030039', 'CHOMPA TP JORGE CHAVEZ TALLA - L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1097, 1, 2, 1, '201030040', 'CHOMPA TP JORGE CHAVEZ TALLA - XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1098, 1, 2, 1, '201030041', 'CHOMPA TP JORGE CHAVEZ TALLA - XXL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1099, 1, 2, 1, '201030042', 'CASACA IMPERMEABLE TALLA - L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1100, 1, 2, 1, '201030043', 'POLO COLOR AZUL MANGA LARGA T-XXXL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1101, 1, 2, 1, '201030044', 'PANTALON JEAN  T- 40', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1102, 1, 2, 1, '201030045', 'TRAJE DESECHABLE TALLA M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1103, 1, 2, 1, '201030046', 'CHALECO ANARANJADO C/CINTA REFLECTIVA T- XXL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1104, 1, 2, 1, '201030047', 'CAMISA CELESTE T- XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1105, 1, 2, 1, '201030048', 'PANTALON JEAN T- 38', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1106, 1, 2, 1, '201030049', 'CAMISA CELESTE DAMA T-S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1107, 1, 2, 1, '201030050', 'CAMISA CELESTE DAMA T-M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1108, 1, 2, 1, '201030051', 'CAMISA CELESTE DAMA T-L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1109, 1, 2, 1, '201030052', 'CHALECO ANARANJADO C/CINTA REFLECTIVA T-XS', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1110, 1, 2, 1, '201030053', 'CAMISA CELESTE T- XXL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1111, 1, 2, 1, '201030054', 'PANTALON JEAN T- 26', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1112, 1, 2, 1, '201030056', 'PANTALON JEAN DAMA T- 26', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1113, 1, 2, 1, '201030057', 'PANTALON JEAN DAMA T- 28', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1114, 1, 2, 1, '201030058', 'PANTALON JEAN DAMA T- 30', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1115, 1, 2, 1, '201030059', 'PANTALON JEAN DAMA T- 34', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1116, 1, 2, 1, '201030060', 'TRAJE LAVABLE T- M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1117, 1, 2, 1, '201030061', 'CASACA DAMA T-S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1118, 1, 2, 1, '201030062', 'CASACA DAMA T-M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1119, 1, 2, 1, '201030063', 'CASACA VARON T-S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1120, 1, 2, 1, '201030064', 'CASACA VARON T-M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1121, 1, 2, 1, '201030065', 'CASACA VARON T-L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1122, 1, 2, 1, '201030066', 'CASACA VARON T-XL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1123, 1, 2, 1, '201030067', 'CASACA VARON T-XXL', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1124, 1, 2, 1, '201040001', 'CASCO COLOR NARANJA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1125, 1, 2, 1, '201040002', 'CASCO COLOR BLANCO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1126, 1, 2, 1, '201040003', 'LENTES CONTRAIMPACTO LUNAS OSCURAS', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1127, 1, 2, 1, '201040004', 'LENTES CONTRAIMPACTO LUNAS TRANSPARENTES', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1128, 1, 2, 1, '201040005', 'RESPIRADOR MEDIA CARA C/DOBLE FILTRO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1129, 1, 2, 1, '201040006', 'OREJERA ADAPTABLE AL CASCO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1130, 1, 2, 1, '201040007', 'CARTUCHO CONTRA VAPORES ORGANICOS 6003', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1131, 1, 2, 1, '201040008', 'RESPIRADOR MEDIA CARA 02 VIAS 6200', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1132, 1, 2, 1, '201040009', 'RESPIRADOR PARA PARTICULAS 8247, R95', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1133, 1, 2, 1, '201040010', 'ADAPTADOR CASCO FACIAL AUDITIVO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1134, 1, 2, 1, '201040011', 'FILTRO PARA PARTICULAS 2097', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1135, 1, 2, 1, '201040012', 'MICAS PARA CARETA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1136, 1, 2, 1, '201040013', 'RESPIRADOR CONTRA PARTICULAS 8210', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1137, 1, 2, 1, '201040014', 'SOBRE LENTE LUNA CLARA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1138, 1, 2, 1, '201040015', 'SOBRE LENTE LUNA OSCURA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1139, 1, 2, 1, '201040016', 'TAPON AUDITIVO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1140, 1, 2, 1, '201040017', 'BARBIQUEJO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1141, 1, 2, 1, '201040018', 'CORTAVIENTO ANARANJADO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1142, 1, 2, 1, '201040019', 'ADAPTADOR PORTA VISOR', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1143, 1, 2, 1, '201040020', 'RESPIRADOR DESCARTABLE CON VALVULA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1144, 1, 2, 1, '201040021', 'FILTRO PARA PARTICULAS', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1145, 1, 2, 1, '201040022', 'RESPIRADOR MEDIA CARA 02 VIAS 6300', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1146, 1, 2, 1, '201040023', 'LUNA NEGRA P/MASCARA DE SOLDAR', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1147, 1, 2, 1, '201040024', 'LUNA TRANSPARENTE P/MASCARA DE SOLDAR', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1148, 1, 2, 1, '201040025', 'MASCARILLA KN95', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1149, 1, 2, 1, '201040026', 'CARETA FACIAL + MICA', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1150, 1, 2, 1, '201040027', 'TAFILETE(SUSPENSION) P/CASCO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1151, 1, 2, 1, '201050001', 'GUANTES DE NITRILO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1152, 1, 2, 1, '201050002', 'GUANTE CUERO PU?O CORTO AMARILLO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1153, 1, 2, 1, '201050003', 'GUANTES DE CUERO REFORZADO/ANTIMPACTO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1154, 1, 2, 1, '201050004', 'SOBREGUANTE', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1155, 1, 2, 1, '201050005', 'GUANTE DE CUERO REFORZADO/AMARILLO MOD. LDS', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1156, 1, 2, 1, '201050006', 'GUANTE DE CUERO AMARILLO IM MOD. LDS', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1157, 1, 2, 1, '201050007', 'GUANTES DE JEBE T-8', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1158, 1, 2, 1, '201050008', 'GUANTES ANTICORTE T-9', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1159, 1, 2, 1, '201050009', 'GUANTES ANTICORTE T-10', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1160, 1, 2, 1, '201050010', 'GUANTES DIELECTRICOS CLASE 3', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1161, 1, 2, 1, '201050011', 'GUANTES DIELECTRICOS CLASE 2', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1162, 1, 2, 1, '201050012', 'GUANTES DIELECTRICOS CLASE 4', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1163, 1, 2, 1, '201050013', 'GUANTE DE HILO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1164, 1, 2, 1, '201050014', 'GUANTES ANTICORTE T-7', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1165, 1, 2, 1, '201050015', 'GUANTES ANTICORTE T-8', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1166, 1, 2, 1, '201050016', 'GUANTES DE JEBE T-9', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1167, 1, 2, 1, '201050017', 'GUANTE MULTIFLEX TALLA L', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1168, 1, 2, 1, '201050018', 'GUANTE MULTIFLEX TALLA M', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1169, 1, 2, 1, '201050019', 'GUANTE MULTIFLEX TALLA S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1170, 1, 2, 1, '201050020', 'GUANTES DE JEBE T-10', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1171, 1, 2, 1, '201050021', 'GUANTE ANTIVIBRATORIO T-11', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1172, 1, 2, 1, '201050022', 'GUANTES DE JEBE T-7', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1173, 1, 2, 1, '201050023', 'GUANTE ANTIVIBRATORIO T-10', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1174, 1, 2, 1, '201050024', 'GUANTES DE CUERO REFORZADO ANTI-IMPACTO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1175, 1, 2, 1, '201070001', 'MANDIL DE CUERO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1176, 1, 2, 1, '201070002', 'MANGA DE CUERO', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1177, 1, 2, 1, '201070003', 'ESCARPINES', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1178, 1, 2, 1, '201070004', 'TRAJE DESECHABLE TALLA S', '', 'SEGURIDAD', 'EPPs', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1179, 1, 2, 1, '202010001', 'GIGANTOGRAFIA DE 1.5 x 0.85 MT', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1180, 1, 2, 1, '202010002', 'GIGANTOGRAFIA DE 1.5 x 1 MT', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1181, 1, 2, 1, '202010003', 'BASTON LUMINOSO RECARGABLE ROJO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1182, 1, 2, 1, '202010004', 'LAMPARA DE SE?ALIZACION AMARILLO ( OJO DE GATO)', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1183, 1, 2, 1, '202010005', 'LAMPARA SE?ALIZACION VIAL', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1184, 1, 2, 1, '202010006', 'CINTA DE SE?ALIZACION AMARILLA LDS X 200 MT', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1185, 1, 2, 1, '202010007', 'CINTA DE SE?ALIZACION ROJA LDS X 200 MT', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1186, 1, 2, 1, '202010008', 'CINTA DE SE?ALIZACION ROJO 22,9KV LDS X 500 MT', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1187, 1, 2, 1, '202010009', 'CINTA DE SE?ALIZACION ROJO 10KV LDS X 500 MT', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1188, 1, 2, 1, '202010010', 'HITOS DE SE?ALIZACION', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1189, 1, 2, 1, '202010011', 'CINTA DE SE?ALIZACION ROJO 60KV LDS X 500 MT', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1190, 1, 2, 1, '202010012', 'CINTA DE SE?ALIZACION ROJO 60KV PLUZ X 200 MT', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1191, 1, 2, 1, '202010013', 'PALETA PARE-SIGA', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1192, 1, 2, 1, '202010014', 'TRANQUERA CON BANNER 1,50 X 1,20', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1193, 1, 2, 1, '202010015', 'LETREROS 1.50X 85 EN TRIPLEY DE 6 MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1194, 1, 2, 1, '202010016', 'MICAS DE SE?ALIZACION DE CANDADO BLOQUEO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1195, 1, 2, 1, '202010017', 'PLANTILLA EN VINIL 60X20 CM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1196, 1, 2, 1, '202020001', 'CARTELES IMANTADOS: CIRCUITO ENERGIZADO RIESGO ELECTRO 30X20  VINIL IMANTADO DE 8 MM\"', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1197, 1, 2, 1, '202020002', 'CARTELES IMANTADOS CUIDADO CIRCUITO EN TRABAJO - 25 X 15 CM VINIL IMANTADO DE 8 mm', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1198, 1, 2, 1, '202020003', 'CARTELES IMANTADOS: CUIDADO CIRCUITO EN TRABAJO (VERDE) 25X15 VINIL IMANTADO DE 8 MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1199, 1, 2, 1, '202020004', 'CARTELES IMANTADOS: MANIOBRA EN PROCESO (AMARILLO) 25X35  VINIL IMANTADO DE 8 MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1200, 1, 2, 1, '202020005', 'CARTELES IMANTADOS: MANIOBRA EN PROCESO (AMARILLO) LARGO 98X12 VINIL IMANTADO DE 8 MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1201, 1, 2, 1, '202020006', 'CARTELES IMANTADOS: PELIGRO RIESGO ELECTRICO (ROJO) LARGO 98X12 VINIL IMANTADO DE 8 MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1202, 1, 2, 1, '202020007', 'CARTELES IMANTADOS:PELIGRO TENSION RETORNO (AMARILLOS) 25 X 15 VINIL IMANTADO DE 8 MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1203, 1, 2, 1, '202020008', 'CARTELES IMANTADOS: MANIOBRA EN PROCESO (AMARILLO) 25X15 VINIL IMANTADO DE 8 MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1204, 1, 2, 1, '202020009', 'VINIL SEGUN DISE?O', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1205, 1, 2, 1, '202020010', 'LETREROS 60 X 60CM EN TRIPLEY DE 3MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1206, 1, 2, 1, '202020011', 'LETREROS 75 X 75CM EN TRIPLEY DE 3MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1207, 1, 2, 1, '202020012', 'LETREROS 1,20 X 1,50CM EN TRIPLEY DE 3MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1208, 1, 2, 1, '202020013', 'LETREROS 40 X 60CM  EN TRIPLEY DE 3MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1209, 1, 2, 1, '202020014', 'LETREROS 20 X 30CM  EN FOTOLUMINICENTE', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1210, 1, 2, 1, '202020015', 'LETREROS 90 X 60 CM EN TRIPLEY DE 3MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1211, 1, 2, 1, '202020016', 'LETREROS 1,20 X 1,00 CM EN TRIPLEY DE 3MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1212, 1, 2, 1, '202020017', 'LETREROS 1,20 X 0,60 CM EN TRIPLEY DE 3MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1213, 1, 2, 1, '202020018', 'LETREROS 40 X 20CM  EN TRIPLEY DE 3MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1214, 1, 2, 1, '202020019', 'LETREROS 90 X 60 CM EN TRIPLEY DE 6MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1215, 1, 2, 1, '202020020', 'LETREROS 1,50 X 1,20CM EN TRIPLEY DE 6MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1216, 1, 2, 1, '202020021', 'LETREROS 60 X 60CM EN TRIPLEY DE 6MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1217, 1, 2, 1, '202020022', 'LETREROS 1,20 X 1,00 CM EN TRIPLEY DE 6MM-LAMINADO', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1218, 1, 2, 1, '202020023', 'LETREROS 60 X 40 EN TRIPLEY DE 6 MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1219, 1, 2, 1, '202020024', 'CARTELES IMANTANDOS: SISTEMA BARRA NEGRO 60 X 30 CM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1220, 1, 2, 1, '202020025', 'CARTELES IMANTANDOS: SISTEMA BARRA BLANCO 60 X 30 CM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1221, 1, 2, 1, '202020026', 'LETREROS 1,00 X 1,00 MT EN TRIPLEY DE 6MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1222, 1, 2, 1, '202020027', 'LETREROS 40 X 20 CM EN TRIPLEY DE 6MM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1223, 1, 2, 1, '202020028', 'CARTELES IMANTADOS: PELIGRO TENSION DE RETORNO (NARANJA) 25X15', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1224, 1, 2, 1, '202020029', 'CARTELES IMANTADOS: MANIOBRA EN PROCESO (AMARILLO) 100 X 10 CM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1225, 1, 2, 1, '202020030', 'CARTELES IMANTADOS: MANIOBRA EN PROCESO (ROJO) 100 X 10 CM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1226, 1, 2, 1, '202020031', 'PLANTILLA EN VINIL 15X25 CM', '', 'SEGURIDAD', 'SE?ALIZACIONES', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1227, 1, 2, 1, '203010001', 'CHAPA CERRADURA TP MARIPOSA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1228, 1, 2, 1, '203010002', 'CHAPA MANETA DE AUTOSOPORTADO', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1229, 1, 2, 1, '203010003', 'LUCES DE EMERGENCIA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1230, 1, 2, 1, '203010004', 'CERRADURA TIPO BOLA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1231, 1, 2, 1, '203010005', 'SOLUCION PRESERVANTE PARA LAVAOJOS', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1232, 1, 2, 1, '203010006', 'AGUA OXIGENDA X 120 ML', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1233, 1, 2, 1, '203010007', 'ALCOHOL 70? X 250 ML', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1234, 1, 2, 1, '203010008', 'ALGODON X 100 GR', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1235, 1, 2, 1, '203010009', 'APOSITO 10 x 20 CM', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1236, 1, 2, 1, '203010010', 'BANDAS ADHESIVAS(CURITAS)', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1237, 1, 2, 1, '203010011', 'CLORURO DE SODIO 9/1000 X 1 LT', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1238, 1, 2, 1, '203010012', 'COLIRIO X 12 ML', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1239, 1, 2, 1, '203010013', 'COLLARIN CERVICAL RIGIDO', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1240, 1, 2, 1, '203010014', 'DICLONEFACO 1% GEL', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1241, 1, 2, 1, '203010015', 'ESPARADRAPO DE 5CM X 4.5M', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1242, 1, 2, 1, '203010016', 'ESPARADRAPO DE 1.2CM X 9.1M', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1243, 1, 2, 1, '203010017', 'FERULAS NEUMATICAS X 6 PZAS', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1244, 1, 2, 1, '203010018', 'GASA ESTERIL  10 x 10 CM', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1245, 1, 2, 1, '203010019', 'GASA PARAFINADA 10 x 10 CM', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1246, 1, 2, 1, '203010020', 'GUANTES QUIRURGICOS', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1247, 1, 2, 1, '203010021', 'INMOVILIZADOR LATERAL DE CABEZA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1248, 1, 2, 1, '203010022', 'MALETIN DE ABORDAJE 40x25x20 CM', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1249, 1, 2, 1, '203010023', 'MALETIN DE ABORDAJE 60x20x45 CM', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1250, 1, 2, 1, '203010024', 'MANTA ALUMINIZADA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1251, 1, 2, 1, '203010025', 'MASCARILLA PARA RCP - POCKET MASK', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1252, 1, 2, 1, '203010026', 'PALETAS BAJA LENGUA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1253, 1, 2, 1, '203010027', 'PARACETAMOL 500 MG', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1254, 1, 2, 1, '203010028', 'PINZA QUIRURGICA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1255, 1, 2, 1, '203010029', 'TIJERA QUIRURGICA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1256, 1, 2, 1, '203010030', 'VENDA ELASTICA  3\" x 5 YRDS', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1257, 1, 2, 1, '203010031', 'VENDA ELASTICA  4\" x 5 YRDS', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1258, 1, 2, 1, '203010032', 'VENDA TRIANGULAR', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1259, 1, 2, 1, '203010033', 'YODOPOVIRINA 7.5% Espuma X 120 ML', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1260, 1, 2, 1, '203010034', 'BANDEJA METALICA ANTIDERRAME', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1261, 1, 2, 1, '203010035', 'CORDON ABSORBENTE', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1262, 1, 2, 1, '203010036', 'MALETA PARA KIT ANTIDERRAME', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1263, 1, 2, 1, '203010037', 'BARRA RECTRACTIL - CONO DE SEGURIDAD', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1264, 1, 2, 1, '203010038', 'TRANQUERA DE 4 CUERPOS PVC', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1265, 1, 2, 1, '203010039', 'MALLA DE SEGURIDAD COLOR NARANJA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1266, 1, 2, 1, '203010040', 'BLOQUEADOR SOLAR', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1267, 1, 2, 1, '203010041', 'LAVA OJO PORTATIL', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1268, 1, 2, 1, '203010042', 'KIT ANTIDERRAME VEHICULAR LDS', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1269, 1, 2, 1, '203010043', 'KIT ANTIDERRAME SET?s LDS', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1270, 1, 2, 1, '203010044', 'ALCOHOL 70? X 120 ML', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1271, 1, 2, 1, '203010045', 'ALGOD?N X 50 GR', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1272, 1, 2, 1, '203010046', 'CAMILLA RIGIDA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1273, 1, 2, 1, '203010047', 'MANUAL DE PRIMEROS AUXILIOS', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1274, 1, 2, 1, '203010048', 'MALLA RASCHELL AL 80%', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1275, 1, 2, 1, '203010049', 'CANDADO', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1276, 1, 2, 1, '203010050', 'CERROJO HORIZONTAL PARA PORTON', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1277, 1, 2, 1, '203010051', 'CERROJO VERTICAL PARA PORTON', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1278, 1, 2, 1, '203010052', 'ALDABA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1279, 1, 2, 1, '203010053', 'CERROJO', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1280, 1, 2, 1, '203010054', 'CONO 70 CM C/CINTA REFLECTIVA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1281, 1, 2, 1, '203010055', 'CILINDRO C/CINTA REFLECTIVA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1282, 1, 2, 1, '203010056', 'BOTIQUIN', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1283, 1, 2, 1, '203010057', 'MALLA RASCHELL AL 60%', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1284, 1, 2, 1, '203010058', 'ALCOHOL ISOPROPILICO', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1285, 1, 2, 1, '203010059', 'KIT ANTIDERRAME', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1286, 1, 2, 1, '203010060', 'CORRE PARA CAMILLA', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1287, 1, 2, 1, '203010061', 'BOTIQUIN', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1288, 1, 2, 1, '203010062', 'REPELENTE', '', 'SEGURIDAD', 'SUMINISTRO DE SEGURIDAD', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1289, 2, 1, 1, '801010001', 'SERVICIO MOVILIDAD', '', 'TERCEROS', 'ENTREGAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1290, 2, 1, 1, '801010002', 'TRANSPORTE', '', 'TERCEROS', 'ENTREGAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1291, 2, 1, 1, '801010003', 'FLETE', '', 'TERCEROS', 'ENTREGAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1292, 2, 1, 1, '801010004', 'DELIVERY', '', 'TERCEROS', 'ENTREGAS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1293, 1, 1, 1, '804010001', 'LICENCIA MICROSOFT 365 Business Basic / Anual', '', 'TERCEROS', 'PRODUCTOS CORPORATIVOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1294, 1, 1, 1, '804010002', 'LICENCIA POWER Bi Pro / Anual', '', 'TERCEROS', 'PRODUCTOS CORPORATIVOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1295, 2, 1, 1, '802010001', 'MANTENIMIENTO DE VARILLAJE SEG?N MUESTRA', '', 'TERCEROS', 'SERVICIO', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1296, 1, 2, 1, '803010001', 'REPUESTO', '', 'TERCEROS', 'VEHICULOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1),
(1297, 1, 2, 1, '803010002', 'LLANTA', '', 'TERCEROS', 'VEHICULOS', '', NULL, '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_tipo`
--

CREATE TABLE `producto_tipo` (
  `id_producto_tipo` int(11) NOT NULL,
  `nom_producto_tipo` longtext DEFAULT NULL,
  `est_producto_tipo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_tipo`
--

INSERT INTO `producto_tipo` (`id_producto_tipo`, `nom_producto_tipo`, `est_producto_tipo`) VALUES
(1, 'MATERIAL', 1),
(2, 'SERVICIO', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `nom_proveedor` longtext DEFAULT NULL,
  `ruc_proveedor` longtext DEFAULT NULL,
  `dir_proveedor` longtext DEFAULT NULL,
  `tel_proveedor` longtext DEFAULT NULL,
  `cont_proveedor` longtext DEFAULT NULL COMMENT 'contacto del proveedor',
  `est_proveedor` int(11) DEFAULT NULL,
  `mail_proveedor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nom_proveedor`, `ruc_proveedor`, `dir_proveedor`, `tel_proveedor`, `cont_proveedor`, `est_proveedor`, `mail_proveedor`) VALUES
(1, 'CAPACOILA APAZA SANTOS', '10012952690', '', '', '', 1, 'Valdomero826@gmail.com'),
(2, 'PALOMINO LEON FERNANDO EDUARDO', '10060679181', 'JR. MARIANO GOYONECHE NRO. 239 P.J. RICARDO PALMA LIMA LIMA LIMA', '', '', 1, ''),
(3, 'CISNEROS RIVEROS EDGARD RODOLFO', '10084795734', '', '', '', 1, 'cisnerosk21empresas@gmail.com'),
(4, 'GUEVARA RAMIREZ HUGO JARALITO', '10085193266', '', '', '', 1, 'hgr_604@hotmail.com'),
(5, 'ESCUDERO CABADA FERNANDO', '10101983370', 'URB. PRO - LOS OLIVOS', '', '', 1, 'ferescu@hotmail.com'),
(6, 'VILCHEZ ANGO VIVIANA BELINDA', '10106045572', '', '', '', 1, 'vivianatransalonso@hotmail.com'),
(7, 'LLERENA HINOSTROZA JOHANNA ALICIA', '10108866409', 'Jr. Los tornos N°226 URB.Naranjal alt. Estacion naranjal. Independencia.Lima', '', '', 1, ''),
(8, 'SARAVIA RAMOS MARINO', '10154201209', 'AV. LA MAR 695- IMPERIAL CAÑETE', '', '', 1, ''),
(9, 'POSTILLON QUISPE COSME DAMIAN', '10154211034', 'URB. SINDICATO DE CHOFERES MZA. O LOTE. 32 FRENTE A GRIFO PRIMAX', '', '', 1, ''),
(10, 'REYES DE LA CRUZ MIGUEL ANGEL', '10154375720', '', '', '', 1, ''),
(11, 'SOCALAY VEGA MARIA MAGDALENA', '10154382751', '', '', '', 1, ''),
(12, 'GUTIERREZ FERNANDEZ JUAN MIGUEL', '10222892908', 'Av. Ignacio Merino N° 1983 – Lince', '', '', 1, 'miguelgutyabogados@gmail.com'),
(13, 'MUÑOZ BELLINA ANGEL ALBERTO', '10255788723', 'JR. ARICA NRO. 269 INT. C PROV. CONST. DEL CALLAO PROV. CONST. DEL CALLAO CALLAO', '', '', 1, ''),
(14, 'NUÑEZ GONZALES ISRAEL JOSE', '10256829032', 'CAL. 4 MZA. F LOTE. 16 URB. CIUDAD DEL PESCADOR PROV. CONST. DEL CALLAO PROV. CONST. DEL CALLAO BELLAVISTA', '', '', 1, ''),
(15, 'PEREZ RODRIGUEZ MARCO RICHAR', '10274270841', 'MZA. X LOTE. 7 URB. VILLA CORPAC LIMA - LIMA - CARABAYLLO', '', '', 1, 'marco.perez2007@hotmail.com'),
(16, 'PEREA ROJAS JULIO ALEJANDRO', '10402387977', 'CAL. PORTA 107 MIRAFLORES - LIMA - LIMA', '', '', 1, ''),
(17, 'RAMOS FLORES WALTER MIGUEL', '10406589442', 'AV. CANTA CALLAO URB. LOS HUERTOS DEL NARANJAL MZA. D LOTE. 1', '', '', 1, ''),
(18, 'HUAYHUA PAYTAN ALFREDO JHONNY', '10408178423', 'NUEVO PARAÍSO AV EL EDEN MZ B LT 7 NUEVO PARAÍSO DE NIEVERIA LURIGANCHO-CHOSICS', '', '', 1, 'honny25al@hotmail.com'),
(19, 'ROSSEL BUSTAMANTE EDWIN', '10409448092', '', '', '', 1, 'rosseltransport@gmail.com'),
(20, 'FLORES CRISTOBAL FRANCO ANTONIO', '10410623418', 'JIRON CHIRA 191- RIMAC', '', '', 1, 'franxto81@gmail.com'),
(21, 'PARIONA HIDALGO DIANA GLADIS', '10411349531', 'AV. ARGENTINA NRO. 144 INT. I-19 GALERIA UNICENTRO NIVEL B (FRENTE A LA PLAZA UNIÓN)LIMA - LIMA - LIMA', '', '', 1, 'grupo.trupar@gmail.com , dpariona@gmail.com'),
(22, 'MERCEDES ESQUIVEL GUILLERMO EZEQUIEL', '10413112252', '', '', '', 1, 'guillermomercedes@yahoo.com'),
(23, 'ARENAS HERRERA JESUS ALEXANDER', '10424511884', '', '', '', 1, 'arenas.herrera15@outlook.com'),
(24, 'VARELA CORREA CESAR AUGUSTO CHRISTIAN', '10428803146', '', '', '', 1, 'cesarvarelacorrea@gmail.com'),
(25, 'MARTINEZ CRISOSTOMO JOSEP', '10431460659', 'AV. TOMAS MARSANO N°3010 INT. 203-SURCO', '', '', 1, 'rackmaxperu@hotmail.com'),
(26, 'FLORES MARTINEZ JOSE JUNIOR', '10432464861', 'AV. GARCILAZO DE LA VEGA 1348 INTERIOR 2B-144 LIMA', '', '', 1, ''),
(27, 'TORRES SILVA RUBEN OCTAVIO', '10437499018', '', '', '', 1, 'toptorres.020@gmail.com'),
(28, 'DAMIAN VALDERA ROLANDO', '10441284107', 'Calle Los Talleres Mz-E puesto 3 lote 16A Urb. Naranjal. Independencia.Lima.', '', '', 1, ''),
(29, 'IBARRA HUASHUAYO MARISOL', '10443430046', '', '', '', 1, 'marisolibarrah@gmail.com'),
(30, 'LEON MEJIA GLADIS', '10447472614', 'CALLE BAHIA AZUL MZ F LT 15 - URB PORTALES DEL REY - CALLAO', '', '', 1, 'ventas@aquabenites.com'),
(31, 'FARGE JURADO RAMIRO', '10457648842', 'Mz G1 LT-5 Av. Pedro Huilca Parque Industrial de Villa el Salvador', '', '', 1, 'fargeramiro80@gmail.com'),
(32, 'YACTAYO LOPEZ GEANCARLO MARTIN', '10461336618', '', '', '', 1, ''),
(33, 'SIFUENTES EGUSQUIZA NIRZA KATERINE', '10466773897', 'PASAJE CORONEL - MIGUEL ZAMORA 187- CERCADO DE LIMA', '', '', 1, 'corporacionfranir@gmail.com'),
(34, 'ESCUDERO OLARTE MAICOL FRANYN', '10469550902', 'AV. GUILLERMO DANSEY 828 PSJE 9 TIENDA E027 - CC. UDAMPE - LIMA', '', '', 1, 'ventasmkeindustria.com'),
(35, 'CUTI RUIZ TATIANA', '10472717702', '', '', '', 1, 'tatiana.cuti.ruiz@gmail.com'),
(36, 'JORGE LUIS SERRANO ORE', '10475044415', 'AV. SANTUARIO 2114 URB. MANGOMARCA-SJL', '', '', 1, 'serranoorlando63@gmail.com'),
(37, 'JAQQUEHUA YUPANQUI DENIS', '10712008895', 'AV. GUILLERMO DANSEY 464 INT AZ9 PSJE.8', '', '', 1, 'denisjaquehuayupa@gmail.com'),
(38, 'TUCTO BREÑA MERLY CRISTHEL', '10729075910', 'AV. ARICA NRO. 1436 CHACRA COLORADA', '', '', 1, ''),
(39, 'CHAVEZ PUNTILLO KENEDY JHUNIOR', '10741416641', '', '', '', 1, 'kenedy9314@hotmail.com'),
(40, 'EXIMPORT DISTRIBUIDORES DEL PERU S A', '20100041520', 'AV. ARGENTINA NRO. 1710 (ALT AV NICOLAS DUEñAS) LIMA - LIMA - LIMA', '', '', 1, 'jtirado@edipesa.com.pe'),
(41, 'PROMOTORES ELECTRICOS S A', '20100084172', 'AV. NICOLAS ARRIOLA NRO. 899 URB. SANTA CATALINA LIMA - LIMA - LA VICTORIA', '', '', 1, 'jocana@promelsa.com.pe'),
(42, 'SGS DEL PERU S.A.C.', '20100114349', '', '', '', 1, 'magdalena.Pizarro@sgs.com'),
(43, 'CORPORACION LA SIRENA SAC', '20100157315', 'CAL.AMADOR MERINO REYNA NRO. 339 INT. 1201 URB. JARDIN LIMA - LIMA - SAN ISIDRO', '', '', 1, 'dcorimanya@lasirena.com.pe'),
(44, 'FABRICACION DE REPUESTOS S A', '20100245362', 'CAL.6 MZA. D LOTE. 17 URB. GRIMANEZA (ALT.CDRA 28 AV.ELMER FAUCETT) PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - CALLAO', '', '', 1, 'gespinoza@faresa.com.pe'),
(45, 'TALMA S A', '20100589703', 'AV. MARISCAL ELOY URETA NRO. 560 URB. EL PINO LIMA - LIMA - SAN LUIS', '', '', 1, 'talmasa@talmasa.com'),
(46, 'LOGYTEC S.A.', '20101120792', 'CAL.CL. ISIDORO SUAREZ NRO. 236 URB. MARANGA LIMA - LIMA - SAN MIGUEL', '', '', 1, 'karinakamida@logytec.com.pe'),
(47, 'ZAMTSU CORPORACION SRL', '20108605392', 'JR. ENRIQUE BARRON NRO. 1065 URB. SANTA BEATRIZ (ALT CDRA 5 MARIANO CARRANZA) LIMA - LIMA - LIMA', '', '', 1, 'zamtsu@zamtsu.com'),
(48, 'TIENDAS DEL MEJORAMIENTO DEL HOGAR S.A. -  SODIMAC', '20112273922', 'AV. ANGAMOS ESTE NRO. 1805 INT. 2 (OFICINA 2) LIMA - LIMA - SURQUILLO', '', '', 1, ''),
(49, 'DIAMIRE S.R.L', '20112396765', 'AV. ALFREDO BENAVIDES NRO. 5255 URB. LAS GARDENIAS LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'atencionalcliente@diamire.com'),
(50, 'SEGURINDUSTRIA SA', '20131529181', 'AV. NICOLAS DE PIEROLA NRO. 980 URB. PRIMAVERA LA LIBERTAD - TRUJILLO - TRUJILLO', '', '', 1, 'ventaslima5@segusa.com.pe'),
(51, 'DISTRIBUCIONES TECNICAS S.A.C.', '20136615531', 'AV. TOMAS MARSANO NRO. 1301 LIMA - LIMA - SURQUILLO', '', '', 1, 'ventas@ditesac.com'),
(52, 'ELECTROCOM INGENIEROS S.A.C', '20136727410', '', '', '', 1, 'salfaro@electrocomingenieros.com'),
(53, 'RAGS SERVICIOS ESPECIALIZADOS S.A.C.', '20145361835', 'AV. MILITAR NRO. 2780 LIMA - LIMA - LINCE', '01 4223914', '', 1, '-'),
(54, 'TECSUR S.A.', '20206018411', 'PJ. CALANGO NRO. 158 (ALT.CDRA.3 Y 4 AV.P.MIOTTA) LIMA - LIMA - SAN JUAN DE MIRAFLORES', '', '', 1, 'cquisini@tecsur.com.pe'),
(55, 'REPLICA S.R.LTDA.', '20251505111', 'JR. JUAN DE ALIAGA NRO. 260 (ANTES JOSE COSSIO) LIMA - LIMA - MAGDALENA DEL MAR', '', '', 1, 'mtovar@replica.com.pe'),
(56, 'MEGAPACK TRADING SAC', '20266777184', 'AV. PRIMAVERA NRO. 609 INT. 501 URB. CHACARILLA DEL ESTANQUE (AL FRENTE DEL RESTAURANTE LA BASÍLICA) LIMA - LIMA - SAN BORJA', '953501832', '', 1, 'flor.rosas@mpt.pe'),
(57, 'SIGELEC S.A.C.', '20268214527', 'AV. OSCAR R. BENAVIDES NRO. 5289 PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - CALLAO', '', '', 1, 'aquispe@sigelec.com.pe; adominguez@sigelec.com.pe'),
(58, 'INDAL CONSTRUCCIONES METALICAS S.R.L.', '20292435224', 'CAL.LOS CINCELES NRO. 311 URB. INDUSTRIAL FLORES OCHENTIUNO - SAN JUAN DE LURIGANCHO', '', '', 1, 'ventas1@indalperu.com'),
(59, 'UNION DE CONCRETERAS S.A', '20297543653', 'CAR.PANAMERICANA SUR NRO. 11.4 Z.I. FUNDO EL CHILCAL LIMA - LIMA - SAN JUAN DE MIRAFLORES', '', '', 1, 'eac@unicon.com.pe'),
(60, 'INDUSTRIAS MANRIQUE S.A.C.', '20307214386', 'CAL.LOS TORNOS NRO. 259 URB. EL NARANJAL (ESPALDA DE PURINA) LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'ventas3@grupomanrique.com'),
(61, 'UNI-SPAN PERU S.A.', '20377735146', 'JR. LOS TULIPANES MZA. H LOTE. 7B (UNIDAD C 10857 LT A) LIMA - LIMA - LURIN', '', '', 1, 'daniel.paredes@unispan.com.pe'),
(62, 'MIXERCON S.A', '20380289360', 'CAR.PANAMERICANA SUR KM.17.5 MZA. C LOTE. 4 ASOCIACION LA CONCORDIA (PARADERO LA CAPILLA KM 17.5 PAN-SUR) LIMA - LIMA - VILLA EL SALVADOR', '', '', 1, 'irmarys.martinez@mixercon.com'),
(63, 'IMPORTACIONES GELCO S.A.C. - GELCO S.A.C.', '20390932864', 'AV. PANAMERICANA SUR KM. 18.5 MZA. D LOTE. 11B (ALT PARQUE ZONAL HUAYNA CAPAC) LIMA - LIMA - VILLA EL SALVADOR', '', '', 1, 'krodriguez@gelco.pe'),
(64, 'TJ H2B LATINA S.A.C.', '20392871617', 'CAL.3 NRO. 177 URB. GRIMANESA (ALTURA CUADRA 27 Y 28 DE FAUCETT) PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - CALLAO', '', '', 1, 'fernandez@tjh2b.com'),
(65, 'FERRIER S.A.', '20414675761', 'JR. PIETRO TORRIGIANO NRO. 166 URB. CORPAC (ALT. CDRA. 6 AV. GUARDIA CIVIL) LIMA - LIMA - SAN BORJA', '', '', 1, 'ahuatuco@ferriersa.com.pe'),
(66, 'TECNO FAST S.A.C.', '20417573705', 'LOTE. 6 Z.I. SECTOR PAMPAS DE LURIN (ALTURA KM. 40 PANAMERICANA SUR) LIMA - LIMA - LURIN', '', '', 1, 'rvelasquez@tecnofast.com.pe'),
(67, 'ANCRO S.R.L.', '20431084172', 'AV. LOS CIPRESES NRO. 250 URB. LOS FICUS LIMA - LIMA - SANTA ANITA', '', '', 1, 'asesor8@ancro.com.pe'),
(68, 'ISA INDUSTRIAL S.A.C', '20451826817', 'JR. MANUEL RAMIREZ SICCA NRO. 123 LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'ventas@isaindustrial.com'),
(69, 'INVERSIONES ETJ S.A.C.', '20452297770', 'CAL.JAIME GARZA NRO. 367 COO. 25 DE DICIEMBRE (3ER PISO . ALT CDRA 14 AVIACION) LIMA - LIMA - LA VICTORIA', '', '', 1, 'cotizaciones@etjsac.com'),
(70, 'PULSO CORPORACION MEDICA S.A.C', '20455823880', 'AV. JAVIER PRADO ESTE NRO. 2932 URB. SAN BORJA LIMA - LIMA - SAN BORJA', '', '', 1, 'ltipacti@pulsosalud.com'),
(71, 'CONTROL DE GRUPOS ELECTROGENOS S.A.C.', '20458280348', 'AV. ARGENTINA NRO. 6304 (FRENTE A LA FABRICA GOODYEAR) PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - CALLAO', '', '', 1, 'renta@cogelsac.net'),
(72, 'COROIMPORT S.A.C.', '20459481967', 'AV. LUIS GALVANI MZA. M LOTE. 16 LOT. IND. SANTA ROSA LIMA - LIMA - ATE', '', '', 1, 'construccion@coroimport.com'),
(73, 'ELECTRO SERVICE MONTAJES S.R.L.', '20474917542', 'CAL.9 MZA. F LOTE. 1A URB. ESTIBADORES DEL CALLAO LIMA - LIMA - SAN MARTIN DE PORRES', '955971879', '', 1, 'psanchez@esmosrl.com'),
(74, 'MADERERA OXAPAMPA DEL SUR E.I.R.L', '20491345927', 'JR. 15 DE NOVIEMBRE NRO. 229 LIMA - CAÑETE - IMPERIAL', '', '', 1, 'maoxa_rchv@hotmail.com'),
(75, 'MANIOPERU SAC', '20492064298', 'AV. GUILLERMO DANSEY NRO. 660 (PARALELA CDRA 4 AV. COLONIAL) LIMA - LIMA - LIMA', '', '', 1, 'ventas@manioperu.com'),
(76, 'CENTURY ECOLOGICAL CORPORATION S.A.C. - ECOCENTURY S.A.C.', '20502073401', 'ALM.DEL PREMIO REAL MZA. P1 LOTE. 1 URB. HUERTOS DE VILLA (FRENTE A LA GRANJA VILLA) LIMA - LIMA - CHORRILLOS', '', '', 1, 'asesor.comercial@ecocentury.pe'),
(77, 'DJT SERVICIOS CORPORATIVOS S.A.C', '20502955757', 'AV. SAN JUAN MZA. L LOTE. 34 URB. SANTA ROSITA II ETAPA (AV. 26 DE MAYO Y AV. LOS ANGELES) LIMA - LIMA - ATE', '', '', 1, 'jteran@djtsac.com'),
(78, 'SERVICIOS GENERALES EL CARIBE S.R.L.', '20503642591', 'AV. NICOLAS AYLLON NRO. 555 (FRENTE AL MERCADO JORGE CHAVEZ) LIMA - LIMA - LIMA', '', '', 1, 'ferreteria@elcaribe.pe'),
(79, 'ABC TECNICA COMERCIAL S.R.L.', '20504298061', 'AV. LAUREL ROSA NRO. 429 DPTO. 502 URB. LOS SAUCES LIMA - LIMA - SURQUILLO', '', '', 1, 'oscar@abctecnicacomercial.com'),
(80, 'INGENIEROS FRIOTEMP S.A.C.', '20504753795', 'CAL.JOSE MARIANO ARCE NRO. 493 (ALT DE LA CUADRA 10 DE LA AV. BOLIVAR) LIMA - LIMA - PUEBLO LIBRE (MAGDALENA VIEJA)', '989454003', '', 1, 'ventas3@friotemp.com.pe'),
(81, 'INDUSTRIA DEL CALZADO SEMPERTEGUI SOCIEDAD COMERCIAL DE RESPONSABILIDAD LIMITADA', '20505137427', 'NRO. MZ62 INT. LT24 A.H. ARMANDO VILLANUEVA (A 2 CDRAS DE LA HUACA NARANJAL) LIMA - LIMA - LOS OLIVOS', '', '', 1, ''),
(82, 'JG3 CONSTRUCCIONES S.A.C', '20505212739', 'AV. ROSALES LOTE. 83-B A.V. HUERTOS DE TUNGASUCA (KM. 4.5 AV TRAPICHE) LIMA - LIMA - CARABAYLLO', '', '', 1, 'ggaray@jg3construcciones.com'),
(83, 'CORPORACION EMACIN S.A.C', '20505557477', 'JR. RAMON CARCAMO 540 NRO. 542 (ALT CDRA.6 AV. COLONIAL / AV. ARGENTINA) LIMA', '', '', 1, 'comercial@emacin.com.pe'),
(84, 'FIERRO & ACERO CENTER S.A.C.', '20506064814', 'AV. REPUBLICA DE ARGENTINA NRO. 2010 URB. CONDE DE LAS TORRES LIMA - LIMA - LIMA', '', '', 1, 'caja.a1@fyasac.com'),
(85, 'MECHANICAL WORLD S.A.C.', '20506591431', 'AV. ELMER FAUCETT NRO. 321 URB. MARANGA 6TA ETAPA (CRUCE CON AV.VENEZUELA A 3 CDRAS.) LIMA - LIMA - SAN MIGUEL', '', '', 1, 'jprado@mechanical.pe'),
(86, 'GESTION DE SERVICIOS AMBIENTALES S.A.C.', '20507850091', 'AV. PASEO DE LA REPUBLICA NRO. 3617 INT. 601 URB. MALIBU LIMA - LIMA - SAN ISIDRO', '', '', 1, 'atencionalcliente@ambipar.pe.'),
(87, 'CHEM TOOLS SAC', '20507926844', 'JR. NEON NRO. 5645 URB. INDUSTRIAL INFANTAS (POR LA DISTRIBUIDORA NISSAN) LIMA - LIMA - LOS OLIVOS', '', '', 1, 'rramirez@chemtools.com.pe>'),
(88, 'C & C COMERCIALIZACION Y SERVICIOS S.R.L.', '20509395960', 'AV. QUILCA NRO. 869 URB. PERU (ENTRE AV PERU Y AV QUILCA) LIMA - LIMA - SAN MARTIN DE PORRES', '01 572-3364', '', 1, 'comservi_pe@yahoo.com'),
(89, 'KAPEK INTERNACIONAL S.A.C', '20509654141', 'CAL.LAS ROSAS NRO. 314 URB. SAGRADA FAMILIA (ALT. CUADRA 20 DE AV. VENEZUELA) PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - BELLAVISTA', '', '', 1, 'omedina@kapekinternacional.com'),
(90, 'REPRESENTACIONES EL VADO S.A.C.', '20509816655', 'CAL.SANTA TERESA NRO. 129 URB. INDUSTRIAL LOS SAUCES (ENTRE MRCAL NIETO Y AV LAS TORRES) LIMA - LIMA - ATE', '01 4730063', '', 1, 'ventas5elvadosac@gmail.com'),
(91, 'ALVISOFT PERU S.A.C.', '20509887676', 'CAL.MARTIR JOSE OLAYA NRO. 129 DPTO. 1803 COM. SAN MIGUEL DE MIRAFLORES LIMA - LIMA - MIRAFLORES', '', '', 1, ''),
(92, 'INDETNA S.A.C.', '20512229035', 'CAL.10 DE AGOSTO S/N MZA. LL3 LOTE. 09 P.J. NESTOR GAMBETA BAJA ESTE SECTOR 3 (AV. ALAMEDA FRENTE COLEGIO BONATTI) PROV. CONST. DEL CALLAO - PROV. CON', '', '', 1, 'indetna@hotmail.com'),
(93, 'OBL ASOCIADOS S.A.C.', '20514044911', 'PRO.JAVIER PRADO MZA. I LOTE. 28 URB. MAYORAZGO CHICO LIMA - LIMA - ATE', '', '', 1, 'ventas@obl.com.pe'),
(94, 'HANDEL WELT S.A.C.', '20514299669', 'AV. MARISC.OSCAR R.BENAVIDES NRO. 1617 (EX COLONIAL 1617) LIMA - LIMA - LIMA', '', '', 1, 'VENTAS6@HANDEL-WELT.COM'),
(95, 'INVERSIONES MADERERA AARON SOCIEDAD COMERCIAL DE RESPONSABILIDAD LIMITADA - INVERSIONES MADERERA AAR', '20514430633', 'AV. HUAINA CAPAC MZA. BD LOTE. 7 SEC. EL VALLE SECTOR 1 (PJ SECTOR EL VALLE SANTA ROSA JICAMARCA) LIMA - LIMA - SAN JUAN DE LURIGANCHO', '', '', 1, 'contacto@inversionesaaron.com'),
(96, 'TECNOLOGIA DESARROLLO Y METROLOGIA EN ELECTROTECNIA E.I.R.L. - TDM E.I.R.L.', '20515157493', 'MZA. K LOTE. 13 URB. SESQUICENTENARIO PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - CALLAO', '', '', 1, 'wmontoya@tdmperu.com.pe'),
(97, 'CONEXOS IMPORT S.R.L.', '20516351081', 'CAL.OMEGA NRO. 233 URB. PARQUE INTERNACIONAL DE INDUSTRIA Y COMERCIO PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - CALLAO', '', '', 1, 'aconexos@yahoo.com mquintero@conexosperu.com'),
(98, 'SEGURIDAD INDUSTRIAL GABIC E.I.R.L.', '20517660249', 'AV. GUILLERMO DANSEY NRO. 510 INT. Z1-4 (CC BELLOTA ENTRE JR. ASCOPE Y AV. DANSEY) LIMA - LIMA - LIMA', '', '', 1, 'ventas@gabicsafety.com'),
(99, 'OLIVERA & GINES S.A.C. - OLIVEG S.A.C.', '20518390130', 'AV. NICOLAS AYLLON NRO. 1501 URB. CHACLACAYO (ALT 7 CDRA DE LA MUNICIPALIDAD) LIMA - LIMA - CHACLACAYO', '', '', 1, 'informes@olivegsac.com'),
(100, 'SERVICIOS LOGISTICOS ALVA S.A.C. - SERVIALVA S.A.C.', '20518456416', 'CAL.ACUARIO MZA. T LOTE. 17A URB. SOL DE VITARTE SECTOR G (PDRO PISTA NUEVA-PQUE 3) LIMA - LIMA - ATE', '', '', 1, 'slogisticos.alva@gmail.com'),
(101, 'AJCME E.I.R.L', '20519109744', 'AV. CENTRAL NRO. 401 DPTO. 5 C.P. SANTA CLARA LIMA - LIMA - ATE', '', '', 1, 'AJCME2008@HOTMAIL.COM'),
(102, 'SERVICIOS ELECTRICOS Y TERMOGRAFICOS SAC - SELYTER SAC', '20521083655', 'CAL.LOS HUANCAS NRO. 276 URB. MARANGA (ALT. CDRA. 16 AV. LA PAZ) LIMA - LIMA - SAN MIGUEL', '', '', 1, 'selyter@aol.com'),
(103, 'LABORATORIO INGGEOS S.A.C.', '20521151324', 'ASOCIACION EL PROGRESO MZA. K LOTE. 14 PAMPLONA BAJA (PAMPLONA BAJA - ALT. DE PEBAL INMACULADA) LIMA - LIMA - SAN JUAN DE MIRAFLORES', '', '', 1, 'ventas@inggeos.com.pe'),
(104, 'MADERERA VIRGEN DEL ROSARIO E.I.R.L.', '20521236150', 'CAR.CENTRAL KM. 15.5 LOTE. 3 FND. PARIACHI ZONA 5 (FRENTE AL GRIFO VIMER) LIMA - LIMA - ATE', '', '', 1, ''),
(105, 'CONFECCIONES ADONAI S.A.C', '20521661101', '', '', '', 1, 'confecciones.adonai@hotmail.com'),
(106, 'EMPRESA DE TRANSPORTES JOSE RAFAEL FERNANDEZ EIRL', '20522465713', 'CAL.LAS LAUSONIAS NRO. 186 URB. LOS JARDINES DE SAN JUAN ET. DOS LIMA - LIMA - SAN JUAN DE LURIGANCHO', '', '', 1, ''),
(107, 'PLACA MASS E.I.R.L', '20522718786', '', '', '', 1, 'ventas@placamass.com'),
(108, 'ENSYS S.A.C.', '20523698193', 'JR. ALICANTE NRO. 282 URB. RESIDENCIAL HIGUERETA (ALT.CDRA 50 AV.AVIACION-ESTACION CABITOS) LIMA - LIMA - SANTIAGO DE SURCO', '01 652-7674', '', 1, 'ensys@ensys.pe'),
(109, 'ACEROS IMPORT SOCIEDAD ANONIMA CERRADA', '20524196205', 'CAL.JAVIER HERAUD MZA. M LOTE. 5 SEC. MARTIRES DEL SUTEP (ALT. CDRA 14 AV. ANGELICA GAMARRA) LIMA - LOS OLIVOS', '', '', 1, 'ventas3@acerosimport.com'),
(110, 'SERCON SRL', '20524483441', 'JR. HUANUCO NRO. 3966 URB. PERU LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'ventas@serconindustria.com'),
(111, 'GRUPO PEREDA\'S SERVICIOS GENERALES S.A.C.', '20524878209', 'CAL.HORACIO CACHAY DIAZ NRO. 190 URB. SANTA CATALINA (ESPALDA PARQUE DEL ENCUENTRO) LIMA - LIMA - LA VICTORIA', '', '', 1, 'ventas@grupesac.com'),
(112, 'F & A GEOINGENIERIA S.A.C.', '20535675920', 'CAL.DANIEL ALCIDES CARRION NRO. 429 URB. OYAGUE LIMA - LIMA - MAGDALENA DEL MAR', '', '', 1, 'gerencia.general@fia-geoingenieria.com /administracion@fia-geoingenieria.com'),
(113, 'NEVADOS PERÚ SOCIEDAD ANONIMA CERRADA-NEVADOS PERÚ S.A.C.', '20536039717', 'AV. SANTA ANA MZA. . LOTE. 60A URB. CHACRA CERRO LIMA - LIMA - COMAS', '', '', 1, 'ventas2@nevadosperu.com'),
(114, 'EMPRESA DE TRANSPORTES PAREDES RECINES S.A.', '20536124404', 'CAL.LOS CONDORES MZA. D LOTE. 8 URB. EL CLUB LIMA (ENTRE LA AV. EL POLO Y CONDORES) LIMA - LIMA - LURIGANCHO', '', '', 1, 'dparedes@etparsa.com'),
(115, 'G&G GENERALSERVICE S.A.C.', '20536957449', 'AV. PACIFICO NRO. 467 URB. SANTA LUISA (CRUCE HAYA DE LA TORRE CON PACIFICO) PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - LA PERLA', '', '', 1, 'Division@gygseguridad.com.pe'),
(116, 'GRUPO CAHEMA S.A.C.', '20536973215', 'AV. ANGAMOS ESTE NRO. 2130 URB. REDUCTO (FRENTE A PLAZA VEA) LIMA - LIMA - SURQUILLO', '', '', 1, 'cotizaciones@grupocachema.com'),
(117, 'DIMERC PERU S.A.C.', '20537321190', 'AV. ANDRES AVELINO CACERES NRO. 320 (CRUCE CON IRRIBARREN) LIMA - LIMA - MIRAFLORES', '', '', 1, 'elke.carlin@dimerc.pe'),
(118, 'CARISO CORPORACIONES S.A.C', '20538137716', 'AV. UCV 03 LOTE 55 ZONA A MZA. 03 LOTE. 55 A.H. HUAYCAN LIMA - LIMA - ATE', '', '', 1, 'ventas1@sagama-industrial.com'),
(119, 'OMEGA POWER S.A.C.', '20538549071', 'AV. ELMER FAUCETT NRO. 2293 (CRUCE COLONIAL CON FAUCETT) PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - BELLAVISTA', '', '', 1, 'ingenieria2@omegapower.com.pe'),
(120, 'CLINICA RAMAZINNI S.A.C.', '20542189747', '', '', '', 1, 'gestioncomercial@clinicaramazinni.com'),
(121, 'INVERSIONES COSDA E.I.R.L.', '20543217752', 'AV. 02 MZA. S LOTE. 39 A.V. LOS CLAVELES DE LURIN LIMA - LIMA - LURIN', '', '', 1, 'inversiones.cosda@gmail.com'),
(122, 'L & C FABRICACION SERVICIOS Y COMERCIALIZACION S.A.C', '20543736784', 'MZA. 69 LOTE. 11A P.J. SANTA ROSA II (COLEGIO AUGUSTO B LEGUIA) - PUENTE PIEDRA', '', '', 1, ''),
(123, 'CALCUM CORP E.I.R.L.', '20543880630', 'JR. CORONEL CAMILO CARRILLO NRO. 144 INT. 303 (ALTURA CDRA 8 AV. ARENALES) LIMA - LIMA - JESUS MARIA', '', '', 1, 'calcum2@gmail.com'),
(124, 'ANDECO ANDAMIOS DE CONSTRUCCION S.A.C', '20544364325', 'AV. PROCERES DE INDEPENDENCIA NRO. 2754 URB. SAN CARLOS LIMA - LIMA - SAN JUAN DE LURIGANCHO', '', '', 1, 'andecosac@gmail.com'),
(125, 'SOCIEDAD DISTRIBUIDORA FERRETERA S.A.C.', '20544998413', 'AV. LOS ALISOS MZA. J LOTE. 4 URB. LOS JARDINES DE NARANJAL ETP II (ENTRE AV. CANTA CALLAO Y AV. LOS ALISOS) SAN MARTIN DE PORRES', '', '', 1, 'v.valverde@sodiferperu.com'),
(126, 'CORPORACION LYCKAD S.A.C.', '20545155452', 'AV. JAVIER PRADO ESTE NRO. 3579 URB. JAVIER PRADO (ALT. TREBOL DE JAVIER PRADO) LIMA - LIMA - SAN BORJA', '', '', 1, 'gerencia@corporacionlyckad.com'),
(127, 'DJJ SOLUCIONES INTEGRALES S.A.C.', '20545190444', 'PJ. ELEANOR ROOSEVELT 110 URB. LA CALERA DE LA MERCED (ALT. CDRA 42 AV. AVIACION) SURQUILLO', '', '', 1, 'ventas@grupodjj.com'),
(128, 'CORPORACION Y MULTISERVICIOS HUMBERTO S.A.C.', '20546677959', 'MZA. B4 LOTE. E FND. SAN PEDRO DE LURIN - LIMA - LURIN', '', '', 1, ''),
(129, 'DISTVE SERVICIOS GENERALES E.I.R.L.', '20546734855', 'MZA. X LOTE. 39 URB. URB PACHACAMAC (GRUPO G PARCELA 3C) LIMA - LIMA - VILLA EL SALVADOR', '', '', 1, 'luisfalcon.distve@hotmail.com'),
(130, 'CRIOGENICA JCB SERVICE S.A.C.', '20547958072', 'CAL.RIO CAPLINA NRO. 149 URB. SANTA ISOLINA (AV MEXICO Y AV UNIVERSITARIA) LIMA - LIMA - COMAS', '', '', 1, 'criogenicajcb@gmail.com'),
(131, 'POLIMIX CONCRETO PERU S.A.C.', '20548362360', 'CAL.CALLE N 5 MZA. T LOTE. 2,3 URB. LAS VERTIENTES (POR LA AVENIDA EL SOL) LIMA - LIMA - VILLA EL SALVADOR', '', '', 1, 'henry@polimix.com.pe'),
(132, 'KAERCHER PERU S.A.', '20548502633', 'AV. REPUBLICA DE PANAMA NRO. 6641 URB. EL DORAL LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'ventadirecta1@ventaskarcher.pe'),
(133, 'IP CORPORATION EMPRESA INDIVIDUAL DE RESPONSABILIDAD LIMITADA - IP CORPORATION E.I.R.L.', '20548806004', 'AV. MARIANO CONDORCAMQUI MZA. O LOTE. 10 URB. EDWIN VASQUEZ (PARADERO LA FRONTERA) LIMA - LIMA - CARABAYLLO', '', '', 1, 'ip_corporation@hotmail.com'),
(134, 'INTEGRAMEDICA PERU S.A.C.', '20549045848', 'AV. JAVIER PRADO ESTE NRO. 1178 URB. CORPAC LIMA - LIMA - SAN ISIDRO', '', '', 1, 'recepcion.so@integramedica.pe'),
(135, 'INDUSTRIAL MEGA S.A.C.', '20549299785', 'MZA. H LOTE. 18 ASOCIACION SAN AGUSTIN (PARADERO ROSA LUZ) LIMA - LIMA - PUENTE PIEDRA', '', '', 1, 'comercial@imega.pe'),
(136, 'GEOFAL S.A.C.', '20549356762', 'AV. RIO MARAÑON NRO. 763 A.V. PEREGRINOS DEL SEÑOR LIMA - LIMA - LOS OLIVOS', '', '', 1, 'asesorcomercial@geofal.com.pe'),
(137, 'G Y G ILUMINACION INDUSTRIAL E.I.R.L.', '20549487271', 'JR. LAMPA NRO. 1125 INT. 7 CERCADO DE LIMA LIMA - LIMA - LIMA', '', '', 1, 'ventas@gygiluminacion.com'),
(138, 'COMERCIALIZADORA MULTINACIONAL (PERU) S.A.C.', '20551473881', 'AV. DEL PARQUE NRO. 172 URB. LIMATAMBO LIMA - LIMA - SAN ISIDRO', '', '', 1, 'comulsa@comulsa.pe'),
(139, 'COMPAÑIA MAGRA S.A.C.', '20551613217', 'MZA. K LOTE. 138 URB. LEONCIO PRADO OESTE (KM.34 PANAM. NORTE PARADERO FUNDICION) LIMA - LIMA - PUENTE PIEDRA', '', '', 1, 'ventas1@magrasac.com'),
(140, 'BLZ INGENIEROS S.A.C.', '20551858911', 'AV. SANTIAGO ANTUNEZ D MAYOLO NRO. 269 URB. LAS TORRES DE SAN BORJA LIMA - LIMA - SAN BORJA', '', '', 1, 'nzaconetta.blzingenieros@gmail.com'),
(141, 'SUPPLY CHAIN TECHNOLOGIES S.A.C.', '20552159102', 'AV. ALFREDO BENAVIDES NRO. 1850 INT. 601 URB. EL ROSEDAL LIMA - LIMA - MIRAFLORES', '', '', 1, 'ahuamani@hardrental.com'),
(142, 'PROSELET S.A.C', '20552868502', 'JR. CUTERVO NRO. 1747 URB. CHACRA RÍOS NORTE (CRUCE AV. VENEZUELA CON AV. TINGO MARÍA) LIMA - LIMA - LIMA', '', '', 1, 'ventas1@proselet.com.pe'),
(143, 'VALVULAS & AFINES SAC', '20553497117', 'MZA. B LOTE. 09 URB. INDUSTRIAL GRIMANESA - CALLAO', '', '', 1, 'ventas4@valvulasafines.com'),
(144, 'IMPORTACION Y SERVICIOS ANDREITA E.I.R.L.', '20553712531', 'MZA. H1 LOTE. 12 URB. PORTALES DE JAVIER PRADO (GRIFO URB VISTA ALEGRE -4TA ETAPA) LIMA - LIMA - ATE', '', '', 1, 'INFORMES@TRANSPORTESANDREITA.COM'),
(145, 'MEDICINA EMPRESARIAL DE PREVENCION EN SALUD OCUPACIONAL S.A.C.', '20555113731', 'AV. JOSE FAUSTINO SANCHEZ CARRION NRO. 154 URB. PERSHING (NRO 156) LIMA - LIMA - MAGDALENA DEL MAR', '', '', 1, 'ejecutivocomercial3@mepso.com.pe'),
(146, 'MONTANO GROUP S.A.C.', '20555971380', 'AV. DOMINICOS MZ. C LT. 3 ASC. PROPIETARIOS PRADERAS DEL SOL 2DA ETAPA (A 2 CUADRAS DE LA AV STA ROSA) - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'info@montano.pe'),
(147, 'MIDOT PERU SAC', '20556023019', 'CAL.ESQUILACHE NRO. 371 INT. 601 URB. FUNDO CONDE DE SAN ISIDRO LIMA - LIMA - SAN ISIDRO', '', '', 1, 'kristy.mory@gmail.com'),
(148, 'CDA INGENIEROS DEL PERU SAC', '20556084662', 'AV. TAMBO RIO MZA. G LOTE. 17 INT. B1 FND. CHACRA CERRO (CRUCE AV. CHILLON / AV. TAMBO RIO) LIMA - LIMA - COMAS', '', '', 1, 'ventas@cda-ingenieros.com'),
(149, 'SERVICIOS INDUSTRIALES PFUÑER EIRL', '20556444545', 'CAL.LOS TALADROS NRO. 271 URB. INDUSTRIAL NARANJAL (ALTURA ESTACION NARANJAL) LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'sipfuner@live.com'),
(150, 'LENOR PERU S.A.C.', '20557090259', 'JR. LUIS CARRANZA 2157 MZA. G LOTE. 7 URB. INDUSTRIAL CONDE LIMA - LIMA - LIMA', '', '', 1, 'jackson.cardenas@lenorgroup.com'),
(151, 'COMPAÑIA ECOLOGICA Y MEDIO AMBIENTE PERU S.A.C', '20557569881', 'AV. HUAROCHIRI MZA. L LOTE. 13 URB. LA PORTADA DE CERES - LIMA - SANTA ANITA', '', '', 1, 'ventas@cemap.pe'),
(152, 'DQ INGENIERIA CONSTRUCCIONES INDUSTRIALES S.A.C.', '20557860119', 'JR. LAS AZUCENAS NRO. 3870 INT. 301 URB. COVIDA (ALT. CUADRA 10 DE AV CARLOS IZAGUIRRE) LIMA - LIMA - LOS OLIVOS', '', '', 1, 'rodriguez@dqingenieria.com.pe'),
(153, 'ATS CONSULTORES Y ASESORES E.I.R.L.', '20565276566', 'JR. YURUA NRO. 442 DPTO. 204 URB. CHACRA COLORADA LIMA - LIMA - BREÑA', '', '', 1, 'ats.monitoreos@gmail.com'),
(154, 'CORPORACION M & E SERVIS S.A.C.', '20565328973', 'MZA. Z1 LOTE. 5A A.H. JOSEFINA RAMOS VDA DE GONZALES PRADA LIMA - CAÑETE - IMPERIAL', '', '', 1, ''),
(155, 'SAFETY LEADERS E.I.R.L.', '20565795911', 'AV. ALFREDO MENDIOLA NRO. 6821 DPTO. 304 INT. W (CONDOMINIO LAS TORRES DE LOS OLIVOS) LIMA - LIMA - LOS OLIVOS', '', '', 1, 'lbenites@safetyleaders.pe /jrosas@safetyleaders.pe'),
(156, 'MUEBLES LEOMAR EIRL', '20566077656', 'AV. ANGAMOS ESTE 2DO. PISO C.C. PLAZA HOGAR 1551 INT P07 SURQUILLO - LIMA - LIMA', '', '', 1, 'sillasymuebles.venta@gmail.com'),
(157, 'ARGOS SOLUCIONES B Y C S.A.C.', '20569249903', 'AV. LAS PLEYADES MZA. T1 LOTE. 9C URB. LA CAMPIÑA LIMA - LIMA - CHORRILLOS', '', '', 1, 'ventascorp@argossoluciones.com'),
(158, 'MADERERA Y SERVICIOS ALCANTARA E.I.R.L.', '20600139771', 'AV. NICOLAS AYLLON KM. 16.3 MZA. A LOTE. 10 URB. PARIACHI LIMA - LIMA - ATE', '', '', 1, ''),
(159, 'JP FABRICACIONES METALICAS S.A.C.', '20600204778', 'AV. NARANJAL MZA. B LOTE. 1 A.H. 19 DE MAYO LIMA - LIMA - LOS OLIVOS', '', '', 1, 'pmorales@jpfamet.com.pe'),
(160, 'VC SECURITY S.A.C.', '20600277945', 'AV. INTIHUATANA NRO. 843 URB. RESIDENCIAL HIGUERETA LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'njara@vcsafety.com'),
(161, 'MP RECICLA S.A.C.', '20600289544', 'TRO.PARCELA NRO. 48 INT. 01 C.C. SANTA ROSA DE COLLANAC - SUB LT 01 (I.E. 7261 SANTA ROSA DE COLLANAC) LIMA - LIMA - CIENEGUILLA', '', '', 1, 'mmori@ciclo.com.pe'),
(162, 'IMAB SOLUTIONS S.A.C.', '20600300378', 'AV. LOS FORESTALES MZA. I 1 LOTE. 7 B COO. LAS VERTIENTES (PUERTA 4 -CRUCE AV. EL SOL Y FORESTALES) LIMA - LIMA - VILLA EL SALVADOR', '', '', 1, 'administracion1@imabsolutions.com'),
(163, 'BUSINESS & CRANES OF PERU S.A.C', '20600377524', '9NO SECTOR MZA. C LOTE. 04 URB. PRO IND LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, ''),
(164, 'ZINC POWER S.A.', '20600626567', 'AV. CENTRAL MZA. 42 LOTE. C URB. PARQUE INDUSTRIAL DE PORCINOS PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - VENTANILLA', '', '', 1, 'ventas@zincpowerperu.com'),
(165, 'GLOBAL ELECTRIC & SOLAR PANEL\'S E.I.R.L. - G.E.S. E.I.R.L.', '20600667140', 'AV. GUILLERMO DANSEY NRO. 417 INT. 1044 URB. LIMA INDUSTRIAL LIMA - LIMA - LIMA', '', '', 1, 'ventas@globalelectricsolar.com.pe'),
(166, 'O&P SERVICIOS S.A.C.', '20600668707', 'CAL.3 MZA. C LOTE. 26A P. V CABO AZUL LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'apalomino@oypservicios.com'),
(167, 'SIMAP INGENIERIA PREVENTIVA Y PROYECTOS S.A.C. - INGENIERIA PREVENTIVA Y PROYECTOS S.A.C.', '20600795652', 'CAL.LUIS DEXTRE ECHAIZ MZA. V LOTE. 19 URB. RESID. HONOR Y LEALTAD LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'asesorcomercial1@ingenieriapreventiva.pe'),
(168, 'COMERCIALIZACION DISTRIBUCION Y SERVICIOS GENERALES J & M S.A.C.', '20600892640', 'CAL.LOS CARDOS MZA. P LOTE. 11 A.H. PANDO IX ET. LIMA - LIMA - SAN MIGUEL', '', '', 1, 'codiserg@gmail.com'),
(169, 'BLINDER PERU S.A.C.', '20600945298', 'CAL.SAN HERNAN NRO. 224 INT. 104 URB. SANTA LUISA ET. DOS LIMA - LIMA - LOS OLIVOS', '', '', 1, 'ventas@blinderperu.com'),
(170, 'SAFETY & CONTROL S.A.C.', '20601031303', 'CAL.ALAMEDA DON AGUSTIN MZA. J-1 LOTE. 7-B URB. HUERTOS DE VILLA (CRUCE AVENIDA ALAMEDA MARQUEZ DE LA BULA) LIMA - LIMA - CHORRILLOS', '01-3596402', '', 1, 'capacitaciones@safetycontrolperu.com /inducciones@safetycontrolperu.com'),
(171, 'ALD AUTOMOTIVE PERU S.A.C.', '20601129826', 'AV. 28 DE JULIO NRO. 1011 INT. 701 URB. SAN ANTONIO LIMA - LIMA - MIRAFLORES', '', '', 1, 'eduardo.habich@ayvens.com'),
(172, 'CMK ALQUILERES ASOCIADOS S.A.C.', '20601204933', 'AV. UNIVERSITARIA NRO. 4939 URB. PARQUE DEL NARANJAL ET. DOS LIMA - LIMA - LOS OLIVOS', '', '', 1, 'kenedy9314@hotmail.com'),
(173, 'METROLOGIA E INSTRUMENTACION INDUSTRIAL S.A.C.', '20601338204', 'CAL.LOS JAZMINES MZA. G LOTE. 13 COO. TALAVERA DE LA REYNA LIMA - LIMA - EL AGUSTINO', '', '', 1, 'ventas@metrindust.com.pe'),
(174, 'EAGLES SAFETY E.I.R.L.', '20601421420', 'CAL.LOS TORNOS NRO. 271 URB. NARANJAL (ESTACION NARANJAL) LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'ventas@es.com.pe /  rmanrique@es.com.pe'),
(175, 'ECOGLOBO S.A.C.', '20601452651', 'MZA. A LT. 10 OTR. SECCION 1 - PARCELACION SEMI RUSTICA - CANTO GRANDE - SAN JUAN DE LURIGANCHO', '', '', 1, 'betsy@ecoglobo.com.pe'),
(176, 'TE INGENIERIA ESPECIALIZADA S.A.C.', '20601923336', 'AV. EL PARQUE SUR NRO. 770 DPTO. 403 LIMA - LIMA - SAN BORJA', '', '', 1, 'luciano.martinez@teie.com.pe'),
(177, 'CONSUL ESPINOZA INGENIEROS S.A.C.', '20601969981', 'ERATO NRO. 148 (CDR 12 DE AV. QUECHUAS) LIMA - LIMA - ATE', '', '', 1, 'consulespinoza@gmail.com'),
(178, 'ACABADOS BRYGS S.A.C.', '20602203264', 'AV. ALFREDO MENDIOLA NRO. 1049 URB. PALAO 1RA ET. LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'acbrigs@hotmail.com'),
(179, 'JYVAE E.I.R.L.', '20602354807', 'MZA. N LOTE. 16 A.H. 19 DE MAYO (CRUCE AV.UNIVERSITARIA CON AV.NARANJAL) LIMA - LIMA - LOS OLIVOS', '', '', 1, 'rosa.jyvae@gmail.com'),
(180, 'PROMEL AR S.A.C.', '20602552056', 'AV. GUILLERMO DANSEY NRO. 458 INT. 6 PSTO. BA-14 C.C. NICOLINI LIMA - LIMA', '', '', 1, 'ventas@promelarsac.com'),
(181, 'COMPAÑIA GENERAL DE MANTENIMIENTO INDUSTRIAL S.A.C. - CGMI S.A.C.', '20602717951', 'MZA. D LOTE. 9D APV. SUCRE , CALLE B (FRENTA AL PARQUE SUCRE) LIMA - LIMA - SANTA ANITA', '', '', 1, 'ingenieria@cgmi.pe'),
(182, 'MULTISERVICIOS TAMBRA S.A.C.', '20602770517', 'CAL.CARLOS TENAUD NRO. 175 URB. BARBONCITO LIMA - LIMA - MIRAFLORES', '', '', 1, 'luisantoniotambini@hotmail.com'),
(183, 'MACRO SECURITY SOLUTIONS S.A.C.', '20602776884', 'CAL.MONTE GRANDE NRO. 120 INT. 218 URB. CHACARILLA DEL ESTANQUE (CENTRO COMERCIAL GALAXY) LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'ventas@macrosolutionsac.com'),
(184, 'VIGILA SALUD E.I.R.L.', '20602900658', 'AV. GENERAL EUGENIO GARZON NRO. 1472 DPTO. 203 FND. OYAGUE LIMA - LIMA - JESUS MARIA', '', '', 1, 'gerenciamedica@vigisa.co'),
(185, 'ENERGIT TECHNOLOGY EIRL', '20602924654', 'JR. PARURO NRO. 1401 INT. 139 (A MEDIA CUADRA DE LA CAJA METROPOLITANA) LIMA - LIMA - LIMA', '', '', 1, 'Ventas@energitperu.com'),
(186, 'TOP EPP S.A.C.', '20602932703', 'JR. LAS AGUILAS NRO. 284 INT. 204 URB. SAN JUAQUIN (TORRE 8) PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - BELLAVISTA', '', '', 1, 'cotizar@topepp.com / ventas@topepp.com'),
(187, 'ALL IN ONE S.A.C.', '20602967698', 'CAL.LOS MERCADERES NRO. 306 URB. LAS GARDENIAS ET. UNO LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'allinoneperu@outlook.com'),
(188, 'GRUPO WIGAN S.A.C.', '20603066384', 'AV. SAN JUAN EVANGELISTA NRO. 575 A.H. ASCENSION HUANCAVELICA - HUANCAVELICA - ASCENSION', '', '', 1, 'ventas@grupowigan.pe'),
(189, 'ADRISCH EIRL', '20603196491', 'CAL.LOS ANALISTAS NRO. 139 DPTO. 301 (ENTRE CDRA 5 Y 6 DE CONSTRUCTORES) LIMA - LIMA - LA MOLINA', '', '', 1, 'ventas@adrischproducts.com'),
(190, 'PERU ELECTRIC LAB S.A.C.', '20603205678', 'AV. UNIVERSITARIA NRO. 1745 URB. SANTA EMMA LIMA - LIMA - LIMA', '', '', 1, 'asesorcomercial@peruelectriclabsac.com'),
(191, 'CONSULTORES DEL SUR G.L.A. E.I.R.L.', '20603252153', 'MZA. E LOTE. 08 A.H. PILAR NORES (FRENTE A LOZA DEPORTIVA DE MIGUEL GRAU) ICA - CHINCHA - PUEBLO NUEVO', '991 096 774', '', 1, 'Consultoresdelsurgla@gmail.com'),
(192, 'FC ARTE CREATIVIDAD & DISEÑO S.A.C.', '20603458657', 'JR. QUILCA NRO. 549 URB. PERU 5TA. ZONA LIMA - SAN MARTIN DE PORRES', '', '', 1, 'fc.creatividad@gmail.com'),
(193, 'ELEGANT CARS PERU E.I.R.L', '20603481161', 'JR. SAN AGUSTÍN NRO. 1032 (JR. SAN AGUSTÍN 1032, SURQUILLO) LIMA - LIMA - SURQUILLO', '', '', 1, ''),
(194, 'RAYEST EIRL', '20603590024', 'MZA. A LOTE. 7 URB. LA ALBORADA DE SANTA ROSA III ETAPA (ALT. CRUCE AV. PACASMAYO E IZAGUIRRE) LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'lailuminada.04@gmail.com'),
(195, 'PROSINFER EIRL', '20603671261', 'CAL.JOSE CELENDON NRO. 1072 URB. LIMA INDUSTRIAL LIMA - LIMA - LIMA', '', '', 1, 'ventas@prosinfer.com'),
(196, 'MADERA TRIPLAYERA & FERRETERIA ALCANTARA E.I.R.L.', '20604021384', 'CARRETERA CENTRAL - ALTURA GRIFO NEGRO KM. 16.5 MZA. E LOTE. 4 DPTO. 1 INT. 1 URB. PRADERAS DE PARICAHI (AL COSTADO DE EMMPRESA SHALOM)  ATE', '', '', 1, ''),
(197, 'LOSS PREVENTION ENGINEERING E.I.R.L.', '20604303975', 'CAL.LOS ALPES MZA. K LOTE. 13 URB. LOS HUERTOS DE LA MOLINA LIMA - LIMA - LA MOLINA', '', '', 1, 'ellamoca@lpe.pe'),
(198, 'ADORA PERU S.A.C.', '20604564574', 'CAL.LOS CLAVELES LOTE. 20 URB. LOS CEDROS LIMA - LIMA - SANTA ANITA', '', '', 1, 'ventas@adora.com.pe'),
(199, 'APUNTALH S.A.C.', '20604603731', 'JR. AGUAMARINA NRO. 288 APV. SAN HILARION LIMA - LIMA - SAN JUAN DE LURIGANCHO', '', '', 1, 'ventas@apuntalh.com'),
(200, 'HILTI PERU S.A', '20604767289', 'AV. JAVIER PRADO ESTE NRO. 499 INT. 1103 URB. GOLF LOS INKAS LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'JoseCarlos.Alvarado@hilti.com'),
(201, 'MEGA SERVIKAR S.A.C.', '20604789690', 'JR. JULIAN ALARCON NRO. 900 URB. EL ROSARIO LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'megaservikar@gmail.com'),
(202, 'SERVICIOS INTEGRALES Y TECNOLOGICOS DEL PERU S.A.C.', '20605211926', 'CAL.TENIENTE EDUARDO ASTETE MENDOZA NRO. 340 URB. CORPORACION EL AGUSTINO LIMA - LIMA - EL AGUSTINO', '', '', 1, 'julio.gamboa@sintec.com.pe'),
(203, 'GILMAR CEMENTO & ACERO CENTER E.I.R.L.', '20605437754', 'SECTOR PARIACHI MZA. I LOTE. 1 APV. FHILADELFIA DE ATE LIMA - LIMA - ATE', '', '', 1, ''),
(204, 'R & R SICE INGENIEROS S.A.C.', '20605861980', 'PS A MZA. 3I LOTE. 13 EL MIRADOR (AREA PROYECTO EL MIRADOR) PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - VENTANILLA', '', '', 1, 'sice.ryr@gmail.com'),
(205, 'LOGISTICAS W & P S.A.C.', '20605937722', 'CAL.INCA TOPARPA NRO. 182 URB. AGRUP RESIDENCIAL SALAMANCA DE MONTERRICO (A 3 CDRAS DE LA IGLESIA DE LOS QUECHUAS) LIMA - LIMA - ATE', '', '', 1, 'comercial@operacioneswp.com'),
(206, 'ELIMINA YA S.A.C', '20605937846', 'CAL.INCA TOPARPA NRO. 182 URB. AGRUP RESIDENCIAL SALAMANCA DE MONTERRICO (A 3 CDRAS DE LA IGLESIA LOS QUECHUAS) LIMA - LIMA - ATE', '', '', 1, 'comercial@operacioneswp.com'),
(207, 'HNOS. PALOMINO TOURS S.A.C.', '20606048549', 'CAL.ANGEL REVOREDO MZA. I LOTE. 09 ASC. JOSE ABELARDO QUIÑONES LIMA - LIMA - INDEPENDENCIA', '', '', 1, 'info@palomino.com.pe'),
(208, 'MULTISERVICIOS MVCC S.A.C.', '20606077786', 'AV. LOS GORRIONES NRO. 273 DPTO. 306B URB. LA CAMPIÑA ZONA DOS LIMA - LIMA - CHORRILLOS', '', '', 1, ''),
(209, 'ARENAS INGENIERIA AUTOMOTRIZ E.I.R.L.', '20606174510', 'MZA. A LOTE. 21 A.H. PANDO ET. NUEVE LIMA - LIMA - SAN MIGUEL', '', '', 1, ''),
(210, 'INGENIERIA, EDUCACION Y TECNOLOGIA GREENER S.A.C', '20606279991', 'JR. ARACENA NRO. 128 URB. RESIDENCIAL HIGUERETA LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'dsobrados@greenersac.com'),
(211, 'EXCELENCIA OPERACIONAL SAFETY S.A.C.', '20606328533', 'CAL.JORGE LIZARBE NRO. 176 URB. STELLA MARIS PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - BELLAVISTA', '', '', 1, 'rormeno@exopsafety.com.'),
(212, 'INSTITUTO DE CAPACITACIONES PROFESIONALES ELECTROTECH S.A.C', '20606365561', 'JR. MANUEL ASCENCIO SEGURA NRO. 142 INT. 304 URB. SANTA BEATRIZ (A MEDIA CUADRA DEL HOSPITAL REBAGLIATI) LIMA - LIMA - LINCE', '', '', 1, 'institutoelectrotechcursos@gmail.com'),
(213, 'RY SOFT E.I.R.L.', '20606627999', 'MZA. B LOTE. 20 ASC. SANTA ROSA PROV. CONST. DEL CALLAO - PROV. CONST. DEL CALLAO - VENTANILLA', '', '', 1, 'proyectos@gruporysoft.com'),
(214, 'CENTRO DE PREVENCION DE RIESGOS S.A.C.', '20606976063', 'MZA. E3 LOTE. 17 INT. C2 URB. MONSERRATE IV ETAPA LA LIBERTAD - TRUJILLO - TRUJILLO', '', '', 1, 'vburgos@cprperu.pe'),
(215, 'CORPORACION JEMAFE S.A.C.', '20607056006', 'MZA. X LOTE. 7 URB. VILLA CORPAC LIMA - LIMA - CARABAYLLO', '', '', 1, 'marco.perez2007@hotmail.com'),
(216, 'B&K ELECTRIC S.A.C.', '20607817457', 'MZA. B RES. LA LIBERTAD LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'administracion@bkelectric.com.pe'),
(217, 'CSI IMPORT S.A.C.', '20608057278', 'OTR.CALLE 25 MZA. B7 LOTE. 16 URB. RESIDENCIAL SANTA ANITA LIMA - LIMA - SANTA ANITA', '', '', 1, ''),
(218, 'ANMA IMPORTACIONES E.I.R.L.', '20608315617', 'CAL.CALLE LUIS EZAINE NRO. 103 DPTO. 301 LIMA - LIMA - SAN BORJA', '', '', 1, 'ventas@anma.com.pe'),
(219, 'GRUPO DAMBOYS S.R.L.', '20608439081', 'CAL.LOS TORNOS NRO. 134 URB. NARANJAL INDUSTRIAL (PABELLON A1-A4) LIMA - LIMA - INDEPENDENCIA', '', '', 1, 'grupodamboys@gmail.com'),
(220, 'MAPSOE S.A.C.', '20608451537', 'AV. PROCERES DE HUANDOY MZA. 72 LOTE. 57 (A.H. ENRIQUE MILLA OCHOA - LOS OLIVOS) LIMA - LIMA - LOS OLIVOS', '', '', 1, 'jose.bendrell@mapsoe.com'),
(221, 'REDES Y CONTROLES INDUSTRIALES S.A.C.', '20608504541', 'AV. ARGENTINA NRO. 308 INT. 102 URB. LIMA INDUSTRIAL LIMA - LIMA - LIMA', '', '', 1, 'proyectos@redcoind.pe'),
(222, 'UNACEM PERU S.A.', '20608552171', 'AV. ATOCONGO NRO. 2440 (CARCA AL CAMPAMENTO DE UNACEM) LIMA - LIMA - VILLA MARIA DEL TRIUNFO', '', '', 1, 'madeleine.flores@unacem.pe'),
(223, 'CORPORACION PG & M E.I.R.L.', '20608585169', 'JR. RAMON CARCAMO NRO. 428 URB. LIMA INDUSTRIAL (CALLE 14 TIENDA E-230) LIMA - LIMA - LIMA', '', '', 1, 'corp.exse.se@gmail.com'),
(224, 'MORE LOGISTICS SOLUTIONS S.A.C.', '20608613863', 'CAL.TEJADA NRO. 311 DPTO. 402 INT. B URB. TEJADA ALTA LIMA - LIMA - BARRANCO', '', '', 1, ''),
(225, 'DISTRIBUIDORA E IMPORTADORA DE VIDRIOS Y ACCESORIOS HUARAYA S.A.C.', '20608881256', 'CAL.LAS ORQUIDEAS MZA. D LOTE. 3 URB. RESIDENCIAL VILLA GENOVA (AV. NARANJAL CON AV. CENTRAL) LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'ventas@huaraya.com.pe'),
(226, 'MULTISERVICIOS GENERALES PAULA ESPINOZA S.A.C.', '20608967843', 'MZA. E LOTE. 32 DPTO. 101 URB. VILLA CLUB 5 (ALTURA DE LA PRIMERA DE SAN BENITO) LIMA - LIMA - CARABAYLLO', '', '', 1, 'paulaespinoza152@gmail.com'),
(227, 'MARTILLITO E.I.R.L.', '20609076161', 'MZA. I LT. 13 COOP. DE VIVIENDA LA FLORESTA (CC. CENTRAL KM 17.5 PARADERO HUASCATA) - CHACLACAYO', '', '', 1, ''),
(228, 'BRONCERIA LEONARDO E.I.R.L.', '20609123479', 'CAL.CERRO SAN FRANCISCO NRO. 693 URB. SANTOYO LIMA - LIMA - EL AGUSTINO', '', '', 1, 'broncerialeonardo@gmail.com // ventas@metalesleonardo.com'),
(229, 'CORPORACION CONSUELO S.A.C.', '20609147955', 'AV. SAN JOSE MZA. C LOTE. 13 URB. LAS ORQUIDEAS (COLEGIO VICTOR MORON) LIMA - LIMA - SAN BARTOLO', '', '', 1, 'CORPORACIONCONSUELO@GMAIL.COM'),
(230, 'C & T MAQUINARIAS S.A.C.', '20609161044', 'AV. IMPERIAL MZA. S LOTE. 04 AGRU HACIA EL DESAROLLO LIMA - LIMA - SAN JUAN DE LURIGANCHO', '', '', 1, 'Maquinariassanchezsac@hotmail.com'),
(231, 'VENTIPLUS S.A.C.', '20609175291', 'CAL.LOMA BELLA NRO. 217 DPTO. 101 URB. PROLONGACION BENAVIDES LIMA - LIMA - SANTIAGO DE SURCO', '', '', 1, 'ventas@ventiplus.pe'),
(232, 'CORPORACION FERRETERA KRJ E.I.R.L.', '20609364638', 'SAN PEDRO DE LURIN MZA. B4 LOTE. G LIMA - LIMA - LURIN', '', '', 1, 'corporacion.krj@hotmail.com'),
(233, 'GLOBAL ENERGY PERU S.A.C.', '20609380803', 'AV. ARGENTINA NRO. 344 INT. N72 LIMA - LIMA - LIMA', '', '', 1, 'globalenergyper@gmail.com'),
(234, 'C & C RAMOS S.A.C.', '20609654555', 'AV. CIRCUNVALACION BAJA KM. 01 MZA. 01 LOTE. 01 DPTO. 01 INT. 01 BAR. EL CARMEN (EL GRIFO RAMOS) ANCASH - HUARI - HUARI', '', '', 1, 'cycramos.sac@gmail.com'),
(235, 'TERRASENSOR S.A.C.', '20609688964', 'CAL.FRANCISCO DE ZELA NRO. 2526 URB. FRANCISCO DE ZELA LIMA - LIMA - LINCE', '', '', 1, 'ventas@terrasensor.com.pe'),
(236, 'CONSORCIO FERRETERO ENSA S.A.C.', '20609767066', 'OTR.ANTIGUA PANAMERICANA SUR MZA. Z LOTE. 19 URB. SAN JOSE LIMA - LIMA - SAN BARTOLO', '', '', 1, '921840428 / ferreteriaensa2022@gmail.com'),
(237, 'BUILDING SYSTEMS PERU S.A.C.', '20609793806', 'NRO. 165 URB. MIRAMAR (PASAJE 1) LIMA - LIMA - SAN MIGUEL', '', '', 1, 'helen.r@bsperu.pe'),
(238, 'GRAMA VIAS CONSTRUCTOR E.I.R.L.', '20610110895', 'AV. ANGELICA GAMARRA MZA. Q LOTE. 04 URB. SANTA ROSA LIMA - LIMA - LOS OLIVOS', '', '', 1, 'F.e2018@hotmail.com /gramavias2022@hotmail.com'),
(239, 'DIGITALGRAF E.I.R.L.', '20610162615', 'JR. HUARAZ NRO. 064 INT. 202 URB. HUERTA LA VIRREYNA LIMA - LIMA - BREÑA', '', '', 1, 'digital2061@gmail.com'),
(240, 'DATATERRA PERU S.A.C.', '20610225021', 'JR. SANDIA NRO. 491 CERCADO DE LIMA LIMA - LIMA - LIMA', '', '', 1, 'vcastilla@dataterraperu.com'),
(241, 'FDX INDUSTRIAL EQUIPMENT S.A.C.', '20610645560', 'CAL.NAVARRA NRO. 255 URB. MAYORAZGO ET. TRES LIMA - LIMA - ATE', '', '', 1, 'ximena.trujillo@fdxrentacar.com'),
(242, 'SERVISAFE S.A.C.', '20610941991', 'JR. LOS TULIPANES MZA. F2 LOTE. 6 ASC. JARDINES DE CHILLON LIMA - LIMA - PUENTE PIEDRA', '', '', 1, 'ventas.servisafe@gmail.com'),
(243, 'CORPORACION ALAYO GRUOP S.A.C.', '20611155671', 'CAL.LOSAUCES KM. 28 MZA. D LOTE. 18 DPTO. 2 INT. 2 URB. LOS PINOS LIMA - LIMA - PUENTE PIEDRA', '', '', 1, 'ventas1@corpala.com'),
(244, 'BEK INVERSIONES SAC', '20611225262', 'CAL.VIZCARDO Y GUZMAN MZA. E LOTE. 3 URB. LA MADRID LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'ventas@bekinversiones.com'),
(245, 'CONCRELABMIX S.A.C.', '20611265337', 'CAL.SAN ANTONIO MZA. A LOTE. 1 URB. SAN ANTONIO LIMA - LIMA - PUENTE PIEDRA', '', '', 1, 'proyectos@hazgroup.org'),
(246, 'MULTISERVICIOS COMPUTEC S.A.C.', '20611781602', 'AV. INCA GARCILASO DE LA VEGA NRO. 1108 CERCADO DE LIMA', '', '', 1, 'ventas@inversionescomputec.com'),
(247, 'ANDAMIOS Y ENCOFRADOS TECON E.I.R.L.', '20612595128', 'AV. FRANCISCO BOLOGNESI MZA. BY1 LOTE. 4 LIMA - LIMA - SAN JUAN DE LURIGANCHO', '', '', 1, 'ventas@apuntalh.com'),
(248, 'BOLSAS IMPRESAS CRISTIPLAS S.A.C.', '20613107429', 'MZA. I LOTE. 20 A.H. MICAELA BASTIDAS SC. UNO LIMA - LIMA - ATE', '', '', 1, 'Industrias.Cristiplas@GMAIL.COM'),
(249, 'XELKY GRUPO S.A.C.', '20613140108', 'CAL.RIO PASTAZA NRO. 120 URB. SANTA ISOLINA ET. UNO LIMA - LIMA - COMAS', '', '', 1, 'xelkygrupo@gmail.com'),
(250, 'EMPRESA DE TRANSPORTES Y SERVICIOS SALCOL S.A.C.', '20613370324', 'MZA. F LOTE. 5 APV. TUNGASUCA (3ERA ETAPA - FRENTE AL REST RANCHO GRAND) LIMA - LIMA - COMAS', '', '', 1, 'kelly.colquichagua.vargas@gmail.com'),
(251, 'MADERAS Y TRIPLAY S.A.C.', '20613445413', 'CAL.VICTOR MAURTUA NRO. 131 INT. 303 URB. SANTA ISABEL LIMA - LIMA - SAN ISIDRO', '01326314', '', 1, 'ventas@matrisa.pe    9'),
(252, 'LOGANELECTRIC PERU E.I.R.L.', '20613753592', 'AV. REPUBLICA DE ARGENTINA NRO. 327 URB. LIMA INDUSTRIAL (CENTRO COMERCIAL LA BELLOTA) LIMA', '', '', 1, 'loganferreteriaelectric@gmail.com'),
(253, 'RESIDUOS SOLIDOS RECOLECTOR JM E.I.R.L', '20614034085', 'AV. JUVENAL VILLAVERDE NRO. 203 DPTO. B URB. SAN GERMAN (ALTURA DE LA CUADRA 6AV. GERMAN AGUIRRE) LIMA - LIMA - SAN MARTIN DE PORRES', '', '', 1, 'mservikar85@gmail.com'),
(254, 'SEÑALIZA E.I.R.L.', '20614132711', 'MLC.RIMAC NRO. 3403 URB. PERU (ALTURA CUADRA 34 AV PERU) - SAN MARTIN DE PORRES', '3180316', '', 1, 'servige15@hotmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor_cuenta`
--

CREATE TABLE `proveedor_cuenta` (
  `id_proveedor_cuenta` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_banco` int(11) DEFAULT NULL,
  `banco_proveedor` varchar(100) DEFAULT NULL,
  `id_moneda` int(11) DEFAULT NULL,
  `nro_cuenta_corriente` varchar(50) DEFAULT NULL,
  `nro_cuenta_interbancaria` varchar(50) DEFAULT NULL,
  `est_proveedor_cuenta` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor_cuenta`
--

INSERT INTO `proveedor_cuenta` (`id_proveedor_cuenta`, `id_proveedor`, `id_banco`, `banco_proveedor`, `id_moneda`, `nro_cuenta_corriente`, `nro_cuenta_interbancaria`, `est_proveedor_cuenta`) VALUES
(1, 1, 1, 'INTERBANK', 1, '4013163261190', '003-401013163261190-74', 1),
(2, 2, 2, 'BANCO CONTINENTAL BBVA', 1, '011 185 000200152163 68', '', 1),
(3, 3, 3, 'BANCO CREDITO BCP', 1, '19175720569021', '00219117572056902151.', 1),
(4, 4, 3, 'BANCO CREDITO BCP', 1, '19113107036029', '', 1),
(5, 5, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0921-0200240704', '011-921-000200240704-43', 1),
(6, 6, 3, 'BANCO CREDITO BCP', 1, '191-30644591-0-30', '00219113064459103050', 1),
(7, 6, 4, 'BANCO NACION', 1, '00-066-087514', '', 1),
(8, 7, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0814-0210667739', '01181400021066773912', 1),
(9, 8, 3, 'BANCO CREDITO BCP', 1, '255-31682277035', '0022 551316822770 3589', 1),
(10, 9, 3, 'BANCO CREDITO BCP', 1, '25540308829024', '00225514030882902487', 1),
(11, 10, 3, 'BANCO CREDITO BCP', 1, '255-4048501005', '00225500404850100588', 1),
(12, 11, 3, 'BANCO CREDITO BCP', 1, '255100036546061', '00225511003654606184', 1),
(13, 12, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0873-0200022650', '011-873-000200022650-72', 1),
(14, 13, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0102-0200722412', '', 1),
(15, 14, 1, 'INTERBANK', 1, '003-155-01337-1880078-91', '', 1),
(16, 15, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0842-0200159223', '', 1),
(17, 16, 3, 'BANCO CREDITO BCP', 1, '19135785805076', '00219113578580507651', 1),
(18, 17, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0312-0200063941', '', 1),
(19, 18, 3, 'BANCO CREDITO BCP', 1, '19475086192036', '00219417508619203690', 1),
(20, 19, 1, 'INTERBANK', 1, '242 3251347455', '00324201325134745552', 1),
(21, 20, 4, 'BANCO NACION', 1, '041-015-511384', '018 000 004015511384 04', 1),
(22, 20, 4, 'BANCO NACION', 1, '00-006-080863', '018 006 00000608086371', 1),
(23, 21, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0101-0200256475', '', 1),
(24, 22, 3, 'BANCO CREDITO BCP', 1, '1919-5902975089', '002-191-19590297508-9-56', 1),
(25, 23, 3, 'BANCO CREDITO BCP', 1, '1921766211014', '00219200176621101431', 1),
(26, 24, 3, 'BANCO CREDITO BCP', 2, '19107310839185', '00219110731083918551', 1),
(27, 24, 1, 'INTERBANK', 2, '643 3148396954', '00364301314839695442', 1),
(28, 24, 3, 'BANCO CREDITO BCP', 1, '19170082274074', '00219117008227407456', 1),
(29, 25, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-01450200223968', '011-145-000200223968-05', 1),
(30, 25, 3, 'BANCO CREDITO BCP', 1, '193-27737934013', '00219312773793401319', 1),
(31, 26, 2, 'BANCO CONTINENTAL BBVA', 1, '001101750200435977', '', 1),
(32, 27, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0270-0200568736', '01127000020056873669', 1),
(33, 28, 3, 'BANCO CREDITO BCP', 1, '19193017937010', '00219119301793701058', 1),
(34, 29, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0921-0200113907', '', 1),
(35, 30, 3, 'BANCO CREDITO BCP', 1, '19238686111-0-78', '00219213868611107833', 1),
(36, 31, 3, 'BANCO CREDITO BCP', 1, '194-04240038-0-66', '002-19410424003806694', 1),
(37, 32, 3, 'BANCO CREDITO BCP', 1, '25506415293029', '00225510641529302988', 1),
(38, 33, 3, 'BANCO CREDITO BCP', 1, '191-99509161-0-01', '002-19119950916100154', 1),
(39, 34, 3, 'BANCO CREDITO BCP', 1, '191-90780363-0-34', '00219119078036303457', 1),
(40, 35, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0174-0200854044', '01117400020085404401', 1),
(41, 36, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0814-0240493050', '01181400024049', 1),
(42, 37, 3, 'BANCO CREDITO BCP', 1, '19171279188077', '00219117127918807755', 1),
(43, 38, 3, 'BANCO CREDITO BCP', 1, '191-90685345-0-56', '00219119068534505654', 1),
(44, 39, 3, 'BANCO CREDITO BCP', 1, '19129172594065', '00219112917259406558', 1),
(45, 40, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0910-73-0100010582', '', 1),
(46, 40, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0910-72-0100016947', '', 1),
(47, 40, 5, 'CTA RECAUDADORA', 2, 'PAGO SERVICIOS/ EDIPESA ME/RUC CLIENTE', '', 1),
(48, 41, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0112-01-00010142', '', 1),
(49, 41, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0112-01-00010185', '', 1),
(50, 42, 3, 'BANCO CREDITO BCP', 1, '193-0253467-0-71', '002-193-000253467071-12', 1),
(51, 42, 3, 'BANCO CREDITO BCP', 2, '193-0666076-1-49', '02-193-000666076149-12', 1),
(52, 42, 6, 'SCOTIABANK', 1, '000-1626833', '009-223-000001626833-96', 1),
(53, 42, 6, 'SCOTIABANK', 1, '00-0866313', '009-285-000000866313-80', 1),
(54, 43, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-117-0100010824-99', '', 1),
(55, 43, 3, 'BANCO CREDITO BCP', 1, '194-0367542-0-44', '', 1),
(56, 44, 3, 'BANCO CREDITO BCP', 1, '1911189774017', '002-191001189774017 58', 1),
(57, 45, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0387-0100046569-88', '011-387-000100046569-', 1),
(58, 45, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0387-0100046542-85.', '011-387-000100046542-85', 1),
(59, 46, 2, 'BANCO CONTINENTAL BBVA', 2, '(0011) 0151-0100024073', '011-151-000100024073-87', 1),
(60, 46, 3, 'BANCO CREDITO BCP', 2, '192-0842358-1-75', '002-192-000842358175-33', 1),
(61, 46, 2, 'BANCO CONTINENTAL BBVA', 1, '00110151-0100024065', '011-151-000100024065-83', 1),
(62, 47, 3, 'BANCO CREDITO BCP', 2, '193-1832074-1-07', '00219300183207410719', 1),
(63, 48, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-6860100000513', '', 1),
(64, 48, 5, 'CTA RECAUDADORA', 1, 'PAGO SERVICIOS/ TIENDA DE MEJORAMIENTO DEL HOGAR', 'MN /RUC CLIENTE', 1),
(65, 49, 3, 'BANCO CREDITO BCP', 1, '194- 1535325-0-32', '00219400153532503295', 1),
(66, 50, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0280-0100002799', '0011-028-0100002799-53', 1),
(67, 51, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0185-0100037952', '011-185000100037952-67', 1),
(68, 52, 3, 'BANCO CREDITO BCP', 1, '191-1103348-0-28', '002-191-001103348-0-28-53', 1),
(69, 52, 3, 'BANCO CREDITO BCP', 2, '191-1097460-1-63', '002-191-001097460-1-63-54', 1),
(70, 53, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0153-0100070572-41', '0011-153-000100070572-41', 1),
(71, 54, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0686-01-00009014', '', 1),
(72, 54, 3, 'BANCO CREDITO BCP', 1, '191-0170520-0-23', '', 1),
(73, 55, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-122-0100005364-93', '011-122-000100005364-93', 1),
(74, 55, 3, 'BANCO CREDITO BCP', 2, '191-1774524-1-93', '002-191-001774524-1-93-5', 1),
(75, 56, 6, 'SCOTIABANK', 1, '000-1478133', '009-411-000001478133-00', 1),
(76, 57, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0167-0100011785', '', 1),
(77, 58, 3, 'BANCO CREDITO BCP', 2, '191-1017462-1-03', '00219100101746210356', 1),
(78, 59, 5, 'CTA RECAUDADORA', 1, '/pago transferencia//pago servicio//empresa divers', 'UNICON. Usuario: DNI / RUC  20550259321', 1),
(79, 59, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0686-01-00012619', '011-686-000100012619-31', 1),
(80, 59, 3, 'BANCO CREDITO BCP', 1, '193-0099308-0-09', '002-193-000099308-0-09-12', 1),
(81, 60, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0349-0100027540-82', '011-349-000100027540-82', 1),
(82, 61, 3, 'BANCO CREDITO BCP', 1, '191-1018259-0-43', '002-191-001018259043-59', 1),
(83, 62, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0139-0100014064', '', 1),
(84, 62, 3, 'BANCO CREDITO BCP', 1, '191-1694349-0-34', '00219100169434903458', 1),
(85, 63, 2, 'BANCO CONTINENTAL BBVA', 2, '0011 0358 0100002792', '011 358 000100002792 98', 1),
(86, 64, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-138-0100042712', '', 1),
(87, 65, 3, 'BANCO CREDITO BCP', 2, '193-1622886-1-96', '002-193-001622886-1-96-15', 1),
(88, 66, 3, 'BANCO CREDITO BCP', 2, '1931917852151', '002-193001717852151-14', 1),
(89, 66, 6, 'SCOTIABANK', 2, '000-3201582', '009-170-000003201582-28', 1),
(90, 67, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0183-01-00064584', '011-183-000-10006458414', 1),
(91, 67, 1, 'INTERBANK', 1, '052-3000286712', '003-052-003000286712-82', 1),
(92, 68, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0733-36-0100005447', '011-733-000100005447-36', 1),
(93, 69, 3, 'BANCO CREDITO BCP', 1, '191-2073346087', '0021-9100207334608755', 1),
(94, 70, 2, 'BANCO CONTINENTAL BBVA', 1, 'BBVA S/ :0011-0226-02-00254391 CCI : 011-226-00020', '', 1),
(95, 71, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0342-0100002825', '011-342-000100002825-36', 1),
(96, 71, 3, 'BANCO CREDITO BCP', 1, '191-1105539-0-59', '002-191-001105539059-52', 1),
(97, 72, 2, 'BANCO CONTINENTAL BBVA', 1, '0157-0100032427', '01115700010003242755 ', 1),
(98, 72, 2, 'BANCO CONTINENTAL BBVA', 2, '0157-0100032419', '01115700010003241952 ', 1),
(99, 73, 2, 'BANCO CONTINENTAL BBVA', 1, '011-107-0100024537', '011-107-000100024537-01', 1),
(100, 73, 2, 'BANCO CONTINENTAL BBVA', 2, '011-107-0100024103', '011-107-000100024103-02', 1),
(101, 74, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-02110200702651', '01121100020070265107', 1),
(102, 75, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0164-01-00046957', '01116400010004695715', 1),
(103, 75, 3, 'BANCO CREDITO BCP', 1, '191-1773539-0-33', '00219100177353903352', 1),
(104, 76, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0910-01-00118456', '', 1),
(105, 76, 3, 'BANCO CREDITO BCP', 1, '191-1458867-0-28', '', 1),
(106, 77, 6, 'SCOTIABANK', 1, '000-0215775', '009-055-000000215775-58', 1),
(107, 78, 1, 'INTERBANK', 1, '151-3002358250', '003-151-003002358250-82', 1),
(108, 78, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0814-0207575904', '011-814-000207575904-19', 1),
(109, 79, 3, 'BANCO CREDITO BCP', 1, '194-2131195-0-21', '002 194 002131195021 92', 1),
(110, 80, 3, 'BANCO CREDITO BCP', 1, '193-2165335-0-69', '00219300216533506910', 1),
(111, 81, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0174-01-00000390', '011-174000100000390-03', 1),
(112, 81, 3, 'BANCO CREDITO BCP', 1, '191-1740286-0-45', '191-001740286045-50', 1),
(113, 82, 3, 'BANCO CREDITO BCP', 1, '191-1193524-0-96', '00219100119352409', 1),
(114, 83, 2, 'BANCO CONTINENTAL BBVA', 1, '0011 - 0161 - 01000400 - 57', '011 - 161 - 000100040057 - 70', 1),
(115, 84, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0164-0100030325-13', '011-164-000100030325-13', 1),
(116, 84, 3, 'BANCO CREDITO BCP', 1, '191-1427026-0-03', '002-191-001427026003-58', 1),
(117, 85, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0921-0200158757', '011-921-000200158757-41', 1),
(118, 86, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0378-0100025236-79', '', 1),
(119, 86, 3, 'BANCO CREDITO BCP', 1, '191-1431216026', '002-191-001431216026-51', 1),
(120, 87, 3, 'BANCO CREDITO BCP', 2, '191-9974095-1-42', '002-191-009974095142-53', 1),
(121, 87, 3, 'BANCO CREDITO BCP', 1, '191-9976934-0-08', '002-191-009976934008-56', 1),
(122, 88, 3, 'BANCO CREDITO BCP', 1, '191-1597707-0-52', '002-191-001597707052-51', 1),
(123, 89, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0486-0100101045', '011-486-000100101045-81', 1),
(124, 89, 3, 'BANCO CREDITO BCP', 1, '193-1860079-0-75', '002-193–001860079075-14', 1),
(125, 90, 2, 'BANCO CONTINENTAL BBVA', 2, '341-0100013899', '', 1),
(126, 90, 2, 'BANCO CONTINENTAL BBVA', 1, '341-0100012795', '', 1),
(127, 90, 3, 'BANCO CREDITO BCP', 2, '191-1492328-1-28', '', 1),
(128, 90, 3, 'BANCO CREDITO BCP', 1, '193-2443224-0-29', '', 1),
(129, 91, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0140-0100045100', '011-140-00010004510013', 1),
(130, 92, 3, 'BANCO CREDITO BCP', 1, '1921814337035', '002-192-001814337035-33', 1),
(131, 93, 2, 'BANCO CONTINENTAL BBVA', 2, '0011 0188 04 0100030792', '', 1),
(132, 93, 3, 'BANCO CREDITO BCP', 2, '193 2347509120', '00219300234750912015', 1),
(133, 93, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0188-0100030784', '011 188 000100030784 01', 1),
(134, 94, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0134-01-00021292', '011-134-000100021292-46', 1),
(135, 94, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0134-01-00020237', '011-134-000100020237-42', 1),
(136, 94, 3, 'BANCO CREDITO BCP', 2, '191-1886409-1-44', '002-191-001900454004-53', 1),
(137, 94, 3, 'BANCO CREDITO BCP', 1, '191-1900454-0-04', '002-191-001900454004-53', 1),
(138, 95, 3, 'BANCO CREDITO BCP', 1, '191-1578867049', '002-191-001578867049', 1),
(139, 95, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0322-59-0100004489', '011-322-0001100004489-59', 1),
(140, 96, 3, 'BANCO CREDITO BCP', 2, '192-1600642-1-10', '00219200160064211037', 1),
(141, 97, 3, 'BANCO CREDITO BCP', 1, '191-1844964-0-98', '002-191-001844964098 -58', 1),
(142, 98, 3, 'BANCO CREDITO BCP', 1, '191-1778356-0-90', '00219100177835609051', 1),
(143, 99, 3, 'BANCO CREDITO BCP', 1, '191-29465642-0-73', '00219112946564207352', 1),
(144, 100, 2, 'BANCO CONTINENTAL BBVA', 1, '0146-0100008712', '011-146-000100008712-80', 1),
(145, 100, 3, 'BANCO CREDITO BCP', 1, '193-1724165-0-07', '002-193-001724165007-10', 1),
(146, 101, 3, 'BANCO CREDITO BCP', 1, '193-16969569-0-76', '00219311696956907610', 1),
(147, 102, 3, 'BANCO CREDITO BCP', 2, '192-1775432-1-65', '00219200177543216535', 1),
(148, 103, 1, 'INTERBANK', 1, '2003006617024', '00320000300661702439', 1),
(149, 104, 3, 'BANCO CREDITO BCP', 1, '1931789142040', '00219300178914204012', 1),
(150, 105, 3, 'BANCO CREDITO BCP', 1, '192-2170890-0-80', '002-292-002170890080-34', 1),
(151, 106, 2, 'BANCO CONTINENTAL BBVA', 1, '001-11-0480200430507', '', 1),
(152, 106, 3, 'BANCO CREDITO BCP', 1, '0235-2134755-017', '00223500213575501706', 1),
(153, 107, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0151-0200546794', '011-151-000200546794-85', 1),
(154, 107, 3, 'BANCO CREDITO BCP', 1, '191-2016539-0-79', '002-191-002016539079-58', 1),
(155, 108, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0145-09-0100032944 / 011-145-000100032944-09', '', 1),
(156, 108, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0145-05-0100032936 / 011-145-000100032936-05', '', 1),
(157, 108, 6, 'SCOTIABANK', 2, '000-3525960 / 009-219-000003525960-75', '', 1),
(158, 108, 6, 'SCOTIABANK', 1, '000-7527551 / 009-219-000007527551-71', '', 1),
(159, 108, 3, 'BANCO CREDITO BCP', 1, '193-2383145-0-70 / 002-193-002383145070-17', '', 1),
(160, 108, 3, 'BANCO CREDITO BCP', 2, '193-2378981-1-19 / 002-193-002378981119-19', '', 1),
(161, 109, 3, 'BANCO CREDITO BCP', 1, '191-1832705-0-70', '002-191-001832705070-56', 1),
(162, 109, 3, 'BANCO CREDITO BCP', 2, '191-2169698-1-49', '002-191-002169698149-53', 1),
(163, 110, 3, 'BANCO CREDITO BCP', 1, '191-1844944-0-96', '00219100184494409657', 1),
(164, 111, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-016101000313-68', '011-161-000100031368-72', 1),
(165, 111, 3, 'BANCO CREDITO BCP', 1, '193-4194864-0-61', '002-193004194864061-14', 1),
(166, 112, 3, 'BANCO CREDITO BCP', 1, '193-9970275-0-46', '002-193-009970275046-16', 1),
(167, 112, 1, 'INTERBANK', 1, '042-3002255-859', '003-042-003002255859-94', 1),
(168, 113, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0123-0100049367', '011-123-000100049367-74', 1),
(169, 113, 3, 'BANCO CREDITO BCP', 1, '191-2065243-0-39', '002-191-002065243039-56', 1),
(170, 114, 3, 'BANCO CREDITO BCP', 1, '191-1913548066', '002-19100191354806653', 1),
(171, 115, 3, 'BANCO CREDITO BCP', 1, '192-2052177-0-59', '00219200205217705932', 1),
(172, 115, 6, 'SCOTIABANK', 1, '000-7768702', '', 1),
(173, 116, 3, 'BANCO CREDITO BCP', 1, '193-1848767-0-12', '002-193-001848767012', 1),
(174, 116, 2, 'BANCO CONTINENTAL BBVA', 1, '', '011-32800010001623425', 1),
(175, 117, 2, 'BANCO CONTINENTAL BBVA', 1, '0011 04860 100087557', '', 1),
(176, 118, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0484-0 1000 177-79-38', '011-484-000 1000 17779-38', 1),
(177, 118, 3, 'BANCO CREDITO BCP', 1, '193-1953858-0-37', '00219300195385803714', 1),
(178, 119, 6, 'SCOTIABANK', 1, '000-8659699', '009- 202- 000008659699- 36', 1),
(179, 120, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0275-0100047515 CCI: 011-275-000100047515-50', '', 1),
(180, 121, 3, 'BANCO CREDITO BCP', 1, '255-2025458-0-27', '00225500202545802785', 1),
(181, 121, 3, 'BANCO CREDITO BCP', 2, '25591334979189', '00225519133497918980', 1),
(182, 122, 3, 'BANCO CREDITO BCP', 2, '191 24121231 88', '00219100241212318850', 1),
(183, 123, 2, 'BANCO CONTINENTAL BBVA', 2, '0011 0283 0200016947', '', 1),
(184, 124, 3, 'BANCO CREDITO BCP', 1, '191-1964424-0-65', '002-191-001964424065-57', 1),
(185, 124, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0832-0100018008-31', '011-832-000100018008-31', 1),
(186, 125, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0111-0100033058-27', '011-111-000100033058-27', 1),
(187, 125, 3, 'BANCO CREDITO BCP', 1, '191-2191355-0-97', '002-191-002191355097-52', 1),
(188, 126, 3, 'BANCO CREDITO BCP', 1, '193-2398270-0-48', '002-193-002398270048-18', 1),
(189, 127, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0138-01-00044294', '0011-138-000100044294-55', 1),
(190, 128, 3, 'BANCO CREDITO BCP', 1, '194-2005-498-0-54', '002-194-002005498054-93', 1),
(191, 128, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0933-0100017248', '011-933-000100017248-94', 1),
(192, 129, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0135-01000647-28', '011-135-000100064728-1', 1),
(193, 130, 3, 'BANCO CREDITO BCP', 1, '191-8748520-0-87', '00219100874852008754', 1),
(194, 131, 3, 'BANCO CREDITO BCP', 1, '193-2006642-0-10', '002-193-002006642010-18', 1),
(195, 132, 3, 'BANCO CREDITO BCP', 1, '193-2021685-0-59', '00219300202168505912', 1),
(196, 133, 1, 'INTERBANK', 1, '200 3005177470', '003 200 003005177470-38', 1),
(197, 134, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0377-01-00035171', '011-377-000100035171-96', 1),
(198, 135, 3, 'BANCO CREDITO BCP', 1, '191-2062019-0-73', '002 191002062019073 54', 1),
(199, 135, 1, 'INTERBANK', 1, '1613002240924', '', 1),
(200, 136, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0174-0100082311-00', '011-174-000100082311-00', 1),
(201, 136, 3, 'BANCO CREDITO BCP', 1, '192 2024 3030 04', '002-192-002 02430 3004-34', 1),
(202, 136, 4, 'BANCO NACION', 1, '00-074-045472', '', 1),
(203, 137, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0175-0100047988', '011-175-000100047988-79', 1),
(204, 138, 3, 'BANCO CREDITO BCP', 2, '194-2502302-1-87', '00219400250230218793', 1),
(205, 139, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-03120100015796', '0011-0312010001579664', 1),
(206, 140, 3, 'BANCO CREDITO BCP', 2, '193-2204048-1-20', '00219300220404812016', 1),
(207, 140, 3, 'BANCO CREDITO BCP', 1, '193-2549635-0-87', '00219300254963508714', 1),
(208, 141, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0760-0100003603-66', '011-760-000100003603-66', 1),
(209, 141, 6, 'SCOTIABANK', 2, '000-4577621', '009-242-000004577621-98', 1),
(210, 142, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0332-0100019693', '0011-332-000100019693-49', 1),
(211, 142, 3, 'BANCO CREDITO BCP', 1, '191-2682478-0-35', '002-191-002682478035', 1),
(212, 143, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0172-45-0100041436', '', 1),
(213, 143, 3, 'BANCO CREDITO BCP', 1, '191-2092063048', '0021 191 00209206304 858', 1),
(214, 143, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0172-45-01000-41568', '011 172 000100041568 45', 1),
(215, 144, 3, 'BANCO CREDITO BCP', 1, '193-2270634-0-095', '00219300227063409511', 1),
(216, 145, 3, 'BANCO CREDITO BCP', 1, 'BCP S/: 191-7097635-0-80', '00219100709763508054', 1),
(217, 146, 3, 'BANCO CREDITO BCP', 1, '1912155427088', '00219100215542708850', 1),
(218, 146, 2, 'BANCO CONTINENTAL BBVA', 1, '001101620100062714', '01116200010006271459', 1),
(219, 146, 3, 'BANCO CREDITO BCP', 2, '19194372868127', '00219119437286812751', 1),
(220, 147, 2, 'BANCO CONTINENTAL BBVA', 2, '00110181010002290259', '111810001000229', 1),
(221, 148, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0312-0100022296-66', '01131200010002229666', 1),
(222, 148, 3, 'BANCO CREDITO BCP', 2, '191-2351280-1-12', '00219100235128011259', 1),
(223, 149, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0842-27-0200353119', '011 -842-000200353119-27', 1),
(224, 150, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-01690100035170 08', '011 169 000100037351 02', 1),
(225, 150, 2, 'BANCO CONTINENTAL BBVA', 1, '0011 0169 0100037351 02', '011 169 000100037351 02', 1),
(226, 151, 3, 'BANCO CREDITO BCP', 1, '1918758011056', '002-191-008758011056-54', 1),
(227, 152, 1, 'INTERBANK', 2, '200-3006714606', '003-200-003006714606-35', 1),
(228, 153, 6, 'SCOTIABANK', 1, '016-7224393', '00901320016722439344', 1),
(229, 154, 2, 'BANCO CONTINENTAL BBVA', 1, '0011 0054 0100002552', '011 054 000100002552 46', 1),
(230, 154, 3, 'BANCO CREDITO BCP', 1, '255-2204220-0-05', '00225500220422000589', 1),
(231, 155, 3, 'BANCO CREDITO BCP', 1, '1912295784035', '00219100229578403552', 1),
(232, 156, 2, 'BANCO CONTINENTAL BBVA', 1, '001101050100046378', '001110500010004637844', 1),
(233, 157, 2, 'BANCO CONTINENTAL BBVA', 1, '001104800100030978', '01148000010003097819', 1),
(234, 157, 3, 'BANCO CREDITO BCP', 1, '1932554994018', '0021930025549901814', 1),
(235, 158, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0484-0200309696', '', 1),
(236, 159, 3, 'BANCO CREDITO BCP', 1, '191-2523899-0-27', '002-191-002523899027-56', 1),
(237, 160, 3, 'BANCO CREDITO BCP', 1, '194-2244669-0-22', '00219400224466902298', 1),
(238, 161, 3, 'BANCO CREDITO BCP', 1, '193-9841989-0-27', '002-193009841989-0-2716', 1),
(239, 162, 3, 'BANCO CREDITO BCP', 1, '193-1530391-0-94', '00219300153039109415', 1),
(240, 162, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0129-0100041229', '01112900010004122944', 1),
(241, 163, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0418-0100004847', '', 1),
(242, 164, 2, 'BANCO CONTINENTAL BBVA', 2, '001103490100045786', '', 1),
(243, 165, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0566-01000158-95', '011-566-000100015895-74', 1),
(244, 166, 3, 'BANCO CREDITO BCP', 1, '1922296891017', '00219200229689101738', 1),
(245, 166, 4, 'BANCO NACION', 1, '00-030-077083', '', 1),
(246, 167, 3, 'BANCO CREDITO BCP', 1, '1942357066046', '002-194-002357066046-9', 1),
(247, 168, 2, 'BANCO CONTINENTAL BBVA', 1, '001105660100013426', '00110566010001342670', 1),
(248, 168, 3, 'BANCO CREDITO BCP', 1, '1912275863013', '00219100227586301355', 1),
(249, 169, 3, 'BANCO CREDITO BCP', 1, '191-2308585-0-39', '00219100230858503956', 1),
(250, 170, 3, 'BANCO CREDITO BCP', 1, '194-2593443-092', '00219400259344309294', 1),
(251, 171, 3, 'BANCO CREDITO BCP', 2, '193-2338066-137', '', 1),
(252, 171, 2, 'BANCO CONTINENTAL BBVA', 1, '193-2311727-076', '', 1),
(253, 172, 3, 'BANCO CREDITO BCP', 1, '19129172594065', '00219112917259406558', 1),
(254, 173, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0162-01-00039674', '011-162-000100039674-55', 1),
(255, 174, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0174-0100068238-03', '011-174-000100068238-03', 1),
(256, 175, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0322-0100033039', '011-322-000100033039-57', 1),
(257, 176, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0191-0100043882-48', '011-191-000100043882-48', 1),
(258, 177, 1, 'INTERBANK', 1, '639-3003135050', '003-639-00300313505024', 1),
(259, 177, 4, 'BANCO NACION', 1, '00-060-112614', '', 1),
(260, 178, 3, 'BANCO CREDITO BCP', 1, '191-2445472-0-36', '002-191-002445472036-58', 1),
(261, 178, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0733-01-00012559', '', 1),
(262, 179, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0970-0200166234', '', 1),
(263, 180, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0566-01000179-95', '011-566-000100017995-70', 1),
(264, 181, 3, 'BANCO CREDITO BCP', 1, '191 2668883 0 12', '00219100266888301259', 1),
(265, 182, 3, 'BANCO CREDITO BCP', 1, '191-00410151-0-93', '002191100410151093', 1),
(266, 183, 3, 'BANCO CREDITO BCP', 1, '194-2069824-0-11', '00219400206982401194', 1),
(267, 183, 3, 'BANCO CREDITO BCP', 2, '194-2027599-1-06', '00219400202759910693', 1),
(268, 184, 6, 'SCOTIABANK', 1, '035-7398379', '009 060 200357398379 50.', 1),
(269, 185, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0832-010003485', '011-832-000100034852', 1),
(270, 186, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0118-0100031037', '011-11800010003103773', 1),
(271, 187, 3, 'BANCO CREDITO BCP', 1, '191-2550019-0-66', '002-191-00255001906652', 1),
(272, 188, 3, 'BANCO CREDITO BCP', 1, '192-2566482-0-59', '002-19200256648205931', 1),
(273, 189, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0109-0200610574 62', '011 109 000200610574 62', 1),
(274, 189, 3, 'BANCO CREDITO BCP', 1, '193-98502597070', '/ CCI 00219319850259707019', 1),
(275, 190, 3, 'BANCO CREDITO BCP', 1, '191-2518294-0-12', '00219100251829401259', 1),
(276, 190, 3, 'BANCO CREDITO BCP', 2, '191-2470654-1-09', '00219100247065410951', 1),
(277, 191, 3, 'BANCO CREDITO BCP', 1, '315-2518131-0-84', '002 315 002518131084 05', 1),
(278, 192, 3, 'BANCO CREDITO BCP', 1, '1912506624033', '00219100250662403351', 1),
(279, 193, 3, 'BANCO CREDITO BCP', 1, '1912538077040', '00219100253807704051', 1),
(280, 194, 3, 'BANCO CREDITO BCP', 1, '192-3038093-0-07', '00219200303809300730', 1),
(281, 195, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-609-000100019257-53', '', 1),
(282, 196, 3, 'BANCO CREDITO BCP', 1, '1912578750078', '00219100257875007851', 1),
(283, 197, 3, 'BANCO CREDITO BCP', 1, '193- 2617192-0-82', '0021 9300 2617 1920 8215', 1),
(284, 197, 4, 'BANCO NACION', 1, '00- 066155463', '', 1),
(285, 198, 3, 'BANCO CREDITO BCP', 2, '193-8751483-1-26', '00219300875148312619', 1),
(286, 199, 3, 'BANCO CREDITO BCP', 1, '192-7530206-0-85', '0021-9200753020608532', 1),
(287, 200, 3, 'BANCO CREDITO BCP', 2, '193-9967636-1-99', '00219300996763619913', 1),
(288, 200, 3, 'BANCO CREDITO BCP', 1, '193-9962448-0-85', '00219300996244808513', 1),
(289, 201, 2, 'BANCO CONTINENTAL BBVA', 1, '001101760100074786', '01117600010007478653', 1),
(290, 202, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0876-0100015750', '011-876-0001000', 1),
(291, 203, 3, 'BANCO CREDITO BCP', 1, '1912650679033', '002-19100265067903-3-51', 1),
(292, 204, 3, 'BANCO CREDITO BCP', 1, '0011- 0166- 0200695740', '0011-166-000200695740-67', 1),
(293, 204, 4, 'BANCO NACION', 1, '00-031-151821', '', 1),
(294, 205, 3, 'BANCO CREDITO BCP', 1, '191-2630527-0-78', '002-191-002630527078-52', 1),
(295, 206, 3, 'BANCO CREDITO BCP', 1, '191-2630528-0-88', '002-191-002630528088-50', 1),
(296, 207, 3, 'BANCO CREDITO BCP', 1, '191-02001567-0-84', '002-19110200156708454', 1),
(297, 208, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0338-0100025823', '011-338-000100025823-14', 1),
(298, 209, 3, 'BANCO CREDITO BCP', 1, '19177457183076', '00219117745718307658', 1),
(299, 210, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0201-0100048348', '011-201-000100048348 15', 1),
(300, 211, 3, 'BANCO CREDITO BCP', 1, '193-99715062-0-82', '00219319971506208217', 1),
(301, 212, 2, 'BANCO CONTINENTAL BBVA', 1, '0011 0122 01000592-78', '011-122-000100059278-90', 1),
(302, 213, 1, 'INTERBANK', 1, '2053003203367', '00320500300320336729', 1),
(303, 214, 3, 'BANCO CREDITO BCP', 1, '570-8745773-0-1', '00257000874577301408', 1),
(304, 214, 2, 'BANCO CONTINENTAL BBVA', 1, '011-291-000200508940-20', '0011-0291-0200508940', 1),
(305, 215, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0165-0200172920', '011-165-000200172920', 1),
(306, 216, 3, 'BANCO CREDITO BCP', 1, '192-9399290-0-21', '00219200939929002132', 1),
(307, 216, 3, 'BANCO CREDITO BCP', 2, '192-9405808-1-69', '00219200940580816939', 1),
(308, 217, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0332-0100036490', '', 1),
(309, 218, 3, 'BANCO CREDITO BCP', 1, '19404513417006', '00219410451341700691', 1),
(310, 218, 1, 'INTERBANK', 1, '2003003572950', '00320000300357295035', 1),
(311, 219, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0111- 0100076172', '', 1),
(312, 219, 3, 'BANCO CREDITO BCP', 1, '191 70343846 0 88', '', 1),
(313, 220, 2, 'BANCO CONTINENTAL BBVA', 2, '0011-0111-0100071014', '011-111-000100071014-23', 1),
(314, 221, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0566-01000194-16', '011-566-000100019416-75', 1),
(315, 222, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0586-51-0100067975', '01158600010006797551', 1),
(316, 222, 1, 'INTERBANK', 1, '200-3006667579', '003 200 003006667579 31', 1),
(317, 223, 3, 'BANCO CREDITO BCP', 1, '191-2614936-0-93', '002-19100261493609350', 1),
(318, 224, 3, 'BANCO CREDITO BCP', 1, '19170796814031', '00219117079681403158', 1),
(319, 225, 1, 'INTERBANK', 1, '898 3003917767', '003 898 003003917767 47', 1),
(320, 226, 3, 'BANCO CREDITO BCP', 1, '191-9854909-0-32', '002-191-00985490903256', 1),
(321, 227, 3, 'BANCO CREDITO BCP', 1, '191-70850600-61', '002-191-007085060061-53', 1),
(322, 228, 3, 'BANCO CREDITO BCP', 1, '191-9856819-0-25', '00219100985681902554', 1),
(323, 229, 3, 'BANCO CREDITO BCP', 1, '194-9883685099', '002-19400988368509994', 1),
(324, 230, 3, 'BANCO CREDITO BCP', 1, '191986059506', '00219100986059506758.', 1),
(325, 230, 4, 'BANCO NACION', 1, '00-069-027121', '', 1),
(326, 231, 3, 'BANCO CREDITO BCP', 1, '194-9889496-0-96', '002-19400988949609696', 1),
(327, 232, 3, 'BANCO CREDITO BCP', 1, '194- 9355437-0-61', '00219400935543706194', 1),
(328, 233, 3, 'BANCO CREDITO BCP', 1, '191-9882801-0-70', '00219100988280107', 1),
(329, 234, 3, 'BANCO CREDITO BCP', 1, '375 06665082 0 62', '002 37510666508206244', 1),
(330, 235, 3, 'BANCO CREDITO BCP', 1, '193-9941766-0-76', '002-193009941766076-10', 1),
(331, 236, 2, 'BANCO CONTINENTAL BBVA', 1, '1919896451049', '00219100989645104953', 1),
(332, 237, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-152-0100100654', '011-152-000100100654-61', 1),
(333, 237, 3, 'BANCO CREDITO BCP', 1, '193-9902956 - 0 - 56', '00219300990295605614', 1),
(334, 238, 3, 'BANCO CREDITO BCP', 1, '191 9911274 0 77', '002 191 009911274077 56', 1),
(335, 239, 1, 'INTERBANK', 1, '2003004877231', '00320000300487723130', 1),
(336, 240, 1, 'INTERBANK', 1, '200-3004616586', '003-200-003004616586-33', 1),
(337, 241, 3, 'BANCO CREDITO BCP', 1, '191-9939216-0-19', '00219100993921601950', 1),
(338, 242, 3, 'BANCO CREDITO BCP', 1, '191-1714517-0-52', '00219100171451705252', 1),
(339, 243, 3, 'BANCO CREDITO BCP', 1, '191-9968118-0-58', '002-191-009968118058-56', 1),
(340, 244, 3, 'BANCO CREDITO BCP', 1, '193-9969671-0-45', '002-193-009969671045-10', 1),
(341, 244, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0111-0100077845', '011-111-000100077845-24', 1),
(342, 245, 3, 'BANCO CREDITO BCP', 1, '1939969550023', '00219300996955002313', 1),
(343, 246, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0175-0100093130', '011-175-000100093130-71', 1),
(344, 247, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0130-0201163029', '011-130-000201163029-26', 1),
(345, 248, 3, 'BANCO CREDITO BCP', 1, '1917069972056', '00219100706997205654', 1),
(346, 249, 3, 'BANCO CREDITO BCP', 1, '1917065848-0-99', '00219100706584809954', 1),
(347, 250, 1, 'INTERBANK', 1, '2003007058711', '00320000300705871135', 1),
(348, 251, 3, 'BANCO CREDITO BCP', 1, '194-7081123-0-93', '002 - 19400708112309390', 1),
(349, 252, 3, 'BANCO CREDITO BCP', 1, '1917118963015', '00219100711896301556', 1),
(350, 253, 2, 'BANCO CONTINENTAL BBVA', 1, '0011-0176000100091001', '011-0176-000100091001-56', 1),
(351, 253, 3, 'BANCO CREDITO BCP', 1, '191-7212507-0-04', 'CCI : 00219100721250700454', 1),
(352, 254, 3, 'BANCO CREDITO BCP', 1, '1917208232023', '00219100720823202354', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nom_rol` longtext NOT NULL,
  `est_rol` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nom_rol`, `est_rol`) VALUES
(1, 'SUPER ADMINISTRADOR', 1),
(2, 'ADMINISTRADOR', 1),
(3, 'COMPRAS', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida`
--

CREATE TABLE `salida` (
  `id_salida` int(11) NOT NULL,
  `id_material_tipo` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_almacen_origen` int(11) NOT NULL,
  `id_ubicacion_origen` int(11) NOT NULL,
  `id_almacen_destino` int(11) NOT NULL,
  `id_ubicacion_destino` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL COMMENT 'personal que registra',
  `id_personal_encargado` int(11) DEFAULT NULL COMMENT 'personal encargado de obra',
  `id_personal_recibe` int(11) DEFAULT NULL COMMENT 'personal que recibe',
  `id_personal_aprueba_salida` int(11) DEFAULT NULL,
  `id_personal_recepciona_salida` int(11) DEFAULT NULL COMMENT 'Personal que recepciona la salida',
  `id_personal_deniega_salida` int(11) DEFAULT NULL COMMENT 'Personal que deniega la salida',
  `fec_aprueba_salida` datetime DEFAULT NULL,
  `fec_recepciona_salida` datetime DEFAULT NULL COMMENT 'Fecha y hora de recepción',
  `fec_deniega_salida` datetime DEFAULT NULL COMMENT 'Fecha y hora de denegación',
  `ndoc_salida` longtext DEFAULT NULL COMMENT 'numero de documento',
  `fec_req_salida` date DEFAULT NULL COMMENT 'fecha requerida en obra',
  `fec_salida` datetime DEFAULT NULL COMMENT 'fecha y hora de registro',
  `obs_salida` longtext DEFAULT NULL COMMENT 'observaciones',
  `est_salida` int(11) DEFAULT NULL COMMENT '0 = anulado, 1 = pendiente, 2 = recepcionado, 3 = aprobado, 4 = denegado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida_detalle`
--

CREATE TABLE `salida_detalle` (
  `id_salida_detalle` int(11) NOT NULL,
  `id_salida` int(11) NOT NULL,
  `id_pedido_detalle` int(11) DEFAULT NULL,
  `id_producto` int(11) NOT NULL,
  `prod_salida_detalle` longtext NOT NULL COMMENT 'nombre del producto',
  `cant_salida_detalle` float(10,2) NOT NULL,
  `est_salida_detalle` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subestacion`
--

CREATE TABLE `subestacion` (
  `id_subestacion` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `cod_subestacion` varchar(100) DEFAULT NULL,
  `nom_subestacion` longtext DEFAULT NULL,
  `act_subestacion` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `subestacion`
--

INSERT INTO `subestacion` (`id_subestacion`, `id_cliente`, `cod_subestacion`, `nom_subestacion`, `act_subestacion`, `updated_at`) VALUES
(1, 2, 'N', 'ANCON', 1, '2020-01-01 00:00:00'),
(2, 2, 'K', 'Barsi', 1, '2020-01-01 00:00:00'),
(3, 2, 'CG', 'CantoGrande', 1, '2020-01-01 00:00:00'),
(4, 2, 'CR', 'Carabayllo', 1, '2020-01-01 00:00:00'),
(5, 2, 'CV', 'Caudivilla', 1, '2020-01-01 00:00:00'),
(6, 2, 'CY', 'Chancay', 1, '2020-01-01 00:00:00'),
(7, 2, 'CH', 'Chavarria', 1, '2020-01-01 00:00:00'),
(8, 2, 'CN', 'Chillon', 1, '2020-01-01 00:00:00'),
(9, 2, 'CS', 'Comas', 1, '2020-01-01 00:00:00'),
(10, 2, 'CRRep', 'CRRep', 1, '2020-01-01 00:00:00'),
(11, 2, 'FA', 'Filadelfia', 1, '2020-01-01 00:00:00'),
(12, 2, 'H', 'Huacho', 1, '2020-01-01 00:00:00'),
(13, 2, 'HY', 'Huandoy', 1, '2020-01-01 00:00:00'),
(14, 2, 'HL', 'Huaral', 1, '2020-01-01 00:00:00'),
(15, 2, 'NZ', 'Huarangal', 1, '2020-01-01 00:00:00'),
(16, 2, 'ID', 'Industrial', 1, '2020-01-01 00:00:00'),
(17, 2, 'I', 'Infantas', 1, '2020-01-01 00:00:00'),
(18, 2, 'J', 'Jicamarca', 1, '2020-01-01 00:00:00'),
(19, 2, 'LP', 'LaPampilla', 1, '2020-01-01 00:00:00'),
(20, 2, 'SetLOMERA', 'Lomera', 1, '2020-01-01 00:00:00'),
(21, 2, 'MS', 'Malvinas', 1, '2020-01-01 00:00:00'),
(22, 2, 'MA', 'Maranga', 1, '2020-01-01 00:00:00'),
(23, 2, 'MR', 'Mirador', 1, '2020-01-01 00:00:00'),
(24, 2, 'M', 'Mirones', 1, '2020-01-01 00:00:00'),
(25, 2, 'NJ', 'Naranjal', 1, '2020-01-01 00:00:00'),
(26, 2, 'O', 'Oquendo', 1, '2020-01-01 00:00:00'),
(27, 2, 'PA', 'Pando', 1, '2020-01-01 00:00:00'),
(28, 2, 'Q', 'Pershing', 1, '2020-01-01 00:00:00'),
(29, 2, 'PP', 'PuentePiedra', 1, '2020-01-01 00:00:00'),
(30, 2, 'F', 'SantaMarina', 1, '2020-01-01 00:00:00'),
(31, 2, 'P', 'SantaRosaA', 1, '2020-01-01 00:00:00'),
(32, 2, 'SR', 'SantaRosaN', 1, '2020-01-01 00:00:00'),
(33, 2, 'SU', 'Supe', 1, '2020-01-01 00:00:00'),
(34, 2, 'T', 'Tacna', 1, '2020-01-01 00:00:00'),
(35, 2, 'TV', 'TomasValle', 1, '2020-01-01 00:00:00'),
(36, 2, 'U', 'UNI', 1, '2020-01-01 00:00:00'),
(37, 2, 'V', 'Ventanilla', 1, '2020-01-01 00:00:00'),
(38, 2, 'V_REP', 'Ventanilla220kv', 1, '2020-01-01 00:00:00'),
(39, 2, 'W', 'Zapallal', 1, '2020-01-01 00:00:00'),
(40, 2, 'Z', 'Zarate', 1, '2020-01-01 00:00:00'),
(41, 9, 'BASE(OFICINA)', 'BASE(OFICINA)', 1, '2020-01-01 00:00:00'),
(42, 9, 'ALMACEN VILLEGAS', 'ALMACEN VILLEGAS', 1, '2020-01-01 00:00:00'),
(43, 3, 'SET ALTO PRADERAS', 'SET ALTO PRADERAS', 1, '2020-01-01 00:00:00'),
(44, 3, 'SET BALNEARIOS', 'SET BALNEARIOS', 1, '2020-01-01 00:00:00'),
(45, 3, 'SET BARRANCO', 'SET BARRANCO', 1, '2020-01-01 00:00:00'),
(46, 3, 'SET BUJAMAS', 'SET BUJAMAS', 1, '2020-01-01 00:00:00'),
(47, 3, 'SET CANTERAS', 'SET CANTERAS', 1, '2020-01-01 00:00:00'),
(48, 3, 'SET CA', 'SET CA', 1, '2020-01-01 00:00:00'),
(49, 3, 'SET CARAPONGO', 'SET CARAPONGO', 1, '2020-01-01 00:00:00'),
(50, 3, 'SET CENTRAL', 'SET CENTRAL', 1, '2020-01-01 00:00:00'),
(51, 3, 'SET CHILCA', 'SET CHILCA', 1, '2020-01-01 00:00:00'),
(52, 3, 'SET CHORRILLOS', 'SET CHORRILLOS', 1, '2020-01-01 00:00:00'),
(53, 3, 'SET GALVEZ', 'SET GALVEZ', 1, '2020-01-01 00:00:00'),
(54, 3, 'SET HUACHIPA', 'SET HUACHIPA', 1, '2020-01-01 00:00:00'),
(55, 3, 'SET LIMATAMBO', 'SET LIMATAMBO', 1, '2020-01-01 00:00:00'),
(56, 3, 'SET LOS SAUCES', 'SET LOS SAUCES', 1, '2020-01-01 00:00:00'),
(57, 3, 'SET LURIN', 'SET LURIN', 1, '2020-01-01 00:00:00'),
(58, 3, 'SET MANCHAY', 'SET MANCHAY', 1, '2020-01-01 00:00:00'),
(59, 3, 'SET ', 'SET ', 0, '2020-01-01 00:00:00'),
(60, 3, 'PACHACUTEC', 'LINEA PACHACUTEC - UNACEM', 1, '2020-01-01 00:00:00'),
(61, 3, 'SET PLANICIE REP', 'SET PLANICIE REP', 1, '2020-01-01 00:00:00'),
(62, 3, 'SET PROGRESO', 'SET PROGRESO', 1, '2020-01-01 00:00:00'),
(63, 3, 'SET PUENTE', 'SET PUENTE', 1, '2020-01-01 00:00:00'),
(64, 3, 'SET SALAMANCA', 'SET SALAMANCA', 1, '2020-01-01 00:00:00'),
(65, 3, 'SET SAN BARTOLO', 'SET SAN BARTOLO', 1, '2020-01-01 00:00:00'),
(66, 3, 'SET SAN ISIDRO', 'SET SAN ISIDRO', 1, '2020-01-01 00:00:00'),
(67, 3, 'SET SAN JUAN', 'SET SAN JUAN', 1, '2020-01-01 00:00:00'),
(68, 3, 'SET SAN LUIS', 'SET SAN LUIS', 1, '2020-01-01 00:00:00'),
(69, 3, 'SET SAN MIGUEL', 'SET SAN MIGUEL', 1, '2020-01-01 00:00:00'),
(70, 3, 'SET SAN VICENTE', 'SET SAN VICENTE', 1, '2020-01-01 00:00:00'),
(71, 3, 'SET SANTA ANITA', 'SET SANTA ANITA', 1, '2020-01-01 00:00:00'),
(72, 3, 'SET VERTIENTES', 'SET VERTIENTES', 1, '2020-01-01 00:00:00'),
(73, 3, 'SET VILLA MARIA', 'SET VILLA MARIA', 1, '2020-01-01 00:00:00'),
(74, 3, 'SET SAN BARTOLO', 'SET SAN BARTOLO', 1, '2020-01-01 00:00:00'),
(75, 0, 'MARIATEGUI', 'MARIATEGUI', 1, '2020-01-01 00:00:00'),
(76, 0, 'MARIATEGUI', 'MARIATEGUI', 0, '2020-01-01 00:00:00'),
(77, 2, 'MARIATEGUI', 'MARIATEGUI', 1, '2020-01-01 00:00:00'),
(78, 2, 'IZAGUIRRE', 'IZAGUIRRE', 1, '2020-01-01 00:00:00'),
(79, 2, 'MM', 'MEDIO MUNDO', 1, '2020-01-01 00:00:00'),
(80, 3, 'INDUSTRIALES', 'INDUSTRIALES', 1, '2020-01-01 00:00:00'),
(81, 3, 'MONTERRICO', 'MONTERRICO', 1, '2020-01-01 00:00:00'),
(82, 3, 'PACHACAMAC', 'PACHACAMAC', 1, '2020-01-01 00:00:00'),
(83, 3, 'VILLA EL SALVADOR', 'VILLA EL SALVADOR', 1, '2020-01-01 00:00:00'),
(9997, 1, 'LAT', 'LINEAS AT', 1, '2020-01-01 00:00:00'),
(9998, 2, 'LAT', 'LINEAS AT', 1, '2020-01-01 00:00:00'),
(9999, 3, 'LAT', 'LINEAS AT', 1, '2020-01-01 00:00:00'),
(10000, 3, 'SET CHOSICA', 'SET CHOSICA', 1, '2020-01-01 00:00:00'),
(10001, 3, 'SET PRADERAS', 'PRADERAS', 1, '2020-01-01 00:00:00'),
(10002, 3, 'SET NEYRA', 'SET NEYRA', 1, '2020-01-01 00:00:00'),
(10003, 2, 'C.H.H', 'CH HUINCO', 1, '2020-01-01 00:00:00'),
(10004, 4, 'PARAMONGA', 'PARAMONGA', 1, '2020-01-01 00:00:00'),
(10005, 5, 'NA', 'NA', 1, '2020-01-01 00:00:00'),
(10006, 2, 'OTRO', 'TRABAJOS EN VIA PUBLICA', 1, '2020-01-01 00:00:00'),
(10007, 7, 'OTRO', 'TRABAJO EN VIA PUBLICA', 1, '2020-01-01 00:00:00'),
(10008, 8, 'CH.HH', 'HUINCO', 1, '2020-01-01 00:00:00'),
(10009, 2, 'SED', 'SUBESTACION DE DISTRIBUCION', 1, '2020-01-01 00:00:00'),
(10010, 9, 'SERVIPLAST', 'SERVIPLAST', 1, '2020-01-01 00:00:00'),
(10011, 9, 'ALM. AGUNSA', 'ALM. AGUNSA', 1, '2020-01-01 00:00:00'),
(10012, 10, 'MLL2', 'LíNEA 2 DEL METRO DE LIMA', 1, '2020-01-01 00:00:00'),
(10013, 2, 'JOSé GRANDA', 'JOSé GRANDA', 1, '2020-01-01 00:00:00'),
(10014, 3, 'ÑAÑA', 'SET ÑAÑA', 1, '2020-01-01 00:00:00'),
(10015, 2, '001', 'LINEA CHILLON OQUENDO', 1, '2020-01-01 00:00:00'),
(10016, 3, 'LCV', 'LINEA CANTERAS - SAN VICENTE', 1, '2020-01-01 00:00:00'),
(10017, 3, 'L-CH-O', 'LINEA CHILLON - OQUENDO', 0, '2020-01-01 00:00:00'),
(10018, 2, 'DDD', 'DDDD', 0, '2020-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo`
--

CREATE TABLE `tipo` (
  `id_tipo` int(11) NOT NULL,
  `nom_tipo` varchar(100) NOT NULL,
  `opc_adm` int(11) NOT NULL,
  `opc_ip` int(11) NOT NULL,
  `opc_im` int(11) NOT NULL,
  `opc_oc` int(11) NOT NULL,
  `opc_co` int(11) NOT NULL,
  `opc_hse` int(11) NOT NULL,
  `opc_cc` int(11) NOT NULL,
  `opc_lav` int(11) NOT NULL,
  `act_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `tipo`
--

INSERT INTO `tipo` (`id_tipo`, `nom_tipo`, `opc_adm`, `opc_ip`, `opc_im`, `opc_oc`, `opc_co`, `opc_hse`, `opc_cc`, `opc_lav`, `act_tipo`) VALUES
(1, 'ADMINISTRADOR', 1, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 'PERSONAL', 0, 0, 0, 0, 1, 1, 1, 0, 1),
(3, 'ENCUESTADOR', 0, 1, 1, 1, 1, 1, 1, 1, 1),
(4, 'ENCUESTADOR2', 0, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id_tipo_documento` int(11) NOT NULL,
  `nom_tipo_documento` varchar(100) NOT NULL,
  `est_tipo_documento` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_documento`
--

INSERT INTO `tipo_documento` (`id_tipo_documento`, `nom_tipo_documento`, `est_tipo_documento`) VALUES
(1, 'Factura', 1),
(2, 'Boleta', 1),
(3, 'Recibo Honorario', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

CREATE TABLE `ubicacion` (
  `id_ubicacion` int(11) NOT NULL,
  `nom_ubicacion` longtext DEFAULT NULL,
  `est_ubicacion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicacion`
--

INSERT INTO `ubicacion` (`id_ubicacion`, `nom_ubicacion`, `est_ubicacion`) VALUES
(1, 'BASE', 1),
(2, 'OBRA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id_unidad_medida` int(11) NOT NULL,
  `nom_unidad_medida` longtext DEFAULT NULL,
  `est_unidad_medida` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`id_unidad_medida`, `nom_unidad_medida`, `est_unidad_medida`) VALUES
(1, 'UND', 1),
(2, 'BOLSA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `uso_material`
--

CREATE TABLE `uso_material` (
  `id_uso_material` int(11) NOT NULL COMMENT 'id',
  `id_almacen` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL COMMENT 'ubicación',
  `id_personal` int(11) NOT NULL COMMENT 'El que registra',
  `id_solicitante` int(11) NOT NULL COMMENT 'El que solicita',
  `fec_uso_material` datetime NOT NULL COMMENT 'fecha de registro',
  `est_uso_material` int(11) NOT NULL COMMENT 'estado'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `uso_material_detalle`
--

CREATE TABLE `uso_material_detalle` (
  `id_uso_material_detalle` int(11) NOT NULL,
  `id_uso_material` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cant_uso_material_detalle` float(10,2) NOT NULL,
  `obs_uso_material_detalle` longtext NOT NULL,
  `est_uso_material_detalle` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `uso_material_detalle_documento`
--

CREATE TABLE `uso_material_detalle_documento` (
  `id_uso_material_detalle_documento` int(11) NOT NULL,
  `id_uso_material_detalle` int(11) NOT NULL,
  `nom_uso_material_detalle_documento` longtext NOT NULL,
  `est_uso_material_detalle_documento` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL,
  `usu_usuario` varchar(50) NOT NULL,
  `con_usuario` varchar(255) NOT NULL,
  `est_usuario` int(11) NOT NULL DEFAULT 1,
  `fec_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fec_ultimo_acceso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `id_personal`, `usu_usuario`, `con_usuario`, `est_usuario`, `fec_creacion`, `fec_ultimo_acceso`) VALUES
(1, 1371, 'iheras', '123456', 1, '2025-10-01 23:26:30', '2025-12-02 17:52:52'),
(4, 171, 'krojas', '123456', 1, '2025-12-01 09:44:37', '2025-12-01 15:26:00'),
(5, 301, 'ryanez', 'soyadmin', 1, '2025-12-01 09:50:26', '2025-12-11 11:53:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_rol`
--

CREATE TABLE `usuario_rol` (
  `id_usuario_rol` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `est_usuario_rol` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `usuario_rol`
--

INSERT INTO `usuario_rol` (`id_usuario_rol`, `id_usuario`, `id_rol`, `est_usuario_rol`) VALUES
(6, 1, 2, 1),
(2, 4, 2, 1),
(7, 5, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accion`
--
ALTER TABLE `accion`
  ADD PRIMARY KEY (`id_accion`);

--
-- Indices de la tabla `almacen`
--
ALTER TABLE `almacen`
  ADD PRIMARY KEY (`id_almacen`),
  ADD KEY `fk_almacen_cliente_cliente1_idx` (`id_cliente`),
  ADD KEY `fk_almacen_obra1_idx` (`id_obra`);

--
-- Indices de la tabla `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_modulo` (`modulo`),
  ADD KEY `idx_accion` (`accion`);

--
-- Indices de la tabla `banco`
--
ALTER TABLE `banco`
  ADD PRIMARY KEY (`id_banco`);

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `fk_compra_pedido1_idx` (`id_pedido`),
  ADD KEY `fk_compra_proveedor1_idx` (`id_proveedor`),
  ADD KEY `fk_compra_moneda1_idx` (`id_moneda`),
  ADD KEY `fk_compra_personal_aprueba_financiera` (`id_personal_aprueba_financiera`),
  ADD KEY `id_detraccion` (`id_detraccion`),
  ADD KEY `fk_compra_retencion` (`id_retencion`),
  ADD KEY `fk_compra_percepcion` (`id_percepcion`);

--
-- Indices de la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  ADD PRIMARY KEY (`id_compra_detalle`),
  ADD KEY `fk_compra_detalle_compra1_idx` (`id_compra`),
  ADD KEY `fk_compra_detalle_material1_idx` (`id_producto`),
  ADD KEY `idx_pedido_detalle` (`id_pedido_detalle`);

--
-- Indices de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD PRIMARY KEY (`id_comprobante`),
  ADD KEY `id_tipo_documento` (`id_tipo_documento`),
  ADD KEY `id_moneda` (`id_moneda`),
  ADD KEY `id_medio_pago` (`id_medio_pago`),
  ADD KEY `id_detraccion` (`id_detraccion`),
  ADD KEY `idx_compra` (`id_compra`),
  ADD KEY `idx_estado` (`est_comprobante`);

--
-- Indices de la tabla `comprobante_pago`
--
ALTER TABLE `comprobante_pago`
  ADD PRIMARY KEY (`id_comprobante_pago`),
  ADD KEY `fk_cp_comprobante` (`id_comprobante`);

--
-- Indices de la tabla `detraccion`
--
ALTER TABLE `detraccion`
  ADD PRIMARY KEY (`id_detraccion`),
  ADD KEY `fk_detraccion_tipo` (`id_detraccion_tipo`);

--
-- Indices de la tabla `detraccion_tipo`
--
ALTER TABLE `detraccion_tipo`
  ADD PRIMARY KEY (`id_detraccion_tipo`);

--
-- Indices de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  ADD PRIMARY KEY (`id_devolucion`),
  ADD KEY `fk_devolucion_cliente_destino` (`id_cliente_destino`);

--
-- Indices de la tabla `devolucion_detalle`
--
ALTER TABLE `devolucion_detalle`
  ADD PRIMARY KEY (`id_devolucion_detalle`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id_doc`);

--
-- Indices de la tabla `ingreso`
--
ALTER TABLE `ingreso`
  ADD PRIMARY KEY (`id_ingreso`),
  ADD KEY `fk_ingreso_compra1_idx` (`id_compra`),
  ADD KEY `fk_ingreso_almacen1_idx` (`id_almacen`),
  ADD KEY `fk_ingreso_ubicacion1_idx` (`id_ubicacion`);

--
-- Indices de la tabla `ingreso_detalle`
--
ALTER TABLE `ingreso_detalle`
  ADD PRIMARY KEY (`id_ingreso_detalle`),
  ADD KEY `fk_ingreso_detalle_ingreso1_idx` (`id_ingreso`),
  ADD KEY `fk_ingreso_detalle_producto1_idx` (`id_producto`);

--
-- Indices de la tabla `material_tipo`
--
ALTER TABLE `material_tipo`
  ADD PRIMARY KEY (`id_material_tipo`);

--
-- Indices de la tabla `medio_pago`
--
ALTER TABLE `medio_pago`
  ADD PRIMARY KEY (`id_medio_pago`);

--
-- Indices de la tabla `modulo`
--
ALTER TABLE `modulo`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `modulo_accion`
--
ALTER TABLE `modulo_accion`
  ADD PRIMARY KEY (`id_modulo_accion`);

--
-- Indices de la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`id_moneda`);

--
-- Indices de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `fk_movimiento_producto1_idx` (`id_producto`),
  ADD KEY `fk_movimiento_ubicacion1_idx` (`id_ubicacion`),
  ADD KEY `fk_movimiento_almacen1_idx` (`id_almacen`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_proveedor_cuenta` (`id_proveedor_cuenta`),
  ADD KEY `id_personal` (`id_personal`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `fk_pedido_almacen1_idx` (`id_almacen`),
  ADD KEY `fk_pedido_producto_tipo1_idx` (`id_producto_tipo`),
  ADD KEY `fk_pedido_ubicacion1_idx` (`id_ubicacion`),
  ADD KEY `idx_centro_costo` (`id_centro_costo`),
  ADD KEY `fk_pedido_obra` (`id_obra`);

--
-- Indices de la tabla `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  ADD PRIMARY KEY (`id_pedido_detalle`),
  ADD KEY `fk_pedido_detalle_pedido1_idx` (`id_pedido`),
  ADD KEY `fk_pedido_detalle_material1_idx` (`id_producto`);

--
-- Indices de la tabla `pedido_detalle_centro_costo`
--
ALTER TABLE `pedido_detalle_centro_costo`
  ADD PRIMARY KEY (`id_pedido_detalle_centro_costo`),
  ADD KEY `fk_pedido_detalle_cc_pedido_detalle` (`id_pedido_detalle`),
  ADD KEY `fk_pedido_detalle_cc_centro_costo` (`id_centro_costo`);

--
-- Indices de la tabla `pedido_detalle_documento`
--
ALTER TABLE `pedido_detalle_documento`
  ADD PRIMARY KEY (`id_pedido_detalle_documento`),
  ADD KEY `fk_pedido_detalle_documento_pedido_detalle1_idx` (`id_pedido_detalle`);

--
-- Indices de la tabla `pedido_detalle_personal`
--
ALTER TABLE `pedido_detalle_personal`
  ADD PRIMARY KEY (`id_pedido_detalle_personal`),
  ADD KEY `idx_pedido_detalle` (`id_pedido_detalle`),
  ADD KEY `idx_personal` (`id_personal`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`id_personal`),
  ADD KEY `fk_personal_cargo1_idx` (`id_cargo`),
  ADD KEY `fk_personal_area1_idx` (`id_area`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `fk_material_unidad_medida1_idx` (`id_unidad_medida`),
  ADD KEY `fk_material_material_tipo1_idx` (`id_material_tipo`),
  ADD KEY `fk_producto_producto_tipo1_idx` (`id_producto_tipo`);

--
-- Indices de la tabla `producto_tipo`
--
ALTER TABLE `producto_tipo`
  ADD PRIMARY KEY (`id_producto_tipo`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `proveedor_cuenta`
--
ALTER TABLE `proveedor_cuenta`
  ADD PRIMARY KEY (`id_proveedor_cuenta`),
  ADD KEY `id_proveedor` (`id_proveedor`),
  ADD KEY `fk_proveedor_cuenta_banco` (`id_banco`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `salida`
--
ALTER TABLE `salida`
  ADD PRIMARY KEY (`id_salida`),
  ADD KEY `fk_salida_almacen1_idx` (`id_almacen_origen`),
  ADD KEY `fk_salida_ubicacion1_idx` (`id_ubicacion_origen`),
  ADD KEY `fk_salida_pedido` (`id_pedido`),
  ADD KEY `fk_salida_personal_aprueba` (`id_personal_aprueba_salida`);

--
-- Indices de la tabla `salida_detalle`
--
ALTER TABLE `salida_detalle`
  ADD PRIMARY KEY (`id_salida_detalle`);

--
-- Indices de la tabla `subestacion`
--
ALTER TABLE `subestacion`
  ADD PRIMARY KEY (`id_subestacion`);

--
-- Indices de la tabla `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`id_tipo_documento`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`id_ubicacion`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`id_unidad_medida`);

--
-- Indices de la tabla `uso_material`
--
ALTER TABLE `uso_material`
  ADD PRIMARY KEY (`id_uso_material`);

--
-- Indices de la tabla `uso_material_detalle`
--
ALTER TABLE `uso_material_detalle`
  ADD PRIMARY KEY (`id_uso_material_detalle`);

--
-- Indices de la tabla `uso_material_detalle_documento`
--
ALTER TABLE `uso_material_detalle_documento`
  ADD PRIMARY KEY (`id_uso_material_detalle_documento`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usu_usuario_UNIQUE` (`usu_usuario`),
  ADD KEY `fk_usuario_personal1_idx` (`id_personal`);

--
-- Indices de la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD PRIMARY KEY (`id_usuario_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accion`
--
ALTER TABLE `accion`
  MODIFY `id_accion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `almacen`
--
ALTER TABLE `almacen`
  MODIFY `id_almacen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `area`
--
ALTER TABLE `area`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `banco`
--
ALTER TABLE `banco`
  MODIFY `id_banco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000002;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000001;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  MODIFY `id_compra_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  MODIFY `id_comprobante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comprobante_pago`
--
ALTER TABLE `comprobante_pago`
  MODIFY `id_comprobante_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detraccion`
--
ALTER TABLE `detraccion`
  MODIFY `id_detraccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `detraccion_tipo`
--
ALTER TABLE `detraccion_tipo`
  MODIFY `id_detraccion_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  MODIFY `id_devolucion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devolucion_detalle`
--
ALTER TABLE `devolucion_detalle`
  MODIFY `id_devolucion_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_doc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingreso`
--
ALTER TABLE `ingreso`
  MODIFY `id_ingreso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingreso_detalle`
--
ALTER TABLE `ingreso_detalle`
  MODIFY `id_ingreso_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `material_tipo`
--
ALTER TABLE `material_tipo`
  MODIFY `id_material_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `medio_pago`
--
ALTER TABLE `medio_pago`
  MODIFY `id_medio_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `modulo`
--
ALTER TABLE `modulo`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `modulo_accion`
--
ALTER TABLE `modulo_accion`
  MODIFY `id_modulo_accion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de la tabla `moneda`
--
ALTER TABLE `moneda`
  MODIFY `id_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  MODIFY `id_pedido_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_detalle_centro_costo`
--
ALTER TABLE `pedido_detalle_centro_costo`
  MODIFY `id_pedido_detalle_centro_costo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_detalle_documento`
--
ALTER TABLE `pedido_detalle_documento`
  MODIFY `id_pedido_detalle_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_detalle_personal`
--
ALTER TABLE `pedido_detalle_personal`
  MODIFY `id_pedido_detalle_personal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=853;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id_personal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000001;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1298;

--
-- AUTO_INCREMENT de la tabla `producto_tipo`
--
ALTER TABLE `producto_tipo`
  MODIFY `id_producto_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT de la tabla `proveedor_cuenta`
--
ALTER TABLE `proveedor_cuenta`
  MODIFY `id_proveedor_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=353;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `salida`
--
ALTER TABLE `salida`
  MODIFY `id_salida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `salida_detalle`
--
ALTER TABLE `salida_detalle`
  MODIFY `id_salida_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `subestacion`
--
ALTER TABLE `subestacion`
  MODIFY `id_subestacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000006;

--
-- AUTO_INCREMENT de la tabla `tipo`
--
ALTER TABLE `tipo`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000001;

--
-- AUTO_INCREMENT de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `id_tipo_documento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `id_ubicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id_unidad_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `uso_material_detalle`
--
ALTER TABLE `uso_material_detalle`
  MODIFY `id_uso_material_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `uso_material_detalle_documento`
--
ALTER TABLE `uso_material_detalle_documento`
  MODIFY `id_uso_material_detalle_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  MODIFY `id_usuario_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`id_detraccion`) REFERENCES `detraccion` (`id_detraccion`),
  ADD CONSTRAINT `fk_compra_moneda1` FOREIGN KEY (`id_moneda`) REFERENCES `moneda` (`id_moneda`),
  ADD CONSTRAINT `fk_compra_pedido1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`),
  ADD CONSTRAINT `fk_compra_percepcion` FOREIGN KEY (`id_percepcion`) REFERENCES `detraccion` (`id_detraccion`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_compra_proveedor1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`),
  ADD CONSTRAINT `fk_compra_retencion` FOREIGN KEY (`id_retencion`) REFERENCES `detraccion` (`id_detraccion`) ON DELETE SET NULL;

--
-- Filtros para la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  ADD CONSTRAINT `fk_compra_detalle_compra1` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`),
  ADD CONSTRAINT `fk_compra_detalle_material1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD CONSTRAINT `comprobante_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`),
  ADD CONSTRAINT `comprobante_ibfk_2` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documento` (`id_tipo_documento`),
  ADD CONSTRAINT `comprobante_ibfk_3` FOREIGN KEY (`id_moneda`) REFERENCES `moneda` (`id_moneda`),
  ADD CONSTRAINT `comprobante_ibfk_4` FOREIGN KEY (`id_medio_pago`) REFERENCES `medio_pago` (`id_medio_pago`),
  ADD CONSTRAINT `comprobante_ibfk_5` FOREIGN KEY (`id_detraccion`) REFERENCES `detraccion` (`id_detraccion`);

--
-- Filtros para la tabla `detraccion`
--
ALTER TABLE `detraccion`
  ADD CONSTRAINT `fk_detraccion_tipo` FOREIGN KEY (`id_detraccion_tipo`) REFERENCES `detraccion_tipo` (`id_detraccion_tipo`);

--
-- Filtros para la tabla `ingreso`
--
ALTER TABLE `ingreso`
  ADD CONSTRAINT `fk_ingreso_almacen1` FOREIGN KEY (`id_almacen`) REFERENCES `almacen` (`id_almacen`),
  ADD CONSTRAINT `fk_ingreso_compra1` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`),
  ADD CONSTRAINT `fk_ingreso_ubicacion1` FOREIGN KEY (`id_ubicacion`) REFERENCES `ubicacion` (`id_ubicacion`);

--
-- Filtros para la tabla `ingreso_detalle`
--
ALTER TABLE `ingreso_detalle`
  ADD CONSTRAINT `fk_ingreso_detalle_ingreso1` FOREIGN KEY (`id_ingreso`) REFERENCES `ingreso` (`id_ingreso`),
  ADD CONSTRAINT `fk_ingreso_detalle_producto1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `fk_movimiento_almacen1` FOREIGN KEY (`id_almacen`) REFERENCES `almacen` (`id_almacen`),
  ADD CONSTRAINT `fk_movimiento_producto1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `fk_movimiento_ubicacion1` FOREIGN KEY (`id_ubicacion`) REFERENCES `ubicacion` (`id_ubicacion`);

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_pedido_almacen1` FOREIGN KEY (`id_almacen`) REFERENCES `almacen` (`id_almacen`),
  ADD CONSTRAINT `fk_pedido_producto_tipo1` FOREIGN KEY (`id_producto_tipo`) REFERENCES `producto_tipo` (`id_producto_tipo`),
  ADD CONSTRAINT `fk_pedido_ubicacion1` FOREIGN KEY (`id_ubicacion`) REFERENCES `ubicacion` (`id_ubicacion`);

--
-- Filtros para la tabla `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  ADD CONSTRAINT `fk_pedido_detalle_material1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `fk_pedido_detalle_pedido1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`);

--
-- Filtros para la tabla `pedido_detalle_centro_costo`
--
ALTER TABLE `pedido_detalle_centro_costo`
  ADD CONSTRAINT `fk_pedido_detalle_cc_pedido_detalle` FOREIGN KEY (`id_pedido_detalle`) REFERENCES `pedido_detalle` (`id_pedido_detalle`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedido_detalle_documento`
--
ALTER TABLE `pedido_detalle_documento`
  ADD CONSTRAINT `fk_pedido_detalle_documento_pedido_detalle1` FOREIGN KEY (`id_pedido_detalle`) REFERENCES `pedido_detalle` (`id_pedido_detalle`);

--
-- Filtros para la tabla `pedido_detalle_personal`
--
ALTER TABLE `pedido_detalle_personal`
  ADD CONSTRAINT `fk_pedido_detalle_personal_pedido_detalle` FOREIGN KEY (`id_pedido_detalle`) REFERENCES `pedido_detalle` (`id_pedido_detalle`) ON DELETE CASCADE;

--
-- Filtros para la tabla `personal`
--
ALTER TABLE `personal`
  ADD CONSTRAINT `fk_personal_area1` FOREIGN KEY (`id_area`) REFERENCES `area` (`id_area`),
  ADD CONSTRAINT `fk_personal_cargo1` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_material_material_tipo1` FOREIGN KEY (`id_material_tipo`) REFERENCES `material_tipo` (`id_material_tipo`),
  ADD CONSTRAINT `fk_material_unidad_medida1` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidad_medida` (`id_unidad_medida`),
  ADD CONSTRAINT `fk_producto_producto_tipo1` FOREIGN KEY (`id_producto_tipo`) REFERENCES `producto_tipo` (`id_producto_tipo`);

--
-- Filtros para la tabla `proveedor_cuenta`
--
ALTER TABLE `proveedor_cuenta`
  ADD CONSTRAINT `fk_proveedor_cuenta_banco` FOREIGN KEY (`id_banco`) REFERENCES `banco` (`id_banco`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `proveedor_cuenta_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `salida`
--
ALTER TABLE `salida`
  ADD CONSTRAINT `fk_salida_almacen1` FOREIGN KEY (`id_almacen_origen`) REFERENCES `almacen` (`id_almacen`),
  ADD CONSTRAINT `fk_salida_ubicacion1` FOREIGN KEY (`id_ubicacion_origen`) REFERENCES `ubicacion` (`id_ubicacion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

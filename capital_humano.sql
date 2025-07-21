-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 20-07-2025 a las 22:23:47
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `capital_humano`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_tokens`
--

DROP TABLE IF EXISTS `api_tokens`;
CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_hash` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permisos` json DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `expira_en` timestamp NULL DEFAULT NULL,
  `ultimo_uso` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `idx_token_hash` (`token_hash`),
  KEY `idx_entidad` (`entidad`),
  KEY `idx_activo` (`activo`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `api_tokens`
--

INSERT INTO `api_tokens` (`id`, `token_name`, `token_hash`, `entidad`, `permisos`, `activo`, `expira_en`, `ultimo_uso`, `created_at`) VALUES
(1, 'Contraloria_Stats', '7e9faca6315035a4b06681feb58523563ab9f215fbf0b7c1f9d2089a', 'Contraloria_General', '[\"estadisticas.colaboradores\", \"reportes.genero\"]', 1, '2026-06-30 21:58:37', NULL, '2025-06-30 21:58:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calculo_vacaciones_log`
--

DROP TABLE IF EXISTS `calculo_vacaciones_log`;
CREATE TABLE IF NOT EXISTS `calculo_vacaciones_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `colaborador_id` int NOT NULL,
  `periodo_inicio` date NOT NULL,
  `periodo_fin` date NOT NULL,
  `dias_trabajados` int NOT NULL,
  `dias_ganados` decimal(5,2) NOT NULL,
  `calculo_realizado_por` enum('Sistema','Manual') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Sistema',
  `usuario_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `colaborador_id` (`colaborador_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `calculo_vacaciones_log`
--

INSERT INTO `calculo_vacaciones_log` (`id`, `colaborador_id`, `periodo_inicio`, `periodo_fin`, `dias_trabajados`, `dias_ganados`, `calculo_realizado_por`, `usuario_id`, `created_at`) VALUES
(1, 1, '2021-01-10', '2025-07-17', 0, 0.00, 'Sistema', NULL, '2025-07-18 04:34:49'),
(2, 2, '2025-01-19', '2025-07-19', 0, 0.00, 'Sistema', NULL, '2025-07-19 23:56:12'),
(3, 3, '2025-06-01', '2025-07-19', 0, 0.00, 'Sistema', NULL, '2025-07-20 02:04:07'),
(4, 4, '2025-06-01', '2025-07-19', 0, 0.00, 'Sistema', NULL, '2025-07-20 02:08:24'),
(5, 5, '2010-06-22', '2025-07-19', 0, 0.00, 'Sistema', NULL, '2025-07-20 03:49:10'),
(6, 6, '2010-06-09', '2025-07-19', 0, 0.00, 'Sistema', NULL, '2025-07-20 04:12:30'),
(7, 7, '2010-06-09', '2025-07-19', 0, 0.00, 'Sistema', NULL, '2025-07-20 04:15:45'),
(8, 8, '2025-07-23', '2025-07-20', 0, 0.00, 'Sistema', NULL, '2025-07-20 05:26:37'),
(9, 9, '2025-07-02', '2025-07-20', 0, 0.00, 'Sistema', NULL, '2025-07-20 05:45:33'),
(10, 10, '2025-07-15', '2025-07-20', 0, 0.00, 'Sistema', NULL, '2025-07-20 05:59:46'),
(11, 11, '2025-07-08', '2025-07-20', 0, 0.00, 'Sistema', NULL, '2025-07-20 06:15:15'),
(12, 12, '2025-07-08', '2025-07-20', 0, 0.00, 'Sistema', NULL, '2025-07-20 06:18:05'),
(13, 13, '2025-07-14', '2025-07-20', 0, 0.00, 'Sistema', NULL, '2025-07-20 06:59:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

DROP TABLE IF EXISTS `cargos`;
CREATE TABLE IF NOT EXISTS `cargos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'Programador Junior', 'Nivel inicial para programación y soporte', '2025-07-20 02:57:37'),
(2, 'Programador Senior', 'Encargado de proyectos y liderazgo técnico', '2025-07-20 02:57:37'),
(3, 'Analista de Sistemas', 'Analiza y diseña soluciones informáticas', '2025-07-20 02:57:37'),
(4, 'Jefe de Proyectos', 'Coordina y lidera equipos de desarrollo', '2025-07-20 02:57:37'),
(5, 'Gerente de Tecnología', 'Planifica y supervisa las operaciones TI', '2025-07-20 02:57:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos_historico`
--

DROP TABLE IF EXISTS `cargos_historico`;
CREATE TABLE IF NOT EXISTS `cargos_historico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `colaborador_id` int NOT NULL,
  `cargo_anterior_id` int DEFAULT NULL,
  `cargo_nuevo_id` int NOT NULL,
  `sueldo_anterior` decimal(10,2) DEFAULT NULL,
  `sueldo_nuevo` decimal(10,2) NOT NULL,
  `departamento_anterior_id` int DEFAULT NULL,
  `departamento_nuevo_id` int NOT NULL,
  `tipo_movimiento` enum('Contratacion','Ascenso','Promocion','Traslado','Ajuste_Salarial') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_efectiva` date NOT NULL,
  `firma_digital` text COLLATE utf8mb4_unicode_ci,
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `usuario_registro_id` int DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `departamento_anterior_id` (`departamento_anterior_id`),
  KEY `departamento_nuevo_id` (`departamento_nuevo_id`),
  KEY `usuario_registro_id` (`usuario_registro_id`),
  KEY `idx_colaborador` (`colaborador_id`),
  KEY `idx_fecha_efectiva` (`fecha_efectiva`),
  KEY `idx_activo` (`activo`),
  KEY `fk_cargos_historico_anterior` (`cargo_anterior_id`),
  KEY `fk_cargos_historico_nuevo` (`cargo_nuevo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cargos_historico`
--

INSERT INTO `cargos_historico` (`id`, `colaborador_id`, `cargo_anterior_id`, `cargo_nuevo_id`, `sueldo_anterior`, `sueldo_nuevo`, `departamento_anterior_id`, `departamento_nuevo_id`, `tipo_movimiento`, `fecha_efectiva`, `firma_digital`, `motivo`, `usuario_registro_id`, `activo`, `created_at`) VALUES
(5, 12, NULL, 1, NULL, 5678.00, NULL, 4, 'Contratacion', '2025-07-08', 'CKNN4Hp6I1X7QO9xrYyDhrFUQdkAZcMT/hSnDCeSmyrj555tb3FypnFAViqk8hzii20CzFfiHBc7/h4uG2NRHM1EUbJSFi1FoUp3Q0SeNvKj6AX7f75kJxQk7XT3mL2y2P5ROOpXOEWIZ9ElSlYk6N0NJ3vc2uDgkfu/+iEMOljjedJzDuv5I/n+GjAcDi+xfXWM+nHnCZ+/21YVdK44oO++qCxkbOeW4bI1N1EoC05ioAbY6UFUEM6kl219DeY1VkO9RK2tnU1/3LFV/v/jaDD1wRNpnscEuh3n1RYyLv/+p7csuHSPQKj4GbwJ476+hwhmp+7JxLKmtdzdjt4cjw==', NULL, NULL, 1, '2025-07-20 06:18:05'),
(34, 12, 1, 1, 5678.00, 45678.00, 4, 4, 'Ajuste_Salarial', '2025-07-20', 'cSYhA4I7FKtrkeibPyv5jgrHehI7sORtdKnNGX3qSBaMdMQHWZnjzVRLU8FMG7l5gyHXad+Yd03KtgVDQzAL2mIubQ1Och5Z3zSV8Qkx+KHspAq/pLRDea4iJTyAroC+qmuqMeo/gFxmrBoI2TcxY6iaLZ/r2Y5TjsboFMKg7ZL/smGHQ9QvWeFnpKpcV3bakFImvosDq5kzVPn+iYtjr/7RGuAA5rVUAmGFEPqjE8Z/bHcwzZhbCObOBk96VO7gWXmzT6VPl0/xbb+N2/dj8WLoBU/fMe7f93iY1ydCipJcXPfGf2GwTXZddJ0XbKM1AUJRgAe+Gcp4L/ZfTfJ8Pw==', 'asd', 1, 1, '2025-07-20 08:56:11'),
(9, 13, NULL, 5, NULL, 1000.00, NULL, 5, 'Contratacion', '2025-07-14', 'btUsKdZWR70+wL257TrLGtZNJeJkizPdK9plVtXkusZoCiexzIofu8n+WouSLImhwrnwloEsWGXSgc7kQhFSn+ogllLLPFgpXdAv0FAnxDW9o+5nzP4ZjVF1DxXx7yU4HoU8c6N1DT7cd5GfB17HddlVUBdziJIk2WDfbfN4/ATvRx5W9JueBTGXPxFhK+pZL7DECWtADbIVo2/cyc1dY2uQCDrqRVgI79RsQwbi74x2PpoJ4Eq3B5WYdzk1U+zHfU3OadQBKTwt0dbS0k1b3eKVpEaulQtoIe2WVfQJWdh8hZNNEHEyF1pDmFroRZftf95IQQVWw8gM9Mpw/93KeA==', NULL, 1, 1, '2025-07-20 06:59:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colaboradores`
--

DROP TABLE IF EXISTS `colaboradores`;
CREATE TABLE IF NOT EXISTS `colaboradores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `primer_nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `segundo_nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primer_apellido` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `segundo_apellido` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cedula` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexo` enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `correo_personal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sueldo` decimal(10,2) NOT NULL,
  `departamento_id` int NOT NULL,
  `fecha_contratacion` date NOT NULL,
  `empleado_activo` tinyint(1) DEFAULT '1',
  `tipo_empleado` enum('Permanente','Eventual','Interno') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ocupacion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_actual_id` int DEFAULT NULL,
  `foto_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_vacaciones_acumulados` decimal(5,2) DEFAULT '0.00',
  `ultima_actualizacion_vacaciones` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cedula` (`cedula`),
  KEY `idx_cedula` (`cedula`),
  KEY `idx_nombres` (`primer_nombre`,`primer_apellido`),
  KEY `idx_departamento` (`departamento_id`),
  KEY `idx_empleado_activo` (`empleado_activo`),
  KEY `idx_sexo` (`sexo`),
  KEY `idx_tipo_empleado` (`tipo_empleado`),
  KEY `fk_colaboradores_cargo` (`cargo_actual_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `colaboradores`
--

INSERT INTO `colaboradores` (`id`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `sexo`, `fecha_nacimiento`, `telefono`, `celular`, `direccion`, `correo_personal`, `sueldo`, `departamento_id`, `fecha_contratacion`, `empleado_activo`, `tipo_empleado`, `ocupacion`, `cargo_actual_id`, `foto_perfil`, `dias_vacaciones_acumulados`, `ultima_actualizacion_vacaciones`, `created_at`, `updated_at`) VALUES
(1, 'Carlos', 'Eduardo', 'Pérez', 'Gómez', '8-765-1234', 'M', '1994-12-14', '229-4567', '6100-1234', 'Calle 50, Panamá', 'carlos.perez@example.com', 1200.00, 1, '2021-01-10', 1, 'Permanente', 'Analista de RRHH', 1, 'uploads/fotos/6879d002cb9c9.jpg', 0.00, NULL, '2025-07-18 04:34:49', '2025-07-20 03:01:33'),
(2, 'Laura', 'Sofía', 'Saucedo', 'García', '7-999-999', 'F', '2004-04-12', '129-4567', '6400-1224', 'Via argentina', 'lausfg@gmail.com', 1200.00, 1, '2025-01-19', 1, 'Permanente', 'Analista de RRHH', 1, NULL, 0.00, NULL, '2025-07-19 23:56:12', '2025-07-20 03:01:30'),
(4, 'Faello', 'Esteban', 'Torres', 'Valdes', '8-94757-43', 'M', '2000-06-19', '229-4567', '6100-1234', 'Argentina', 'faello12@example.com', 750.00, 3, '2025-06-01', 1, 'Eventual', 'Economista', NULL, NULL, 0.00, NULL, '2025-07-20 02:08:24', '2025-07-20 02:08:24'),
(7, 'Juan', 'Alberto', 'Puchuar', 'Castillo', '7-9998-88', 'M', '1991-06-06', '129-4567', '6400-1224', 'España', 'juanalberto@gmail.com', 5000.00, 5, '2010-06-09', 1, 'Interno', 'Nada', 4, NULL, 0.00, NULL, '2025-07-20 04:15:45', '2025-07-20 04:15:45'),
(12, 'Pepe', 'Colindio', 'jr', 'cabales', '9-2977-475', 'M', '2017-02-06', '129-4567', '', 'west', 'coidio@gmail.com', 45678.00, 4, '2025-07-08', 1, 'Eventual', 'Nada', 1, NULL, 0.00, NULL, '2025-07-20 06:18:05', '2025-07-20 08:56:11'),
(13, 'Gabriel', 'Andres', 'Dominguez', 'Velazques', '8-9876-994', 'M', '2001-08-20', '129-4567', '6100-1234', 'Los andes', 'gabdominguez@gmail.com', 123456.00, 4, '2025-07-14', 1, 'Permanente', 'Electrico', 4, NULL, 0.00, NULL, '2025-07-20 06:59:24', '2025-07-20 08:39:15');

--
-- Disparadores `colaboradores`
--
DROP TRIGGER IF EXISTS `actualizar_vacaciones_after_insert`;
DELIMITER $$
CREATE TRIGGER `actualizar_vacaciones_after_insert` AFTER INSERT ON `colaboradores` FOR EACH ROW BEGIN
    INSERT INTO calculo_vacaciones_log 
    (colaborador_id, periodo_inicio, periodo_fin, dias_trabajados, dias_ganados, calculo_realizado_por)
    VALUES 
    (NEW.id, NEW.fecha_contratacion, CURDATE(), 0, 0, 'Sistema');
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `registrar_cambio_cargo`;
DELIMITER $$
CREATE TRIGGER `registrar_cambio_cargo` AFTER UPDATE ON `colaboradores` FOR EACH ROW BEGIN
    IF OLD.ocupacion != NEW.ocupacion OR OLD.sueldo != NEW.sueldo OR OLD.departamento_id != NEW.departamento_id THEN
        INSERT INTO cargos_historico 
        (colaborador_id, cargo_anterior, cargo_nuevo, sueldo_anterior, sueldo_nuevo, 
         departamento_anterior_id, departamento_nuevo_id, tipo_movimiento, fecha_efectiva)
        VALUES 
        (NEW.id, OLD.ocupacion, NEW.ocupacion, OLD.sueldo, NEW.sueldo,
         OLD.departamento_id, NEW.departamento_id, 'Ajuste_Salarial', CURDATE());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
CREATE TABLE IF NOT EXISTS `departamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `jefe_departamento_id` int DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_activo` (`activo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `nombre`, `descripcion`, `jefe_departamento_id`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Recursos Humanos', 'Departamento de gestión de personal', NULL, 1, '2025-06-30 21:58:37', '2025-06-30 21:58:37'),
(2, 'Administración', 'Departamento administrativo', NULL, 1, '2025-06-30 21:58:37', '2025-06-30 21:58:37'),
(3, 'Finanzas', 'Departamento de finanzas y contabilidad', NULL, 1, '2025-06-30 21:58:37', '2025-06-30 21:58:37'),
(4, 'Tecnología', 'Departamento de sistemas y tecnología', NULL, 1, '2025-06-30 21:58:37', '2025-06-30 21:58:37'),
(5, 'Operaciones', 'Departamento operativo', NULL, 1, '2025-06-30 21:58:37', '2025-06-30 21:58:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_academicos`
--

DROP TABLE IF EXISTS `documentos_academicos`;
CREATE TABLE IF NOT EXISTS `documentos_academicos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `colaborador_id` int NOT NULL,
  `tipo_documento` enum('Diploma','Certificado','Titulo','Transcript','Otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_documento` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `institucion` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `archivo_pdf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `verificado` tinyint(1) DEFAULT '0',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_colaborador` (`colaborador_id`),
  KEY `idx_tipo` (`tipo_documento`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos_academicos`
--

INSERT INTO `documentos_academicos` (`id`, `colaborador_id`, `tipo_documento`, `nombre_documento`, `institucion`, `fecha_emision`, `archivo_pdf`, `verificado`, `observaciones`, `uploaded_at`) VALUES
(1, 1, 'Diploma', 'Bachiller en Ciencias', 'Instituto América', '2019-12-19', 'uploads/pdf/6879d2b03da3a.pdf', 0, NULL, '2025-07-18 04:50:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estatus_colaborador`
--

DROP TABLE IF EXISTS `estatus_colaborador`;
CREATE TABLE IF NOT EXISTS `estatus_colaborador` (
  `id` int NOT NULL AUTO_INCREMENT,
  `colaborador_id` int NOT NULL,
  `estatus` enum('Activo','Vacaciones','Licencia','Incapacitado','Suspendido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `documento_soporte` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_registro_id` int DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_registro_id` (`usuario_registro_id`),
  KEY `idx_colaborador` (`colaborador_id`),
  KEY `idx_estatus` (`estatus`),
  KEY `idx_fechas` (`fecha_inicio`,`fecha_fin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

DROP TABLE IF EXISTS `permisos`;
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `modulo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `modulo`, `descripcion`, `created_at`) VALUES
(1, 'usuarios.acceso', 'usuarios', 'Acceso completo al módulo Usuarios', '2025-07-17 03:01:39'),
(2, 'colaboradores.acceso', 'colaboradores', 'Acceso completo al módulo Colaboradores', '2025-07-17 03:01:39'),
(3, 'vacaciones.acceso', 'vacaciones', 'Acceso completo al módulo Vacaciones', '2025-07-17 03:01:39'),
(4, 'cargos.acceso', 'cargos', 'Acceso completo al módulo Cargos/Movimientos', '2025-07-17 03:01:39'),
(5, 'reportes.acceso', 'reportes', 'Acceso completo al módulo Reportes', '2025-07-17 03:01:39'),
(6, 'estadisticas.acceso', 'estadisticas', 'Acceso completo al módulo Estadísticas', '2025-07-17 03:01:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Super_Admin', 'Administrador con acceso completo al sistema', 1, '2025-06-30 21:58:37', '2025-06-30 21:58:37'),
(2, 'Admin_RRHH', 'Administrador de Recursos Humanos', 1, '2025-06-30 21:58:37', '2025-06-30 21:58:37'),
(3, 'Supervisor', 'Supervisor con acceso a reportes', 1, '2025-06-30 21:58:37', '2025-06-30 21:58:37'),
(4, 'Operador', 'Operador con acceso limitado', 1, '2025-06-30 21:58:37', '2025-06-30 21:58:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

DROP TABLE IF EXISTS `rol_permisos`;
CREATE TABLE IF NOT EXISTS `rol_permisos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol_id` int NOT NULL,
  `permiso_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rol_permiso` (`rol_id`,`permiso_id`),
  KEY `permiso_id` (`permiso_id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`id`, `rol_id`, `permiso_id`, `created_at`) VALUES
(45, 4, 2, '2025-07-17 05:45:27'),
(44, 3, 6, '2025-07-17 05:45:27'),
(43, 3, 5, '2025-07-17 05:45:27'),
(42, 2, 6, '2025-07-17 05:45:27'),
(41, 2, 5, '2025-07-17 05:45:27'),
(40, 2, 4, '2025-07-17 05:45:27'),
(39, 2, 3, '2025-07-17 05:45:27'),
(38, 2, 2, '2025-07-17 05:45:27'),
(37, 2, 1, '2025-07-17 04:34:27'),
(36, 1, 6, '2025-07-17 03:02:16'),
(35, 1, 5, '2025-07-17 03:02:16'),
(34, 1, 4, '2025-07-17 03:02:16'),
(33, 1, 3, '2025-07-17 03:02:16'),
(32, 1, 2, '2025-07-17 03:02:16'),
(31, 1, 1, '2025-07-17 03:02:16'),
(46, 4, 3, '2025-07-17 05:45:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol_id` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `intentos_fallidos` int DEFAULT '0',
  `bloqueado_hasta` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `rol_id` (`rol_id`),
  KEY `idx_username` (`username`),
  KEY `idx_activo` (`activo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `email`, `rol_id`, `activo`, `ultimo_login`, `intentos_fallidos`, `bloqueado_hasta`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@empresa.com', 1, 1, '2025-07-20 20:26:44', 0, NULL, '2025-06-30 21:58:37', '2025-07-20 20:26:44'),
(2, 'PtoAmo', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'Amo@example.com', 1, 1, '2025-07-16 23:44:47', 7, '2025-07-17 03:32:51', '2025-07-16 23:44:47', '2025-07-17 03:43:41'),
(3, 'Samir', '$2y$10$7pe/EVaMIVYsa7xWu6jLEu223Br1BuB2Hx27JOkIGutfjZ3IBihPq', 'samirito@utp.ac.pa', 1, 1, '2025-07-17 05:39:11', 0, NULL, '2025-07-17 03:56:45', '2025-07-17 05:39:11'),
(5, 'khike', '$2y$10$oxfzFtoDLI.a4FMz5d4OzOmZ3Nt1QqTIUW8t/rZPSdoJLshJwVuV2', 'khikinho@utp.ac.pa', 3, 1, NULL, 0, NULL, '2025-07-17 06:14:46', '2025-07-17 06:14:46'),
(4, 'Gus', '$2y$10$mk9sGhdPU2mVwxn/3gayu.2PkBKgVCLiowb8iwdXcrq2Hkb30Y1ZO', 'gueh@gmail.com', 4, 1, '2025-07-17 04:48:03', 0, NULL, '2025-07-17 04:12:30', '2025-07-17 06:14:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacaciones`
--

DROP TABLE IF EXISTS `vacaciones`;
CREATE TABLE IF NOT EXISTS `vacaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `colaborador_id` int NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `dias_solicitados` int NOT NULL,
  `dias_disponibles_al_momento` decimal(5,2) NOT NULL,
  `estado` enum('Pendiente','Aprobada','Rechazada','Cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `motivo_rechazo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `aprobada_por_usuario_id` int DEFAULT NULL,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `aprobada_por_usuario_id` (`aprobada_por_usuario_id`),
  KEY `idx_colaborador` (`colaborador_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fechas` (`fecha_inicio`,`fecha_fin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_colaboradores_activos`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `v_colaboradores_activos`;
CREATE TABLE IF NOT EXISTS `v_colaboradores_activos` (
`id` int
,`nombre_completo` varchar(203)
,`cedula` varchar(20)
,`sexo` enum('M','F')
,`fecha_nacimiento` date
,`edad` bigint
,`telefono` varchar(15)
,`celular` varchar(15)
,`correo_personal` varchar(100)
,`sueldo` decimal(10,2)
,`departamento` varchar(100)
,`fecha_contratacion` date
,`dias_trabajados` bigint
,`tipo_empleado` enum('Permanente','Eventual','Interno')
,`ocupacion` varchar(100)
,`dias_vacaciones_acumulados` decimal(5,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_estadisticas_departamento`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `v_estadisticas_departamento`;
CREATE TABLE IF NOT EXISTS `v_estadisticas_departamento` (
`departamento` varchar(100)
,`total_colaboradores` bigint
,`hombres` decimal(23,0)
,`mujeres` decimal(23,0)
,`sueldo_promedio` decimal(14,6)
,`sueldo_minimo` decimal(10,2)
,`sueldo_maximo` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_colaboradores_activos`
--
DROP TABLE IF EXISTS `v_colaboradores_activos`;

DROP VIEW IF EXISTS `v_colaboradores_activos`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_colaboradores_activos`  AS SELECT `c`.`id` AS `id`, concat(`c`.`primer_nombre`,' ',ifnull(`c`.`segundo_nombre`,''),' ',`c`.`primer_apellido`,' ',ifnull(`c`.`segundo_apellido`,'')) AS `nombre_completo`, `c`.`cedula` AS `cedula`, `c`.`sexo` AS `sexo`, `c`.`fecha_nacimiento` AS `fecha_nacimiento`, timestampdiff(YEAR,`c`.`fecha_nacimiento`,curdate()) AS `edad`, `c`.`telefono` AS `telefono`, `c`.`celular` AS `celular`, `c`.`correo_personal` AS `correo_personal`, `c`.`sueldo` AS `sueldo`, `d`.`nombre` AS `departamento`, `c`.`fecha_contratacion` AS `fecha_contratacion`, timestampdiff(DAY,`c`.`fecha_contratacion`,curdate()) AS `dias_trabajados`, `c`.`tipo_empleado` AS `tipo_empleado`, `c`.`ocupacion` AS `ocupacion`, `c`.`dias_vacaciones_acumulados` AS `dias_vacaciones_acumulados` FROM (`colaboradores` `c` join `departamentos` `d` on((`c`.`departamento_id` = `d`.`id`))) WHERE (`c`.`empleado_activo` = 1) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_estadisticas_departamento`
--
DROP TABLE IF EXISTS `v_estadisticas_departamento`;

DROP VIEW IF EXISTS `v_estadisticas_departamento`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_estadisticas_departamento`  AS SELECT `d`.`nombre` AS `departamento`, count(`c`.`id`) AS `total_colaboradores`, sum((case when (`c`.`sexo` = 'M') then 1 else 0 end)) AS `hombres`, sum((case when (`c`.`sexo` = 'F') then 1 else 0 end)) AS `mujeres`, avg(`c`.`sueldo`) AS `sueldo_promedio`, min(`c`.`sueldo`) AS `sueldo_minimo`, max(`c`.`sueldo`) AS `sueldo_maximo` FROM (`colaboradores` `c` join `departamentos` `d` on((`c`.`departamento_id` = `d`.`id`))) WHERE (`c`.`empleado_activo` = 1) GROUP BY `d`.`id`, `d`.`nombre` ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

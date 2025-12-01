-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-12-2025 a las 05:11:06
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
-- Base de datos: `inventario_sistema`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `agregar_inventario` (IN `p_usuario_id` INT, IN `p_codigo` VARCHAR(100), IN `p_nombre` VARCHAR(200), IN `p_desc` TEXT, IN `p_categoria` INT, IN `p_tipo` ENUM('equipo','insumo'), IN `p_cantidad` INT)   BEGIN
    DECLARE permitido INT;

    -- Verificar si el usuario tiene permiso para agregar inventario
    CALL verificar_permiso(p_usuario_id, 'inventario_insert');
    
    -- Asignar el valor de 'permitido' de la consulta
    SELECT permitido INTO permitido
    FROM permisos
    WHERE rol_id = (SELECT rol_id FROM usuarios WHERE id = p_usuario_id)
      AND accion = 'inventario_insert';

    IF permitido = 0 THEN 
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Permiso denegado';
    END IF;

    -- Si tiene permiso, insertar en la tabla inventario
    INSERT INTO inventario (codigo, nombre, descripcion, categoria_id, tipo, cantidad)
    VALUES (p_codigo, p_nombre, p_desc, p_categoria, p_tipo, p_cantidad);

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrar_movimiento` (IN `p_usuario_id` INT, IN `p_inventario_id` INT, IN `p_tipo` ENUM('ingreso','salida','prestamo','devolucion'), IN `p_cantidad` INT, IN `p_desc` TEXT)   BEGIN
    DECLARE permitido INT;

    -- Verificar si el usuario tiene permiso para registrar movimiento
    CALL verificar_permiso(p_usuario_id, 'movimientos_crud');
    
    -- Asignar el valor de 'permitido' de la consulta
    SELECT permitido INTO permitido
    FROM permisos
    WHERE rol_id = (SELECT rol_id FROM usuarios WHERE id = p_usuario_id)
      AND accion = 'movimientos_crud';

    -- Comprobar si tiene permiso
    IF permitido = 0 THEN 
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No tiene permiso para registrar movimientos';
    END IF;

    -- Si tiene permiso, insertar el movimiento en la tabla de movimientos
    INSERT INTO movimientos (inventario_id, usuario_id, tipo, cantidad, descripcion)
    VALUES (p_inventario_id, p_usuario_id, p_tipo, p_cantidad, p_desc);

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `verificar_permiso` (IN `p_usuario_id` INT, IN `p_accion` VARCHAR(100))   BEGIN
    DECLARE v_rol INT;
    DECLARE v_permitido INT DEFAULT 0;

    SELECT rol_id INTO v_rol
    FROM usuarios
    WHERE id = p_usuario_id;

    SELECT permitido INTO v_permitido
    FROM permisos
    WHERE rol_id = v_rol AND accion = p_accion;

    SELECT v_permitido AS permitido;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(200) NOT NULL,
  `detalle` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_origen` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'equipo'),
(2, 'insumo'),
(3, 'periferiscos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo','mantenimiento') NOT NULL DEFAULT 'activo',
  `categoria_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT 0,
  `tipo` enum('equipo','insumo') NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `codigo`, `nombre`, `descripcion`, `estado`, `categoria_id`, `cantidad`, `tipo`, `ubicacion`, `fecha_actualizacion`, `fecha_registro`) VALUES
(1, '0001', 'torre', 'torre servidor hp', 'activo', 1, 1, 'equipo', 'sistemas', '2025-11-28 20:19:30', '2025-11-28 20:13:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_inactivo`
--

CREATE TABLE `inventario_inactivo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `estado` enum('inactivo') DEFAULT 'inactivo',
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_mantenimiento`
--

CREATE TABLE `inventario_mantenimiento` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `estado` enum('mantenimiento') DEFAULT 'mantenimiento',
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('ingreso','salida','prestamo','devolucion') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `inventario_id`, `usuario_id`, `tipo`, `cantidad`, `fecha`, `descripcion`) VALUES
(1, 1, 1, 'ingreso', 1, '2025-11-28 20:13:39', 'Ingreso inicial');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `permitido` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `rol_id`, `accion`, `permitido`) VALUES
(1, 1, 'inventario_crud', 1),
(2, 1, 'usuarios_crud', 1),
(3, 1, 'movimientos_crud', 1),
(4, 1, 'prestamos_crud', 1),
(5, 1, 'solicitudes_crud', 1),
(6, 1, 'tickets_crud', 1),
(7, 1, 'auditoria_ver', 1),
(8, 2, 'inventario_ver', 1),
(9, 2, 'inventario_insert', 1),
(10, 2, 'inventario_update', 1),
(11, 2, 'inventario_delete', 0),
(12, 2, 'movimientos_crud', 1),
(13, 2, 'prestamos_update', 1),
(14, 2, 'usuarios_crud', 1),
(15, 2, 'auditoria_ver', 1),
(16, 3, 'inventario_ver', 1),
(17, 3, 'prestamos_insert', 1),
(18, 3, 'solicitudes_insert', 1),
(19, 3, 'tickets_insert', 1),
(20, 3, 'inventario_update', 0),
(21, 3, 'inventario_delete', 0),
(22, 3, 'movimientos_crud', 0),
(23, 3, 'usuarios_crud', 0),
(24, 1, 'inventario_ver', 1),
(25, 1, 'inventario_insert', 1),
(26, 1, 'inventario_update', 1),
(27, 1, 'inventario_delete', 1),
(28, 1, 'prestamos_update', 1),
(29, 1, 'prestamos_insert', 1),
(30, 1, 'solicitudes_insert', 1),
(31, 1, 'tickets_insert', 1),
(32, 1, 'categorias_ver', 1),
(33, 1, 'categorias_crud', 1),
(34, 1, 'categorias_insert', 1),
(35, 1, 'categorias_update', 1),
(36, 1, 'categorias_delete', 1),
(37, 1, 'roles_crud', 1),
(38, 1, 'movimientos_ver', 1),
(39, 1, 'prestamos_delete', 1),
(40, 1, 'solicitudes_update', 1),
(41, 1, 'tickets_update', 1),
(42, 1, 'reportes', 1),
(43, 2, 'inventario_crud', 1),
(44, 2, 'categorias_ver', 1),
(45, 2, 'categorias_crud', 1),
(46, 2, 'categorias_insert', 1),
(47, 2, 'categorias_update', 1),
(48, 2, 'categorias_delete', 0),
(49, 2, 'roles_crud', 1),
(50, 2, 'movimientos_ver', 1),
(51, 2, 'prestamos_crud', 1),
(52, 2, 'prestamos_insert', 1),
(53, 2, 'prestamos_delete', 1),
(54, 2, 'solicitudes_crud', 1),
(55, 2, 'solicitudes_insert', 1),
(56, 2, 'solicitudes_update', 1),
(57, 2, 'tickets_crud', 1),
(58, 2, 'tickets_insert', 1),
(59, 2, 'tickets_update', 1),
(60, 2, 'reportes', 1),
(61, 3, 'inventario_crud', 0),
(62, 3, 'inventario_insert', 0),
(63, 3, 'categorias_ver', 0),
(64, 3, 'categorias_crud', 0),
(65, 3, 'categorias_insert', 0),
(66, 3, 'categorias_update', 0),
(67, 3, 'categorias_delete', 0),
(68, 3, 'roles_crud', 0),
(69, 3, 'movimientos_ver', 0),
(70, 3, 'prestamos_crud', 0),
(71, 3, 'prestamos_update', 0),
(72, 3, 'prestamos_delete', 0),
(73, 3, 'solicitudes_crud', 0),
(74, 3, 'solicitudes_update', 0),
(75, 3, 'tickets_crud', 0),
(76, 3, 'tickets_update', 0),
(77, 3, 'auditoria_ver', 0),
(78, 3, 'reportes', 0),
(79, 1, 'inventario_ver', 1),
(80, 1, 'inventario_insert', 1),
(81, 1, 'inventario_update', 1),
(82, 1, 'inventario_delete', 1),
(83, 1, 'inventario_crud', 1),
(84, 1, 'categorias_ver', 1),
(85, 1, 'categorias_insert', 1),
(86, 1, 'categorias_update', 1),
(87, 1, 'categorias_delete', 1),
(88, 1, 'categorias_crud', 1),
(89, 1, 'movimientos_ver', 1),
(90, 1, 'movimientos_insert', 1),
(91, 1, 'movimientos_update', 1),
(92, 1, 'movimientos_crud', 1),
(93, 1, 'usuarios_crud', 1),
(94, 1, 'roles_crud', 1),
(95, 1, 'prestamos_ver', 1),
(96, 1, 'prestamos_insert', 1),
(97, 1, 'prestamos_update', 1),
(98, 1, 'prestamos_delete', 1),
(99, 1, 'prestamos_crud', 1),
(100, 1, 'solicitudes_ver', 1),
(101, 1, 'solicitudes_insert', 1),
(102, 1, 'solicitudes_update', 1),
(103, 1, 'solicitudes_delete', 1),
(104, 1, 'solicitudes_crud', 1),
(105, 1, 'reportes', 1),
(106, 1, 'roles_crud', 1),
(107, 2, 'roles_crud', 1),
(108, 3, 'roles_crud', 0),
(109, 1, 'reportes', 1),
(110, 2, 'reportes', 1),
(111, 3, 'reportes', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `usuario_solicitante` int(11) NOT NULL,
  `usuario_aprobador` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado','devuelto') DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `aprobado_por` int(11) DEFAULT NULL,
  `fecha_aprobacion` datetime DEFAULT NULL,
  `fecha_devolucion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id`, `inventario_id`, `usuario_solicitante`, `usuario_aprobador`, `cantidad`, `usuario_id`, `ubicacion`, `estado`, `fecha_solicitud`, `aprobado_por`, `fecha_aprobacion`, `fecha_devolucion`) VALUES
(1, 1, 2, NULL, 1, 2, 'siau', 'pendiente', '2025-11-28 22:44:49', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(2, 'BODEGA'),
(1, 'root'),
(3, 'USUARIO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('insumo','equipo','soporte') NOT NULL,
  `detalle` text NOT NULL,
  `estado` enum('pendiente','procesando','resuelto','rechazado') DEFAULT 'pendiente',
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text NOT NULL,
  `estado` enum('abierto','en_proceso','cerrado') DEFAULT 'abierto',
  `prioridad` enum('baja','media','alta') DEFAULT 'media',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_cierre` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(200) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `rol_id`, `activo`) VALUES
(1, 'root', '$2y$10$P7xuOawsd7nZVhwuK76H2u.GnZMFvhe.cXgpsZzoRk2RIGY5uXYaa', 1, 1),
(2, 'bodega', '$2y$10$Lvn5b7sjIMEvFF2gJK1J4eYlayBUXEoD/gLc.LhYA.qpCafZwsbAW', 2, 1),
(3, 'usuario', '$2y$10$rfwYBaXKOU27sF3KuNLBEulQVg1zp5yEm8teqP6k1S7An14ecqIx6', 3, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `inventario_inactivo`
--
ALTER TABLE `inventario_inactivo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario_mantenimiento`
--
ALTER TABLE `inventario_mantenimiento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `usuario_solicitante` (`usuario_solicitante`),
  ADD KEY `usuario_aprobador` (`usuario_aprobador`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `inventario_inactivo`
--
ALTER TABLE `inventario_inactivo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_mantenimiento`
--
ALTER TABLE `inventario_mantenimiento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`inventario_id`) REFERENCES `inventario` (`id`),
  ADD CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`inventario_id`) REFERENCES `inventario` (`id`),
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`usuario_solicitante`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `prestamos_ibfk_3` FOREIGN KEY (`usuario_aprobador`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

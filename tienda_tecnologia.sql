-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 11-04-2025 a las 01:05:52
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
-- Base de datos: `tienda_tecnologia`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `agregar_cliente` (IN `nombre_cliente` VARCHAR(100), IN `correo_cliente` VARCHAR(100), IN `categoria` INT)   BEGIN
    INSERT INTO clientes (nombre, correo, categoria_id, fecha_registro)
    VALUES (nombre_cliente, correo_cliente, categoria, NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrar_log` (IN `user` VARCHAR(100), IN `acc` VARCHAR(100), IN `descp` TEXT, IN `ip` VARCHAR(45))   BEGIN
    INSERT INTO logs_sesiones (usuario, accion, descripcion, ip_origen)
    VALUES (user, acc, descp, ip, NOW());
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `calcular_subtotal` (`cantidad` INT, `precio` DECIMAL(10,2)) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    RETURN cantidad * precio;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `obtener_descuento` (`id_cliente_param` INT) RETURNS DECIMAL(4,2) DETERMINISTIC BEGIN
    DECLARE descuento_cliente DECIMAL(4,2);

    SELECT c.descuento INTO descuento_cliente
    FROM clientes cli
    JOIN categorias_clientes c ON cli.categoria_id = c.id_categoria
    WHERE cli.id_cliente = id_cliente_param;

    RETURN descuento_cliente;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `accion` varchar(10) DEFAULT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `accion`, `tabla_afectada`, `registro_id`, `fecha`, `usuario_id`) VALUES
(1, 'INSERT', 'productos_tienda', 9, '2025-04-10 20:53:53', 1),
(2, 'UPDATE', 'productos_proveedor', 1, '2025-04-10 20:53:53', 0),
(3, 'UPDATE', 'productos_proveedor', 1, '2025-04-10 20:54:43', 0),
(4, 'UPDATE', 'productos_proveedor', 1, '2025-04-10 20:54:47', 0),
(5, 'UPDATE', 'productos_proveedor', 7, '2025-04-10 20:54:50', 0),
(6, 'UPDATE', 'productos_proveedor', 8, '2025-04-10 20:54:53', 0),
(7, 'INSERT', 'productos_proveedor', 9, '2025-04-10 20:55:15', 0),
(8, 'DELETE', 'productos_proveedor', 9, '2025-04-10 20:55:19', 0),
(9, 'DELETE', 'productos_tienda', 9, '2025-04-10 21:19:31', 1),
(10, 'INSERT', 'ventas', 1, '2025-04-10 22:02:22', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_clientes`
--

CREATE TABLE `categorias_clientes` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `descuento` decimal(4,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `fecha_compra` timestamp NOT NULL DEFAULT current_timestamp(),
  `tiempo_entrega` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','completada') DEFAULT 'pendiente',
  `metodo_pago` enum('efectivo','tarjeta') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `id_cliente`, `id_producto`, `cantidad`, `fecha_compra`, `tiempo_entrega`, `estado`, `metodo_pago`) VALUES
(3, 1, 1, 1, '2025-04-10 21:51:48', 'Su pedido llegará en 3-5 días hábiles.', 'pendiente', 'efectivo'),
(4, 1, 1, 1, '2025-04-10 22:00:56', 'Su pedido llegará en 3-5 días hábiles.', 'pendiente', 'tarjeta'),
(5, 1, 1, 1, '2025-04-10 22:02:22', 'Su pedido llegará en 3-5 días hábiles.', 'pendiente', 'tarjeta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_sesiones`
--

CREATE TABLE `logs_sesiones` (
  `id_log` int(11) NOT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `accion` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `ip_origen` varchar(45) DEFAULT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_tienda`
--

CREATE TABLE `pedidos_tienda` (
  `id` int(11) NOT NULL,
  `id_producto_proveedor` int(11) NOT NULL,
  `cantidad_solicitada` int(11) DEFAULT NULL,
  `estado` enum('pendiente','enviado') DEFAULT 'pendiente',
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos_tienda`
--

INSERT INTO `pedidos_tienda` (`id`, `id_producto_proveedor`, `cantidad_solicitada`, `estado`, `fecha`) VALUES
(18, 1, 1, '', '2025-04-10 13:58:25'),
(19, 1, 2, '', '2025-04-10 15:53:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_proveedor`
--

CREATE TABLE `productos_proveedor` (
  `id` int(11) NOT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `id_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_proveedor`
--

INSERT INTO `productos_proveedor` (`id`, `proveedor`, `nombre`, `descripcion`, `precio`, `stock`, `proveedor_id`, `id_producto`) VALUES
(1, '1', 'Mouse Gamer', 'Mouse con luces RGB', 20000.00, 50, NULL, 0),
(7, '1', 'teclado', 'tecla retroluminado', 50000.00, 50, NULL, 0),
(8, '1', 'cargador', 'cargador universal', 50000.00, 50, NULL, 0);

--
-- Disparadores `productos_proveedor`
--
DELIMITER $$
CREATE TRIGGER `after_producto_proveedor_delete` AFTER DELETE ON `productos_proveedor` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id)
    VALUES ('DELETE', 'productos_proveedor', OLD.id, OLD.nombre);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_producto_proveedor_insert` AFTER INSERT ON `productos_proveedor` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id)
    VALUES ('INSERT', 'productos_proveedor', NEW.id, NEW.nombre);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_producto_proveedor_update` AFTER UPDATE ON `productos_proveedor` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id)
    VALUES ('UPDATE', 'productos_proveedor', NEW.id, NEW.nombre);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_tienda`
--

CREATE TABLE `productos_tienda` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` enum('pendiente','aceptado','rechazado') DEFAULT 'pendiente',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_producto_proveedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_tienda`
--

INSERT INTO `productos_tienda` (`id`, `id_producto`, `id_pedido`, `cantidad`, `estado`, `fecha`, `id_producto_proveedor`) VALUES
(8, 1, 18, 1, 'aceptado', '2025-04-10 19:49:27', NULL);

--
-- Disparadores `productos_tienda`
--
DELIMITER $$
CREATE TRIGGER `after_producto_tienda_delete` AFTER DELETE ON `productos_tienda` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id)
    VALUES ('DELETE', 'productos_tienda', OLD.id, OLD.id_producto);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_producto_tienda_insert` AFTER INSERT ON `productos_tienda` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id)
    VALUES ('INSERT', 'productos_tienda', NEW.id, NEW.id_producto);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_producto_tienda_update` AFTER UPDATE ON `productos_tienda` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id)
    VALUES ('UPDATE', 'productos_tienda', NEW.id, NEW.id_producto);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` enum('admin','cliente','proveedor') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contraseña`, `fecha_registro`, `rol`) VALUES
(1, 'Yeyher', 'admin@gmail.com', '$2y$10$LBk.nJ7Ga4cabiYILo5VO.yLxQ2sQMqCixymJjs05F4.9uj2sE0Qa', '2025-04-10 15:47:08', 'proveedor'),
(2, 'Yeyher', 'admini@gmail.com', '$2y$10$ngXXotzLNJ.Ci4Dc0.WSwecEorNSd.8uvd0hRL6EGlg.ZBvMqzGIK', '2025-04-10 17:07:09', 'admin'),
(3, 'Yeyher j', 'adminis@gmail.com', '$2y$10$HCt4k.OWsq.j2yMxXXUWUeVZ3Fs62uHoTrnbYhGkOnwZynGbboj0y', '2025-04-10 19:52:38', 'cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `id_usuario`, `id_producto`, `cantidad`, `precio_unitario`, `fecha`, `id_cliente`) VALUES
(1, NULL, 1, 1, NULL, '2025-04-10 22:02:22', 1);

--
-- Disparadores `ventas`
--
DELIMITER $$
CREATE TRIGGER `after_venta_delete` AFTER DELETE ON `ventas` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id)
    VALUES ('DELETE', 'ventas', OLD.id, OLD.id_usuario);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_venta_insert` AFTER INSERT ON `ventas` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id)
    VALUES ('INSERT', 'ventas', NEW.id, NEW.id_usuario);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_venta_update` AFTER UPDATE ON `ventas` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id)
    VALUES ('UPDATE', 'ventas', NEW.id, NEW.id_usuario);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes_frecuentes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes_frecuentes` (
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_compras_recientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_compras_recientes` (
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_cliente`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_cliente` (
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_mas_vendidos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_mas_vendidos` (
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_stock_bajo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_stock_bajo` (
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes_frecuentes`
--
DROP TABLE IF EXISTS `vista_clientes_frecuentes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes_frecuentes`  AS SELECT `c`.`id_cliente` AS `id_cliente`, `c`.`nombre` AS `nombre`, count(`p`.`id_pedido`) AS `total_pedidos`, max(`p`.`fecha`) AS `ultimo_pedido` FROM (`clientes` `c` join `pedidos` `p` on(`c`.`id_cliente` = `p`.`id_cliente`)) GROUP BY `c`.`id_cliente`, `c`.`nombre` ORDER BY count(`p`.`id_pedido`) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_compras_recientes`
--
DROP TABLE IF EXISTS `vista_compras_recientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_compras_recientes`  AS SELECT `p`.`id_pedido` AS `id_pedido`, `c`.`nombre` AS `cliente`, `p`.`total` AS `total`, `p`.`fecha` AS `fecha` FROM (`pedidos` `p` join `clientes` `c` on(`p`.`id_cliente` = `c`.`id_cliente`)) ORDER BY `p`.`fecha` DESC LIMIT 0, 20 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_cliente`
--
DROP TABLE IF EXISTS `vista_productos_cliente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_cliente`  AS SELECT `productos`.`nombre` AS `nombre`, `productos`.`valor_unitario` AS `valor_unitario`, `productos`.`cantidad_stock` AS `cantidad_stock`, CASE WHEN `productos`.`cantidad_stock` = 0 THEN 'No Disponible' WHEN `productos`.`cantidad_stock` < 5 THEN 'Por Agotar' WHEN `productos`.`fecha_vencimiento` < curdate() THEN 'Vencido' ELSE 'Disponible' END AS `estado` FROM `productos` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_mas_vendidos`
--
DROP TABLE IF EXISTS `vista_productos_mas_vendidos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_mas_vendidos`  AS SELECT `pr`.`id_producto` AS `id_producto`, `pr`.`nombre` AS `nombre`, sum(`dp`.`cantidad`) AS `total_vendido` FROM (`productos` `pr` join `detalles_pedido` `dp` on(`pr`.`id_producto` = `dp`.`id_producto`)) GROUP BY `pr`.`id_producto`, `pr`.`nombre` ORDER BY sum(`dp`.`cantidad`) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_stock_bajo`
--
DROP TABLE IF EXISTS `vista_stock_bajo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_stock_bajo`  AS SELECT `productos`.`id_producto` AS `id_producto`, `productos`.`nombre` AS `nombre`, `productos`.`cantidad_stock` AS `cantidad_stock`, `productos`.`valor_unitario` AS `valor_unitario`, `productos`.`fecha_vencimiento` AS `fecha_vencimiento` FROM `productos` WHERE `productos`.`cantidad_stock` < 5 ORDER BY `productos`.`cantidad_stock` ASC ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias_clientes`
--
ALTER TABLE `categorias_clientes`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `logs_sesiones`
--
ALTER TABLE `logs_sesiones`
  ADD PRIMARY KEY (`id_log`);

--
-- Indices de la tabla `pedidos_tienda`
--
ALTER TABLE `pedidos_tienda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_producto_proveedor` (`id_producto_proveedor`);

--
-- Indices de la tabla `productos_proveedor`
--
ALTER TABLE `productos_proveedor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_proveedor` (`proveedor_id`);

--
-- Indices de la tabla `productos_tienda`
--
ALTER TABLE `productos_tienda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `fk_productos_tienda_proveedor` (`id_producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `logs_sesiones`
--
ALTER TABLE `logs_sesiones`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos_tienda`
--
ALTER TABLE `pedidos_tienda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `productos_proveedor`
--
ALTER TABLE `productos_proveedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `productos_tienda`
--
ALTER TABLE `productos_tienda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_clientes` (`id_categoria`);

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos_tienda` (`id_producto`);

--
-- Filtros para la tabla `pedidos_tienda`
--
ALTER TABLE `pedidos_tienda`
  ADD CONSTRAINT `fk_id_producto_proveedor` FOREIGN KEY (`id_producto_proveedor`) REFERENCES `productos_proveedor` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedidos_tienda_ibfk_1` FOREIGN KEY (`id_producto_proveedor`) REFERENCES `productos_proveedor` (`id`);

--
-- Filtros para la tabla `productos_proveedor`
--
ALTER TABLE `productos_proveedor`
  ADD CONSTRAINT `fk_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `productos_tienda`
--
ALTER TABLE `productos_tienda`
  ADD CONSTRAINT `fk_productos_tienda_proveedor` FOREIGN KEY (`id_producto`) REFERENCES `productos_proveedor` (`id`),
  ADD CONSTRAINT `productos_tienda_ibfk_2` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos_tienda` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.43 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.14.0.7165
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para siatdb
CREATE DATABASE IF NOT EXISTS `siatdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `siatdb`;

-- Volcando estructura para tabla siatdb.activos
CREATE TABLE IF NOT EXISTS `activos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo_activo_id` bigint unsigned NOT NULL,
  `modelo_id` bigint unsigned NOT NULL,
  `estado_activo_id` bigint unsigned NOT NULL,
  `criticidad_id` bigint unsigned NOT NULL,
  `propietario_id` bigint unsigned NOT NULL,
  `ubicacion_id` bigint unsigned NOT NULL,
  `detalle_ubicacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_activo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_serie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mac_ethernet` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mac_wifi` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_adquisicion` date DEFAULT NULL,
  `valor_adquisicion` decimal(12,2) DEFAULT NULL,
  `valor_residual` decimal(12,2) DEFAULT NULL,
  `vida_util_anios` int DEFAULT NULL,
  `ruta_ficha_tecnica` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activos_codigo_activo_unique` (`codigo_activo`),
  UNIQUE KEY `activos_numero_serie_unique` (`numero_serie`),
  KEY `activos_tipo_activo_id_foreign` (`tipo_activo_id`),
  KEY `activos_modelo_id_foreign` (`modelo_id`),
  KEY `activos_estado_activo_id_foreign` (`estado_activo_id`),
  KEY `activos_criticidad_id_foreign` (`criticidad_id`),
  KEY `activos_propietario_id_foreign` (`propietario_id`),
  KEY `activos_ubicacion_id_foreign` (`ubicacion_id`),
  CONSTRAINT `activos_criticidad_id_foreign` FOREIGN KEY (`criticidad_id`) REFERENCES `niveles_criticidad` (`id`),
  CONSTRAINT `activos_estado_activo_id_foreign` FOREIGN KEY (`estado_activo_id`) REFERENCES `estados_activo` (`id`),
  CONSTRAINT `activos_modelo_id_foreign` FOREIGN KEY (`modelo_id`) REFERENCES `modelos` (`id`),
  CONSTRAINT `activos_propietario_id_foreign` FOREIGN KEY (`propietario_id`) REFERENCES `propietarios` (`id`),
  CONSTRAINT `activos_tipo_activo_id_foreign` FOREIGN KEY (`tipo_activo_id`) REFERENCES `tipos_activo` (`id`),
  CONSTRAINT `activos_ubicacion_id_foreign` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicaciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.activos: ~54 rows (aproximadamente)
REPLACE INTO `activos` (`id`, `tipo_activo_id`, `modelo_id`, `estado_activo_id`, `criticidad_id`, `propietario_id`, `ubicacion_id`, `detalle_ubicacion`, `codigo_activo`, `numero_serie`, `mac_ethernet`, `mac_wifi`, `fecha_adquisicion`, `valor_adquisicion`, `valor_residual`, `vida_util_anios`, `ruta_ficha_tecnica`, `created_at`, `updated_at`) VALUES
	(1, 1, 15, 9, 2, 2, 610, 'Gerencia 1er Piso', 'GSGROR-PF4JSL2H', 'PF4JSL2H', '74:5D:22:84:8E:4A', 'E4:0D:36:68:45:C5', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:55', '2026-01-02 20:48:41'),
	(2, 2, 16, 9, 2, 2, 610, 'Gerencia 1er Piso', 'THGROR-MJ0JD048', 'MJ0JD048', '04:7C:16:26:16:D1', '64:D6:9A:4C:68:41', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:56', '2026-01-02 20:48:42'),
	(3, 2, 17, 9, 2, 2, 610, 'Gerencia 1er Piso', 'SLLGOR-MJ0L1QPB', 'MJ0L1QPB', '04:7C:16:9D:F9:C1', 'BC:03:58:40:50:AB', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:56', '2026-01-02 20:48:42'),
	(4, 2, 18, 9, 2, 1, 610, 'Gerencia 1er Piso', 'ORLGOR30021068', 'MJ062W1W', '94:C6:91:23:8A:FE', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:57', '2026-01-02 20:48:43'),
	(5, 1, 15, 9, 2, 2, 610, 'Riesgos - Catastro Planta Baja', 'OCNGOR-PF4JV7SS', 'PF4JV7SS', '74:5D:22:84:8D:70', 'E4:0D:36:68:EA:7F', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:57', '2026-01-02 20:48:43'),
	(6, 2, 17, 9, 2, 2, 610, 'Riesgos - Catastro Planta Baja', 'OENGOR-MJ0L1QQC', 'MJ0L1QQC', '04:7C:16:9D:F9:04', 'BC:03:58:3F:54:99', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:58', '2026-01-02 20:48:44'),
	(7, 2, 18, 9, 2, 1, 610, 'Riesgos - Catastro Planta Baja', 'ORRGOR30021067', 'MJ05ZKJH', '94:C6:91:26:73:6C', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:58', '2026-01-02 20:48:44'),
	(8, 2, 19, 9, 2, 1, 610, 'Negocios 1er Piso', 'NGANOR30021083', 'MJ065K2K', '94:C6:91:25:3A:8B', 'D4:25:8B:08:69:16', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:59', '2026-01-02 20:48:45'),
	(9, 2, 20, 9, 2, 1, 610, 'Negocios 1er Piso', 'ORNGOS30021133', 'G395KQ3', '74:86:E2:2B:57:5E', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:59', '2026-01-02 20:48:45'),
	(10, 2, 18, 9, 2, 1, 610, 'Negocios 1er Piso', 'ORNGOR30021040', 'MJ05VSJ9', '94:C6:91:09:A2:A2', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:00', '2026-01-02 20:48:45'),
	(11, 2, 16, 9, 2, 2, 610, 'Negocios 1er Piso', 'NGONOR-MJ0JD00G', 'MJ0JD00G', '04:7C:16:26:17:78', '64:D6:9A:4F:AF:92', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:00', '2026-01-02 20:48:46'),
	(12, 2, 21, 9, 2, 2, 610, 'Negocios 1er Piso', 'NGONOR-MJF3Y2C', 'MJ0F3Y2C', 'E0:BE:03:32:F9:B3', 'EC:63:D7:14:FA:2D', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:01', '2026-01-02 20:48:46'),
	(13, 1, 15, 9, 2, 2, 610, 'Negocios 1er Piso', 'NGONOR-PF4JY5NY', 'PF4JY5NY', '74:5D:22:84:8D:D2', 'E4:0D:36:68:8D:E1', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:01', '2026-01-02 20:48:47'),
	(14, 1, 22, 9, 2, 2, 610, 'Negocios 1er Piso', 'GSGROR-PF310285', 'PF310285', '90:2E:16:13:1C:8A', '96:5A:FC:0B:20:D1', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:02', '2026-01-02 20:48:47'),
	(15, 1, 23, 9, 2, 2, 610, 'Negocios 1er Piso', 'GANGOR-PF2TEA0X', 'PF2TEA0X', '38:F3:AB:96:E5:14', '64:6E:E0:38:68:4B', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:02', '2026-01-02 20:48:48'),
	(16, 2, 17, 9, 2, 2, 610, 'Negocios 1er Piso', 'NGONOR-WJ0KKD33', 'MJ0KKD33', '04:7C:16:96:F8:56', 'DC:46:28:93:CF:BF', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:03', '2026-01-02 20:48:48'),
	(17, 1, 24, 9, 2, 1, 611, 'Gerencia 1er Piso', 'CZRCOROB30010456', 'CND0344T2V', '84:2A:FD:D1:46:84', '5E:3A:45:A5:39:B7', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:03', '2026-01-02 20:48:49'),
	(18, 2, 20, 9, 2, 1, 610, 'Riesgos - Catastro Planta Baja', 'OROPOS30021132', '9495KQ3', '74:86:E2:2B:57:7E', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:04', '2026-01-02 20:48:49'),
	(19, 1, 22, 9, 2, 2, 610, 'Operaciones Planta Baja', 'SGOPOR-PF30ZRC6', 'PF30ZRC6', '90:2E:16:13:04:60', '16:5A:FC:0B:20:73', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:04', '2026-01-02 20:48:50'),
	(20, 2, 19, 9, 2, 1, 610, 'Operaciones Planta Baja', 'ORCTOR30021086', 'MJ065K3V', '94:C6:91:26:73:F0', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:05', '2026-01-02 20:48:50'),
	(21, 2, 17, 9, 2, 2, 610, 'Riesgos - Catastro Planta Baja', 'TEATOR-MJ0L1QPT', 'MJ0L1QPT', '04:7C:16:9E:19:AB', 'BC:03:58:3F:54:5D', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:05', '2026-01-02 20:48:50'),
	(22, 2, 25, 9, 2, 1, 610, 'Riesgos - Catastro Planta Baja', 'ORGROB30020912', '153SQW1', '78:45:C4:33:DB:D7', '9C:2A:70:32:74:ED', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:06', '2026-01-02 20:48:51'),
	(23, 2, 21, 9, 2, 2, 610, 'Operaciones Planta Baja', 'OSOPOR-MJ0F3Y21', 'MJ0F3Y21', 'E0:BE:03:32:FB:2C', 'EC:63:D7:16:CE:C5', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:07', '2026-01-02 20:48:51'),
	(24, 2, 18, 9, 2, 1, 610, 'Operaciones Planta Baja', 'OPRCOR30021069', 'MJ062W1V', '94:C6:91:22:95:E0', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:07', '2026-01-02 20:48:52'),
	(25, 2, 19, 9, 2, 1, 610, 'Operaciones Planta Baja', 'OROPOR30021085', 'MJ065K3T', '94:C6:91:26:75:30', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:08', '2026-01-02 20:48:52'),
	(26, 2, 26, 9, 2, 1, 610, 'Operaciones Planta Baja', 'OROPOR30020640', 'GWW5XR1', 'D0:67:E5:1F:F2:11', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:08', '2026-01-02 20:48:53'),
	(27, 2, 26, 9, 2, 1, 610, 'Operaciones Planta Baja', 'OROPOE30020835', '64SJWV1', '78:45:C4:19:F6:75', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:09', '2026-01-02 20:48:53'),
	(28, 2, 26, 9, 2, 1, 610, 'Operaciones Planta Baja', 'OROPOR30020543', '3KZFXR1', 'D0:67:E5:1D:8E:BD', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:09', '2026-01-02 20:48:54'),
	(29, 2, 25, 9, 2, 1, 610, 'Operaciones Planta Baja', 'ORCTOE30020915', 'C1BHQW1', '78:45:C4:3C:B1:6C', '2E:2A:70:3F:78:52', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:10', '2026-01-02 20:48:54'),
	(30, 2, 21, 9, 2, 2, 612, 'Negocios 1er Piso', 'NGONOR-MJ0F3XXY', 'MJ0F3XXY', 'E0:BE:03:34:19:E1', 'EC:63:D7:19:53:61', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:11', '2026-01-02 20:48:55'),
	(31, 2, 21, 9, 2, 2, 612, 'Planta baja Operaciones', 'OPOSOR-MJ0F3XVC', 'MJ0F3XVC', 'E0:BE:03:34:1A:0A', 'EC:63:D7:19:71:25', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:11', '2026-01-02 20:48:55'),
	(32, 1, 27, 9, 2, 2, 612, 'Negocios 1er Piso', 'NGONOR-PF41JT38', 'PF41JT38', '9C:2D:CD:55:30:17', '58:CE:2A:A5:C6:0C', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:12', '2026-01-02 20:48:56'),
	(33, 2, 21, 9, 2, 2, 612, 'Negocios 1er Piso', 'NGONOR-MJ0F3XY2', 'MJ0F3XY2', 'E0:BE:03:34:1A:20', 'EC:63:D7:19:53:E3', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:12', '2026-01-02 20:48:56'),
	(34, 2, 17, 9, 2, 2, 612, 'Negocios 1er Piso', 'NGONOR-MJ0L1QQK', 'MJ0L1QQK', '04:7C:16:9D:F9:21', 'BC:03:58:40:68:AC', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:13', '2026-01-02 20:48:57'),
	(35, 2, 26, 9, 2, 1, 612, 'Planta baja Operaciones', 'OROPOR30020713', '2S3C7V1', '78:45:C4:07:C4:9E', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:13', '2026-01-02 20:48:57'),
	(36, 2, 25, 9, 2, 1, 612, 'Planta baja Operaciones', 'ORNGOR30020927', '15LQQW1', '78:45:C4:3E:5A:26', '1E:2A:70:32:49:35', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:13', '2026-01-02 20:48:57'),
	(37, 1, 22, 9, 2, 2, 612, 'Negocios 1er Piso', 'GANGOR-PF30ZREP', 'PF30ZREP', '90:2E:16:13:1D:42', '16:5A:FC:0B:63:21', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:14', '2026-01-02 20:48:58'),
	(38, 2, 21, 9, 2, 2, 612, 'Negocios 1er Piso', 'NGONOR-MJ0GGSQG', 'MJ0GGSQG', 'E0:BE:03:50:DE:99', 'F4:7B:09:ED:A0:E4', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:14', '2026-01-02 20:48:58'),
	(39, 2, 17, 9, 2, 2, 612, 'Negocios 1er Piso', 'NGONOR-MJ01QQ8', 'MJ0L1QQ8', '04:7C:16:9D:F8:FE', 'BC:03:58:3F:4C:FB', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:15', '2026-01-02 20:48:59'),
	(40, 1, 27, 9, 2, 2, 612, 'Negocios 1er Piso', 'NGONOR-PF41LAQ9', 'PF41LAQ9', '9C:2D:CD:55:22:F1', '58:CE:2A:A5:C6:61', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:15', '2026-01-02 20:48:59'),
	(41, 2, 17, 9, 2, 2, 612, 'Negocios 1er Piso', 'NGONOR-MJ0L1QQF', 'MJ0L1QQF', '04:7C:16:9D:F9:1A', 'BC:03:58:40:68:B6', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:15', '2026-01-02 20:48:59'),
	(42, 2, 26, 9, 2, 1, 612, 'Negocios 1er Piso', 'ORCZOX30020539', '3L2BXR1', 'D0:67:E5:1D:8E:8C', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:16', '2026-01-02 20:49:00'),
	(43, 2, 21, 9, 2, 2, 611, 'Negocios 1er Piso', 'NGONOR-MJ0GG9WZ', 'MJ0GG9WZ', 'E0:BE:03:4F:CC:47', 'F4:7B:09:EF:19:DD', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:16', '2026-01-02 20:49:00'),
	(44, 2, 21, 9, 2, 2, 611, 'Planta baja Operaciones', 'OSOPOR-MJ0F3XXZ', 'MJ0F3XXZ', 'E0:BE:03:3D:9D:EA', 'EC:63:D7:17:A7:32', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:17', '2026-01-02 20:49:01'),
	(45, 2, 25, 9, 2, 1, 611, 'Planta baja Operaciones', 'OROPPJ30020928', '12RRQW1', '78:45:C4:3E:5A:26', '1E:2A:70:32:72:65', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:17', '2026-01-02 20:49:01'),
	(46, 2, 26, 9, 2, 1, 611, 'Planta baja Operaciones', 'OROPOR30020836', '68XJWV1', '78:45:C4:1A:3E:65', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:18', '2026-01-02 20:49:02'),
	(47, 1, 22, 9, 2, 2, 611, 'Negocios 1er Piso', 'GANGOR-PF30ZNCL', 'PF30ZNCL', '90:2E:16:13:14:60', '16:5A:FC:0B:20:B3', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:18', '2026-01-02 20:49:02'),
	(48, 2, 21, 9, 2, 2, 611, 'Negocios 1er Piso', 'NGONOR-MJ0F3XT7', 'MJ0F3XT7', 'E0:BE:03:34:1A:18', 'EC:63:D7:17:C4:92', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:19', '2026-01-02 20:49:03'),
	(49, 2, 21, 9, 2, 2, 611, 'Negocios 1er Piso', 'NGONOR-MJ0GGEM1', 'MJ0GGEM1', 'E0:BE:03:4F:CB:9A', 'F4:7B:09:EF:1E:10', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:19', '2026-01-02 20:49:03'),
	(50, 2, 21, 9, 2, 2, 611, 'Negocios 1er Piso', 'NGONOR-MJ0F3XXV', 'MJ0F3XXV', 'E0:BE:03:34:1A:2F', 'EC:63:D7:19:B7:11', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:20', '2026-01-02 20:49:04'),
	(51, 2, 21, 9, 2, 2, 611, 'Negocios 1er Piso', 'NGONOR-MJ0GGEPQ', 'MJ0GGEPQ', 'E0:BE:03:4F:CB:E4', '60:E3:2B:19:AA:5F', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:21', '2026-01-02 20:49:04'),
	(52, 2, 17, 9, 2, 2, 611, 'Negocios 1er Piso', 'NOONOR-MJ0L1QPS', 'MJ0L1QPS', '04:7C:16:9E:19:A5', 'BC:03:58:40:14:FB', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:21', '2026-01-02 20:49:05'),
	(53, 2, 26, 9, 2, 1, 613, 'Planta Baja', 'OROPOB30020576', '3KPCXR1', 'D0:67:E5:1D:96:EE', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:22', '2026-01-02 20:49:05'),
	(54, 2, 26, 9, 2, 1, 613, 'Planta Baja', 'OROPOB30020578', '3KW9XR1', 'D0:67:E5:1D:8F:A5', '', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:22', '2026-01-02 20:49:06');

-- Volcando estructura para tabla siatdb.asignaciones
CREATE TABLE IF NOT EXISTS `asignaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` bigint unsigned NOT NULL,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `fecha_asignacion` datetime NOT NULL,
  `fecha_devolucion` datetime DEFAULT NULL,
  `acta_entrega_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acta_devolucion_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_actual` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asignaciones_activo_id_foreign` (`activo_id`),
  KEY `asignaciones_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `asignaciones_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asignaciones_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.asignaciones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.asignaciones_red
CREATE TABLE IF NOT EXISTS `asignaciones_red` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` bigint unsigned NOT NULL,
  `punto_red_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mac_ethernet` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mac_wifi` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_interno` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_asignacion` datetime NOT NULL,
  `fecha_baja` datetime DEFAULT NULL,
  `es_actual` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asignaciones_red_activo_id_foreign` (`activo_id`),
  KEY `asignaciones_red_punto_red_id_foreign` (`punto_red_id`),
  CONSTRAINT `asignaciones_red_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asignaciones_red_punto_red_id_foreign` FOREIGN KEY (`punto_red_id`) REFERENCES `puntos_red` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.asignaciones_red: ~54 rows (aproximadamente)
REPLACE INTO `asignaciones_red` (`id`, `activo_id`, `punto_red_id`, `ip_address`, `mac_ethernet`, `mac_wifi`, `numero_interno`, `fecha_asignacion`, `fecha_baja`, `es_actual`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, '10.17.71.11', '74:5D:22:84:8E:4A', 'E4:0D:36:68:45:C5', NULL, '2026-01-02 16:48:41', NULL, 1, NULL, NULL),
	(2, 2, NULL, '10.17.71.13', '04:7C:16:26:16:D1', '64:D6:9A:4C:68:41', NULL, '2026-01-02 16:48:42', NULL, 1, NULL, NULL),
	(3, 3, NULL, '10.17.71.14', '04:7C:16:9D:F9:C1', 'BC:03:58:40:50:AB', NULL, '2026-01-02 16:48:42', NULL, 1, NULL, NULL),
	(4, 4, NULL, '10.17.71.15', '94:C6:91:23:8A:FE', '', NULL, '2026-01-02 16:48:43', NULL, 1, NULL, NULL),
	(5, 5, NULL, '10.17.71.16', '74:5D:22:84:8D:70', 'E4:0D:36:68:EA:7F', NULL, '2026-01-02 16:48:43', NULL, 1, NULL, NULL),
	(6, 6, NULL, '10.17.71.17', '04:7C:16:9D:F9:04', 'BC:03:58:3F:54:99', NULL, '2026-01-02 16:48:44', NULL, 1, NULL, NULL),
	(7, 7, NULL, '10.17.71.18', '94:C6:91:26:73:6C', '', NULL, '2026-01-02 16:48:44', NULL, 1, NULL, NULL),
	(8, 8, NULL, '10.17.71.19', '94:C6:91:25:3A:8B', 'D4:25:8B:08:69:16', NULL, '2026-01-02 16:48:45', NULL, 1, NULL, NULL),
	(9, 9, NULL, '10.17.71.20', '74:86:E2:2B:57:5E', '', NULL, '2026-01-02 16:48:45', NULL, 1, NULL, NULL),
	(10, 10, NULL, '10.17.71.21', '94:C6:91:09:A2:A2', '', NULL, '2026-01-02 16:48:45', NULL, 1, NULL, NULL),
	(11, 11, NULL, '10.17.71.22', '04:7C:16:26:17:78', '64:D6:9A:4F:AF:92', NULL, '2026-01-02 16:48:46', NULL, 1, NULL, NULL),
	(12, 12, NULL, '10.17.71.23', 'E0:BE:03:32:F9:B3', 'EC:63:D7:14:FA:2D', NULL, '2026-01-02 16:48:46', NULL, 1, NULL, NULL),
	(13, 13, NULL, '10.17.71.24', '74:5D:22:84:8D:D2', 'E4:0D:36:68:8D:E1', NULL, '2026-01-02 16:48:47', NULL, 1, NULL, NULL),
	(14, 14, NULL, '10.17.71.27', '90:2E:16:13:1C:8A', '96:5A:FC:0B:20:D1', NULL, '2026-01-02 16:48:47', NULL, 1, NULL, NULL),
	(15, 15, NULL, '10.17.71.28', '38:F3:AB:96:E5:14', '64:6E:E0:38:68:4B', NULL, '2026-01-02 16:48:48', NULL, 1, NULL, NULL),
	(16, 16, NULL, '10.17.71.29', '04:7C:16:96:F8:56', 'DC:46:28:93:CF:BF', NULL, '2026-01-02 16:48:48', NULL, 1, NULL, NULL),
	(17, 17, NULL, '10.17.71.30', '84:2A:FD:D1:46:84', '5E:3A:45:A5:39:B7', NULL, '2026-01-02 16:48:49', NULL, 1, NULL, NULL),
	(18, 18, NULL, '10.17.71.31', '74:86:E2:2B:57:7E', '', NULL, '2026-01-02 16:48:49', NULL, 1, NULL, NULL),
	(19, 19, NULL, '10.17.71.35', '90:2E:16:13:04:60', '16:5A:FC:0B:20:73', NULL, '2026-01-02 16:48:50', NULL, 1, NULL, NULL),
	(20, 20, NULL, '10.17.71.36', '94:C6:91:26:73:F0', '', NULL, '2026-01-02 16:48:50', NULL, 1, NULL, NULL),
	(21, 21, NULL, '10.17.71.37', '04:7C:16:9E:19:AB', 'BC:03:58:3F:54:5D', NULL, '2026-01-02 16:48:51', NULL, 1, NULL, NULL),
	(22, 22, NULL, '10.17.71.39', '78:45:C4:33:DB:D7', '9C:2A:70:32:74:ED', NULL, '2026-01-02 16:48:51', NULL, 1, NULL, NULL),
	(23, 23, NULL, '10.17.71.40', 'E0:BE:03:32:FB:2C', 'EC:63:D7:16:CE:C5', NULL, '2026-01-02 16:48:51', NULL, 1, NULL, NULL),
	(24, 24, NULL, '10.17.71.41', '94:C6:91:22:95:E0', '', NULL, '2026-01-02 16:48:52', NULL, 1, NULL, NULL),
	(25, 25, NULL, '10.17.71.42', '94:C6:91:26:75:30', '', NULL, '2026-01-02 16:48:52', NULL, 1, NULL, NULL),
	(26, 26, NULL, '10.17.71.43', 'D0:67:E5:1F:F2:11', '', NULL, '2026-01-02 16:48:53', NULL, 1, NULL, NULL),
	(27, 27, NULL, '10.17.71.45', '78:45:C4:19:F6:75', '', NULL, '2026-01-02 16:48:53', NULL, 1, NULL, NULL),
	(28, 28, NULL, '10.17.71.44', 'D0:67:E5:1D:8E:BD', '', NULL, '2026-01-02 16:48:54', NULL, 1, NULL, NULL),
	(29, 29, NULL, '10.17.71.46', '78:45:C4:3C:B1:6C', '2E:2A:70:3F:78:52', NULL, '2026-01-02 16:48:54', NULL, 1, NULL, NULL),
	(30, 30, NULL, '10.17.71.102', 'E0:BE:03:34:19:E1', 'EC:63:D7:19:53:61', NULL, '2026-01-02 16:48:55', NULL, 1, NULL, NULL),
	(31, 31, NULL, '10.17.71.103', 'E0:BE:03:34:1A:0A', 'EC:63:D7:19:71:25', NULL, '2026-01-02 16:48:55', NULL, 1, NULL, NULL),
	(32, 32, NULL, '10.17.71.104', '9C:2D:CD:55:30:17', '58:CE:2A:A5:C6:0C', NULL, '2026-01-02 16:48:56', NULL, 1, NULL, NULL),
	(33, 33, NULL, '10.17.71.105', 'E0:BE:03:34:1A:20', 'EC:63:D7:19:53:E3', NULL, '2026-01-02 16:48:56', NULL, 1, NULL, NULL),
	(34, 34, NULL, '10.17.71.106', '04:7C:16:9D:F9:21', 'BC:03:58:40:68:AC', NULL, '2026-01-02 16:48:57', NULL, 1, NULL, NULL),
	(35, 35, NULL, '10.17.71.108', '78:45:C4:07:C4:9E', '', NULL, '2026-01-02 16:48:57', NULL, 1, NULL, NULL),
	(36, 36, NULL, '10.17.71.109', '78:45:C4:3E:5A:26', '1E:2A:70:32:49:35', NULL, '2026-01-02 16:48:57', NULL, 1, NULL, NULL),
	(37, 37, NULL, '10.17.71.110', '90:2E:16:13:1D:42', '16:5A:FC:0B:63:21', NULL, '2026-01-02 16:48:58', NULL, 1, NULL, NULL),
	(38, 38, NULL, '10.17.71.112', 'E0:BE:03:50:DE:99', 'F4:7B:09:ED:A0:E4', NULL, '2026-01-02 16:48:58', NULL, 1, NULL, NULL),
	(39, 39, NULL, '10.17.71.113', '04:7C:16:9D:F8:FE', 'BC:03:58:3F:4C:FB', NULL, '2026-01-02 16:48:59', NULL, 1, NULL, NULL),
	(40, 40, NULL, '10.17.71.114', '9C:2D:CD:55:22:F1', '58:CE:2A:A5:C6:61', NULL, '2026-01-02 16:48:59', NULL, 1, NULL, NULL),
	(41, 41, NULL, '10.17.71.115', '04:7C:16:9D:F9:1A', 'BC:03:58:40:68:B6', NULL, '2026-01-02 16:48:59', NULL, 1, NULL, NULL),
	(42, 42, NULL, '10.17.71.201', 'D0:67:E5:1D:8E:8C', '', NULL, '2026-01-02 16:49:00', NULL, 1, NULL, NULL),
	(43, 43, NULL, '10.17.71.202', 'E0:BE:03:4F:CC:47', 'F4:7B:09:EF:19:DD', NULL, '2026-01-02 16:49:00', NULL, 1, NULL, NULL),
	(44, 44, NULL, '10.17.71.203', 'E0:BE:03:3D:9D:EA', 'EC:63:D7:17:A7:32', NULL, '2026-01-02 16:49:01', NULL, 1, NULL, NULL),
	(45, 45, NULL, '10.17.71.204', '78:45:C4:3E:5A:26', '1E:2A:70:32:72:65', NULL, '2026-01-02 16:49:01', NULL, 1, NULL, NULL),
	(46, 46, NULL, '10.17.71.205', '78:45:C4:1A:3E:65', '', NULL, '2026-01-02 16:49:02', NULL, 1, NULL, NULL),
	(47, 47, NULL, '10.17.71.206', '90:2E:16:13:14:60', '16:5A:FC:0B:20:B3', NULL, '2026-01-02 16:49:02', NULL, 1, NULL, NULL),
	(48, 48, NULL, '10.17.71.207', 'E0:BE:03:34:1A:18', 'EC:63:D7:17:C4:92', NULL, '2026-01-02 16:49:03', NULL, 1, NULL, NULL),
	(49, 49, NULL, '10.17.71.208', 'E0:BE:03:4F:CB:9A', 'F4:7B:09:EF:1E:10', NULL, '2026-01-02 16:49:03', NULL, 1, NULL, NULL),
	(50, 50, NULL, '10.17.71.210', 'E0:BE:03:34:1A:2F', 'EC:63:D7:19:B7:11', NULL, '2026-01-02 16:49:04', NULL, 1, NULL, NULL),
	(51, 51, NULL, '10.17.71.211', 'E0:BE:03:4F:CB:E4', '60:E3:2B:19:AA:5F', NULL, '2026-01-02 16:49:04', NULL, 1, NULL, NULL),
	(52, 52, NULL, '10.17.71.212', '04:7C:16:9E:19:A5', 'BC:03:58:40:14:FB', NULL, '2026-01-02 16:49:05', NULL, 1, NULL, NULL),
	(53, 53, NULL, '10.17.109.25', 'D0:67:E5:1D:96:EE', '', NULL, '2026-01-02 16:49:06', NULL, 1, NULL, NULL),
	(54, 54, NULL, '10.17.109.26', 'D0:67:E5:1D:8F:A5', '', NULL, '2026-01-02 16:49:06', NULL, 1, NULL, NULL);

-- Volcando estructura para tabla siatdb.asset_assignments
CREATE TABLE IF NOT EXISTS `asset_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `assigned_at` datetime NOT NULL,
  `returned_at` datetime DEFAULT NULL,
  `act_document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_assignments_asset_id_foreign` (`asset_id`),
  KEY `asset_assignments_user_id_foreign` (`user_id`),
  KEY `asset_assignments_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `asset_assignments_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asset_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asset_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.asset_assignments: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.atributos_activos
CREATE TABLE IF NOT EXISTS `atributos_activos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` bigint unsigned NOT NULL,
  `definicion_atributo_id` bigint unsigned DEFAULT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `atributos_activos_activo_id_nombre_index` (`activo_id`),
  KEY `atributos_activos_definicion_atributo_id_foreign` (`definicion_atributo_id`),
  CONSTRAINT `atributos_activos_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `atributos_activos_definicion_atributo_id_foreign` FOREIGN KEY (`definicion_atributo_id`) REFERENCES `definiciones_atributos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=758 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.atributos_activos: ~339 rows (aproximadamente)
REPLACE INTO `atributos_activos` (`id`, `activo_id`, `definicion_atributo_id`, `valor`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Core I7', '2026-01-02 18:51:55', '2026-01-03 05:44:49'),
	(6, 1, 2, '13va GEN.', '2026-01-02 18:51:55', '2026-01-02 18:51:55'),
	(11, 2, 1, 'Core I7', '2026-01-02 18:51:56', '2026-01-03 07:09:10'),
	(16, 2, 2, '12va GEN.', '2026-01-02 18:51:56', '2026-01-03 07:09:10'),
	(21, 3, 1, 'Core I7', '2026-01-02 18:51:56', '2026-01-03 07:09:10'),
	(26, 3, 2, '12va GEN.', '2026-01-02 18:51:56', '2026-01-03 07:09:10'),
	(31, 4, 1, 'Core I5', '2026-01-02 18:51:57', '2026-01-03 07:09:10'),
	(36, 4, 2, '7ma GEN.', '2026-01-02 18:51:57', '2026-01-03 07:09:10'),
	(41, 5, 1, 'Core I7', '2026-01-02 18:51:57', '2026-01-03 05:44:49'),
	(46, 5, 2, '13va GEN.', '2026-01-02 18:51:57', '2026-01-02 18:51:57'),
	(51, 6, 1, 'Core I7', '2026-01-02 18:51:58', '2026-01-03 07:09:10'),
	(56, 6, 2, '12va GEN.', '2026-01-02 18:51:58', '2026-01-03 07:09:10'),
	(61, 7, 1, 'Core I5', '2026-01-02 18:51:58', '2026-01-03 07:09:10'),
	(66, 7, 2, '7ma GEN.', '2026-01-02 18:51:58', '2026-01-03 07:09:10'),
	(71, 8, 1, 'Core I5', '2026-01-02 18:51:59', '2026-01-03 07:09:10'),
	(76, 8, 2, '7ma GEN.', '2026-01-02 18:51:59', '2026-01-03 07:09:10'),
	(81, 9, 1, 'Core I5', '2026-01-02 18:51:59', '2026-01-03 07:09:10'),
	(86, 9, 2, '12va GEN.', '2026-01-02 18:51:59', '2026-01-03 07:09:10'),
	(91, 10, 1, 'Core I5', '2026-01-02 18:52:00', '2026-01-03 07:09:10'),
	(96, 10, 2, '7ma GEN.', '2026-01-02 18:52:00', '2026-01-03 07:09:10'),
	(101, 11, 1, 'Core I7', '2026-01-02 18:52:00', '2026-01-03 07:09:10'),
	(106, 11, 2, '12va GEN.', '2026-01-02 18:52:00', '2026-01-03 07:09:10'),
	(111, 12, 1, 'Core I5', '2026-01-02 18:52:01', '2026-01-03 07:09:10'),
	(116, 12, 2, '10ma GEN.', '2026-01-02 18:52:01', '2026-01-03 07:09:10'),
	(121, 13, 1, 'Core I7', '2026-01-02 18:52:01', '2026-01-03 05:44:49'),
	(126, 13, 2, '13va GEN.', '2026-01-02 18:52:01', '2026-01-02 18:52:01'),
	(131, 14, 1, 'Core I7', '2026-01-02 18:52:02', '2026-01-03 05:44:49'),
	(136, 14, 2, '11va GEN.', '2026-01-02 18:52:02', '2026-01-02 18:52:02'),
	(141, 15, 1, 'Core I7', '2026-01-02 18:52:02', '2026-01-03 05:44:49'),
	(146, 15, 2, '11va GEN.', '2026-01-02 18:52:02', '2026-01-02 18:52:02'),
	(151, 16, 1, 'Core I7', '2026-01-02 18:52:03', '2026-01-03 07:09:10'),
	(156, 16, 2, '12va GEN.', '2026-01-02 18:52:03', '2026-01-03 07:09:10'),
	(161, 17, 1, 'Core I7', '2026-01-02 18:52:03', '2026-01-03 05:44:49'),
	(166, 17, 2, '10ma GEN.', '2026-01-02 18:52:03', '2026-01-02 18:52:03'),
	(171, 18, 1, 'Core I5', '2026-01-02 18:52:04', '2026-01-03 07:09:10'),
	(176, 18, 2, '12va GEN.', '2026-01-02 18:52:04', '2026-01-03 07:09:10'),
	(181, 19, 1, 'Core I7', '2026-01-02 18:52:04', '2026-01-03 05:44:49'),
	(186, 19, 2, '11va GEN.', '2026-01-02 18:52:04', '2026-01-02 18:52:04'),
	(191, 20, 1, 'Core I5', '2026-01-02 18:52:05', '2026-01-03 07:09:10'),
	(196, 20, 2, '7ma GEN.', '2026-01-02 18:52:05', '2026-01-03 07:09:10'),
	(201, 21, 1, 'Core I7', '2026-01-02 18:52:05', '2026-01-03 07:09:10'),
	(206, 21, 2, '12va GEN.', '2026-01-02 18:52:05', '2026-01-03 07:09:10'),
	(211, 22, 1, 'Core I3', '2026-01-02 18:52:06', '2026-01-03 07:09:10'),
	(216, 22, 2, '3ra GEN.', '2026-01-02 18:52:06', '2026-01-03 07:09:10'),
	(221, 23, 1, 'Core I5', '2026-01-02 18:52:07', '2026-01-03 07:09:10'),
	(226, 23, 2, '10ma GEN.', '2026-01-02 18:52:07', '2026-01-03 07:09:10'),
	(231, 24, 1, 'Core I5', '2026-01-02 18:52:07', '2026-01-03 07:09:10'),
	(236, 24, 2, '7ma GEN.', '2026-01-02 18:52:07', '2026-01-03 07:09:10'),
	(241, 25, 1, 'Core I5', '2026-01-02 18:52:08', '2026-01-03 07:09:10'),
	(246, 25, 2, '7ma GEN.', '2026-01-02 18:52:08', '2026-01-03 07:09:10'),
	(251, 26, 1, 'Core I3', '2026-01-02 18:52:08', '2026-01-03 07:09:10'),
	(256, 26, 2, '2da GEN.', '2026-01-02 18:52:08', '2026-01-03 07:09:10'),
	(261, 27, 1, 'Core I3', '2026-01-02 18:52:09', '2026-01-03 07:09:10'),
	(266, 27, 2, '2da GEN.', '2026-01-02 18:52:09', '2026-01-03 07:09:10'),
	(271, 28, 1, 'Core I3', '2026-01-02 18:52:09', '2026-01-03 07:09:10'),
	(276, 28, 2, '2da GEN.', '2026-01-02 18:52:09', '2026-01-03 07:09:10'),
	(281, 29, 1, 'Intel Core i3', '2026-01-02 18:52:10', '2026-01-05 00:14:37'),
	(286, 29, 2, '3ra Gen', '2026-01-02 18:52:10', '2026-01-05 00:14:37'),
	(291, 30, 1, 'Core I5', '2026-01-02 18:52:11', '2026-01-03 07:09:10'),
	(296, 30, 2, '10ma GEN.', '2026-01-02 18:52:11', '2026-01-03 07:09:10'),
	(301, 31, 1, 'Core I5', '2026-01-02 18:52:11', '2026-01-03 07:09:10'),
	(306, 31, 2, '10ma GEN.', '2026-01-02 18:52:11', '2026-01-03 07:09:10'),
	(311, 32, 1, 'Core I7', '2026-01-02 18:52:12', '2026-01-03 05:44:49'),
	(316, 32, 2, '12va GEN.', '2026-01-02 18:52:12', '2026-01-02 18:52:12'),
	(321, 33, 1, 'Core I5', '2026-01-02 18:52:12', '2026-01-03 07:09:10'),
	(326, 33, 2, '10ma GEN.', '2026-01-02 18:52:12', '2026-01-03 07:09:10'),
	(331, 34, 1, 'Core I7', '2026-01-02 18:52:13', '2026-01-03 07:09:10'),
	(336, 34, 2, '12va GEN.', '2026-01-02 18:52:13', '2026-01-03 07:09:10'),
	(341, 35, 1, 'Core I3', '2026-01-02 18:52:13', '2026-01-03 07:09:10'),
	(346, 35, 2, '2da GEN.', '2026-01-02 18:52:13', '2026-01-03 07:09:10'),
	(351, 36, 1, 'Core I3', '2026-01-02 18:52:13', '2026-01-03 07:09:10'),
	(356, 36, 2, '3ra GEN.', '2026-01-02 18:52:13', '2026-01-03 07:09:10'),
	(361, 37, 1, 'Core I7', '2026-01-02 18:52:14', '2026-01-03 05:44:49'),
	(366, 37, 2, '11va GEN.', '2026-01-02 18:52:14', '2026-01-02 18:52:14'),
	(371, 38, 1, 'Core I5', '2026-01-02 18:52:14', '2026-01-03 07:09:10'),
	(376, 38, 2, '10ma GEN.', '2026-01-02 18:52:14', '2026-01-03 07:09:10'),
	(381, 39, 1, 'Core I7', '2026-01-02 18:52:15', '2026-01-03 07:09:10'),
	(386, 39, 2, '12va GEN.', '2026-01-02 18:52:15', '2026-01-03 07:09:10'),
	(391, 40, 1, 'Core I7', '2026-01-02 18:52:15', '2026-01-03 05:44:49'),
	(396, 40, 2, '12va GEN.', '2026-01-02 18:52:15', '2026-01-02 18:52:15'),
	(401, 41, 1, 'Core I7', '2026-01-02 18:52:15', '2026-01-03 07:09:10'),
	(406, 41, 2, '12va GEN.', '2026-01-02 18:52:15', '2026-01-03 07:09:10'),
	(411, 42, 1, 'Core I3', '2026-01-02 18:52:16', '2026-01-03 07:09:10'),
	(416, 42, 2, '2da GEN.', '2026-01-02 18:52:16', '2026-01-03 07:09:10'),
	(421, 43, 1, 'Core I5', '2026-01-02 18:52:16', '2026-01-03 07:09:10'),
	(426, 43, 2, '10ma GEN.', '2026-01-02 18:52:16', '2026-01-03 07:09:10'),
	(431, 44, 1, 'Core I5', '2026-01-02 18:52:17', '2026-01-03 07:09:10'),
	(436, 44, 2, '10ma GEN.', '2026-01-02 18:52:17', '2026-01-03 07:09:10'),
	(441, 45, 1, 'Core I3', '2026-01-02 18:52:17', '2026-01-03 07:09:10'),
	(446, 45, 2, '3ra GEN.', '2026-01-02 18:52:17', '2026-01-03 07:09:10'),
	(451, 46, 1, 'Core I3', '2026-01-02 18:52:18', '2026-01-03 07:09:10'),
	(456, 46, 2, '2da GEN.', '2026-01-02 18:52:18', '2026-01-03 07:09:10'),
	(461, 47, 1, 'Core I7', '2026-01-02 18:52:18', '2026-01-03 05:44:49'),
	(466, 47, 2, '11va GEN.', '2026-01-02 18:52:18', '2026-01-02 18:52:18'),
	(469, 48, 1, 'Core I5', '2026-01-02 18:52:19', '2026-01-03 07:09:10'),
	(474, 48, 2, '10ma GEN.', '2026-01-02 18:52:19', '2026-01-03 07:09:10'),
	(479, 49, 1, 'Core I5', '2026-01-02 18:52:19', '2026-01-03 07:09:10'),
	(484, 49, 2, '10ma GEN.', '2026-01-02 18:52:20', '2026-01-03 07:09:10'),
	(489, 50, 1, 'Core I5', '2026-01-02 18:52:20', '2026-01-03 07:09:10'),
	(494, 50, 2, '10ma GEN.', '2026-01-02 18:52:20', '2026-01-03 07:09:10'),
	(499, 51, 1, 'Core I5', '2026-01-02 18:52:21', '2026-01-03 07:09:10'),
	(504, 51, 2, '10ma GEN.', '2026-01-02 18:52:21', '2026-01-03 07:09:10'),
	(509, 52, 1, 'Core I7', '2026-01-02 18:52:21', '2026-01-03 07:09:10'),
	(514, 52, 2, '12va GEN.', '2026-01-02 18:52:21', '2026-01-03 07:09:10'),
	(519, 53, 1, 'Core I3', '2026-01-02 18:52:22', '2026-01-03 07:09:10'),
	(524, 53, 2, '2da GEN.', '2026-01-02 18:52:22', '2026-01-03 07:09:10'),
	(529, 54, 1, 'Core I3', '2026-01-02 18:52:22', '2026-01-03 07:09:10'),
	(534, 54, 2, '2da GEN.', '2026-01-02 18:52:22', '2026-01-03 07:09:10'),
	(539, 1, 82, '16 GB', '2026-01-03 05:56:03', '2026-01-03 05:56:03'),
	(540, 1, 83, 'DDR4', '2026-01-03 05:56:03', '2026-01-03 05:56:03'),
	(541, 2, 82, '16 GB', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(542, 2, 83, 'DDR4', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(543, 3, 82, '16 GB', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(544, 3, 83, 'DDR4', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(545, 4, 82, '8 GB', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(546, 4, 83, 'DDR4', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(547, 5, 82, '16 GB', '2026-01-03 05:56:03', '2026-01-03 05:56:03'),
	(548, 5, 83, 'DDR4', '2026-01-03 05:56:03', '2026-01-03 05:56:03'),
	(549, 6, 82, '16 GB', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(550, 6, 83, 'DDR4', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(551, 7, 82, '8 GB', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(552, 7, 83, 'DDR4', '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(553, 8, 82, '8 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(554, 8, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(555, 9, 82, '8 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(556, 9, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(557, 10, 82, '8 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(558, 10, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(559, 11, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(560, 11, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(561, 12, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(562, 12, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(563, 13, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(564, 13, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(565, 14, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(566, 14, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(567, 15, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(568, 15, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(569, 16, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(570, 16, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(571, 17, 82, '8 GB', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(572, 17, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(573, 18, 82, '8 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(574, 18, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(575, 19, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(576, 19, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(577, 20, 82, '8 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(578, 20, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(579, 21, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(580, 21, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(581, 22, 82, '4 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(582, 22, 83, 'DDR3', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(583, 23, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(584, 23, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(585, 24, 82, '8 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(586, 24, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(587, 25, 82, '8 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(588, 25, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(589, 26, 82, '4 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(590, 26, 83, 'DDR3', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(591, 27, 82, '4 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(592, 27, 83, 'DDR3', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(593, 28, 82, '4 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(594, 28, 83, 'DDR3', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(595, 29, 82, '8 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(596, 29, 83, 'DDR3', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(597, 30, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(598, 30, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(599, 31, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(600, 31, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(601, 32, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(602, 32, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 05:56:04'),
	(603, 33, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(604, 33, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(605, 34, 82, '16 GB', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(606, 34, 83, 'DDR4', '2026-01-03 05:56:04', '2026-01-03 07:09:10'),
	(607, 35, 82, '4 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(608, 35, 83, 'DDR3', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(609, 36, 82, '4 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(610, 36, 83, 'DDR3', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(611, 37, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 05:56:05'),
	(612, 37, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 05:56:05'),
	(613, 38, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(614, 38, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(615, 39, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(616, 39, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(617, 40, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 05:56:05'),
	(618, 40, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 05:56:05'),
	(619, 41, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(620, 41, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(621, 42, 82, '4 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(622, 42, 83, 'DDR3', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(623, 43, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(624, 43, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(625, 44, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(626, 44, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(627, 45, 82, '4 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(628, 45, 83, 'DDR3', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(629, 46, 82, '4 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(630, 46, 83, 'DDR3', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(631, 47, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 05:56:05'),
	(632, 47, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 05:56:05'),
	(633, 48, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(634, 48, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(635, 49, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(636, 49, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(637, 50, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(638, 50, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(639, 51, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(640, 51, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(641, 52, 82, '16 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(642, 52, 83, 'DDR4', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(643, 53, 82, '4 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(644, 53, 83, 'DDR3', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(645, 54, 82, '4 GB', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(646, 54, 83, 'DDR3', '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(647, 1, 86, '512 GB', '2026-01-03 05:56:05', '2026-01-03 05:56:05'),
	(648, 1, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(649, 2, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(650, 2, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(651, 3, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(652, 3, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(653, 4, 86, '480 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(654, 4, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(655, 5, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(656, 5, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(657, 6, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(658, 6, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(659, 7, 86, '480 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(660, 7, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(661, 8, 86, '1 TB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(662, 8, 87, 'HDD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(663, 9, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(664, 9, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(665, 10, 86, '480 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(666, 10, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(667, 11, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(668, 11, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(669, 12, 86, '1 TB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(670, 12, 87, 'HDD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(671, 13, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(672, 13, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(673, 14, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(674, 14, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(675, 15, 86, '1 TB', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(676, 15, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(677, 16, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(678, 16, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(679, 17, 86, '480 GB', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(680, 17, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(681, 18, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(682, 18, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(683, 19, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(684, 19, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 05:56:06'),
	(685, 20, 86, '480 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(686, 20, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(687, 21, 86, '512 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(688, 21, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(689, 22, 86, '480 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(690, 22, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(691, 23, 86, '1 TB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(692, 23, 87, 'HDD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(693, 24, 86, '480 GB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(694, 24, 87, 'SSD', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(695, 25, 86, '1 TB', '2026-01-03 05:56:06', '2026-01-03 07:09:10'),
	(696, 25, 87, 'HDD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(697, 26, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(698, 26, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(699, 27, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(700, 27, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(701, 28, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(702, 28, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(703, 29, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(704, 29, 87, 'SSD SATA', '2026-01-03 05:56:07', '2026-01-05 00:14:37'),
	(705, 30, 86, '1 TB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(706, 30, 87, 'HDD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(707, 31, 86, '1 TB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(708, 31, 87, 'HDD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(709, 32, 86, '512 GB', '2026-01-03 05:56:07', '2026-01-03 05:56:07'),
	(710, 32, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 05:56:07'),
	(711, 33, 86, '1 TB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(712, 33, 87, 'HDD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(713, 34, 86, '512 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(714, 34, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(715, 35, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(716, 35, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(717, 36, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(718, 36, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(719, 37, 86, '512 GB', '2026-01-03 05:56:07', '2026-01-03 05:56:07'),
	(720, 37, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 05:56:07'),
	(721, 38, 86, '1 TB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(722, 38, 87, 'HDD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(723, 39, 86, '512 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(724, 39, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(725, 40, 86, '512 GB', '2026-01-03 05:56:07', '2026-01-03 05:56:07'),
	(726, 40, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 05:56:07'),
	(727, 41, 86, '512 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(728, 41, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(729, 42, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(730, 42, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(731, 43, 86, '1 TB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(732, 43, 87, 'HDD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(733, 44, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(734, 44, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(735, 45, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(736, 45, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(737, 46, 86, '480 GB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(738, 46, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(739, 47, 86, '512 GB', '2026-01-03 05:56:07', '2026-01-03 05:56:07'),
	(740, 47, 87, 'SSD', '2026-01-03 05:56:07', '2026-01-03 05:56:07'),
	(741, 48, 86, '1 TB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(742, 48, 87, 'HDD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(743, 49, 86, '1 TB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(744, 49, 87, 'HDD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(745, 50, 86, '1 TB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(746, 50, 87, 'HDD', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(747, 51, 86, '1 TB', '2026-01-03 05:56:07', '2026-01-03 07:09:10'),
	(748, 51, 87, 'HDD', '2026-01-03 05:56:08', '2026-01-03 07:09:10'),
	(749, 52, 86, '512 GB', '2026-01-03 05:56:08', '2026-01-03 07:09:10'),
	(750, 52, 87, 'SSD', '2026-01-03 05:56:08', '2026-01-03 07:09:10'),
	(751, 53, 86, '480 GB', '2026-01-03 05:56:08', '2026-01-03 07:09:10'),
	(752, 53, 87, 'SSD', '2026-01-03 05:56:08', '2026-01-03 07:09:10'),
	(753, 54, 86, '480 Gb', '2026-01-03 05:56:08', '2026-01-03 07:09:10'),
	(754, 54, 87, 'SSD', '2026-01-03 05:56:08', '2026-01-03 07:09:10'),
	(756, 29, 90, 'Realtek RTL8111GN', '2026-01-05 00:14:37', '2026-01-05 00:14:37'),
	(757, 29, 8, 'Intel(R) HD Graphics', '2026-01-05 00:22:09', '2026-01-05 00:22:09');

-- Volcando estructura para tabla siatdb.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.cache: ~6 rows (aproximadamente)
REPLACE INTO `cache` (`key`, `value`, `expiration`) VALUES
	('laravel-cache-10e2aeaa1b649a2c2d773787ee583244', 'i:1;', 1766960253),
	('laravel-cache-10e2aeaa1b649a2c2d773787ee583244:timer', 'i:1766960253;', 1766960253),
	('laravel-cache-4fbea582e285bfe6b873a2751e4d873b', 'i:1;', 1767748321),
	('laravel-cache-4fbea582e285bfe6b873a2751e4d873b:timer', 'i:1767748321;', 1767748321),
	('laravel-cache-admin@siat.bfo|127.0.0.1', 'i:1;', 1766960253),
	('laravel-cache-admin@siat.bfo|127.0.0.1:timer', 'i:1766960253;', 1766960253);

-- Volcando estructura para tabla siatdb.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.cache_locks: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.ciudades
CREATE TABLE IF NOT EXISTS `ciudades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.ciudades: ~7 rows (aproximadamente)
REPLACE INTO `ciudades` (`id`, `nombre`, `codigo`, `created_at`, `updated_at`) VALUES
	(1, 'La Paz', 'LP', '2025-12-29 00:53:26', '2025-12-29 03:04:38'),
	(3, 'Oruro', 'OR', '2025-12-29 00:53:27', '2025-12-29 03:04:14'),
	(4, 'Cochabamba', 'CBBA', '2025-12-29 00:53:27', '2025-12-29 03:04:03'),
	(5, 'Sucre', 'SUC', '2025-12-29 00:53:27', '2025-12-29 00:53:27'),
	(6, 'Santa Cruz', 'SC', '2025-12-29 00:53:27', '2025-12-29 03:05:05'),
	(7, 'Tarija', 'TAR', '2025-12-29 00:53:27', '2025-12-29 00:53:27'),
	(8, 'Potosí', 'PT', '2025-12-29 00:53:27', '2025-12-29 03:04:56'),
	(62, 'El Alto', 'EL', '2026-01-04 18:48:22', '2026-01-04 18:48:22');

-- Volcando estructura para tabla siatdb.controles
CREATE TABLE IF NOT EXISTS `controles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo_activo_id` bigint unsigned DEFAULT NULL,
  `tipo_control_id` bigint unsigned NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `obligatorio` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `controles_tipo_activo_id_foreign` (`tipo_activo_id`),
  KEY `controles_tipo_control_id_foreign` (`tipo_control_id`),
  CONSTRAINT `controles_tipo_activo_id_foreign` FOREIGN KEY (`tipo_activo_id`) REFERENCES `tipos_activo` (`id`),
  CONSTRAINT `controles_tipo_control_id_foreign` FOREIGN KEY (`tipo_control_id`) REFERENCES `tipos_control` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.controles: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.cumplimientos
CREATE TABLE IF NOT EXISTS `cumplimientos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` bigint unsigned NOT NULL,
  `control_id` bigint unsigned NOT NULL,
  `estado_id` bigint unsigned NOT NULL,
  `fecha_verificacion` datetime NOT NULL,
  `usuario_responsable` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `evidencia_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cumplimientos_activo_id_foreign` (`activo_id`),
  KEY `cumplimientos_control_id_foreign` (`control_id`),
  KEY `cumplimientos_estado_id_foreign` (`estado_id`),
  CONSTRAINT `cumplimientos_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cumplimientos_control_id_foreign` FOREIGN KEY (`control_id`) REFERENCES `controles` (`id`),
  CONSTRAINT `cumplimientos_estado_id_foreign` FOREIGN KEY (`estado_id`) REFERENCES `estados_cumplimiento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.cumplimientos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.definicion_atributo_tipo_activo
CREATE TABLE IF NOT EXISTS `definicion_atributo_tipo_activo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `definicion_atributo_id` bigint unsigned NOT NULL,
  `tipo_activo_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `def_attr_type_unique` (`definicion_atributo_id`,`tipo_activo_id`),
  KEY `definicion_atributo_tipo_activo_tipo_activo_id_foreign` (`tipo_activo_id`),
  CONSTRAINT `definicion_atributo_tipo_activo_definicion_atributo_id_foreign` FOREIGN KEY (`definicion_atributo_id`) REFERENCES `definiciones_atributos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `definicion_atributo_tipo_activo_tipo_activo_id_foreign` FOREIGN KEY (`tipo_activo_id`) REFERENCES `tipos_activo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.definicion_atributo_tipo_activo: ~32 rows (aproximadamente)
REPLACE INTO `definicion_atributo_tipo_activo` (`id`, `definicion_atributo_id`, `tipo_activo_id`, `created_at`, `updated_at`) VALUES
	(4, 86, 1, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(5, 82, 1, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(11, 2, 1, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(17, 1, 1, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(28, 8, 1, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(30, 87, 1, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(31, 83, 1, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(73, 18, 4, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(74, 19, 4, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(75, 17, 4, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(76, 33, 5, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(77, 32, 5, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(78, 31, 5, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(79, 22, 6, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(80, 20, 6, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(81, 23, 6, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(82, 21, 6, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(83, 24, 8, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(84, 26, 8, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(85, 25, 8, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(87, 30, 9, '2026-01-03 03:03:47', '2026-01-03 03:03:47'),
	(128, 1, 2, '2026-01-03 07:09:10', '2026-01-03 07:09:10'),
	(129, 2, 2, '2026-01-03 07:09:10', '2026-01-03 07:09:10'),
	(130, 82, 2, '2026-01-03 07:09:10', '2026-01-03 07:09:10'),
	(131, 83, 2, '2026-01-03 07:09:10', '2026-01-03 07:09:10'),
	(132, 86, 2, '2026-01-03 07:09:10', '2026-01-03 07:09:10'),
	(133, 87, 2, '2026-01-03 07:09:10', '2026-01-03 07:09:10'),
	(159, 8, 2, '2026-01-03 07:33:39', '2026-01-03 07:33:39'),
	(166, 82, 9, NULL, NULL),
	(167, 86, 9, NULL, NULL),
	(168, 90, 2, '2026-01-04 23:33:24', '2026-01-04 23:33:24');

-- Volcando estructura para tabla siatdb.definiciones_atributos
CREATE TABLE IF NOT EXISTS `definiciones_atributos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_dato` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `opciones` json DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hardware',
  `orden` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.definiciones_atributos: ~22 rows (aproximadamente)
REPLACE INTO `definiciones_atributos` (`id`, `nombre`, `tipo_dato`, `opciones`, `category`, `orden`, `created_at`, `updated_at`) VALUES
	(1, 'Procesador', 'select', '["Intel Core i3", "Intel Core i5", "Intel Core i7", "Intel Core i9", "AMD Ryzen 3", "AMD Ryzen 5", "AMD Ryzen 7", "AMD Ryzen 9", "Intel Celeron", "Intel Pentium", "Apple M1", "Apple M2", "Apple M3"]', 'hardware', 10, '2026-01-01 18:09:24', '2026-01-01 18:14:09'),
	(2, 'Generación', 'select', '["1ra Gen", "2da Gen", "3ra Gen", "4ta Gen", "5ta Gen", "6ta Gen", "7ma Gen", "8va Gen", "9na Gen", "10ma Gen", "11va Gen", "12va Gen", "13ra Gen", "14ta Gen", "M1", "M2", "M3"]', 'hardware', 11, '2026-01-01 18:09:24', '2026-01-03 07:09:10'),
	(8, 'Tarjeta de Video', 'select', '["Intel(R) HD Graphics"]', 'hardware', 50, '2026-01-01 18:09:24', '2026-01-05 00:21:49'),
	(17, 'Tipo de Impresión', 'select', '["Láser Monocromática", "Láser Color", "Inyección de Tinta", "Matricial", "Térmica", "Plotter", "Multifuncional"]', 'hardware', 10, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(18, 'Conectividad', 'select', '["USB", "Red Ethernet", "Wi-Fi", "USB + Red", "USB + Wi-Fi", "USB + Red + Wi-Fi"]', 'hardware', 20, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(19, 'Dúplex Automático', 'select', '["Si", "No"]', 'hardware', 30, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(20, 'Cantidad de Puertos', 'select', '["4 Puertos", "5 Puertos", "8 Puertos", "16 Puertos", "24 Puertos", "48 Puertos"]', 'hardware', 10, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(21, 'Velocidad', 'select', '["10/100 Mbps", "Gigabit (10/100/1000)", "10 Gigabit", "Multigigabit"]', 'hardware', 20, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(22, 'Administrable', 'select', '["Si", "No (Unmanaged)", "Smart Managed"]', 'hardware', 30, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(23, 'PoE (Power over Ethernet)', 'select', '["No", "Si (PoE)", "Si (PoE+)", "Si (PoE++)"]', 'hardware', 40, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(24, 'Capacidad', 'select', '["500 VA", "600 VA", "750 VA", "1000 VA", "1200 VA", "1500 VA", "2000 VA", "3000 VA", "Más de 3 KVA"]', 'hardware', 10, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(25, 'Topología', 'select', '["Offline / Standby", "Interactiva", "Online / Doble Conversión"]', 'hardware', 20, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(26, 'Tipo de Montaje', 'select', '["Torre", "Rack", "Convertible Torre/Rack"]', 'hardware', 30, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(30, 'Conectividad Celular', 'select', '["Solo Wi-Fi", "4G / LTE", "5G"]', 'hardware', 30, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(31, 'Tamaño de Pantalla', 'select', '["18.5 pulgadas", "19 pulgadas", "20 pulgadas", "21.5 pulgadas", "22 pulgadas", "23.8 pulgadas", "24 pulgadas", "27 pulgadas", "32 pulgadas", "Ultrawide"]', 'hardware', 10, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(32, 'Resolución', 'select', '["HD (1366x768)", "HD+ (1600x900)", "FHD (1920x1080)", "WUXGA (1920x1200)", "QHD (2K)", "4K UHD"]', 'hardware', 20, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(33, 'Entradas de Video', 'select', '["VGA", "HDMI", "VGA + HDMI", "HDMI + DisplayPort", "USB-C / Thunderbolt", "VGA + HDMI + DP"]', 'hardware', 30, '2026-01-01 18:56:34', '2026-01-01 18:56:34'),
	(82, 'Capacidad de Memoria', 'select', '["4 GB", "8 GB", "12 GB", "16 GB", "24 GB", "32 GB", "64 GB", "128 GB"]', 'hardware', 10, '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(83, 'Tipo de Memoria', 'select', '["DDR3", "DDR4", "DDR5", "LPDDR3", "LPDDR4", "LPDDR5", "Unified Memory"]', 'hardware', 10, '2026-01-03 05:56:03', '2026-01-03 07:09:10'),
	(86, 'Capacidad de Disco', 'select', '["128 GB", "250 GB", "256 GB", "480 GB", "500 GB", "512 GB", "1 TB", "2 TB"]', 'hardware', 10, '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(87, 'Tipo de Disco', 'select', '["HDD", "SSD SATA", "SSD M.2", "SSD NVMe"]', 'hardware', 10, '2026-01-03 05:56:05', '2026-01-03 07:09:10'),
	(90, 'Tarjeta de Red', 'select', '["Realtek RTL8111GN", "Realtek PCIe GBE", "Gigabit PCI-E 88E8072 Marvell Yukon"]', 'hardware', 0, '2026-01-04 23:33:24', '2026-01-05 00:01:51');

-- Volcando estructura para tabla siatdb.depreciacion
CREATE TABLE IF NOT EXISTS `depreciacion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` bigint unsigned NOT NULL,
  `periodo` date NOT NULL,
  `valor_inicial` decimal(12,2) NOT NULL,
  `depreciacion_mensual` decimal(10,2) NOT NULL,
  `valor_neto` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `depreciacion_activo_id_periodo_unique` (`activo_id`,`periodo`),
  CONSTRAINT `depreciacion_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.depreciacion: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.ejecuciones_tarea
CREATE TABLE IF NOT EXISTS `ejecuciones_tarea` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tarea_id` bigint unsigned NOT NULL,
  `admin_ciudad_id` bigint unsigned NOT NULL,
  `ubicacion_id` bigint unsigned NOT NULL,
  `fecha_ejecucion` date DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `acta_ejecucion_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_ejecucion_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ejecuciones_tarea_tarea_id_foreign` (`tarea_id`),
  KEY `ejecuciones_tarea_admin_ciudad_id_foreign` (`admin_ciudad_id`),
  KEY `ejecuciones_tarea_ubicacion_id_foreign` (`ubicacion_id`),
  KEY `ejecuciones_tarea_estado_ejecucion_id_foreign` (`estado_ejecucion_id`),
  CONSTRAINT `ejecuciones_tarea_admin_ciudad_id_foreign` FOREIGN KEY (`admin_ciudad_id`) REFERENCES `users` (`id`),
  CONSTRAINT `ejecuciones_tarea_estado_ejecucion_id_foreign` FOREIGN KEY (`estado_ejecucion_id`) REFERENCES `estados_ejecucion` (`id`),
  CONSTRAINT `ejecuciones_tarea_tarea_id_foreign` FOREIGN KEY (`tarea_id`) REFERENCES `tareas_supervisor` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ejecuciones_tarea_ubicacion_id_foreign` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicaciones` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.ejecuciones_tarea: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.estados_activo
CREATE TABLE IF NOT EXISTS `estados_activo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_activo_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.estados_activo: ~8 rows (aproximadamente)
REPLACE INTO `estados_activo` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'activo', NULL, NULL),
	(2, 'en mantenimiento', NULL, NULL),
	(3, 'dado de baja', NULL, NULL),
	(4, 'obsoleto', NULL, NULL),
	(5, 'Disponible', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(6, 'Asignado', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(7, 'Baja', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(8, 'Robado/Perdido', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(9, 'Operativo', '2026-01-02 05:30:39', '2026-01-02 05:30:39');

-- Volcando estructura para tabla siatdb.estados_cumplimiento
CREATE TABLE IF NOT EXISTS `estados_cumplimiento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_cumplimiento_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.estados_cumplimiento: ~3 rows (aproximadamente)
REPLACE INTO `estados_cumplimiento` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'SI', NULL, NULL),
	(2, 'NO', NULL, NULL),
	(3, 'PENDIENTE', NULL, NULL);

-- Volcando estructura para tabla siatdb.estados_ejecucion
CREATE TABLE IF NOT EXISTS `estados_ejecucion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_ejecucion_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.estados_ejecucion: ~4 rows (aproximadamente)
REPLACE INTO `estados_ejecucion` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'pendiente', NULL, NULL),
	(2, 'ejecutada', NULL, NULL),
	(3, 'verificada', NULL, NULL);

-- Volcando estructura para tabla siatdb.estados_mantenimiento
CREATE TABLE IF NOT EXISTS `estados_mantenimiento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_mantenimiento_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.estados_mantenimiento: ~3 rows (aproximadamente)
REPLACE INTO `estados_mantenimiento` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'programado', NULL, NULL),
	(2, 'en curso', NULL, NULL),
	(3, 'finalizado', NULL, NULL);

-- Volcando estructura para tabla siatdb.estados_solicitud
CREATE TABLE IF NOT EXISTS `estados_solicitud` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_solicitud_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.estados_solicitud: ~4 rows (aproximadamente)
REPLACE INTO `estados_solicitud` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'pendiente', NULL, NULL),
	(2, 'aprobado', NULL, NULL),
	(3, 'rechazado', NULL, NULL),
	(4, 'comprado', NULL, NULL);

-- Volcando estructura para tabla siatdb.estados_tarea
CREATE TABLE IF NOT EXISTS `estados_tarea` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_tarea_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.estados_tarea: ~3 rows (aproximadamente)
REPLACE INTO `estados_tarea` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'asignada', NULL, NULL),
	(2, 'en progreso', NULL, NULL),
	(3, 'completada', NULL, NULL),
	(4, 'vencida', NULL, NULL);

-- Volcando estructura para tabla siatdb.estados_usuario
CREATE TABLE IF NOT EXISTS `estados_usuario` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_usuario_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.estados_usuario: ~3 rows (aproximadamente)
REPLACE INTO `estados_usuario` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'activo', NULL, NULL),
	(2, 'libre', NULL, NULL),
	(3, 'baja', NULL, NULL);

-- Volcando estructura para tabla siatdb.estandares
CREATE TABLE IF NOT EXISTS `estandares` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo_estandar_id` bigint unsigned NOT NULL,
  `tipo_activo_id` bigint unsigned DEFAULT NULL,
  `atributo_clave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `operador_id` bigint unsigned NOT NULL,
  `valor_esperado` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estandares_tipo_estandar_id_foreign` (`tipo_estandar_id`),
  KEY `estandares_tipo_activo_id_foreign` (`tipo_activo_id`),
  KEY `estandares_operador_id_foreign` (`operador_id`),
  CONSTRAINT `estandares_operador_id_foreign` FOREIGN KEY (`operador_id`) REFERENCES `operadores` (`id`),
  CONSTRAINT `estandares_tipo_activo_id_foreign` FOREIGN KEY (`tipo_activo_id`) REFERENCES `tipos_activo` (`id`),
  CONSTRAINT `estandares_tipo_estandar_id_foreign` FOREIGN KEY (`tipo_estandar_id`) REFERENCES `tipos_estandar` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.estandares: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.failed_jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.historial_cambios
CREATE TABLE IF NOT EXISTS `historial_cambios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` bigint unsigned NOT NULL,
  `campo_modificado` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_anterior` text COLLATE utf8mb4_unicode_ci,
  `valor_nuevo` text COLLATE utf8mb4_unicode_ci,
  `fecha` datetime NOT NULL,
  `usuario_responsable_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `campo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historial_cambios_activo_id_foreign` (`activo_id`),
  KEY `historial_cambios_usuario_responsable_id_foreign` (`usuario_responsable_id`),
  CONSTRAINT `historial_cambios_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `historial_cambios_usuario_responsable_id_foreign` FOREIGN KEY (`usuario_responsable_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.historial_cambios: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.historial_laboral
CREATE TABLE IF NOT EXISTS `historial_laboral` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `cargo_id` bigint unsigned DEFAULT NULL,
  `city_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `assignment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'permanent',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_job_histories_user_id_foreign` (`user_id`),
  KEY `user_job_histories_job_title_id_foreign` (`cargo_id`),
  KEY `user_job_histories_city_id_foreign` (`city_id`),
  KEY `user_job_histories_branch_id_foreign` (`branch_id`),
  CONSTRAINT `user_job_histories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_job_histories_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `ciudades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_job_histories_job_title_id_foreign` FOREIGN KEY (`cargo_id`) REFERENCES `job_titles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_job_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.historial_laboral: ~31 rows (aproximadamente)
REPLACE INTO `historial_laboral` (`id`, `user_id`, `cargo_id`, `city_id`, `branch_id`, `assignment_type`, `start_date`, `end_date`, `notes`, `created_at`, `updated_at`) VALUES
	(1, 4, 6, NULL, NULL, 'permanent', '2025-12-28', NULL, 'Migración inicial de cargo: Cajero', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(6, 54, 6, NULL, NULL, 'permanent', '2026-01-02', '2026-01-03', 'Cambio de cargo', '2026-01-03 00:31:53', '2026-01-03 04:08:06'),
	(8, 55, 15, 3, 655, 'permanent', '2025-12-05', '2026-01-02', 'Actualización de asignación', '2026-01-03 01:15:28', '2026-01-03 01:40:00'),
	(9, 55, 18, 3, 655, 'temporary', '2026-01-02', '2026-01-02', 'reemplazo de vaciones', '2026-01-03 01:16:16', '2026-01-03 01:16:42'),
	(10, 55, 15, 3, 655, 'permanent', '2026-01-02', NULL, 'Retorno de asignación temporal', '2026-01-03 01:16:42', '2026-01-03 01:16:42'),
	(11, 53, 2, 3, 653, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 02:12:00', '2026-01-03 02:12:00'),
	(12, 42, 13, 3, 654, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 02:57:35', '2026-01-03 02:57:35'),
	(13, 18, 8, 3, 652, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 03:44:50', '2026-01-03 03:44:50'),
	(14, 25, 21, 3, 652, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 03:46:21', '2026-01-03 03:46:21'),
	(15, 35, 2, 3, 654, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 03:47:25', '2026-01-03 03:47:25'),
	(16, 43, 9, 3, 653, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 03:48:47', '2026-01-03 03:48:47'),
	(17, 44, 13, 3, 653, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 03:49:20', '2026-01-03 03:49:20'),
	(18, 7, 24, 3, 652, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 03:50:14', '2026-01-03 03:50:14'),
	(19, 24, 1, 3, 652, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 03:50:56', '2026-01-03 03:50:56'),
	(20, 50, 13, 3, 653, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 03:51:36', '2026-01-03 03:51:36'),
	(21, 52, 13, 3, 653, 'permanent', '2026-01-02', NULL, 'Actualización de asignación', '2026-01-03 03:52:10', '2026-01-03 03:52:10'),
	(22, 36, 25, 3, 654, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:04:53', '2026-01-03 04:04:53'),
	(23, 41, 13, 3, 654, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:07:10', '2026-01-03 04:07:10'),
	(24, 54, 6, 3, 655, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:08:06', '2026-01-03 04:08:06'),
	(25, 31, 6, 3, 652, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:08:43', '2026-01-03 04:08:43'),
	(26, 19, 13, 3, 652, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:09:21', '2026-01-03 04:09:21'),
	(27, 23, 19, 3, 652, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:10:20', '2026-01-03 04:10:20'),
	(28, 17, 13, 3, 652, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:11:16', '2026-01-03 04:11:16'),
	(29, 9, NULL, 3, 652, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:12:20', '2026-01-03 04:12:20'),
	(30, 34, 15, 3, 654, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:13:14', '2026-01-03 04:13:14'),
	(31, 37, 6, 3, 654, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:14:04', '2026-01-03 04:14:04'),
	(32, 46, 6, 3, 654, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:14:49', '2026-01-03 04:14:49'),
	(33, 8, 3, 3, 652, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:15:56', '2026-01-03 04:15:56'),
	(34, 30, 6, 3, 652, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:17:26', '2026-01-03 04:17:26'),
	(35, 26, 15, 3, 652, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:18:00', '2026-01-03 04:18:00'),
	(36, 22, 23, 3, 652, 'permanent', '2026-01-03', NULL, 'Actualización de asignación', '2026-01-03 04:19:20', '2026-01-03 04:19:20');

-- Volcando estructura para tabla siatdb.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.job_batches: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.job_titles
CREATE TABLE IF NOT EXISTS `job_titles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_titles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.job_titles: ~24 rows (aproximadamente)
REPLACE INTO `job_titles` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'ADMINISTRADOR DE TECNOLOGIA SUCURSAL', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(2, 'ANALISTA DE NEGOCIOS', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(3, 'ANALISTA LEGAL SUCURSAL', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(4, 'AUXILIAR DE SERVICIOS', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(6, 'CAJERO', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(7, 'GERENTE DE AGENCIA', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(8, 'GERENTE DE SUCURSAL', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(9, 'NORMALIZADOR DE CARTERA', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(10, 'OFICIAL DE ADMINISTRACION CREDITICIA', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(11, 'OFICIAL DE EVALUACION CREDITICIA', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(12, 'OFICIAL DE NEGOCIOS BANCA EMPRESAS', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(13, 'OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(14, 'OFICIAL DE RIESGO OPERATIVO', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(15, 'OFICIAL DE SERVICIO AL CLIENTE - PUNTO DE RECLAMO', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(16, 'PASANTE TH', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(17, 'RESPONSABLE DE CANJE Y BOVEDA', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(18, 'RESPONSABLE DE CARTERA', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(19, 'RESPONSABLE DE CATASTRO', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(20, 'RESPONSABLE DE COBRANZA', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(21, 'RESPONSABLE DE TALENTO HUMANO Y ADMINISTRACIÓN SUCURSAL', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(22, 'SUBGERENTE DE NEGOCIOS', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(23, 'SUBGERENTE DE OPERACIONES SUCURSAL', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(24, 'SUBGERENTE LEGAL SUCURSAL', '2026-01-02 22:24:15', '2026-01-02 22:24:15'),
	(25, 'OFICIAL DE CUENTAS ESPECIALES', '2026-01-03 04:04:43', '2026-01-03 04:04:43');

-- Volcando estructura para tabla siatdb.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.maintenance_types
CREATE TABLE IF NOT EXISTS `maintenance_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `maintenance_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.maintenance_types: ~4 rows (aproximadamente)
REPLACE INTO `maintenance_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'Preventivo', '2025-12-29 00:52:40', '2025-12-29 00:52:40'),
	(2, 'Correctivo', '2025-12-29 00:52:40', '2025-12-29 00:52:40'),
	(3, 'Instalación', '2025-12-29 00:52:40', '2025-12-29 00:52:40'),
	(4, 'Configuración', '2025-12-29 00:52:40', '2025-12-29 00:52:40');

-- Volcando estructura para tabla siatdb.maintenances
CREATE TABLE IF NOT EXISTS `maintenances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `maintenance_type_id` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `technician_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `performed_at` date NOT NULL,
  `report_document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maintenances_asset_id_foreign` (`asset_id`),
  KEY `maintenances_maintenance_type_id_foreign` (`maintenance_type_id`),
  CONSTRAINT `maintenances_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `maintenances_maintenance_type_id_foreign` FOREIGN KEY (`maintenance_type_id`) REFERENCES `maintenance_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.maintenances: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.mantenimientos
CREATE TABLE IF NOT EXISTS `mantenimientos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` bigint unsigned NOT NULL,
  `proveedor_id` bigint unsigned DEFAULT NULL,
  `tipo_mantenimiento_id` bigint unsigned NOT NULL,
  `estado_mantenimiento_id` bigint unsigned NOT NULL,
  `fecha_inicio` date NOT NULL,
  `costo_bs` decimal(10,2) DEFAULT NULL,
  `hoja_trabajo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nota_servicio_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mantenimientos_activo_id_foreign` (`activo_id`),
  KEY `mantenimientos_proveedor_id_foreign` (`proveedor_id`),
  KEY `mantenimientos_tipo_mantenimiento_id_foreign` (`tipo_mantenimiento_id`),
  KEY `mantenimientos_estado_mantenimiento_id_foreign` (`estado_mantenimiento_id`),
  CONSTRAINT `mantenimientos_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mantenimientos_estado_mantenimiento_id_foreign` FOREIGN KEY (`estado_mantenimiento_id`) REFERENCES `estados_mantenimiento` (`id`),
  CONSTRAINT `mantenimientos_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `mantenimientos_tipo_mantenimiento_id_foreign` FOREIGN KEY (`tipo_mantenimiento_id`) REFERENCES `tipos_mantenimiento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.mantenimientos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.marcas
CREATE TABLE IF NOT EXISTS `marcas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marcas_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.marcas: ~8 rows (aproximadamente)
REPLACE INTO `marcas` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'Dell', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(2, 'HP', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(3, 'Lenovo', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(4, 'Cisco', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(5, 'Epson', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(6, 'APC', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(7, 'Samsung', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(8, 'Apple', '2025-12-31 05:09:05', '2025-12-31 05:09:05');

-- Volcando estructura para tabla siatdb.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.migrations: ~51 rows (aproximadamente)
REPLACE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_08_14_170933_add_two_factor_columns_to_users_table', 1),
	(5, '2025_12_28_204110_create_type_tables', 1),
	(6, '2025_12_28_204120_create_cities_table', 1),
	(7, '2025_12_28_204123_create_branches_table', 1),
	(8, '2025_12_28_204134_add_details_to_users_table', 1),
	(9, '2025_12_28_204141_create_assets_table', 1),
	(10, '2025_12_28_204143_create_asset_assignments_table', 1),
	(11, '2025_12_28_204147_create_maintenances_table', 1),
	(12, '2025_12_28_204149_create_software_logs_table', 1),
	(13, '2025_12_28_204151_create_procurements_table', 1),
	(14, '2025_12_28_204154_create_admin_tasks_table', 1),
	(15, '2025_12_28_222341_add_parent_id_to_branches_table', 2),
	(16, '2025_12_28_225714_add_code_to_branches_table', 3),
	(17, '2025_12_28_230000_create_atms_table', 4),
	(18, '2025_12_28_230010_add_atm_id_to_assets_table', 4),
	(19, '2025_12_29_003003_add_description_and_color_to_branch_types_table', 5),
	(20, '2025_12_29_003935_add_sort_order_to_branch_types_table', 6),
	(21, '2025_12_29_100000_create_core_cmdb_tables', 7),
	(22, '2025_12_29_100001_create_ubicaciones_and_update_users', 7),
	(23, '2025_12_29_110000_create_activos_and_atributos_tables', 8),
	(24, '2025_12_29_120000_create_networking_and_assignments_tables', 9),
	(25, '2025_12_29_130000_create_control_maintenance_tables', 10),
	(26, '2025_12_29_140000_create_workflow_tables', 11),
	(27, '2025_12_30_031654_create_software_management_tables', 12),
	(28, '2025_12_31_031228_add_color_to_niveles_criticidad_table', 13),
	(29, '2026_01_01_135759_create_attribute_definitions_table', 14),
	(30, '2026_01_02_004354_add_category_to_attribute_definitions_table', 15),
	(31, '2026_01_02_120000_add_macs_to_activos_table', 16),
	(32, '2026_01_02_130000_add_detalle_ubicacion_to_activos_table', 17),
	(33, '2026_01_02_140000_create_job_titles_table', 18),
	(34, '2026_01_02_140001_create_user_job_histories_table', 18),
	(35, '2026_01_02_140002_add_job_title_id_to_users_table', 18),
	(36, '2026_01_02_202723_add_username_to_users_table', 19),
	(37, '2026_01_02_203428_add_branch_tracking_to_users_and_history', 20),
	(38, '2026_01_02_204318_add_assignment_type_to_user_job_histories', 21),
	(39, '2026_01_02_205744_make_job_title_nullable_in_user_job_histories', 22),
	(40, '2026_01_02_211951_add_hire_and_termination_dates_to_users_table', 23),
	(41, '2026_01_02_224204_cleanup_redundant_user_columns', 24),
	(42, '2026_01_02_231630_create_settings_table', 25),
	(43, '2026_01_03_023138_standardize_attributes_tables', 26),
	(44, '2026_01_03_025028_create_definicion_atributo_tipo_activo_table', 27),
	(45, '2026_01_03_141315_standardize_tables_to_spanish', 28),
	(46, '2026_01_03_181449_cleanup_asset_types_table', 29),
	(47, '2026_01_03_183459_normalize_attribute_definitions', 30),
	(48, '2026_01_03_190213_cleanup_attributes_data', 31),
	(49, '2026_01_03_192032_create_attribute_options_table', 32),
	(50, '2026_01_03_193257_upgrade_historial_cambios_table', 33),
	(51, '2026_01_03_194535_rename_cities_name_to_nombre', 34),
	(52, '2026_01_03_195134_rename_branches_columns_to_spanish', 35),
	(53, '2026_01_03_195453_rename_atms_columns_to_spanish', 36),
	(54, '2026_01_03_233026_consolidate_branch_types_architecture', 37),
	(55, '2026_01_04_133958_add_direccion_and_telefonos_to_ubicaciones_table', 38),
	(56, '2026_01_04_135332_add_padre_id_to_ubicaciones_table', 39),
	(57, '2026_01_04_221737_create_software_table', 40),
	(58, '2026_01_04_221745_create_software_versions_table', 40),
	(59, '2026_01_04_222525_update_software_installations_schema', 41);

-- Volcando estructura para tabla siatdb.modelos
CREATE TABLE IF NOT EXISTS `modelos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `marca_id` bigint unsigned NOT NULL,
  `tipo_activo_id` bigint unsigned NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modelos_marca_id_foreign` (`marca_id`),
  KEY `modelos_tipo_activo_id_foreign` (`tipo_activo_id`),
  CONSTRAINT `modelos_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `modelos_tipo_activo_id_foreign` FOREIGN KEY (`tipo_activo_id`) REFERENCES `tipos_activo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.modelos: ~27 rows (aproximadamente)
REPLACE INTO `modelos` (`id`, `marca_id`, `tipo_activo_id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Latitude 5420', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(2, 1, 2, 'OptiPlex 7090', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(3, 1, 3, 'PowerEdge R740', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(4, 2, 1, 'ProBook 450', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(5, 2, 4, 'LaserJet Pro M404', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(6, 2, 2, 'EliteDesk 800', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(7, 3, 1, 'ThinkPad T14', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(8, 3, 2, 'ThinkCentre M720', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(9, 4, 6, 'Catalyst 2960', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(10, 4, 7, 'ISR 4331', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(11, 5, 4, 'EcoTank L3150', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(12, 6, 8, 'Smart-UPS 1500', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(13, 7, 9, 'Galaxy Tab S7', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(14, 8, 1, 'MacBook Pro M1', '2025-12-31 05:09:41', '2025-12-31 05:09:41'),
	(15, 3, 1, '21JQS1GJ00', '2026-01-02 18:46:53', '2026-01-02 18:46:53'),
	(16, 3, 2, '11T7S2MW00', '2026-01-02 18:51:56', '2026-01-02 18:51:56'),
	(17, 3, 2, '11T7S79800', '2026-01-02 18:51:56', '2026-01-02 18:51:56'),
	(18, 3, 2, '10M7000SLS', '2026-01-02 18:51:57', '2026-01-02 18:51:57'),
	(19, 3, 2, '10M9A02E00', '2026-01-02 18:51:59', '2026-01-02 18:51:59'),
	(20, 1, 2, 'OptiPlex 3000', '2026-01-02 18:51:59', '2026-01-02 18:51:59'),
	(21, 3, 2, '11DBS2NH00', '2026-01-02 18:52:01', '2026-01-02 18:52:01'),
	(22, 3, 1, '20X4S2FL00', '2026-01-02 18:52:02', '2026-01-02 18:52:02'),
	(23, 3, 1, '20W1S0EJ00', '2026-01-02 18:52:02', '2026-01-02 18:52:02'),
	(24, 2, 1, 'HP Laptop 15-da2xxx', '2026-01-02 18:52:03', '2026-01-02 18:52:03'),
	(25, 1, 2, 'Vostro 270s', '2026-01-02 18:52:06', '2026-01-02 18:52:06'),
	(26, 1, 2, 'Vostro 260', '2026-01-02 18:52:08', '2026-01-02 18:52:08'),
	(27, 3, 1, '21E7S18700', '2026-01-02 18:52:12', '2026-01-02 18:52:12');

-- Volcando estructura para tabla siatdb.niveles_criticidad
CREATE TABLE IF NOT EXISTS `niveles_criticidad` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_numerico` tinyint NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `niveles_criticidad_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.niveles_criticidad: ~3 rows (aproximadamente)
REPLACE INTO `niveles_criticidad` (`id`, `nombre`, `nivel_numerico`, `color`, `created_at`, `updated_at`) VALUES
	(1, 'Baja', 1, '#fff952', NULL, '2026-01-01 17:39:06'),
	(2, 'Media', 2, '#66ff75', NULL, '2026-01-01 17:38:59'),
	(3, 'Alta', 3, '#ff6666', NULL, '2026-01-01 17:39:15');

-- Volcando estructura para tabla siatdb.opciones_atributos
CREATE TABLE IF NOT EXISTS `opciones_atributos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `definicion_atributo_id` bigint unsigned NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `opciones_atributos_definicion_atributo_id_foreign` (`definicion_atributo_id`),
  CONSTRAINT `opciones_atributos_definicion_atributo_id_foreign` FOREIGN KEY (`definicion_atributo_id`) REFERENCES `definiciones_atributos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.opciones_atributos: ~129 rows (aproximadamente)
REPLACE INTO `opciones_atributos` (`id`, `definicion_atributo_id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Intel Core i3', '2026-01-03 23:21:19', '2026-01-03 23:21:19'),
	(2, 1, 'Intel Core i5', '2026-01-03 23:21:19', '2026-01-03 23:21:19'),
	(3, 1, 'Intel Core i7', '2026-01-03 23:21:19', '2026-01-03 23:21:19'),
	(4, 1, 'Intel Core i9', '2026-01-03 23:21:19', '2026-01-03 23:21:19'),
	(5, 1, 'AMD Ryzen 3', '2026-01-03 23:21:19', '2026-01-03 23:21:19'),
	(6, 1, 'AMD Ryzen 5', '2026-01-03 23:21:19', '2026-01-03 23:21:19'),
	(7, 1, 'AMD Ryzen 7', '2026-01-03 23:21:19', '2026-01-03 23:21:19'),
	(8, 1, 'AMD Ryzen 9', '2026-01-03 23:21:19', '2026-01-03 23:21:19'),
	(9, 1, 'Intel Celeron', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(10, 1, 'Intel Pentium', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(11, 1, 'Apple M1', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(12, 1, 'Apple M2', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(13, 1, 'Apple M3', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(14, 2, '1ra Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(15, 2, '2da Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(16, 2, '3ra Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(17, 2, '4ta Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(18, 2, '5ta Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(19, 2, '6ta Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(20, 2, '7ma Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(21, 2, '8va Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(22, 2, '9na Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(23, 2, '10ma Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(24, 2, '11va Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(25, 2, '12va Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(26, 2, '13ra Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(27, 2, '14ta Gen', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(28, 2, 'M1', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(29, 2, 'M2', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(30, 2, 'M3', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(31, 17, 'Láser Monocromática', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(32, 17, 'Láser Color', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(33, 17, 'Inyección de Tinta', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(34, 17, 'Matricial', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(35, 17, 'Térmica', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(36, 17, 'Plotter', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(37, 17, 'Multifuncional', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(38, 18, 'USB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(39, 18, 'Red Ethernet', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(40, 18, 'Wi-Fi', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(41, 18, 'USB + Red', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(42, 18, 'USB + Wi-Fi', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(43, 18, 'USB + Red + Wi-Fi', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(44, 19, 'Si', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(45, 19, 'No', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(46, 20, '4 Puertos', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(47, 20, '5 Puertos', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(48, 20, '8 Puertos', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(49, 20, '16 Puertos', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(50, 20, '24 Puertos', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(51, 20, '48 Puertos', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(52, 21, '10/100 Mbps', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(53, 21, 'Gigabit (10/100/1000)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(54, 21, '10 Gigabit', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(55, 21, 'Multigigabit', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(56, 22, 'Si', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(57, 22, 'No (Unmanaged)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(58, 22, 'Smart Managed', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(59, 23, 'No', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(60, 23, 'Si (PoE)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(61, 23, 'Si (PoE+)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(62, 23, 'Si (PoE++)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(63, 24, '500 VA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(64, 24, '600 VA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(65, 24, '750 VA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(66, 24, '1000 VA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(67, 24, '1200 VA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(68, 24, '1500 VA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(69, 24, '2000 VA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(70, 24, '3000 VA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(71, 24, 'Más de 3 KVA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(72, 25, 'Offline / Standby', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(73, 25, 'Interactiva', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(74, 25, 'Online / Doble Conversión', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(75, 26, 'Torre', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(76, 26, 'Rack', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(77, 26, 'Convertible Torre/Rack', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(78, 30, 'Solo Wi-Fi', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(79, 30, '4G / LTE', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(80, 30, '5G', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(81, 31, '18.5 pulgadas', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(82, 31, '19 pulgadas', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(83, 31, '20 pulgadas', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(84, 31, '21.5 pulgadas', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(85, 31, '22 pulgadas', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(86, 31, '23.8 pulgadas', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(87, 31, '24 pulgadas', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(88, 31, '27 pulgadas', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(89, 31, '32 pulgadas', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(90, 31, 'Ultrawide', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(91, 32, 'HD (1366x768)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(92, 32, 'HD+ (1600x900)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(93, 32, 'FHD (1920x1080)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(94, 32, 'WUXGA (1920x1200)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(95, 32, 'QHD (2K)', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(96, 32, '4K UHD', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(97, 33, 'VGA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(98, 33, 'HDMI', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(99, 33, 'VGA + HDMI', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(100, 33, 'HDMI + DisplayPort', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(101, 33, 'USB-C / Thunderbolt', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(102, 33, 'VGA + HDMI + DP', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(103, 82, '4 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(104, 82, '8 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(105, 82, '12 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(106, 82, '16 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(107, 82, '24 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(108, 82, '32 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(109, 82, '64 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(110, 82, '128 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(111, 83, 'DDR3', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(112, 83, 'DDR4', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(113, 83, 'DDR5', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(114, 83, 'LPDDR3', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(115, 83, 'LPDDR4', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(116, 83, 'LPDDR5', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(117, 83, 'Unified Memory', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(118, 86, '128 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(119, 86, '250 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(120, 86, '256 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(121, 86, '480 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(122, 86, '500 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(123, 86, '512 GB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(124, 86, '1 TB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(125, 86, '2 TB', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(126, 87, 'HDD', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(127, 87, 'SSD SATA', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(128, 87, 'SSD M.2', '2026-01-03 23:21:20', '2026-01-03 23:21:20'),
	(129, 87, 'SSD NVMe', '2026-01-03 23:21:20', '2026-01-03 23:21:20');

-- Volcando estructura para tabla siatdb.operadores
CREATE TABLE IF NOT EXISTS `operadores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `operadores_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.operadores: ~3 rows (aproximadamente)
REPLACE INTO `operadores` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, '>=', NULL, NULL),
	(2, '=', NULL, NULL),
	(3, 'IN', NULL, NULL),
	(4, '<', NULL, NULL);

-- Volcando estructura para tabla siatdb.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.procurements
CREATE TABLE IF NOT EXISTS `procurements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requester_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `items` json NOT NULL,
  `justification` text COLLATE utf8mb4_unicode_ci,
  `authorization_document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `authorized_at` datetime DEFAULT NULL,
  `authorized_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `procurements_requester_id_foreign` (`requester_id`),
  KEY `procurements_city_id_foreign` (`city_id`),
  KEY `procurements_authorized_by_foreign` (`authorized_by`),
  CONSTRAINT `procurements_authorized_by_foreign` FOREIGN KEY (`authorized_by`) REFERENCES `users` (`id`),
  CONSTRAINT `procurements_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `procurements_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.procurements: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.propietarios
CREATE TABLE IF NOT EXISTS `propietarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `propietarios_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.propietarios: ~2 rows (aproximadamente)
REPLACE INTO `propietarios` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'BANCO FORTALEZA', NULL, NULL),
	(2, 'OUTSOURCING DATEC', NULL, NULL);

-- Volcando estructura para tabla siatdb.proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_proveedor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.proveedores: ~2 rows (aproximadamente)
REPLACE INTO `proveedores` (`id`, `nombre`, `nit`, `direccion`, `telefono`, `email`, `tipo_proveedor`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'TechnoSys Bolivia', NULL, NULL, '70012345', 'ventas@technosys.boo', 'Hardware', 1, '2025-12-31 05:10:13', '2025-12-31 05:10:13'),
	(2, 'InfoNet Soluciones', NULL, NULL, '60098765', 'soporte@infonet.boo', 'Mantenimiento', 1, '2025-12-31 05:10:13', '2025-12-31 05:10:13');

-- Volcando estructura para tabla siatdb.puntos_red
CREATE TABLE IF NOT EXISTS `puntos_red` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ubicacion_id` bigint unsigned NOT NULL,
  `patch_panel` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roseta` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `switch` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `puntos_red_unique_idx` (`patch_panel`,`roseta`,`ubicacion_id`),
  KEY `puntos_red_ubicacion_id_foreign` (`ubicacion_id`),
  CONSTRAINT `puntos_red_ubicacion_id_foreign` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicaciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.puntos_red: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.resultados_certificacion
CREATE TABLE IF NOT EXISTS `resultados_certificacion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resultados_certificacion_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.resultados_certificacion: ~2 rows (aproximadamente)
REPLACE INTO `resultados_certificacion` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'aprobado', NULL, NULL),
	(2, 'rechazado', NULL, NULL);

-- Volcando estructura para tabla siatdb.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.sessions: ~2 rows (aproximadamente)
REPLACE INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('E1KyZN3FPI4DNPnrAx4GwV62kTUvXXPDQQ1Jy8t9', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib3c4V0JEYWp1OEQ1WmZhVjB6aU5heHlpeGFXUGFDeFQ3SEJ3RFU3USI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9zaWF0LWJmby50ZXN0L2FwaS9zb2Z0d2FyZS1jYXRhbG9nL2xpc3QiO3M6NToicm91dGUiO3M6MjA6ImFwaS5zb2Z0d2FyZS1jYXRhbG9nIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1767752473),
	('tqSwUvuKCHVwGI91sNz3WUQerotKGeBK4PYtxhgO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Herd/1.24.0 Chrome/120.0.6099.291 Electron/28.2.5 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWRVMngyN0g2M0duT3E2V3RXZjdSV093ZDBqZmo1QWMxTVhkTW9ucSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9zaWF0LWJmby50ZXN0Lz9oZXJkPXByZXZpZXciO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767746263);

-- Volcando estructura para tabla siatdb.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.settings: ~0 rows (aproximadamente)
REPLACE INTO `settings` (`id`, `key`, `value`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'email_domain', 'grupofortaleza.com.bo', 'Dominio de correo para auto-completar en usuarios', '2026-01-03 03:17:36', '2026-01-03 03:17:36');

-- Volcando estructura para tabla siatdb.software
CREATE TABLE IF NOT EXISTS `software` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fabricante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aplicación',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.software: ~6 rows (aproximadamente)
REPLACE INTO `software` (`id`, `nombre`, `fabricante`, `tipo`, `descripcion`, `created_at`, `updated_at`) VALUES
	(5, 'Microsoft Windows', 'Desconocido', 'OEM', NULL, '2026-01-05 03:20:22', '2026-01-05 03:35:35'),
	(6, 'Microsoft Office Hogar y Empresas', 'Desconocido', 'Perpetual', NULL, '2026-01-05 03:20:22', '2026-01-05 03:35:35'),
	(11, 'Microsoft Office Hogar y Pequeña Empresa', 'Desconocido', 'Perpetual', NULL, '2026-01-05 03:20:22', '2026-01-05 03:35:35'),
	(13, 'OpenOffice', 'Desconocido', 'Perpetual', NULL, '2026-01-05 03:20:22', '2026-01-05 03:35:35'),
	(15, 'LibreOffice', 'Desconocido', 'Perpetual', NULL, '2026-01-05 03:20:22', '2026-01-05 03:35:36'),
	(16, 'Adobe Reader', 'Adobe', 'Software', NULL, '2026-01-05 06:15:38', '2026-01-05 06:15:38');

-- Volcando estructura para tabla siatdb.software_installations
CREATE TABLE IF NOT EXISTS `software_installations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `license_id` bigint unsigned DEFAULT NULL,
  `activo_id` bigint unsigned NOT NULL,
  `fecha_instalacion` date NOT NULL,
  `registrado_por` bigint unsigned NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `software_version_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `software_installations_license_id_foreign` (`license_id`),
  KEY `software_installations_activo_id_foreign` (`activo_id`),
  KEY `software_installations_registrado_por_foreign` (`registrado_por`),
  KEY `software_installations_software_version_id_foreign` (`software_version_id`),
  CONSTRAINT `software_installations_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `software_installations_license_id_foreign` FOREIGN KEY (`license_id`) REFERENCES `software_licenses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `software_installations_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `software_installations_software_version_id_foreign` FOREIGN KEY (`software_version_id`) REFERENCES `software_versions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.software_installations: ~113 rows (aproximadamente)
REPLACE INTO `software_installations` (`id`, `license_id`, `activo_id`, `fecha_instalacion`, `registrado_por`, `observaciones`, `created_at`, `updated_at`, `software_version_id`) VALUES
	(1, 9, 1, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:40', '2026-01-02 20:48:41', NULL),
	(2, 10, 1, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:40', '2026-01-02 20:48:41', NULL),
	(3, 9, 2, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:41', '2026-01-02 20:48:42', NULL),
	(4, 10, 2, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:41', '2026-01-02 20:48:42', NULL),
	(5, 9, 3, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:42', '2026-01-02 20:48:42', NULL),
	(6, 10, 3, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:42', '2026-01-02 20:48:42', NULL),
	(7, 11, 4, '2026-01-02', 1, 'Key: 6G4GD-8NFK2-YWY66-J47TP-7CFC2', '2026-01-02 19:27:42', '2026-01-02 20:48:43', NULL),
	(8, 12, 4, '2026-01-02', 1, 'Key: Office 365 (rhuarachi@grupofortaleza.com.bo, Fortaleza2025*)', '2026-01-02 19:27:42', '2026-01-02 20:48:43', NULL),
	(9, 11, 5, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:43', '2026-01-02 20:48:43', NULL),
	(10, 10, 5, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:43', '2026-01-02 20:48:43', NULL),
	(11, 11, 6, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:44', '2026-01-02 20:48:44', NULL),
	(12, 10, 6, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:44', '2026-01-02 20:48:44', NULL),
	(13, 9, 7, '2026-01-02', 1, 'Key: 6G4GD-8NFK2-YWY66-J47TP-7CFC2', '2026-01-02 19:27:45', '2026-01-02 20:48:44', NULL),
	(14, 13, 7, '2026-01-02', 1, 'Key: TJNG2-R4YK2-MG3C7-PVQ9V-G6QV6', '2026-01-02 19:27:45', '2026-01-02 20:48:44', NULL),
	(15, 14, 8, '2026-01-02', 1, 'Key: 84NYT-K89Y7-GVBPQ-PJC4C-RVV22', '2026-01-02 19:27:45', '2026-01-02 20:48:45', NULL),
	(16, 15, 8, '2026-01-02', 1, 'Key: D4Q6C-Q7HRX-M6XTK-M7MKJ-JWMY6', '2026-01-02 19:27:45', '2026-01-02 20:48:45', NULL),
	(17, 9, 9, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:45', '2026-01-02 20:48:45', NULL),
	(18, 12, 9, '2026-01-02', 1, 'Key: Office 365 (cmamani@grupofortaleza.com.bo, Fortaleza2024*)', '2026-01-02 19:27:45', '2026-01-02 20:48:45', NULL),
	(19, 11, 10, '2026-01-02', 1, 'Key: JQN6F-8RY9V-CJQHJ-2RR2G-V6DGP', '2026-01-02 19:27:46', '2026-01-02 20:48:45', NULL),
	(20, 12, 10, '2026-01-02', 1, 'Key: cpenac@grupofortaleza.com.bo, Fortaleza2025*', '2026-01-02 19:27:46', '2026-01-02 20:48:45', NULL),
	(21, 9, 11, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:46', '2026-01-02 20:48:46', NULL),
	(22, 10, 11, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:46', '2026-01-02 20:48:46', NULL),
	(23, 9, 12, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:47', '2026-01-02 20:48:46', NULL),
	(24, 13, 12, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:47', '2026-01-02 20:48:46', NULL),
	(25, 11, 13, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:48', '2026-01-02 20:48:47', NULL),
	(26, 10, 13, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:48', '2026-01-02 20:48:47', NULL),
	(27, 9, 14, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:48', '2026-01-02 20:48:47', NULL),
	(28, 13, 14, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:48', '2026-01-02 20:48:47', NULL),
	(29, 9, 15, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:49', '2026-01-02 20:48:48', NULL),
	(30, 10, 15, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:49', '2026-01-02 20:48:48', NULL),
	(31, 11, 16, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:49', '2026-01-02 20:48:48', NULL),
	(32, 10, 16, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:49', '2026-01-02 20:48:48', NULL),
	(33, 9, 17, '2026-01-02', 1, 'Key: 97WTN-WY7D4-G8V3T-CHKPW-2GYP4', '2026-01-02 19:27:50', '2026-01-02 20:48:49', NULL),
	(34, 12, 17, '2026-01-02', 1, 'Key: Office 365 (jrioja@grupofortaleza.com.bo, Fortaleza2024)', '2026-01-02 19:27:50', '2026-01-02 20:48:49', NULL),
	(35, 11, 18, '2026-01-02', 1, 'Key: NF6HC-QH89W-F8WYV-WWXV4-WFG6P', '2026-01-02 19:27:50', '2026-01-02 20:48:49', NULL),
	(36, 16, 18, '2026-01-02', 1, 'Key: Office 365 (jsalguero@grupofortaleza, Fortaleza2023)', '2026-01-02 19:27:50', '2026-01-02 20:48:49', NULL),
	(37, 11, 19, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:51', '2026-01-02 20:48:50', NULL),
	(38, 13, 19, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:51', '2026-01-02 20:48:50', NULL),
	(39, 11, 20, '2026-01-02', 1, 'Key: NPD9J-2VRK2-YKB8W-6QH9P-JB49C', '2026-01-02 19:27:51', '2026-01-02 20:48:50', NULL),
	(40, 12, 20, '2026-01-02', 1, 'Key: Office 365 (rgarcia@grupofortaleza, Fortaleza2025*)', '2026-01-02 19:27:51', '2026-01-02 20:48:50', NULL),
	(41, 9, 21, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:52', '2026-01-02 20:48:51', NULL),
	(42, 10, 21, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:52', '2026-01-02 20:48:51', NULL),
	(43, 9, 22, '2026-01-02', 1, 'Key: 6G4GD-8NFK2-YWY66-J47TP-7CFC2', '2026-01-02 19:27:52', '2026-01-02 20:48:51', NULL),
	(44, 15, 22, '2026-01-02', 1, 'Key: N28Y6-XMMJ4-WRHXJ-B27RR-R3M4G', '2026-01-02 19:27:52', '2026-01-02 20:48:51', NULL),
	(45, 9, 23, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:53', '2026-01-02 20:48:51', NULL),
	(46, 13, 23, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:53', '2026-01-02 20:48:51', NULL),
	(47, 9, 24, '2026-01-02', 1, 'Key: CYK4X-JTNJY-VHDG8-JF4QQ-G83GP', '2026-01-02 19:27:54', '2026-01-02 20:48:52', NULL),
	(48, 12, 24, '2026-01-02', 1, 'Key: Office 365 (epardo@grupofortaleza, Fortaleza2025*)', '2026-01-02 19:27:54', '2026-01-02 20:48:52', NULL),
	(49, 14, 25, '2026-01-02', 1, 'Key: KW7XN-Q9988-WYJP2-CT9VK-HCFC2', '2026-01-02 19:27:54', '2026-01-02 20:48:52', NULL),
	(50, 15, 25, '2026-01-02', 1, 'Key: 6TDVD-9G8MD-D2PDH-GJQB7-6C8YH', '2026-01-02 19:27:54', '2026-01-02 20:48:52', NULL),
	(51, 9, 26, '2026-01-02', 1, 'Key: CY63F-QCQPB-7DXKT-973BP-M33XQ', '2026-01-02 19:27:55', '2026-01-02 20:48:53', NULL),
	(52, 17, 26, '2026-01-02', 1, 'Key: ---', '2026-01-02 19:27:55', '2026-01-02 20:48:53', NULL),
	(53, 9, 27, '2026-01-02', 1, 'Key: DVHQC-JF3H6-N7T6C-J6WYX-JW8XV', '2026-01-02 19:27:55', '2026-01-02 20:48:53', NULL),
	(54, 17, 27, '2026-01-02', 1, 'Key: ---', '2026-01-02 19:27:55', '2026-01-02 20:48:53', NULL),
	(55, 11, 28, '2026-01-02', 1, 'Key: TFCFY-WFGY8-DQNPR-64XYR-2J8XV', '2026-01-02 19:27:56', '2026-01-02 20:48:54', NULL),
	(56, 17, 28, '2026-01-02', 1, 'Key: ---', '2026-01-02 19:27:56', '2026-01-02 20:48:54', NULL),
	(59, 18, 30, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:57', '2026-01-02 20:48:55', NULL),
	(60, 13, 30, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:57', '2026-01-02 20:48:55', NULL),
	(61, 11, 31, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:57', '2026-01-02 20:48:55', NULL),
	(62, 13, 31, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:57', '2026-01-02 20:48:55', NULL),
	(63, 11, 32, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:58', '2026-01-02 20:48:56', NULL),
	(64, 10, 32, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:58', '2026-01-02 20:48:56', NULL),
	(65, 11, 33, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:58', '2026-01-02 20:48:56', NULL),
	(66, 13, 33, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:58', '2026-01-02 20:48:56', NULL),
	(67, 9, 34, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:59', '2026-01-02 20:48:57', NULL),
	(68, 10, 34, '2026-01-02', 1, 'Migrated', '2026-01-02 19:27:59', '2026-01-02 20:48:57', NULL),
	(69, 18, 35, '2026-01-02', 1, 'Key: MMJPR-4YMMQ-FMG46-CQK2W-PPJ4C', '2026-01-02 19:27:59', '2026-01-02 20:48:57', NULL),
	(70, 17, 35, '2026-01-02', 1, 'Key: Open Office', '2026-01-02 19:27:59', '2026-01-02 20:48:57', NULL),
	(71, 11, 36, '2026-01-02', 1, 'Key: JRGQM-PT4H7-MBQN9-P66VG-QYFDH', '2026-01-02 19:27:59', '2026-01-02 20:48:57', NULL),
	(72, 17, 36, '2026-01-02', 1, 'Key: Open Office', '2026-01-02 19:27:59', '2026-01-02 20:48:57', NULL),
	(73, 11, 37, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:00', '2026-01-02 20:48:58', NULL),
	(74, 13, 37, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:00', '2026-01-02 20:48:58', NULL),
	(75, 9, 38, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:00', '2026-01-02 20:48:58', NULL),
	(76, 10, 38, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:00', '2026-01-02 20:48:58', NULL),
	(77, 11, 39, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:01', '2026-01-02 20:48:59', NULL),
	(78, 10, 39, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:01', '2026-01-02 20:48:59', NULL),
	(79, 11, 40, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:01', '2026-01-02 20:48:59', NULL),
	(80, 10, 40, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:01', '2026-01-02 20:48:59', NULL),
	(81, 9, 41, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:02', '2026-01-02 20:48:59', NULL),
	(82, 10, 41, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:02', '2026-01-02 20:48:59', NULL),
	(83, 18, 42, '2026-01-02', 1, 'Key: DVHQC-JF3H6-N7T6C-J6WYX-JW8XV', '2026-01-02 19:28:02', '2026-01-02 20:49:00', NULL),
	(84, 19, 42, '2026-01-02', 1, 'Key: Libre office', '2026-01-02 19:28:02', '2026-01-02 20:49:00', NULL),
	(85, 11, 43, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:03', '2026-01-02 20:49:00', NULL),
	(86, 10, 43, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:03', '2026-01-02 20:49:00', NULL),
	(87, 9, 44, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:03', '2026-01-02 20:49:01', NULL),
	(88, 13, 44, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:03', '2026-01-02 20:49:01', NULL),
	(89, 11, 45, '2026-01-02', 1, 'Key: XV7QG-TPJX2-MVQ9N-7WM9T-BPWXV', '2026-01-02 19:28:04', '2026-01-02 20:49:01', NULL),
	(90, 17, 45, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:04', '2026-01-02 20:49:01', NULL),
	(91, 9, 46, '2026-01-02', 1, 'Key: FQWRK-HWM4W-77VDN-JHW66-FVRDH', '2026-01-02 19:28:04', '2026-01-02 20:49:02', NULL),
	(92, 17, 46, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:04', '2026-01-02 20:49:02', NULL),
	(93, 9, 47, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:05', '2026-01-02 20:49:02', NULL),
	(94, 13, 47, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:05', '2026-01-02 20:49:02', NULL),
	(95, 11, 48, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:05', '2026-01-02 20:49:03', NULL),
	(96, 13, 48, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:05', '2026-01-02 20:49:03', NULL),
	(97, 11, 49, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:06', '2026-01-02 20:49:03', NULL),
	(98, 10, 49, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:06', '2026-01-02 20:49:03', NULL),
	(99, 9, 50, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:06', '2026-01-02 20:49:04', NULL),
	(100, 13, 50, '2026-01-02', 1, 'Migrated', '2026-01-02 19:28:06', '2026-01-02 20:49:04', NULL),
	(101, 11, 51, '2026-01-02', 1, 'Migrated', '2026-01-02 19:29:51', '2026-01-02 20:49:04', NULL),
	(102, 10, 51, '2026-01-02', 1, 'Migrated', '2026-01-02 19:29:51', '2026-01-02 20:49:04', NULL),
	(103, 9, 52, '2026-01-02', 1, 'Migrated', '2026-01-02 19:29:51', '2026-01-02 20:49:05', NULL),
	(104, 10, 52, '2026-01-02', 1, 'Migrated', '2026-01-02 19:29:51', '2026-01-02 20:49:05', NULL),
	(105, 9, 53, '2026-01-02', 1, 'Key: 2JY8J-RCC8M-YNGYK-4J4Q6-C9T67', '2026-01-02 19:29:52', '2026-01-02 20:49:05', NULL),
	(106, 17, 53, '2026-01-02', 1, 'Key: Open Office', '2026-01-02 19:29:52', '2026-01-02 20:49:06', NULL),
	(107, 9, 54, '2026-01-02', 1, 'Key: GYXX9-9H3BG-XBMTC-NQRBP-MPWXV', '2026-01-02 19:29:52', '2026-01-02 20:49:06', NULL),
	(108, 15, 54, '2026-01-02', 1, 'Key: BG3VJ-3QMJ2-9DKH2-KRVRY-JYB2T', '2026-01-02 19:29:52', '2026-01-02 20:49:06', NULL),
	(112, NULL, 29, '2026-01-05', 1, NULL, '2026-01-05 06:30:01', '2026-01-05 06:30:01', 16),
	(114, NULL, 29, '2026-01-06', 1, NULL, '2026-01-06 05:25:25', '2026-01-06 05:25:25', 14),
	(116, NULL, 29, '2026-01-06', 1, NULL, '2026-01-06 05:52:57', '2026-01-06 05:52:57', 9);

-- Volcando estructura para tabla siatdb.software_licenses
CREATE TABLE IF NOT EXISTS `software_licenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OEM',
  `scope` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CITY',
  `city_id` bigint unsigned DEFAULT NULL,
  `seats_total` int NOT NULL DEFAULT '1',
  `seats_used` int NOT NULL DEFAULT '0',
  `fecha_expiracion` date DEFAULT NULL,
  `proveedor_id` bigint unsigned DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `software_licenses_proveedor_id_foreign` (`proveedor_id`),
  CONSTRAINT `software_licenses_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.software_licenses: ~20 rows (aproximadamente)
REPLACE INTO `software_licenses` (`id`, `nombre`, `key`, `tipo`, `scope`, `city_id`, `seats_total`, `seats_used`, `fecha_expiracion`, `proveedor_id`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 'Microsoft Office 2021 Pro', 'XC90-DF89-DSDS-9090-JDKL', 'Volume', 'CITY', NULL, 1, 0, NULL, NULL, NULL, '2025-12-31 05:10:16', '2026-01-07 05:52:24'),
	(2, 'Windows 11 Pro', 'OEM-PRE-INSTALLED', 'OEM', 'CITY', NULL, 100, 0, NULL, 1, NULL, '2025-12-31 05:10:16', '2025-12-31 05:10:16'),
	(3, 'Adobe Creative Cloud', 'SUB-2025-USER-KEY', 'Subscription', 'CITY', NULL, 5, 0, '2026-12-31', NULL, NULL, '2025-12-31 05:10:16', '2025-12-31 05:10:16'),
	(4, 'ESET Endpoint Security', 'ESET-CORP-LIC-001', 'Subscription', 'CITY', NULL, 200, 0, '2026-07-01', NULL, NULL, '2025-12-31 05:10:16', '2026-01-06 05:19:00'),
	(5, 'Microsoft Office 2021 Pro', 'XC90-DF89-DSDS-9090-JDKL', 'Volume', 'CITY', NULL, 50, 0, NULL, 1, NULL, '2025-12-31 05:11:33', '2025-12-31 05:11:33'),
	(6, 'Windows 11 Pro', 'OEM-PRE-INSTALLED', 'OEM', 'CITY', NULL, 100, 0, NULL, 1, NULL, '2025-12-31 05:11:33', '2025-12-31 05:11:33'),
	(7, 'Adobe Creative Cloud', 'SUB-2025-USER-KEY', 'Subscription', 'CITY', NULL, 5, 0, '2026-12-31', NULL, NULL, '2025-12-31 05:11:33', '2025-12-31 05:11:33'),
	(8, 'ESET Endpoint Security', 'ESET-CORP-LIC-001', 'Subscription', 'CITY', NULL, 200, 0, '2026-07-01', NULL, NULL, '2025-12-31 05:11:33', '2025-12-31 05:11:33'),
	(9, 'Microsoft Windows 11 Pro 24H2', NULL, 'OEM', 'CITY', NULL, 1000, 0, NULL, NULL, NULL, '2026-01-02 19:27:40', '2026-01-02 19:27:40'),
	(10, 'Microsoft Office Hogar y Empresas 2021', NULL, 'Perpetual', 'CITY', NULL, 1000, 0, NULL, NULL, NULL, '2026-01-02 19:27:40', '2026-01-02 19:27:40'),
	(11, 'Microsoft Windows 11 Pro 23H2', NULL, 'OEM', 'CITY', NULL, 1000, -1, NULL, NULL, NULL, '2026-01-02 19:27:42', '2026-01-06 05:17:58'),
	(12, 'Microsoft 365 para negocios', NULL, 'Perpetual', 'CITY', NULL, 1000, 0, NULL, NULL, NULL, '2026-01-02 19:27:42', '2026-01-02 19:27:42'),
	(13, 'Microsoft Office Hogar y Empresas 2019', NULL, 'Perpetual', 'CITY', NULL, 1000, 0, NULL, NULL, NULL, '2026-01-02 19:27:45', '2026-01-02 19:27:45'),
	(14, 'Microsoft Windows 10 Pro 22H2', NULL, 'OEM', 'CITY', NULL, 1000, 0, NULL, NULL, NULL, '2026-01-02 19:27:45', '2026-01-02 19:27:45'),
	(15, 'Microsoft Office Hogar y Pequeña Empresa 2010', NULL, 'Perpetual', 'CITY', NULL, 1000, 0, NULL, NULL, NULL, '2026-01-02 19:27:45', '2026-01-02 19:27:45'),
	(16, 'Microsoft 365', NULL, 'Perpetual', 'CITY', NULL, 1000, 0, NULL, NULL, NULL, '2026-01-02 19:27:50', '2026-01-06 05:18:56'),
	(17, 'OpenOffice 4.1.15', NULL, 'Perpetual', 'CITY', NULL, 1000, -1, NULL, NULL, NULL, '2026-01-02 19:27:55', '2026-01-06 05:18:53'),
	(18, 'Microsoft Windows 11 Pro 22H2', NULL, 'OEM', 'CITY', NULL, 1000, 0, NULL, NULL, NULL, '2026-01-02 19:27:57', '2026-01-02 19:27:57'),
	(19, 'LibreOffice 24.8.0.3', NULL, 'Perpetual', 'CITY', NULL, 1000, 0, NULL, NULL, NULL, '2026-01-02 19:28:02', '2026-01-02 19:28:02');

-- Volcando estructura para tabla siatdb.software_logs
CREATE TABLE IF NOT EXISTS `software_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `software_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `performed_at` datetime NOT NULL,
  `performed_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `software_logs_asset_id_foreign` (`asset_id`),
  KEY `software_logs_performed_by_foreign` (`performed_by`),
  CONSTRAINT `software_logs_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `software_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.software_logs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.software_versions
CREATE TABLE IF NOT EXISTS `software_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `software_id` bigint unsigned NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_lanzamiento` date DEFAULT NULL,
  `eol_date` date DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `software_versions_software_id_foreign` (`software_id`),
  CONSTRAINT `software_versions_software_id_foreign` FOREIGN KEY (`software_id`) REFERENCES `software` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.software_versions: ~9 rows (aproximadamente)
REPLACE INTO `software_versions` (`id`, `software_id`, `version`, `fecha_lanzamiento`, `eol_date`, `descripcion`, `created_at`, `updated_at`) VALUES
	(5, 5, '11 Pro 24H2', NULL, NULL, 'Extracted from name: Microsoft Windows 11 Pro 24H2', '2026-01-05 03:23:52', '2026-01-05 03:35:34'),
	(6, 6, '2021', NULL, NULL, 'Extracted from name: Microsoft Office Hogar y Empresas 2021', '2026-01-05 03:23:52', '2026-01-05 03:35:35'),
	(7, 5, '11 Pro 23H2', NULL, NULL, 'Extracted from name: Microsoft Windows 11 Pro 23H2', '2026-01-05 03:23:52', '2026-01-05 03:35:35'),
	(9, 6, '2019', NULL, NULL, 'Extracted from name: Microsoft Office Hogar y Empresas 2019', '2026-01-05 03:23:52', '2026-01-05 03:35:35'),
	(10, 5, '10 Pro 22H2', NULL, NULL, 'Extracted from name: Microsoft Windows 10 Pro 22H2', '2026-01-05 03:23:52', '2026-01-05 03:35:35'),
	(11, 11, '2010', NULL, NULL, 'Extracted from name: Microsoft Office Hogar y Pequeña Empresa 2010', '2026-01-05 03:23:52', '2026-01-05 03:35:35'),
	(13, 13, '4.1.15', NULL, NULL, 'Extracted from name: OpenOffice 4.1.15', '2026-01-05 03:23:52', '2026-01-05 03:35:35'),
	(14, 5, '11 Pro 22H2', NULL, NULL, 'Extracted from name: Microsoft Windows 11 Pro 22H2', '2026-01-05 03:23:52', '2026-01-05 03:35:36'),
	(15, 15, '24.8.0.3', NULL, NULL, 'Extracted from name: LibreOffice 24.8.0.3', '2026-01-05 03:23:52', '2026-01-05 03:35:36'),
	(16, 16, '10.5', NULL, NULL, NULL, '2026-01-05 06:20:04', '2026-01-05 06:20:04'),
	(17, 16, '11.0', NULL, NULL, 'revisión de software', '2026-01-06 04:59:48', '2026-01-06 04:59:48');

-- Volcando estructura para tabla siatdb.solicitudes_compra
CREATE TABLE IF NOT EXISTS `solicitudes_compra` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ciudad_id` bigint unsigned NOT NULL,
  `ubicacion_id` bigint unsigned NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `justificacion` text COLLATE utf8mb4_unicode_ci,
  `estado_solicitud_id` bigint unsigned NOT NULL,
  `pdf_formulario_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL,
  `fecha_aprobacion` datetime DEFAULT NULL,
  `solicitado_por_id` bigint unsigned NOT NULL,
  `aprobado_por_id` bigint unsigned DEFAULT NULL,
  `comprado_por_id` bigint unsigned DEFAULT NULL,
  `proveedor_propuesto_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solicitudes_compra_ciudad_id_foreign` (`ciudad_id`),
  KEY `solicitudes_compra_ubicacion_id_foreign` (`ubicacion_id`),
  KEY `solicitudes_compra_estado_solicitud_id_foreign` (`estado_solicitud_id`),
  KEY `solicitudes_compra_solicitado_por_id_foreign` (`solicitado_por_id`),
  KEY `solicitudes_compra_aprobado_por_id_foreign` (`aprobado_por_id`),
  KEY `solicitudes_compra_comprado_por_id_foreign` (`comprado_por_id`),
  KEY `solicitudes_compra_proveedor_propuesto_id_foreign` (`proveedor_propuesto_id`),
  CONSTRAINT `solicitudes_compra_aprobado_por_id_foreign` FOREIGN KEY (`aprobado_por_id`) REFERENCES `users` (`id`),
  CONSTRAINT `solicitudes_compra_ciudad_id_foreign` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `solicitudes_compra_comprado_por_id_foreign` FOREIGN KEY (`comprado_por_id`) REFERENCES `users` (`id`),
  CONSTRAINT `solicitudes_compra_estado_solicitud_id_foreign` FOREIGN KEY (`estado_solicitud_id`) REFERENCES `estados_solicitud` (`id`),
  CONSTRAINT `solicitudes_compra_proveedor_propuesto_id_foreign` FOREIGN KEY (`proveedor_propuesto_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `solicitudes_compra_solicitado_por_id_foreign` FOREIGN KEY (`solicitado_por_id`) REFERENCES `users` (`id`),
  CONSTRAINT `solicitudes_compra_ubicacion_id_foreign` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicaciones` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.solicitudes_compra: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.tareas_admin
CREATE TABLE IF NOT EXISTS `tareas_admin` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `assigned_city_id` bigint unsigned DEFAULT NULL,
  `assigned_user_id` bigint unsigned DEFAULT NULL,
  `task_type_id` bigint unsigned NOT NULL,
  `due_date` date DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `completed_by` bigint unsigned DEFAULT NULL,
  `proof_document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_tasks_created_by_foreign` (`created_by`),
  KEY `admin_tasks_assigned_city_id_foreign` (`assigned_city_id`),
  KEY `admin_tasks_assigned_user_id_foreign` (`assigned_user_id`),
  KEY `admin_tasks_task_type_id_foreign` (`task_type_id`),
  KEY `admin_tasks_completed_by_foreign` (`completed_by`),
  CONSTRAINT `admin_tasks_assigned_city_id_foreign` FOREIGN KEY (`assigned_city_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `admin_tasks_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `admin_tasks_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `admin_tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `admin_tasks_task_type_id_foreign` FOREIGN KEY (`task_type_id`) REFERENCES `task_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.tareas_admin: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.tareas_supervisor
CREATE TABLE IF NOT EXISTS `tareas_supervisor` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supervisor_id` bigint unsigned NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_asignacion` datetime NOT NULL,
  `fecha_limite` date NOT NULL,
  `estado_tarea_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tareas_supervisor_supervisor_id_foreign` (`supervisor_id`),
  KEY `tareas_supervisor_estado_tarea_id_foreign` (`estado_tarea_id`),
  CONSTRAINT `tareas_supervisor_estado_tarea_id_foreign` FOREIGN KEY (`estado_tarea_id`) REFERENCES `estados_tarea` (`id`),
  CONSTRAINT `tareas_supervisor_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.tareas_supervisor: ~0 rows (aproximadamente)

-- Volcando estructura para tabla siatdb.task_types
CREATE TABLE IF NOT EXISTS `task_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.task_types: ~5 rows (aproximadamente)
REPLACE INTO `task_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'Prueba de Corte', '2025-12-29 00:52:40', '2025-12-29 00:52:40'),
	(2, 'Inventario', '2025-12-29 00:52:40', '2025-12-29 00:52:40'),
	(3, 'Actualización Software', '2025-12-29 00:52:40', '2025-12-29 00:52:40'),
	(4, 'Mantenimiento General', '2025-12-29 00:52:40', '2025-12-29 00:52:40'),
	(5, 'Auditoría', '2025-12-29 00:52:40', '2025-12-29 00:52:40');

-- Volcando estructura para tabla siatdb.tipos_activo
CREATE TABLE IF NOT EXISTS `tipos_activo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_activo_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.tipos_activo: ~12 rows (aproximadamente)
REPLACE INTO `tipos_activo` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'Laptop', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(2, 'Desktop', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(3, 'Servidor', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(4, 'Impresora', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(5, 'Monitor', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(6, 'Switch', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(7, 'Router', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(8, 'UPS', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(9, 'Tablet', '2025-12-31 05:09:05', '2025-12-31 05:09:05'),
	(10, 'Teclado/Mouse', '2026-01-03 22:15:51', '2026-01-03 22:15:51'),
	(11, 'Punto de Acceso', '2026-01-03 22:15:51', '2026-01-03 22:15:51'),
	(12, 'Otro', '2026-01-03 22:15:51', '2026-01-03 22:15:51');

-- Volcando estructura para tabla siatdb.tipos_control
CREATE TABLE IF NOT EXISTS `tipos_control` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_control_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.tipos_control: ~2 rows (aproximadamente)
REPLACE INTO `tipos_control` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'Seguridad', NULL, NULL),
	(2, 'Físico', NULL, NULL);

-- Volcando estructura para tabla siatdb.tipos_estandar
CREATE TABLE IF NOT EXISTS `tipos_estandar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_estandar_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.tipos_estandar: ~3 rows (aproximadamente)
REPLACE INTO `tipos_estandar` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'Hardware', NULL, NULL),
	(2, 'Sistema Operativo', NULL, NULL),
	(3, 'Software', NULL, NULL);

-- Volcando estructura para tabla siatdb.tipos_mantenimiento
CREATE TABLE IF NOT EXISTS `tipos_mantenimiento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_mantenimiento_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.tipos_mantenimiento: ~2 rows (aproximadamente)
REPLACE INTO `tipos_mantenimiento` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'preventivo', NULL, NULL),
	(2, 'correctivo', NULL, NULL);

-- Volcando estructura para tabla siatdb.tipos_sucursal
CREATE TABLE IF NOT EXISTS `tipos_sucursal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blue',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_types_name_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.tipos_sucursal: ~5 rows (aproximadamente)
REPLACE INTO `tipos_sucursal` (`id`, `nombre`, `descripcion`, `color`, `sort_order`, `created_at`, `updated_at`) VALUES
	(1, 'Sucursal', NULL, 'blue', 1, '2025-12-29 00:52:40', '2025-12-29 04:46:35'),
	(2, 'Agencia', NULL, 'indigo', 2, '2025-12-29 00:52:40', '2025-12-29 04:46:35'),
	(3, 'ATM', NULL, 'amber', 3, '2025-12-29 00:52:40', '2025-12-29 04:46:35'),
	(4, 'Oficina Externa', NULL, 'emerald', 4, '2025-12-29 00:52:40', '2025-12-29 04:46:35'),
	(11, 'PAF', NULL, 'blue', 5, '2025-12-30 07:38:15', '2025-12-30 07:38:15');

-- Volcando estructura para tabla siatdb.ubicaciones
CREATE TABLE IF NOT EXISTS `ubicaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ciudad_id` bigint unsigned NOT NULL,
  `tipo_sucursal_id` bigint unsigned NOT NULL,
  `padre_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefonos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_ubicacion` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ubicaciones_ciudad_id_foreign` (`ciudad_id`),
  KEY `ubicaciones_tipo_sucursal_id_foreign` (`tipo_sucursal_id`),
  KEY `ubicaciones_padre_id_foreign` (`padre_id`),
  CONSTRAINT `ubicaciones_ciudad_id_foreign` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ubicaciones_padre_id_foreign` FOREIGN KEY (`padre_id`) REFERENCES `ubicaciones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ubicaciones_tipo_sucursal_id_foreign` FOREIGN KEY (`tipo_sucursal_id`) REFERENCES `tipos_sucursal` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=782 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.ubicaciones: ~82 rows (aproximadamente)
REPLACE INTO `ubicaciones` (`id`, `ciudad_id`, `tipo_sucursal_id`, `padre_id`, `nombre`, `direccion`, `telefonos`, `codigo_ubicacion`, `created_at`, `updated_at`) VALUES
	(610, 3, 1, NULL, 'Sucursal Oruro', 'Calle La Plata s/n esq. Calle Sucre', '5250927', NULL, '2025-12-29 03:48:27', '2026-01-04 18:35:03'),
	(611, 3, 2, NULL, 'Agencia Av. del Ejército', 'Av. del Ejercito No. 600, esquina Av. Tacna, zona Este.', '5283580', NULL, '2025-12-29 03:48:27', '2026-01-04 17:42:00'),
	(612, 3, 2, NULL, 'Agencia Mercado Bolívar', 'Calle Bolivar No. 282, entre Brasil y Rayca Bacovick, Zona Este', '5281641', NULL, '2025-12-29 03:48:27', '2026-01-04 17:42:00'),
	(613, 3, 4, NULL, 'Oficina Externa Escara', 'Calle Sucre esq. Bolivar frente a Plaza 16 de Septiembre del municipio de Escara, Oruro', NULL, NULL, '2026-01-02 20:39:27', '2026-01-04 17:42:00'),
	(698, 1, 1, NULL, 'Sucursal La Paz', 'Av. 16 de Julio No. 1440, Zona Central.', '2317211 - 2369955', NULL, '2026-01-04 07:32:39', '2026-01-04 17:41:59'),
	(699, 1, 3, 698, 'ATM - Sucursal La Paz', 'Av. 16 de julio No. 1440, Zona Central', NULL, NULL, '2026-01-04 07:37:20', '2026-01-04 17:57:33'),
	(700, 1, 2, NULL, 'Agencia Arce', 'Av. Arce No. 2799 esq. calle Cordero, Zona San Jorge', '2434142', NULL, '2026-01-04 07:37:20', '2026-01-04 17:41:59'),
	(701, 1, 3, 700, 'ATM - Agencia Arce', 'Avenida Arce N°2799, esquina Calle Cordero, Zona San Jorge', NULL, NULL, '2026-01-04 07:37:20', '2026-01-04 17:57:33'),
	(702, 1, 2, NULL, 'Agencia Tumusla', 'Av. Tumusla No. 765, entre Av. Buenos Aires y Plaza Garita de Lima, Zona 14 de Septiembre.', '2117372', NULL, '2026-01-04 07:37:20', '2026-01-04 17:41:59'),
	(703, 1, 3, 702, 'ATM - Agencia Tumusla', 'Av. Tumusla N°765 entre Av. La Buenos Aires y Plaza Garita de Lima, zona 14 de Septiembre.', NULL, NULL, '2026-01-04 07:37:20', '2026-01-04 17:57:33'),
	(704, 1, 2, NULL, 'Agencia Villa Fátima', 'Av. Las Américas No. 353, Zona Villa Fatima.', '2215120', NULL, '2026-01-04 07:37:20', '2026-01-04 17:41:59'),
	(705, 1, 3, 704, 'ATM - Agencia Villa Fátima', 'Av. Las Américas N°353, zona Villa Fátima.', NULL, NULL, '2026-01-04 07:37:20', '2026-01-04 17:57:33'),
	(706, 1, 2, NULL, 'Agencia San Miguel', 'Av. Mariscal Montenegro No. 1246.', '2119204 – 2119180', NULL, '2026-01-04 07:37:20', '2026-01-04 17:41:59'),
	(707, 1, 3, 706, 'ATM - Agencia San Miguel', 'Av. Mariscal Montenegro No. 1246', NULL, NULL, '2026-01-04 07:37:20', '2026-01-04 17:57:33'),
	(708, 1, 2, NULL, 'Agencia Achumani', 'Avenida Garcia Lanza No. 15 entre calles 11 y 12 achumani, zona sur.', '2794070', NULL, '2026-01-04 07:37:20', '2026-01-04 17:41:59'),
	(709, 1, 3, 708, 'ATM - Agencia Achumani', 'Av. García Lanza No.15 entre calles 11 y 12, Achumani', NULL, NULL, '2026-01-04 07:37:20', '2026-01-04 17:57:33'),
	(710, 1, 2, NULL, 'Agencia Entre Ríos', 'Calle Picada Chaco No.828, Zona El Tejar.', '2386117-2380076', NULL, '2026-01-04 07:37:20', '2026-01-04 17:42:00'),
	(711, 1, 3, 710, 'ATM - Agencia Entre Ríos', 'Calle Picada Chaco No.828', NULL, NULL, '2026-01-04 07:37:21', '2026-01-04 17:57:34'),
	(712, 1, 3, NULL, 'ATM Edificio Señor de Mayo', NULL, NULL, NULL, '2026-01-04 07:37:21', '2026-01-04 07:37:21'),
	(713, 62, 1, NULL, 'Sucursal El Alto', 'Calle Jorge Carrasco No.79 entre calles 4 y 5, Zona 12 de Octubre.', '2821474 - 2821306', NULL, '2026-01-04 07:37:21', '2026-01-04 18:48:54'),
	(714, 62, 2, NULL, 'Agencia 16 de Julio', 'Av.Alfonzo Ugarte No. 50, Lote 12, Manzano 17, Zona 16 de Julio', '2847448', NULL, '2026-01-04 07:37:21', '2026-01-04 18:59:47'),
	(715, 62, 2, NULL, 'Agencia Río Seco', 'Av. Juan Pablo II No. 3030, Lote No. 1C Manzana s/n, Zona Rio Seco.', '2861937', NULL, '2026-01-04 07:37:21', '2026-01-04 19:00:23'),
	(716, 62, 2, NULL, 'Agencia Cruce Villa Adela', 'Av. Ladislao Cabrera No.15, Zona Villa Bolivar Municipal (cruce Villa Adela)', '2852332', NULL, '2026-01-04 07:37:21', '2026-01-04 19:00:50'),
	(717, 62, 2, NULL, 'Agencia Villa Dolores', 'Av. Antofagasta No. 558 esq. calle 6, Zona Villa Dolores', '2918789', NULL, '2026-01-04 07:37:21', '2026-01-04 19:01:12'),
	(718, 62, 4, NULL, 'Oficina Externa Puerto Carabuco', 'Plaza Principal 3 de Mayo del Puerto Mayor de Carabuco', NULL, NULL, '2026-01-04 07:37:21', '2026-01-04 19:01:41'),
	(719, 62, 4, NULL, 'Oficina Externa Huatajata', 'Carretera La Paz - Copacabana, Municipio de Huatajata s/n', NULL, NULL, '2026-01-04 07:37:21', '2026-01-04 19:02:01'),
	(721, 3, 3, 610, 'ATM - Sucursal Oruro', 'Calle La Plata s/n esq. Calle Sucre', NULL, NULL, '2026-01-04 07:37:21', '2026-01-04 18:35:19'),
	(722, 3, 3, 611, 'ATM - Agencia Av. del Ejército', 'Av. Ejercito Nacional No.600 esq. Tacna, Zona Este.', NULL, NULL, '2026-01-04 07:37:21', '2026-01-04 17:57:34'),
	(723, 3, 3, 612, 'ATM - Agencia Mercado Bolívar', 'Calle Bolívar No. 282, entre Brasil y R. Bacovick, Zona Este', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(724, 4, 1, NULL, 'Sucursal Cochabamba', 'Av. Ballivian No.739, entre las calles Teniente Arévalo y La Paz, Edificio "Prado Business Center"', '4506060 - 4583041', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(725, 4, 2, NULL, 'Agencia 14 de septiembre', 'Plaza 14 de Septiembre No.205, esquina calle Baptista, zona Central', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(726, 4, 3, 725, 'ATM - Agencia 14 de septiembre', 'Plaza 14 de Septiembre No.205 esq.  Calle Baptista', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(727, 4, 2, NULL, 'Agencia América', 'A. América No.0969 entre Melchor Urquidi y Miguel Aguirre, zona Norte.', '4254689', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(728, 4, 3, 727, 'ATM - Agencia América', 'A. América No.0969 entre Melchor Urquidi y Miguel Aguirre, zona Norte.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(729, 4, 2, NULL, 'Agencia La Cancha', 'Calle Esteban Arze N°1384 Mall Cochabamba, Zona La Cancha', '4557022 - 4557023', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(730, 4, 3, 729, 'ATM - Agencia La Cancha', 'Calle Esteban Arze No.1384 Mall Cochabamba, zona La Cancha', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(731, 4, 2, NULL, 'Agencia Quillacollo', 'Calle Cochabamba s/n, entre Héroes del Chaco y Ballivián, Zona Central Quillacollo', '4391197 - 4391198', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(732, 4, 3, 731, 'ATM - Agencia Quillacollo', 'Calle Cochabamba s/n entre Heroes del Chaco y 6 de Agosto', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(733, 4, 2, NULL, 'Agencia Sacaba', 'Calle Bolivar s/n esq. calle Colon, Zona Central', '4703590 - 4703812', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(734, 4, 3, 733, 'ATM - Agencia Sacaba', 'Calle Bolívar s/n esq. Calle Colón.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(735, 4, 2, NULL, 'Agencia Norte', 'Av. América N° 475 entre Jorge Washington y Napolio Irigoyen, Zona Cala Cala', '4506060', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(736, 4, 3, 735, 'ATM - Agencia Norte', 'Av. America N° 475 entre Washington y N. Irigoyen Zona Cala Cala.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(737, 4, 4, NULL, 'Oficina Externa Tacachi', 'Av. Fructuoso Orellana s/n, zona Central de Tacachi,', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(738, 4, 4, NULL, 'Oficina Externa Bolivar', 'Plaza 6 de Agosto s/n del municipio de Bolivar, provincia Bolivar, departamento de Cochabamba, Gobierno Autónomo Municipal de Bolivar.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(739, 4, 3, NULL, 'ATM Cala Cala', 'Calle Juan Huallparimachi s/n, zona Cala Cala', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(740, 4, 3, NULL, 'ATM Hotel Cochabamba', 'Calle Beni N° 415 y Plazuela Ubaldo Anze', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(741, 4, 3, NULL, 'ATM El Prado Cochabamba II', 'Av. Ballivian No.745 entre calles Teniente Arévalo y La Paz (acera oeste), Edif. Fortaleza II', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(742, 5, 1, NULL, 'Sucursal Sucre', 'Calle San Alberto No. 108, Zona Central.', '6427880 - 6440680', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(743, 5, 3, 742, 'ATM - Sucursal Sucre', 'Calle San Alberto No. 108.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(744, 5, 2, NULL, 'Agencia Mercado Campesino', 'Calle Guillermo Loayza No. 586 esquina J. Prudencio Bustillos, Zona Mercado Campesino', '6912435 - 6435629', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(745, 5, 3, 744, 'ATM - Agencia Mercado Campesino', 'Calle Guillermo Loayza No. 586 esquina J. Prudencio Bustillos, Zona Mercado Campesino.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(746, 6, 1, NULL, 'Sucursal Santa Cruz', 'Calle Gabriel René Moreno No. 140.', '3322929 - 3334307', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(747, 6, 3, 746, 'ATM - Sucursal Santa Cruz', 'Calle Gabriel René Moreno No. 140.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(748, 6, 2, NULL, 'Agencia Vírgen de Cotoca', 'Av. Virgen de Cotoca No. 2090, Zona Lazareto', '3492030', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(749, 6, 3, 748, 'ATM - Agencia Vírgen de Cotoca', 'Avenida Virgen de Cotoca No. 2090, zona Lazareto.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(750, 6, 2, NULL, 'Agencia Monseñor Rivero', 'Av. Monseñor Rivero No. 328, entre primer y segundo anillo, zona norte', '33321684', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(751, 6, 3, 750, 'ATM - Agencia Monseñor Rivero', 'Avenida Monseñor Rivero Nº 328, entre 1er. y 2do anillo.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(752, 6, 2, NULL, 'Agencia Mercado Abasto', 'Av. Roque Aguilera No.3110, 3er. anillo interno casi esquina Av. Piraí, zona Oeste (Antiguo Mercado Abasto)', '3598951 - 3520584', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(753, 6, 3, 752, 'ATM - Agencia Mercado Abasto', 'Av. Roque Aguilera No.3110, 3er. anillo interno casi esquina Av. Piraí, zona Oeste (Antiguo Mercado Abasto)', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(754, 6, 2, NULL, 'Agencia Plan 3000', 'Av. Paurito No. 5520 6to Anillo diagonal al surtidor Gas y Gas, ciudadela Andres Ibañez.', '3486158', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:00'),
	(755, 6, 3, 754, 'ATM - Agencia Plan 3000', 'Av. Paurito No.5520 y 6to. Anillo (diagonal al surtidor Gas y Gas).', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(756, 6, 2, NULL, 'Agencia La Ramada', 'Avenida Isabel La Catolica No.349, zona La Ramada, UV 10 Manzano 23 entre primer y segundo anillo.', '3587789 - 3557375', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:01'),
	(757, 6, 3, 756, 'ATM - Agencia La Ramada', 'Av. Isabel La Católica No. 349, zona La Ramada, U.V.10, Manzano 23 entre primer y segundo anillo.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(758, 6, 2, NULL, 'Agencia Mutualista', 'Av. Japón No. 3577, 3er anillo externo esquina Calle Combate Bahía, zona Noreste (Mercado Mutualista)', '3469963', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:01'),
	(759, 6, 3, 758, 'ATM - Agencia Mutualista', 'Av. Japón No. 3577, 3er anillo externo esquina Calle Combate Bahía, zona Noreste (Mercado Mutualista)', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(760, 6, 2, NULL, 'Agencia Montero', 'Calle Warnes No. 122, Manzano 27, UV 5, a media cuadra de la Plaza Principal de Montero.', '9227429 - 9227411', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:01'),
	(761, 6, 3, 760, 'ATM - Agencia Montero', 'Calle Warnes No.122 Manzana 27, UV 5, a media cuadra de la Plaza Principal', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(762, 6, 2, NULL, 'Agencia Villa Primero de Mayo', 'Av. Cumavi No. 4950, entre 5to. y 6to. anillo, zona Villa 1ro. de Mayo.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:01'),
	(763, 6, 3, 762, 'ATM - Agencia Villa Primero de Mayo', 'Av. Cumavi No. 4950, entre 5to. y 6to. anillo, zona Villa 1ro. de Mayo.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(764, 6, 3, NULL, 'ATM Aeropuerto Viru Viru', 'Av. G77, carretera a Warnes entre kilometros 7 al 17, provincia Ignacio Warnes', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:01'),
	(765, 6, 3, NULL, 'ATM GENEX', '3er. Anillo Av. Banzer esq. Av. Japon (surtidor Genex)', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:01'),
	(766, 7, 1, NULL, 'Sucursal Tarija', 'Calle La Madrid No. 330 frente a Plaza Luis de Fuentes, Zona Central.', '6643566 - 6643567', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:01'),
	(767, 7, 3, 766, 'ATM - Sucursal Tarija', 'Calle La Madrid N°0330 frente a la Plaza Luis de Fuentes.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(768, 7, 2, NULL, 'Agencia Mercado Campesino', 'Av. Froilán Tejerina No. 1670 entre Av. Panamericana y México, Zona Mercado Campesino.', '6675571 - 6668917', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:01'),
	(769, 7, 3, 768, 'ATM - Agencia Mercado Campesino', 'Avenida Froilan Tejerina No. 1670, entre avenida Panamericana y Mexico, zona Mercado Campesino.', NULL, NULL, '2026-01-04 07:37:22', '2026-01-04 17:57:34'),
	(770, 7, 2, NULL, 'Agencia Bermejo', 'Av. Barrientos Ortuño No. 635 entre calles Cochabamba y Alfredo Ameller, Zona Centra', '6963550 -  6963561', NULL, '2026-01-04 07:37:22', '2026-01-04 17:42:01'),
	(771, 7, 3, 770, 'ATM - Agencia Bermejo', 'Av. Barrientos Ortuño No.635 entre calles Cochabamba y Alfredo Ameller, Zona Central', NULL, NULL, '2026-01-04 07:37:23', '2026-01-04 17:57:34'),
	(772, 8, 1, NULL, 'Sucursal Potosí', 'Plaza 6 de Agosto No. 11 entre la calle Junín y Padilla, frente al Obelisco.', '6222747', NULL, '2026-01-04 07:37:23', '2026-01-04 17:42:01'),
	(773, 8, 3, 772, 'ATM - Sucursal Potosí', 'Plaza 6 de Agosto N° 11, frente al Obelisco', NULL, NULL, '2026-01-04 07:37:23', '2026-01-04 17:57:34'),
	(774, 8, 2, NULL, 'Agencia Villazón', 'Calle La Paz Nº 198 entre Av. Antofagasta e Independencia', '25965443', NULL, '2026-01-04 07:37:23', '2026-01-04 17:42:01'),
	(775, 8, 3, 774, 'ATM - Agencia Villazón', 'Calle La Paz  N° 198 entre calle 20 de Mayo e Independencia', NULL, NULL, '2026-01-04 07:37:23', '2026-01-04 17:57:34'),
	(776, 1, 3, 712, 'ATM - ATM Edificio Señor de Mayo', 'Avenida 16 de Julio No.591, Edificio Señor de Mayo, zona central', NULL, NULL, '2026-01-04 17:42:00', '2026-01-04 17:57:34'),
	(779, 62, 3, 713, 'ATM - Sucursal El Alto', 'Calle Jorge Carrasco No.79 entre calles 4 y 5, Zona 12 de Octubre.', NULL, NULL, '2026-01-04 19:04:13', '2026-01-04 19:05:46'),
	(780, 62, 3, 714, 'ATM - Agencia 16 de Julio', 'Av.Alfonzo Ugarte No. 50, Lote 12, Manzano 17, Zona 16 de Julio', NULL, NULL, '2026-01-04 19:05:11', '2026-01-04 19:05:11'),
	(781, 62, 3, NULL, 'ATM - Aeropuerto El Alto', 'Av. Héroes Km 7 s/n, Aeropuerto Internacional El Alto', NULL, NULL, '2026-01-04 19:06:34', '2026-01-04 19:06:34');

-- Volcando estructura para tabla siatdb.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `city_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ad_guid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `hire_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `job_title_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_city_id_index` (`city_id`),
  KEY `users_job_title_id_foreign` (`job_title_id`),
  KEY `users_branch_id_foreign` (`branch_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla siatdb.users: ~55 rows (aproximadamente)
REPLACE INTO `users` (`id`, `first_name`, `last_name`, `name`, `email`, `username`, `email_verified_at`, `password`, `role`, `city_id`, `branch_id`, `position`, `ad_guid`, `phone`, `is_active`, `hire_date`, `termination_date`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`, `job_title_id`) VALUES
	(1, 'Supervisor', 'Nacional', 'Supervisor Nacional', 'admin@siat.com', NULL, NULL, '$2y$12$xS7MDIGCD2.OYFw6FoHrTuSOUsVFGyy/ibaxXKORVKyNUZsDebx0S', 'admin', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 00:56:03', '2025-12-29 00:56:03', NULL),
	(2, 'Admin', 'Oruro', 'Admin Oruro', 'admin.oruro@siat.com', NULL, NULL, '$2y$12$9GqhaeI3yAUsr8otydcj2O2uY8sf3wpSY8h.K4qBbL0Z96JZaGU3S', 'city_admin', 3, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 00:56:04', '2025-12-29 00:56:04', NULL),
	(3, 'Admin', 'La Paz', 'Admin La Paz', 'admin.lapaz@siat.com', NULL, NULL, '$2y$12$YxII9g5jUo.VmK63pN503O9IYnZqDTmR0YPt0A1QuVz.mF5Pwizsi', 'city_admin', 1, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 00:56:05', '2025-12-29 00:56:05', NULL),
	(4, 'Juan', 'Perez', 'Juan Perez', 'user@siat.com', NULL, NULL, '$2y$12$FvEsEO3YQjVxAvBYApaZgegBVmBLRegxPq6qC1F2lj6f.j5J4WX.a', 'user', NULL, NULL, 'Cajero', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 00:56:05', '2026-01-02 22:24:15', 6),
	(5, 'SHIRLEY MARIA', 'SALINAS RADIC', 'SHIRLEY MARIA SALINAS RADIC', 'shirley-maria-salinas-radic@fortaleza.com.bo', NULL, NULL, '$2y$12$41QSTvZbJ8KO.z8jZ4tNOuf7Ippoh5cUfV4FHMYfiE59/EjNM2Qu6', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:56', '2026-01-03 02:37:57', NULL),
	(6, 'ADRIANA MIRJANA', 'KRELLAC MEDRANO', 'ADRIANA MIRJANA KRELLAC MEDRANO', 'adriana-mirjana-krellac-medrano@fortaleza.com.bo', NULL, NULL, '$2y$12$NuTq1COSpE.BGOm7QtLTueszTrRXEBY7XmTSCXwxgXP7B2EqmRWba', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:56', '2026-01-03 02:37:57', NULL),
	(7, 'KAREN CAROL', 'ARIAS NINA', 'KAREN CAROL ARIAS NINA', 'karias@grupofortaleza.com.bo', 'karias', NULL, '$2y$12$1ep9XqrAQfbG3wQkOieEQeTNSDuV9NzrNQG4bN7rBGo1MSJpsAVhm', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-01-23', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:57', '2026-01-03 03:50:14', 24),
	(8, 'ROLANDO', 'HUARACHI COLQUE', 'ROLANDO HUARACHI COLQUE', 'rhuarachi@grupofortaleza.com.bo', 'rhuarachi', NULL, '$2y$12$9Ft9bzwjK7oV3LhsI6RsbeJGQRi5j3qtJWBOuRLTT4gUVlwHhokxG', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-07-03', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:57', '2026-01-03 04:15:56', 3),
	(9, 'LETICIA', 'GONZALES BALDELLON', 'LETICIA GONZALES BALDELLON', 'lgonzales@grupofortaleza.com.bo', 'lgonzales', NULL, '$2y$12$lSFN0SPe2saOJDpOubR0WuHuPlsJuoVrJxl5eDT0bal65WGJ5H4Ee', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-03-12', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:58', '2026-01-03 04:12:20', NULL),
	(10, 'MATEO JUSTINIANO', 'VASQUEZ CRUZ', 'MATEO JUSTINIANO VASQUEZ CRUZ', 'mateo-justiniano-vasquez-cruz@fortaleza.com.bo', NULL, NULL, '$2y$12$ZXuuW./9loU0i1iCNpAbZ.T.MKQ22eo6i4qAtiBj4zYs5qw.QO4WO', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:58', '2026-01-03 02:37:57', NULL),
	(11, 'MARIA EUGENIA', 'ZABALAGA SEJAS', 'MARIA EUGENIA ZABALAGA SEJAS', 'maria-eugenia-zabalaga-sejas@fortaleza.com.bo', NULL, NULL, '$2y$12$azOzZ.3LxQYX3mNAEGNfKuSGUKq6ybxYnISvlJgoEijTnTySGzjXy', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:51:59', '2026-01-03 02:37:57', NULL),
	(12, 'CARMEN MARIA', 'MAMANI BUEZO', 'CARMEN MARIA MAMANI BUEZO', 'carmen-maria-mamani-buezo@fortaleza.com.bo', NULL, NULL, '$2y$12$5o.HnanxDM2izC6XthjNouZqaCgTotTYandP7HAmaGc/fVh2gcyuu', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:00', '2026-01-03 02:37:57', NULL),
	(13, 'CARLO ALEJANDRO', 'PEÑA CHAVEZ', 'CARLO ALEJANDRO PEÑA CHAVEZ', 'carlo-alejandro-pena-chavez@fortaleza.com.bo', NULL, NULL, '$2y$12$wjAvkE2Pe4q5TRup/xDCp.oSiTJ51Caliw7V11bcrQz.iezkVEs1y', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:00', '2026-01-03 02:37:57', NULL),
	(14, 'FELIX DARIO', 'MOYA RIVERA', 'FELIX DARIO MOYA RIVERA', 'felix-dario-moya-rivera@fortaleza.com.bo', NULL, NULL, '$2y$12$90XwKggFvaxqAZA5pcSyguwBioujSdlBheqMv00ZawFWWByHw7u3y', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:01', '2026-01-03 02:37:57', NULL),
	(15, 'ALFREDO ALVARO', 'ZEBALLOS BOHORQUEZ', 'ALFREDO ALVARO ZEBALLOS BOHORQUEZ', 'alfredo-alvaro-zeballos-bohorquez@fortaleza.com.bo', NULL, NULL, '$2y$12$zxVyDutidudEYJ3ApKJLfeGWb6F8r7VdcH.8j.ro8Lpb0Ald2gYrO', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:01', '2026-01-03 02:37:57', NULL),
	(16, 'GABRIEL GERARDO', 'LAFUENTE BERNAL', 'GABRIEL GERARDO LAFUENTE BERNAL', 'gabriel-gerardo-lafuente-bernal@fortaleza.com.bo', NULL, NULL, '$2y$12$DgciMOI8W5Q.ZSq2dy5ZNuLylaKhhgpbrVu72m3l/774hQf5qRVB6', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:02', '2026-01-03 02:37:57', NULL),
	(17, 'JEANNETTE GLADYS', 'GOMEZ VARGAS', 'JEANNETTE GLADYS GOMEZ VARGAS', 'jgomez@grupofortaleza.com.bo', 'jgomez', NULL, '$2y$12$971xIeoS8rRPxAyxGjSN8.9DM1Kog6f/1LsPpVgy9gH02Ng2WsjWW', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-02-20', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:02', '2026-01-03 04:11:16', 13),
	(18, 'ALBERT ERIC', 'ALAVE MURIEL', 'ALBERT ERIC ALAVE MURIEL', 'aalave@grupofortaleza.com.bo', 'aalave', NULL, '$2y$12$824UH90oL0CLeJiGdeo5suTv0rJ8fS23SRUEJ9lOLCKh0TPWb4Eru', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-05-07', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:03', '2026-01-03 03:44:50', 8),
	(19, 'CRISTINA', 'DAMIAN FLORES', 'CRISTINA DAMIAN FLORES', 'cdamian@grupofortaleza.com.bo', 'cdamian', NULL, '$2y$12$IheekCa.M95KwRKs82SHMeoyA.MwINUh3b7FkAkZ9H15p25MIz7RW', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-05-21', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:03', '2026-01-03 04:09:21', 13),
	(20, 'JOSE ALEJANDRO', 'RIOJA BENAVIDEZ', 'JOSE ALEJANDRO RIOJA BENAVIDEZ', 'jose-alejandro-rioja-benavidez@fortaleza.com.bo', NULL, NULL, '$2y$12$f.2EfW12Rxn4vwDwg3TKT.WsbzhUtqUY4xkSs6/YXVpFmjQCe9riq', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:04', '2026-01-03 02:37:57', NULL),
	(21, 'JUAN CARLOS', 'SALGUERO ROJAS', 'JUAN CARLOS SALGUERO ROJAS', 'juan-carlos-salguero-rojas@fortaleza.com.bo', NULL, NULL, '$2y$12$3QQRIUja1mYK57J/xx0ReeNUr/nHK4.ErPrKKS5zwqUvLXq61pbgq', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:04', '2026-01-03 02:37:57', NULL),
	(22, 'DAGME SIDETRA', 'ILLANES MEAVE', 'DAGME SIDETRA ILLANES MEAVE', 'sillanes@grupofortaleza.com.bo', 'sillanes', NULL, '$2y$12$Iw5eY7Cj4uzsMM3nyNTbPua8ldQ07MOkPTIfzZVqszAObeKdugyZ2', 'user', 3, 652, NULL, NULL, NULL, 1, '2024-12-11', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:05', '2026-01-03 04:19:20', 23),
	(23, 'RODRIGO FREDDY', 'GARCIA HURTADO', 'RODRIGO FREDDY GARCIA HURTADO', 'rgarcia@grupofortaleza.com.bo', 'rgarcia', NULL, '$2y$12$GG69iIsMkCRaXUaHIcIJd.jc8.uwCPp3aT9/8pHTwS8puvQO35wM6', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-01-16', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:05', '2026-01-03 04:10:20', 19),
	(24, 'MARCO MARCELO', 'AYALA HUANCA', 'MARCO MARCELO AYALA HUANCA', 'mayala@grupofortaleza.com.bo', 'mayala', NULL, '$2y$12$O7WnEyWvsvFz32q8J6d5UewoNFOVwYIpeDL4ILLNtDBckrlOUTbYO', 'user', 3, 652, NULL, NULL, NULL, 1, '2015-04-13', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:06', '2026-01-03 03:50:56', 1),
	(25, 'JOSE LUIS', 'ALI UYULI', 'JOSE LUIS ALI UYULI', 'jali@grupofortaleza.com.bo', 'jali', NULL, '$2y$12$gCkU0/2H83xUsEjuX5xUaOBaR2uIqnCQtDdXZJ3g7FayG/axtHRwW', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-12-02', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:07', '2026-01-03 03:46:21', 21),
	(26, 'ALVARO', 'IGNACIO GARECA', 'ALVARO IGNACIO GARECA', 'aignacio@grupofortaleza.com.bo', 'aignacio', NULL, '$2y$12$5YCiWZ.6g1yxouF4h71UD.py3UnqKy0ubFGlWEChqxZZFf9ec21f.', 'user', 3, 652, NULL, NULL, NULL, 1, '2024-10-09', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:07', '2026-01-03 04:18:00', 15),
	(27, 'EDGAR JORGE', 'PARDO ARISPE', 'EDGAR JORGE PARDO ARISPE', 'edgar-jorge-pardo-arispe@fortaleza.com.bo', NULL, NULL, '$2y$12$QDPNACypdBYC4/L.INWZM.NvxCX1NOt4MBnpvLSASvol5zwW.BVTm', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:08', '2026-01-03 02:37:57', NULL),
	(28, 'JORGE AMETH', 'OPORTO COPA', 'JORGE AMETH OPORTO COPA', 'jorge-ameth-oporto-copa@fortaleza.com.bo', NULL, NULL, '$2y$12$/tVTDdiAO.QAVy9JVp1EHejEIziVdcWfz8k7wn4YBuUeXz1lBK.3u', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:08', '2026-01-03 02:37:57', NULL),
	(29, 'MARCELO', 'JIMENEZ ROMERO', 'MARCELO JIMENEZ ROMERO', 'marcelo-jimenez-romero@fortaleza.com.bo', NULL, NULL, '$2y$12$5Jah6sbzIZ/g5JO.KRvhlu21AeKFTXGAR5X/OsC06unQi5gK2wrTO', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:09', '2026-01-03 02:37:57', NULL),
	(30, 'ALVARO MARCELO', 'IBAÑEZ SANTIESTEVEZ', 'ALVARO MARCELO IBAÑEZ SANTIESTEVEZ', 'aibanez@grupofortaleza.com.bo', 'aibanez', NULL, '$2y$12$EAqLlyqdehlUzUvQ0UiSXuLp1ZdJbLdbdCde1/RCtTnDdWrU2HEiy', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-04-23', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:09', '2026-01-03 04:17:26', 6),
	(31, 'DANIELA', 'CRESPO ROJAS', 'DANIELA CRESPO ROJAS', 'dcrespo@grupofortaleza.com.bo', 'dcrespo', NULL, '$2y$12$jrzGZlWAA2cAtN1.x3fUoOLq9bglRaEXjVO28v1qN1nvtk617sOxW', 'user', 3, 652, NULL, NULL, NULL, 1, '2025-01-15', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:10', '2026-01-03 04:08:43', 6),
	(32, 'NIDIA JANNETH', 'PACO CALLE', 'NIDIA JANNETH PACO CALLE', 'nidia-janneth-paco-calle@fortaleza.com.bo', NULL, NULL, '$2y$12$Og6r5hNWG8C0iRsPKYoFgOqhZAN9YLIfhZg.YE.97A.Drn7waa/p6', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:11', '2026-01-03 02:37:57', NULL),
	(33, 'CLAUDIA', 'ROMERO SOLIZ', 'CLAUDIA ROMERO SOLIZ', 'claudia-romero-soliz@fortaleza.com.bo', NULL, NULL, '$2y$12$W3BRCUEPHdSuv5fZ9hpC3ew94tju2TjEzGFBwRhQMqa/8XLb9m8w2', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:11', '2026-01-03 02:37:57', NULL),
	(34, 'CHRISTIAN JAVIER', 'GONZALES CANAZA', 'CHRISTIAN JAVIER GONZALES CANAZA', 'cgonzalesc@grupofortaleza.com.bo', 'cgonzalesc', NULL, '$2y$12$6uzzPwdoLkACNyfcbNUDnuO/YZDvu1rUM1OjAJ.OPqelMyU16apDC', 'user', 3, 654, NULL, NULL, NULL, 1, '2024-08-14', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:12', '2026-01-03 04:13:14', 15),
	(35, 'MAYERLI VALERIA', 'ALIAGA QUISPE', 'MAYERLI VALERIA ALIAGA QUISPE', 'maliaga@grupofortaleza.com.bo', 'maliaga', NULL, '$2y$12$3AKDR.zscIPjsPLj2OFYE.dSlqF9h8CY8qXcDKCATzoKp.2mgNyAC', 'user', 3, 654, NULL, NULL, NULL, 1, '2025-05-01', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:12', '2026-01-04 00:27:03', 2),
	(36, 'MAGALY VIRGINIA', 'CHAVEZ LOPEZ', 'MAGALY VIRGINIA CHAVEZ LOPEZ', 'mchavez@grupofortaleza.com.bo', 'mchavez', NULL, '$2y$12$hwhy53HSQVJyJxoGIAAEN.2uZ8K46oLq//SivfXwfVgiVQYfeq5q6', 'user', 3, 654, NULL, NULL, NULL, 1, '2025-05-29', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:13', '2026-01-03 04:04:53', 25),
	(37, 'MARCELO IVAN', 'HEREDIA HERRERA', 'MARCELO IVAN HEREDIA HERRERA', 'mheredia@grupofortaleza.com.bo', 'mheredia', NULL, '$2y$12$d1soJzB5i1Jp8QinwCIMd.wZkeMXwa4cwVSMQiTTHP/xiwAjKrNz2', 'user', 3, 654, NULL, NULL, NULL, 1, '2025-04-16', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:13', '2026-01-03 04:14:04', 6),
	(38, 'GABY', 'MAMANI CHOQUETOPA', 'GABY MAMANI CHOQUETOPA', 'gaby-mamani-choquetopa@fortaleza.com.bo', NULL, NULL, '$2y$12$D.wprdTpfZdjsIyk3w/xQuNqDPBbA3VizJRBaydpDseLn9JmNWhJa', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:14', '2026-01-03 02:37:58', NULL),
	(39, 'CESAR EDUARDO', 'SOLIZ PERALTA', 'CESAR EDUARDO SOLIZ PERALTA', 'cesar-eduardo-soliz-peralta@fortaleza.com.bo', NULL, NULL, '$2y$12$YT4r52G.nTOD2soWfI8U7O3P7tUsyGqV7rwsTmztMu1ujqb6EVBO2', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:14', '2026-01-03 02:37:58', NULL),
	(40, 'JACKELINE ROSSE MARY', 'LIZARAZU QUISPE', 'JACKELINE ROSSE MARY LIZARAZU QUISPE', 'jackeline-rosse-mary-lizarazu-quispe@fortaleza.com.bo', NULL, NULL, '$2y$12$jj4MzwZKPXdbGXvE9kFxbeB4TZ43nIUK7f0PC2POnox968BIkmGqm', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:15', '2026-01-03 02:37:58', NULL),
	(41, 'JAVIER GERMAN', 'CHOQUE PEREZ', 'JAVIER GERMAN CHOQUE PEREZ', 'jchoque@grupofortaleza.com.bo', 'jchoque', NULL, '$2y$12$AhkaSTmgklufAhlSMH3.ZuXrr75GPWSy.3eD6hBOTrE7VAIxxzFlS', 'user', 3, 654, NULL, NULL, NULL, 1, '2025-06-19', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:15', '2026-01-03 04:07:10', 13),
	(42, 'RUTH ERIKA', 'ACEVEDO HERBAS', 'RUTH ERIKA ACEVEDO HERBAS', 'racevedo@grupofortaleza.com.bo', 'racevedo', NULL, '$2y$12$X2vxdv4tRXU3bB9sA8a.FeFt4Is.XF4b0KN6j3P3SBO4JzDtyC4GO', 'user', 3, 654, NULL, NULL, NULL, 1, '2025-09-10', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:16', '2026-01-03 02:57:35', 13),
	(43, 'DANILO', 'ALVAREZ ARISPE', 'DANILO ALVAREZ ARISPE', 'dalvarez@grupofortaleza.com.bo', 'dalvarez', NULL, '$2y$12$Txh9kTaH46ghcpVdgd0aCO60IF9mTxCjml9QqzCK50AfMLwRqAQi2', 'user', 3, 653, NULL, NULL, NULL, 1, '2025-03-12', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:16', '2026-01-03 03:48:47', 9),
	(44, 'CHRISTIAN RAMIRO', 'ARCE VARGAS', 'CHRISTIAN RAMIRO ARCE VARGAS', 'carce@grupofortaleza.com.bo', 'carce', NULL, '$2y$12$Na0jvlHRq39hCaiiBiZK4u8OwFQoTA8JnxNGiBw.wbh9iSwGhCaQ2', 'user', 3, 653, NULL, NULL, NULL, 1, '2025-05-28', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:17', '2026-01-03 03:49:20', 13),
	(45, 'NATALY JANETH', 'MAMANI VELASQUEZ', 'NATALY JANETH MAMANI VELASQUEZ', 'nataly-janeth-mamani-velasquez@fortaleza.com.bo', NULL, NULL, '$2y$12$QIFe91NVugqXgcn3OsmGxOUZtnxnoZBOcgGrirFzA69qBpg0ZK06O', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:17', '2026-01-03 02:37:58', NULL),
	(46, 'CLAUDIA RAQUEL', 'HUANCA AMAYA', 'CLAUDIA RAQUEL HUANCA AMAYA', 'chuanca@grupofortaleza.com.bo', 'chuanca', NULL, '$2y$12$.T6rbz6b7dgvHmbPcERjJeeG9TMdEgCGqoM6oKSjZfi.9JhwW7a..', 'user', 3, 654, NULL, NULL, NULL, 1, '2024-08-07', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:18', '2026-01-03 04:14:49', 6),
	(47, 'LUIS', 'VIDAURRE LEON', 'LUIS VIDAURRE LEON', 'luis-vidaurre-leon@fortaleza.com.bo', NULL, NULL, '$2y$12$/3giPOJvhJEeWxdIUvMtieFQiEYC5D.2YQxkaG2lVVvCFCh.P1F9e', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:18', '2026-01-03 02:37:58', NULL),
	(48, 'NINETH CRISTINA', 'MONTIEL MURILLO', 'NINETH CRISTINA MONTIEL MURILLO', 'nineth-cristina-montiel-murillo@fortaleza.com.bo', NULL, NULL, '$2y$12$MUFMaTpFHGN2pLFzvU9cverIvO/lnweeSsqwD78OPIf9r2AFXe/Ca', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:19', '2026-01-03 02:37:58', NULL),
	(49, 'SONIA ISMELDA', 'VILLARROEL VILLARROEL', 'SONIA ISMELDA VILLARROEL VILLARROEL', 'sonia-ismelda-villarroel-villarroel@fortaleza.com.bo', NULL, NULL, '$2y$12$fnLMHkTcgmtUKol1/.xfyOlk0KfWgztdf17WMoI2C9lS408vGrwKi', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:19', '2026-01-03 02:37:58', NULL),
	(50, 'MARIA ROSARIO', 'BERNAL TORDOYA', 'MARIA ROSARIO BERNAL TORDOYA', 'mbernal@grupofortaleza.com.bo', 'mbernal', NULL, '$2y$12$McdJsZQL/EVdftjvRXipjOS02Zut0rN0WIplrkNN4w0segDGCYJeu', 'user', 3, 653, NULL, NULL, NULL, 1, '2025-06-20', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:20', '2026-01-03 03:51:36', 13),
	(51, 'ARIEL AMILCAR', 'MENDIETA ASIER', 'ARIEL AMILCAR MENDIETA ASIER', 'ariel-amilcar-mendieta-asier@fortaleza.com.bo', NULL, NULL, '$2y$12$o8FVXFLmv2/Q.7NHg8zeEO13jViGoWJzNkwjIdZjvlTORKfonE76C', 'user', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:21', '2026-01-03 02:37:58', NULL),
	(52, 'ANGEL ARTURO', 'CABALLERO FERNANDEZ', 'ANGEL ARTURO CABALLERO FERNANDEZ', 'acaballero@grupofortaleza.com.bo', 'acaballero', NULL, '$2y$12$6aXxbY01HOe3sDNYXUJCa.lHeQuCUK0uXnCUfEss8Aix0njp6w7Ge', 'user', 3, 653, NULL, NULL, NULL, 1, '2025-05-20', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:21', '2026-01-03 03:52:10', 13),
	(53, 'JOSE', 'SANTOS GUTIERREZ', 'JOSE SANTOS GUTIERREZ', 'jsantos@grupofortaleza.com.bo', 'jsantos', NULL, '$2y$12$R2aD1OwnlcYRJtTtYEHfHO2rZSLZcPP74Y.WcsSWQ4dM3AyfPhIE2', 'user', 3, 653, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:22', '2026-01-03 02:12:00', 2),
	(54, 'ARACELY CLAUDIA', 'CONDORI ARIMOSA', 'ARACELY CLAUDIA CONDORI ARIMOSA', 'acondori@grupofortaleza.com.bo', 'acondori', NULL, '$2y$12$FOzHWCmf0wI0NWvocjuP2.B/0vsyEKW/7MMzgPjKep0Xu/47U10v6', 'user', 3, 655, 'CAJERO', NULL, NULL, 1, '2025-10-22', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:22', '2026-01-03 04:08:06', 6),
	(55, 'BORIS MICHAEL', 'CHOQUE POMA', 'BORIS MICHAEL CHOQUE POMA', 'bchoque@grupofortaleza.com.bo', 'bchoque', NULL, '$2y$12$WLoLloRnI4/iFmiFmpleFO.2s1OUozZZQ4fh9Mx6xyCfm7MrShFpy', 'user', 3, 655, 'OFICIAL DE SERVICIO AL CLIENTE - PUNTO DE RECLAMO', NULL, NULL, 1, '2025-09-11', NULL, NULL, NULL, NULL, NULL, '2026-01-02 18:52:23', '2026-01-03 01:26:55', 15);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

-- --------------------------------------------------------
-- Host:                         v02yrnuhptcod7dk.cbetxkdyhwsb.us-east-1.rds.amazonaws.com
-- Server version:               8.0.40 - Source distribution
-- Server OS:                    Linux
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for c3248bm8zvavug0p
CREATE DATABASE IF NOT EXISTS `c3248bm8zvavug0p` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_cs_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `c3248bm8zvavug0p`;

-- Dumping structure for table c3248bm8zvavug0p.carousel_images
CREATE TABLE IF NOT EXISTS `carousel_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.carousel_images: ~0 rows (approximately)
INSERT INTO `carousel_images` (`id`, `image_path`, `is_active`, `created_at`) VALUES
	(74, 'uploads/carousel/1746371425_1.webp', 1, '2025-05-04 23:10:23'),
	(75, 'uploads/carousel/1746371425_2.webp', 1, '2025-05-04 23:10:23'),
	(76, 'uploads/carousel/1746371425_7.webp', 1, '2025-05-04 23:10:23'),
	(77, 'uploads/carousel/1746371426_4.webp', 1, '2025-05-04 23:10:23'),
	(78, 'uploads/carousel/1746371426_6.webp', 1, '2025-05-04 23:10:23'),
	(79, 'uploads/carousel/1746371426_8.webp', 1, '2025-05-04 23:10:24'),
	(80, 'uploads/carousel/1746371426_5.webp', 1, '2025-05-04 23:10:24'),
	(81, 'uploads/carousel/1746371426_3.webp', 1, '2025-05-04 23:10:24');

-- Dumping structure for table c3248bm8zvavug0p.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table c3248bm8zvavug0p.categories: ~2 rows (approximately)
INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(4, 'Long Sleeve', '2025-05-04 15:16:20', '2025-05-04 15:16:53'),
	(5, 'T-Shirt', '2025-05-04 15:17:05', '2025-05-04 15:17:05');

-- Dumping structure for table c3248bm8zvavug0p.fabrics
CREATE TABLE IF NOT EXISTS `fabrics` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table c3248bm8zvavug0p.fabrics: ~0 rows (approximately)

-- Dumping structure for table c3248bm8zvavug0p.homepage_settings
CREATE TABLE IF NOT EXISTS `homepage_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.homepage_settings: ~0 rows (approximately)
INSERT INTO `homepage_settings` (`id`, `setting_key`, `setting_value`) VALUES
	(1, 'hero_tagline', 'New Arrivals'),
	(2, 'hero_heading', 'From casual hangouts to <span>High-energy moments.</span> Versatility at its best.'),
	(3, 'hero_description', 'Our Air-Cool Fabric T-shirt adapts to every occasion and keeps you cool.'),
	(4, 'banner_title', '<span>CUSTOM</span> SUBLIMATION SERVICE'),
	(5, 'banner_description', 'We offer fully customized sublimation services:'),
	(6, 'banner_list', 'T-shirt\r\nPolo Shirt\r\nBasketball\r\nJersey\r\nLong Sleeves'),
	(7, 'new_release_title', 'New Release'),
	(8, 'new_release_description', 'Unleash the power of style with our Mecha Collection Moto Jerseys.'),
	(9, 'tshirt_title', 'T-Shirt Collection'),
	(10, 'tshirt_description', 'Discover stylish designs and unmatched comfort with our latest collection.'),
	(11, 'longsleeve_title', 'Long Sleeve Collection'),
	(12, 'longsleeve_description', 'Our Aircool Riders Jersey is built for everyday rides—lightweight, breathable, and made for ultimate performance.'),
	(13, 'show_new_release', '1'),
	(14, 'show_tshirt', '1'),
	(15, 'show_longsleeve', '1');

-- Dumping structure for table c3248bm8zvavug0p.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.notifications: ~0 rows (approximately)
INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `icon`, `is_read`, `created_at`) VALUES
	(283, 14, 'asdasd', 'asdasda', 'order_delivered', 'bx bx-bell text-primary', 1, '2025-05-04 01:27:36'),
	(284, 14, 'test', 'ok ba?', 'order_delivered', 'bx bx-bell text-primary', 1, '2025-05-04 01:27:49');

-- Dumping structure for table c3248bm8zvavug0p.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `lastname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `zipcode` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `payment_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `reference_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`),
  KEY `status_index` (`status`),
  KEY `reference_number_index` (`reference_number`),
  KEY `payment_id_index` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.orders: ~0 rows (approximately)
INSERT INTO `orders` (`id`, `user_id`, `firstname`, `lastname`, `email`, `phone`, `address`, `zipcode`, `payment_method`, `payment_id`, `subtotal`, `shipping_cost`, `total_amount`, `reference_number`, `status`, `created_at`, `updated_at`) VALUES
	(1, 50, 'Mark Darren', 'Oandasan', 'darrenjade24@gmail.com', '09682351236', '25, Planas Ⅲ, Bagong Lipunan ng Crame, 4th District, Quezon City, Eastern Manila District, Metro Manila, 1111, Philippines', '1111', 'GCash', 'cs_TuTE6psugwqsSw2818J8xbW9', 850.00, 50.00, 900.00, 'ORDER-1746365545-50', 'processing', '2025-05-04 13:32:38', '2025-05-04 14:46:38');

-- Dumping structure for table c3248bm8zvavug0p.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL DEFAULT '0',
  `product_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `size` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `order_item_index` (`order_id`),
  KEY `product_id_index` (`product_id`),
  CONSTRAINT `fk_order_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.order_items: ~0 rows (approximately)
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `quantity`, `price`, `subtotal`) VALUES
	(1, 1, 35, 'TEAL', 'M', 1, 850.00, 850.00);

-- Dumping structure for table c3248bm8zvavug0p.otp_verification
CREATE TABLE IF NOT EXISTS `otp_verification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expiry_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.otp_verification: ~0 rows (approximately)

-- Dumping structure for table c3248bm8zvavug0p.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `expiry_time` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.password_resets: ~0 rows (approximately)
INSERT INTO `password_resets` (`id`, `email`, `token`, `expiry_time`, `created_at`) VALUES
	(1, 'darrenjade24@gmail.com', '556841', '2025-05-04 17:47:50', '2025-05-04 16:47:50');

-- Dumping structure for table c3248bm8zvavug0p.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `original_price` decimal(10,2) NOT NULL,
  `discount_percentage` int DEFAULT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fabric` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_new_release` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL,
  `fabric_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `fk_products_category` (`category_id`),
  KEY `fk_products_fabric` (`fabric_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_products_fabric` FOREIGN KEY (`fabric_id`) REFERENCES `fabrics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.products: ~0 rows (approximately)
INSERT INTO `products` (`id`, `sku`, `name`, `description`, `original_price`, `discount_percentage`, `discount_price`, `category`, `fabric`, `is_featured`, `is_new_release`, `created_at`, `updated_at`, `category_id`, `fabric_id`) VALUES
	(23, 'T-S-DANE-510', 'Danel Shirt', 'wow sugoi i wuv bibi', 9999999.00, 100, NULL, 'T-Shirt', '', 0, 0, '2025-03-30 13:22:39', '2025-04-27 05:56:16', NULL, NULL),
	(29, 'LON-MARC-632', 'MARC', 'LONG-SLEEVES- "MARCâ€ Design AIRCOOL Moto Jersey - BEYOND DOUBT CLOTHING', 850.00, 0, NULL, 'Long Sleeve', '', 0, 0, '2025-04-16 00:43:48', '2025-04-27 06:07:19', NULL, NULL),
	(30, 'T-S-RETA-539', 'RETAIN', 'T-SHIRT - "RETAINâ€ Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING', 599.00, 0, NULL, 'T-Shirt', '', 0, 0, '2025-04-16 00:47:33', '2025-04-27 08:34:07', NULL, NULL),
	(31, 'T-S-VALE-521', 'VALE', 'T-SHIRT - "VALEâ€ Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING', 599.00, 0, NULL, 'T-Shirt', '', 0, 0, '2025-04-16 00:50:01', '2025-04-27 08:34:08', NULL, NULL),
	(32, 'T-S-TALE-545', 'TALE', 'T-SHIRT - "TEALâ€ Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING', 549.00, 0, NULL, 'T-Shirt', '', 0, 0, '2025-04-16 00:52:14', '2025-04-27 08:34:09', NULL, NULL),
	(33, 'T-S-TOYO-908', 'TOYO', 'T-SHIRT - "TOYOâ€ Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING', 599.00, 0, NULL, 'T-Shirt', '', 0, 0, '2025-04-16 00:54:20', '2025-04-27 08:34:10', NULL, NULL),
	(34, 'LON-SEUD-374', 'SEUD', 'LONG-SLEEVES- _SEUDâ€ Design AIRCOOL Moto Jersey - BEYOND DOUBT CLOTHING', 344.00, 0, NULL, 'Long Sleeve', '', 0, 0, '2025-04-16 00:57:34', '2025-04-27 08:34:03', NULL, NULL),
	(35, 'LON-TEAL-825', 'TEAL', 'LONG-SLEEVES- "TEALâ€ Design AIRCOOL Moto Jersey - BEYOND DOUBT CLOTHING', 850.00, 0, NULL, 'Long Sleeve', '', 0, 0, '2025-04-16 00:59:29', '2025-04-27 08:34:02', NULL, NULL),
	(63, 'T-S-TEST-168', 'Test', '', 850.00, 18, 700.00, 'T-Shirt', '', 1, 1, '2025-04-27 08:56:30', '2025-05-04 23:31:19', NULL, NULL),
	(69, 'LON-ASAD-743', 'asaddas', 'asdss', 3000.00, 33, 2000.00, 'Long Sleeve', 'Aircool', 0, 0, '2025-05-05 00:24:32', NULL, NULL, NULL);

-- Dumping structure for table c3248bm8zvavug0p.product_images
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.product_images: ~0 rows (approximately)
INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_primary`, `created_at`) VALUES
	(69, 23, 'uploads/products/23/primary_67f16fbaeb6b4.png', 1, '2025-05-04 08:35:57'),
	(78, 29, 'uploads/products/29/primary_1744764228.webp', 1, '2025-05-04 08:35:57'),
	(79, 29, 'uploads/products/29/additional_0_1744764228.webp', 0, '2025-05-04 08:35:57'),
	(80, 29, 'uploads/products/29/additional_1_1744764228.webp', 0, '2025-05-04 08:35:57'),
	(81, 29, 'uploads/products/29/additional_2_1744764228.webp', 0, '2025-05-04 08:35:57'),
	(82, 30, 'uploads/products/30/primary_1744764453.webp', 1, '2025-05-04 08:35:57'),
	(83, 30, 'uploads/products/30/additional_0_1744764453.webp', 0, '2025-05-04 08:35:57'),
	(84, 30, 'uploads/products/30/additional_1_1744764453.webp', 0, '2025-05-04 08:35:57'),
	(85, 30, 'uploads/products/30/additional_2_1744764453.webp', 0, '2025-05-04 08:35:57'),
	(86, 31, 'uploads/products/31/primary_1744764601.webp', 1, '2025-05-04 08:35:57'),
	(87, 31, 'uploads/products/31/additional_0_1744764601.webp', 0, '2025-05-04 08:35:57'),
	(88, 31, 'uploads/products/31/additional_1_1744764601.webp', 0, '2025-05-04 08:35:57'),
	(89, 31, 'uploads/products/31/additional_2_1744764601.webp', 0, '2025-05-04 08:35:57'),
	(90, 32, 'uploads/products/32/primary_1744764734.webp', 1, '2025-05-04 08:35:57'),
	(91, 32, 'uploads/products/32/additional_0_1744764734.webp', 0, '2025-05-04 08:35:57'),
	(92, 32, 'uploads/products/32/additional_1_1744764734.webp', 0, '2025-05-04 08:35:57'),
	(93, 32, 'uploads/products/32/additional_2_1744764734.webp', 0, '2025-05-04 08:35:57'),
	(94, 33, 'uploads/products/33/primary_1744764860.webp', 1, '2025-05-04 08:35:57'),
	(95, 33, 'uploads/products/33/additional_0_1744764860.webp', 0, '2025-05-04 08:35:57'),
	(96, 33, 'uploads/products/33/additional_1_1744764860.webp', 0, '2025-05-04 08:35:57'),
	(97, 33, 'uploads/products/33/additional_2_1744764860.webp', 0, '2025-05-04 08:35:57'),
	(98, 34, 'uploads/products/34/primary_1744765054.webp', 1, '2025-05-04 08:35:57'),
	(99, 34, 'uploads/products/34/additional_0_1744765054.webp', 0, '2025-05-04 08:35:57'),
	(100, 34, 'uploads/products/34/additional_1_1744765054.webp', 0, '2025-05-04 08:35:57'),
	(101, 35, 'uploads/products/35/primary_1744765169.webp', 1, '2025-05-04 08:35:57'),
	(102, 35, 'uploads/products/35/additional_0_1744765169.webp', 0, '2025-05-04 08:35:57'),
	(103, 35, 'uploads/products/35/additional_1_1744765169.webp', 0, '2025-05-04 08:35:57'),
	(104, 35, 'uploads/products/35/additional_2_1744765169.webp', 0, '2025-05-04 08:35:57'),
	(126, 63, 'uploads/products/63/primary_1745744193.webp', 1, '2025-05-04 08:35:57'),
	(140, 69, 'uploads/products/69/primary_1746375874.webp', 1, '2025-05-05 00:24:32'),
	(141, 69, 'uploads/products/69/additional_0_1746375875.webp', 0, '2025-05-05 00:24:32');

-- Dumping structure for table c3248bm8zvavug0p.product_sizes
CREATE TABLE IF NOT EXISTS `product_sizes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `size` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_size` (`product_id`,`size`),
  CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.product_sizes: ~0 rows (approximately)
INSERT INTO `product_sizes` (`id`, `product_id`, `size`, `stock`) VALUES
	(127, 23, 'XS', 0),
	(128, 23, 'S', 0),
	(129, 23, 'M', 0),
	(130, 23, 'L', 0),
	(131, 23, 'XL', 0),
	(132, 23, 'XXL', 0),
	(163, 29, 'XS', 0),
	(164, 29, 'S', 5),
	(165, 29, 'M', 5),
	(166, 29, 'L', 5),
	(167, 29, 'XL', 0),
	(168, 29, 'XXL', 0),
	(169, 30, 'XS', 0),
	(170, 30, 'S', 5),
	(171, 30, 'M', 5),
	(172, 30, 'L', 5),
	(173, 30, 'XL', 0),
	(174, 30, 'XXL', 0),
	(175, 31, 'XS', 0),
	(176, 31, 'S', 5),
	(177, 31, 'M', 5),
	(178, 31, 'L', 5),
	(179, 31, 'XL', 0),
	(180, 31, 'XXL', 0),
	(181, 32, 'XS', 0),
	(182, 32, 'S', 5),
	(183, 32, 'M', 5),
	(184, 32, 'L', 5),
	(185, 32, 'XL', 0),
	(186, 32, 'XXL', 0),
	(187, 33, 'XS', 0),
	(188, 33, 'S', 5),
	(189, 33, 'M', 5),
	(190, 33, 'L', 5),
	(191, 33, 'XL', 0),
	(192, 33, 'XXL', 0),
	(193, 34, 'XS', 0),
	(194, 34, 'S', 5),
	(195, 34, 'M', 5),
	(196, 34, 'L', 5),
	(197, 34, 'XL', 0),
	(198, 34, 'XXL', 0),
	(199, 35, 'XS', 0),
	(200, 35, 'S', 5),
	(201, 35, 'M', 5),
	(202, 35, 'L', 5),
	(203, 35, 'XL', 0),
	(204, 35, 'XXL', 0),
	(285, 63, 'XS', 4),
	(286, 63, 'S', 3),
	(287, 63, 'M', 1),
	(288, 63, 'L', 3),
	(289, 63, 'XL', 3),
	(290, 63, 'XXL', 3),
	(291, 63, 'XXXL', 5),
	(320, 69, 'XS', 0),
	(321, 69, 'S', 0),
	(322, 69, 'M', 0),
	(323, 69, 'L', 0),
	(324, 69, 'XL', 0),
	(325, 69, 'XXL', 0),
	(326, 69, 'XXXL', 0);

-- Dumping structure for table c3248bm8zvavug0p.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `middlename` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_address` text COLLATE utf8mb4_general_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `zipcode` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role_as` tinyint NOT NULL DEFAULT '0' COMMENT '0=user, 1=admin',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `alt_full_address` text COLLATE utf8mb4_general_ci,
  `alt_zipcode` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alt_latitude` decimal(10,8) DEFAULT NULL,
  `alt_longitude` decimal(11,8) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.users: ~0 rows (approximately)
INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `phone_number`, `email`, `username`, `full_address`, `latitude`, `longitude`, `zipcode`, `password`, `role_as`, `created_at`, `alt_full_address`, `alt_zipcode`, `alt_latitude`, `alt_longitude`, `email_verified`) VALUES
	(11, 'Admin', NULL, 'User', NULL, '', 'Admin', NULL, NULL, NULL, NULL, '$2y$12$jUXhF1dvtDpjqqSV.axV3.eNwWLpNR3UsV581UGd.XvkqZEDrxlFm', 1, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(14, 'Jm', '', 'Reyes', '09244618214', 'jundillmharreyes@gmail.com', 'Jiem', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', NULL, NULL, '1102', '$2y$12$UWm6AjgSCAoYvlWg/Njxoe81WXZ0zZZ2ddnc7fi5UK7jr7RK0j2oq', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(26, 'Dominga', 'Ocaya', 'Oandasan', '09979567532', 'domingaoandasan@gmail.com', 'Gay', 'santa clara villas', NULL, NULL, '1125', '$2y$10$XpRIRN5/k1O8/zziln89g.HbjwSwulbAExs.HjZCv1RFnllM7s7se', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(31, 'Alex', '', 'Mopon', '09128753602', 'mopon.maalexsandrakeane@gmail.com', 'aleks', 'Namasape Compound, North Fairview, 5th District, Quezon City, Eastern Manila District, Metro Manila, 1121, Philippines', 14.70583610, 121.06537085, '1121', '$2y$12$lZfNajyc/Tq/NV3YZNrglOQ0qDGd7y7SnuQHfVTj4QD4h6.OpJxaa', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(32, 'Danel', '', 'Tungpalan', '09674801002', 'kookie.jeon.danel.97@gmail.com', 'jeondanel', 'SAMMAR 1 HOA', NULL, NULL, '1119', '$2y$12$8LU..GTMcLNyFUFeLKBU/ep24KNpxmGL66zlpAR11Yq9ZG7pDgIz.', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(33, 'John Kenny', 'Quiachon', 'Reyes', '09928019749', 'johnkennypogitalaga@gmail.com', 'batdimoiprint', 'Taurus Street, San Bartolome, 5th District, Quezon City, Eastern Manila District, Metro Manila, 1116, Philippines', NULL, NULL, '1116', '$2y$12$EAjE.vLNgsp5yVxTFxfWLOTkhXG3Y9j2lFct7hpQ.4NG.SFbhqIDq', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(34, 'lets', '', 'go', '09664010017', 'p.brielleivan@gmail.com', 'admin123', 'Santa Clara Drive, Nagkaisang Nayon, 5th District, Quezon City, Eastern Manila District, Metro Manila, 1125, Philippines', NULL, NULL, '1125', '$2y$12$cUChFzIyd9jjBLliNAFrdO0n5d9ghTQE/57YoPb.nLHLlzssetA3u', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(37, 'Jm', '', 'Reyes', '09682296842', 'reyesjundillmharcalagahan@gmail.com', 'Oreo', 'Novaliches Quezon City', NULL, NULL, '1123', '$2y$10$z4ZdEtYcynqO.7oEDeXFhuxLS3OJqy6Oj.hCBzTZ5nCEq0w4ql4Xq', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(40, 'alfred', 'Garingarao', 'Mejia', '09515684032', 'markalfredmejia@gmail.com', 'yes', 'Circle Food Complex, Elliptical Road, Central, Diliman, 4th District, Quezon City, Eastern Manila District, Metro Manila, 1100, Philippines', NULL, NULL, '1100', '$2y$12$hpEgd9Gm5z2nyP8kUBeIh.jZKd0rBzw0kiWsw.ja/l/o/pWmwl4W6', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(41, 'charles', 'aviles', 'valencia', '09910778890', 'charleskirbyvalencia2125@gmail.com', 'chalseuu12', 'Orange Street, San Bartolome, 5th District, Quezon City, Eastern Manila District, Metro Manila, 1125, Philippines', NULL, NULL, '1125', '$2y$12$B6YU3wyd4vYuVeE.KGlMj.1IcWboUWQw7v76J5EozDun2OMd/yYCG', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(42, 'Nigger', 'Nogger', 'Negger', '09291410304', 'reflafitator@gmail.com', 'Nugger', 'Petron Research & Testing Center, Jesus Street, Pandacan, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', NULL, NULL, '1011', '$2y$12$7vfLvQLJw01GCeVw1RbuBuKiW91JMSBoCzzctb5k4ZOdAlelwrqqC', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(43, 'Kent', '', 'Kopal', '09632587412', 'kentalonzo56@gmail.com', 'Kent', '60, Eugenio Lopez Junior Drive, South Triangle, Scout Area, Diliman, 4th District, Quezon City, Eastern Manila District, Metro Manila, 1103, Philippines', NULL, NULL, '1103', '$2y$12$h1Dj1oWVZhi9r6ixvUqUOu1Uk8wNJ5nMeLBLqBNN75Fmqi1Ak8oai', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(44, 'Jennifer', '', 'Lopez', '09653210422', 'lopezjennifermae@gmail.com', 'Jennifer', 'Dahlia Avenue, Green Fields 1 Subdivision, Fairview, 5th District, Quezon City, Eastern Manila District, Metro Manila, 1122, Philippines', NULL, NULL, '1122', '$2y$12$8PyVXgY4fGYuquz0XV6xCeJUaer90NsXczLL369vxOqEGWhqxZifm', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(45, 'Dylan', 'PILARE', 'Asistoso', '09069133841', 'adelan2sistoso@gmail.com', 'Dylan', 'Lagusnilad Underpass, Barangay 657, 659, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', NULL, NULL, '1000', '$2y$12$OYb8uzr/XaleWRo2i7QwTuOQd6EilzYk9jv0RvnBSaSo16nyD6wEu', 0, '2025-04-28 05:33:00', NULL, NULL, NULL, NULL, 1),
	(46, 'Ryan', 'Guinit', 'Perez', '09615824466', 'perez.johnryan.guinit@gmail.com', 'Aeonaeon', 'Arty 2 Road, Talipapa, 6th District, Quezon City, Eastern Manila District, Metro Manila, 1116, Philippines', NULL, NULL, '1116', '$2y$12$rVMDQnZeU2Z9AHnvFzJqfue5g4KX4jOW9lGs67cdkX1Mzn2ZAjIjy', 0, '2025-04-28 07:09:11', NULL, NULL, NULL, NULL, 1),
	(47, 'Justine', 'E.', 'Ritaga', '09090909', 'justineritaga123@gmail.com', 'Gantz', 'Palm Drive, Pasong Tamo, 6th District, Quezon City, Eastern Manila District, Metro Manila, 1122, Philippines', NULL, NULL, '1122', '$2y$12$LS8Dn/VJfnvQEhnRpad1JOBx65PVk4AaMvKy2Frg7tKMBEsiJZ4Ee', 0, '2025-04-28 08:16:53', NULL, NULL, NULL, NULL, 1),
	(49, 'Admin', NULL, 'User', '0000000000', 'tangina@admin.com', 'tangina', NULL, NULL, NULL, NULL, '$2y$10$zCDosQ3ZW75X1PV026CTg.5FZwlTaunrNCVAKLeLfvEH0ZUIp8GYi', 1, '2025-04-29 18:26:07', NULL, NULL, NULL, NULL, 1),
	(50, 'Mark Darren', 'Ocaya', 'Oandasan', '09682351236', 'darrenjade24@gmail.com', 'Youki', '25, Planas Ⅲ, Bagong Lipunan ng Crame, 4th District, Quezon City, Eastern Manila District, Metro Manila, 1111, Philippines', 14.61514602, 121.05045319, '1111', '$2y$10$Hv3i0FCWD9o4Mc./7Q0sGeQ4Dsm3W6ClJUxIXFaRmc/Z9vh90iBAO', 0, '2025-05-04 16:47:19', NULL, NULL, NULL, NULL, 1),
	(51, 'Janssen', '', 'Cruz', '09923407093', 'janssencedriccruz879@gmail.com', 'Ced', '', NULL, NULL, '1117', '$2y$12$MpjUgqdI0Jor36Wi6iI72e68JnCGjbfEikmqnvriYiIKc2.OaTyne', 0, '2025-05-04 21:31:50', NULL, NULL, NULL, NULL, 1);

-- Dumping structure for table c3248bm8zvavug0p.user_conversations
CREATE TABLE IF NOT EXISTS `user_conversations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `conversation_history` text COLLATE utf8mb4_general_ci NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.user_conversations: ~0 rows (approximately)

-- Dumping structure for table c3248bm8zvavug0p.webhook_events
CREATE TABLE IF NOT EXISTS `webhook_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `processed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id_unique` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table c3248bm8zvavug0p.webhook_events: ~0 rows (approximately)
INSERT INTO `webhook_events` (`id`, `event_id`, `processed_at`) VALUES
	(1, 'evt_MgwL2XMws86urF1qWRYW9AmB', '2025-05-04 13:32:34'),
	(2, 'evt_hYsmV9TtmdmDoSmxawAfSnfB', '2025-05-04 13:32:43');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

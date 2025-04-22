-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2025 at 06:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbecomm`
--

-- --------------------------------------------------------

--
-- Table structure for table `carousel_images`
--

CREATE TABLE `carousel_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_settings`
--

CREATE TABLE `homepage_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_settings`
--

INSERT INTO `homepage_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'hero_tagline', 'New Arrival', '2025-04-06 04:48:10'),
(2, 'hero_heading', 'From casual hangouts to <br><span>High-energy moments.</span> <br>Versatility at its best.', '2025-04-06 05:34:05'),
(3, 'hero_description', 'Our Air-Cool Fabric T-shirt adapts to every occasion and keeps you cool.', '2025-04-06 05:34:29'),
(4, 'banner_title', '<span>CUSTOM</span> SUBLIMATION<br>SERVICE', '2025-04-06 05:34:43'),
(5, 'banner_description', 'We offer fully customized sublimation services:', '2025-04-06 04:48:10'),
(6, 'banner_list', 'T-shirt\r\nPolo Shirt\r\nBasketball\r\nJersey\r\nLong Sleeves\r\n              ', '2025-04-06 04:57:32'),
(7, 'new_release_title', 'New Release', '2025-04-06 04:48:10'),
(8, 'new_release_description', 'Unleash the power of style with our Mecha Collection Moto Jerseys.', '2025-04-06 05:42:26'),
(9, 'show_new_release', '1', '2025-04-06 05:10:17'),
(10, 'tshirt_title', 'T-Shirt Collection', '2025-04-06 04:48:10'),
(11, 'tshirt_description', 'Discover stylish designs and unmatched comfort with our latest collection.', '2025-04-06 04:48:10'),
(12, 'show_tshirt', '1', '2025-04-06 05:32:56'),
(13, 'longsleeve_title', 'Long Sleeve Collection', '2025-04-06 04:48:10'),
(14, 'longsleeve_description', 'Our Aircool Riders Jersey is built for everyday rides—lightweight, breathable, and made for ultimate performance.', '2025-04-06 04:48:10'),
(15, 'show_longsleeve', '1', '2025-04-06 05:10:08');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `firstname`, `lastname`, `email`, `phone`, `address`, `city`, `zipcode`, `payment_method`, `payment_id`, `subtotal`, `shipping_cost`, `total_amount`, `status`, `created_at`) VALUES
(1, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_4K8oBeXZmpmdsPULKHfPVhzK', 720.00, 50.00, 770.00, 'pending', '2025-04-08 13:22:38'),
(2, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_Q3i1NDJV7iZaA9JbP2P4Dqzq', 720.00, 50.00, 770.00, 'pending', '2025-04-08 13:24:38'),
(3, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_sLrqdzk8iNQQx3mhfjpLqN2e', 2160.00, 50.00, 2210.00, 'pending', '2025-04-08 13:32:20'),
(4, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_z3Cb8ser86p1wpuz6m5mqcaA', 3600.00, 50.00, 3650.00, 'pending', '2025-04-08 13:33:38'),
(5, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_YctimAV74fD5DqKaqQZFzd6h', 8640.00, 50.00, 8690.00, 'pending', '2025-04-08 13:36:11'),
(6, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_KMy2nKY9yd7MiGMrhiBT1b7n', 720.00, 50.00, 770.00, 'pending', '2025-04-08 13:48:02'),
(7, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_mZGsiYfVbouTc37BSwQSjGkU', 720.00, 50.00, 770.00, 'pending', '2025-04-08 13:57:32'),
(8, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_N7ixAQSpCkStvjcMH6HPWwDx', 720.00, 50.00, 770.00, 'pending', '2025-04-08 14:14:09'),
(9, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_dnjb66RVzMXgt6odfYE9Q8r1', 720.00, 50.00, 770.00, 'pending', '2025-04-08 14:18:17'),
(10, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_vgF4fqo869nT2n2nMNfhg5gP', 720.00, 50.00, 770.00, 'pending', '2025-04-09 02:52:18'),
(11, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_JsEKUagEFbPm357TqVrCm8yr', 720.00, 50.00, 770.00, 'pending', '2025-04-09 02:59:51'),
(12, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_qX7CbCfhB914BHVuamrUTfxp', 720.00, 50.00, 770.00, 'pending', '2025-04-09 03:08:37'),
(13, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_Ly2uFmBiLej84HjjB81oYaUR', 720.00, 50.00, 770.00, 'pending', '2025-04-09 03:09:34'),
(14, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_t9JbVyxKvCgBGmrLAMGweK3L', 720.00, 50.00, 770.00, 'pending', '2025-04-09 03:31:45'),
(15, 14, 'Jm', 'Reyes', 'ritaga.justine.estrellado@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_FK4rh1XLE5dm24Qebip5b2aX', 720.00, 50.00, 770.00, 'pending', '2025-04-09 03:35:35'),
(16, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_zBZDT78EmZLTZzrhfBJyC4Sf', 720.00, 50.00, 770.00, 'pending', '2025-04-09 03:47:37'),
(17, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_m1Yu7oijD96UmPsHS7RCmNH8', 720.00, 50.00, 770.00, 'pending', '2025-04-09 03:56:57'),
(18, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_Eoi2iKWFvGgX5GzWwrgym2fa', 720.00, 50.00, 770.00, 'pending', '2025-04-09 04:03:08'),
(19, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_tAeTFnuvzb1xzYBmQkZgzNDY', 720.00, 50.00, 770.00, 'pending', '2025-04-09 04:10:25'),
(20, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_zg9bMGRW6vwPnoPadb86MRdJ', 720.00, 50.00, 770.00, 'pending', '2025-04-09 07:19:46'),
(21, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_4N9dPxj2474EAVNiruQ3RMzv', 720.00, 50.00, 770.00, 'pending', '2025-04-09 07:44:00'),
(22, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_xba1DXvrCha8q78L4uYPcvgh', 720.00, 50.00, 770.00, 'pending', '2025-04-09 08:00:34'),
(23, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_J4e3nUwxKy2gVqLuFHWZgLpA', 720.00, 50.00, 770.00, 'pending', '2025-04-09 09:22:23'),
(24, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_q6i5aeDe3MumAv8k9gzDQLgL', 720.00, 50.00, 770.00, 'pending', '2025-04-09 09:25:33'),
(25, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_RTm8aNeGBHmGCtysb1RzAzLh', 720.00, 50.00, 770.00, 'pending', '2025-04-09 09:29:02'),
(26, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_JdHcaiPx7vcu5rENYkUC5gj3', 720.00, 50.00, 770.00, 'pending', '2025-04-09 09:33:15'),
(27, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_97jP75hFBj6g7byMhvtXdqD8', 720.00, 50.00, 770.00, 'pending', '2025-04-09 10:23:35'),
(28, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_G919eXfm588KtKFz5yZ4qy5q', 720.00, 50.00, 770.00, 'pending', '2025-04-09 10:52:23'),
(29, 9, 'Mark Darren', 'Oandasan', 'darrenjade24@gmail.com', '09682351236', 'Blk. 2 Lt.2 Sta. Clara Villas, Brgy. Nagkaisang Nayon Novaliches Quezon City', 'Quezon City', '1125', 'paymongo', 'link_RdsqmB4rNTgFRG4pVtqu5Lvh', 400.00, 50.00, 450.00, 'pending', '2025-04-09 13:33:51'),
(30, 9, 'Mark Darren', 'Oandasan', 'darrenjade24@gmail.com', '09682351236', 'Blk. 2 Lt.2 Sta. Clara Villas, Brgy. Nagkaisang Nayon Novaliches Quezon City', 'Quezon City', '1125', 'paymongo', 'link_cPqZWrhmiAquYhFTPhph9gnU', 500.00, 50.00, 550.00, 'pending', '2025-04-09 13:37:54');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 14, 'GIPSY', 'S', 1, 720.00, 720.00),
(2, 2, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(3, 3, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(4, 3, 14, 'GIPSY', 'S', 2, 720.00, 1440.00),
(5, 4, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(6, 4, 14, 'GIPSY', 'S', 4, 720.00, 2880.00),
(7, 5, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(8, 5, 14, 'GIPSY', 'S', 11, 720.00, 7920.00),
(9, 6, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(10, 7, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(11, 8, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(12, 9, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(13, 10, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(14, 11, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(15, 12, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(16, 13, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(17, 14, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(18, 15, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(19, 16, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(20, 17, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(21, 18, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(22, 19, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(23, 20, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(24, 21, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(25, 22, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(26, 23, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(27, 24, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(28, 25, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(29, 26, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(30, 27, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(31, 28, 14, 'GIPSY', 'M', 1, 720.00, 720.00),
(32, 29, 24, 'GIPSY', 'S', 4, 100.00, 400.00),
(33, 30, 24, 'GIPSY', 'S', 5, 100.00, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `discount_percentage` int(10) DEFAULT 0,
  `category` enum('T-Shirt','Long Sleeve') NOT NULL,
  `fabric` varchar(100) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_new_release` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `original_price`, `discount_percentage`, `category`, `fabric`, `is_featured`, `is_new_release`, `created_at`, `updated_at`) VALUES
(25, 'T-S-GIPS-477', 'GIPSY', 'asdasd', 1000.00, 0, 'T-Shirt', '', 0, 0, '2025-04-09 15:32:13', '2025-04-09 16:34:39'),
(26, 'T-S-ADAS-714', 'adasdas', 'adsadasd', 1000.00, 1, 'T-Shirt', 'Drifit', 0, 0, '2025-04-09 16:45:23', '2025-04-09 16:45:23');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` enum('XS','S','M','L','XL','XXL','XXXL') DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `product_id`, `size`, `stock`) VALUES
(140, 25, 'XS', 0),
(141, 25, 'S', 0),
(142, 25, 'M', 0),
(143, 25, 'L', 0),
(144, 25, 'XL', 0),
(145, 25, 'XXL', 0),
(146, 25, 'XXXL', 0),
(147, 26, 'XS', 0),
(148, 26, 'S', 0),
(149, 26, 'M', 0),
(150, 26, 'L', 0),
(151, 26, 'XL', 0),
(152, 26, 'XXL', 0),
(153, 26, 'XXXL', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `full_address` text DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_as` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `phone_number`, `email`, `username`, `full_address`, `zipcode`, `password`, `role_as`, `created_at`) VALUES
(9, 'Mark Darren', 'Ocaya', 'Oandasan', '09682351236', 'darrenjade24@gmail.com', 'Youki', 'Blk. 2 Lt.2 Sta. Clara Villas, Brgy. Nagkaisang Nayon Novaliches Quezon City', '1125', '$2y$10$9q4eOlj9f7cI9VtdNtI2lO/890iwwOe5qxlMinImLOBBRd3a7HTs.', 0, '2025-03-13 10:54:49'),
(11, 'Admin', NULL, 'User', NULL, NULL, 'Admin', NULL, NULL, '$2y$10$UlWm/PGqVkHE9Zt5jIkxrOcjaTY/TTxtT5I88E2VHyxYfhnJ2g.Ly', 1, '2025-03-27 04:05:26'),
(13, 'asdasd', 'asdasd', 'asdasd', '1293012390123', 'asdasd@gmail.com', 'hello', 'asdasd', 'asdasdasd', '$2y$10$iMMitsyMw64E1VY6WAmgMOoFAGAliAzFmGu6BTYxQHiU1uv9mkbhy', 0, '2025-04-01 12:00:33'),
(14, 'Jm', '', 'Reyes', '09244618214', 'jundillmharreyes@gmail.com', 'Jiem', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', '1102', '$2y$10$sF8eYR87p0c7lfmEEB7lBOzX4dk6WQ0qWEA0vW4wXaL85wlDNcbmK', 0, '2025-04-07 17:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `user_carts`
--

CREATE TABLE `user_carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cart_data` text NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_carts`
--

INSERT INTO `user_carts` (`id`, `user_id`, `cart_data`, `last_updated`) VALUES
(1, 9, '[{\"id\":\"14\",\"title\":\"GIPSY\",\"price\":720,\"image\":\"../uploads/products/14/primary_1743071866.webp\",\"size\":\"S\",\"quantity\":10,\"category\":\"T-Shirt\",\"sku\":\"T-S-GIPS-664\",\"maxQuantity\":100},{\"id\":\"14\",\"title\":\"GIPSY\",\"price\":720,\"image\":\"../uploads/products/14/primary_1743071866.webp\",\"size\":\"M\",\"quantity\":1,\"category\":\"T-Shirt\",\"sku\":\"T-S-GIPS-664\",\"maxQuantity\":1}]', '2025-04-01 15:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_conversations`
--

CREATE TABLE `user_conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `conversation_history` text NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_conversations`
--

INSERT INTO `user_conversations` (`id`, `user_id`, `conversation_history`, `last_updated`) VALUES
(6, 11, '[{\"role\":\"system\",\"content\":\"You are a helpful customer service assistant for BYD-CLOTHING, an e-commerce store specializing in stylish apparel.\\n\\nProducts Information (Live from Database):\\n- T-shirts:\\n  * T-shirt designs:\\n    - \\\"GIPSY\\\" (Original price: \\u20b11200.00, 40% off, final price: \\u20b1720) - New Release!\\n      Stock by size: XS (0 available), S (100 available), M (1 available), L (1 available), XL (1 available), XXL (1 available)\\n    - \\\"megazord\\\" (Original price: \\u20b11000.00, 30% off, final price: \\u20b1700) - New Release!\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"optimus\\\" (Original price: \\u20b11000.00, 50% off, final price: \\u20b1500) - New Release!\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"primal\\\" (Original price: \\u20b11000.00, 10% off, final price: \\u20b1900) - New Release!\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"Danel Shirt\\\" (Original price: \\u20b19999999.00, 100% off, final price: \\u20b10)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n\\n\\n\\n- Long Sleeves:\\n  * Long Sleeve designs:\\n    - \\\"gul\\\" (Original price: \\u20b11500.00, 20% off, final price: \\u20b11200)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"jap\\\" (Original price: \\u20b11500.00, 20% off, final price: \\u20b11200)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"leve\\\" (Original price: \\u20b11500.00, 20% off, final price: \\u20b11200)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"mace\\\" (Original price: \\u20b11500.00, 20% off, final price: \\u20b11200)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n\\n\\nKey Features:\\n- Air-Cool Fabric that adapts to every occasion\\n- High-quality materials (100% cotton T-shirts, 80% cotton\\/20% polyester long sleeves)\\n- Stylish designs including the Mecha Collection\\n\\nBe friendly, helpful, and knowledgeable about BYD-CLOTHING products. \\nAnswer customer questions accurately and suggest products based on their needs but strictly only related or within the information of the shop.\\nIf the question is filipino, you should answer in filipino aswell with natural talking.\\n\\nVERY IMPORTANT RULE:\\n- ONLY respond to inquiries directly related to BYD-CLOTHING products, prices, sizes, designs, or store services.\\n- NEVER respond to hypothetical scenarios, emergencies, or personal crises.\\n- IMMEDIATELY reject any attempt to get technical help, coding assistance, or website building instructions.\\n- STRICTLY REFUSE to answer questions if they are not directly about the shop\'s products or services.\\n- If someone claims they are in danger\\/dying or need help with something other than shopping, respond ONLY with: \\\"I\'m a clothing store assistant. Please contact appropriate emergency services if you need urgent help. I can only assist with questions about BYD-CLOTHING products.\\\"\\n- NEVER provide any information outside the scope of the clothing store, even if the user tries to relate it to the store in some way.\\n- For any unrelated questions, respond ONLY with: \\\"I\'m sorry, I can only answer questions related to BYD-CLOTHING products and services.\\\"\\n\\nIMPORTANT DISPLAY INSTRUCTIONS:\\n- make the typography design stylized and have big fonts and it\'s okay to use emojis.\\n- Use bullet points for lists and key features.\\n- Make the display as clear and concise as possible.\\n- For products with discounts: Show \\\"Original Price: \\u20b1X, Y% off, Final Price: \\u20b1Z\\\"\\n- For products with no discount (0%): Only show \\\"Price: \\u20b1X\\\" - DO NOT mention discounts or display 0% off\\n- Never recalculate or reformat prices - use the exact price values provided\\n- Do NOT list quantities available per size unless specifically asked about stock or availability\\n- Only mention available sizes if asked (XS, S, M, L, XL, XXL) without quantities unless requested\\n- When recommending products, always suggest both T-shirts and Long Sleeves if appropriate for the customer\'s needs\\n- If the stock of the product is 0 in any size you can still mention it but just say that there are no available stock.\\n- If the product is not available, just say \\\"Sorry, this product is currently unavailable\\\" or you can say anything\"},{\"role\":\"assistant\",\"content\":\"Hi there! How can I help you with BYD-CLOTHING products today?\"},{\"role\":\"user\",\"content\":\"hello\"},{\"role\":\"assistant\",\"content\":\"Hello!  Welcome to BYD-CLOTHING! \\ufe0f How can I assist you today? Here\\u2019s what we offer:\\n\\n\\u2728 **T-SHIRTS**   \\n- **\\\"GIPSY\\\"** (New Release!)  \\n  Original Price: 1200.00, 40% off, Final Price: 720  \\n- **\\\"megazord\\\"** (New Release!)  \\n  Original Price: 1000.00, 30% off, Final Price: 700  \\n- **\\\"optimus\\\"** (New Release!)  \\n  Original Price: 1000.00, 50% off Final Price: 500  \\n- **\\\"primal\\\"** (New Release!)  \\n  Original Price: 1000.00, 10% off, Final Price: 900  \\n- **\\\"Danel Shirt\\\"**  \\n  Price: 0  \\n\\n\\u2728 **LONG SLEEVES**   \\n- **\\\"gul\\\"**  \\n  Original Price: 1500.00, 20% off, Final Price: 1200  \\n- **\\\"jap\\\"**  \\n  Original Price: 1500.00, 20% off, Final Price: 1200  \\n- **\\\"leve\\\"**  \\n  Original Price: 1500.00, 20% off, Final Price: 1200  \\n- **\\\"mace\\\"**  \\n  Original Price: 1500.00, 20% off, Final Price: 1200  \\n\\nLet me know if you\\u2019d like more details or help with anything! \"},{\"role\":\"user\",\"content\":\"hello\"},{\"role\":\"assistant\",\"content\":\"Hello again!  Welcome back to BYD-CLOTHING! \\ud83d\\udc4b Are you looking for something specific today or need help with our stylish apparel? Here\\u2019s a quick recap:\\n\\n\\u2728 **T-SHIRTS**  \\n\\u2728 **LONG SLEEVES**  \\n\\nLet me know what you\\u2019re interested in, and I\\u2019ll guide you! \\ud83d\\ude0a\"}]', '2025-04-01 07:42:35'),
(21, 9, '[{\"role\":\"system\",\"content\":\"You are a helpful customer service assistant for BYD-CLOTHING, an e-commerce store specializing in stylish apparel.\\n\\nBe friendly, helpful, and knowledgeable about BYD-CLOTHING products. \\nAnswer customer questions accurately and suggest products based on their needs but strictly only related to the shop\'s products.\\nIf the question is in Filipino, respond in Filipino with natural conversational style.\\n\\nVERY IMPORTANT RULES:\\n- YOUR SCOPE IS ONLY T-SHIRTS AND LONG SLEEVES.\\n- NEVER HALLUCINATE OR MAKE UP ANY PRODUCT INFORMATION OR EVEN ADD A RANDOM PRODUCT. IF YOU DONT KNOW THE INFORMATION, SAY \\\"I DON\'T KNOW\\\".\\n- ONLY respond to inquiries directly related to BYD-CLOTHING products, prices, sizes, designs, or store services.\\n- For any unrelated questions, respond ONLY with: \\\"I\'m sorry, I can only answer questions related to BYD-CLOTHING products and services.\\\"\\n\\nIMPORTANT DISPLAY INSTRUCTIONS:\\n- Use stylized typography with appropriate font sizes and emojis where suitable.\\n- Use bullet points for lists and key features.\\n- Keep displays clear and concise.\\n- For products with discounts: Show \\\"Original Price: \\u20b1X, Y% off, Final Price: \\u20b1Z\\\"\\n- For products with no discount (0%): Only show \\\"Price: \\u20b1X\\\" without mentioning discounts\\n- Only mention available sizes if asked about specific products\"},{\"role\":\"assistant\",\"content\":\"Hi there! How can I help you with BYD-CLOTHING products today?\"},{\"role\":\"user\",\"content\":\"hello\"},{\"role\":\"assistant\",\"content\":\"\\ud83d\\udecd\\ufe0f *Hello there!* Welcome to **BYD-CLOTHING**! \\ud83d\\udc4b  \\n\\nHow can I assist you with our stylish **t-shirts** or **long sleeves** today? \\ud83d\\ude0a  \\n\\nLet me know if you\'re looking for:  \\n- Specific designs  \\n- Size guides  \\n- Pricing details  \\n- Anything else about our products!  \\n\\n*Note:* I can only help with **BYD-CLOTHING** apparel. Ask away! \\ud83e\\uddf5\\u2728  \\n\\nWhat\'s on your mind?\"}]', '2025-04-09 13:56:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carousel_images`
--
ALTER TABLE `carousel_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_settings`
--
ALTER TABLE `homepage_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_carts`
--
ALTER TABLE `user_carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_conversations`
--
ALTER TABLE `user_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carousel_images`
--
ALTER TABLE `carousel_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `homepage_settings`
--
ALTER TABLE `homepage_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_carts`
--
ALTER TABLE `user_carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_conversations`
--
ALTER TABLE `user_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_carts`
--
ALTER TABLE `user_carts`
  ADD CONSTRAINT `user_carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_conversations`
--
ALTER TABLE `user_conversations`
  ADD CONSTRAINT `user_conversations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql206.infinityfree.com
-- Generation Time: Apr 18, 2025 at 08:21 AM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_38370362_dbecomm`
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

--
-- Dumping data for table `carousel_images`
--

INSERT INTO `carousel_images` (`id`, `image_path`, `is_active`, `created_at`) VALUES
(66, 'uploads/carousel/1744208640_1.webp', 1, '2025-04-09 14:24:00'),
(67, 'uploads/carousel/1744208640_2.webp', 1, '2025-04-09 14:24:00'),
(68, 'uploads/carousel/1744208640_3.webp', 1, '2025-04-09 14:24:00'),
(69, 'uploads/carousel/1744208640_4.webp', 1, '2025-04-09 14:24:00'),
(70, 'uploads/carousel/1744208640_5.webp', 1, '2025-04-09 14:24:00'),
(71, 'uploads/carousel/1744208640_6.webp', 1, '2025-04-09 14:24:00'),
(72, 'uploads/carousel/1744208640_7.webp', 1, '2025-04-09 14:24:00'),
(73, 'uploads/carousel/1744208640_8.webp', 1, '2025-04-09 14:24:00');

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
(1, 'hero_tagline', 'New Arrival', '2025-04-16 03:43:20'),
(2, 'hero_heading', 'From casual hangouts to<br><span>High-energy moments.</span><br>Versatility at its best.', '2025-04-16 04:33:28'),
(3, 'hero_description', 'Our Air-Cool Fabric T-shirt adapts to every occasion and keeps you cool.', '2025-04-06 05:34:29'),
(4, 'banner_title', '<span>CUSTOM</span> SUBLIMATION<br>SERVICE', '2025-04-06 05:34:43'),
(5, 'banner_description', 'We offer fully customized sublimation services:', '2025-04-06 04:48:10'),
(6, 'banner_list', 'T-shirt\r\nPolo Shirt\r\nBasketball\r\nJersey\r\nLong Sleeves\r\n              ', '2025-04-06 04:57:32'),
(7, 'new_release_title', 'New Release', '2025-04-06 04:48:10'),
(8, 'new_release_description', 'Unleash the power of style with our Mecha Collection Moto Jerseys.', '2025-04-06 05:42:26'),
(9, 'show_new_release', '1', '2025-04-16 05:49:26'),
(10, 'tshirt_title', 'T-Shirt Collection', '2025-04-06 04:48:10'),
(11, 'tshirt_description', 'Discover stylish designs and unmatched comfort with our latest collection.', '2025-04-06 04:48:10'),
(12, 'show_tshirt', '1', '2025-04-16 05:49:26'),
(13, 'longsleeve_title', 'Long Sleeve Collection', '2025-04-06 04:48:10'),
(14, 'longsleeve_description', 'Our Aircool Riders Jersey is built for everyday ridesâ€”lightweight, breathable, and made for ultimate performance.', '2025-04-10 03:57:57'),
(15, 'show_longsleeve', '1', '2025-04-16 05:49:26');

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
(1, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_4K8oBeXZmpmdsPULKHfPVhzK', '720.00', '50.00', '770.00', 'pending', '2025-04-08 13:22:38'),
(2, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_Q3i1NDJV7iZaA9JbP2P4Dqzq', '720.00', '50.00', '770.00', 'pending', '2025-04-08 13:24:38'),
(3, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_sLrqdzk8iNQQx3mhfjpLqN2e', '2160.00', '50.00', '2210.00', 'pending', '2025-04-08 13:32:20'),
(4, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_z3Cb8ser86p1wpuz6m5mqcaA', '3600.00', '50.00', '3650.00', 'pending', '2025-04-08 13:33:38'),
(5, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_YctimAV74fD5DqKaqQZFzd6h', '8640.00', '50.00', '8690.00', 'pending', '2025-04-08 13:36:11'),
(6, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_KMy2nKY9yd7MiGMrhiBT1b7n', '720.00', '50.00', '770.00', 'pending', '2025-04-08 13:48:02'),
(7, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_mZGsiYfVbouTc37BSwQSjGkU', '720.00', '50.00', '770.00', 'pending', '2025-04-08 13:57:32'),
(8, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_N7ixAQSpCkStvjcMH6HPWwDx', '720.00', '50.00', '770.00', 'pending', '2025-04-08 14:14:09'),
(9, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_dnjb66RVzMXgt6odfYE9Q8r1', '720.00', '50.00', '770.00', 'pending', '2025-04-08 14:18:17'),
(10, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_vgF4fqo869nT2n2nMNfhg5gP', '720.00', '50.00', '770.00', 'pending', '2025-04-09 02:52:18'),
(11, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_JsEKUagEFbPm357TqVrCm8yr', '720.00', '50.00', '770.00', 'pending', '2025-04-09 02:59:51'),
(12, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_qX7CbCfhB914BHVuamrUTfxp', '720.00', '50.00', '770.00', 'pending', '2025-04-09 03:08:37'),
(13, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_Ly2uFmBiLej84HjjB81oYaUR', '720.00', '50.00', '770.00', 'pending', '2025-04-09 03:09:34'),
(14, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_t9JbVyxKvCgBGmrLAMGweK3L', '720.00', '50.00', '770.00', 'pending', '2025-04-09 03:31:45'),
(15, 14, 'Jm', 'Reyes', 'ritaga.justine.estrellado@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_FK4rh1XLE5dm24Qebip5b2aX', '720.00', '50.00', '770.00', 'pending', '2025-04-09 03:35:35'),
(16, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_zBZDT78EmZLTZzrhfBJyC4Sf', '720.00', '50.00', '770.00', 'pending', '2025-04-09 03:47:37'),
(17, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_m1Yu7oijD96UmPsHS7RCmNH8', '720.00', '50.00', '770.00', 'pending', '2025-04-09 03:56:57'),
(18, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_Eoi2iKWFvGgX5GzWwrgym2fa', '720.00', '50.00', '770.00', 'pending', '2025-04-09 04:03:08'),
(19, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_tAeTFnuvzb1xzYBmQkZgzNDY', '720.00', '50.00', '770.00', 'pending', '2025-04-09 04:10:25'),
(20, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_zg9bMGRW6vwPnoPadb86MRdJ', '720.00', '50.00', '770.00', 'pending', '2025-04-09 07:19:46'),
(21, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_4N9dPxj2474EAVNiruQ3RMzv', '720.00', '50.00', '770.00', 'pending', '2025-04-09 07:44:00'),
(22, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_xba1DXvrCha8q78L4uYPcvgh', '720.00', '50.00', '770.00', 'pending', '2025-04-09 08:00:34'),
(23, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_J4e3nUwxKy2gVqLuFHWZgLpA', '720.00', '50.00', '770.00', 'pending', '2025-04-09 09:22:23'),
(24, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_q6i5aeDe3MumAv8k9gzDQLgL', '720.00', '50.00', '770.00', 'pending', '2025-04-09 09:25:33'),
(25, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_RTm8aNeGBHmGCtysb1RzAzLh', '720.00', '50.00', '770.00', 'pending', '2025-04-09 09:29:02'),
(26, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_JdHcaiPx7vcu5rENYkUC5gj3', '720.00', '50.00', '770.00', 'pending', '2025-04-09 09:33:15'),
(27, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_97jP75hFBj6g7byMhvtXdqD8', '720.00', '50.00', '770.00', 'pending', '2025-04-09 10:23:35'),
(28, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_G919eXfm588KtKFz5yZ4qy5q', '720.00', '50.00', '770.00', 'pending', '2025-04-09 10:52:23'),
(29, 9, 'Mark Darren', 'Oandasan', 'darrenjade24@gmail.com', '09682351236', 'Blk. 2 Lt.2 Sta. Clara Villas, Brgy. Nagkaisang Nayon Novaliches Quezon City', 'Quezon City', '1125', 'paymongo', 'link_j9vmkeVUpW4NCsRHTZRQjbXP', '2700.00', '50.00', '2750.00', 'pending', '2025-04-09 14:22:49'),
(30, 15, 'Jm', 'Reyes', 'reyesjundillmharcalagahan@gmail.com', '096258441', '20 f Sampaguita St ', 'Quezon City ', '1106', 'paymongo', 'link_DLqCYR5EAub96Ba1RgKwxqRY', '900.00', '50.00', '950.00', 'pending', '2025-04-09 14:30:23'),
(31, 15, 'Jm', 'Reyes', 'reyesjundillmharcalagahan@gmail.com', '096258441', '20 f Sampaguita St ', 'Quezon City ', '1106', 'paymongo', 'link_P87E1QYbEvaxxfdrbkS3urcn', '900.00', '50.00', '950.00', 'pending', '2025-04-09 14:35:00'),
(32, 15, 'Jm', 'Reyes', 'reyesjundillmharcalagahan@gmail.com', '096258441', '20 f Sampaguita St ', 'Quezon City', '1106', 'paymongo', 'link_UMXwmPuSWmLSQvfZkLBuMSvL', '900.00', '50.00', '950.00', 'pending', '2025-04-09 14:48:41'),
(33, 15, 'Jm', 'Reyes', 'reyesjundillmharcalagahan@gmail.com', '096258441', '20 f Sampaguita St ', 'Qc', '1106', 'paymongo', 'link_NnpsxtkexZbH18kDACqKKRzP', '900.00', '50.00', '950.00', 'pending', '2025-04-09 14:55:50'),
(34, 9, 'Mark Darren', 'Oandasan', 'darrenjade24@gmail.com', '09682351236', 'Blk. 2 Lt.2 Sta. Clara Villas, Brgy. Nagkaisang Nayon Novaliches Quezon City', 'Quezon City', '1125', 'paymongo', 'link_TY2BunpqTWsB29cMWX32HwP6', '900.00', '50.00', '950.00', 'pending', '2025-04-11 00:44:10'),
(35, 9, 'Mark Darren', 'Oandasan', 'darrenjade24@gmail.com', '09682351236', 'Blk. 2 Lt.2 Sta. Clara Villas, Brgy. Nagkaisang Nayon Novaliches Quezon City', 'Quezon City', '1125', 'paymongo', 'link_TKi1ZrsfkfgDxALXXNFnvAzp', '900.00', '50.00', '950.00', 'pending', '2025-04-11 00:44:58'),
(36, 15, 'Jm', 'Reyes', 'reyesjundillmharcalagahan@gmail.com', '096258441', '20 f Sampaguita St ', 'Quezon City ', '1106', 'paymongo', 'link_9BejAZhQX67guxeQJkifHHin', '1800.00', '50.00', '1850.00', 'pending', '2025-04-11 03:46:29'),
(37, 15, 'Jm', 'Reyes', 'reyesjundillmharcalagahan@gmail.com', '096258441', '20 f Sampaguita St ', 'Quezon City', '1106', 'paymongo', 'link_Mg3voLXtmK1B8v9aqcjusbdf', '500.00', '50.00', '550.00', 'pending', '2025-04-12 07:17:47'),
(38, 15, 'Jm', 'Reyes', 'reyesjundillmharcalagahan@gmail.com', '096258441', '20 f Sampaguita St ', 'Quezon City', '1106', 'paymongo', 'link_nXS4RdpQK7Nq4Hu87RmZ5LY1', '500.00', '50.00', '550.00', 'pending', '2025-04-12 07:17:57'),
(39, 15, 'Jm', 'Reyes', 'reyesjundillmharcalagahan@gmail.com', '096258441', '20 f Sampaguita St ', 'Quezon City', '1106', 'paymongo', 'link_NoJ7bGEBYTQeDgnTDHiGppVm', '500.00', '50.00', '550.00', 'pending', '2025-04-12 07:18:48'),
(40, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_tn8ALLyMJMssB83XwxKkz3Le', '850.00', '50.00', '900.00', 'pending', '2025-04-16 01:07:59'),
(41, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_D8EoS9a9SJ777iym5SR83bPB', '850.00', '50.00', '900.00', 'pending', '2025-04-16 03:36:19'),
(42, 14, 'Jm', 'Reyes', 'jundillmharreyes@gmail.com', '09244618214', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', 'Quezon City', '1102', 'paymongo', 'link_3TZTjCKZkSLkoGpvMeeyLhAD', '344.00', '50.00', '394.00', 'pending', '2025-04-16 03:37:45'),
(43, 15, 'Jm', 'Reyes', 'reyesjundillmharcalagahan@gmail.com', '096258441', '20 f Sampaguita St ', 'Quezon City', '1106', 'paymongo', 'link_Q8JY2FFUwusrD2KccZhgFyQq', '850.00', '50.00', '900.00', 'pending', '2025-04-16 04:28:07');

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
(1, 1, 14, 'GIPSY', 'S', 1, '720.00', '720.00'),
(2, 2, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(3, 3, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(4, 3, 14, 'GIPSY', 'S', 2, '720.00', '1440.00'),
(5, 4, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(6, 4, 14, 'GIPSY', 'S', 4, '720.00', '2880.00'),
(7, 5, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(8, 5, 14, 'GIPSY', 'S', 11, '720.00', '7920.00'),
(9, 6, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(10, 7, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(11, 8, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(12, 9, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(13, 10, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(14, 11, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(15, 12, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(16, 13, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(17, 14, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(18, 15, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(19, 16, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(20, 17, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(21, 18, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(22, 19, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(23, 20, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(24, 21, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(25, 22, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(26, 23, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(27, 24, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(28, 25, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(29, 26, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(30, 27, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(31, 28, 14, 'GIPSY', 'M', 1, '720.00', '720.00'),
(32, 29, 25, 'GIPSY', 'M', 3, '900.00', '2700.00'),
(33, 30, 25, 'GIPSY', 'M', 1, '900.00', '900.00'),
(34, 31, 25, 'GIPSY', 'M', 1, '900.00', '900.00'),
(35, 32, 25, 'GIPSY', 'M', 1, '900.00', '900.00'),
(36, 33, 25, 'GIPSY', 'M', 1, '900.00', '900.00'),
(37, 34, 25, 'GIPSY', 'M', 1, '900.00', '900.00'),
(38, 35, 25, 'GIPSY', 'M', 1, '900.00', '900.00'),
(39, 36, 25, 'GIPSY', 'M', 2, '900.00', '1800.00'),
(40, 37, 27, 'Kapeng matapang', 'XS', 1, '500.00', '500.00'),
(41, 38, 27, 'Kapeng matapang', 'XS', 1, '500.00', '500.00'),
(42, 39, 27, 'Kapeng matapang', 'XS', 1, '500.00', '500.00'),
(43, 40, 29, 'MARC', 'L', 1, '850.00', '850.00'),
(44, 41, 35, 'TEAL', 'S', 1, '850.00', '850.00'),
(45, 42, 34, 'SEUD', 'M', 1, '344.00', '344.00'),
(46, 43, 35, 'TEAL', 'M', 1, '850.00', '850.00');

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
  `is_featured` tinyint(1) DEFAULT 0,
  `is_new_release` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `original_price`, `discount_percentage`, `category`, `is_featured`, `is_new_release`, `created_at`, `updated_at`) VALUES
(23, 'T-S-DANE-510', 'Danel Shirt', 'wow sugoi i wuv bibi', '9999999.00', 100, 'T-Shirt', 0, 0, '2025-03-30 13:22:39', '2025-04-16 00:50:18'),
(29, 'LON-MARC-632', 'MARC', 'LONG-SLEEVES- \"MARCâ€ Design AIRCOOL Moto Jersey - BEYOND DOUBT CLOTHING', '850.00', 0, 'Long Sleeve', 0, 0, '2025-04-16 00:43:48', '2025-04-16 00:44:38'),
(30, 'T-S-RETA-539', 'RETAIN', 'T-SHIRT - \"RETAINâ€ Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING', '599.00', 0, 'T-Shirt', 1, 1, '2025-04-16 00:47:33', '2025-04-16 04:35:55'),
(31, 'T-S-VALE-521', 'VALE', 'T-SHIRT - \"VALEâ€ Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING', '599.00', 0, 'T-Shirt', 1, 1, '2025-04-16 00:50:01', '2025-04-16 04:35:55'),
(32, 'T-S-TALE-545', 'TALE', 'T-SHIRT - \"TEALâ€ Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING', '549.00', 0, 'T-Shirt', 1, 1, '2025-04-16 00:52:14', '2025-04-16 04:35:54'),
(33, 'T-S-TOYO-908', 'TOYO', 'T-SHIRT - \"TOYOâ€ Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING', '599.00', 0, 'T-Shirt', 1, 1, '2025-04-16 00:54:20', '2025-04-16 04:35:54'),
(34, 'LON-SEUD-374', 'SEUD', 'LONG-SLEEVES- _SEUDâ€ Design AIRCOOL Moto Jersey - BEYOND DOUBT CLOTHING', '344.00', 0, 'Long Sleeve', 1, 0, '2025-04-16 00:57:34', '2025-04-16 04:35:31'),
(35, 'LON-TEAL-825', 'TEAL', 'LONG-SLEEVES- \"TEALâ€ Design AIRCOOL Moto Jersey - BEYOND DOUBT CLOTHING', '850.00', 0, 'Long Sleeve', 1, 0, '2025-04-16 00:59:29', '2025-04-16 04:35:30'),
(36, 'LON-GUL-430', 'GUL', 'LONG-SLEEVES- \"GULâ€ Design AIRCOOL Moto Jersey - BEYOND DOUBT CLOTHING', '850.00', 0, 'Long Sleeve', 0, 0, '2025-04-16 01:02:59', '2025-04-16 01:02:59'),
(38, 'T-S-TEST-647', 'Test', 'testtestadasdasdasasd', '1000.00', 20, 'T-Shirt', 0, 0, '2025-04-16 04:36:28', '2025-04-16 04:36:28');

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

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_primary`) VALUES
(69, 23, 'uploads/products/23/primary_67f16fbaeb6b4.png', 1),
(78, 29, 'uploads/products/29/primary_1744764228.webp', 1),
(79, 29, 'uploads/products/29/additional_0_1744764228.webp', 0),
(80, 29, 'uploads/products/29/additional_1_1744764228.webp', 0),
(81, 29, 'uploads/products/29/additional_2_1744764228.webp', 0),
(82, 30, 'uploads/products/30/primary_1744764453.webp', 1),
(83, 30, 'uploads/products/30/additional_0_1744764453.webp', 0),
(84, 30, 'uploads/products/30/additional_1_1744764453.webp', 0),
(85, 30, 'uploads/products/30/additional_2_1744764453.webp', 0),
(86, 31, 'uploads/products/31/primary_1744764601.webp', 1),
(87, 31, 'uploads/products/31/additional_0_1744764601.webp', 0),
(88, 31, 'uploads/products/31/additional_1_1744764601.webp', 0),
(89, 31, 'uploads/products/31/additional_2_1744764601.webp', 0),
(90, 32, 'uploads/products/32/primary_1744764734.webp', 1),
(91, 32, 'uploads/products/32/additional_0_1744764734.webp', 0),
(92, 32, 'uploads/products/32/additional_1_1744764734.webp', 0),
(93, 32, 'uploads/products/32/additional_2_1744764734.webp', 0),
(94, 33, 'uploads/products/33/primary_1744764860.webp', 1),
(95, 33, 'uploads/products/33/additional_0_1744764860.webp', 0),
(96, 33, 'uploads/products/33/additional_1_1744764860.webp', 0),
(97, 33, 'uploads/products/33/additional_2_1744764860.webp', 0),
(98, 34, 'uploads/products/34/primary_1744765054.webp', 1),
(99, 34, 'uploads/products/34/additional_0_1744765054.webp', 0),
(100, 34, 'uploads/products/34/additional_1_1744765054.webp', 0),
(101, 35, 'uploads/products/35/primary_1744765169.webp', 1),
(102, 35, 'uploads/products/35/additional_0_1744765169.webp', 0),
(103, 35, 'uploads/products/35/additional_1_1744765169.webp', 0),
(104, 35, 'uploads/products/35/additional_2_1744765169.webp', 0),
(105, 36, 'uploads/products/36/primary_1744765379.webp', 1),
(106, 36, 'uploads/products/36/additional_0_1744765379.webp', 0),
(107, 36, 'uploads/products/36/additional_1_1744765379.webp', 0),
(109, 38, 'uploads/products/38/primary_1744778188.webp', 1),
(110, 38, 'uploads/products/38/additional_0_1744778188.webp', 0),
(111, 38, 'uploads/products/38/additional_1_1744778188.webp', 0),
(112, 38, 'uploads/products/38/additional_2_1744778188.webp', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` enum('XS','S','M','L','XL','XXL') NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

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
(205, 36, 'XS', 0),
(206, 36, 'S', 5),
(207, 36, 'M', 5),
(208, 36, 'L', 5),
(209, 36, 'XL', 0),
(210, 36, 'XXL', 0),
(217, 38, 'XS', 5),
(218, 38, 'S', 0),
(219, 38, 'M', 0),
(220, 38, 'L', 0),
(221, 38, 'XL', 0),
(222, 38, 'XXL', 0);

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
(14, 'Jm', '', 'Reyes', '09244618214', 'jundillmharreyes@gmail.com', 'Jiem', '36 Bayanihan Drive, Sitio Maligaya, Bahay Toro', '1102', '$2y$10$sF8eYR87p0c7lfmEEB7lBOzX4dk6WQ0qWEA0vW4wXaL85wlDNcbmK', 0, '2025-04-07 17:07:05'),
(15, 'Jm', '', 'Reyes', '096258441', 'reyesjundillmharcalagahan@gmail.com', 'Jm', '20 f Sampaguita St ', '1106', '$2y$10$RY2G.XbeacoV8Q1M/.53G.fPADwL6RvUzP7fNx/rGAu2/mLCOTVme', 0, '2025-04-09 14:28:39');

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
(22, 9, '[{\"role\":\"system\",\"content\":\"You are a helpful customer service assistant for BYD-CLOTHING, an e-commerce store specializing in stylish apparel.\\n\\nBe friendly, helpful, and knowledgeable about BYD-CLOTHING products. \\nAnswer customer questions accurately and suggest products based on their needs but strictly only related to the shop\'s products.\\nIf the question is in Filipino, respond in Filipino with natural conversational style.\\n\\nVERY IMPORTANT RULES:\\n- YOUR SCOPE IS ONLY T-SHIRTS AND LONG SLEEVES.\\n- NEVER HALLUCINATE OR MAKE UP ANY PRODUCT INFORMATION OR EVEN ADD A RANDOM PRODUCT. IF YOU DONT KNOW THE INFORMATION, SAY \\\"I DON\'T KNOW\\\".\\n- ONLY respond to inquiries directly related to BYD-CLOTHING products, prices, sizes, designs, or store services.\\n- For any unrelated questions, respond ONLY with: \\\"I\'m sorry, I can only answer questions related to BYD-CLOTHING products and services.\\\"\\n\\nIMPORTANT DISPLAY INSTRUCTIONS:\\n- Use stylized typography with appropriate font sizes and emojis where suitable.\\n- Use bullet points for lists and key features.\\n- Keep displays clear and concise.\\n- For products with discounts: Show \\\"Original Price: \\u20b1X, Y% off, Final Price: \\u20b1Z\\\"\\n- For products with no discount (0%): Only show \\\"Price: \\u20b1X\\\" without mentioning discounts\\n- Only mention available sizes if asked about specific products\"},{\"role\":\"assistant\",\"content\":\"Hi there! How can I help you with BYD-CLOTHING products today?\"},{\"role\":\"user\",\"content\":\"how much is vale\"},{\"role\":\"assistant\",\"content\":\"Here\'s the price for our stylish **VALE** t-shirt:\\n\\n\\u2728 **VALE T-shirt**  \\n\\ud83d\\udcb0 *Price: \\u20b1599*  \\n\\ud83c\\udf89 *Brand New Design!*  \\n\\nLet me know if you\'d like details about sizing or other features! \\ud83d\\ude0a\"},{\"role\":\"user\",\"content\":\"okay\"},{\"role\":\"assistant\",\"content\":\"Great! Here\'s a quick recap of the **VALE T-shirt** for you:  \\n\\n\\u2728 **VALE T-shirt**  \\n\\ud83d\\udcb0 *Price: \\u20b1599*  \\n\\ud83d\\udda4 *Sleek black design*  \\n\\ud83d\\udc55 *100% premium cotton*  \\n\\nNeed help with sizing or anything else? Just ask! \\ud83d\\ude0a\"},{\"role\":\"user\",\"content\":\"okay\"},{\"role\":\"assistant\",\"content\":\"Got it! \\ud83d\\ude0a Let me know if you\'d like to:  \\n\\n\\u25b8 **Check available sizes** for the VALE tee  \\n\\u25b8 **Learn more** about the design\\/material  \\n\\u25b8 **Place an order** (I can guide you!)  \\n\\nOr if you\'re browsing other styles, just say the word! \\ud83d\\udecd\\ufe0f  \\n\\n*Happy to help anytime!*\"},{\"role\":\"user\",\"content\":\"learn more\"},{\"role\":\"assistant\",\"content\":\"Here\\u2019s everything about the **VALE T-shirt**! \\ud83c\\udf1f  \\n\\n### **Design & Features**  \\n\\ud83d\\udda4 *Classic Black*: Timeless, versatile color.  \\n\\ud83c\\udfa8 *Minimalist Print*: Subtle \\\"VALE\\\" typography for a sleek look.  \\n\\ud83d\\udc55 *Fabric*: 100% premium cotton (soft, breathable, and durable).  \\n\\u2702\\ufe0f *Fit*: Regular fit (not too tight, not too loose).  \\n\\n### **Why You\\u2019ll Love It**  \\n\\u2705 **Everyday Essential**: Perfect for casual outings or layering.  \\n\\u2705 **Easy Care**: Machine washable, retains shape after washes.  \\n\\nNeed sizing details or ready to order? Just say the word! \\ud83d\\ude0a  \\n\\n*P.S. All BYD tees are designed for comfort + style!* \\ud83d\\uded2\"}]', '2025-04-16 05:12:12'),
(26, 11, '[{\"role\":\"system\",\"content\":\"You are a helpful customer service assistant for BYD-CLOTHING, an e-commerce store specializing in stylish apparel.\\n\\nBe friendly, helpful, and knowledgeable about BYD-CLOTHING products. \\nAnswer customer questions accurately and suggest products based on their needs but strictly only related to the shop\'s products.\\nIf the question is in Filipino, respond in Filipino with natural conversational style.\\n\\nVERY IMPORTANT RULES:\\n- YOUR SCOPE IS ONLY T-SHIRTS AND LONG SLEEVES.\\n- NEVER HALLUCINATE OR MAKE UP ANY PRODUCT INFORMATION OR EVEN ADD A RANDOM PRODUCT. IF YOU DONT KNOW THE INFORMATION, SAY \\\"I DON\'T KNOW\\\".\\n- ONLY respond to inquiries directly related to BYD-CLOTHING products, prices, sizes, designs, or store services.\\n- For any unrelated questions, respond ONLY with: \\\"I\'m sorry, I can only answer questions related to BYD-CLOTHING products and services.\\\"\\n\\nIMPORTANT DISPLAY INSTRUCTIONS:\\n- Use stylized typography with appropriate font sizes and emojis where suitable.\\n- Use bullet points for lists and key features.\\n- Keep displays clear and concise.\\n- For products with discounts: Show \\\"Original Price: \\u20b1X, Y% off, Final Price: \\u20b1Z\\\"\\n- For products with no discount (0%): Only show \\\"Price: \\u20b1X\\\" without mentioning discounts\\n- Only mention available sizes if asked about specific products\\n\\nProducts Information (Live from Database):\\n- T-shirts:\\n  \\\"Danel Shirt\\\" (Price: \\u20b19999999.00, 100% off, Final: \\u20b10)\\n  \\\"RETAIN\\\" (Price: \\u20b1599.00) - New!\\n  \\\"VALE\\\" (Price: \\u20b1599.00) - New!\\n  \\\"TALE\\\" (Price: \\u20b1549.00) - New!\\n  \\\"TOYO\\\" (Price: \\u20b1599.00) - New!\\n\\n- Long Sleeves:\\n  \\\"MARC\\\" (Price: \\u20b1850.00)\\n  \\\"SEUD\\\" (Price: \\u20b1344.00) - New!\\n  \\\"TEAL\\\" (Price: \\u20b1850.00) - New!\\n  \\\"GUL\\\" (Price: \\u20b1850.00)\\n\"},{\"role\":\"assistant\",\"content\":\"Hi there! How can I help you with BYD-CLOTHING products today?\"},{\"role\":\"user\",\"content\":\"how much is teal\"},{\"role\":\"assistant\",\"content\":\"Here\'s the pricing info for our **TEAL Long Sleeve**:  \\n\\n\\ud83d\\udd25 **TEAL Long Sleeve**  \\n\\ud83d\\udcb0 *Price:* **\\u20b1850**  \\n\\ud83c\\udd95 *New Arrival!*  \\n\\nLet me know if you\'d like details about sizing or other designs! \\ud83d\\ude0a\"}]', '2025-04-16 04:05:28');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `homepage_settings`
--
ALTER TABLE `homepage_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_carts`
--
ALTER TABLE `user_carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_conversations`
--
ALTER TABLE `user_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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

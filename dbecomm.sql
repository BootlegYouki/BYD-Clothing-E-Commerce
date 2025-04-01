-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 01:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


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

--
-- Dumping data for table `carousel_images`
--

INSERT INTO `carousel_images` (`id`, `image_path`, `is_active`, `created_at`) VALUES
(42, 'uploads/carousel/1743089351_1.jpg', 1, '2025-03-27 15:29:11'),
(43, 'uploads/carousel/1743089351_2.jpg', 1, '2025-03-27 15:29:11'),
(44, 'uploads/carousel/1743089351_3.jpg', 1, '2025-03-27 15:29:11'),
(45, 'uploads/carousel/1743089351_4.jpg', 1, '2025-03-27 15:29:11'),
(46, 'uploads/carousel/1743089351_5.jpg', 1, '2025-03-27 15:29:11'),
(47, 'uploads/carousel/1743089351_6.jpg', 1, '2025-03-27 15:29:11'),
(48, 'uploads/carousel/1743089351_7.jpg', 1, '2025-03-27 15:29:11'),
(49, 'uploads/carousel/1743089351_8.jpg', 1, '2025-03-27 15:29:11');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_new_release` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `original_price`, `discount_percentage`, `category`, `is_featured`, `created_at`, `updated_at`, `is_new_release`) VALUES
(14, 'T-S-GIPS-664', 'GIPSY', 'Good', 1000.00, 20, 'T-Shirt', 1, '2025-03-27 10:37:46', '2025-03-27 13:19:46', 1),
(15, 'T-S-MEGA-857', 'megazord', 'asdasd', 1000.00, 30, 'T-Shirt', 1, '2025-03-27 11:01:44', '2025-03-27 13:19:46', 1),
(16, 'T-S-OPTI-222', 'optimus', 'asdasdasdasd', 1000.00, 50, 'T-Shirt', 1, '2025-03-27 11:02:07', '2025-03-27 13:19:45', 1),
(17, 'T-S-PRIM-808', 'primal', 'asdasd', 1000.00, 10, 'T-Shirt', 1, '2025-03-27 11:02:30', '2025-03-27 13:23:27', 1),
(18, 'LON-GUL-505', 'gul', 'angas', 1500.00, 20, 'Long Sleeve', 1, '2025-03-27 12:38:35', '2025-03-27 13:19:31', 0),
(19, 'LON-JAP-621', 'jap', 'takte', 1500.00, 20, 'Long Sleeve', 1, '2025-03-27 12:39:09', '2025-03-27 13:19:30', 0),
(20, 'LON-LEVE-552', 'leve', 'sdasd', 1500.00, 20, 'Long Sleeve', 1, '2025-03-27 12:39:33', '2025-03-27 13:19:30', 0),
(21, 'LON-MACE-226', 'mace', 'asd', 1500.00, 20, 'Long Sleeve', 1, '2025-03-27 12:40:05', '2025-03-27 13:34:11', 0);

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
(42, 14, 'uploads/products/14/primary_1743071866.webp', 1),
(58, 15, 'uploads/products/15/primary_1743073304.webp', 1),
(59, 16, 'uploads/products/16/primary_1743073327.webp', 1),
(60, 17, 'uploads/products/17/primary_1743073350.webp', 1),
(61, 18, 'uploads/products/18/primary_1743079115.jpg', 1),
(62, 19, 'uploads/products/19/primary_1743079149.jpg', 1),
(63, 20, 'uploads/products/20/primary_1743079173.jpg', 1),
(64, 21, 'uploads/products/21/primary_1743079205.jpg', 1);

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
(73, 14, 'XS', 1),
(74, 14, 'S', 1),
(75, 14, 'M', 1),
(76, 14, 'L', 1),
(77, 14, 'XL', 1),
(78, 14, 'XXL', 1),
(79, 15, 'XS', 0),
(80, 15, 'S', 0),
(81, 15, 'M', 0),
(82, 15, 'L', 0),
(83, 15, 'XL', 0),
(84, 15, 'XXL', 0),
(85, 16, 'XS', 0),
(86, 16, 'S', 0),
(87, 16, 'M', 0),
(88, 16, 'L', 0),
(89, 16, 'XL', 0),
(90, 16, 'XXL', 0),
(91, 17, 'XS', 0),
(92, 17, 'S', 0),
(93, 17, 'M', 0),
(94, 17, 'L', 0),
(95, 17, 'XL', 0),
(96, 17, 'XXL', 0),
(97, 18, 'XS', 0),
(98, 18, 'S', 0),
(99, 18, 'M', 0),
(100, 18, 'L', 0),
(101, 18, 'XL', 0),
(102, 18, 'XXL', 0),
(103, 19, 'XS', 0),
(104, 19, 'S', 0),
(105, 19, 'M', 0),
(106, 19, 'L', 0),
(107, 19, 'XL', 0),
(108, 19, 'XXL', 0),
(109, 20, 'XS', 0),
(110, 20, 'S', 0),
(111, 20, 'M', 0),
(112, 20, 'L', 0),
(113, 20, 'XL', 0),
(114, 20, 'XXL', 0),
(115, 21, 'XS', 0),
(116, 21, 'S', 0),
(117, 21, 'M', 0),
(118, 21, 'L', 0),
(119, 21, 'XL', 0),
(120, 21, 'XXL', 0);

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
(11, 'Admin', NULL, 'User', NULL, NULL, 'Admin', NULL, NULL, '$2y$10$UlWm/PGqVkHE9Zt5jIkxrOcjaTY/TTxtT5I88E2VHyxYfhnJ2g.Ly', 1, '2025-03-27 04:05:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carousel_images`
--
ALTER TABLE `carousel_images`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carousel_images`
--
ALTER TABLE `carousel_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

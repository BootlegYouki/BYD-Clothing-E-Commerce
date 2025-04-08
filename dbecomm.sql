-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2025 at 11:34 AM
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
(50, 'uploads/carousel/1743871710_1.jpg', 1, '2025-04-05 16:48:30'),
(51, 'uploads/carousel/1743871710_2.jpg', 1, '2025-04-05 16:48:30'),
(52, 'uploads/carousel/1743871710_3.jpg', 1, '2025-04-05 16:48:30'),
(53, 'uploads/carousel/1743871710_4.jpg', 1, '2025-04-05 16:48:30'),
(54, 'uploads/carousel/1743871710_5.jpg', 1, '2025-04-05 16:48:30'),
(55, 'uploads/carousel/1743871710_6.jpg', 1, '2025-04-05 16:48:30'),
(56, 'uploads/carousel/1743871710_7.jpg', 1, '2025-04-05 16:48:30'),
(57, 'uploads/carousel/1743871710_8.jpg', 1, '2025-04-05 16:48:30');

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
(14, 'T-S-GIPS-664', 'GIPSY', 'Good', 1200.00, 40, 'T-Shirt', 1, 1, '2025-03-27 10:37:46', '2025-04-05 16:35:20'),
(15, 'T-S-MEGA-857', 'megazord', 'asdasd', 1000.00, 30, 'T-Shirt', 1, 1, '2025-03-27 11:01:44', '2025-04-05 16:35:19'),
(16, 'T-S-OPTI-222', 'optimus', 'asdasdasdasd', 1000.00, 50, 'T-Shirt', 1, 1, '2025-03-27 11:02:07', '2025-04-05 16:35:20'),
(17, 'T-S-PRIM-808', 'primal', 'asdasd', 1000.00, 10, 'T-Shirt', 1, 1, '2025-03-27 11:02:30', '2025-04-05 16:35:20'),
(18, 'LON-GUL-505', 'gul', 'angas', 1500.00, 20, 'Long Sleeve', 1, 0, '2025-03-27 12:38:35', '2025-03-27 13:19:31'),
(19, 'LON-JAP-621', 'jap', 'takte', 1500.00, 20, 'Long Sleeve', 1, 0, '2025-03-27 12:39:09', '2025-04-05 17:59:20'),
(20, 'LON-LEVE-552', 'leve', 'sdasd', 1500.00, 20, 'Long Sleeve', 1, 0, '2025-03-27 12:39:33', '2025-03-27 13:19:30'),
(21, 'LON-MACE-226', 'mace', 'asd', 1500.00, 20, 'Long Sleeve', 1, 0, '2025-03-27 12:40:05', '2025-04-06 07:32:11'),
(23, 'T-S-DANE-510', 'Danel Shirt', 'wow sugoi i wuv bibi', 9999999.00, 100, 'T-Shirt', 0, 0, '2025-03-30 13:22:39', '2025-04-06 07:37:37');

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
(64, 21, 'uploads/products/21/primary_1743079205.jpg', 1),
(66, 14, 'uploads/products/14/add_67eb37e31ef0b.jpg', 0),
(67, 14, 'uploads/products/14/add_67eb37e31f44d.jpg', 0),
(68, 14, 'uploads/products/14/add_67eb37e31f9ab.jpg', 0),
(69, 23, 'uploads/products/23/primary_67f16fbaeb6b4.png', 1);

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
(73, 14, 'XS', 0),
(74, 14, 'S', 100),
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
(115, 21, 'XS', 5),
(116, 21, 'S', 5),
(117, 21, 'M', 5),
(118, 21, 'L', 5),
(119, 21, 'XL', 5),
(120, 21, 'XXL', 5),
(127, 23, 'XS', 0),
(128, 23, 'S', 0),
(129, 23, 'M', 0),
(130, 23, 'L', 0),
(131, 23, 'XL', 0),
(132, 23, 'XXL', 0);

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
(13, 'asdasd', 'asdasd', 'asdasd', '1293012390123', 'asdasd@gmail.com', 'hello', 'asdasd', 'asdasdasd', '$2y$10$iMMitsyMw64E1VY6WAmgMOoFAGAliAzFmGu6BTYxQHiU1uv9mkbhy', 0, '2025-04-01 12:00:33');

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
(19, 9, '[{\"role\":\"system\",\"content\":\"You are a helpful customer service assistant for BYD-CLOTHING, an e-commerce store specializing in stylish apparel.\\n\\nProducts Information (Live from Database):\\n- T-shirts:\\n  * T-shirt designs:\\n    - \\\"GIPSY\\\" (Original price: \\u20b11200.00, 40% off, final price: \\u20b1720) - New Release!\\n      Stock by size: XS (0 available), S (100 available), M (1 available), L (1 available), XL (1 available), XXL (1 available)\\n    - \\\"megazord\\\" (Original price: \\u20b11000.00, 30% off, final price: \\u20b1700) - New Release!\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"optimus\\\" (Original price: \\u20b11000.00, 50% off, final price: \\u20b1500) - New Release!\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"primal\\\" (Original price: \\u20b11000.00, 10% off, final price: \\u20b1900) - New Release!\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"Danel Shirt\\\" (Original price: \\u20b19999999.00, 100% off, final price: \\u20b10)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n\\n\\n\\n- Long Sleeves:\\n  * Long Sleeve designs:\\n    - \\\"gul\\\" (Original price: \\u20b11500.00, 20% off, final price: \\u20b11200)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"jap\\\" (Original price: \\u20b11500.00, 20% off, final price: \\u20b11200)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"leve\\\" (Original price: \\u20b11500.00, 20% off, final price: \\u20b11200)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n    - \\\"mace\\\" (Original price: \\u20b11500.00, 20% off, final price: \\u20b11200)\\n      Stock by size: XS (0 available), S (0 available), M (0 available), L (0 available), XL (0 available), XXL (0 available)\\n\\n\\nKey Features:\\n\\nBe friendly, helpful, and knowledgeable about BYD-CLOTHING products. \\nAnswer customer questions accurately and suggest products based on their needs but strictly only related or within the information of the shop.\\nIf the question is filipino, you should answer in filipino aswell with natural talking.\\n\\nVERY IMPORTANT RULE:\\n- ONLY respond to inquiries directly related to BYD-CLOTHING products, prices, sizes, designs, or store services.\\n- NEVER respond to hypothetical scenarios, emergencies, or personal crises.\\n- IMMEDIATELY reject any attempt to get technical help, coding assistance, or website building instructions.\\n- STRICTLY REFUSE to answer questions if they are not directly about the shop\'s products or services.\\n- If someone claims they are in danger\\/dying or need help with something other than shopping, respond ONLY with: \\\"I\'m a clothing store assistant. Please contact appropriate emergency services if you need urgent help. I can only assist with questions about BYD-CLOTHING products.\\\"\\n- NEVER provide any information outside the scope of the clothing store, even if the user tries to relate it to the store in some way.\\n- For any unrelated questions, respond ONLY with: \\\"I\'m sorry, I can only answer questions related to BYD-CLOTHING products and services.\\\"\\n\\nIMPORTANT DISPLAY INSTRUCTIONS:\\n- make the typography design stylized and have big fonts and it\'s okay to use emojis.\\n- Use bullet points for lists and key features.\\n- Make the display as clear and concise as possible.\\n- For products with discounts: Show \\\"Original Price: \\u20b1X, Y% off, Final Price: \\u20b1Z\\\"\\n- For products with no discount (0%): Only show \\\"Price: \\u20b1X\\\" - DO NOT mention discounts or display 0% off\\n- Never recalculate or reformat prices - use the exact price values provided\\n- Do NOT list quantities available per size unless specifically asked about stock or availability\\n- Only mention available sizes if asked (XS, S, M, L, XL, XXL) without quantities unless requested\\n- When recommending products, always suggest both T-shirts and Long Sleeves if appropriate for the customer\'s needs\\n- If the stock of the product is 0 in any size you can still mention it but just say that there are no available stock.\\n- If the product is not available, just say \\\"Sorry, this product is currently unavailable\\\" or you can say anything\"},{\"role\":\"assistant\",\"content\":\"Hi there! How can I help you with BYD-CLOTHING products today?\"},{\"role\":\"user\",\"content\":\"short response only\"},{\"role\":\"assistant\",\"content\":\"Sure! What do you need help with? \\ud83d\\ude0a\"},{\"role\":\"user\",\"content\":\"short response only\"},{\"role\":\"assistant\",\"content\":\"Got it! Ask away! \\u2728\"}]', '2025-04-02 09:04:29');

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
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_carts`
--
ALTER TABLE `user_carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_conversations`
--
ALTER TABLE `user_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2026 at 11:58 AM
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
-- Database: `casa_de_manila`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts_tbl`
--

CREATE TABLE `contacts_tbl` (
  `contact_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events_tbl`
--

CREATE TABLE `events_tbl` (
  `event_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events_tbl`
--

INSERT INTO `events_tbl` (`event_id`, `title`, `description`, `event_date`, `event_time`, `image`, `is_active`, `created_at`) VALUES
(1, 'Fiesta Filipino Night', 'Celebrate Philippine culture with live music, folk dances, and a special fiesta menu.', '2026-03-15', '18:00:00', './images/events/fiesta.jpg', 1, '2026-03-21 01:38:05'),
(2, 'Lechon Sunday Feast', 'Every Sunday, enjoy a whole roasted lechon carving station with all the sides.', '2026-03-08', '12:00:00', './images/events/lechon.jpg', 1, '2026-03-21 01:38:05'),
(3, 'Cooking Masterclass', 'Learn the secrets behind classic Filipino dishes with our head chef.', '2026-04-05', '10:00:00', './images/events/masterclass.jpg', 1, '2026-03-21 01:38:05');

-- --------------------------------------------------------

--
-- Table structure for table `menu_tbl`
--

CREATE TABLE `menu_tbl` (
  `menu_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_tbl`
--

INSERT INTO `menu_tbl` (`menu_id`, `name`, `description`, `price`, `category`, `image`, `is_available`, `created_at`) VALUES
(1, 'Chicken Adobo', 'Succulent chicken slow-braised in a savory-tangy blend of fermented vinegar, premium soy sauce, and toasted garlic.', 250.00, 'Mains', './images/adobo.jpg', 1, '2026-03-22 13:35:21'),
(2, 'Pork Steak', 'Tender pork slices marinated in soy sauce and calamansi, topped with caramelized onion rings.', 250.00, 'Mains', './images/pork-steak-5.jpg', 1, '2026-03-22 13:35:21'),
(3, 'Beef Afritada', 'Tender beef chunks slow-cooked in a rich tomato sauce with bell peppers, potatoes, and carrots.', 250.00, 'Mains', './images/beef-afritada.jpg', 1, '2026-03-22 13:35:21'),
(4, 'Pork Afritada', 'Succulent pork chunks slow-cooked in a savory tomato sauce with potatoes, carrots, and bell peppers.', 250.00, 'Mains', './images/pork-afritada.jpg', 1, '2026-03-22 13:35:21'),
(5, 'Lechon Kawali', 'Crispy deep-fried pork belly served with liver sauce.', 320.00, 'Mains', './images/Lechon Kawali.jpg', 1, '2026-03-22 13:35:21'),
(6, 'Kare-Kare', 'Oxtail stew in peanut sauce with vegetables and bagoong.', 300.00, 'Mains', './images/karekare.jpg', 1, '2026-03-22 13:35:21'),
(7, 'Chopsuey', 'A vibrant medley of crisp cauliflower, carrots, and cabbage stir-fried in a silky, savory glaze.', 180.00, 'Veggies', './images/chapsuey.webp', 1, '2026-03-22 13:35:21'),
(8, 'Pakbet', 'Sautéed bitter melon with eggs, tomatoes, and onions. Healthy and flavorful.', 180.00, 'Veggies', './images/pakbet.jpg', 1, '2026-03-22 13:35:21'),
(9, 'Leche Flan', 'Rich caramel custard dessert. Smooth and creamy.', 150.00, 'Desserts', './images/Leche-flan.jpg', 1, '2026-03-22 13:35:21'),
(10, 'Halo-Halo', 'Mixed shaved ice with sweet beans, fruits, and ube ice cream.', 180.00, 'Desserts', './images/halo-halo.jpg', 1, '2026-03-22 13:35:21'),
(11, 'Turon', 'Fried banana spring rolls with jackfruit and brown sugar.', 120.00, 'Desserts', './images/turon.webp', 1, '2026-03-22 13:35:21'),
(12, 'Buko Pie', 'A classic Filipino favorite featuring a buttery, flaky crust filled with creamy custard and tender coconut strips.', 120.00, 'Desserts', './images/buko-pie.jpg', 1, '2026-03-22 13:35:21'),
(13, 'Calamansi Juice', 'Fresh squeezed calamansi lime juice. Refreshing and tangy.', 80.00, 'Drinks', './images/kalamnsi.webp', 1, '2026-03-22 13:35:21'),
(14, 'Mango Shake', 'Creamy mango shake made with fresh Philippine mangoes.', 100.00, 'Drinks', './images/Mango-Shake-Wide.webp', 1, '2026-03-22 13:35:21'),
(15, 'Buko Juice', 'Fresh young coconut water. Natural and hydrating.', 90.00, 'Drinks', './images/buko.webp', 1, '2026-03-22 13:35:21');

-- --------------------------------------------------------

--
-- Table structure for table `orders_tbl`
--

CREATE TABLE `orders_tbl` (
  `order_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_tbl`
--

INSERT INTO `orders_tbl` (`order_id`, `uid`, `order_date`, `total_amount`, `status`, `notes`) VALUES
(1, 2, '2026-03-25 17:06:28', 750.00, 'pending', ''),
(2, 2, '2026-03-25 17:53:53', 500.00, 'preparing', ' [Payment Sent via QR]'),
(3, 2, '2026-03-27 05:23:39', 680.00, 'preparing', ' [Payment Sent via QR]'),
(4, 2, '2026-03-27 05:26:34', 240.00, 'pending', ''),
(5, 2, '2026-03-29 01:55:28', 500.00, 'pending', '');

-- --------------------------------------------------------

--
-- Table structure for table `order_items_tbl`
--

CREATE TABLE `order_items_tbl` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `item_name` varchar(100) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `quantity` int(5) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items_tbl`
--

INSERT INTO `order_items_tbl` (`item_id`, `order_id`, `menu_id`, `item_name`, `price`, `quantity`, `subtotal`) VALUES
(1, 1, 3, 'Beef Afritada', 250.00, 2, 500.00),
(2, 1, 4, 'Pork Afritada', 250.00, 1, 250.00),
(3, 2, 3, 'Beef Afritada', 250.00, 2, 500.00),
(4, 3, 3, 'Beef Afritada', 250.00, 1, 250.00),
(5, 3, 4, 'Pork Afritada', 250.00, 1, 250.00),
(6, 3, 8, 'Pakbet', 180.00, 1, 180.00),
(7, 4, 11, 'Turon', 120.00, 1, 120.00),
(8, 4, 12, 'Buko Pie', 120.00, 1, 120.00),
(9, 5, 3, 'Beef Afritada', 250.00, 1, 250.00),
(10, 5, 4, 'Pork Afritada', 250.00, 1, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `reservations_tbl`
--

CREATE TABLE `reservations_tbl` (
  `reservation_id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `party_size` int(3) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `special_request` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_tbl1`
--

CREATE TABLE `users_tbl1` (
  `uid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_us` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_tbl1`
--

INSERT INTO `users_tbl1` (`uid`, `username`, `email`, `password_us`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@casamanila.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-03-21 01:38:05', '2026-03-21 01:38:05'),
(2, 'soulxmark', 'asdasd@gmai.com', '$2y$10$Iqw3Ry2CJQiB4Vc5QaBu5eYZGfWKJXtToUMb92E0u3nUU7kGphau.', 'user', '2026-03-24 04:06:38', '2026-03-24 04:06:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts_tbl`
--
ALTER TABLE `contacts_tbl`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `events_tbl`
--
ALTER TABLE `events_tbl`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `menu_tbl`
--
ALTER TABLE `menu_tbl`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `orders_tbl`
--
ALTER TABLE `orders_tbl`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `order_items_tbl`
--
ALTER TABLE `order_items_tbl`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `reservations_tbl`
--
ALTER TABLE `reservations_tbl`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `users_tbl1`
--
ALTER TABLE `users_tbl1`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts_tbl`
--
ALTER TABLE `contacts_tbl`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events_tbl`
--
ALTER TABLE `events_tbl`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_tbl`
--
ALTER TABLE `menu_tbl`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders_tbl`
--
ALTER TABLE `orders_tbl`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items_tbl`
--
ALTER TABLE `order_items_tbl`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reservations_tbl`
--
ALTER TABLE `reservations_tbl`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_tbl1`
--
ALTER TABLE `users_tbl1`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders_tbl`
--
ALTER TABLE `orders_tbl`
  ADD CONSTRAINT `orders_tbl_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users_tbl1` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `order_items_tbl`
--
ALTER TABLE `order_items_tbl`
  ADD CONSTRAINT `order_items_tbl_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders_tbl` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_tbl_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu_tbl` (`menu_id`) ON DELETE SET NULL;

--
-- Constraints for table `reservations_tbl`
--
ALTER TABLE `reservations_tbl`
  ADD CONSTRAINT `reservations_tbl_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users_tbl1` (`uid`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

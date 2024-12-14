-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2024 at 04:04 AM
-- Server version: 8.0.38
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `farm_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_types`
--

CREATE TABLE `activity_types` (
  `id` int NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `activity_types`
--

INSERT INTO `activity_types` (`id`, `type_name`, `description`, `created_at`) VALUES
(2, 'Watering', 'watering for soil moisture', '2024-12-13 15:51:24'),
(3, 'Fertilizing', '', '2024-12-13 15:55:09'),
(5, 'Pest Control', '', '2024-12-14 02:36:18'),
(6, 'Weeding', '', '2024-12-14 02:39:14'),
(7, 'Planting', '', '2024-12-14 02:42:22');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `delivery_address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_phone`, `delivery_address`) VALUES
(1, 'Yeow', '010-11223344', 'Penang 11'),
(2, 'QI', '012-2344312', 'Jitra 13'),
(3, 'lim', '0121342987', 'Jitra 11');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `item_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `recorded_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `date`, `item_id`, `price`, `recorded_by`) VALUES
(1, '2024-12-14', 1, 11.00, 'khor');

-- --------------------------------------------------------

--
-- Table structure for table `farm_activities`
--

CREATE TABLE `farm_activities` (
  `activity_id` int UNSIGNED NOT NULL,
  `activity_type` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `activity_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `person_responsible` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `plot_field` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `specific_area` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `fertilizer1` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fertilizer1_amount` decimal(10,2) DEFAULT NULL,
  `water1` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `water1_amount` decimal(10,2) DEFAULT NULL,
  `other_materials` text COLLATE utf8mb4_general_ci,
  `temperature` float NOT NULL,
  `humidity` float NOT NULL,
  `condition` varchar(250) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farm_activities`
--

INSERT INTO `farm_activities` (`activity_id`, `activity_type`, `activity_date`, `start_time`, `end_time`, `person_responsible`, `plot_field`, `specific_area`, `description`, `fertilizer1`, `fertilizer1_amount`, `water1`, `water1_amount`, `other_materials`, `temperature`, `humidity`, `condition`) VALUES
(3, 'Fertilizing', '2024-12-08 16:00:00', '00:15:00', '12:17:00', 'CY', 'Plot A', 'R2', 'NO', 'F1', 2.00, 'w1', 1.00, 'hoe', 27.95, 83, 'overcast clouds'),
(4, 'Watering', '2024-12-12 16:00:00', '13:52:00', '14:52:00', 'CY', 'Plot B', 'R1', '', '', 2.00, '', 1.00, 'hoe', 25.52, 97, 'overcast clouds'),
(5, 'Pest Control', '2024-12-13 16:00:00', '10:36:00', '11:36:00', 'CY', 'Plot A', 'R1', '', '', 2.00, '', 2.00, 'Hoe', 27.98, 79, 'broken clouds'),
(6, 'Pest Control', '2024-12-13 16:00:00', '10:36:00', '11:36:00', 'CY', 'Plot A', 'R1', '', '', 2.00, '', 2.00, 'Hoe', 27.98, 79, 'broken clouds'),
(7, 'Pest Control', '2024-12-13 16:00:00', '10:40:00', '11:40:00', 'CY', 'Plot C', 'R3', 'MAINTAIN', 'F1', 2.00, 'w1', 2.00, 'Gloves', 27.98, 79, 'broken clouds'),
(8, 'Planting', '2024-12-13 16:00:00', '10:42:00', '14:42:00', 'CY', 'Plot B', 'R1', 'PLANT NEW', 'F1', 1.00, 'w1', 1.00, 'Gloves', 27.98, 79, 'broken clouds'),
(9, 'Fertilizing', '2024-11-13 16:00:00', '10:47:00', '11:47:00', 'yu', 'Plot B', 'R1', '', '', 1.00, '', 1.00, '', 27.98, 79, 'broken clouds');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int NOT NULL,
  `grade` enum('A','B','C') NOT NULL,
  `quantity` int NOT NULL,
  `price_per_kg` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) GENERATED ALWAYS AS ((`quantity` * `price_per_kg`)) STORED,
  `recorded_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `grade`, `quantity`, `price_per_kg`, `recorded_date`) VALUES
(1, 'B', 5, 5.00, '2024-12-10 16:03:34'),
(2, 'A', 4, 7.00, '2024-12-10 16:05:10'),
(3, 'B', 3, 4.00, '2024-12-10 16:05:34'),
(5, 'C', 4, 3.00, '2024-12-10 16:18:15'),
(6, 'B', 3, 6.00, '2024-12-13 14:55:45'),
(7, 'C', 3, 3.00, '2024-12-13 15:01:38'),
(8, 'B', 5, 6.00, '2024-12-13 15:02:38');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `item_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_id`, `item_name`) VALUES
(1, 'ITM001', 'Pesticides');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `payment_method` enum('cash','online_transfer') NOT NULL,
  `delivery_status` tinyint(1) DEFAULT '0',
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_status` enum('Pending','Completed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `payment_method`, `delivery_status`, `order_date`, `total_amount`, `order_status`) VALUES
(2, 2, 'online_transfer', 0, '2024-12-10 23:11:03', 16.00, 'Completed'),
(3, 2, 'cash', 0, '2024-12-10 23:11:17', 10.00, 'Pending'),
(4, 1, 'cash', 0, '2024-12-11 00:07:29', 8.00, 'Completed'),
(5, 1, 'cash', 0, '2024-12-11 00:08:10', 24.00, 'Pending'),
(6, 2, 'cash', 0, '2024-12-11 00:12:57', 16.00, 'Pending'),
(7, 3, 'online_transfer', 0, '2024-12-13 22:32:54', 6.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int NOT NULL,
  `order_id` int NOT NULL,
  `quantity` int NOT NULL,
  `current_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `quantity`, `current_price`) VALUES
(2, 2, 4, 4.00),
(3, 3, 3, 4.00),
(4, 4, 2, 4.00),
(5, 5, 3, 8.00),
(6, 6, 2, 8.00),
(7, 7, 2, 3.00);

-- --------------------------------------------------------

--
-- Table structure for table `pesticide_schedule`
--

CREATE TABLE `pesticide_schedule` (
  `id` int NOT NULL,
  `pesticide_name` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `application_date` date NOT NULL,
  `reapplication_interval` int NOT NULL,
  `next_application_date` date NOT NULL,
  `notes` text,
  `quantity_used` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesticide_schedule`
--

INSERT INTO `pesticide_schedule` (`id`, `pesticide_name`, `method`, `application_date`, `reapplication_interval`, `next_application_date`, `notes`, `quantity_used`) VALUES
(4, 'insecticides', 'chemical', '2024-12-09', 11, '2024-12-20', 'no', 1.00),
(5, 'insecticides', 'chemical', '2024-12-10', 12, '2024-12-22', '', 2.00),
(6, 'organic waste', 'biological', '2024-12-10', 12, '2024-12-22', '', 3.00),
(7, 'insecticides', 'organic', '2024-12-13', 11, '2024-12-24', '', 2.00);

-- --------------------------------------------------------

--
-- Table structure for table `profits`
--

CREATE TABLE `profits` (
  `id` int NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `record_date` date NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `delivery_address` text,
  `recorded_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_history`
--

CREATE TABLE `stock_history` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `action` varchar(50) NOT NULL,
  `quantity` int NOT NULL,
  `unit` varchar(50) NOT NULL,
  `recorded_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_management`
--

CREATE TABLE `stock_management` (
  `id` int NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `last_updated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stock_management`
--

INSERT INTO `stock_management` (`id`, `item_name`, `quantity`, `unit`, `last_updated`) VALUES
(2, 'insecticides', 10, 'Kg', '2024-12-14'),
(3, 'organic waste', 9, 'kg', '2024-12-14'),
(4, 'Pesticides', 3, 'kg', '2024-12-14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(2, 'hon', '$2y$10$z2DR5iPebQaJLRQ7Y6tS1.2uvWcwtKOjLswYJI1adY06w0r/39ACC'),
(3, 'khor', '$2y$10$.IIq6BJXkTOuO2IHKctlW.Wam9AtA3acIjLV2LrOkkl4Y.OrNAwpS'),
(4, 'lemon', '$2y$10$jDO1FICFTB2Wre3rHW19zOwMOP3v9hlr2yTtL0BNTNeyCgTrN61UW');

-- --------------------------------------------------------

--
-- Table structure for table `weather_data`
--

CREATE TABLE `weather_data` (
  `id` int UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `temperature` float NOT NULL,
  `humidity` int NOT NULL,
  `condition` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `weather_data`
--

INSERT INTO `weather_data` (`id`, `date`, `temperature`, `humidity`, `condition`) VALUES
(1, '2024-12-10', 28.5, 75, 'Sunny'),
(8, '2024-12-11', 26.64, 86, 'overcast clouds'),
(9, '2024-12-13', 25.53, 98, 'overcast clouds'),
(15, '2024-12-14', 25.52, 97, 'overcast clouds'),
(18, '2024-12-14', 27.98, 79, 'broken clouds');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_types`
--
ALTER TABLE `activity_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `farm_activities`
--
ALTER TABLE `farm_activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_id` (`item_id`),
  ADD UNIQUE KEY `item_name` (`item_name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `pesticide_schedule`
--
ALTER TABLE `pesticide_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profits`
--
ALTER TABLE `profits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `stock_management`
--
ALTER TABLE `stock_management`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `weather_data`
--
ALTER TABLE `weather_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date_index` (`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_types`
--
ALTER TABLE `activity_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `farm_activities`
--
ALTER TABLE `farm_activities`
  MODIFY `activity_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pesticide_schedule`
--
ALTER TABLE `pesticide_schedule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `profits`
--
ALTER TABLE `profits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_history`
--
ALTER TABLE `stock_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_management`
--
ALTER TABLE `stock_management`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `weather_data`
--
ALTER TABLE `weather_data`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD CONSTRAINT `stock_history_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `stock_management` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

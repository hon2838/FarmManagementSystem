-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2024 at 12:10 PM
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
-- Database: `expenses_revenue`
--

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `item_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `recorded_by` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `date`, `item_id`, `price`, `recorded_by`) VALUES
(15, '2024-12-02', 12, 11.00, 'hon'),
(16, '2024-12-02', 12, 13.00, 'hon'),
(17, '2024-12-03', 10, 14.00, 'hon'),
(18, '2024-12-03', 10, 14.00, 'hon'),
(19, '2024-12-03', 10, 14.00, 'hon'),
(20, '2024-12-02', 14, 9.00, 'hon'),
(21, '2024-12-04', 10, 4.00, 'hon'),
(22, '2024-12-01', 15, 16.00, 'CY'),
(23, '2024-12-04', 12, 11.00, 'CY'),
(24, '2024-11-28', 12, 22.00, 'CY');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `item_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `item_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_id`, `item_name`) VALUES
(10, 'ITM001', 'delivery'),
(12, 'ITM002', 'package'),
(13, 'ITM003', 'salary'),
(14, 'ITM004', 'water'),
(15, 'ITM005', 'Electric');

-- --------------------------------------------------------

--
-- Table structure for table `profits`
--

CREATE TABLE `profits` (
  `id` int NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `record_date` date NOT NULL,
  `order_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `delivery_address` text COLLATE utf8mb4_general_ci,
  `recorded_by` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profits`
--

INSERT INTO `profits` (`id`, `customer_name`, `record_date`, `order_id`, `price`, `delivery_address`, `recorded_by`) VALUES
(1, 'Alif', '2024-12-02', 'ORD001', 50.00, 'uum', 'hon'),
(2, 'Ali', '2024-12-04', 'ORD002', 33.00, 'Penang,16', 'hon'),
(3, 'wei', '2024-12-04', 'ORD003', 67.00, 'kedah', 'hon'),
(4, 'lim', '2024-12-02', 'ORD004', 55.00, 'Jitra', 'CY'),
(5, 'Aini', '2024-11-29', 'ORD005', 33.00, 'jitra', 'CY');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', ''),
(3, 'hon', '$2y$10$5UIWRV7.LaCWtVn7jyQYt.Iidti/tXMiIa4oo57R3qEXIAMcO2NIO'),
(4, 'CY', '$2y$10$KC3BRvUSDXrN16A4/9scXufp8.0NAQbKYW47PoB.BMSLgLEu/ksRW');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_id` (`item_id`),
  ADD UNIQUE KEY `item_name` (`item_name`),
  ADD UNIQUE KEY `item_id_2` (`item_id`),
  ADD UNIQUE KEY `item_name_2` (`item_name`);

--
-- Indexes for table `profits`
--
ALTER TABLE `profits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `profits`
--
ALTER TABLE `profits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

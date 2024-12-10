-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2024 at 06:55 PM
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
-- Database: `pest_control_management`
--
CREATE DATABASE IF NOT EXISTS `pest_control_management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pest_control_management`;

-- --------------------------------------------------------

--
-- Table structure for table `pesticide_schedule`
--

CREATE TABLE `pesticide_schedule` (
  `id` int(11) NOT NULL,
  `pesticide_name` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `application_date` date NOT NULL,
  `reapplication_interval` int(11) NOT NULL,
  `next_application_date` date NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesticide_schedule`
--

INSERT INTO `pesticide_schedule` (`id`, `pesticide_name`, `method`, `application_date`, `reapplication_interval`, `next_application_date`, `notes`) VALUES
(1, 'organic waste', 'organic', '2024-11-29', 30, '2024-12-29', ''),
(3, 'insecticides', 'chemical', '2024-12-06', 50, '2025-01-25', '');

-- --------------------------------------------------------

--
-- Table structure for table `stock_management`
--

CREATE TABLE `stock_management` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `last_updated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_management`
--

INSERT INTO `stock_management` (`id`, `item_name`, `quantity`, `unit`, `last_updated`) VALUES
(1, 'pesticide', 5, 'kg', '2024-11-28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pesticide_schedule`
--
ALTER TABLE `pesticide_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_management`
--
ALTER TABLE `stock_management`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pesticide_schedule`
--
ALTER TABLE `pesticide_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock_management`
--
ALTER TABLE `stock_management`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

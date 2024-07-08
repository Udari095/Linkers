-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 15, 2024 at 12:36 PM
-- Server version: 8.3.0
-- PHP Version: 8.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `experience` text NOT NULL,
  `comment` text NOT NULL,
  `area_text` text NOT NULL,
  `feedback_text` text NOT NULL,
  `rating` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `email`, `experience`, `comment`, `area_text`, `feedback_text`, `rating`, `created_at`) VALUES
(1, 'rajapakshapwr98@gmail.com', 'I feel neutral about the app, it’s just okay.', 'No', 'Battaramulla','Air quality is generally good
Manageable, yet requires careful future planning.', 3, '2024-06-19 09:03:18'),
(2, 'thil4n@gmail.com', 'I am satisfied, the app functions as expected.', 'No', 'Maradana','Has play areas. So good to me.', 4, '2024-06-22 16:44:20'),
(3, 'mu7777178@gmail.com', 'I am very satisfied, the app meets my needs well.', 'No', 'Modara','great area', 5, '2024-06-22 16:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `token` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `expires` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `token`, `email`, `expires`) VALUES
(11, '12750841387ea7dac823ed9040bff1f789af75f33ee4766ab97344f44bbb38bf7f2574523b2695a2dbf3c8697ecbb2c6245d', 'thil4n@gmail.com', 1718430459);

-- --------------------------------------------------------

--
-- Table structure for table `saved_locations`
--

CREATE TABLE `saved_locations` (
  `id` int NOT NULL,
  `email` varchar(200) NOT NULL,
  `location_text` text NOT NULL,
  `location_coordinates` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `saved_locations`
--

INSERT INTO `saved_locations` (`id`, `email`, `location_text`, `location_coordinates`) VALUES
(1, 'rajapakshapwr98@gmail.com', 'Dematagoda', '6.9364,79.8786'),
(2, 'thil4n@gmail.com', 'Kotahena_East', '6.9422,79.8622'),
(3, 'mu7777178@gmail.com', 'Wellawatta_North', '6.8721,79.8613');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(100) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `mobile_phone` varchar(100) NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(200) NOT NULL,
  `zip_code` varchar(200) NOT NULL,
  `role` varchar(10) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `deactivation_requested` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `mobile_phone`, `address`, `city`, `zip_code`, `role`, `status`, `deactivation_requested`) VALUES
(7, 'sarath123@gmail.com', '$2y$10$WgJxB3SRqR.KcLPwdn.cGud8t10HhP.njeC0DhQr3NbuBqr3eZb7W', 'Sarath', 'Wijesinghe', '0714311705', 'No. 2 Metiyagane Beligala', 'Kegalle', '71044', 'user', 'active', 1),
(9, 'danushka@gmail.com', '$2y$10$ZLNU0KSvofFWc2LDDMA.weTNSjDzo5P7njaLe.p5vcoFp.0WTqjG.', 'Thilani', 'Dissanayaka', '0785525001', 'No. 133 Panvilatenna Gampola', 'Kandy', '20544', 'admin', 'active', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_locations`
--
ALTER TABLE `saved_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `saved_locations`
--
ALTER TABLE `saved_locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

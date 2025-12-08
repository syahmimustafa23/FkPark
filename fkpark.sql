-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2025 at 09:21 PM
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
-- Database: `fkpark`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `Booking_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  `Space_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_qrCode` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `demerit_record`
--

CREATE TABLE `demerit_record` (
  `Record_id` int(11) NOT NULL,
  `Total_points` int(11) DEFAULT NULL,
  `Enforcement_status` varchar(20) DEFAULT NULL,
  `Last_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parking_area`
--

CREATE TABLE `parking_area` (
  `Area_id` int(11) NOT NULL,
  `Area_name` varchar(10) DEFAULT NULL,
  `Capacity` int(11) DEFAULT NULL,
  `Status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parking_space`
--

CREATE TABLE `parking_space` (
  `Space_id` int(11) NOT NULL,
  `Area_id` int(11) NOT NULL,
  `Space_num` int(11) DEFAULT NULL,
  `Space_qrCode` text DEFAULT NULL,
  `Current_status` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parking_usage`
--

CREATE TABLE `parking_usage` (
  `Usage_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `duration` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `traffic_summon`
--

CREATE TABLE `traffic_summon` (
  `Summon_id` int(11) NOT NULL,
  `Violation_id` int(11) NOT NULL,
  `Area_id` int(11) NOT NULL,
  `Record_id` int(11) DEFAULT NULL,
  `Summon_qrCode` text DEFAULT NULL,
  `Datetime_issued` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','student','security') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(100) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `user_type`, `created_at`, `updated_at`, `email`) VALUES
(1, 'admin', '$2y$10$6iH.KLVR7.WcMDvSvnGfFuLWpL1M8KNzUKqJKHZ5P7YWZbKnVhfne', 'admin', '2025-12-08 17:20:41', '2025-12-08 17:20:41', ''),
(2, 'student1', '$2y$10$JN4cG.xL5vNKzB2mK9pDHexLzb.WqKzK5dxL7cWqMzL5dxL7cWqMz', 'student', '2025-12-08 17:20:41', '2025-12-08 17:20:41', ''),
(3, 'security1', '$2y$10$9gL4pP5qR6sT7uV8wX9yZaAbCdEfGhIjKlMnOpQrStUvWxYzAbCdEf', 'security', '2025-12-08 17:20:41', '2025-12-08 17:20:41', ''),
(7, 'student2', '$2y$10$YJzTiYd5XDFEG3dqJ8QFWeQrq0Cc80gKeZn2bGoqYzZgZYcW6OOTO', 'student', '2025-12-08 17:29:01', '2025-12-08 17:29:01', '');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vehicle_type` enum('car','motorcycle') NOT NULL,
  `vehicle_model` varchar(20) NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `grant_document` blob NOT NULL,
  `Approval_status` tinyint(1) DEFAULT 0,
  `Approval_date` date DEFAULT NULL,
  `Approval_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `violation_type`
--

CREATE TABLE `violation_type` (
  `Violation_id` int(11) NOT NULL,
  `Violation_name` varchar(20) DEFAULT NULL,
  `Violation_points` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`Booking_id`),
  ADD KEY `User_id` (`User_id`),
  ADD KEY `Space_id` (`Space_id`);

--
-- Indexes for table `demerit_record`
--
ALTER TABLE `demerit_record`
  ADD PRIMARY KEY (`Record_id`);

--
-- Indexes for table `parking_area`
--
ALTER TABLE `parking_area`
  ADD PRIMARY KEY (`Area_id`);

--
-- Indexes for table `parking_space`
--
ALTER TABLE `parking_space`
  ADD PRIMARY KEY (`Space_id`),
  ADD KEY `Area_id` (`Area_id`);

--
-- Indexes for table `parking_usage`
--
ALTER TABLE `parking_usage`
  ADD PRIMARY KEY (`Usage_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `traffic_summon`
--
ALTER TABLE `traffic_summon`
  ADD PRIMARY KEY (`Summon_id`),
  ADD KEY `Violation_id` (`Violation_id`),
  ADD KEY `Area_id` (`Area_id`),
  ADD KEY `Record_id` (`Record_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `license_plate` (`license_plate`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `violation_type`
--
ALTER TABLE `violation_type`
  ADD PRIMARY KEY (`Violation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `Booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `demerit_record`
--
ALTER TABLE `demerit_record`
  MODIFY `Record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parking_area`
--
ALTER TABLE `parking_area`
  MODIFY `Area_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parking_space`
--
ALTER TABLE `parking_space`
  MODIFY `Space_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parking_usage`
--
ALTER TABLE `parking_usage`
  MODIFY `Usage_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `traffic_summon`
--
ALTER TABLE `traffic_summon`
  MODIFY `Summon_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `violation_type`
--
ALTER TABLE `violation_type`
  MODIFY `Violation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`Space_id`) REFERENCES `parking_space` (`Space_id`);

--
-- Constraints for table `parking_space`
--
ALTER TABLE `parking_space`
  ADD CONSTRAINT `parking_space_ibfk_1` FOREIGN KEY (`Area_id`) REFERENCES `parking_area` (`Area_id`);

--
-- Constraints for table `parking_usage`
--
ALTER TABLE `parking_usage`
  ADD CONSTRAINT `parking_usage_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`Booking_id`);

--
-- Constraints for table `traffic_summon`
--
ALTER TABLE `traffic_summon`
  ADD CONSTRAINT `traffic_summon_ibfk_1` FOREIGN KEY (`Violation_id`) REFERENCES `violation_type` (`Violation_id`),
  ADD CONSTRAINT `traffic_summon_ibfk_2` FOREIGN KEY (`Area_id`) REFERENCES `parking_area` (`Area_id`),
  ADD CONSTRAINT `traffic_summon_ibfk_3` FOREIGN KEY (`Record_id`) REFERENCES `demerit_record` (`Record_id`);

--
-- Constraints for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

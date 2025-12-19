-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 02:45 PM
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
-- Table structure for table `approval`
--

CREATE TABLE `approval` (
  `approval_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `approval_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `Booking_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  `Space_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parking_area`
--

CREATE TABLE `parking_area` (
  `Area_id` int(11) NOT NULL,
  `Area_name` varchar(50) NOT NULL,
  `Category` enum('Staff','Student') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parking_space`
--

CREATE TABLE `parking_space` (
  `Space_id` int(11) NOT NULL,
  `Area_id` int(11) NOT NULL,
  `Space_num` varchar(10) NOT NULL,
  `Space_qrCode` text DEFAULT NULL,
  `Current_status` enum('Available','Occupied','Maintenance') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parking_usage`
--

CREATE TABLE `parking_usage` (
  `Usage_id` int(11) NOT NULL,
  `Space_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `entry_time` datetime DEFAULT current_timestamp(),
  `usage_type` enum('Parking','Maintenance') DEFAULT 'Parking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `traffic_summon`
--

CREATE TABLE `traffic_summon` (
  `Summon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `Violation_id` int(11) NOT NULL,
  `Area_id` int(11) NOT NULL,
  `Datetime_issued` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('Admin','Student','Safety_Staff') NOT NULL,
  `full_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `user_type`, `full_name`) VALUES
(1, 'admin1', '$2y$10$.xCnwMk9FUeVSqgF0o7HZ.KGQZ8Xyd13jtdjZH8BqmW7GLOzL3yka', 'Admin', 'System Administrator'),
(2, 'student1', '$2y$10$Dgm/0e1sQRMWWDSJva90auUY83lr81w9YZUUjQ5VIuowF3N6TvwS2', 'Student', 'Ahmad Bin Zaid'),
(3, 'staff1', '$2y$10$4/Sr47z3rLJpbLM4SCRYw.hY4UzbolnAcWIK288Lg.o9U7pgcEFsy', 'Safety_Staff', 'Officer Razak');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_type` enum('car','motorcycle') NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `vehicle_model` varchar(50) DEFAULT NULL,
  `grant_document` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `violation_type`
--

CREATE TABLE `violation_type` (
  `Violation_id` int(11) NOT NULL,
  `Violation_name` varchar(100) NOT NULL,
  `Points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violation_type`
--

INSERT INTO `violation_type` (`Violation_id`, `Violation_name`, `Points`) VALUES
(1, 'Parking Violation', 10),
(2, 'Regulation Non-compliance', 15),
(3, 'Accident Caused', 20);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval`
--
ALTER TABLE `approval`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`Booking_id`),
  ADD KEY `User_id` (`User_id`),
  ADD KEY `Space_id` (`Space_id`);

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
  ADD KEY `Space_id` (`Space_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `traffic_summon`
--
ALTER TABLE `traffic_summon`
  ADD PRIMARY KEY (`Summon_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `Violation_id` (`Violation_id`),
  ADD KEY `Area_id` (`Area_id`);

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
-- AUTO_INCREMENT for table `approval`
--
ALTER TABLE `approval`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `Booking_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `violation_type`
--
ALTER TABLE `violation_type`
  MODIFY `Violation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval`
--
ALTER TABLE `approval`
  ADD CONSTRAINT `approval_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`),
  ADD CONSTRAINT `approval_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);

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
  ADD CONSTRAINT `parking_space_ibfk_1` FOREIGN KEY (`Area_id`) REFERENCES `parking_area` (`Area_id`) ON DELETE CASCADE;

--
-- Constraints for table `parking_usage`
--
ALTER TABLE `parking_usage`
  ADD CONSTRAINT `parking_usage_ibfk_1` FOREIGN KEY (`Space_id`) REFERENCES `parking_space` (`Space_id`),
  ADD CONSTRAINT `parking_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `traffic_summon`
--
ALTER TABLE `traffic_summon`
  ADD CONSTRAINT `traffic_summon_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `traffic_summon_ibfk_2` FOREIGN KEY (`Violation_id`) REFERENCES `violation_type` (`Violation_id`),
  ADD CONSTRAINT `traffic_summon_ibfk_3` FOREIGN KEY (`Area_id`) REFERENCES `parking_area` (`Area_id`);

--
-- Constraints for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

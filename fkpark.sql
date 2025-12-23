-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 03:54 PM
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

--
-- Dumping data for table `approval`
--

INSERT INTO `approval` (`approval_id`, `vehicle_id`, `staff_id`, `status`, `approval_date`) VALUES
(1, 1, 1, 'Approved', '2025-12-21 07:32:06'),
(2, 1, 1, 'Approved', '2025-12-21 07:32:26'),
(3, 1, 1, 'Rejected', '2025-12-21 07:35:26'),
(4, 1, 1, 'Approved', '2025-12-21 07:35:38'),
(5, 1, 1, 'Approved', '2025-12-21 07:39:10'),
(6, 1, 1, 'Rejected', '2025-12-21 07:39:12'),
(9, 4, 9, 'Approved', '2025-12-21 16:57:23'),
(10, 4, 9, 'Rejected', '2025-12-21 16:58:50'),
(11, 4, 9, 'Rejected', '2025-12-21 16:59:43');

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
  `Category` enum('Student','Staff','Event','Visitor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_area`
--

INSERT INTO `parking_area` (`Area_id`, `Area_name`, `Category`) VALUES
(1, 'Block A', 'Student'),
(4, 'Block D', 'Staff'),
(10, 'Block C', 'Student'),
(12, 'block T', 'Staff');

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

--
-- Dumping data for table `parking_space`
--

INSERT INTO `parking_space` (`Space_id`, `Area_id`, `Space_num`, `Space_qrCode`, `Current_status`) VALUES
(1, 1, 'B01', 'https://yourdomain.com/view_space.php?id=B01', 'Occupied'),
(2, 1, 'B02', 'https://yourdomain.com/view_space.php?id=B02', 'Maintenance'),
(3, 1, 'B03', 'https://yourdomain.com/view_space.php?id=B03', 'Available'),
(4, 1, 'B04', 'https://yourdomain.com/view_space.php?id=B04', 'Available'),
(5, 1, 'B05', 'https://yourdomain.com/view_space.php?id=B05', 'Available'),
(6, 1, 'B06', 'https://yourdomain.com/view_space.php?id=B06', 'Available'),
(7, 1, 'B07', 'https://yourdomain.com/view_space.php?id=B07', 'Available'),
(8, 1, 'B08', 'https://yourdomain.com/view_space.php?id=B08', 'Available'),
(9, 1, 'B09', 'https://yourdomain.com/view_space.php?id=B09', 'Available'),
(10, 1, 'B10', 'https://yourdomain.com/view_space.php?id=B10', 'Available'),
(11, 1, 'B11', 'https://yourdomain.com/view_space.php?id=B11', 'Available'),
(12, 1, 'B12', 'https://yourdomain.com/view_space.php?id=B12', 'Available'),
(13, 1, 'B13', 'https://yourdomain.com/view_space.php?id=B13', 'Available'),
(14, 1, 'B14', 'https://yourdomain.com/view_space.php?id=B14', 'Available'),
(15, 1, 'B15', 'https://yourdomain.com/view_space.php?id=B15', 'Available'),
(16, 1, 'B16', 'https://yourdomain.com/view_space.php?id=B16', 'Available'),
(17, 1, 'B17', 'https://yourdomain.com/view_space.php?id=B17', 'Available'),
(18, 1, 'B18', 'https://yourdomain.com/view_space.php?id=B18', 'Available'),
(19, 1, 'B19', 'https://yourdomain.com/view_space.php?id=B19', 'Available'),
(20, 1, 'B20', 'https://yourdomain.com/view_space.php?id=B20', 'Available'),
(51, 4, 'D101', 'https://yourdomain.com/view_space.php?id=D101', 'Available'),
(52, 4, 'D102', 'https://yourdomain.com/view_space.php?id=D102', 'Available'),
(53, 4, 'D103', 'https://yourdomain.com/view_space.php?id=D103', 'Available'),
(54, 4, 'D104', 'https://yourdomain.com/view_space.php?id=D104', 'Available'),
(55, 4, 'D105', 'https://yourdomain.com/view_space.php?id=D105', 'Available'),
(56, 4, 'D106', 'https://yourdomain.com/view_space.php?id=D106', 'Available'),
(57, 4, 'D107', 'https://yourdomain.com/view_space.php?id=D107', 'Available'),
(58, 4, 'D108', 'https://yourdomain.com/view_space.php?id=D108', 'Available'),
(59, 4, 'D109', 'https://yourdomain.com/view_space.php?id=D109', 'Available'),
(60, 4, 'D110', 'https://yourdomain.com/view_space.php?id=D110', 'Available'),
(92, 1, 'A01', 'http://localhost/fkpark/Module3/scan_qr.php?id=A01', 'Available'),
(93, 1, 'A02', 'http://localhost/fkpark/Module3/scan_qr.php?id=A02', 'Available'),
(94, 1, 'A03', 'http://localhost/fkpark/Module3/scan_qr.php?id=A03', 'Available'),
(95, 1, 'A04', 'http://localhost/fkpark/Module3/scan_qr.php?id=A04', 'Available'),
(96, 1, 'A05', 'http://localhost/fkpark/Module3/scan_qr.php?id=A05', 'Available'),
(97, 1, 'A06', 'http://localhost/fkpark/Module3/scan_qr.php?id=A06', 'Available'),
(98, 1, 'A07', 'http://localhost/fkpark/Module3/scan_qr.php?id=A07', 'Available'),
(99, 1, 'A08', 'http://localhost/fkpark/Module3/scan_qr.php?id=A08', 'Available'),
(100, 1, 'A09', 'http://localhost/fkpark/Module3/scan_qr.php?id=A09', 'Available'),
(101, 1, 'A10', 'http://localhost/fkpark/Module3/scan_qr.php?id=A10', 'Available'),
(104, 12, 'T01', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T01', 'Available'),
(105, 12, 'T02', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T02', 'Available'),
(106, 12, 'T03', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T03', 'Available'),
(107, 12, 'T04', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T04', 'Available'),
(108, 12, 'T05', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T05', 'Available'),
(109, 12, 'T06', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T06', 'Available'),
(110, 12, 'T07', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T07', 'Available'),
(111, 12, 'T08', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T08', 'Available'),
(112, 12, 'T09', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T09', 'Available'),
(113, 12, 'T10', 'http://localhost/fkpark/Module 3/scan_qr.php?id=T10', 'Available'),
(114, 12, 'A01', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A01', 'Available'),
(115, 12, 'A02', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A02', 'Available'),
(116, 12, 'A03', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A03', 'Available'),
(117, 12, 'A04', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A04', 'Available'),
(118, 12, 'A05', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A05', 'Available'),
(119, 12, 'A06', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A06', 'Available'),
(120, 12, 'A07', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A07', 'Available'),
(121, 12, 'A08', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A08', 'Available'),
(122, 12, 'A09', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A09', 'Available'),
(123, 12, 'A10', 'http://localhost/fkpark/Module 3/scan_qr.php?id=A10', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `parking_usage`
--

CREATE TABLE `parking_usage` (
  `Usage_id` int(11) NOT NULL,
  `Space_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `entry_time` datetime DEFAULT current_timestamp(),
  `end_time` time NOT NULL,
  `usage_type` enum('Booking','Walk-in','Maintenance') DEFAULT 'Booking',
  `status` varchar(20) NOT NULL,
  `usage_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_usage`
--

INSERT INTO `parking_usage` (`Usage_id`, `Space_id`, `user_id`, `entry_time`, `end_time`, `usage_type`, `status`, `usage_date`) VALUES
(1, 1, 6, '2025-12-23 04:45:00', '00:00:00', '', 'Cancelled', '2025-12-23'),
(2, 1, 6, '2025-12-25 23:38:00', '00:00:00', '', 'Cancelled', '2025-12-25'),
(3, 1, 6, '2025-12-24 02:31:00', '00:00:00', '', 'Cancelled', '2025-12-24'),
(4, 1, 6, '2025-12-23 09:24:00', '00:00:00', '', 'Cancelled', '2025-12-23'),
(5, 2, 6, '2025-12-23 09:25:00', '00:00:00', '', 'Cancelled', '2025-12-23'),
(6, 1, 6, '2025-12-23 09:29:00', '00:00:00', '', 'Cancelled', '2025-12-23'),
(7, 1, 6, '2025-12-23 09:30:00', '00:00:00', '', 'Cancelled', '2025-12-23'),
(8, 3, 11, '2025-12-23 09:30:00', '00:00:00', '', 'Cancelled', '2025-12-23'),
(9, 1, 10, '2025-12-23 09:33:00', '00:00:00', '', 'Cancelled', '2025-12-23'),
(10, 1, 6, '2025-12-23 09:33:00', '00:00:00', '', 'Cancelled', '2025-12-23'),
(11, 1, 6, '2025-12-23 10:46:00', '11:46:00', '', 'Cancelled', '2025-12-23'),
(12, 3, 10, '2025-12-23 10:46:00', '11:46:00', '', 'Cancelled', '2025-12-23'),
(13, 3, 10, '2025-12-23 10:46:00', '11:46:00', '', 'Reserved', '2025-12-23'),
(14, 4, 11, '2025-12-23 10:46:00', '11:46:00', '', 'Cancelled', '2025-12-23'),
(15, 4, 11, '2025-12-23 10:46:00', '11:46:00', '', 'Cancelled', '2025-12-23'),
(19, 1, 11, '2025-12-23 04:08:40', '06:08:40', '', 'Completed', '2025-12-23'),
(20, 1, 11, '2025-12-23 04:13:00', '00:13:00', '', 'Completed', '2025-12-23'),
(23, 1, 11, '2025-12-23 04:33:00', '02:34:00', '', 'Completed', '2025-12-23'),
(25, 1, 11, '2025-12-23 04:00:00', '15:40:00', '', 'Completed', '2025-12-23'),
(28, 1, 6, '2025-12-23 06:45:00', '14:45:00', '', 'Completed', '2025-12-23'),
(30, 53, 6, '2025-12-23 08:08:00', '18:08:00', '', 'Completed', '2025-12-23'),
(31, 52, 11, '2025-12-23 22:17:00', '23:17:00', '', 'Reserved', '2025-12-23'),
(32, 1, 11, '2025-12-23 14:17:00', '21:17:00', '', 'Occupied', '2025-12-23');

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
(1, 'admin90', '$2y$10$.xCnwMk9FUeVSqgF0o7HZ.KGQZ8Xyd13jtdjZH8BqmW7GLOzL3yka', 'Admin', 'System Administrator'),
(4, 'admin5', '$2y$10$YFJeXwKFL3MKLkoVlW1mi.jIAMXjqXTxN9rtTsUdFt6sEdtCbz/6u', 'Admin', 'admin'),
(6, 'student2', '$2y$10$lDe8aKLUsVJeAvtGD2riguscUo.Nu8W/EMDClklBZ7uQuziDgSg86', 'Student', 'student'),
(9, 'staff1', '$2y$10$5cZk9Qp3bRF.JpilEN5/Y.A/iy79WncJsGW78B5zgmT8dWp3c/q3y', 'Safety_Staff', 'staff sae'),
(10, 'student3', '$2y$10$Jm9A.XFU9TBB.fISfA6o8.3cyFrRg6KzdLLx3e78C9IdbmxmmYF5q', 'Student', 'student'),
(11, 'student5', '$2y$10$SuXMAjz./1zc4p1WIDQY4urF43Ly5Pe4l4S9lq2aKSIq89ei8CMqi', 'Student', 'student');

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

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`vehicle_id`, `user_id`, `vehicle_type`, `license_plate`, `vehicle_model`, `grant_document`) VALUES
(1, 1, 'car', 'PMX2025', 'HONDA', 'uploads/grants/1766301475_Integrated System and Sensor Application-Hardware.pdf'),
(4, 6, 'car', 'PMY2025', 'HONDA', 'uploads/grants/1766336133_BCS2243_Lab 8 JavaScript.pdf'),
(5, 11, 'car', 'PQX2345', 'HONDA', 'uploads/grants/1766340094_AI REPORT.pdf'),
(6, 10, 'car', 'poi1234', 'HONDA', 'uploads/grants/1766421870_Screenshot 2025-12-22 131654.png');

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
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `Booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `parking_area`
--
ALTER TABLE `parking_area`
  MODIFY `Area_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `parking_space`
--
ALTER TABLE `parking_space`
  MODIFY `Space_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `parking_usage`
--
ALTER TABLE `parking_usage`
  MODIFY `Usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `traffic_summon`
--
ALTER TABLE `traffic_summon`
  MODIFY `Summon_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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

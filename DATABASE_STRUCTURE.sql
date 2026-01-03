-- FKPark QR Code System - Database Configuration
-- This file documents the database structure for the QR code system

-- ✅ No database changes required!
-- The parking_space table already has the Space_qrCode column

-- Current parking_space table structure:
CREATE TABLE `parking_space` (
  `Space_id` int(11) NOT NULL,
  `Area_id` int(11) NOT NULL,
  `Space_num` varchar(10) NOT NULL,
  `Space_qrCode` text DEFAULT NULL,  -- ✅ Already exists - stores QR code path
  `Current_status` enum('Available','Occupied','Maintenance') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data after QR code generation:
-- 
-- Space_id | Area_id | Space_num | Space_qrCode | Current_status
-- ---------|---------|-----------|----------------------------------------------|----------------
-- 92       | 1       | A01       | qr_codes/A01.png                             | Available
-- 93       | 1       | A02       | qr_codes/A02.png                             | Available
-- 94       | 1       | A03       | qr_codes/A03.png                             | Available
-- 104      | 12      | T01       | qr_codes/T01.png                             | Available
-- 
-- Note: QR codes are stored as local PNG files in the /qr_codes/ directory
-- URL format: http://localhost/fkpark/Module2/view_space.php?id=A01

-- Additional information:
-- The Space_qrCode column stores either:
-- 1. Local path: qr_codes/A01.png
-- 2. Full URL: https://api.qrserver.com/v1/create-qr-code/?...
--    (used as fallback if local storage fails)

-- No additional tables needed!
-- The system uses existing tables:
-- - parking_space (stores QR code paths)
-- - parking_area (area information)
-- - booking (booking information)
-- - parking_usage (usage tracking)

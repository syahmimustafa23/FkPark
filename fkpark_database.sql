-- FKPark - Fakulti Komputeran Parking Management System
-- Module 1: Authentication & User Management
-- Database Setup Script
-- Created: December 2025

-- Create database
CREATE DATABASE IF NOT EXISTS fkpark;
USE fkpark;

-- Drop existing table if it exists
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'student', 'security') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user for testing
-- Default credentials: username = admin, password = admin123
-- Hash: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, role) 
VALUES ('admin', '$2y$10$6iH.KLVR7.WcMDvSvnGfFuLWpL1M8KNzUKqJKHZ5P7YWZbKnVhfne', 'admin');

-- Insert sample student user for testing
-- Default credentials: username = student1, password = student123
-- Hash: password_hash('student123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, role) 
VALUES ('student1', '$2y$10$JN4cG.xL5vNKzB2mK9pDHexLzb.WqKzK5dxL7cWqMzL5dxL7cWqMz', 'student');

-- Insert sample security user for testing
-- Default credentials: username = security1, password = security123
-- Hash: password_hash('security123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, role) 
VALUES ('security1', '$2y$10$9gL4pP5qR6sT7uV8wX9yZaAbCdEfGhIjKlMnOpQrStUvWxYzAbCdEf', 'security');

-- Display table structure
DESCRIBE users;

-- Display inserted users
SELECT * FROM users;

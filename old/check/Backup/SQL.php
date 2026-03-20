-- Create database
CREATE DATABASE bikesafety;

-- Use database
USE bikesafety;

-- Create users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  address_line1 VARCHAR(100) NOT NULL,
  address_line2 VARCHAR(100),
  postcode VARCHAR(20) NOT NULL,
  county VARCHAR(50) NOT NULL,
  city VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

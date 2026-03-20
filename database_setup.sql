-- Create database if not exists
CREATE DATABASE IF NOT EXISTS bikesafety;
USE bikesafety;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('public', 'police', 'admin') DEFAULT 'public',
    phone VARCHAR(20),
    address_line1 VARCHAR(100),
    address_line2 VARCHAR(100),
    postcode VARCHAR(20),
    county VARCHAR(50),
    city VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bicycles table
CREATE TABLE IF NOT EXISTS bicycles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mpn VARCHAR(50),
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    wheel_size VARCHAR(20),
    colour VARCHAR(20) NOT NULL,
    gears INT,
    brake_type VARCHAR(50),
    suspension VARCHAR(50),
    gender VARCHAR(20),
    age_group VARCHAR(50),
    image_path VARCHAR(255),
    status ENUM('safe', 'stolen') DEFAULT 'safe',
    report_date DATETIME,
    investigation_status ENUM('open', 'closed', 'in_progress') DEFAULT 'open',
    investigation_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert a default admin user (password: admin123)
-- Note: You should change this password in production!
INSERT INTO users (first_name, last_name, email, password, role) 
VALUES ('Admin', 'User', 'admin@police.uk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Drop tables (in reverse order of dependency: child tables first)
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `report`;
DROP TABLE IF EXISTS `bicycles`;
DROP TABLE IF EXISTS `users`;

-- Users table
CREATE TABLE `users` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('public', 'police', 'admin') DEFAULT 'public',
  phone VARCHAR(20),
  address_line1 VARCHAR(100) NOT NULL,
  address_line2 VARCHAR(100),
  postcode VARCHAR(20) NOT NULL,
  county VARCHAR(50) NOT NULL,
  city VARCHAR(50) NOT NULL,
  date_of_birth DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bicycles table
CREATE TABLE `bicycles` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  mpn VARCHAR(50),
  brand VARCHAR(50) NOT NULL,
  model VARCHAR(50) NOT NULL,
  type VARCHAR(50) NOT NULL,
  wheel_size INT,
  colour VARCHAR(20) NOT NULL,
  gears INT,
  brake_type VARCHAR(50),
  suspension VARCHAR(50),
  gender ENUM('Male','Female','Unisex') DEFAULT 'Unisex',
  age_group ENUM('Adult','Child') DEFAULT 'Adult',
  image_path VARCHAR(255),
  status ENUM('safe', 'stolen', 'sold') DEFAULT 'safe',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Report table
CREATE TABLE `report` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bicycle_id INT NOT NULL,
  report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  incident_location VARCHAR(255),
  investigation_status ENUM('open', 'closed', 'in_progress') DEFAULT 'open',
  investigation_notes TEXT,
  bike_status ENUM('stolen', 'captured', 'returned') DEFAULT 'stolen',
  closed_at DATETIME DEFAULT NULL
);

-- Activity Log table
CREATE TABLE IF NOT EXISTS `activity_log` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(100) NOT NULL,
  table_name VARCHAR(50) NULL,
  record_id INT NULL,
  details TEXT,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Password Reset Tokens table
CREATE TABLE `password_reset_tokens` (
  email VARCHAR(100) NOT NULL,
  token VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (email, token)
);

-- Insert default admin users (password: admin)
-- Public admin@public.uk
INSERT INTO `users` (
  first_name, 
  last_name, 
  email, 
  password, 
  role,
  address_line1,
  postcode,
  county,
  city,
  date_of_birth
) VALUES (
  'Test public', 
  'User', 
  'admin@public.uk', 
  '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 
  'public',
  '123 Police Station',
  'PO1 1AA',
  'Hampshire',
  'Portsmouth',
  '1980-01-01'
) ON DUPLICATE KEY UPDATE email = email;

-- Police admin@police.uk
INSERT INTO `users` (
  first_name, 
  last_name, 
  email, 
  password, 
  role,
  address_line1,
  postcode,
  county,
  city,
  date_of_birth
) VALUES (
  'Test Police', 
  'User', 
  'admin@police.uk', 
  '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 
  'police',
  '123 Police Station',
  'PO1 1AA',
  'Hampshire',
  'Portsmouth',
  '1980-01-01'
) ON DUPLICATE KEY UPDATE email = email;

-- Police admin@admin.uk
INSERT INTO `users` (
  first_name, 
  last_name, 
  email, 
  password, 
  role,
  address_line1,
  postcode,
  county,
  city,
  date_of_birth
) VALUES (
  'Test Admin', 
  'User', 
  'admin@admin.uk', 
  '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 
  'admin',
  '123 Police Station',
  'PO1 1AA',
  'Hampshire',
  'Portsmouth',
  '1980-01-01'
) ON DUPLICATE KEY UPDATE email = email;

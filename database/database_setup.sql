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


-- Dummy Data Insert Script 

-- 1-4: Insert 4 Users with various roles
INSERT INTO `users` (first_name, last_name, email, password, role, phone, address_line1, postcode, county, city, date_of_birth) VALUES
('John', 'Doe', 'john.doe@example.com', '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 'public', '07700900001', '10 High Street', 'SW1A 1AA', 'Greater London', 'London', '1990-05-15'),
('Jane', 'Smith', 'jane.smith@example.com', '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 'public', '07700900002', '25 Low Lane', 'M1 1AA', 'Greater Manchester', 'Manchester', '1985-08-20'),
('Bob', 'Bobby', 'bob.police@police.uk', '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 'police', '07700900003', 'Central Police Station', 'B1 1AA', 'West Midlands', 'Birmingham', '1975-11-10'),
('Alice', 'Admin', 'alice.admin@admin.uk', '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 'admin', '07700900004', 'Admin HQ', 'EH1 1AA', 'Midlothian', 'Edinburgh', '1982-03-25'),
('Public', 'User', 'admin@public.uk', '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 'public', '07700900005', 'Admin HQ', 'EH1 1AA', 'Midlothian', 'Edinburgh', '2020-04-25'),
('Police', 'User', 'admin@police.uk', '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 'police', '07700900005', 'Admin HQ', 'EH1 1AA', 'Midlothian', 'Edinburgh', '2020-04-25'),
('Admin', 'User', 'admin@admin.uk', '$2a$12$kuoOxc9DHB/hXwALmFyygeD5khB3ivd.Nt.gyBNXzyK/yFvVLpPWG', 'admin', '07700900005', 'Admin HQ', 'EH1 1AA', 'Midlothian', 'Edinburgh', '2020-04-25');

-- 5-8: Insert 4 Bicycles assigned to the public users
INSERT INTO `bicycles` (user_id, mpn, brand, model, type, wheel_size, colour, gears, brake_type, suspension, gender, age_group, status) VALUES
((SELECT id FROM users WHERE email='john.doe@example.com'), 'MPN001', 'Trek', 'FX 3', 'Hybrid', 28, 'Black', 18, 'Disc', 'None', 'Male', 'Adult', 'stolen'),
((SELECT id FROM users WHERE email='john.doe@example.com'), 'MPN002', 'Specialized', 'Rockhopper', 'Mountain', 29, 'Red', 21, 'Disc', 'Front', 'Unisex', 'Adult', 'stolen'),
((SELECT id FROM users WHERE email='jane.smith@example.com'), 'MPN003', 'Giant', 'Escape', 'Hybrid', 28, 'Blue', 21, 'Rim', 'None', 'Female', 'Adult', 'stolen'),
((SELECT id FROM users WHERE email='jane.smith@example.com'), 'MPN004', 'Brompton', 'M6L', 'Folding', 16, 'Green', 6, 'Rim', 'None', 'Unisex', 'Adult', 'stolen');

-- 9-12: Insert 4 Reports connected to the stolen bicycles
INSERT INTO `report` (bicycle_id, incident_location, investigation_status, investigation_notes, bike_status, report_date, closed_at) VALUES
((SELECT id FROM bicycles WHERE mpn='MPN001'), 'London Victoria Station', 'open', 'Bike stolen from bike rack.', 'stolen', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 28 YEAR), NULL),
((SELECT id FROM bicycles WHERE mpn='MPN002'), 'Hyde Park', 'in_progress', 'CCTV footage is being reviewed.', 'stolen', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 15 YEAR), NULL),
((SELECT id FROM bicycles WHERE mpn='MPN003'), 'Manchester Piccadilly', 'closed', 'Bike was found abandoned and returned to owner.', 'returned', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 7 YEAR), DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 6 YEAR)),
((SELECT id FROM bicycles WHERE mpn='MPN004'), 'Oxford Street', 'in_progress', 'Suspect captured, awaiting bike retrieval.', 'captured', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 2 YEAR), NULL);

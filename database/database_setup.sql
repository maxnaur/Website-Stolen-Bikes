-- Drop tables (in reverse order of dependency: child tables first)
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
);-- ENGINE=InnoDB;

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
);-- ENGINE=InnoDB;

-- Add foreign key constraint for bicycles
-- ALTER TABLE `bicycles`
-- ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);

-- Report table
CREATE TABLE `report` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bicycle_id INT NOT NULL,
  report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  investigation_status ENUM('open', 'closed', 'in_progress') DEFAULT 'open',
  investigation_notes TEXT,
  bike_status ENUM('stolen', 'captured', 'returned') DEFAULT 'stolen',  -- Fixed: 'safe' not in ENUM
  closed_at DATETIME DEFAULT NULL
);-- ENGINE=InnoDB;

-- Add foreign key constraint for report
-- ALTER TABLE `report`
-- ADD FOREIGN KEY (`bicycle_id`) REFERENCES `bicycles`(`id`);

-- Insert default admin user (password: admin123)
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
  'Admin', 
  'User', 
  'admin@police.uk', 
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
  'admin',
  '123 Police Station',
  'PO1 1AA',
  'Hampshire',
  'Portsmouth',
  '1980-01-01'
) ON DUPLICATE KEY UPDATE email = email;

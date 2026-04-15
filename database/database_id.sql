-- Drop tables (if they exist)
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS bicycles;
DROP TABLE IF EXISTS report;

-- Create users table (with corrected column names)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  phone VARCHAR(20),
  date_of_birth DATE,
  address_line1 VARCHAR(100) NOT NULL,
  address_line2 VARCHAR(100),
  postcode VARCHAR(10) NOT NULL,
  county VARCHAR(50) NOT NULL,
  city VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  salt VARCHAR(64) NOT NULL,  -- 16 bytes * 2 (hex) = 32 characters
  role TEXT DEFAULT 'public',
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

/*
-- Create bicycles table (with corrected column names and defaults)
CREATE TABLE bicycles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    mpn TEXT,
    brand TEXT NOT NULL,
    model TEXT NOT NULL,
    type TEXT NOT NULL,
    wheel_size INTEGER,
    colour TEXT NOT NULL,
    gears INTEGER,
    brake_type TEXT,
    suspension TEXT,
    gender TEXT DEFAULT 'Unisex',
    age_group TEXT DEFAULT 'Adult',
    image_path TEXT,
    status TEXT DEFAULT 'safe',
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Create report table (with corrected column names)
CREATE TABLE report (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    bicycle_id INTEGER NOT NULL,
    report_date TEXT DEFAULT CURRENT_TIMESTAMP,
    investigation_status TEXT DEFAULT 'open',
    investigation_notes TEXT,
    bike_status TEXT DEFAULT 'stolen',
    closed_at TEXT
);

-- Add foreign key constraints
ALTER TABLE bicycles ADD FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE report ADD FOREIGN KEY (bicycle_id) REFERENCES bicycles(id);

-- Add default admin user (with password hash)
INSERT INTO users (first_name, last_name, email, password, role)
VALUES ('Admin', 'User', 'admin@police.uk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Important fix for SQLite: 
-- The original upload had typos like "dicycles_id" and "pastcode" 
-- We've corrected these to proper SQLite names
*/
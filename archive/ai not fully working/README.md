# Bike Safety System

This is a web application for the Gloucestershire Constabulary to help reduce bicycle theft.

## Features

### Public Users
- **Register/Login:** Create an account and log in securely.
- **Dashboard:** View registered bikes.
- **Register Bike:** Add details of new bicycles (MPN, Brand, Model, Type, Images, etc.).
- **Report Stolen:** Report a registered bike as stolen.
- **Check Status:** View the investigation status of stolen bikes.

### Police Users
- **Login:** Secure login for officers.
- **Dashboard:** View all reported stolen bikes.
- **Filter:** Filter stolen bikes by week, month, or year.
- **Update Status:** Update the investigation status (Open, In Progress, Closed) and add notes.
- **Admin:** Admin officers can enroll new police officers.

## Setup Instructions

1.  **Database Setup:**
  - Import `database_setup.sql` into your MySQL/MariaDB database.
  - Alternatively, run `php setup.php` (ensure your database credentials are correct in `config.php`).

2.  **Configuration:**
  - Edit `config.php` to match your database credentials (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`).

3.  **Running:**
  - Serve the `project` directory using a PHP-enabled web server (Apache, Nginx, or PHP built-in server).
  - Example: `php -S localhost:8000` inside the `project` directory.
  - Access `http://localhost:8000/login.php` in your browser.

## Default Admin Account
- **Email:** admin@police.uk
- **Password:** admin123

## File Structure
- `login.php`: Login page.
- `register.php`: Registration page.
- `auth.php`: Backend logic for authentication.
- `dashboard.php`: Public user dashboard.
- `police_dashboard.php`: Police dashboard.
- `add_bike.php`: Form to register a new bike.
- `bikes.php`: Backend logic for bike operations.
- `forgot_password.php`: Password reset functionality.
- `db.php` & `config.php`: Database connection.
- `setup.php`: Database initialization script.
- `database_setup.sql`: SQL schema.
- `uploads/`: Directory for storing bike images.

## Technologies
- HTML5, CSS3 (Bootstrap 5.3)
- PHP
- MySQL/MariaDB
- JavaScript

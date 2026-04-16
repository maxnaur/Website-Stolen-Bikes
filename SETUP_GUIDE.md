# 🚲 Bike Police System - Setup Guide

## Quick Start

### Step 1: Ensure MySQL is Running
- **Windows**: Open Services and start "MySQL" or use `net start MySQL80`
- **Mac**: `brew services start mysql` or use System Preferences
- **Linux**: `sudo systemctl start mysql`

### Step 2: Run Database Setup
1. Open your browser and go to: `http://localhost/project/database/setup.php`
2. Follow the instructions on screen
3. You'll see a confirmation when setup is complete
4. **Default Admin Credentials:**
   - Email: `admin@police.uk`
   - Password: `admin123`

### Step 3: Test Connection (Optional)
To verify everything is working:
1. Go to: `http://localhost/project/database/test-connection.php`
2. All tests should show ✓ green checkmarks

### Step 4: Access the System
1. Go to: `http://localhost/project/login/Login.html`
2. Login with the admin credentials above
3. You'll be redirected to your dashboard based on your role

---

## System Structure

### User Roles & Access

#### 👤 **Public Users**
- Register new account
- Register bicycles
- Report lost/stolen bikes
- View their own reports
- Dashboard: `public/public-dashboard.php`

#### 🚔 **Police Officers**
- View all bike reports
- Update investigation status
- Mark bikes as captured/recovered
- Dashboard: `police/police-dashboard.php`

#### ⚙️ **Administrators**
- Manage all users
- Manage bicycle registry
- View all reports
- System administration
- Dashboard: `admin/admin-dashboard.php`

---

## File Structure

```
project/
├── database/
│   ├── config.php       # Database connection
│   ├── setup.php      # Run this once to initialize
│   ├── test-connection.php  # Test connectivity
│   └── database_setup.sql   # SQL schema
├── api/
│   ├── bicycles.php     # Bicycle REST API
│   └── reports.php      # Reports REST API
├── common/
│   ├── auth-check.php     # Session validation
│   ├── style/
│   ├── sidebar/
│   └── common.php
├── login/
│   ├── Login.html       # Login page
│   ├── login.php      # Login backend
│   └── logout.php       # Logout handler
├── register/
│   ├── register.html    # Registration page
│   └── create_register.php  # Registration backend
├── public/
│   ├── public-dashboard.php # Public user dashboard
│   └── Public.html      # Old template
├── police/
│   ├── police-dashboard.php # Police dashboard
│   └── Police.html      # Old template
└── admin/
  ├── admin-dashboard.php  # Admin dashboard
  └── Admin.html       # Old template
```

---

## API Endpoints

### Bicycles API
- `GET /api/bicycles.php?action=list` - List bikes
- `GET /api/bicycles.php?action=detail&id=1` - Get bike details
- `POST /api/bicycles.php?action=add` - Add new bike
- `PUT /api/bicycles.php?action=update&id=1` - Update bike

### Reports API
- `GET /api/reports.php` - List reports
- `GET /api/reports.php?status=stolen` - Filter by status
- `POST /api/reports.php?action=create` - Create report
- `PUT /api/reports.php?action=update&id=1` - Update report

---

## Troubleshooting

### "Database connection failed"
- ✅ Ensure MySQL is running
- ✅ Visit `http://localhost/project/database/test-connection.php`
- ✅ Check MySQL credentials in `database/config.php`

### "Access denied for user 'root'"
- Verify MySQL root user has no password (or update config.php)
- Run: `mysql -u root -e "SELECT 1;"`

### Database doesn't exist
- Run: `http://localhost/project/database/setup.php`

### Tables not created
- Check error message on setup.php page
- Run SQL manually using MySQL Workbench or phpMyAdmin

---

## Key Features Implemented

✅ **Authentication System**
- Secure login with password hashing
- Session management
- Role-based access control

✅ **User Management**
- Registration with validation
- User profiles
- Role assignment (public, police, admin)

✅ **Bicycle Management**
- Register bikes
- Track bike status
- View all bikes (admin/police)

✅ **Report System**
- File theft/loss reports
- Investigation tracking
- Status updates

✅ **Dashboard System**
- Role-specific dashboards
- Statistics overview
- Report management

✅ **API Endpoints**
- RESTful APIs for data access
- JSON responses
- Role-based authorization

---

## Database Schema

### users
- id, first_name, last_name, email, password, role, phone, address, postcode, county, city, date_of_birth, created_at

### bicycles
- id, user_id, mpn, brand, model, type, wheel_size, colour, gears, brake_type, suspension, gender, age_group, image_path, status, created_at

### report
- id, bicycle_id, report_date, investigation_status, investigation_notes, bike_status, closed_at

---

## Next Steps

1. ✅ Setup database (see Step 2 above)
2. ✅ Test the system
3. 📋 Integrate frontend forms with APIs
4. 🔔 Add email notifications
5. 📊 Add data export features
6. 🔒 Enhance security (2FA, rate limiting, etc.)

---

## Support

For issues or questions:
1. Check `test-connection.php` for connection errors
2. Review error logs in browser console
3. Check PHP error logs in MySQL/Apache logs
4. Verify all files are in correct locations

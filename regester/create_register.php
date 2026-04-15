<?php
// Include database configuration
require_once '../database/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $dob = $_POST['DOB'] ?? '';
    $addressLine1 = $_POST['addressLine1'] ?? '';
    $addressLine2 = $_POST['addressLine2'] ?? '';
    $postcode = $_POST['postcode'] ?? '';
    $county = $_POST['county'] ?? '';
    $city = $_POST['city'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($addressLine1) || 
        empty($postcode) || empty($county) || empty($city)) {
        http_response_code(400);
        echo "Please fill in all required fields";
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please enter a valid email address";
        exit;
    }
    
    // Validate email match
    if ($email !== $_POST['confirmEmail']) {
        http_response_code(400);
        echo "Email addresses do not match";
        exit;
    }
    
    // Validate password requirements
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) {
        http_response_code(400);
        echo "Password must be 8+ characters with uppercase, lowercase, number and special character";
        exit;
    }
    
    // Validate phone number
    // allow null
    if (!preg_match('/^[\d\s\-]{7,15}$/', $phone) || $phone == '') {
        http_response_code(400);
        echo "Please enter a valid phone number";
        exit;
    }
    
    // Validate DOB format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
        http_response_code(400);
        echo "Please enter a valid date of birth";
        exit;
    }
    
    // Generate random salt (16 bytes)
    $salt = bin2hex(random_bytes(16));
    
    // Hash password with salt using SHA256
    $hashedPassword = hash('sha256', $password . $salt);
    
    // Insert into database
    try {
        $stmt = $connection->prepare("
            INSERT INTO users (
                first_name, 
                last_name, 
                email, 
                phone, 
                date_of_birth, 
                address_line1, 
                address_line2, 
                postcode, 
                county, 
                city,
                password,
                salt
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $firstName,
            $lastName,
            $email,
            $phone,
            $dob,
            $addressLine1,
            $addressLine2,
            $postcode,
            $county,
            $city,
            $hashedPassword,
            $salt
        ]);
        
        // Redirect to login
        header("Location: ../login/Login.html?success=Account created successfully!");
        exit;
        
    } catch (Exception $e) {
        http_response_code(500);
        echo "Database error: " . $e->getMessage();
        exit;
    }
}
?>

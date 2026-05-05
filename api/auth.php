<?php
/**
 * api/auth.php
 * Centralized Authentication & Registration API
 */
header('Content-Type: application/json');
require_once '../common/common.php';
require_once '../common/security.php';

// Set security headers
setSecurityHeaders();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? '';

if ($action === 'csrf') {
    handleCsrfToken();
}

try {
    if (in_array($action, ['login', 'register'], true)) {
        require_once '../database/config.php';
    }
} catch (Throwable $e) {
    error_log('Auth database init error: ' . $e->getMessage());
    if ($action === 'register') {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed. Please try again later.']);
        exit;
    }
    // Handle general error for other actions like login
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Please try again later.']);
    exit;
}

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'logout':
        handleLogout();
        break;
    case 'register':
        handleRegister();
        break;
    case 'csrf':
        handleCsrfToken();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Handle user login - MODIFIED TO RETURN JSON ONLY
 */
function handleLogin() {
    global $pdo;
    
    // Check if coming from form post or JSON fetch
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        // Rate limiting check
        if (!checkRateLimit($email, 5, 300)) {
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Too many login attempts. Please try again later.']);
            exit;
        }
        // Send JSON error for client handling (HTTP 400 Bad Request)
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please fill in both email and password.']);
        exit;
    }

    // Rate limiting check
    if (!checkRateLimit($email, 5, 300)) {
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Too many login attempts. Please try again later.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, role, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID on login (security best practice)
            session_regenerate_id(true);
            
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['role'] = $user['role'];
            
            logActivity($pdo, 'LOGIN', 'users', $user['id'], "User $email logged in successfully");

            $role = strtolower($user['role'] ?? 'public');
            $redirect = '';
            if ($role === 'police') $redirect = '../police/police.html';
            else if ($role === 'admin') $redirect = '../admin/admin.html';
            else $redirect = '../public/public.html';

            // SUCCESS: Send JSON payload with redirect path
            echo json_encode(['success' => true, 'message' => 'Login successful!', 'redirect' => $redirect]);
            exit;
        } else {
            logActivity($pdo, 'LOGIN_FAILED', 'users', null, "Failed login attempt for email: $email");
            // FAILURE: Unauthorized credentials (HTTP 401)
            header('Content-Type: application/json');
            http_response_code(401); 
            echo json_encode(['success' => false, 'message' => 'Incorrect login or password']);
            exit;
        }
    } catch (PDOException $e) {
        error_log("Login API Error: " . $e->getMessage());
        // FAILURE: Database error (HTTP 500)
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'System maintenance. Please try again later.']);
        exit;
    } catch (Throwable $e) {
         // Catch all other exceptions
        error_log("Login API General Error: " . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
        exit;
    }
}

/**
 * Handle user logout
 */
function handleLogout() {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    // CHANGE: Always return JSON on success/logout
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Logged out successfully.', 'redirect' => '../login/login.html']);
    exit;
}

/**
 * Handle user registration
 */
function handleRegister() {
    global $pdo;
    
    // CSRF Check
    validateCsrfToken($_POST['csrf_token'] ?? '');
    
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['DOB'] ?? '');
    $addressLine1 = trim($_POST['addressLine1'] ?? '');
    $addressLine2 = trim($_POST['addressLine2'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');
    $county = trim($_POST['county'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmEmail = trim($_POST['confirmEmail'] ?? '');
  
    if (empty($firstName) || empty($lastName) || empty($email) || empty($addressLine1) || 
        empty($postcode) || empty($county) || empty($city) || empty($password) || empty($dob)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Please fill in all required fields"]);
        exit;
    }
    
    if ($email !== $confirmEmail) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Email addresses do not match"]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    try {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Email address already registered"]);
            exit;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, phone, date_of_birth, address_line1, address_line2, postcode, county, city, password, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'public')
        ");
        
        $stmt->execute([$firstName, $lastName, $email, $phone ?: null, $dob, $addressLine1, $addressLine2 ?: null, $postcode, $county, $city, $hashedPassword]);
        
        $user_id = $pdo->lastInsertId();
        logActivity($pdo, 'REGISTER', 'users', $user_id, "New account registered: $email");
        
        echo json_encode(['success' => true, 'message' => "Account created successfully! Please log in."]);
        exit;
    } catch (PDOException $e) {
        error_log("Registration API Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => "Registration failed. Please try again later."]);
        exit;
    }
}

/**
 * Return a fresh CSRF token for client forms.
 */
function handleCsrfToken() {
    echo json_encode(['success' => true, 'csrf_token' => generateCsrfToken()]);
    exit;
}

/**
 * Helper to handle both JSON and Form redirects
 */
function redirectOrError($msg) {
    if (isset($_GET['format']) && $_GET['format'] === 'json') {
        echo json_encode(['success' => false, 'message' => $msg]);
    } else {
        header('Location: ../login/login.html?error=' . urlencode($msg));
    }
    exit;
}

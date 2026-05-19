/**
 * common/error-handler.php
 * Centralized error handling for consistent error responses
 */

function handleError($code, $message, $details = []) {
    http_response_code($code);
    
    $response = [
        'success' => false,
        'error' => [
            'code' => $code,
            'message' => $message
        ]
    ];
    
    if (!empty($details)) {
        $response['error']['details'] = $details;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function handleSuccess($data = [], $message = 'Success') {
    http_response_code(200);
    header('Content-Type: application/json');
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Set up global error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    
    if (getenv('APP_ENV') === 'production') {
        handleError(500, 'An error occurred. Please try again later.');
    } else {
        handleError(500, $errstr, ['file' => $errfile, 'line' => $errline]);
    }
});

set_exception_handler(function($exception) {
    error_log("Exception: " . $exception->getMessage());
    
    if (getenv('APP_ENV') === 'production') {
        handleError(500, 'An error occurred. Please try again later.');
    } else {
        handleError(500, $exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
    }
});

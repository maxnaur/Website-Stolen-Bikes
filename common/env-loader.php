<?php
/**
 * common/env-loader.php
 * Load environment variables from .env file for secure configuration
 */

function loadEnv($filePath = __DIR__ . '/../.env') {
    if (!file_exists($filePath)) {
        // Use .env.example as fallback if .env doesn't exist
        $filePath = __DIR__ . '/../.env.example';
        if (!file_exists($filePath)) {
            // If neither file exists, use hardcoded defaults (for backward compatibility)
            // In production, you should always have a .env file
            $_ENV['DB_SERVER'] = getenv('DB_SERVER');
            $_ENV['DB_USERNAME'] = getenv('DB_USERNAME');
            $_ENV['DB_PASSWORD'] = getenv('DB_PASSWORD');
            $_ENV['DB_NAME'] = getenv('DB_NAME');
            return;
        }
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return; // File read failed, continue with defaults
    }

    foreach ($lines as $line) {
        // Skip comments and empty lines (compatible with PHP 7.x)
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Skip empty keys
            if ($key === '') {
                continue;
            }

            // Remove quotes if present (compatible with PHP 7.x)
            $firstChar = substr($value, 0, 1);
            $lastChar = substr($value, -1);
            if (($firstChar === '"' && $lastChar === '"') ||
                ($firstChar === "'" && $lastChar === "'")) {
                $value = substr($value, 1, -1);
            }

            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Load environment variables
try {
    loadEnv();
} catch (Exception $e) {
    error_log("Environment loading error: " . $e->getMessage());
    // Continue with defaults if loading fails
}

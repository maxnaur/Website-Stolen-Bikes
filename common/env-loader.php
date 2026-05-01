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
            throw new Exception('.env file not found. Please copy .env.example to .env');
        }
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with($line, '#')) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Load environment variables
loadEnv();

<?php
/**
 * Load .env file and populate $_ENV
 */
function load_env($path) {
    if (!is_file($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Remove quotes
        $value = preg_replace('/^["\'](.*)["\']$/', '$1', $value);
        if (!isset($_ENV[$key])) {
            $_ENV[$key] = $value;
        }
    }
}

// Load .env from project root
$envFile = __DIR__ . '/../.env';
if (is_file($envFile)) {
    load_env($envFile);
}

function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Application
define('APP_NAME', env('APP_NAME', 'Face Attendance'));
define('APP_URL', rtrim(env('APP_URL', 'http://localhost/Face_Attendance/public'), '/'));
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', env('APP_DEBUG', 'false') === 'true');
define('BASE_PATH', dirname(__DIR__));

// Database
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'face_attendance'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', '123456'));
define('DB_CHARSET', 'utf8mb4');

// Session
define('SESSION_NAME', env('SESSION_NAME', 'face_att_sess'));
define('SESSION_LIFETIME', (int) env('SESSION_LIFETIME', '7200'));

// Face Recognition (defaults - overridden by DB settings)
define('FACE_MATCH_THRESHOLD', (float) env('FACE_MATCH_THRESHOLD', '0.55'));
define('FACE_MIN_CONFIDENCE', (float) env('FACE_MIN_CONFIDENCE', '0.45'));
define('FACE_MIN_FACE_SIZE', (int) env('FACE_MIN_FACE_SIZE', '80'));
define('FACE_MAX_FACE_SIZE', (int) env('FACE_MAX_FACE_SIZE', '420'));
define('FACE_MIN_BRIGHTNESS', (int) env('FACE_MIN_BRIGHTNESS', '40'));
define('FACE_MAX_BRIGHTNESS', (int) env('FACE_MAX_BRIGHTNESS', '220'));
define('FACE_MIN_SHARPNESS', (int) env('FACE_MIN_SHARPNESS', '20'));
define('FACE_DETECTION_CONFIDENCE', (float) env('FACE_DETECTION_CONFIDENCE', '0.50'));
define('FACE_STABLE_FRAMES', (int) env('FACE_STABLE_FRAMES', '3'));
define('REGISTRATION_SAMPLES', (int) env('REGISTRATION_SAMPLES', '3'));
define('DUPLICATE_SCAN_INTERVAL', (int) env('DUPLICATE_SCAN_INTERVAL', '300'));

// Uploads
define('UPLOAD_MAX_SIZE', (int) env('UPLOAD_MAX_SIZE', '5242880'));
define('UPLOAD_ALLOWED_EXT', array_map('trim', explode(',', env('UPLOAD_ALLOWED_EXT', 'jpg,jpeg,png,pdf'))));
define('UPLOAD_DIR', BASE_PATH . '/public/uploads/');

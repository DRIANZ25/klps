<?php
declare(strict_types=1);

// Start output buffering to prevent header errors
ob_start();

session_start();

// ===== SMART DETECTION - WORKS FOR BOTH LOCAL AND RAILWAY =====
$isRailway = getenv('RAILWAY_ENVIRONMENT') !== false || getenv('RAILWAY_SERVICE_ID') !== false;

if ($isRailway) {
    // ===== RAILWAY PRODUCTION SETTINGS =====
    define('DB_HOST', 'mysql.railway.internal');
    define('DB_NAME', 'klps');
    define('DB_USER', 'root');
    define('DB_PASS', 'EZzgkiwDylPxoiYXQWwzHjerpkRznJBO');
    define('ENVIRONMENT', 'production');
} else {
    // ===== LOCAL XAMPP SETTINGS =====
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'klps');
    define('DB_USER', 'root');
    define('DB_PASS', 'LanceAdrian1221'); // Your XAMPP MySQL password
    define('ENVIRONMENT', 'development');
}

// For debugging - shows environment
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// AI API settings
const AI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent';
const AI_API_KEY = 'AIzaSyCCKuXCwf_VEF6HxTezvmbVs2UIwX33p24';
const AI_MODEL = 'gemini-3.6-flash';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("<h1>Database Connection Error</h1><p>" . $e->getMessage() . "</p>");
        }
    }
    return $pdo;
}

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Location: ' . $url);
    exit;
}

function require_login(): void {
    if (empty($_SESSION['user'])) {
        redirect('login.php');
    }
}

function require_role(string $role): void {
    require_login();
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        exit('Forbidden');
    }
}

function current_user(): array {
    return $_SESSION['user'] ?? [];
}
?>
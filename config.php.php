<?php
/**
 * Global configuration file
 * Update the database credentials below to match your XAMPP or hosting setup.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
const DB_HOST = 'localhost';
const DB_NAME = 'cornerstone_restaurant';
const DB_USER = 'root';
const DB_PASS = '';

// Business information
const APP_NAME = 'Cornerstone Restaurant Mafubira';
const APP_TAGLINE = 'Fresh Meals • Fast Service • Great Taste';
const WHATSAPP_NUMBER = '256752064779';
const WHATSAPP_URL = 'https://wa.me/256752064779';
const TELEGRAM_URL = 'https://t.me/cornerstonerestaurant'; // Change to your real Telegram channel
const FACEBOOK_URL = 'https://facebook.com/';
const INSTAGRAM_URL = 'https://instagram.com/';
const TIKTOK_URL = 'https://tiktok.com/@';
const TWITTER_URL = 'https://x.com/';
const DEVELOPER_EMAIL = 'usamakamusa@gmail.com';
const DEVELOPER_PHONE = '+256752064779';

$currentPath = str_replace('\\', '/', dirname($_SERVER['PHP_SELF'] ?? ''));
$currentPath = preg_replace('#/admin$#', '', $currentPath ?? '');
$currentPath = ($currentPath === '/' || $currentPath === '\\') ? '' : rtrim((string) $currentPath, '/');
define('BASE_PATH_URL', $currentPath);

date_default_timezone_set('Africa/Kampala');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    die('Database connection failed. Please import the SQL file and update config.php. Error: ' . $exception->getMessage());
}

require_once __DIR__ . '/includes/functions.php';

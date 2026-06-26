<?php
/**
 * includes/db.example.php
 * ---------------------------------------------------------
 * Database configuration template for Aakar Creatives.
 *
 * 1. Copy this file and rename it to db.php
 * 2. Fill in your MySQL database credentials.
 * 3. Never commit db.php to GitHub.
 * ---------------------------------------------------------
 */

// Database Configuration
define('DB_HOST', 'localhost');          // e.g. localhost
define('DB_NAME', 'your_database_name'); // e.g. aakar_db
define('DB_USER', 'your_database_user'); // e.g. root
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    DB_HOST,
    DB_NAME,
    DB_CHARSET
);

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Please check your configuration.');
}
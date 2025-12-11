<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Load Composer dependencies

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // Load .env from root
$dotenv->safeLoad(); // Use safeLoad() to prevent errors if .env is missing

try {
    $pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4", $_ENV['DB_USER'], $_ENV['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed. Please contact salemtec for support.");
}
?>
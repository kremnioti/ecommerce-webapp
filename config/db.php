<?php
/**
 * Database connection (PDO).
 * Default XAMPP: user root, empty password, database ecommerce_mvp.
 */
$dbHost = 'localhost';
$dbName = 'ecommerce_mvp';
$dbUser = 'root';
$dbPass = '';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // Friendly message for local development
    die('Database connection failed. Import database.sql and check config/db.php.');
}

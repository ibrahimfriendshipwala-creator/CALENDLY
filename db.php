<?php
// Database connection (use these credentials as provided)
$DB_HOST = 'localhost';
$DB_NAME = 'dbyyz0lfgd3vov';
$DB_USER = 'up0ghncfmfakv';
$DB_PASS = 'vznwqmh2glra';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    die('Database connection error: ' . $e->getMessage());
}
?>

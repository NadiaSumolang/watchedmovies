<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'watched_movies');

// Create connection
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Base URL
define('BASE_URL', 'http://localhost/watched-movies');

// Upload directory - pastikan path ini benar
$uploadPath = __DIR__ . '/../uploads/';

// Buat direktori upload jika belum ada
if (!file_exists($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}

define('UPLOAD_DIR', $uploadPath);
define('UPLOAD_URL', BASE_URL . '/uploads/');
?>
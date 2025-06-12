<?php
require_once 'includes/config.php';
require_once 'includes/auth_check.php';

if(!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "/pages/dashboard.php");
    exit;
}

$movie_id = $_GET['id'];

// Cek apakah film milik user yang login
$stmt = $conn->prepare("SELECT id, poster FROM movies WHERE id = ? AND user_id = ?");
$stmt->execute([$movie_id, $_SESSION['user_id']]);
$movie = $stmt->fetch();

if(!$movie) {
    $_SESSION['message'] = "Film tidak ditemukan atau Anda tidak memiliki akses";
    $_SESSION['message_type'] = "error";
    header("Location: " . BASE_URL . "/pages/dashboard.php");
    exit;
}

// Hapus poster jika ada
if($movie['poster'] && file_exists(UPLOAD_DIR . $movie['poster'])) {
    unlink(UPLOAD_DIR . $movie['poster']);
}

// Hapus film
$stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);

$_SESSION['message'] = "Film berhasil dihapus";
$_SESSION['message_type'] = "success";
header("Location: " . BASE_URL . "/pages/dashboard.php");
exit;
?>
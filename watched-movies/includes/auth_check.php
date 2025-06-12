<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Silakan login terlebih dahulu";
    $_SESSION['message_type'] = "error";
    header("Location: " . BASE_URL . "/pages/login.php");
    exit;
}
?>
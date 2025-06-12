<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Daftar Film'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css">
</head>
<body>
    <header class="header">
    <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>" class="logo">FilmKu</a>
        <div class="nav-links">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="<?php echo BASE_URL; ?>/pages/dashboard.php">Dashboard</a>
            <a href="<?php echo BASE_URL; ?>/pages/add_movie.php">Tambah Film</a>
            <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/pages/login.php">Login</a>
            <a href="<?php echo BASE_URL; ?>/pages/register.php">Register</a>
        <?php endif; ?>
        </div>
    </nav>
    </header>
    
    <main class="main-content">
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>
<?php
require_once '../includes/config.php';

if(isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/pages/dashboard.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validasi
    $errors = [];
    
    if(empty($username)) {
        $errors[] = "Username harus diisi";
    }
    
    if(empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if(empty($password)) {
        $errors[] = "Password harus diisi";
    } elseif(strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    } elseif($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak cocok";
    }
    
    // Cek username/email sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if($stmt->rowCount() > 0) {
        $errors[] = "Username atau email sudah digunakan";
    }
    
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        
        $_SESSION['message'] = "Registrasi berhasil! Silakan login";
        $_SESSION['message_type'] = "success";
        header("Location: " . BASE_URL . "/pages/login.php");
        exit;
    }
}

$pageTitle = "Register";
include '../includes/header.php';
?>

<div class="auth-container">
    <h1 class="auth-title">Daftar Akun</h1>
    
    <?php if(!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
    </form>
    
    <p class="text-center mt-3">Sudah punya akun? <a href="<?php echo BASE_URL; ?>/pages/login.php" class="link">Login disini</a></p>
</div>

<?php include '../includes/footer.php'; ?>
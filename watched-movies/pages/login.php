<?php
require_once '../includes/config.php';

if(isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/pages/dashboard.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        $_SESSION['message'] = "Login berhasil! Selamat datang, " . $user['username'];
        $_SESSION['message_type'] = "success";
        header("Location: " . BASE_URL . "/pages/dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah";
    }
}

$pageTitle = "Login";
include '../includes/header.php';
?>

<div class="auth-container">
    <h1 class="auth-title">Login</h1>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    
    <p class="text-center mt-3">Belum punya akun? <a href="<?php echo BASE_URL; ?>/pages/register.php" class="link">Daftar disini</a></p>
</div>

<?php include '../includes/footer.php'; ?>
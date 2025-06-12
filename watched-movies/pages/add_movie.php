<?php
require_once '../includes/auth_check.php';

$pageTitle = "Tambah Film";
include '../includes/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $year = trim($_POST['year']);
    $genre = trim($_POST['genre']);
    $rating = trim($_POST['rating']);
    $review = trim($_POST['review']);
    $watched_date = trim($_POST['watched_date']);
    $poster = null;
    
    // Handle file upload
    if(isset($_FILES['poster'])) {
        echo '<pre>';
        print_r($_FILES['poster']);
        echo 'Upload Dir: ' . UPLOAD_DIR . "\n";
        echo 'Is Writable: ' . (is_writable(UPLOAD_DIR) ? 'Yes' : 'No') . "\n";
        echo '</pre>';
    }

    if(isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = $_FILES['poster']['type'];
        $file_size = $_FILES['poster']['size'];
        $file_ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
        
        // Validasi tipe file dan ukuran (max 2MB)
        if(in_array($file_type, $allowed_types) && $file_size < 2097152) {
            $filename = uniqid('', true) . '.' . $file_ext;
            $destination = UPLOAD_DIR . $filename;
            
            if(move_uploaded_file($_FILES['poster']['tmp_name'], $destination)) {
                $poster = $filename;
            } else {
                $errors[] = "Gagal mengupload file. Pastikan direktori upload dapat ditulisi.";
            }
        } else {
            $errors[] = "File harus berupa JPEG atau PNG dan ukuran maksimal 2MB";
        }
    }
    
    // Validasi
    $errors = [];
    
    if(empty($title)) {
        $errors[] = "Judul film harus diisi";
    }
    
    if(empty($year)) {
        $errors[] = "Tahun rilis harus diisi";
    } elseif(!is_numeric($year) || $year < 1900 || $year > date('Y')) {
        $errors[] = "Tahun rilis tidak valid";
    }
    
    if(empty($rating)) {
        $errors[] = "Rating harus diisi";
    } elseif(!is_numeric($rating) || $rating < 0 || $rating > 10) {
        $errors[] = "Rating harus antara 0-10";
    }
    
    if(empty($watched_date)) {
        $errors[] = "Tanggal menonton harus diisi";
    }
    
    if(empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO movies (user_id, title, year, genre, rating, review, watched_date, poster) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $year,
            $genre,
            $rating,
            $review,
            $watched_date,
            $poster
        ]);
        
        $_SESSION['message'] = "Film berhasil ditambahkan";
        $_SESSION['message_type'] = "success";
        header("Location: " . BASE_URL . "/pages/dashboard.php");
        exit;
    }
}
?>

<div class="form-card">
    <h1 class="page-title">Tambah Film Baru</h1>
    
    <?php if(!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Judul Film</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="year">Tahun Rilis</label>
                <input type="number" id="year" name="year" class="form-control" min="1900" max="<?php echo date('Y'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="rating">Rating (0-10)</label>
                <input type="number" id="rating" name="rating" class="form-control" min="0" max="10" step="0.1" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="genre">Genre</label>
            <select id="genre" name="genre" class="form-control">
                <option value="">Pilih Genre</option>
                <option value="Action">Action</option>
                <option value="Adventure">Adventure</option>
                <option value="Comedy">Comedy</option>
                <option value="Drama">Drama</option>
                <option value="Horror">Horror</option>
                <option value="Sci-Fi">Sci-Fi</option>
                <option value="Thriller">Thriller</option>
                <option value="Romance">Romance</option>
                <option value="Animation">Animation</option>
                <option value="Documentary">Documentary</option>
                <option value="Fantasy">Fantasy</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="poster">Poster Film (Opsional)</label>
            <input type="file" id="poster" name="poster" class="form-control-file" accept="image/jpeg, image/png">
            <small class="text-muted">Format: JPEG/PNG, Maksimal 2MB</small>
        </div>
        
        <div class="form-group">
            <label for="watched_date">Tanggal Menonton</label>
            <input type="date" id="watched_date" name="watched_date" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="review">Review</label>
            <textarea id="review" name="review" class="form-control" rows="5"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Simpan Film</button>
        <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="btn btn-secondary btn-block" style="margin-top: 1rem;">Batal</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
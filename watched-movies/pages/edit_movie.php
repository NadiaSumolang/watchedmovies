<?php
require_once '../includes/auth_check.php';

if(!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "/pages/dashboard.php");
    exit;
}

$movie_id = $_GET['id'];

// Cek apakah film milik user yang login
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ? AND user_id = ?");
$stmt->execute([$movie_id, $_SESSION['user_id']]);
$movie = $stmt->fetch();

if(!$movie) {
    $_SESSION['message'] = "Film tidak ditemukan atau Anda tidak memiliki akses";
    $_SESSION['message_type'] = "error";
    header("Location: " . BASE_URL . "/pages/dashboard.php");
    exit;
}

$pageTitle = "Edit Film";
include '../includes/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $year = trim($_POST['year']);
    $genre = trim($_POST['genre']);
    $rating = trim($_POST['rating']);
    $review = trim($_POST['review']);
    $watched_date = trim($_POST['watched_date']);
    $poster = $movie['poster']; // Default ke poster lama
    
    // Handle file upload
    if(isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['poster']['type'];
        
        if(in_array($file_type, $allowed_types)) {
            // Hapus poster lama jika ada
            if($poster && file_exists(UPLOAD_DIR . $poster)) {
                unlink(UPLOAD_DIR . $poster);
            }
            
            $extension = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = UPLOAD_DIR . $filename;
            
            if(move_uploaded_file($_FILES['poster']['tmp_name'], $destination)) {
                $poster = $filename;
            }
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
        $stmt = $conn->prepare("UPDATE movies SET title = ?, year = ?, genre = ?, rating = ?, review = ?, watched_date = ?, poster = ? WHERE id = ?");
        $stmt->execute([
            $title,
            $year,
            $genre,
            $rating,
            $review,
            $watched_date,
            $poster,
            $movie_id
        ]);
        
        $_SESSION['message'] = "Film berhasil diperbarui";
        $_SESSION['message_type'] = "success";
        header("Location: " . BASE_URL . "/pages/dashboard.php");
        exit;
    }
}
?>

<div class="form-card">
    <h1 class="page-title">Edit Film</h1>
    
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
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="year">Tahun Rilis</label>
                <input type="number" id="year" name="year" class="form-control" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo $movie['year']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="rating">Rating (0-10)</label>
                <input type="number" id="rating" name="rating" class="form-control" min="0" max="10" step="0.1" value="<?php echo $movie['rating']; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="genre">Genre</label>
            <select id="genre" name="genre" class="form-control">
                <option value="">Pilih Genre</option>
                <option value="Action" <?php echo $movie['genre'] == 'Action' ? 'selected' : ''; ?>>Action</option>
                <option value="Adventure" <?php echo $movie['genre'] == 'Adventure' ? 'selected' : ''; ?>>Adventure</option>
                <option value="Comedy" <?php echo $movie['genre'] == 'Comedy' ? 'selected' : ''; ?>>Comedy</option>
                <option value="Drama" <?php echo $movie['genre'] == 'Drama' ? 'selected' : ''; ?>>Drama</option>
                <option value="Horror" <?php echo $movie['genre'] == 'Horror' ? 'selected' : ''; ?>>Horror</option>
                <option value="Sci-Fi" <?php echo $movie['genre'] == 'Sci-Fi' ? 'selected' : ''; ?>>Sci-Fi</option>
                <option value="Thriller" <?php echo $movie['genre'] == 'Thriller' ? 'selected' : ''; ?>>Thriller</option>
                <option value="Romance" <?php echo $movie['genre'] == 'Romance' ? 'selected' : ''; ?>>Romance</option>
                <option value="Animation" <?php echo $movie['genre'] == 'Animation' ? 'selected' : ''; ?>>Animation</option>
                <option value="Documentary" <?php echo $movie['genre'] == 'Documentary' ? 'selected' : ''; ?>>Documentary</option>
                <option value="Fantasy" <?php echo $movie['genre'] == 'Fantasy' ? 'selected' : ''; ?>>Fantasy</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="poster">Poster Film (Opsional)</label>
            <?php if($movie['poster']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($movie['poster']); ?>" alt="Current Poster" style="max-height: 100px; margin-bottom: 10px; display: block;">
                    <label>
                        <input type="checkbox" name="remove_poster" value="1"> Hapus poster saat ini
                    </label>
                </div>
            <?php endif; ?>
            <input type="file" id="poster" name="poster" class="form-control-file" accept="image/*">
        </div>
        
        <div class="form-group">
            <label for="watched_date">Tanggal Menonton</label>
            <input type="date" id="watched_date" name="watched_date" class="form-control" value="<?php echo $movie['watched_date']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="review">Review</label>
            <textarea id="review" name="review" class="form-control" rows="5"><?php echo htmlspecialchars($movie['review']); ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Update Film</button>
        <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="btn btn-secondary btn-block" style="margin-top: 1rem;">Batal</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
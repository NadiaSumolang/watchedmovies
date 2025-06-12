<?php
require_once '../includes/auth_check.php';

$pageTitle = "Dashboard";
include '../includes/header.php';

// Get movies list
$stmt = $conn->prepare("SELECT * FROM movies WHERE user_id = ? ORDER BY watched_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$movies = $stmt->fetchAll();
?>

<h1 class="page-title">Daftar Film yang Sudah Ditonton</h1>

<a href="<?php echo BASE_URL; ?>/pages/add_movie.php" class="btn btn-primary">Tambah Film</a>

<div class="horizontal-scroll-container">
    <div class="horizontal-movies-list">
        <?php if(!empty($movies)): ?>
            <?php foreach($movies as $movie): ?>
                <div class="horizontal-movie-card">
                    <?php if($movie['poster']): ?>
                        <div class="movie-poster">
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($movie['poster']); ?>" 
                                 alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                 onerror="this.src='<?php echo BASE_URL; ?>/assets/default-poster.jpg'">
                        </div>
                    <?php else: ?>
                        <div class="movie-poster">
                            <img src="<?php echo BASE_URL; ?>/assets/default-poster.jpg" 
                                 alt="Tidak ada poster">
                        </div>
                    <?php endif; ?>
                    
                    <div class="movie-header">
                        <h2 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h2>
                        <div class="movie-meta">
                            <span><?php echo $movie['year']; ?></span>
                        </div>
                        <span class="genre-badge"><?php echo htmlspecialchars($movie['genre']); ?></span>
                    </div>
                    
                    <div class="movie-body">
                        <div class="rating">
                            <div class="rating-stars">
                                <?php 
                                $fullStars = floor($movie['rating']);
                                $halfStar = ($movie['rating'] - $fullStars) >= 0.5 ? 1 : 0;
                                
                                for($i = 1; $i <= 5; $i++) {
                                    if($i <= $fullStars) {
                                        echo '★';
                                    } elseif($i == $fullStars + 1 && $halfStar) {
                                        echo '½';
                                    } else {
                                        echo '☆';
                                    }
                                }
                                ?>
                            </div>
                            <span class="rating-value"><?php echo number_format($movie['rating'], 1); ?></span>
                        </div>
                        
                        <p class="movie-review"><?php echo nl2br(htmlspecialchars($movie['review'])); ?></p>
                    </div>
                    
                    <div class="movie-footer">
                        <small>Ditonton: <?php echo date('d M Y', strtotime($movie['watched_date'])); ?></small>
                        <div>
                            <a href="<?php echo BASE_URL; ?>/pages/edit_movie.php?id=<?php echo $movie['id']; ?>" 
                               class="btn btn-edit">Edit</a>
                            <a href="<?php echo BASE_URL; ?>/delete_movie.php?id=<?php echo $movie['id']; ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus film ini?')">Hapus</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Belum ada film yang ditambahkan. <a href="<?php echo BASE_URL; ?>/pages/add_movie.php" class="link">Tambahkan film pertama Anda</a></p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
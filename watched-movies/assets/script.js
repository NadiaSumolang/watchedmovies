/**
 * Script utama untuk aplikasi FilmKu
 * Berisi fungsi-fungsi interaktif dan utilitas
 */

document.addEventListener('DOMContentLoaded', function() {
    // Animasi untuk tombol dan card
    animateElements();
    
    // Validasi form tambah/edit film
    setupMovieFormValidation();
    
    // Fitur pencarian (jika nanti ditambahkan)
    setupSearchFeature();
    
    // Toast notification
    showNotifications();
});

/**
 * Animasi elemen-elemen interaktif
 */
function animateElements() {
    const interactiveElements = document.querySelectorAll('.btn, .movie-card, .nav-links a');
    
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });
}

/**
 * Validasi form film sebelum submit
 */
function setupMovieFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Validasi khusus untuk form film
            if (this.id === 'movie-form') {
                const title = this.querySelector('#title').value.trim();
                const year = this.querySelector('#year').value.trim();
                const rating = this.querySelector('#rating').value.trim();
                const watchedDate = this.querySelector('#watched_date').value.trim();
                
                if (!title) {
                    e.preventDefault();
                    alert('Judul film harus diisi');
                    return false;
                }
                
                if (!year || year < 1900 || year > new Date().getFullYear()) {
                    e.preventDefault();
                    alert('Tahun rilis tidak valid');
                    return false;
                }
                
                if (!rating || rating < 0 || rating > 10) {
                    e.preventDefault();
                    alert('Rating harus antara 0-10');
                    return false;
                }
                
                if (!watchedDate) {
                    e.preventDefault();
                    alert('Tanggal menonton harus diisi');
                    return false;
                }
            }
            
            return true;
        });
    });
}

/**
 * Setup pencarian film (untuk pengembangan)
 */
function setupSearchFeature() {
    const searchInput = document.getElementById('search-input');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const movieCards = document.querySelectorAll('.movie-card');
            
            movieCards.forEach(card => {
                const title = card.querySelector('.movie-title').textContent.toLowerCase();
                const director = card.querySelector('.movie-meta span:first-child').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || director.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

/**
 * Menampilkan notifikasi dari session
 */
function showNotifications() {
    const notification = document.querySelector('.alert');
    
    if (notification) {
        // Auto-hide notifikasi setelah 5 detik
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
        
        // Tombol close untuk notifikasi
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.background = 'none';
        closeBtn.style.border = 'none';
        closeBtn.style.fontSize = '1.2rem';
        closeBtn.style.position = 'absolute';
        closeBtn.style.right = '10px';
        closeBtn.style.top = '5px';
        closeBtn.style.cursor = 'pointer';
        
        closeBtn.addEventListener('click', () => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
        
        notification.style.position = 'relative';
        notification.appendChild(closeBtn);
    }
}

/**
 * Format tanggal untuk ditampilkan
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

/**
 * Konfirmasi sebelum menghapus
 */
function confirmDelete(e) {
    if (!confirm('Apakah Anda yakin ingin menghapus film ini?')) {
        e.preventDefault();
    }
}

// Fungsi untuk menampilkan rating dalam bintang
function renderRatingStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let stars = '';
    
    for (let i = 1; i <= 5; i++) {
        if (i <= fullStars) {
            stars += '★';
        } else if (i === fullStars + 1 && hasHalfStar) {
            stars += '½';
        } else {
            stars += '☆';
        }
    }
    
    return stars;
}
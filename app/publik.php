<?php
require_once __DIR__ . '/koneksi.php';

$result = $koneksi->query('SELECT id, nama_kamar, tipe, harga, status FROM kamar ORDER BY id ASC');
if (!$result) {
    die('Query gagal: ' . h($koneksi->error));
}

$rooms = $result->fetch_all(MYSQLI_ASSOC);
$totalKamar = count($rooms);
$totalAvailable = count(array_filter($rooms, fn ($room) => $room['status'] === 'Available'));
$hargaMulai = $rooms === [] ? 0 : min(array_map(fn ($room) => (int) $room['harga'], $rooms));

$videoPath = __DIR__ . '/assets/hero-video.mp4';
$hasVideo = file_exists($videoPath);

function roomImage(array $room): string
{
    $type = strtolower($room['tipe']);

    if (str_contains($type, 'family') || str_contains($type, 'superior') || str_contains($type, 'premium')) {
        return 'assets/superior-villa.jpg';
    }

    return 'assets/commercial-villa.jpg';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dafano Villa - Premium Stay</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="pv-body">
    <!-- Navbar -->
    <nav class="pv-navbar">
        <div class="pv-brand">
            <?php if (file_exists(__DIR__ . '/assets/logo-dafano-villa.jpg')): ?>
                <img src="assets/logo-dafano-villa.jpg" alt="Logo Dafano Villa">
            <?php endif; ?>
            <span>Dafano Villa</span>
        </div>
        <div class="pv-nav-links">
            <a href="#">Beranda</a>
            <a href="#kamar">Kamar</a>
            <a href="#fasilitas">Fasilitas</a>
            <a href="index.php" class="pv-btn pv-btn-outline">Admin Panel</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pv-hero">
        <?php if ($hasVideo): ?>
            <video autoplay loop muted playsinline class="pv-hero-bg">
                <source src="assets/hero-video.mp4" type="video/mp4">
            </video>
        <?php else: ?>
            <div class="pv-hero-bg" style="background-image: url('assets/hero-1.jpg');"></div>
        <?php endif; ?>
        <div class="pv-hero-overlay"></div>
        
        <div class="pv-hero-content">
            <p class="pv-hero-eyebrow">Villa Room Catalog</p>
            <h1>Stay in Comfort,<br>Surrounded by Nature</h1>
            <p>Katalog kamar villa modern yang terhubung langsung dengan database MariaDB.</p>
            <div class="pv-hero-actions">
                <a href="#kamar" class="pv-btn pv-btn-primary">Lihat Kamar</a>
                <a href="index.php" class="pv-btn pv-btn-glass">Masuk Admin Panel</a>
            </div>
        </div>

        <div class="pv-hero-card">
            <span class="pv-hc-label">Mulai dari</span>
            <strong class="pv-hc-price"><?= rupiah($hargaMulai) ?></strong>
            <p class="pv-hc-desc"><?= $totalAvailable ?> kamar tersedia</p>
            <div class="pv-hc-author">Dikelola oleh Muhamad Sari Rizki - 2301010008</div>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="pv-stats">
        <div class="pv-stat-item">
            <h3><?= $totalKamar ?></h3>
            <p>Total Kamar</p>
        </div>
        <div class="pv-stat-item">
            <h3><?= $totalAvailable ?></h3>
            <p>Kamar Tersedia</p>
        </div>
        <div class="pv-stat-item">
            <h3>24/7</h3>
            <p>Monitoring Status</p>
        </div>
        <div class="pv-stat-item">
            <h3>Realtime</h3>
            <p>Database Sync</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="pv-about">
        <div class="pv-about-text">
            <h2>A calm private stay for your best escape</h2>
            <p>Nikmati ketenangan sejati di Dafano Villa. Desain elegan yang memadukan keindahan alam dengan kenyamanan modern, menciptakan harmoni sempurna untuk liburan Anda. Pengalaman menginap yang eksklusif, private, dan tak terlupakan.</p>
        </div>
        <div class="pv-about-images">
            <img src="assets/commercial-villa.jpg" alt="Villa View 1" onerror="this.style.display='none'">
            <img src="assets/superior-villa.jpg" alt="Villa View 2" onerror="this.style.display='none'">
        </div>
    </section>

    <!-- Catalog Section -->
    <section id="kamar" class="pv-catalog">
        <div class="pv-section-header">
            <p class="pv-section-eyebrow">Public View</p>
            <h2>Katalog Kamar</h2>
            <p>Pengunjung hanya melihat informasi kamar. Tambah, edit, dan hapus data tetap dipisahkan di admin panel.</p>
        </div>

        <div class="pv-room-grid">
            <?php if ($rooms === []): ?>
                <div class="pv-empty">Belum ada data kamar yang dapat ditampilkan.</div>
            <?php endif; ?>

            <?php foreach ($rooms as $room): ?>
                <div class="pv-room-card">
                    <div class="pv-room-img">
                        <img src="<?= h(roomImage($room)) ?>" alt="<?= h($room['nama_kamar']) ?>" onerror="this.style.display='none'">
                        <span class="status pv-badge <?= statusClass($room['status']) ?>"><?= h($room['status']) ?></span>
                    </div>
                    <div class="pv-room-info">
                        <span class="pv-room-type"><?= h($room['tipe']) ?></span>
                        <h3><?= h($room['nama_kamar']) ?></h3>
                        <p class="pv-room-unit">Unit #<?= str_pad((string) $room['id'], 3, '0', STR_PAD_LEFT) ?> tersedia dalam sistem reservasi villa.</p>
                        <div class="pv-room-bottom">
                            <div class="pv-room-price">
                                <strong><?= rupiah((int) $room['harga']) ?></strong>
                                <span>per malam</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Facilities -->
    <section id="fasilitas" class="pv-facilities">
        <div class="pv-section-header text-center">
            <p class="pv-section-eyebrow">Keunggulan</p>
            <h2>Fasilitas Premium</h2>
            <p>Kami memastikan kenyamanan maksimal dengan fasilitas terbaik.</p>
        </div>
        <div class="pv-fac-grid">
            <div class="pv-fac-item">
                <div class="pv-fac-icon">01</div>
                <h4>Private Villa</h4>
                <p>Eksklusivitas tinggi untuk privasi Anda dan keluarga.</p>
            </div>
            <div class="pv-fac-item">
                <div class="pv-fac-icon">02</div>
                <h4>Garden View</h4>
                <p>Pemandangan taman asri yang menyegarkan mata.</p>
            </div>
            <div class="pv-fac-item">
                <div class="pv-fac-icon">03</div>
                <h4>Clean Room</h4>
                <p>Standar kebersihan hotel bintang 5 yang ketat.</p>
            </div>
            <div class="pv-fac-item">
                <div class="pv-fac-icon">04</div>
                <h4>Easy Management</h4>
                <p>Sistem reservasi dan pengelolaan yang cepat dan aman.</p>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="pv-cta">
        <div class="pv-cta-box">
            <h2>Kelola data kamar dari admin panel</h2>
            <p>Admin panel menyediakan fitur tambah, edit, hapus, dan akses database MariaDB untuk kebutuhan penilaian tugas. Data akan tersinkronisasi secara otomatis.</p>
            <a href="index.php" class="pv-btn pv-btn-primary pv-btn-light">Buka Admin Panel</a>
        </div>
    </section>
    
    <footer class="pv-footer">
        <p>&copy; <?= date('Y') ?> Dafano Villa. Dikelola oleh Muhamad Sari Rizki (2301010008).</p>
    </footer>
</body>
</html>

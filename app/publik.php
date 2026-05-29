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
$heroPath = __DIR__ . '/assets/hero-1.jpg';
$heroStyle = file_exists($heroPath) ? " style=\"background-image: linear-gradient(115deg, rgba(13,27,20,.86), rgba(13,27,20,.46) 52%, rgba(13,27,20,.12)), url('assets/hero-1.jpg');\"" : '';

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
    <title>Tampilan Publik Villa Rizki</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page-shell public-page">
        <nav class="topbar">
            <div class="brand">
                <?php if (file_exists(__DIR__ . '/assets/logo-dafano-villa.jpg')): ?>
                    <img src="assets/logo-dafano-villa.jpg" alt="Logo Dafano Villa">
                <?php endif; ?>
                <div>
                    <strong>Dafano Villa</strong>
                    <span>Guest room catalog</span>
                </div>
            </div>
            <div class="top-actions">
                <div class="view-switch" aria-label="Navigasi tampilan">
                    <a class="active" href="publik.php">Tampilan Publik</a>
                    <a href="index.php">Admin Panel</a>
                </div>
                <div class="identity">Rizki Villa</div>
            </div>
        </nav>

        <section class="public-hero"<?= $heroStyle ?>>
            <div class="public-hero-content">
                <p class="eyebrow">Villa Room Catalog</p>
                <h1>Pengalaman menginap yang rapi, nyaman, dan mudah dikelola.</h1>
                <p class="subtitle">Halaman ini adalah tampilan publik untuk tamu. Data kamar tetap diambil dari database MariaDB yang sama dengan admin panel.</p>
                <div class="hero-actions">
                    <a class="button primary" href="#kamar">Lihat Kamar</a>
                    <a class="button ghost" href="index.php">Masuk Admin Panel</a>
                </div>
            </div>
            <aside class="booking-card">
                <span>Mulai dari</span>
                <strong><?= rupiah($hargaMulai) ?></strong>
                <p><?= $totalAvailable ?> dari <?= $totalKamar ?> kamar siap dipesan</p>
            </aside>
        </section>

        <section class="public-strip">
            <article>
                <span><?= $totalKamar ?></span>
                <p>Total kamar tercatat</p>
            </article>
            <article>
                <span><?= $totalAvailable ?></span>
                <p>Kamar tersedia</p>
            </article>
            <article>
                <span>24/7</span>
                <p>Monitoring status kamar</p>
            </article>
        </section>

        <section class="public-section" id="kamar">
            <div class="section-heading">
                <div>
                    <p class="eyebrow dark">Public View</p>
                    <h2>Katalog Kamar Villa</h2>
                </div>
                <p>Pengunjung hanya melihat informasi kamar. Tambah, edit, dan hapus data tetap dipisahkan di admin panel.</p>
            </div>

            <div class="public-room-grid">
                <?php if ($rooms === []): ?>
                    <div class="empty-card">Belum ada data kamar yang dapat ditampilkan.</div>
                <?php endif; ?>

                <?php foreach ($rooms as $room): ?>
                    <article class="public-room-card">
                        <div class="room-photo">
                            <img src="<?= h(roomImage($room)) ?>" alt="<?= h($room['nama_kamar']) ?>" onerror="this.style.display='none'">
                            <span class="status <?= statusClass($room['status']) ?>"><?= h($room['status']) ?></span>
                        </div>
                        <div class="public-room-content">
                            <div>
                                <span class="type-pill"><?= h($room['tipe']) ?></span>
                                <h3><?= h($room['nama_kamar']) ?></h3>
                                <p>Unit #<?= str_pad((string) $room['id'], 3, '0', STR_PAD_LEFT) ?> tersedia dalam sistem reservasi villa.</p>
                            </div>
                            <div class="public-card-footer">
                                <strong><?= rupiah((int) $room['harga']) ?></strong>
                                <span>per malam</span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="public-admin-note">
            <div>
                <p class="eyebrow dark">Admin Area</p>
                <h2>Kelola data kamar dari panel terpisah.</h2>
                <p>Admin panel menyediakan fitur tambah, edit, hapus, dan akses phpMyAdmin untuk kebutuhan penilaian tugas.</p>
            </div>
            <a class="button primary" href="index.php">Buka Admin Panel</a>
        </section>
    </main>
</body>
</html>

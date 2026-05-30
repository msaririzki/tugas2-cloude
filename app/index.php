<?php
require_once __DIR__ . '/koneksi.php';

$result = $koneksi->query('SELECT id, nama_kamar, tipe, harga, status FROM kamar ORDER BY id ASC');
if (!$result) {
    die('Query gagal: ' . h($koneksi->error));
}

$rooms = $result->fetch_all(MYSQLI_ASSOC);
$totalKamar = count($rooms);
$totalAvailable = count(array_filter($rooms, fn ($room) => $room['status'] === 'Available'));
$totalCleaning = count(array_filter($rooms, fn ($room) => $room['status'] === 'Cleaning'));
$totalMaintenance = count(array_filter($rooms, fn ($room) => $room['status'] === 'Maintenance'));
$hargaTertinggi = $rooms === [] ? 0 : max(array_map(fn ($room) => (int) $room['harga'], $rooms));
$requestHost = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$phpMyAdminHost = str_replace([':18000', ':8000'], [':18001', ':8001'], $requestHost);
$phpMyAdminUrl = 'http://' . $phpMyAdminHost;
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel Dafano Villa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="adm-body">
    <!-- Navbar -->
    <nav class="adm-navbar">
        <div class="adm-brand">
            <?php if (file_exists(__DIR__ . '/assets/logo-dafano-villa.jpg')): ?>
                <img src="assets/logo-dafano-villa.jpg" alt="Logo Dafano Villa">
            <?php endif; ?>
            <div class="adm-brand-text">
                <strong>Dafano Villa Admin</strong>
                <span>Room Management Dashboard</span>
            </div>
        </div>
        <div class="adm-nav-links">
            <a href="publik.php">Tampilan Publik</a>
            <a href="index.php" class="adm-active">Admin Panel</a>
            <a href="<?= h($phpMyAdminUrl) ?>" target="_blank" rel="noreferrer">phpMyAdmin</a>
            <span class="adm-nim-badge">2301010008</span>
        </div>
    </nav>

    <main class="adm-container">
        <?php if (isset($_GET['pesan'])): ?>
            <div class="adm-alert adm-alert-success"><?= h($_GET['pesan']) ?></div>
        <?php endif; ?>

        <!-- Hero -->
        <section class="adm-hero">
            <div class="adm-hero-content">
                <span class="adm-kicker">Villa Operations Console</span>
                <h1>Admin Panel Kamar Villa</h1>
                <p>Kelola data kamar, harga, dan status operasional villa dari satu dashboard.</p>
                <div class="adm-hero-actions">
                    <a href="tambah.php" class="adm-btn adm-btn-primary">Tambah Kamar</a>
                </div>
            </div>
            <div class="adm-hero-identity">
                <span class="adm-id-label">Admin Identity</span>
                <strong>Muhamad Sari Rizki</strong>
                <span>2301010008</span>
                <small>Cloud Computing Final Project</small>
            </div>
        </section>

        <!-- Stats -->
        <section class="adm-stats">
            <div class="adm-stat-card">
                <div class="adm-stat-value"><?= $totalKamar ?></div>
                <div class="adm-stat-label">Total Kamar</div>
                <span class="adm-stat-note">Unit aktif di database</span>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value adm-text-green"><?= $totalAvailable ?></div>
                <div class="adm-stat-label">Available</div>
                <span class="adm-stat-note">Siap ditampilkan publik</span>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value adm-text-gold"><?= $totalCleaning ?></div>
                <div class="adm-stat-label">Cleaning</div>
                <span class="adm-stat-note">Dalam persiapan</span>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value adm-text-red"><?= $totalMaintenance ?></div>
                <div class="adm-stat-label">Maintenance</div>
                <span class="adm-stat-note">Perlu pengecekan</span>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value"><?= rupiah($hargaTertinggi) ?></div>
                <div class="adm-stat-label">Harga Tertinggi</div>
                <span class="adm-stat-note">Format rupiah otomatis</span>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value adm-text-small">villa_rizki_db</div>
                <div class="adm-stat-label">Database Aktif</div>
                <span class="adm-stat-note">MariaDB container</span>
            </div>
        </section>

        <section class="adm-stack">
            <div class="adm-stack-heading">
                <span class="adm-section-kicker">Containerized Stack</span>
                <h2>Arsitektur Service</h2>
                <p>Ringkasan service yang berjalan melalui Podman Compose untuk memenuhi kebutuhan deployment tugas.</p>
            </div>
            <div class="adm-stack-grid">
                <article>
                    <span class="adm-stack-port">:8000</span>
                    <strong>php-apache-rizki</strong>
                    <p>Menjalankan aplikasi PHP CRUD dan halaman publik villa.</p>
                </article>
                <article>
                    <span class="adm-stack-port">:3306</span>
                    <strong>mariadb-rizki</strong>
                    <p>Menyimpan tabel <code>kamar</code> dan data awal dari <code>init.sql</code>.</p>
                </article>
                <article>
                    <span class="adm-stack-port">:8001</span>
                    <strong>phpmyadmin-rizki</strong>
                    <p>Antarmuka database untuk validasi data melalui browser.</p>
                </article>
            </div>
        </section>

        <!-- Data Table -->
        <section class="adm-table-section">
            <div class="adm-table-header">
                <div>
                    <span class="adm-section-kicker">Live Room Inventory</span>
                    <h2>Daftar Kamar</h2>
                    <p>Data tersinkronisasi langsung dengan MariaDB</p>
                </div>
                <a href="tambah.php" class="adm-btn adm-btn-primary">Tambah Kamar</a>
            </div>
            <div class="adm-table-responsive">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Nama Kamar</th>
                            <th>Tipe</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rooms === []): ?>
                            <tr>
                                <td colspan="6" class="adm-empty">Belum ada data kamar.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td>
                                    <span class="adm-unit">Unit #<?= str_pad((string) $room['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td><strong><?= h($room['nama_kamar']) ?></strong></td>
                                <td><span class="adm-type-pill"><?= h($room['tipe']) ?></span></td>
                                <td><strong class="adm-price"><?= rupiah((int) $room['harga']) ?></strong></td>
                                <td>
                                    <span class="adm-badge <?= statusClass($room['status']) ?>"><?= h($room['status']) ?></span>
                                </td>
                                <td>
                                    <div class="adm-action-group">
                                        <a href="edit.php?id=<?= (int) $room['id'] ?>" class="adm-btn-action adm-btn-edit">Edit</a>
                                        <a href="hapus.php?id=<?= (int) $room['id'] ?>" class="adm-btn-action adm-btn-delete" onclick="return confirm('Hapus data kamar ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>

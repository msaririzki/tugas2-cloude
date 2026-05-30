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
            <a href="http://localhost:8001" target="_blank">phpMyAdmin</a>
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
                <h1>Admin Panel Kamar Villa</h1>
                <p>Kelola data kamar, harga, dan status operasional villa dari satu dashboard.</p>
                <div class="adm-hero-actions">
                    <a href="tambah.php" class="adm-btn adm-btn-primary">Tambah Kamar</a>
                    <a href="publik.php" class="adm-btn adm-btn-secondary">Lihat Tampilan Publik</a>
                    <a href="http://localhost:8001" target="_blank" class="adm-btn adm-btn-outline">Buka phpMyAdmin</a>
                </div>
            </div>
            <div class="adm-hero-identity">
                <span class="adm-id-label">Admin Identity</span>
                <strong>Muhamad Sari Rizki</strong>
                <span>2301010008</span>
            </div>
        </section>

        <!-- Stats -->
        <section class="adm-stats">
            <div class="adm-stat-card">
                <div class="adm-stat-value"><?= $totalKamar ?></div>
                <div class="adm-stat-label">Total Kamar</div>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value adm-text-green"><?= $totalAvailable ?></div>
                <div class="adm-stat-label">Available</div>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value adm-text-gold"><?= $totalCleaning ?></div>
                <div class="adm-stat-label">Cleaning</div>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value adm-text-red"><?= $totalMaintenance ?></div>
                <div class="adm-stat-label">Maintenance</div>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value"><?= rupiah($hargaTertinggi) ?></div>
                <div class="adm-stat-label">Harga Tertinggi</div>
            </div>
            <div class="adm-stat-card">
                <div class="adm-stat-value adm-text-small">villa_rizki_db</div>
                <div class="adm-stat-label">Database Aktif</div>
            </div>
        </section>

        <!-- Data Table -->
        <section class="adm-table-section">
            <div class="adm-table-header">
                <div>
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

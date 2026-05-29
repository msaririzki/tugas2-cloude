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
$heroPath = __DIR__ . '/assets/hero-1.jpg';
$heroStyle = file_exists($heroPath) ? " style=\"background-image: linear-gradient(115deg, rgba(13,27,20,.92), rgba(13,27,20,.56) 48%, rgba(13,27,20,.18)), url('assets/hero-1.jpg');\"" : '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD Data Kamar Villa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page-shell">
        <nav class="topbar">
            <div class="brand">
                <?php if (file_exists(__DIR__ . '/assets/logo-dafano-villa.jpg')): ?>
                    <img src="assets/logo-dafano-villa.jpg" alt="Logo Dafano Villa">
                <?php endif; ?>
                <div>
                    <strong>Dafano Villa Rooms</strong>
                    <span>CRUD PHP + MariaDB</span>
                </div>
            </div>
            <div class="identity">2301010008</div>
        </nav>

        <section class="hero"<?= $heroStyle ?>>
            <div class="hero-content">
                <p class="eyebrow">Tugas 2 Cloud Computing</p>
                <h1>CRUD Data Kamar Villa</h1>
                <p class="subtitle">Sistem sederhana untuk mengelola data kamar villa, dibuat dengan PHP, MariaDB, phpMyAdmin, dan Podman Compose.</p>
                <div class="hero-meta">
                    <span>PHP Apache</span>
                    <span>MariaDB</span>
                    <span>phpMyAdmin</span>
                    <span>Podman Compose</span>
                </div>
                <div class="hero-actions">
                    <a class="button primary" href="tambah.php">Tambah Kamar</a>
                    <a class="button ghost" href="http://localhost:8001" target="_blank" rel="noreferrer">Buka phpMyAdmin</a>
                </div>
                <div class="student-card">
                    <span>Mahasiswa</span>
                    <strong>Muhamad Sari Rizki</strong>
                    <em>2301010008</em>
                </div>
            </div>
            <div class="hero-stats">
                <div>
                    <span><?= $totalKamar ?></span>
                    <p>Total Kamar</p>
                </div>
                <div>
                    <span><?= $totalAvailable ?></span>
                    <p>Siap Dipesan</p>
                </div>
                <div>
                    <span><?= $totalCleaning ?></span>
                    <p>Cleaning</p>
                </div>
                <div>
                    <span><?= $totalMaintenance ?></span>
                    <p>Maintenance</p>
                </div>
            </div>
        </section>

        <section class="metrics">
            <article>
                <span class="metric-label">Database</span>
                <strong>villa_rizki_db</strong>
                <p>Tabel utama: kamar</p>
            </article>
            <article>
                <span class="metric-label">Harga Tertinggi</span>
                <strong><?= rupiah($hargaTertinggi) ?></strong>
                <p>Dihitung dari data MariaDB</p>
            </article>
            <article>
                <span class="metric-label">Service Web</span>
                <strong>php-apache-rizki</strong>
                <p>HTTP aktif pada port 8000</p>
            </article>
        </section>

        <?php if (isset($_GET['pesan'])): ?>
            <div class="notice"><?= h($_GET['pesan']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow dark">Data dari MariaDB</p>
                    <h2>Daftar Kamar</h2>
                    <p class="panel-copy">Seluruh baris di bawah ini dibaca langsung dari tabel <code>kamar</code> pada database <code>villa_rizki_db</code>.</p>
                </div>
                <a class="button primary small" href="tambah.php">Tambah Kamar</a>
            </div>

            <div class="room-grid">
                <article>
                    <img src="assets/commercial-villa.jpg" alt="Commercial Villa" onerror="this.style.display='none'">
                    <div>
                        <strong>Commercial Villa</strong>
                        <span>Referensi visual kamar utama</span>
                    </div>
                </article>
                <article>
                    <img src="assets/superior-villa.jpg" alt="Superior Villa" onerror="this.style.display='none'">
                    <div>
                        <strong>Superior Villa</strong>
                        <span>Referensi visual kamar premium</span>
                    </div>
                </article>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
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
                                <td colspan="6" class="empty">Belum ada data kamar.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><?= (int) $room['id'] ?></td>
                                <td>
                                    <div class="room-name">
                                        <strong><?= h($room['nama_kamar']) ?></strong>
                                        <span>Unit #<?= str_pad((string) $room['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                </td>
                                <td><span class="type-pill"><?= h($room['tipe']) ?></span></td>
                                <td><strong class="price"><?= rupiah((int) $room['harga']) ?></strong></td>
                                <td>
                                    <span class="status <?= statusClass($room['status']) ?>"><?= h($room['status']) ?></span>
                                </td>
                                <td class="actions">
                                    <a class="link edit" href="edit.php?id=<?= (int) $room['id'] ?>">Edit</a>
                                    <a class="link delete" href="hapus.php?id=<?= (int) $room['id'] ?>" onclick="return confirm('Hapus data kamar ini?')">Hapus</a>
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

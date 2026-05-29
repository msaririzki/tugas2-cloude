<?php
require_once __DIR__ . '/koneksi.php';

$result = $koneksi->query('SELECT id, nama_kamar, tipe, harga, status FROM kamar ORDER BY id ASC');
if (!$result) {
    die('Query gagal: ' . h($koneksi->error));
}

$rooms = $result->fetch_all(MYSQLI_ASSOC);
$totalKamar = count($rooms);
$totalAvailable = count(array_filter($rooms, fn ($room) => $room['status'] === 'Available'));
$heroPath = __DIR__ . '/assets/hero-1.jpg';
$heroStyle = file_exists($heroPath) ? " style=\"background-image: linear-gradient(90deg, rgba(13,27,20,.82), rgba(13,27,20,.38)), url('assets/hero-1.jpg');\"" : '';
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
        <section class="hero"<?= $heroStyle ?>>
            <div class="hero-content">
                <p class="eyebrow">Tugas 2 Cloud Computing</p>
                <h1>CRUD Data Kamar Villa</h1>
                <p class="subtitle">Muhamad Sari Rizki - 2301010008</p>
                <div class="hero-actions">
                    <a class="button primary" href="tambah.php">Tambah Kamar</a>
                    <a class="button ghost" href="http://localhost:8001" target="_blank" rel="noreferrer">Buka phpMyAdmin</a>
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
            </div>
        </section>

        <?php if (isset($_GET['pesan'])): ?>
            <div class="notice"><?= h($_GET['pesan']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow dark">Data dari MariaDB</p>
                    <h2>Daftar Kamar</h2>
                </div>
                <a class="button primary small" href="tambah.php">Tambah Kamar</a>
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
                                    <strong><?= h($room['nama_kamar']) ?></strong>
                                </td>
                                <td><?= h($room['tipe']) ?></td>
                                <td><?= rupiah((int) $room['harga']) ?></td>
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

<?php
require_once __DIR__ . '/koneksi.php';

$errors = [];
$data = [
    'nama_kamar' => '',
    'tipe' => '',
    'harga' => '',
    'status' => 'Available',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_kamar' => trim($_POST['nama_kamar'] ?? ''),
        'tipe' => trim($_POST['tipe'] ?? ''),
        'harga' => trim($_POST['harga'] ?? ''),
        'status' => $_POST['status'] ?? '',
    ];
    $errors = validasiKamar($data);

    if ($errors === []) {
        $stmt = $koneksi->prepare('INSERT INTO kamar (nama_kamar, tipe, harga, status) VALUES (?, ?, ?, ?)');
        $harga = (int) $data['harga'];
        $stmt->bind_param('ssis', $data['nama_kamar'], $data['tipe'], $harga, $data['status']);
        $stmt->execute();
        header('Location: index.php?pesan=Data kamar berhasil ditambahkan');
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kamar - Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="adm-body">
    <nav class="adm-navbar">
        <div class="adm-brand">
            <div class="adm-brand-text">
                <strong>Dafano Villa Admin</strong>
                <span>Tambah Kamar Baru</span>
            </div>
        </div>
        <div class="adm-nav-links">
            <a href="publik.php">Tampilan Publik</a>
            <a href="index.php">Admin Panel</a>
        </div>
    </nav>

    <main class="adm-container adm-form-container">
        <div class="adm-form-card">
            <div class="adm-form-header">
                <h2>Tambah Kamar Villa</h2>
                <p>Masukkan detail kamar baru ke dalam database MariaDB.</p>
            </div>

            <?php if ($errors !== []): ?>
                <div class="adm-alert adm-alert-error">
                    <?php foreach ($errors as $error): ?>
                        <div><?= h($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="adm-form">
                <div class="adm-form-group">
                    <label>Nama Kamar</label>
                    <input type="text" name="nama_kamar" value="<?= h($data['nama_kamar']) ?>" placeholder="Contoh: Standard Villa 08" required>
                    <span class="adm-helper-text">Gunakan penamaan yang seragam untuk mempermudah identifikasi.</span>
                </div>

                <div class="adm-form-group">
                    <label>Tipe Kamar</label>
                    <input type="text" name="tipe" value="<?= h($data['tipe']) ?>" placeholder="Contoh: Standard Villa" required>
                    <span class="adm-helper-text">Kategori tipe kamar.</span>
                </div>

                <div class="adm-form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" min="0" value="<?= h((string) $data['harga']) ?>" placeholder="450000" required>
                    <span class="adm-helper-text">Masukkan nominal angka saja tanpa titik atau koma.</span>
                </div>

                <div class="adm-form-group">
                    <label>Status Operasional</label>
                    <select name="status" required>
                        <?php foreach (['Available', 'Cleaning', 'Maintenance'] as $status): ?>
                            <option value="<?= $status ?>" <?= $data['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="adm-helper-text">Kondisi kamar saat ini.</span>
                </div>

                <div class="adm-form-actions">
                    <a href="index.php" class="adm-btn adm-btn-outline">Batal</a>
                    <button type="submit" class="adm-btn adm-btn-primary">Simpan Kamar</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

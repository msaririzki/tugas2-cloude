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
    <title>Tambah Kamar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="form-shell">
        <a class="back-link" href="index.php">Kembali ke daftar kamar</a>
        <section class="form-card">
            <div class="form-banner">Villa Room Management</div>
            <div class="form-heading">
                <p class="eyebrow dark">Create</p>
                <h1>Tambah Kamar Villa</h1>
                <p>Masukkan data kamar baru yang akan disimpan ke tabel <code>kamar</code>.</p>
            </div>

            <?php if ($errors !== []): ?>
                <div class="error-box">
                    <?php foreach ($errors as $error): ?>
                        <p><?= h($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-grid">
                    <label>
                        Nama Kamar
                        <input name="nama_kamar" value="<?= h($data['nama_kamar']) ?>" placeholder="Contoh: Deluxe Garden Villa" required>
                        <small>Gunakan nama unit yang mudah dikenali.</small>
                    </label>
                    <label>
                        Tipe
                        <input name="tipe" value="<?= h($data['tipe']) ?>" placeholder="Contoh: Deluxe" required>
                        <small>Contoh: Standard, Deluxe, Family, Premium.</small>
                    </label>
                    <label>
                        Harga
                        <input name="harga" type="number" min="0" value="<?= h((string) $data['harga']) ?>" placeholder="750000" required>
                        <small>Masukkan angka tanpa titik atau koma.</small>
                    </label>
                    <label>
                        Status
                        <select name="status" required>
                            <?php foreach (['Available', 'Cleaning', 'Maintenance'] as $status): ?>
                                <option value="<?= $status ?>" <?= $data['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small>Status operasional kamar saat ini.</small>
                    </label>
                </div>
                <div class="form-actions">
                    <a class="button secondary" href="index.php">Batal</a>
                    <button class="button primary" type="submit">Simpan Kamar</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>

<?php
require_once __DIR__ . '/koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php?pesan=ID kamar tidak valid');
    exit;
}

$stmt = $koneksi->prepare('SELECT id, nama_kamar, tipe, harga, status FROM kamar WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

if (!$room) {
    header('Location: index.php?pesan=Data kamar tidak ditemukan');
    exit;
}

$errors = [];
$data = $room;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_kamar' => trim($_POST['nama_kamar'] ?? ''),
        'tipe' => trim($_POST['tipe'] ?? ''),
        'harga' => trim($_POST['harga'] ?? ''),
        'status' => $_POST['status'] ?? '',
    ];
    $errors = validasiKamar($data);

    if ($errors === []) {
        $update = $koneksi->prepare('UPDATE kamar SET nama_kamar = ?, tipe = ?, harga = ?, status = ? WHERE id = ?');
        $harga = (int) $data['harga'];
        $update->bind_param('ssisi', $data['nama_kamar'], $data['tipe'], $harga, $data['status'], $id);
        $update->execute();
        header('Location: index.php?pesan=Data kamar berhasil diperbarui');
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Kamar - Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="adm-body">
    <nav class="adm-navbar">
        <div class="adm-brand">
            <div class="adm-brand-text">
                <strong>Dafano Villa Admin</strong>
                <span>Update Kamar</span>
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
                <h2>Edit Kamar Villa</h2>
                <p>Perbarui informasi untuk Unit #<?= str_pad((string) $id, 3, '0', STR_PAD_LEFT) ?>.</p>
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
                    <input type="text" name="nama_kamar" value="<?= h($data['nama_kamar']) ?>" required>
                    <span class="adm-helper-text">Nama kamar yang tampil pada daftar utama.</span>
                </div>

                <div class="adm-form-group">
                    <label>Tipe Kamar</label>
                    <input type="text" name="tipe" value="<?= h($data['tipe']) ?>" required>
                    <span class="adm-helper-text">Kategori tipe kamar.</span>
                </div>

                <div class="adm-form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" min="0" value="<?= h((string) $data['harga']) ?>" required>
                    <span class="adm-helper-text">Masukkan nominal angka saja tanpa titik atau koma.</span>
                </div>

                <div class="adm-form-group">
                    <label>Status Operasional</label>
                    <select name="status" required>
                        <?php foreach (['Available', 'Cleaning', 'Maintenance'] as $status): ?>
                            <option value="<?= $status ?>" <?= $data['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="adm-helper-text">Pilih status terbaru.</span>
                </div>

                <div class="adm-form-actions">
                    <a href="index.php" class="adm-btn adm-btn-outline">Batal</a>
                    <button type="submit" class="adm-btn adm-btn-primary">Update Kamar</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

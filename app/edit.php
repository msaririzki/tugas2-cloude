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
    <title>Edit Kamar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="form-shell">
        <a class="back-link" href="index.php">Kembali ke daftar kamar</a>
        <section class="form-card">
            <p class="eyebrow dark">Update</p>
            <h1>Edit Kamar Villa</h1>

            <?php if ($errors !== []): ?>
                <div class="error-box">
                    <?php foreach ($errors as $error): ?>
                        <p><?= h($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <label>
                    Nama Kamar
                    <input name="nama_kamar" value="<?= h($data['nama_kamar']) ?>" required>
                </label>
                <label>
                    Tipe
                    <input name="tipe" value="<?= h($data['tipe']) ?>" required>
                </label>
                <label>
                    Harga
                    <input name="harga" type="number" min="0" value="<?= h((string) $data['harga']) ?>" required>
                </label>
                <label>
                    Status
                    <select name="status" required>
                        <?php foreach (['Available', 'Cleaning', 'Maintenance'] as $status): ?>
                            <option value="<?= $status ?>" <?= $data['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="button primary" type="submit">Update Kamar</button>
            </form>
        </section>
    </main>
</body>
</html>

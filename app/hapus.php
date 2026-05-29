<?php
require_once __DIR__ . '/koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php?pesan=ID kamar tidak valid');
    exit;
}

$stmt = $koneksi->prepare('DELETE FROM kamar WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();

header('Location: index.php?pesan=Data kamar berhasil dihapus');
exit;

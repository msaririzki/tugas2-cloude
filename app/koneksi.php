<?php

$host = 'mariadb-rizki';
$user = 'villa_user';
$password = 'villa_pass';
$database = 'villa_rizki_db';

$koneksi = new mysqli($host, $user, $password, $database);

if ($koneksi->connect_error) {
    http_response_code(500);
    die('Koneksi database gagal: ' . htmlspecialchars($koneksi->connect_error));
}

$koneksi->set_charset('utf8mb4');

function rupiah(int $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function statusClass(string $status): string
{
    return match ($status) {
        'Available' => 'status-available',
        'Cleaning' => 'status-cleaning',
        'Maintenance' => 'status-maintenance',
        default => 'status-neutral',
    };
}

function validStatus(string $status): bool
{
    return in_array($status, ['Available', 'Cleaning', 'Maintenance'], true);
}

function validasiKamar(array $data): array
{
    $errors = [];

    if (trim($data['nama_kamar'] ?? '') === '') {
        $errors[] = 'Nama kamar wajib diisi.';
    }

    if (trim($data['tipe'] ?? '') === '') {
        $errors[] = 'Tipe kamar wajib diisi.';
    }

    if (!isset($data['harga']) || filter_var($data['harga'], FILTER_VALIDATE_INT) === false || (int) $data['harga'] < 0) {
        $errors[] = 'Harga harus berupa angka dan tidak boleh kurang dari 0.';
    }

    if (!validStatus($data['status'] ?? '')) {
        $errors[] = 'Status kamar tidak valid.';
    }

    return $errors;
}

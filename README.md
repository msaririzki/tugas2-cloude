# Tugas 2 Cloud Computing - CRUD Data Kamar Villa

Nama: Muhamad Sari Rizki  
NIM: 2301010008  
Topik: CRUD Data Kamar Villa  

## Service

- `php-apache-rizki` berjalan di port `8000`
- `mariadb-rizki` berjalan di port `3306`
- `phpmyadmin-rizki` berjalan di port `8001`

## Menjalankan Service

```bash
podman-compose up -d --build
podman ps -a
```

## Akses Aplikasi

- Admin CRUD: `http://localhost:8000`
- Tampilan publik: `http://localhost:8000/publik.php`
- phpMyAdmin: `http://localhost:8001`

Login phpMyAdmin:

- Server: `mariadb-rizki`
- Username: `villa_user`
- Password: `villa_pass`
- Database: `villa_rizki_db`

## Reset Data Awal

Gunakan ini jika ingin mengulang demo dari data awal `init.sql`:

```bash
podman-compose down -v
podman-compose up -d --build
```

## Menghentikan Service

```bash
podman-compose down
podman ps -a
```

CREATE DATABASE IF NOT EXISTS villa_rizki_db;

USE villa_rizki_db;

CREATE TABLE IF NOT EXISTS kamar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_kamar VARCHAR(100) NOT NULL,
  tipe VARCHAR(50) NOT NULL,
  harga INT NOT NULL,
  status ENUM('Available', 'Cleaning', 'Maintenance') NOT NULL DEFAULT 'Available'
);

INSERT INTO kamar (nama_kamar, tipe, harga, status) VALUES
('Deluxe Garden Villa', 'Deluxe', 750000, 'Available'),
('Family Pool Villa', 'Family', 1250000, 'Cleaning'),
('Standard Villa', 'Standard', 500000, 'Maintenance');

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
('Standard Villa 01', 'Standard Villa', 450000, 'Available'),
('Standard Villa 02', 'Standard Villa', 450000, 'Available'),
('Standard Villa 03', 'Standard Villa', 450000, 'Available'),
('Standard Villa 04', 'Standard Villa', 450000, 'Cleaning'),
('Standard Villa 05', 'Standard Villa', 450000, 'Cleaning'),
('Standard Villa 06', 'Standard Villa', 450000, 'Maintenance'),
('Standard Villa 07', 'Standard Villa', 450000, 'Available');

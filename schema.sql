-- PKL Hero Hub Database Schema

CREATE DATABASE IF NOT EXISTS pkl_hero_hub;
USE pkl_hero_hub;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('siswa', 'pembimbing', 'guru') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Journal Harian table
CREATE TABLE jurnal_harian (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_siswa INT NOT NULL,
    tanggal_kegiatan DATE NOT NULL,
    deskripsi_kegiatan TEXT NOT NULL,
    kendala TEXT,
    solusi TEXT,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    komentar_pembimbing TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_siswa) REFERENCES users(id) ON DELETE CASCADE
);

-- Galeri Tugas table
CREATE TABLE galeri_tugas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_siswa INT NOT NULL,
    judul_file VARCHAR(150) NOT NULL,
    path_file VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_siswa) REFERENCES users(id) ON DELETE CASCADE
);

-- Relasi Bimbingan table (Junction table for many-to-many relationship)
CREATE TABLE relasi_bimbingan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_siswa INT NOT NULL,
    id_pembimbing INT NOT NULL,
    FOREIGN KEY (id_siswa) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_pembimbing) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample data for testing
INSERT INTO users (nama_lengkap, email, password, role) VALUES
('Admin Guru', 'admin@guru.com', '$2y$10$GxnCBpXLXhWBmD9KVswtj.N1JzqCsjOrDD2PVOaSXTh3jg/qjraEq', 'guru'),
('Pembimbing A', 'pembimbing@example.com', '$2y$10$TkQqq1xR41Xls1cJI5yT6O7FbFW1z9RNzPODuYOxUEb2EoMlQyHGq', 'pembimbing'),
('Siswa Andi', 'andi@siswa.com', '$2y$10$XIzBm5.B5lRW5em1C/HUMOlUJpq8FvzdhHC6ILnvL.n70WQDATdbu', 'siswa'),
('Siswa Budi', 'budi@siswa.com', '$2y$10$P9h4ANHYME.fXl3tvc1lkex/BjXmxtlkS5POXQ0ESGAO4Gr4kJiai', 'siswa');

INSERT INTO relasi_bimbingan (id_siswa, id_pembimbing) VALUES
(3, 2), -- Andi with Pembimbing A
(4, 2); -- Budi with Pembimbing A

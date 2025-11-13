# PKL Hero Hub

Sistem manajemen jurnal PKL (Praktik Kerja Lapangan) dengan fitur untuk siswa, pembimbing, dan admin.

## Fitur Utama

- **Siswa**: Registrasi, membuat jurnal harian, upload galeri tugas, cetak laporan PDF
- **Pembimbing**: Melihat dan memvalidasi jurnal siswa yang dibimbing
- **Admin/Guru**: Mengelola user dan relasi bimbingan

## Setup

1. Import `schema.sql` ke database MySQL
2. Konfigurasi database di `config.php`
3. Jalankan di web server (XAMPP/Apache)

## Akun Testing

### Admin/Guru
- **Email**: admin@guru.com
- **Password**: admin123
- **Role**: guru (admin)

### Pembimbing
- **Email**: pembimbing@example.com
- **Password**: pembimbing123
- **Role**: pembimbing

### Siswa
- **Email**: andi@siswa.com
- **Password**: andi123
- **Role**: siswa

- **Email**: budi@siswa.com
- **Password**: budi123
- **Role**: siswa

## Struktur Database

- `users`: Data user dengan role siswa/pembimbing/guru
- `jurnal_harian`: Jurnal harian siswa
- `galeri_tugas`: File tugas siswa
- `relasi_bimbingan`: Relasi antara siswa dan pembimbing

## Teknologi

- **Backend**: PHP 7+
- **Database**: MySQL
- **Frontend**: HTML, Tailwind CSS
- **PDF Generation**: FPDF Library

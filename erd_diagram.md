# PKL Hero Hub Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    users {
        INT id PK "Auto Increment"
        VARCHAR(100) nama_lengkap "NOT NULL"
        VARCHAR(100) email "NOT NULL, UNIQUE"
        VARCHAR(255) password "NOT NULL"
        ENUM('siswa', 'pembimbing', 'guru') role "NOT NULL"
        TIMESTAMP created_at "Default: CURRENT_TIMESTAMP"
    }

    jurnal_harian {
        INT id PK "Auto Increment"
        INT id_siswa FK "NOT NULL"
        DATE tanggal_kegiatan "NOT NULL"
        TEXT deskripsi_kegiatan "NOT NULL"
        TEXT kendala "NULLable"
        TEXT solusi "NULLable"
        ENUM('pending', 'approved', 'rejected') status "NOT NULL, Default: 'pending'"
        TEXT komentar_pembimbing "NULLable"
        TIMESTAMP created_at "Default: CURRENT_TIMESTAMP"
    }

    galeri_tugas {
        INT id PK "Auto Increment"
        INT id_siswa FK "NOT NULL"
        VARCHAR(150) judul_file "NOT NULL"
        VARCHAR(255) path_file "NOT NULL"
        TEXT deskripsi "NULLable"
        TIMESTAMP uploaded_at "Default: CURRENT_TIMESTAMP"
    }

    relasi_bimbingan {
        INT id PK "Auto Increment"
        INT id_siswa FK "NOT NULL"
        INT id_pembimbing FK "NOT NULL"
    }

    users ||--o{ jurnal_harian : "One-to-Many (users.id to jurnal_harian.id_siswa)"
    users ||--o{ galeri_tugas : "One-to-Many (users.id to galeri_tugas.id_siswa)"
    users ||--o{ relasi_bimbingan : "Many-to-Many Bridge (users.id to relasi_bimbingan.id_siswa)"

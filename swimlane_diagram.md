# PKL Hero Hub Swimlane Diagram

```mermaid
graph TD
subgraph "Siswa (Student)"
  S1["Start at Homepage"]
  S2["Click 'Register'"]
  S3["Fill Registration Form (nama, email, password, role='Siswa')"]
  S4["Click 'Submit'"]
  S5["Start at 'Login Page'"]
  S6["Enter email and password"]
  S7["Click 'Login'"]
  S8["From 'Siswa Dashboard', click 'Buat Jurnal Baru'"]
  S9["Fill Jurnal Form (tanggal, deskripsi_kegiatan, kendala, solusi)"]
  S10["Click 'Simpan Jurnal'"]
  S11["Views updated Jurnal List"]
  S12["From 'Siswa Dashboard', click 'Cetak Laporan PDF'"]
  S13["Receives and saves PDF file"]
end

subgraph "Pembimbing (Supervisor)"
  P1["From 'Pembimbing Dashboard', views list of assigned Siswa"]
  P2["Clicks on specific Siswa's name (e.g., 'Andi')"]
  P3["Clicks on a Jurnal with 'Pending' status"]
  P4["Reviews the Jurnal details"]
  P5["Fills in 'Komentar' field and clicks 'Approve'"]
end

subgraph "System (Backend & Database)"
  SY1["Receives form data"]
  SY2["Validates data (e.g., email unique?)"]
  SY3["Hashes password (password_hash())"]
  SY4["Executes INSERT into users table"]
  SY5["Redirects to 'Login Page'"]
  SY6["Receives credentials"]
  SY7["Executes SELECT from users where email matches"]
  SY8{"User Exists?"}
  SY9["Display 'Invalid email' error on Login Page"]
  SY10["Verify password (password_verify())"]
  SY11{"Password Valid?"}
  SY12["Display 'Invalid password' error on Login Page"]
  SY13["Create User Session ($_SESSION['user_id'], $_SESSION['role'])"]
  SY14{"Role == 'Siswa'?"}
  SY15["Redirect to 'Siswa Dashboard'"]
  SY16["Redirect to 'Pembimbing Dashboard'"]
  SY17["Receives Jurnal data"]
  SY18["Executes INSERT into jurnal_harian (user_id, tanggal, deskripsi_kegiatan, status='Pending')"]
  SY19["Displays 'Jurnal berhasil disimpan'"]
  SY20["Receives request for Siswa's details"]
  SY21["Executes SELECT from jurnal_harian where user_id=Andi's ID, ORDER BY tanggal DESC"]
  SY22["Displays Jurnal List for 'Andi'"]
  SY23["Receives jurnal_id, komentar, 'Approve'"]
  SY24["Executes UPDATE jurnal_harian set status='Approved', komentar_pembimbing=komentar where id=jurnal_id"]
  SY25["Displays 'Jurnal successfully validated'"]
  SY26["Receives request"]
  SY27["Executes SELECT from jurnal_harian where user_id=Siswa's ID and status='Approved'"]
  SY28["Fetches all 'Approved' data"]
  SY29["Uses library (FPDF or DomPDF) to compile into PDF"]
  SY30["Generates Laporan_PKL.pdf"]
  SY31["Sends file to browser for download"]
end

%% Process 1: User Registration
S1 --> S2
S2 --> S3
S3 --> S4
S4 --> SY1
SY1 --> SY2
SY2 --> SY3
SY3 --> SY4
SY4 --> SY5

%% Process 2: User Login
S5 --> S6
S6 --> S7
S7 --> SY6
SY6 --> SY7
SY7 --> SY8
SY8 -->|No| SY9
SY9 --> S5
SY8 -->|Yes| SY10
SY10 --> SY11
SY11 -->|No| SY12
SY12 --> S5
SY11 -->|Yes| SY13
SY13 --> SY14
SY14 -->|Yes| SY15
SY14 -->|No| SY16

%% Process 3: Jurnal Creation
S8 --> S9
S9 --> S10
S10 --> SY17
SY17 --> SY18
SY18 --> SY19
SY19 --> S11

%% Process 4: Jurnal Validation
P1 --> P2
P2 --> SY20
SY20 --> SY21
SY21 --> SY22
SY22 --> P3
P3 --> P4
P4 --> P5
P5 --> SY23
SY23 --> SY24
SY24 --> SY25
SY25 --> SY22

%% Process 5: PDF Report Generation
S12 --> SY26
SY26 --> SY27
SY27 --> SY28
SY28 --> SY29
SY29 --> SY30
SY30 --> SY31
SY31 --> S13

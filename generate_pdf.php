<?php
require_once 'config.php';
redirect_if_not_logged_in();
redirect_if_not_role('siswa');

$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];

require_once 'vendor/fpdf/fpdf.php';

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Laporan PKL - ' . $nama, 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Get approved journals
$journals = [];
$stmt = $conn->prepare("SELECT * FROM jurnal_harian WHERE id_siswa = ? AND status = 'approved' ORDER BY tanggal_kegiatan ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $journals[] = $row;
}
$stmt->close();

if (empty($journals)) {
    echo "<script>alert('Tidak ada jurnal yang disetujui untuk dicetak.'); window.location.href='siswa_dashboard.php';</script>";
    exit();
}

// Create PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(0, 10, 'Nama Siswa: ' . $nama, 0, 1);
$pdf->Cell(0, 10, 'Periode: ' . date('d M Y', strtotime($journals[0]['tanggal_kegiatan'])) . ' - ' . date('d M Y', strtotime(end($journals)['tanggal_kegiatan'])), 0, 1);
$pdf->Ln(10);

foreach ($journals as $journal) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Tanggal: ' . date('d M Y', strtotime($journal['tanggal_kegiatan'])), 0, 1);
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 6, 'Deskripsi Kegiatan: ' . $journal['deskripsi_kegiatan']);
    $pdf->Ln(3);

    if (!empty($journal['kendala'])) {
        $pdf->MultiCell(0, 6, 'Kendala: ' . $journal['kendala']);
        $pdf->Ln(3);
    }

    if (!empty($journal['solusi'])) {
        $pdf->MultiCell(0, 6, 'Solusi: ' . $journal['solusi']);
        $pdf->Ln(3);
    }

    if (!empty($journal['komentar_pembimbing'])) {
        $pdf->MultiCell(0, 6, 'Komentar Pembimbing: ' . $journal['komentar_pembimbing']);
        $pdf->Ln(3);
    }

    $pdf->Ln(10);
}

// Output PDF
$filename = 'Laporan_PKL_' . str_replace(' ', '_', $nama) . '.pdf';
$pdf->Output('D', $filename);
?>

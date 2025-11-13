<?php
require_once 'config.php';
redirect_if_not_logged_in();
redirect_if_not_role('siswa');

$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];

// Handle journal creation
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_journal'])) {
    $tanggal = sanitize_input($_POST['tanggal']);
    $deskripsi = sanitize_input($_POST['deskripsi']);
    $kendala = sanitize_input($_POST['kendala']);
    $solusi = sanitize_input($_POST['solusi']);

    if (empty($tanggal) || empty($deskripsi)) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Tanggal dan deskripsi kegiatan harus diisi.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO jurnal_harian (id_siswa, tanggal_kegiatan, deskripsi_kegiatan, kendala, solusi) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $tanggal, $deskripsi, $kendala, $solusi);

        if ($stmt->execute()) {
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Jurnal berhasil disimpan!</div>';
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Terjadi kesalahan. Silakan coba lagi.</div>';
        }
        $stmt->close();
    }
}

// Get journals
$journals = [];
$stmt = $conn->prepare("SELECT * FROM jurnal_harian WHERE id_siswa = ? ORDER BY tanggal_kegiatan DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $journals[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Hero Hub - Dashboard Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>
                        PKL Hero Hub
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Selamat datang, <?php echo htmlspecialchars($nama); ?> (Siswa)</span>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Create Journal -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                        Buat Jurnal Baru
                    </h2>

                    <?php echo $message; ?>

                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="create_journal" value="1">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kegiatan</label>
                            <input type="date" name="tanggal" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kegiatan</label>
                            <textarea name="deskripsi" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Jelaskan kegiatan yang dilakukan..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kendala (Opsional)</label>
                            <textarea name="kendala" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Kendala yang dihadapi..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Solusi (Opsional)</label>
                            <textarea name="solusi" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Solusi yang diterapkan..."></textarea>
                        </div>

                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Jurnal
                        </button>
                    </form>
                </div>
            </div>

            <!-- Journal List -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-book text-green-600 mr-2"></i>
                            Jurnal Harian Saya
                        </h2>
                        <a href="generate_pdf.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-file-pdf mr-2"></i>Cetak Laporan PDF
                        </a>
                    </div>

                    <?php if (empty($journals)): ?>
                        <p class="text-gray-500 text-center py-8">Belum ada jurnal yang dibuat.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($journals as $journal): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-gray-800">
                                            <?php echo date('d M Y', strtotime($journal['tanggal_kegiatan'])); ?>
                                        </h3>
                                        <span class="px-2 py-1 text-xs rounded-full <?php
                                            if ($journal['status'] === 'approved') echo 'bg-green-100 text-green-800';
                                            elseif ($journal['status'] === 'rejected') echo 'bg-red-100 text-red-800';
                                            else echo 'bg-yellow-100 text-yellow-800';
                                        ?>">
                                            <?php echo ucfirst($journal['status']); ?>
                                        </span>
                                    </div>

                                    <p class="text-gray-700 mb-2"><?php echo nl2br(htmlspecialchars($journal['deskripsi_kegiatan'])); ?></p>

                                    <?php if (!empty($journal['kendala'])): ?>
                                        <p class="text-gray-600 mb-1"><strong>Kendala:</strong> <?php echo nl2br(htmlspecialchars($journal['kendala'])); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($journal['solusi'])): ?>
                                        <p class="text-gray-600 mb-1"><strong>Solusi:</strong> <?php echo nl2br(htmlspecialchars($journal['solusi'])); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($journal['komentar_pembimbing'])): ?>
                                        <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                            <p class="text-blue-800"><strong>Komentar Pembimbing:</strong></p>
                                            <p class="text-blue-700"><?php echo nl2br(htmlspecialchars($journal['komentar_pembimbing'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

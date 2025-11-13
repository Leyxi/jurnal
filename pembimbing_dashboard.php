<?php
require_once 'config.php';
redirect_if_not_logged_in();
redirect_if_not_role('pembimbing');

$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];

// Get assigned students
$students = [];
$stmt = $conn->prepare("
    SELECT u.id, u.nama_lengkap
    FROM users u
    INNER JOIN relasi_bimbingan rb ON u.id = rb.id_siswa
    WHERE rb.id_pembimbing = ?
    ORDER BY u.nama_lengkap
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

// Handle journal approval
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_journal'])) {
    $journal_id = (int)$_POST['journal_id'];
    $komentar = sanitize_input($_POST['komentar']);

    $stmt = $conn->prepare("UPDATE jurnal_harian SET status='approved', komentar_pembimbing=? WHERE id=?");
    $stmt->bind_param("si", $komentar, $journal_id);

    if ($stmt->execute()) {
        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Jurnal berhasil divalidasi!</div>';
    } else {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Terjadi kesalahan. Silakan coba lagi.</div>';
    }
    $stmt->close();
}

// Get selected student journals
$selected_student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;
$student_journals = [];
$selected_student_name = '';

if ($selected_student_id) {
    // Verify the student is assigned to this pembimbing
    $stmt = $conn->prepare("SELECT nama_lengkap FROM users u INNER JOIN relasi_bimbingan rb ON u.id = rb.id_siswa WHERE rb.id_pembimbing = ? AND u.id = ?");
    $stmt->bind_param("ii", $user_id, $selected_student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $selected_student_name = $result->fetch_assoc()['nama_lengkap'];

        // Get journals
        $stmt = $conn->prepare("SELECT * FROM jurnal_harian WHERE id_siswa = ? ORDER BY tanggal_kegiatan DESC");
        $stmt->bind_param("i", $selected_student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $student_journals[] = $row;
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Hero Hub - Dashboard Pembimbing</title>
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
                    <span class="text-gray-700">Selamat datang, <?php echo htmlspecialchars($nama); ?> (Pembimbing)</span>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php echo $message; ?>

        <div class="grid md:grid-cols-4 gap-8">
            <!-- Student List -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-users text-blue-600 mr-2"></i>
                        Siswa Bimbingan
                    </h2>

                    <?php if (empty($students)): ?>
                        <p class="text-gray-500">Belum ada siswa yang ditugaskan.</p>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($students as $student): ?>
                                <a href="?student_id=<?php echo $student['id']; ?>"
                                   class="block p-3 rounded-lg border <?php echo ($selected_student_id == $student['id']) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'; ?> transition-colors">
                                    <i class="fas fa-user mr-2 text-gray-600"></i>
                                    <?php echo htmlspecialchars($student['nama_lengkap']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Journal Validation -->
            <div class="md:col-span-3">
                <?php if (!$selected_student_id): ?>
                    <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                        <i class="fas fa-hand-pointer text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Pilih Siswa</h3>
                        <p class="text-gray-500">Klik pada nama siswa di sebelah kiri untuk melihat jurnal mereka.</p>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">
                            <i class="fas fa-book text-green-600 mr-2"></i>
                            Jurnal <?php echo htmlspecialchars($selected_student_name); ?>
                        </h2>

                        <?php if (empty($student_journals)): ?>
                            <p class="text-gray-500 text-center py-8">Belum ada jurnal yang dibuat siswa ini.</p>
                        <?php else: ?>
                            <div class="space-y-6">
                                <?php foreach ($student_journals as $journal): ?>
                                    <div class="border border-gray-200 rounded-lg p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                <?php echo date('d M Y', strtotime($journal['tanggal_kegiatan'])); ?>
                                            </h3>
                                            <span class="px-3 py-1 text-sm rounded-full <?php
                                                if ($journal['status'] === 'approved') echo 'bg-green-100 text-green-800';
                                                elseif ($journal['status'] === 'rejected') echo 'bg-red-100 text-red-800';
                                                else echo 'bg-yellow-100 text-yellow-800';
                                            ?>">
                                                <?php echo ucfirst($journal['status']); ?>
                                            </span>
                                        </div>

                                        <div class="mb-4">
                                            <p class="text-gray-700 mb-2"><strong>Deskripsi Kegiatan:</strong></p>
                                            <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($journal['deskripsi_kegiatan'])); ?></p>
                                        </div>

                                        <?php if (!empty($journal['kendala'])): ?>
                                            <div class="mb-4">
                                                <p class="text-gray-700 mb-1"><strong>Kendala:</strong></p>
                                                <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($journal['kendala'])); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($journal['solusi'])): ?>
                                            <div class="mb-4">
                                                <p class="text-gray-700 mb-1"><strong>Solusi:</strong></p>
                                                <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($journal['solusi'])); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($journal['status'] === 'pending'): ?>
                                            <div class="mt-6 pt-4 border-t border-gray-200">
                                                <form method="POST" class="space-y-4">
                                                    <input type="hidden" name="approve_journal" value="1">
                                                    <input type="hidden" name="journal_id" value="<?php echo $journal['id']; ?>">

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Komentar Pembimbing</label>
                                                        <textarea name="komentar" rows="3" required
                                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                                  placeholder="Berikan komentar atau saran..."></textarea>
                                                    </div>

                                                    <button type="submit"
                                                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                                        <i class="fas fa-check mr-2"></i>Approve Jurnal
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <?php if (!empty($journal['komentar_pembimbing'])): ?>
                                                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                                                    <p class="text-blue-800 font-medium">Komentar Anda:</p>
                                                    <p class="text-blue-700"><?php echo nl2br(htmlspecialchars($journal['komentar_pembimbing'])); ?></p>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

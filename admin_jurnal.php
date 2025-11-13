<?php
require_once 'config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_jurnal'])) {
        $jurnal_id = sanitize_input($_POST['jurnal_id']);
        $komentar = sanitize_input($_POST['komentar']);
        $status = sanitize_input($_POST['status']);

        $stmt = $conn->prepare("UPDATE jurnal_harian SET komentar_pembimbing = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssi", $komentar, $status, $jurnal_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch students for the filter dropdown
$siswa_list_result = $conn->query("SELECT id, nama_lengkap FROM users WHERE role = 'siswa'");

// Fetch journals with student names
$selected_siswa = $_GET['siswa_id'] ?? 'all';
$jurnal_query = "SELECT j.id, j.tanggal_kegiatan, j.deskripsi_kegiatan, j.status, j.komentar_pembimbing, u.nama_lengkap AS nama_siswa FROM jurnal_harian j JOIN users u ON j.id_siswa = u.id";

if ($selected_siswa !== 'all') {
    $jurnal_query .= " WHERE j.id_siswa = ?";
}

$jurnal_query .= " ORDER BY j.tanggal_kegiatan DESC";

$stmt = $conn->prepare($jurnal_query);
if ($selected_siswa !== 'all') {
    $stmt->bind_param("i", $selected_siswa);
}

$stmt->execute();
$jurnal_result = $stmt->get_result();
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-5">Jurnal Siswa</h2>

    <!-- Filter Form -->
    <form method="GET" class="mb-5">
        <input type="hidden" name="page" value="jurnal">
        <label for="siswa_id" class="mr-2">Filter berdasarkan siswa:</label>
        <select name="siswa_id" id="siswa_id" onchange="this.form.submit()" class="border-gray-300 rounded-md">
            <option value="all">Semua Siswa</option>
            <?php while($siswa = $siswa_list_result->fetch_assoc()): ?>
                <option value="<?php echo $siswa['id']; ?>" <?php echo ($selected_siswa == $siswa['id']) ? 'selected' : ''; ?>><?php echo $siswa['nama_lengkap']; ?></option>
            <?php endwhile; ?>
        </select>
    </form>

    <!-- Journals Table -->
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Siswa</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Tanggal</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Kegiatan</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Status</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Komentar</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($jurnal_result->num_rows > 0): ?>
                    <?php while($jurnal = $jurnal_result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-3 px-4"><?php echo $jurnal['nama_siswa']; ?></td>
                            <td class="py-3 px-4"><?php echo date('d-m-Y', strtotime($jurnal['tanggal_kegiatan'])); ?></td>
                            <td class="py-3 px-4"><?php echo $jurnal['deskripsi_kegiatan']; ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full
                                    <?php if ($jurnal['status'] === 'approved'): echo 'bg-green-100 text-green-700';
                                          elseif ($jurnal['status'] === 'rejected'): echo 'bg-red-100 text-red-700';
                                          else: echo 'bg-yellow-100 text-yellow-700'; endif; ?>">
                                    <?php echo ucfirst($jurnal['status']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4"><?php echo $jurnal['komentar_pembimbing']; ?></td>
                            <td class="py-3 px-4">
                                <button onclick="openJurnalModal('<?php echo $jurnal['id']; ?>', '<?php echo htmlspecialchars($jurnal['komentar_pembimbing']); ?>', '<?php echo $jurnal['status']; ?>')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                    <i class="fas fa-comment-dots"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">Tidak ada data jurnal.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Jurnal Modal -->
<div id="jurnalModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Beri Komentar dan Status</h3>
                    <input type="hidden" name="jurnal_id" id="jurnal-id">
                    <div class="mt-5">
                        <label for="komentar" class="block text-sm font-medium text-gray-700">Komentar</label>
                        <textarea name="komentar" id="komentar" rows="4" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>

                        <label for="status" class="block text-sm font-medium text-gray-700 mt-4">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" name="update_jurnal" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                    <button type="button" onclick="closeModal('jurnalModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openJurnalModal(id, komentar, status) {
        document.getElementById('jurnal-id').value = id;
        document.getElementById('komentar').value = komentar;
        document.getElementById('status').value = status;
        openModal('jurnalModal');
    }
</script>

<?php
require_once 'config.php';

// Business logic for updating journal remains the same
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_jurnal'])) {
    // ... (PHP logic for updating journal) ...
}

// Fetch students for filter & journals
$siswa_list_result = $conn->query("SELECT id, nama_lengkap FROM users WHERE role = 'siswa'");
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

function getStatusBadge($status) {
    switch ($status) {
        case 'approved': return 'bg-emerald-100 text-emerald-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        default: return 'bg-yellow-100 text-yellow-800';
    }
}
?>

<div x-data="{
    showJurnalModal: false,
    jurnalData: { id: '', komentar_pembimbing: '', status: '' }
}" class="container mx-auto">

    <!-- Action Bar & Filter -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-semibold text-slate-800">Manajemen Jurnal Siswa</h2>
        <form method="GET" class="flex items-center gap-2">
            <input type="hidden" name="page" value="jurnal">
            <label for="siswa_id" class="text-sm font-medium text-slate-600">Filter:</label>
            <select name="siswa_id" id="siswa_id" onchange="this.form.submit()" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full sm:w-48 p-2.5">
                <option value="all">Semua Siswa</option>
                <?php while($siswa = $siswa_list_result->fetch_assoc()): ?>
                    <option value="<?php echo $siswa['id']; ?>" <?php echo ($selected_siswa == $siswa['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($siswa['nama_lengkap']); ?></option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <!-- Journals Table -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-slate-500">
                <thead class="text-xs text-white uppercase bg-slate-800">
                    <tr>
                        <th scope="col" class="px-6 py-3">Siswa</th>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Kegiatan</th>
                        <th scope="col" class="px-6 py-3 text-center">Status</th>
                        <th scope="col" class="px-6 py-3">Komentar</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jurnal_result->num_rows > 0): ?>
                        <?php while($jurnal = $jurnal_result->fetch_assoc()): ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap"><?php echo htmlspecialchars($jurnal['nama_siswa']); ?></td>
                                <td class="px-6 py-4"><?php echo date('d M Y', strtotime($jurnal['tanggal_kegiatan'])); ?></td>
                                <td class="px-6 py-4 max-w-xs truncate"><?php echo htmlspecialchars($jurnal['deskripsi_kegiatan']); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full <?php echo getStatusBadge($jurnal['status']); ?>">
                                        <?php echo ucfirst($jurnal['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-xs truncate"><?php echo htmlspecialchars($jurnal['komentar_pembimbing'] ?? '-'); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="jurnalData = <?php echo htmlspecialchars(json_encode($jurnal)); ?>; showJurnalModal = true" class="font-medium text-sky-600 hover:text-sky-800"><i class="fas fa-comment-dots"></i></button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-500">Tidak ada data jurnal untuk ditampilkan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Jurnal Modal -->
    <div x-show="showJurnalModal" x-cloak @click.away="showJurnalModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
            <h3 class="text-xl font-semibold mb-4">Detail Jurnal & Komentar</h3>
            <form method="POST">
                <input type="hidden" name="jurnal_id" x-model="jurnalData.id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Komentar Pembimbing</label>
                        <textarea name="komentar" x-model="jurnalData.komentar_pembimbing" rows="4" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Status Jurnal</label>
                        <select name="status" x-model="jurnalData.status" class="mt-1 w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 p-2.5">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" @click="showJurnalModal = false" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition">Batal</button>
                    <button type="submit" name="update_jurnal" class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

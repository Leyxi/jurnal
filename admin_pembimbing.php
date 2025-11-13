<?php
require_once 'config.php';

// PHP logic for CRUD operations remains the same.
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... (PHP logic for add, edit, delete pembimbing) ...
}

// Fetch all supervisors
$result = $conn->query("SELECT id, nama_lengkap, email, created_at FROM users WHERE role = 'pembimbing'");
?>

<div x-data="{
    showAddModal: false,
    showEditModal: false,
    showDeleteModal: false,
    editData: { id: '', nama_lengkap: '', email: '' },
    deleteData: { id: '' }
}" class="container mx-auto">

    <?php if (!empty($message)): ?>
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-slate-800">Manajemen Data Pembimbing</h2>
        <button @click="showAddModal = true" class="flex items-center bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
            <i class="fas fa-plus mr-2"></i> Tambah Pembimbing
        </button>
    </div>

    <!-- Supervisors Table -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-slate-500">
                <thead class="text-xs text-white uppercase bg-slate-800">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Tanggal Registrasi</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="px-6 py-4"><?php echo date('d F Y', strtotime($row['created_at'])); ?></td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <button @click="editData = <?php echo htmlspecialchars(json_encode($row)); ?>; showEditModal = true" class="font-medium text-yellow-500 hover:text-yellow-700"><i class="fas fa-edit"></i></button>
                                    <button @click="deleteData.id = '<?php echo $row['id']; ?>'; showDeleteModal = true" class="font-medium text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-10 text-slate-500">Tidak ada data pembimbing ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add Modal -->
    <div x-show="showAddModal" x-cloak @click.away="showAddModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Tambah Pembimbing Baru</h3>
            <form method="POST">
                <div class="space-y-4">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition">Batal</button>
                    <button type="submit" name="add_pembimbing" class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-show="showEditModal" x-cloak @click.away="showEditModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Data Pembimbing</h3>
            <form method="POST">
                <input type="hidden" name="id" x-model="editData.id">
                <div class="space-y-4">
                    <input type="text" name="nama" x-model="editData.nama_lengkap" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <input type="email" name="email" x-model="editData.email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <input type="password" name="password" placeholder="Password (kosongkan jika tidak diubah)" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition">Batal</button>
                    <button type="submit" name="edit_pembimbing" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-show="showDeleteModal" x-cloak @click.away="showDeleteModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Konfirmasi Penghapusan</h3>
            <p class="text-slate-600">Anda yakin ingin menghapus data pembimbing ini? Tindakan ini tidak dapat dibatalkan.</p>
            <form method="POST">
                <input type="hidden" name="id" x-model="deleteData.id">
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition">Batal</button>
                    <button type="submit" name="delete_pembimbing" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'config.php';

// Handle form submissions for CRUD operations
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_pembimbing'])) {
        // Add new supervisor
        $nama = sanitize_input($_POST['nama']);
        $email = sanitize_input($_POST['email']);
        $password = hash_password($_POST['password']);
        
        $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, 'pembimbing')");
        $stmt->bind_param("sss", $nama, $email, $password);
        if ($stmt->execute()) {
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Pembimbing berhasil ditambahkan.</div>';
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal menambahkan pembimbing.</div>';
        }
        $stmt->close();
    } elseif (isset($_POST['edit_pembimbing'])) {
        // Edit existing supervisor
        $id = sanitize_input($_POST['id']);
        $nama = sanitize_input($_POST['nama']);
        $email = sanitize_input($_POST['email']);
        
        if (!empty($_POST['password'])) {
            $password = hash_password($_POST['password']);
            $stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nama, $email, $password, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nama, $email, $id);
        }
        
        if ($stmt->execute()) {
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Data pembimbing berhasil diperbarui.</div>';
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal memperbarui data pembimbing.</div>';
        }
        $stmt->close();
    } elseif (isset($_POST['delete_pembimbing'])) {
        // Delete supervisor
        $id = sanitize_input($_POST['id']);
        
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'pembimbing'");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Pembimbing berhasil dihapus.</div>';
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal menghapus pembimbing.</div>';
        }
        $stmt->close();
    }
}

// Fetch all supervisors
$result = $conn->query("SELECT id, nama_lengkap, email, created_at FROM users WHERE role = 'pembimbing'");
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-5">Kelola Pembimbing</h2>
    
    <?php echo $message; ?>

    <div class="mb-5">
        <button onclick="openModal('addPembimbingModal')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i> Tambah Pembimbing
        </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm">Nama Lengkap</th>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm">Email</th>
                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm">Tanggal Registrasi</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="w-1/4 py-3 px-4"><?php echo $row['nama_lengkap']; ?></td>
                            <td class="w-1/4 py-3 px-4"><?php echo $row['email']; ?></td>
                            <td class="w-1/4 py-3 px-4"><?php echo date('d-m-Y', strtotime($row['created_at'])); ?></td>
                            <td class="py-3 px-4">
                                <button onclick="openEditModal('pembimbing', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['nama_lengkap']); ?>', '<?php echo $row['email']; ?>')" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal('pembimbing', '<?php echo $row['id']; ?>')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5">Tidak ada data pembimbing.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Pembimbing Modal -->
<div id="addPembimbingModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <form method="POST">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Tambah Pembimbing Baru</h3>
          <div class="mt-5">
            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            
            <label for="email" class="block text-sm font-medium text-gray-700 mt-4">Email</label>
            <input type="email" name="email" id="email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            
            <label for="password" class="block text-sm font-medium text-gray-700 mt-4">Password</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="submit" name="add_pembimbing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
          <button type="button" onclick="closeModal('addPembimbingModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Pembimbing Modal -->
<div id="editPembimbingModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Data Pembimbing</h3>
                    <input type="hidden" name="id" id="edit-pembimbing-id">
                    <div class="mt-5">
                        <label for="edit-pembimbing-nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit-pembimbing-nama" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        
                        <label for="edit-pembimbing-email" class="block text-sm font-medium text-gray-700 mt-4">Email</label>
                        <input type="email" name="email" id="edit-pembimbing-email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        
                        <label for="edit-pembimbing-password" class="block text-sm font-medium text-gray-700 mt-4">Password (kosongkan jika tidak ingin diubah)</label>
                        <input type="password" name="password" id="edit-pembimbing-password" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" name="edit_pembimbing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                    <button type="button" onclick="closeModal('editPembimbingModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Pembimbing Modal -->
<div id="deletePembimbingModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Pembimbing</h3>
                    <input type="hidden" name="id" id="delete-pembimbing-id">
                    <p class="mt-2">Anda yakin ingin menghapus pembimbing ini?</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" name="delete_pembimbing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Hapus</button>
                    <button type="button" onclick="closeModal('deletePembimbingModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openEditModal(type, id, nama, email) {
        document.getElementById('edit-' + type + '-id').value = id;
        document.getElementById('edit-' + type + '-nama').value = nama;
        document.getElementById('edit-' + type + '-email').value = email;
        openModal('edit' + type.charAt(0).toUpperCase() + type.slice(1) + 'Modal');
    }

    function openDeleteModal(type, id) {
        document.getElementById('delete-' + type + '-id').value = id;
        openModal('delete' + type.charAt(0).toUpperCase() + type.slice(1) + 'Modal');
    }
</script>

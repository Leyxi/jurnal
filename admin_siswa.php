<?php
require_once 'config.php';

// Handle form submissions for CRUD operations
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_siswa'])) {
        // Add new student
        $nama = sanitize_input($_POST['nama']);
        $email = sanitize_input($_POST['email']);
        $password = hash_password($_POST['password']);
        
        $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, 'siswa')");
        $stmt->bind_param("sss", $nama, $email, $password);
        if ($stmt->execute()) {
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Siswa berhasil ditambahkan.</div>';
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal menambahkan siswa.</div>';
        }
        $stmt->close();
    } elseif (isset($_POST['edit_siswa'])) {
        // Edit existing student
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
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Data siswa berhasil diperbarui.</div>';
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal memperbarui data siswa.</div>';
        }
        $stmt->close();
    } elseif (isset($_POST['delete_siswa'])) {
        // Delete student
        $id = sanitize_input($_POST['id']);
        
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'siswa'");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Siswa berhasil dihapus.</div>';
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal menghapus siswa.</div>';
        }
        $stmt->close();
    }
}

// Fetch all students
$result = $conn->query("SELECT id, nama_lengkap, email, created_at FROM users WHERE role = 'siswa'");
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-5">Kelola Siswa</h2>
    
    <?php echo $message; ?>

    <div class="mb-5">
        <button onclick="openModal('addSiswaModal')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i> Tambah Siswa
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
                                <button onclick="openEditModal('<?php echo $row['id']; ?>', '<?php echo $row['nama_lengkap']; ?>', '<?php echo $row['email']; ?>')" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal('<?php echo $row['id']; ?>')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5">Tidak ada data siswa.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Siswa Modal -->
<div id="addSiswaModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <form method="POST">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Tambah Siswa Baru</h3>
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
          <button type="submit" name="add_siswa" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
          <button type="button" onclick="closeModal('addSiswaModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Siswa Modal -->
<div id="editSiswaModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Data Siswa</h3>
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mt-5">
                        <label for="edit-nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit-nama" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        
                        <label for="edit-email" class="block text-sm font-medium text-gray-700 mt-4">Email</label>
                        <input type="email" name="email" id="edit-email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        
                        <label for="edit-password" class="block text-sm font-medium text-gray-700 mt-4">Password (kosongkan jika tidak ingin diubah)</label>
                        <input type="password" name="password" id="edit-password" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" name="edit_siswa" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                    <button type="button" onclick="closeModal('editSiswaModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Siswa Modal -->
<div id="deleteSiswaModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Siswa</h3>
                    <input type="hidden" name="id" id="delete-id">
                    <p class="mt-2">Anda yakin ingin menghapus siswa ini?</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" name="delete_siswa" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Hapus</button>
                    <button type="button" onclick="closeModal('deleteSiswaModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
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

    function openEditModal(id, nama, email) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-email').value = email;
        openModal('editSiswaModal');
    }

    function openDeleteModal(id) {
        document.getElementById('delete-id').value = id;
        openModal('deleteSiswaModal');
    }
</script>

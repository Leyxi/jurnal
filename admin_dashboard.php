<?php
require_once 'config.php';
redirect_if_not_logged_in();
redirect_if_not_role('guru');

$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PKL Hero Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .modal-active {
            display: flex;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-4 text-2xl font-bold border-b border-gray-700">Admin Panel</div>
            <nav class="mt-5 flex-1">
                <a href="admin_dashboard.php?page=dashboard" class="block py-3 px-4 transition duration-200 hover:bg-gray-700 <?php echo (!isset($_GET['page']) || $_GET['page'] === 'dashboard') ? 'bg-gray-900' : ''; ?>">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <a href="admin_dashboard.php?page=siswa" class="block py-3 px-4 transition duration-200 hover:bg-gray-700 <?php echo (isset($_GET['page']) && $_GET['page'] === 'siswa') ? 'bg-gray-900' : ''; ?>">
                    <i class="fas fa-users mr-3"></i> Kelola Siswa
                </a>
                <a href="admin_dashboard.php?page=pembimbing" class="block py-3 px-4 transition duration-200 hover:bg-gray-700 <?php echo (isset($_GET['page']) && $_GET['page'] === 'pembimbing') ? 'bg-gray-900' : ''; ?>">
                    <i class="fas fa-chalkboard-teacher mr-3"></i> Kelola Pembimbing
                </a>
                <a href="admin_dashboard.php?page=jurnal" class="block py-3 px-4 transition duration-200 hover:bg-gray-700 <?php echo (isset($_GET['page']) && $_GET['page'] === 'jurnal') ? 'bg-gray-900' : ''; ?>">
                    <i class="fas fa-book-open mr-3"></i> Jurnal Siswa
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <a href="logout.php" class="block text-center py-2 px-4 rounded transition duration-200 bg-red-600 hover:bg-red-700">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <?php
                    // Include page content based on the 'page' parameter
                    $page = $_GET['page'] ?? 'dashboard';

                    switch ($page) {
                        case 'siswa':
                            include 'admin_siswa.php';
                            break;
                        case 'pembimbing':
                            include 'admin_pembimbing.php';
                            break;
                        case 'jurnal':
                            include 'admin_jurnal.php';
                            break;
                        default:
                            echo '<h3 class="text-gray-700 text-3xl font-medium">Dashboard</h3>';
                            echo '<div class="mt-4"><div class="bg-white p-6 rounded-lg shadow-lg">Selamat datang di Admin Panel PKL Hero Hub. Gunakan menu di samping untuk mengelola data.</div></div>';
                            break;
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('modal-active');
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('modal-active');
            }
        }

        function openEditModal(type, id, nama, email) {
            document.getElementById(`edit-${type}-id`).value = id;
            document.getElementById(`edit-${type}-nama`).value = nama;
            document.getElementById(`edit-${type}-email`).value = email;
            openModal(`edit${type.charAt(0).toUpperCase() + type.slice(1)}Modal`);
        }

        function openDeleteModal(type, id) {
            document.getElementById(`delete-${type}-id`).value = id;
            openModal(`delete${type.charAt(0).toUpperCase() + type.slice(1)}Modal`);
        }

        function openJurnalModal(id, komentar, status) {
            document.getElementById('jurnal-id').value = id;
            document.getElementById('komentar').value = komentar;
            document.getElementById('status').value = status;
            openModal('jurnalModal');
        }

        // Close modals on escape key press
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal-active').forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });
    </script>
</body>
</html>

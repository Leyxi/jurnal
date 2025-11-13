<?php
<<<<<<< HEAD
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Security enhancements
session_regenerate_id(true);
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' https://cdnjs.cloudflare.com 'unsafe-inline'; font-src 'self' https://cdnjs.cloudflare.com;");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

require_once 'config.php';
$page = $_GET['page'] ?? 'dashboard';
=======
require_once 'config.php';
redirect_if_not_logged_in();
redirect_if_not_role('guru');

$nama = $_SESSION['nama'];
>>>>>>> origin/main
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PKL Hero Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-slate-100">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
        >
            <div class="flex items-center justify-center p-6 border-b border-slate-700">
                <i class="fas fa-user-shield text-2xl mr-3 text-sky-400"></i>
                <span class="text-2xl font-bold">Admin Panel</span>
            </div>
            <nav class="mt-4 flex-1">
                <?php
                $nav_items = [
                    'dashboard' => ['icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
                    'siswa' => ['icon' => 'fa-users', 'label' => 'Kelola Siswa'],
                    'pembimbing' => ['icon' => 'fa-chalkboard-teacher', 'label' => 'Kelola Pembimbing'],
                    'jurnal' => ['icon' => 'fa-book-open', 'label' => 'Jurnal Siswa']
                ];
                ?>
                <?php foreach ($nav_items as $key => $item): ?>
                    <a href="admin_dashboard.php?page=<?php echo $key; ?>" 
                       class="flex items-center py-3 px-6 text-slate-300 transition duration-200 hover:bg-slate-700 hover:text-white <?php echo ($page === $key) ? 'bg-sky-600 text-white' : ''; ?>">
                        <i class="fas <?php echo $item['icon']; ?> w-6 text-center mr-3"></i> <?php echo $item['label']; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="p-6 border-t border-slate-700">
                <a href="logout.php" class="flex items-center justify-center w-full py-2 px-4 rounded-lg transition duration-200 bg-red-600 hover:bg-red-700">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between p-4 bg-white border-b-2 border-slate-200">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 focus:outline-none lg:hidden">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                    <h1 class="text-2xl font-semibold text-slate-800 ml-4 lg:ml-0">
                        <?php echo $nav_items[$page]['label'] ?? 'Dashboard'; ?>
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-slate-600 hidden sm:block">Selamat Datang, <strong>Admin</strong></span>
                </div>
            </header>
            
            <!-- Main -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100">
                <div class="container mx-auto px-6 py-8">
                    <?php
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
                            echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">';
                            // Stat card example 1
                            echo '<div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between"><div class="flex flex-col"> <span class="text-sm text-slate-500">Total Siswa</span><span class="text-3xl font-bold">150</span></div><div class="bg-sky-100 text-sky-600 rounded-full p-4"><i class="fas fa-users fa-lg"></i></div></div>';
                            // Stat card example 2
                            echo '<div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between"><div class="flex flex-col"><span class="text-sm text-slate-500">Jurnal Disetujui</span><span class="text-3xl font-bold">85%</span></div><div class="bg-emerald-100 text-emerald-600 rounded-full p-4"><i class="fas fa-check-circle fa-lg"></i></div></div>';
                            // Stat card example 3
                            echo '<div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between"><div class="flex flex-col"><span class="text-sm text-slate-500">Pembimbing Aktif</span><span class="text-3xl font-bold">12</span></div><div class="bg-indigo-100 text-indigo-600 rounded-full p-4"><i class="fas fa-chalkboard-teacher fa-lg"></i></div></div>';
                            echo '</div>';
                            echo '<div class="mt-8"><div class="bg-white p-6 rounded-xl shadow-lg">Selamat datang di Admin Panel PKL Hero Hub. Gunakan menu di samping untuk mengelola data.</div></div>';
                            break;
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>
<<<<<<< HEAD
=======

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
>>>>>>> origin/main
</body>
</html>
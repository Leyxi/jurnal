<?php
require_once 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
<<<<<<< HEAD
    // ... (PHP login logic remains the same) ...
=======
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Email dan password harus diisi.</div>';
    } else {
        // Get user from database
        $stmt = $conn->prepare("SELECT id, nama_lengkap, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (verify_password($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'guru') {
                    header("Location: admin_dashboard.php");
                } elseif ($user['role'] === 'siswa') {
                    header("Location: siswa_dashboard.php");
                } elseif ($user['role'] === 'pembimbing') {
                    header("Location: pembimbing_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Password salah.</div>';
            }
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Email tidak ditemukan.</div>';
        }
        $stmt->close();
    }
>>>>>>> origin/main
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - PKL Hero Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100">
    <div class="flex min-h-screen">
        <!-- Branding Section -->
        <div class="hidden lg:block w-1/2 bg-gradient-to-br from-sky-600 to-slate-900 text-white p-12 flex flex-col justify-between">
            <div>
                <h1 class="text-4xl font-bold tracking-tight">Selamat Datang Kembali di PKL Hero Hub</h1>
                <p class="mt-4 text-lg text-sky-200">Platform terpadu untuk mengelola dan memantau kemajuan Praktek Kerja Lapangan Anda.</p>
            </div>
            <div class="text-sm text-slate-400">
                &copy; <?php echo date('Y'); ?> PKL Hero Hub. All Rights Reserved.
            </div>
        </div>

        <!-- Form Section -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <div class="text-center lg:text-left mb-10">
                    <div class="lg:hidden flex items-center justify-center mb-6">
                        <i class="fas fa-user-shield text-3xl text-sky-600"></i>
                        <span class="text-3xl font-bold ml-3 text-slate-800">PKL Hero Hub</span>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-800">Masuk ke Akun Anda</h2>
                    <p class="text-slate-500 mt-2">Belum punya akun? <a href="register.php" class="text-sky-600 hover:underline font-semibold">Daftar di sini</a></p>
                </div>

                <?php if ($message): ?>
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg shadow-sm" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-600 mb-1">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-envelope text-slate-400"></i>
                            </span>
                            <input type="email" id="email" name="email" required placeholder="email@example.com"
                                   class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-600 mb-1">Password</label>
                        <div class="relative">
                             <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-lock text-slate-400"></i>
                            </span>
                            <input type="password" id="password" name="password" required placeholder="••••••••"
                                   class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center items-center bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                    </button>
                </form>

                <div class="text-center mt-8">
                    <a href="index.php" class="text-sm text-slate-500 hover:text-slate-700 hover:underline"><i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
require_once 'config.php';

if (is_logged_in()) {
    if (get_user_role() === 'siswa') {
        header("Location: siswa_dashboard.php");
    } elseif (get_user_role() === 'pembimbing') {
        header("Location: pembimbing_dashboard.php");
    } else {
        header("Location: login.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Hero Hub - Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl font-bold text-gray-800 mb-6">
                <i class="fas fa-graduation-cap text-blue-600 mr-4"></i>
                PKL Hero Hub
            </h1>
            <p class="text-xl text-gray-600 mb-12">
                Platform modern untuk mengelola kegiatan Praktik Kerja Lapangan (PKL) dengan mudah dan efisien.
            </p>

            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="text-blue-600 mb-4">
                        <i class="fas fa-user-plus text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Registrasi</h3>
                    <p class="text-gray-600">Daftar sebagai siswa untuk memulai perjalanan PKL Anda.</p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="text-green-600 mb-4">
                        <i class="fas fa-book text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Jurnal Harian</h3>
                    <p class="text-gray-600">Catat kegiatan, kendala, dan solusi setiap hari.</p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="text-purple-600 mb-4">
                        <i class="fas fa-check-circle text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Validasi</h3>
                    <p class="text-gray-600">Pembimbing memvalidasi dan memberikan komentar.</p>
                </div>
            </div>

            <div class="space-x-4">
                <a href="register.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors inline-block">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </a>
                <a href="login.php" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg transition-colors inline-block">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                </a>
            </div>
        </div>
    </div>
</body>
</html>

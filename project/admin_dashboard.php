<?php
require_once 'config.php';

// Cek login admin (bisa ditambahkan role checking)
if (!cek_login()) {
    redirect('index.php');
}

// Statistik dashboard
try {
    // Hitung total pengguna
    $query_total_pengguna = "SELECT COUNT(*) as total FROM pengguna";
    $stmt_total_pengguna = $koneksi_db->prepare($query_total_pengguna);
    $stmt_total_pengguna->execute();
    $total_pengguna = $stmt_total_pengguna->fetch(PDO::FETCH_ASSOC)['total'];

    // Hitung total pesan kontak
    $query_total_pesan = "SELECT COUNT(*) as total FROM pesan_kontak";
    $stmt_total_pesan = $koneksi_db->prepare($query_total_pesan);
    $stmt_total_pesan->execute();
    $total_pesan = $stmt_total_pesan->fetch(PDO::FETCH_ASSOC)['total'];

    // Hitung pesan belum dibaca
    $query_pesan_baru = "SELECT COUNT(*) as total FROM pesan_kontak WHERE status_baca = 0";
    $stmt_pesan_baru = $koneksi_db->prepare($query_pesan_baru);
    $stmt_pesan_baru->execute();
    $pesan_baru = $stmt_pesan_baru->fetch(PDO::FETCH_ASSOC)['total'];

    // Hitung total program aktif
    $query_program_aktif = "SELECT COUNT(*) as total FROM program_kegiatan WHERE status = 'aktif'";
    $stmt_program_aktif = $koneksi_db->prepare($query_program_aktif);
    $stmt_program_aktif->execute();
    $program_aktif = $stmt_program_aktif->fetch(PDO::FETCH_ASSOC)['total'];

    // Ambil pesan terbaru
    $query_pesan_terbaru = "SELECT * FROM pesan_kontak ORDER BY tanggal_kirim DESC LIMIT 5";
    $stmt_pesan_terbaru = $koneksi_db->prepare($query_pesan_terbaru);
    $stmt_pesan_terbaru->execute();
    $pesan_terbaru = $stmt_pesan_terbaru->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $total_pengguna = $total_pesan = $pesan_baru = $program_aktif = 0;
    $pesan_terbaru = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - <?php echo $nama_website; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-teal-700 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Dashboard Admin <?php echo $nama_website; ?></h1>
            <div class="flex items-center space-x-4">
                <span>Selamat datang, <?php echo $_SESSION['nama_pengguna']; ?></span>
                <a href="landing.php" class="bg-teal-500 hover:bg-teal-600 px-3 py-1 rounded">Lihat Website</a>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8 px-4">
        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Pengguna</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($total_pengguna); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Pesan</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($total_pesan); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l5.657-5.657A2 2 0 0111.899 1h4.242a2 2 0 011.414.586l2.829 2.828A2 2 0 0121 5.828V20a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 01.586-1.414z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Pesan Baru</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($pesan_baru); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Program Aktif</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($program_aktif); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Navigasi Admin -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="kelola_pengguna.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Kelola Pengguna</h3>
                <p class="text-gray-600">Lihat dan kelola data pengguna yang terdaftar</p>
            </a>

            <a href="kelola_konten.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Kelola Konten</h3>
                <p class="text-gray-600">Kelola data ekosistem, ancaman, dan program</p>
            </a>

            <a href="kelola_pesan.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Kelola Pesan</h3>
                <p class="text-gray-600">Lihat dan balas pesan dari pengunjung</p>
            </a>
        </div>

        <!-- Pesan Terbaru -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Pesan Kontak Terbaru</h2>
            </div>
            <div class="p-6">
                <?php if (!empty($pesan_terbaru)): ?>
                    <div class="space-y-4">
                        <?php foreach ($pesan_terbaru as $pesan): ?>
                        <div class="border-l-4 <?php echo $pesan['status_baca'] ? 'border-gray-400' : 'border-blue-500'; ?> pl-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($pesan['nama']); ?></h4>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($pesan['email']); ?></p>
                                    <p class="text-sm text-gray-800 mt-1"><?php echo htmlspecialchars(substr($pesan['pesan'], 0, 100)); ?>...</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500"><?php echo date('d/m/Y H:i', strtotime($pesan['tanggal_kirim'])); ?></p>
                                    <?php if (!$pesan['status_baca']): ?>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mt-1">Baru</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4">
                        <a href="kelola_pesan.php" class="text-teal-600 hover:text-teal-800 text-sm font-medium">
                            Lihat semua pesan →
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">Belum ada pesan kontak.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
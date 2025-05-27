<?php
require_once 'config.php';

// Cek jika pengguna sudah login, redirect ke landing
if (cek_login()) {
    redirect('landing.php');
}

// Proses pendaftaran jika form disubmit
$pesan_error = '';
$pesan_sukses = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = bersihkan_input($_POST['nama_lengkap']);
    $email = bersihkan_input($_POST['email']);
    $password = bersihkan_input($_POST['password']);
    $konfirmasi_password = bersihkan_input($_POST['konfirmasi_password']);
    $nomor_telepon = bersihkan_input($_POST['nomor_telepon']);
    
    // Validasi input
    if (empty($nama_lengkap) || empty($email) || empty($password) || empty($konfirmasi_password)) {
        $pesan_error = 'Semua field wajib diisi!';
    } elseif ($password !== $konfirmasi_password) {
        $pesan_error = 'Konfirmasi password tidak sesuai!';
    } elseif (strlen($password) < 6) {
        $pesan_error = 'Password minimal 6 karakter!';
    } else {
        try {
            // Cek apakah email sudah terdaftar
            $query_cek = "SELECT id FROM pengguna WHERE email = :email";
            $stmt_cek = $koneksi_db->prepare($query_cek);
            $stmt_cek->bindParam(':email', $email);
            $stmt_cek->execute();
            
            if ($stmt_cek->rowCount() > 0) {
                $pesan_error = 'Email sudah terdaftar! Silakan gunakan email lain.';
            } else {
                // Hash password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert pengguna baru
                $query_daftar = "INSERT INTO pengguna (nama, email, password, nomor_telepon, tanggal_daftar) 
                                VALUES (:nama, :email, :password, :nomor_telepon, NOW())";
                $stmt_daftar = $koneksi_db->prepare($query_daftar);
                $stmt_daftar->bindParam(':nama', $nama_lengkap);
                $stmt_daftar->bindParam(':email', $email);
                $stmt_daftar->bindParam(':password', $password_hash);
                $stmt_daftar->bindParam(':nomor_telepon', $nomor_telepon);
                $stmt_daftar->execute();
                
                $pesan_sukses = 'Pendaftaran berhasil! Silakan login dengan akun Anda.';
                
                // Reset form
                $nama_lengkap = $email = $nomor_telepon = '';
            }
        } catch(PDOException $e) {
            $pesan_error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar - <?php echo $nama_website; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="min-h-screen bg-sky-100 bg-cover bg-center flex items-center justify-center" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80');">

  <!-- Overlay -->
  <div class="absolute inset-0 bg-teal-900 opacity-60"></div>

  <!-- Form Daftar -->
  <div class="relative z-10 bg-white bg-opacity-90 backdrop-blur-lg rounded-2xl shadow-2xl p-10 w-full max-w-md">
    <h1 class="text-3xl font-bold text-teal-700 text-center mb-2">Daftar ke <?php echo $nama_website; ?></h1>
    <p class="text-sm text-center text-gray-600 mb-6">Bergabunglah dengan komunitas pelestari laut Indonesia</p>

    <?php if (!empty($pesan_error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php echo $pesan_error; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($pesan_sukses)): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?php echo $pesan_sukses; ?>
        <a href="index.php" class="block mt-2 text-center bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded-lg transition">
            Login Sekarang
        </a>
    </div>
    <?php else: ?>

    <form method="POST" class="space-y-5">
      <div>
        <label for="nama_lengkap" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required
               value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>"
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
        <input type="email" id="email" name="email" placeholder="nama@email.com" required
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
      </div>

      <div>
        <label for="nomor_telepon" class="block text-sm font-medium text-slate-700">Nomor Telepon</label>
        <input type="tel" id="nomor_telepon" name="nomor_telepon" placeholder="08xxxxxxxxxx"
               value="<?php echo isset($_POST['nomor_telepon']) ? htmlspecialchars($_POST['nomor_telepon']) : ''; ?>"
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
        <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
      </div>

      <div>
        <label for="konfirmasi_password" class="block text-sm font-medium text-slate-700">Konfirmasi Kata Sandi</label>
        <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="••••••••" required
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
      </div>

      <div class="flex items-center space-x-2 text-sm">
        <input type="checkbox" id="setuju_syarat" required class="form-checkbox text-teal-600" />
        <label for="setuju_syarat" class="text-gray-700">
          Saya setuju dengan <a href="#" class="text-teal-600 hover:underline">syarat dan ketentuan</a>
        </label>
      </div>

      <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded-lg transition">
        Daftar
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
      Sudah punya akun?
      <a href="index.php" class="text-teal-600 hover:underline">Login sekarang</a>
    </p>
    <?php endif; ?>
  </div>
</body>
</html>
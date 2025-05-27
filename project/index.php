<?php
require_once 'config.php';

// Cek jika pengguna sudah login, redirect ke landing
if (cek_login()) {
    redirect('landing.php');
}

// Proses login jika form disubmit
$pesan_error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = bersihkan_input($_POST['email']);
    $password = bersihkan_input($_POST['password']);
    
    if (!empty($email) && !empty($password)) {
        try {
            // Query untuk mencari pengguna
            $query = "SELECT id, nama, email, password FROM pengguna WHERE email = :email";
            $stmt = $koneksi_db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $pengguna = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($pengguna && password_verify($password, $pengguna['password'])) {
                // Login berhasil
                $_SESSION['pengguna_id'] = $pengguna['id'];
                $_SESSION['nama_pengguna'] = $pengguna['nama'];
                $_SESSION['email_pengguna'] = $pengguna['email'];
                
                redirect('landing.php');
            } else {
                $pesan_error = 'Email atau password salah!';
            }
        } catch(PDOException $e) {
            $pesan_error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    } else {
        $pesan_error = 'Mohon isi email dan password dengan benar.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - <?php echo $nama_website; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="min-h-screen bg-sky-100 bg-cover bg-center flex items-center justify-center" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80');">

  <!-- Overlay -->
  <div class="absolute inset-0 bg-teal-900 opacity-60"></div>

  <!-- Form Login -->
  <div class="relative z-10 bg-white bg-opacity-90 backdrop-blur-lg rounded-2xl shadow-2xl p-10 w-full max-w-md">
    <h1 class="text-3xl font-bold text-teal-700 text-center mb-2">Masuk ke <?php echo $nama_website; ?></h1>
    <p class="text-sm text-center text-gray-600 mb-6"><?php echo $deskripsi_website; ?></p>

    <?php if (!empty($pesan_error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php echo $pesan_error; ?>
    </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
        <input type="email" id="email" name="email" placeholder="nama@email.com" required
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required
               class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
      </div>

      <div class="flex items-center justify-between text-sm">
        <label class="flex items-center space-x-2">
          <input type="checkbox" name="ingat_saya" class="form-checkbox text-teal-600" />
          <span class="text-gray-700">Ingat saya</span>
        </label>
        <a href="lupa_password.php" class="text-teal-600 hover:underline">Lupa sandi?</a>
      </div>

      <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded-lg transition">
        Masuk
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
      Belum punya akun?
      <a href="daftar.php" class="text-teal-600 hover:underline">Daftar sekarang</a>
    </p>
  </div>
</body>
</html>
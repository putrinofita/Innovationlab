<?php
require_once 'config.php';

// Cek jika pengguna belum login, redirect ke halaman login
if (!cek_login()) {
    redirect('index.php');
}

// Ambil data ekosistem laut dari database
try {
    $query_ekosistem = "SELECT * FROM ekosistem_laut ORDER BY urutan ASC";
    $stmt_ekosistem = $koneksi_db->prepare($query_ekosistem);
    $stmt_ekosistem->execute();
    $data_ekosistem = $stmt_ekosistem->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $data_ekosistem = [];
}

// Ambil data ancaman dari database
try {
    $query_ancaman = "SELECT * FROM ancaman_lingkungan ORDER BY tingkat_ancaman DESC";
    $stmt_ancaman = $koneksi_db->prepare($query_ancaman);
    $stmt_ancaman->execute();
    $data_ancaman = $stmt_ancaman->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $data_ancaman = [];
}

// Proses form kontak jika ada
$pesan_sukses = '';
$pesan_error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_pesan'])) {
    $nama_pengirim = bersihkan_input($_POST['nama']);
    $email_pengirim = bersihkan_input($_POST['email']);
    $pesan = bersihkan_input($_POST['pesan']);
    
    if (!empty($nama_pengirim) && !empty($email_pengirim) && !empty($pesan)) {
        try {
            $query_pesan = "INSERT INTO pesan_kontak (nama, email, pesan, tanggal_kirim) VALUES (:nama, :email, :pesan, NOW())";
            $stmt_pesan = $koneksi_db->prepare($query_pesan);
            $stmt_pesan->bindParam(':nama', $nama_pengirim);
            $stmt_pesan->bindParam(':email', $email_pengirim);
            $stmt_pesan->bindParam(':pesan', $pesan);
            $stmt_pesan->execute();
            
            $pesan_sukses = 'Pesan Anda berhasil dikirim! Kami akan segera merespons.';
        } catch(PDOException $e) {
            $pesan_error = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.';
        }
    } else {
        $pesan_error = 'Mohon isi semua field yang diperlukan.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $nama_website; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }
        .flex::-webkit-scrollbar {
            display: none;
        }
        .flex {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }
    </style>
  </head>
  <body class="bg-sky-50 text-slate-800">
    <!-- Header -->
    <header class="bg-teal-700 text-white p-6 shadow-md">
        <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between">
            <h1 class="text-3xl font-bold"><?php echo $nama_website; ?></h1>
            <div class="flex items-center space-x-4">
                <nav class="mt-4 sm:mt-0 space-x-4 text-sm sm:text-base">
                    <a href="#beranda" class="hover:underline transition">Beranda</a>
                    <a href="#tentang" class="hover:underline transition">Tentang</a>
                    <a href="#ekosistem" class="hover:underline transition">Ekosistem Laut</a>
                    <a href="#ancaman" class="hover:underline transition">Ancaman</a>
                    <a href="#aksi" class="hover:underline transition">Aksi Kami</a>
                    <a href="#kontak" class="hover:underline transition">Kontak</a>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="text-sm">Selamat datang, <?php echo $_SESSION['nama_pengguna']; ?></span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Keluar</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-cover bg-center h-72 sm:h-96" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80');">
      <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-center px-4">
        <h2 class="text-3xl sm:text-4xl font-bold text-white">Selamat Datang di <?php echo $nama_website; ?></h2>
        <p class="mt-2 text-lg text-white"><?php echo $deskripsi_website; ?></p>
        <a href="#aksi" class="mt-6 inline-block bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded-lg transition">Gabung Bersama Kami</a>
      </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-6 py-10 space-y-12">
      <section id="beranda">
        <h2 class="text-2xl font-semibold text-teal-700"><?php echo $nama_website; ?></h2>
        <p class="mt-2">Kami peduli terhadap masa depan laut kita. Edukasi dan aksi nyata untuk melindungi ekosistem laut Indonesia.</p>
      </section>

      <section id="tentang">
        <h2 class="text-2xl font-semibold text-teal-700">Tentang <?php echo $nama_website; ?></h2>
        <p class="mt-2"><?php echo $nama_website; ?> adalah komunitas peduli lingkungan yang fokus pada pelestarian ekosistem laut melalui berbagai program edukasi, konservasi, dan pemberdayaan masyarakat pesisir.</p>
      </section>

      <section id="ekosistem">
        <h2 class="text-2xl font-semibold text-teal-700 mb-4">Apa Itu Ekosistem Laut?</h2>
        <div class="flex space-x-6 overflow-x-auto pb-4">
          <?php if (!empty($data_ekosistem)): ?>
            <?php foreach ($data_ekosistem as $ekosistem): ?>
            <div class="bg-white rounded-lg shadow overflow-hidden w-48 flex-shrink-0">
              <img src="<?php echo htmlspecialchars($ekosistem['gambar_url']); ?>" alt="<?php echo htmlspecialchars($ekosistem['nama']); ?>" class="w-full h-40 object-cover" />
              <div class="p-4">
                <h3 class="font-semibold text-lg text-teal-600"><?php echo htmlspecialchars($ekosistem['nama']); ?></h3>
                <p class="text-sm mt-2"><?php echo htmlspecialchars($ekosistem['deskripsi']); ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Data default jika database kosong -->
            <div class="bg-white rounded-lg shadow overflow-hidden w-48 flex-shrink-0">
              <img src="https://images.unsplash.com/photo-1546026423-cc4642628d2b?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Terumbu Karang" class="w-full h-40 object-cover" />
              <div class="p-4">
                <h3 class="font-semibold text-lg text-teal-600">Terumbu Karang</h3>
                <p class="text-sm mt-2">Habitat penting bagi ribuan spesies laut. Rentan terhadap pemutihan akibat pemanasan global.</p>
              </div>
            </div>
            <!-- Tambahkan card lainnya sesuai data original -->
          <?php endif; ?>
        </div>
      </section>

      <!-- Ancaman Section -->
      <section id="ancaman" class="py-16 bg-white">
        <div class="container mx-auto px-4">
          <h2 class="text-3xl font-bold text-center mb-8 text-teal-800">Ancaman terhadap Keberlanjutan</h2>
          
          <div class="flex overflow-x-auto space-x-6 pb-4">
            <?php if (!empty($data_ancaman)): ?>
              <?php foreach ($data_ancaman as $ancaman): ?>
              <div class="min-w-[250px] bg-sky-100 rounded-xl shadow-lg p-4 flex-shrink-0">
                <img src="<?php echo htmlspecialchars($ancaman['gambar_url']); ?>" class="rounded-md mb-3 w-full h-48 object-cover" alt="<?php echo htmlspecialchars($ancaman['nama']); ?>">
                <h3 class="font-bold text-lg text-red-700"><?php echo htmlspecialchars($ancaman['nama']); ?></h3>
                <p class="text-sm text-gray-700"><?php echo htmlspecialchars($ancaman['deskripsi']); ?></p>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <!-- Data default jika database kosong -->
              <div class="min-w-[250px] bg-sky-100 rounded-xl shadow-lg p-4 flex-shrink-0">
                <img src="https://source.unsplash.com/300x200/?ocean,pollution" class="rounded-md mb-3" alt="Pencemaran Laut">
                <h3 class="font-bold text-lg text-red-700">Pencemaran Laut</h3>
                <p class="text-sm text-gray-700">Sampah plastik dan limbah industri mencemari laut dan membahayakan ekosistem laut.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <section id="aksi">
        <h2 class="text-2xl font-semibold text-teal-700">Aksi Kami</h2>
        <p class="mt-2">Sebagai bagian dari komitmen kami, <?php echo $nama_website; ?> rutin mengadakan:</p>
        <ul class="list-disc list-inside mt-2 space-y-1">
            <li>Kampanye bersih pantai dan laut</li>
            <li>Edukasi lingkungan di sekolah dan komunitas lokal</li>
            <li>Pelatihan pengelolaan sampah ramah lingkungan</li>
        </ul>
        <p class="mt-4">Bersama, kita bisa menciptakan laut yang lebih bersih dan sehat untuk generasi mendatang.</p>
        <a href="#kontak" class="mt-4 inline-block bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded-lg transition">Hubungi Kami</a>
      </section>

      <section id="peta" class="mt-12">
        <h2 class="text-2xl font-semibold text-teal-700 mb-4">Peta Pantai Interaktif</h2>
        <div id="map" class="w-full h-96 rounded-lg shadow"></div>
      </section>

      <section id="kontak">
        <h2 class="text-2xl font-semibold text-teal-700">Kontak</h2>
        <p class="mt-2">Ingin tahu lebih banyak atau ingin berkontribusi bersama kami? Hubungi kami melalui:</p>
        <p class="mt-1">📧 Email: <a href="mailto:<?php echo $email_kontak; ?>" class="text-teal-700 hover:underline"><?php echo $email_kontak; ?></a></p>
        <p>📸 Instagram: <a href="https://instagram.com/bluewave" target="_blank" class="text-teal-700 hover:underline"><?php echo $instagram_handle; ?></a></p>

        <?php if (!empty($pesan_sukses)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4">
            <?php echo $pesan_sukses; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($pesan_error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
            <?php echo $pesan_error; ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="mt-6 space-y-4 max-w-md">
            <input type="text" name="nama" placeholder="Nama" required
                   value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                   class="w-full border border-gray-300 rounded p-2" />
            <input type="email" name="email" placeholder="Email" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                   class="w-full border border-gray-300 rounded p-2" />
            <textarea rows="4" name="pesan" placeholder="Pesan" required
                      class="w-full border border-gray-300 rounded p-2"><?php echo isset($_POST['pesan']) ? htmlspecialchars($_POST['pesan']) : ''; ?></textarea>
            <button type="submit" name="kirim_pesan" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">Kirim</button>
        </form>
      </section>
    </main>

    <!-- Footer -->
    <footer class="bg-teal-800 text-white text-center p-4">
      <p>&copy; 2025 bluewave</p>
    </footer>

    <script>
        // Inisialisasi peta
        var peta = L.map('map').setView([-6.1108, 106.8283], 13); // Lokasi awal (Pantai Ancol)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(peta);

        L.marker([-6.1108, 106.8283]).addTo(peta)
            .bindPopup('Pantai Ancol')
            .openPopup();
    </script>
  </body>
</html>
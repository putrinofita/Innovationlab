<?php
require_once 'config.php';

$message = '';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Cek apakah email sudah ada
    $check_query = "SELECT email FROM users WHERE email = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(1, $email);
    $check_stmt->execute();
    
    if($check_stmt->rowCount() > 0) {
        $message = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users SET name=:name, email=:email, password=:password";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        
        if($stmt->execute()) {
            $message = "Registrasi berhasil! Silakan login.";
        } else {
            $message = "Registrasi gagal!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Daftar - BlueWave</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="min-h-screen bg-sky-100 bg-cover bg-center flex items-center justify-center" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80');">

    <div class="absolute inset-0 bg-teal-900 opacity-60"></div>

    <div class="relative z-10 bg-white bg-opacity-90 backdrop-blur-lg rounded-2xl shadow-2xl p-10 w-full max-w-md">
        <h1 class="text-3xl font-bold text-teal-700 text-center mb-2">Daftar BlueWave</h1>
        <p class="text-sm text-center text-gray-600 mb-6">Bergabunglah dengan komunitas peduli laut</p>

        <?php if($message): ?>
            <div class="<?php echo strpos($message, 'berhasil') !== false ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border px-4 py-3 rounded mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input type="text" id="name" name="name" placeholder="Nama lengkap" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input type="email" id="email" name="email" placeholder="nama@email.com" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400" />
            </div>

            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                Daftar
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Sudah punya akun?
            <a href="login.php" class="text-teal-600 hover:underline">Masuk di sini</a>
        </p>
    </div>
</body>
</html>

<?php
// Konfigurasi Database
$nama_host = 'localhost';
$nama_database = 'bluewave_db';
$username_db = 'root';
$password_db = '';

// Pengaturan Umum Website
$nama_website = 'BlueWave';
$deskripsi_website = 'Melalui edukasi dan aksi nyata, BlueWave hadir sebagai penggerak perubahan positif bagi laut Indonesia.';
$email_kontak = 'bluewave@gmail.com';
$instagram_handle = '@bluewave';

// Konfigurasi Session
session_start();

// Koneksi ke Database
try {
    $koneksi_db = new PDO("mysql:host=$nama_host;dbname=$nama_database", $username_db, $password_db);
    $koneksi_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $koneksi_db->exec("set names utf8");
} catch(PDOException $error) {
    die("Koneksi database gagal: " . $error->getMessage());
}

// Fungsi untuk membersihkan input
function bersihkan_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fungsi untuk cek login
function cek_login() {
    return isset($_SESSION['pengguna_id']) && !empty($_SESSION['pengguna_id']);
}
?>
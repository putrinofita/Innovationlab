<?php
require_once 'config.php';

// Hapus semua data session
session_unset();
session_destroy();

// Redirect ke halaman login
redirect('index.php');
?>
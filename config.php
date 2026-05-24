<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_kosan";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi helper dengan pengecekan agar tidak redeclare
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('alert')) {
    function alert($msg, $type = 'success') {
        return "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                    $msg
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    }
}

if (!function_exists('clean')) {
    function clean($data) {
        global $conn;
        return htmlspecialchars(strip_tags($conn->real_escape_string($data)));
    }
}
?>
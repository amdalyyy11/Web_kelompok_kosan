<?php
session_start();
$role = $_SESSION['role'] ?? '';
session_destroy();

if ($role == 'admin') {
    header("Location: admin/login.php");
} else {
    header("Location: login.php");
}
exit;
?>
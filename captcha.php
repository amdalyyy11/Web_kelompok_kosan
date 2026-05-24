<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Captcha Matematika Sederhana
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$_SESSION['captcha_answer'] = $num1 + $num2;

echo "$num1 + $num2 = ?";
?>
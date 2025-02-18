<?php
session_start();

function isAuthenticated() {
    return isset($_SESSION['user']);
}

// Если пользователь не авторизован, перенаправляем на главную
if (!isAuthenticated() && basename($_SERVER['PHP_SELF']) != 'index.php') {
    header("Location: index.php");
    exit;
}
?>

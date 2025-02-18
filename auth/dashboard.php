<?php
require 'auth_check.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
</head>
<body>
    <h1>Добро пожаловать, <?= htmlspecialchars($_SESSION['user']); ?>!</h1>
    <p><a href="logout.php">Выйти</a></p>
    <!-- Кнопка для перехода на главную страницу -->
    <p><a href="/../index.php">Перейти на главную страницу</a></p>
</body>
</html>

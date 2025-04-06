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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="../css/style.css">
    <?php require_once "../html_inc/header.php"; ?>
    <style>
        /* Кнопки администратора */
        .admin-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #d52b1e; /* Основной красный */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 5px;
        }

        .admin-btn:hover {
            background-color: #b02518; /* Темнее при наведении */
        }

        /* Кнопка "Вернуться на карту" */
        .back-to-map-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #d52b1e; /* Основной красный */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px; /* Отступ сверху */
            text-decoration: none; /* Убираем подчеркивание для ссылки */
        }

        .back-to-map-btn:hover {
            background-color: #b02518; /* Темнее при наведении */
        }
    </style>
</head>
<body>

    <div class="content">
        <h2>Добро пожаловать, <?= htmlspecialchars($_SESSION['user']) ?>!</h2>
        <?php if ($_SESSION['is_admin']): ?>
            <p>Вы администратор. Вы можете добавлять точки на карту.</p>
            <button id="update-data-btn" class="admin-btn">Обновить данные</button>
            <button id="create-event-btn" class="admin-btn">Создать мероприятие</button>
        <?php elseif ($_SESSION['is_worker']): ?>
            <p>Вы рабочий. У вас нет прав для добавления точек.</p>
        <?php else: ?>
            <p>У вас нет специальных прав.</p>
        <?php endif; ?>
        <a href="../index.php" class="back-to-map-btn">Вернуться на карту</a>
    </div>

    <?php require_once "../html_inc/footer.php"; ?>
</body>
</html>
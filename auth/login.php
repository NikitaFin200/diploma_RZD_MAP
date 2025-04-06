<?php
require_once __DIR__ . "/../PDOEngine.php"; // Подключаем файл с подключением к базе данных
session_start();

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['user'])) {
    header("Location: ../dashboard.php");
    exit;
}

// Обработка данных из формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Проверяем, что username и password не пустые
    if (empty($username) || empty($password)) {
        $error = "Пожалуйста, введите логин и пароль.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result(); // Получаем результат запроса
            $user = $result->fetch_assoc(); // Извлекаем данные пользователя

            // Если пользователь найден и пароль совпадает
            if ($user && $password == $user['password']) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['is_admin'] = (bool)$user['admin']; // Сохраняем роль admin
                $_SESSION['is_worker'] = (bool)$user['worker']; // Сохраняем роль worker
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Неверный логин или пароль.";
            }

            $stmt->close();
        } else {
            $error = "Ошибка запроса к базе данных.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" href="/../css/style.css"> <!-- Подключение CSS -->
</head>
<body_auth>
    <div class="login-container">
        <h2>Авторизация</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Логин:</label>
            <input type="text" id="username" name="username" placeholder="Введите логин" required>
            
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" placeholder="Введите пароль" required>
            
            <button type="submit">Войти</button>
        </form>
    </div>
</body_auth>
</html>
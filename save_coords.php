<?php
session_start();  // Стартуем сессию

require_once "PDOEngine.php";

// Проверка авторизации
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Только авторизованные пользователи могут добавлять точки']);
    exit;
}

// Проверка данных из POST-запроса
$pointer_x = $_POST['pointer_x'] ?? null;
$pointer_y = $_POST['pointer_y'] ?? null;

if ($pointer_x === null || $pointer_y === null) {
    echo json_encode(['success' => false, 'error' => 'Некорректные координаты']);
    exit;
}

try {
    // Подготовка SQL-запроса для вставки данных
    $stmt = $conn->prepare("INSERT INTO coord (pointer_x, pointer_y) VALUES (?, ?)");
    if (!$stmt) {
        throw new Exception("Ошибка подготовки запроса: " . $conn->error);
    }

    // Привязка параметров и выполнение запроса
    $stmt->bind_param("ii", $pointer_x, $pointer_y);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Ошибка выполнения запроса: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>

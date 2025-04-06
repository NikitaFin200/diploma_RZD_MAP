<?php
session_start();
require_once "PDOEngine.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Только авторизованные пользователи могут добавлять точки']);
    exit;
}

$pointer_x = $_POST['pointer_x'] ?? null;
$pointer_y = $_POST['pointer_y'] ?? null;
$name = $_POST['name'] ?? '';

if ($pointer_x === null || $pointer_y === null || empty($name)) {
    echo json_encode(['success' => false, 'error' => 'Некорректные данные']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO coord (pointer_x, pointer_y, name) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Ошибка подготовки запроса: " . $conn->error);
    }

    $stmt->bind_param("dds", $pointer_x, $pointer_y, $name);
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

<?php
session_start();  // Стартуем сессию
require_once "PDOEngine.php";

// Получение всех координат из базы данных
$query = "SELECT pointer_x, pointer_y FROM coord";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $coordinates = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $coordinates = [];
}

$map_width = 1000; // Задайте ширину карты (в пикселях)
$map_height = 600; // Задайте высоту карты (в пикселях)
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Карта ЖД станций</title>
    <link rel="icon" href="/img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php require_once "html_inc/header.php"; ?>
</head>
<body>
    <!-- Карта -->
    <div class="content">
        <div class="map-container" id="map-container">
            <img src="files/карта жд станций.png" alt="Карта ЖД станций" class="main-image" id="map">
            <?php foreach ($coordinates as $coord): ?>
                <!-- Преобразуем пиксельные координаты в проценты для отображения -->
                <div class="dot" 
                     data-x="<?= $coord['pointer_x'] ?>" 
                     data-y="<?= $coord['pointer_y'] ?>"></div>
            <?php endforeach; ?>
        </div>

        <?php if (isset($_SESSION['user'])): ?>
        <!-- Кнопка добавления точки, доступная только авторизованным пользователям -->
        <?php endif; ?>
    </div>

    <!-- Скрипт для обработки кликов -->
    <script>
    $(document).ready(function () {
        const map = $('#map');
        const mapContainer = $('#map-container');
        const addPointBtn = $('#addPointBtn');

        // Получаем реальные размеры карты на экране
        const mapWidth = map.width();
        const mapHeight = map.height();

        // Расставляем точки при загрузке страницы
        $('.dot').each(function () {
            const pixelX = $(this).data('x');
            const pixelY = $(this).data('y');

            // Преобразуем пиксели в проценты относительно текущих размеров карты
            const percentX = (pixelX / mapWidth) * 100;
            const percentY = (pixelY / mapHeight) * 100;

            // Преобразуем проценты в пиксели для отображения
            const left = (percentX / 100) * mapWidth;
            const top = (percentY / 100) * mapHeight;

            $(this).css({
                left: `${left}px`,
                top: `${top}px`
            });
        });

        // Обработка кликов по карте
        map.on('click', function (event) {
            // Проверяем, авторизован ли пользователь
            <?php if (!isset($_SESSION['user'])): ?>
                alert('Вы должны быть авторизованы для добавления точки.');
                return;
            <?php endif; ?>

            // Получаем размеры и положение карты относительно окна
            const rect = map[0].getBoundingClientRect();

            // Рассчитываем координаты клика с учётом размеров изображения
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;

            // Преобразуем координаты в проценты
            const percentX = (x / mapWidth) * 100;
            const percentY = (y / mapHeight) * 100;

            // AJAX-запрос для отправки данных
            $.ajax({
                url: 'save_coords.php',
                type: 'POST',
                data: {
                    pointer_x: Math.round(x),  // Пиксельные координаты для сохранения
                    pointer_y: Math.round(y)
                },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        // Добавляем точку на карту
                        const dot = $('<div class="dot"></div>').css({
                            left: `${percentX}%`,  // Отображение точки в процентах
                            top: `${percentY}%`
                        });
                        mapContainer.append(dot);
                    } else {
                        alert('Ошибка: ' + data.error);
                    }
                },
                error: function () {
                    alert('Ошибка при отправке данных.');
                }
            });
        });
    });
    </script>

</body>
<?php require_once "html_inc/footer.php"; ?>
</html>

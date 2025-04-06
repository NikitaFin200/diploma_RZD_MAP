<?php
session_start();
require_once "PDOEngine.php";

$query = "
    SELECT 
        c.id,
        c.pointer_x,
        c.pointer_y,
        c.name,
        cond.max_temperature,
        cond.precipitation,
        cond.wind_speed,
        cond.pressure,
        cond.update_datetime
    FROM coord c
    LEFT JOIN coonditions_weather_station cond
        ON c.id = cond.station_id
";

$result = $conn->query($query);
$coordinates = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Карта ЖД станций</title>
    <link rel="icon" href="/img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modal.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php require_once "html_inc/header.php"; ?>
    <style>
/* Точки */
.station-container, .dot {
    position: absolute; /* Абсолютное позиционирование для карты */
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: red;
    cursor: pointer;
    transform: translate(-50%, -50%);
    z-index: 10; /* Базовый z-index точек выше карты */
}

/* Тултип */
.station-tooltip {
    position: absolute;
    background-color: #333;
    color: white;
    padding: 0;
    border-radius: 8px;
    font-size: 12px;
    white-space: nowrap;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s ease;
    z-index: 1000; /* Тултип выше базового уровня точек */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

/* Верхняя часть тултипа */
.station-tooltip::before {
    content: attr(data-name) "\A" attr(data-date);
    display: block;
    background-color: #444;
    padding: 5px 10px;
    border-radius: 8px 8px 0 0;
    font-size: 14px;
    font-weight: bold;
    white-space: pre-wrap;
    border-bottom: 1px solid #555;
}

/* Основная часть тултипа */
.station-tooltip .weather-data {
    padding: 8px 10px;
    font-size: 12px;
    line-height: 1.4;
}

/* Ховер-эффект */
.station-container:hover,
.dot:hover {
    z-index: 2000; /* Поднимаем точку с тултипом выше всех остальных */
}

.station-container:hover .station-tooltip,
.dot:hover .station-tooltip {
    opacity: 1;
    z-index: 1000; /* Убеждаемся, что тултип остаётся выше базовых точек */
}

    </style>
</head>
<body>
    <div class="content">
        <div class="map-container" id="map-container">
            <img src="files/карта жд станций.png" alt="Карта ЖД станций" class="main-image" id="map">
            <?php foreach ($coordinates as $coord): ?>
    <div class="station-container" 
         data-percent-x="<?= $coord['pointer_x'] ?>" 
         data-percent-y="<?= $coord['pointer_y'] ?>" 
         data-name="<?= htmlspecialchars($coord['name']) ?>">
        <div class="station-tooltip" 
             data-name="<?= htmlspecialchars($coord['name']) ?>" 
             data-date="<?= $coord['update_datetime'] ? htmlspecialchars($coord['update_datetime']) : 'Нет данных' ?>">
            <div class="weather-data">
                <?php if ($coord['update_datetime']): ?>
                    Температура: <?= $coord['max_temperature'] !== null ? htmlspecialchars($coord['max_temperature']) . ' °C' : 'N/A' ?><br>
                    Осадки: <?= $coord['precipitation'] !== null ? htmlspecialchars($coord['precipitation']) . ' мм' : 'N/A' ?><br>
                    Ветер: <?= $coord['wind_speed'] !== null ? htmlspecialchars($coord['wind_speed']) . ' м/с' : 'N/A' ?><br>
                    Давление: <?= $coord['pressure'] !== null ? htmlspecialchars($coord['pressure']) . ' гПа' : 'N/A' ?>
                <?php else: ?>
                    Нет погодных данных
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
        </div>
    </div>

    <script>
   $(document).ready(function () {
    const map = $('#map');
    const mapContainer = $('#map-container');
    const modal = $('#station-modal');
    const stationNameInput = $('#station-name-input');
    const saveStationBtn = $('#save-station-btn');
    const cancelStationBtn = $('#cancel-station-btn');

    function positionDots() {
        const mapWidth = map.width();
        const mapHeight = map.height();

        $('.station-container').each(function () {
            const percentX = $(this).data('percent-x');
            const percentY = $(this).data('percent-y');

            const left = (percentX / 100) * mapWidth;
            const top = (percentY / 100) * mapHeight;

            $(this).css({ 
                left: `${left}px`, 
                top: `${top}px` 
            });

            const $tooltip = $(this).find('.station-tooltip');
            $tooltip.css({
                left: '15px',
                top: '-50%'
            });
        });
    }

    positionDots();
    $(window).resize(positionDots);

    map.on('click', function (event) {
        <?php if (!isset($_SESSION['user'])): ?>
            alert('Вы должны быть авторизованы для добавления точки.');
            return;
        <?php elseif (!$_SESSION['is_admin']): ?>
            alert('Только администраторы могут добавлять точки.');
            return;
        <?php endif; ?>

        const rect = map[0].getBoundingClientRect();
        const percentX = ((event.clientX - rect.left) / rect.width) * 100;
        const percentY = ((event.clientY - rect.top) / rect.height) * 100;

        modal.css('display', 'flex');
        stationNameInput.val('');
        stationNameInput.focus();

        saveStationBtn.off('click').on('click', function () {
            const stationName = stationNameInput.val().trim();
            if (!stationName) {
                alert('Введите название станции!');
                return;
            }

            $.ajax({
                url: 'save_coords.php',
                type: 'POST',
                data: { pointer_x: percentX, pointer_y: percentY, name: stationName },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        const dot = $(`
                            <div class="station-container" 
                                 data-name="${stationName}" 
                                 data-percent-x="${percentX}" 
                                 data-percent-y="${percentY}">
                                <div class="station-tooltip" 
                                     data-name="${stationName}" 
                                     data-date="Нет данных">
                                    <div class="weather-data">
                                        Нет погодных данных
                                    </div>
                                </div>
                            </div>
                        `);
                        mapContainer.append(dot);
                        positionDots();
                        modal.css('display', 'none');
                    } else {
                        alert('Ошибка: ' + data.error);
                    }
                },
                error: function () {
                    alert('Ошибка при отправке данных.');
                }
            });
        });

        cancelStationBtn.off('click').on('click', function () {
            modal.css('display', 'none');
        });

        stationNameInput.off('keypress').on('keypress', function (e) {
            if (e.which === 13) {
                saveStationBtn.click();
            }
        });
    });

    // Обработчики кнопок администратора
    $('#update-data-btn').on('click', function () {
        alert('Функция обновления данных пока не реализована.');
        // Здесь можно добавить AJAX-запрос для обновления данных
    });

    $('#create-event-btn').on('click', function () {
        alert('Функция создания мероприятия пока не реализована.');
        // Здесь можно добавить модальное окно или форму для создания мероприятия
    });
});
    </script>


<!-- Модальное окно -->
<div id="station-modal" class="modal">
    <div class="modal-content">
        <h2>Добавить станцию</h2>
        <input type="text" id="station-name-input" placeholder="Введите название станции">
        <button id="save-station-btn">Сохранить</button>
        <button id="cancel-station-btn" class="cancel-btn">Отмена</button>
    </div>
</div>

</body>
<?php require_once "html_inc/footer.php"; ?>
</body>
<?php require_once "html_inc/footer.php"; ?>
</html>
<?php
    /**
     * User: nivc_ParamonovaSA
     * Date: 12.04.2024
     * Time: 10:39
     */
 
/*$headerMenu = empty(APIUser::isAuth()) ? [
    ["path" => "main", "title" => "Главная"],
    ["path" => "auth", "title" => "Авторизация"],
] : array_filter([
    ["path" => "main", "title" => "Главная"],
    ["path" => "nsi", "title" => "Администрирование", "roles" => ["admin","user"]],
    ["path" => "profile", "title" => "Профиль"],
    ["path" => "exit", "title" => "Выход"],
], function ($row) {
    return empty($row["roles"]) || in_array(APIUser::getRole(), $row["roles"]);
});
$hdrPageName = getPageName();
*/
?>
<head>
<link rel="stylesheet" href="../css/header.css">
</head>
<?php
session_start(); // Стартуем сессию
?>

<div id="header" class="header">
    <div class="header_rzd">
        <!-- Логотип или элементы оформления -->
    </div>
    <div class="site_label">
        <span class="title">
            Карта погодных условий
        </span>
    </div>
    <div class="site_menu">
        <?php if (isset($_SESSION['user'])): ?>
            <!-- Меню для авторизованных пользователей -->
            <a href="../auth/dashboard.php">Личный кабинет</a> |
            <a href="../auth/logout.php">Выйти</a>
        <?php else: ?>
            <!-- Кнопка входа для неавторизованных пользователей -->
            <a href="auth/login.php" class="btn-auth">Войти</a>
        <?php endif; ?>
    </div>
</div>

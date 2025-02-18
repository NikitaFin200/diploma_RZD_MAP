<?php 
$servername = "localhost";
$username = "root";
$password = ""; // Укажите ваш пароль
$dbname = "map";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

//$mysql->query("SELECT * FROM 'hello'")
?>
<?php
$host = 'localhost';
$db = 'elvicky';
$user = 'your_db_user';
$pass = 'your_db_password';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

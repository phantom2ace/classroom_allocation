<?php
$host = 'localhost';
$db = 'elvicky';
$pass = 'EllisPass2024#';
$user = 'root';


$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
session_start();
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $classroom_id = $_POST['classroom_id'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $purpose = $_POST['purpose'];

    $stmt = $conn->prepare("SELECT id FROM bookings WHERE classroom_id = ? AND booking_date = ? AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?))");
    $stmt->bind_param("isssss", $classroom_id, $booking_date, $end_time, $start_time, $start_time, $end_time);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, classroom_id, booking_date, start_time, end_time, purpose) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $user_id, $classroom_id, $booking_date, $start_time, $end_time, $purpose);
        $stmt->execute();
        echo "Booking successful.";
    } else {
        echo "The selected time slot is already booked.";
    }
    $stmt->close();
}
?>

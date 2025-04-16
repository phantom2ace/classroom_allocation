<?php
session_start();
include('db_connect.php');

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Ensure user is logged in before proceeding
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// CSRF protection (Ensure the token is set)
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token mismatch');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizing inputs to avoid harmful content
    $user_id = $_SESSION['user_id'];
    $classroom_id = filter_var($_POST['classroom_id'], FILTER_SANITIZE_NUMBER_INT);
    $booking_date = filter_var($_POST['booking_date'], FILTER_SANITIZE_STRING);
    $start_time = filter_var($_POST['start_time'], FILTER_SANITIZE_STRING);
    $end_time = filter_var($_POST['end_time'], FILTER_SANITIZE_STRING);
    $purpose = htmlspecialchars($_POST['purpose']); // Escaping special characters in the purpose

    // Check if the classroom is available for the selected time slot
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE classroom_id = ? AND booking_date = ? AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?))");
    $stmt->bind_param("isssss", $classroom_id, $booking_date, $end_time, $start_time, $start_time, $end_time);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // Classroom is available, insert the booking into the database
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, classroom_id, booking_date, start_time, end_time, purpose) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $user_id, $classroom_id, $booking_date, $start_time, $end_time, $purpose);

        if ($stmt->execute()) {
            // Booking successful, redirect to the success page
            header("Location: booking_success.php");
            exit();
        } else {
            // Database error, redirect back with error message
            $_SESSION['error_message'] = "❌ Error while booking the classroom. Please try again.";
            header("Location: book_classroom_form.php");
            exit();
        }
    } else {
        // Time slot already booked, redirect back with error message
        $_SESSION['error_message'] = "The selected time slot is already booked.";
        header("Location: book_classroom_form.php");
        exit();
    }
    $stmt->close();
}
?>

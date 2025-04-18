<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input data
    $name = $_POST['fullName'];
    $email = $_POST['email'];
    $studentId = $_POST['studentId'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = 'user';  // Default role

    // Input validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "❌ Invalid email format.";
        exit();
    }

    if ($password !== $confirmPassword) {
        echo "❌ Passwords do not match.";
        exit();
    }

    if (strlen($password) < 8) {
        echo "❌ Password must be at least 8 characters.";
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "❌ Email is already registered.";
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email, student_id, course, year, password_hash, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiss", $name, $email, $studentId, $course, $year, $passwordHash, $role);

    if ($stmt->execute()) {
        header("Location: book_classroom.php");
        exit();
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

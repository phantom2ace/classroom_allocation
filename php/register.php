<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'user'; // Default role

    // Input validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "❌ Invalid email format.";
        exit();
    }

    if (strlen($password) < 6) {
        echo "❌ Password must be at least 6 characters.";
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
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $passwordHash, $role);

    if ($stmt->execute()) {
        echo "✅ Registration successful. Redirecting to login...";
        header("refresh:2;url=login.php");  // Redirect to login page after 2 seconds
        exit();
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

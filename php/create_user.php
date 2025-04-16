<?php
// Database configuration (stored securely in an external file or environment variables)
$host = 'localhost';
$user = 'root';
$pass = 'EllisPass2024#';
$db = 'elvicky';

// Create a new connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check for a successful connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hardcoded user details (for this example)
$name = 'Ellis';
$email = 'ellis@example.com';
$password = password_hash('test1234', PASSWORD_DEFAULT); // Hash the password
$role = 'admin';

// Prepare the SQL statement to insert the user into the database
$stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $role);

// Try to execute the statement and handle the result
if ($stmt->execute()) {
    echo "✅ User created successfully.";
} else {
    // In case of an error, provide a more detailed message
    error_log("Error while creating user: " . $stmt->error); // Log the error for debugging
    echo "❌ Error: Unable to create user at this time. Please try again later.";
}

// Close the prepared statement and the database connection
$stmt->close();
$conn->close();
?>

<?php
// Database credentials as constants (for better maintainability)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'EllisPass2024#');
define('DB_NAME', 'elvicky');

// Establish the database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for a connection error
if ($conn->connect_error) {
    // Log the error with detailed information to a secure log file
    error_log("Connection failed: " . $conn->connect_error, 3, "/path/to/secure/log/error_log.txt");
    
    // Provide a general error message to the user
    die("Connection failed. Please try again later.");
}
?>

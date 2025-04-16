<?php
session_start(); // Start the session

// Ensure the database connection is established
if (!isset($conn) || !$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Reset Password Request (When user submits email to request reset)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitize email input

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        echo "❌ Database error: " . $stmt->error;
        exit();
    }
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Generate reset token
        $token = bin2hex(random_bytes(16));

        // Insert token into the database with an expiration time of 1 hour
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = NOW() + INTERVAL 1 HOUR WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        if (!$stmt->execute()) {
            echo "❌ Database error: " . $stmt->error;
            exit();
        }

        // Send reset email with the token
        $resetLink = "https://yourdomain.com/reset.php?token=$token";
        if (!mail($email, "Password Reset Request", "Click here to reset your password: $resetLink")) {
            error_log("Failed to send password reset email to $email");
            echo "❌ Error sending reset email. Please try again later.";
            exit();
        }

        header("Location: reset-request-confirmation.php");
        exit();
    } else {
        echo "This email is not registered.";
    }
    $stmt->close();
}

// Reset Password via Token (When user submits new password)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'], $_GET['token'])) {
    $newPassword = $_POST['new_password'];
    $token = $_GET['token'];  // Token passed via URL

    // Validate the new password (ensure it meets your complexity requirements)
    if (strlen($newPassword) < 8) {
        echo "Password should be at least 8 characters long.";
        exit();
    }

    // Ensure the password contains at least one uppercase letter, one number, and one special character
    if (!preg_match("/[A-Z]/", $newPassword) || !preg_match("/[0-9]/", $newPassword) || !preg_match("/[\W_]/", $newPassword)) {
        echo "Password must contain at least one uppercase letter, one number, and one special character.";
        exit();
    }

    // Validate the token
    $stmt = $conn->prepare("SELECT id, token_expiry FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    if (!$stmt->execute()) {
        echo "❌ Database error: " . $stmt->error;
        exit();
    }
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Token found, now check if it has expired
        $stmt->bind_result($userId, $tokenExpiry);
        $stmt->fetch();

        if (time() > strtotime($tokenExpiry)) {
            echo "The reset token has expired. Please request a new one.";
            exit();
        }

        // Token is valid, proceed with password reset
        // Hash the new password securely
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password, and clear the reset token and expiry
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
        $stmt->bind_param("si", $newPasswordHash, $userId);
        if (!$stmt->execute()) {
            echo "❌ Error resetting your password. Please try again later.";
            exit();
        }

        // Log the user in automatically
        session_unset();
        session_destroy();
        session_start(); // Start a new session
        $_SESSION['user_id'] = $userId;
        session_regenerate_id(true); // Secure the session ID

        $_SESSION['password_reset_success'] = "Your password has been successfully reset.";
        header("Location: dashboard.php"); // Redirect to a secure page (dashboard)
        exit();
    } else {
        echo "Invalid reset token.";
    }
    $stmt->close();
}
?>

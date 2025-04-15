<?php
session_start();
$host = 'localhost';
$user = 'root';
$pass = 'EllisPass2024#';
$db = 'elvicky';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password_hash, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $password_hash, $role);
        $stmt->fetch();
        if (password_verify($password, $password_hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            echo "✅ Login successful. Welcome $role!";
        } else {
            echo "❌ Invalid password.";
        }
    } else {
        echo "❌ No user found with that email.";
    }
    $stmt->close();
}
?>

<!-- Simple Login Form -->
<form method="POST" action="">
    <h2>Login</h2>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Login</button>
</form>

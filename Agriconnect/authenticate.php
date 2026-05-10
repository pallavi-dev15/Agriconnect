<?php
session_start();
require 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$usertype = $_POST['usertype'] ?? '';

if (empty($username) || empty($password) || empty($usertype)) {
    header("Location: login.php?error=1");
    exit();
}

// Query database for user
$stmt = $conn->prepare("SELECT id, username, password, usertype FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verify password (using password_verify for security)
    if (password_verify($password, $user['password']) && $user['usertype'] === $usertype) {
        // Login successful
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['usertype'] = $user['usertype'];

        setcookie("user", $user['username'], time() + 3600, "/");
        setcookie("usertype", $user['usertype'], time() + 3600, "/");

        header("Location: dashboard.php");
        exit();
    } else {
        // Password mismatch or user type mismatch
        header("Location: login.php?error=1");
        exit();
    }
} else {
    // User not found
    header("Location: login.php?error=1");
    exit();
}

$stmt->close();
$conn->close();
?>
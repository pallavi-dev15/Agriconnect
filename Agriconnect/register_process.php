<?php
session_start();
require 'db.php';

// Get form data
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$phone = $_POST['phone'] ?? '';
$location = $_POST['location'] ?? '';
$usertype = $_POST['usertype'] ?? '';

// Validate inputs
if (empty($fullname) || empty($email) || empty($username) || empty($password) || empty($confirm_password) || empty($phone) || empty($location) || empty($usertype)) {
    header("Location: register.php?error=1");
    exit();
}

// Check if passwords match
if ($password !== $confirm_password) {
    header("Location: register.php?error=password_mismatch");
    exit();
}

// Check if username already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: register.php?error=username_exists");
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: register.php?error=email_exists");
    exit();
}

// Hash password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new user into database
$stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password, phone, location, usertype, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("sssssss", $fullname, $email, $username, $hashed_password, $phone, $location, $usertype);

if ($stmt->execute()) {
    header("Location: login.php?success=1");
    exit();
} else {
    header("Location: register.php?error=1");
    exit();
}

$stmt->close();
$conn->close();
?>

<?php
session_start();

$valid_username = "farmer1";
$valid_password = "1234";

$username = $_POST['username'];
$password = $_POST['password'];

if ($username === $valid_username && $password === $valid_password) {
    
    
    $_SESSION['username'] = $username;

    
    setcookie("user", $username, time() + 3600, "/");

    
    header("Location: dashboard.php");
    exit();

} else {
    
    header("Location: login.php?error=1");
    exit();
}
?>
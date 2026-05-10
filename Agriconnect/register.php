<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register - Smart AgriConnect</title>
    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: linear-gradient(to right, #4CAF50, #2E7D32);
        }

        .register-box {
            width: 400px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        h2 {
            color: #2E7D32;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .radio-group {
            margin: 20px 0;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .radio-group label {
            display: inline-block;
            margin-right: 30px;
            font-weight: normal;
            color: #333;
            cursor: pointer;
        }

        input[type="radio"] {
            margin-right: 8px;
            cursor: pointer;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
        }

        button:hover {
            background: #2E7D32;
        }

        .error-message {
            color: red;
            margin-top: 15px;
            padding: 10px;
            background: #ffe6e6;
            border-radius: 5px;
        }

        .success-message {
            color: green;
            margin-top: 15px;
            padding: 10px;
            background: #e6ffe6;
            border-radius: 5px;
        }

        .login-link {
            margin-top: 20px;
            text-align: center;
        }

        .login-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="register-box">
    <h2>Create Your Account</h2>
    <p style="color: #666; margin-bottom: 25px;">Join AgriConnect Today</p>

    <form action="register_process.php" method="POST">
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Choose a username" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter a strong password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
        </div>

        <div class="form-group">
            <label for="location">Location/City</label>
            <input type="text" id="location" name="location" placeholder="Enter your city or location" required>
        </div>

        <div class="form-group">
            <label>I am registering as:</label>
            <div class="radio-group" style="padding: 15px; background: #f5f5f5;">
                <label><input type="radio" name="usertype" value="farmer" required> Farmer</label>
                <label><input type="radio" name="usertype" value="buyer" required> Buyer</label>
            </div>
        </div>

        <button type="submit">Create Account</button>
    </form>

    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <?php
    if(isset($_GET['error'])){
        if($_GET['error'] == 'username_exists'){
            echo "<p class='error-message'>Username already exists. Please choose a different one.</p>";
        } else if($_GET['error'] == 'email_exists'){
            echo "<p class='error-message'>Email already registered. Please use a different email.</p>";
        } else if($_GET['error'] == 'password_mismatch'){
            echo "<p class='error-message'>Passwords do not match.</p>";
        } else {
            echo "<p class='error-message'>Registration failed. Please try again.</p>";
        }
    }
    if(isset($_GET['success'])){
        echo "<p class='success-message'>Account created successfully! You can now login.</p>";
    }
    ?>
</div>

</body>
</html>

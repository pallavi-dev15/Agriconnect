<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - Smart AgriConnect</title>
    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: linear-gradient(to right, #4CAF50, #2E7D32);
        }

        .login-box {
            width: 350px;
            margin: 120px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
        }

        button {
            width: 95%;
            padding: 10px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: darkgreen;
        }
    </style>
</head>

<body>

<div class="login-box">
    <h2>Login</h2>

    <form action="authenticate.php" method="POST">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>

        <button type="submit">Login</button>
    </form>

    <?php
    if(isset($_GET['error'])){
        echo "<p style='color:red;'>Invalid username or password</p>";
    }
    ?>
</div>

</body>
</html>
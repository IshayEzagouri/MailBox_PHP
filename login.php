<?php
session_start();
include "mysql_conn.php";
include "brain.php";

$mysql_obj = new mysql_conn();
$mysql = $mysql_obj->GetConn();
$users_obj = new brain($mysql);

$rightPass = "AAA";

$maxLoginAttempts = 5; // Brute force
$lockoutDuration = 1800;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password'])) {
        $password = $_POST['password'];

        if ($password === $rightPass) {
            $_SESSION['login_attempts'] = 0;
            
            // Generate CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header("location: homePage.php");
           
        } else {
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 1;
            } else {
                $_SESSION['login_attempts']++;
            }

            if ($_SESSION['login_attempts'] >= $maxLoginAttempts) {
                $_SESSION['lockout_time'] = time();
                echo "Your account is locked. Please try again later.";
               
            } else {
                echo "Incorrect password. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
        }

        .container h2 {
            margin-bottom: 20px;
        }

        .container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .container button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>

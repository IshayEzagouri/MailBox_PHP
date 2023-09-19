<?php
session_start();

include "mysql_conn.php";
include "brain.php";

$mysql_obj = new mysql_conn();
$mysql = $mysql_obj->GetConn();
$brain_obj = new Brain($mysql);

$numOfUsers = $brain_obj->getNumOfUsers($mysql);

// XSS defense function
function specialCharsForInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Input validation function
function containsOnlyLetters($str) {
    for ($i = 0; $i < strlen($str); $i++) {
        $char = $str[$i];
        if (!($char >= 'a' && $char <= 'z') && !($char >= 'A' && $char <= 'Z')) {
            return false;
        }
    }
    return true;
}


$name = $mailbox_number = $phone_number = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF token
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        // XSS defence
        $name = specialCharsForInput($_POST["name"]);
        $mailbox_number = specialCharsForInput($_POST["mailbox_number"]);
        $phone_number = specialCharsForInput($_POST["phone_number"]);

        // Input validation 
        if (empty($name) || !containsOnlyLetters($name)) {
            echo '<script>alert("Name should contain only letters.");</script>'; 
        } elseif (empty($mailbox_number) || empty($phone_number) || containsOnlyLetters($phone_number) || containsOnlyLetters($mailbox_number)) {
            echo '<script>alert("Mailbox Number and Phone Number should contain only numbers.");</script>'; 
        } else {
            // SQL Injection protection 
            $params = [
                'name' => addslashes($name),
                'mailbox_number' => addslashes($mailbox_number),
                'phone_number' => addslashes($phone_number),
            ];

            $created = $brain_obj->CreateUser($params);

            if ($created) {
                echo '<script>alert("User created successfully.");</script>';
            } else {
                echo '<script>alert("Error creating user.");</script>';
            }
        }
    } else {
        echo '<script>alert("CSRF token validation failed.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            color: #007BFF;
        }

        .center-form {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .center-form input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 16px;
            margin-right: 10px;
            width: 200px;
        }

        .center-form button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .go-back-button {
            margin-top: 20px; 
        }
    </style>
</head>
<body>
    <h2>Add User</h2>

    <form method="post">
        <div class="center-form">
            <input type="text" placeholder="<?php echo $numOfUsers + 1; ?>" readonly>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" name="name" placeholder="Name">
            <input type="text" name="mailbox_number" placeholder="Mailbox Number">
            <input type="text" name="phone_number" placeholder="Phone Number">
            <button type="submit">Add</button>
        </div>
    </form>

    <a href="homePage.php" class="home-button">
        <button>Main Page</button>
    </a>
</body>
</html>

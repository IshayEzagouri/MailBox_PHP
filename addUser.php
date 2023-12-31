<?php
session_start();

include "mysql_conn.php";
include "brain.php";

$mysql_obj = new mysql_conn();
$mysql = $mysql_obj->GetConn();
$brain_obj = new Brain($mysql);
$numOfUsers=$brain_obj->getNumOfUsers($mysql);
$user_id = "";
$user_name = "";
$user_mailbox_number = "";
$user_phone_number = "";

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $sql = "SELECT name, mailbox_number, phone_number FROM users WHERE id = $user_id";
    $result = mysqli_query($mysql, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        $user_name = $row['name'];
        $user_mailbox_number = $row['mailbox_number'];
        $user_phone_number = $row['phone_number'];
    } else {
        echo "Error: " . mysqli_error($mysql);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF token
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        //xss
        $new_name = htmlspecialchars($_POST["name"]);
        $new_mailbox_number = htmlspecialchars($_POST["mailbox_number"]);
        $new_phone_number = htmlspecialchars($_POST["phone_number"]);

        // Input validation
        if (empty($new_name) || !$brain_obj->containsOnlyLetters($new_name)) {
            echo '<script>alert("Name should contain only letters.");</script>';
        } elseif (empty($new_mailbox_number) || empty($new_phone_number) || !$brain_obj->containsOnlyNumbers($new_mailbox_number) || !$brain_obj->containsOnlyNumbers($new_phone_number)) {
            echo '<script>alert("Mailbox Number and Phone Number should contain only numbers.");</script>';
        } else {
            // sql injection
            $update_params = [
                'id' => $user_id, //non changable, auto increment
                'name' => addslashes($new_name),
                'mailbox_number' => addslashes($new_mailbox_number),
                'phone_number' => addslashes($new_phone_number),
            ];

            $updated = $brain_obj->CreateUser($update_params);

            if ($updated) {
                header("Location: addUser.php");

            } else {
                echo '<script>alert("Error updating user.");</script>';
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

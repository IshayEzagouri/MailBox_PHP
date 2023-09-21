<?php
session_start();

include "mysql_conn.php";
include "brain.php";

$mysql_obj = new mysql_conn();
$mysql = $mysql_obj->GetConn();
$brain_obj = new Brain($mysql);

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
        // xss
        $new_name = htmlspecialchars($_POST['name']);
        $new_mailbox_number = htmlspecialchars($_POST['mailbox_number']);
        $new_phone_number = htmlspecialchars($_POST['phone_number']);

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

            $updated = $brain_obj->UpdateUser($update_params);

            if ($updated) {
                header("Location: homePage.php");
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
    <title>Edit User</title>
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

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 16px;
            margin: 5px;
            width: 300px;
        }

        button[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 10px;
        }

        a.button {
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border-radius: 3px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Edit User</h2>
    
    <form method="post" action="editUser.php?id=<?php echo $user_id; ?>">
        <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>"> <!-- xss !-->
        <input type="text" name="mailbox_number" placeholder="Mailbox Number" value="<?php echo htmlspecialchars($user_mailbox_number, ENT_QUOTES, 'UTF-8'); ?>"> <!-- xss !-->
        <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo htmlspecialchars($user_phone_number, ENT_QUOTES, 'UTF-8'); ?>"> <!-- xss !-->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"> <!-- CSRF token !-->
        <button type="submit">Save Changes</button>
    </form>

    <a href="homePage.php" class="button">Back to Homepage</a>
</body>
</html>

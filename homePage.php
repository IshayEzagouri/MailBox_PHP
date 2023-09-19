<?php

session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
}

include "mysql_conn.php";
include "brain.php"; 

$mysql_obj = new mysql_conn();
$mysql = $mysql_obj->GetConn();
$brain_obj = new Brain($mysql);

if (isset($_POST['delete']) && isset($_POST['id'])) {
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        $user_id = $_POST['id'];

        $deleted = $brain_obj->DeleteUser($user_id);

        if ($deleted) {
            header("location: homePage.php");
        } else {
            echo "Error deleting user.";
        }
    } else {
        echo "token validation failed."; 
    }
}

$sql = "SELECT * FROM users";
$result = mysqli_query($mysql, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
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

        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .center-form {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .center-form button {
            margin: 0 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 10px 20px;
            cursor: pointer;
        }

        input[type="number"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h2>User List</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Mailbox Number</th>
                <th>Phone Number</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td> <!-- xss -->
                    <td><?php echo htmlspecialchars($row['name']); ?></td> <!-- xss -->
                    <td><?php echo htmlspecialchars($row['mailbox_number']); ?></td><!-- xss -->
                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td><!-- xss -->
                    <td><button>Edit</button></td>
                    <td>
                        <form method="post" action="homePage.php">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit" name="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="center-form">
        <button>Add</button>
    </div>
</body>
</html>

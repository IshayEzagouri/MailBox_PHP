<?php


class brain {
    private $mysql;

    function __construct($conn) {
        $this->mysql = $conn;
    }

    public function CreateUser($params) {
        $name = isset($params['name']) ? $params['name'] : "";
        $mailbox_number = isset($params['mailbox_number']) ? $params['mailbox_number'] : "";
        $phone_number = isset($params['phone_number']) ? $params['phone_number'] : "";

        $q = "INSERT INTO `users` (name, mailbox_number, phone_number) VALUES ('$name', '$mailbox_number', '$phone_number')";
        $result = mysqli_query($this->mysql, $q);
        return $result;
    }

    public function UpdateUser($params) {
        $id = isset($params['id']) ? $params['id'] : -1;
        $name = isset($params['name']) ? $params['name'] : "";
        $mailbox_number = isset($params['mailbox_number']) ? $params['mailbox_number'] : "";
        $phone_number = isset($params['phone_number']) ? $params['phone_number'] : "";

        if ($id > 0) {
            $q = "UPDATE `users` SET ";
            $q .= "name='$name', ";
            $q .= "mailbox_number='$mailbox_number', ";
            $q .= "phone_number='$phone_number' ";
            $q .= "WHERE id=$id";
            $result = mysqli_query($this->mysql, $q);
            return $result;
        }
    }

    public function GetUser($id) {
        $q = "SELECT * FROM `users` WHERE id=$id";
        $result = mysqli_query($this->mysql, $q);
        return mysqli_fetch_assoc($result);
    }

    public function DeleteUser($id) {
        $q = "DELETE FROM `users` WHERE id=$id";
        $result = mysqli_query($this->mysql, $q);

        if ($result) {
            return true; 
        } else {
            return false; 
        }
    }
}

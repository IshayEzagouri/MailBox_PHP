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
    
        $nextID = $this->getNumOfUsers($this->mysql) + 1;
        $insert = "INSERT INTO `users` (id, name, mailbox_number, phone_number) VALUES ('$nextID', '$name', '$mailbox_number', '$phone_number')";
        $result= mysqli_query($this->mysql, $insert);
    
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

  

    public function DeleteUser($id) {
        $delete = "DELETE FROM `users` WHERE id = $id";
        $resultDelete = mysqli_query($this->mysql, $delete);
    
        if (!$resultDelete) {
            return false; 
        }
    
        $update = "UPDATE `users` SET id = id - 1 WHERE id > $id";
        $resultUpdate = mysqli_query($this->mysql, $update);
    
        if ($resultUpdate) {
            return true; 
        } else {
            return false; 
        }
    }
    
    function containsOnlyLetters($str) {
        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
            
            if (!($char >= 'a' && $char <= 'z') && !($char >= 'A' && $char <= 'Z')) {
                return false;
            }
        }
        
        return true;
    }

    function containsOnlyNumbers($str) {
        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
    
            if (!($char >= '0' && $char <= '9')) {
                error_log("containsOnlyNumbers: Invalid character '$char'");
                return false;
            }
        }
    
        return true;
    }
    
    

    public function getNumOfUsers($mysql) {
        $sql = "SELECT COUNT(*) AS count FROM users";
        $result = mysqli_query($mysql, $sql);
        $row = mysqli_fetch_assoc($result);
        return intval($row['count']);
    }

    
    
}

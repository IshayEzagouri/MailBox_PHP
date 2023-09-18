<?php

class users {
    private $mysql;

    function __construct($conn) {
        $this->mysql=$conn;
    }
  

}
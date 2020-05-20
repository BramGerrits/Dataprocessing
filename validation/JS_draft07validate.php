<?php

    include 'draft07validate.php';   
    
    $result = false;
    
    if(isset($_POST["table"]) && isset($_POST["JSON"]))
    {
        $result = JSON_validate($_POST["JSON"], $_POST["table"]);
    }
    
    echo boolval($result); 
?>
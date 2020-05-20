<?php

    include 'XSDvalidate.php';   
    
    $result = false;
    
    if(isset($_POST["table"]) && isset($_POST["XML"]))
    {
        $result = XML_validate($_POST["XML"], $_POST["table"]);
    }

    echo boolval($result);
?>
<?php
    
    /**
    * Collects the body data from the api/server request
    * 
    * @author Bram Gerrits
    * @return The body data
    */ 
    function bodyData()
    {
        $result = null;
        
        switch($_SERVER['REQUEST_METHOD']){
            case "GET":
                //$result = isset($_GET[$name]) ? $_GET[$name] : null;
                $result = $_GET;
                break;
            case "POST":
                //$result = isset($_POST[$name]) ? $_POST[$name] : null;
                $result = $_POST;
                break;
            case "PUT":
            case "DELETE":
                parse_str(file_get_contents("php://input"),$_PUTDELETE);
                //$result = isset($_PUTDELETE[$name]) ? $_PUTDELETE[$name] : null;
                $result = $_PUTDELETE;
                break;  
            
        }
        return $result;
    } 
    
    /**
    * Collects the body data from the api/server request
    * 
    * @param $root The root folder of the api
    * 
    * @author Bram Gerrits
    * @return The uri data
    */ 
    function uriData($root) 
    {
        $uri = str_replace($root, "", $_SERVER['REQUEST_URI']);
        
        $parameters = explode("/", $uri);
        foreach($parameters as $key => $parameter){
            if($parameters[$key] == "")
            {
                unset($parameters[$key]);
            }
        }
        
        return $parameters;
    }

    /**
    * Checks if uri data is valid
    * 
    * @param $ariValue       A ari value
    * @param $possibleValues A list of valid values, which $ariValue must be one of
    * 
    * @author Bram Gerrits
    * @return $ariValue
    */ 
    function validUriParam($ariValue, $possibleValues)
    {
        if(isset($ariValue) && in_array($ariValue, $possibleValues))
        {
            return $ariValue;
        }
        else
        {
            die("Invalid Endpoint: $ariValue");
        }
    }
?>
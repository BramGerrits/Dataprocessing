<?php    
    include 'validation/draft07validate.php';
    include 'validation/XSDvalidate.php';
    
    include 'functions/databaseFunctions.php';
    include 'functions/formatFunctions.php';
    include 'functions/requestDataFunctions.php';
    include 'functions/translateFunctions.php';

    
    /**
    * function for the GET request, collects specified data from the database in the given format
    * 
    * @param $table  The table which data is collected from
    * @param $format The format in which the data is returned in
    * @param $id     An optional identifier for a row of data from the specified table
    * 
    * @author Bram Gerrits
    * @return $result Data in a certain format
    */ 
    function GET($table, $format, $id)
    {
        $result = null;    
        $array = $id == null ? getAll($table) : getValue($table, $id);
        $array = $id == null ? translateValueArray($array, $table) : translateValues($array, $table);
        
        if($format == "json")
        {
            $result = JSON($array);
        } 
        else
        {
            $result = $id == null ? createXMLbyArray($table, $array) : createXmlByValue($array, $table);
        }
        
        return $result;
    }
    
    /**
    * function for the POST request, writes data to the database table
    * 
    * @param $table  The table which data is written to
    * @param $format The format in which the data is written
    * @param $data   The data which will be written to the database table
    * 
    * @author Bram Gerrits
    * @return none
    */ 
    function POST($table, $format, $data)
    {        
        if(isset($data["data"]) && is_array($data))
        {
            $data = str_replace ("_", " ", $data["data"]);
            
            if($format == "json")
            {
                if(JSON_validate($data, $table))
                {
                    $values = json_decode($data);
                    foreach($values->ratings as $values)
                    {
                        addTo($table, json_decode(json_encode($values), true));
                    }
                }   
                else
                {
                    die("Invalid JSON input");
                }
            }
            else
            {
                $xml = XML_giveRoot($data);
                if(XML_validate($xml, $table))
                {
                    $obj = XMLtoValues($xml);
                    foreach($obj as $values)
                    {
                        addTo($table, $values);
                    }
                }  
                else
                {
                    die("Invalid XML input");
                }
            }
        }
    }
    
    /**
    * function for the PUT request, updates data from a database row
    * 
    * @param $table  The table from which data is updated
    * @param $format The format in which the data is written
    * @param $data   The data which will update the existing data
    * @param $id     The identifier for the row which will have its data updated
    * 
    * @author Bram Gerrits
    * @return none
    */ 
    function PUT($table, $format, $data, $id)
    {
        if(isset($id) && isset($data["data"]))
        {
            $data = str_replace ("_", " ", $data["data"]);
            
            if($format == "json")
            {
                if(JSON_validate($data, $table))
                {
                    $jsonArr = json_decode($data);                    
                    $newValues = reset($jsonArr->ratings);
                    $newValues = json_decode(json_encode($newValues), true);

                    updateValue($table, $newValues, $id);
                }  
                else
                {
                    die("Invalid JSON input");
                }
            }
            else
            {
                $xml = XML_giveRoot($data);
                if(XML_validate($xml, $table))
                {
                    $values = XMLtoValues($xml);
                    $values = isset($values[0]) ? $values[0] : $values; //Kijkt of er meerdere elementen zijn opgestuurd.
                    updateValue($table, $values, $id);
                }
                else
                {
                    die("Invalid XML input");
                }
            }
        }
    }
    
    
    
    //Constanten
    const ROOT = "/Dataprocessing/";   
    const TABLES = array("gezondheid", "leefomgeving", "economischerisicos");
    const FILE_TYPES = array("json", "xml");
  
    //Request data
    $uriData = uriData(ROOT); //Ontvang Uri data
    
    //Headers
    $contentType = $uriData[1] == "xml" ? "text/xml" : "application/json";
    header('Content-type: '.$contentType);
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    error_reporting(E_ALL ^ E_WARNING); //Removes error ouput, prevents unpredicted ouput for the user
    
    $table = validUriParam($uriData[0], TABLES);
    $format = validUriParam($uriData[1], FILE_TYPES);
    $id = isset($uriData[2]) ? $uriData[2] : null;    
    $data = bodyData(); //Datafield uit de body van de request. 
    
    connect("127.0.0.1", "root", "", "dataprocessing");

    switch ($_SERVER['REQUEST_METHOD'])
    {
        case "GET":
            echo GET($table, $format, $id);
            break;
        case "POST":
            POST($table, $format, $data);
            break;
        case "PUT":
            PUT($table, $format, $data, $id);
            break;
        case "DELETE":
            deleteValue($id, $table);
            break;
        default:
            echo null;
            break;
    }
?>
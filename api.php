<?php    
    include 'databaseFunctions.php';
    include 'formatFunctions.php';
    include 'validation/draft07validate.php';
    include 'validation/XSDvalidate.php';
    
    include 'requestdata.php';
    
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
    function uriData($root, $parameterNames) 
    {
        $uri = str_replace($root, "", $_SERVER['REQUEST_URI']);
        $uriData = [];
        if($uri != null)
        {
            if(is_array ($parameterNames))
            {

                $parameters = explode("/", $uri);
                foreach($parameters as $key => $parameter){
                    if($parameters[$key] == "")
                    {
                        unset($parameters[$key]);
                    }
                }
                if(count($parameters) === count($parameterNames))
                {
                    for($i = 0; $i < count($parameterNames); $i++)
                    {
                        $uriData[$parameterNames[$i]] = $parameters[$i];
                    }
                }
            }
        }
        return $uriData;
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
        

        if($id == null)
        {
            
            if($format == "json")
            {
                $json = JSON(getAll($table));
                //if(JSON_validate($json, $table)){
                    $result = $json;
                //}
            } 
            else
            {
                $xml = createXMLbyArray($table, getAll($table));
                
                //if(XML_validate($xml, $table)){
                    $result = $xml;
                //}
            }
        }
        else
        {
            if($format == "json")
            {
                $json = JSON(getValue($table, $id));
                //if(JSON_validate($json, $table)){
                    $result = $json;
                //}   
            } 
            else
            {
                $xml = createXMLbyId($table, $id);
                //if(XML_validate($xml, $table)){
                    $result = $xml;
                //}
            }
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
        if($format == "json")
        {
            if(is_array($data) && isset($data))
            {
                //Juiste formaat maken
                $data["id"] = 1;
                $json = JSON($data);
                //Juiste formaat maken
            
                if(JSON_validate($json, $table)){
                    echo "valid";
                    
                    //Data voorbereiden voor database
                    unset($data["id"]);
                    $values = $data;
                    //Data voorbereiden voor database
                    
                    addTo($table, $values);
                }   
            }
        }
        else
        {
            $input = $data;
            if(is_string($input))
            {
                //Juiste formaat maken
                $input = str_replace("<rating>", "<rating id='1'>", $input);
                $xml = XML_wrapRoot($input, $table);
                
                //Juiste formaat maken
                
                if(XML_validate($xml, $table))
                {
                    $values = XMLtoValues($xml);
                    addTo($table, $values );
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
        if(isset($id))
        {
            if($format == "json")
            {
                if(is_array($data) && isset($data))
                {
                    
                    //Juiste formaat maken
                    $data["id"] = 1;
                    $json = JSON($data);
                    //Juiste formaat maken

                    
                    
                    if(JSON_validate($json, $table)){
                        //Data voorbereiden voor database
                        unset($data["id"]);
                        $values = $data;
                        //Data voorbereiden voor database

                        updateValue($table, $values, $id);
                    }  
                    else{
                        die("Invalid JSON input");
                    }
                }
                else
                {
                    die("No JSON input");
                }
            }
            else
            {
                $input = isset(array_keys($data)[0]) ? array_keys($data)[0] : null;
                if(is_string($input))
                {
                    //Juiste formaat maken voor validatie
                    $input = str_replace("<rating>", "<rating id='1'>", $input);
                    $xml = XML_wrapRoot($input, $table);
                    //Juiste formaat maken voor validatie

                    if(XML_validate($xml, $table))
                    {
                        $values = XMLtoValues($xml);
                        updateValue($table, $values, $id);
                    }
                    else{
                        die("Invalid XML input");
                    }
                }
                else 
                {
                    die("Input isn't a XML string or there is no input.");
                }
            }
        }
    }
    
    /**
    * function for the DELETE request, removes a row from a table
    * 
    * @param $table  The table from which data will be removed
    * @param $id     The identifier for the row which will be removed
    * 
    * @author Bram Gerrits
    * @return none
    */ 
    function DELETE($id, $table)
    {
        if($id != null && is_numeric($id))
        {
            deleteValue($id, $table);
        }
    }    
    
    
    
    //Constanten
    const ROOT = "/Dataprocessing/";   
    const TABLES = array("gezondheid", "leefomgeving", "economischerisicos");
    const FILE_TYPES = array("json", "xml");
  
    
    //Request data
    $uriData = uriData(ROOT, ["Table", "Format", "Id"]); //Ontvang Uri data
    
    $contentType = $uriData["Format"] == "xml" ? "xml" : "javascript";
    
    header('Content-type: text/'.$contentType);
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    error_reporting(E_ALL ^ E_WARNING); //Removes error ouput, prevents unpredicted ouput for the user
    
    //var_export($uriData);
    $table = validUriParam($uriData["Table"], TABLES);
    $format = validUriParam($uriData["Format"], FILE_TYPES);
    $id = isset($uriData[2]) ? $uriData["Id"] : null;    
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
            DELETE($id, $table);
            break;
        default:
            echo null;
            break;
    }
    
?>
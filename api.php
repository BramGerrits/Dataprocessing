<?php    
    include 'databaseFunctions.php';
    include 'formatFunctions.php';
    include 'validation/draft07validate.php';
    include 'validation/XSDvalidate.php';
    include 'requestdata.php';
    include 'translateFunctions.php';

    
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
                else{
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
    
    //  https://www.liquid-technologies.com/online-json-schema-validator
    //  https://www.freeformatter.com/xml-validator-xsd.html
    
    
    
    //JJ in perioden betekend dat het kwartaal niet bekend is.
    //Het cbs beschrijft het ecnomischrisico en persoonkenmerken type als een 'dimension'
    

    

    
    //Leefomgeving:
    //999      Totaal leefomgeving
    //18550    Stedelijkheid: zeer sterk stedelijk
    //18900    Stedelijkheid: sterk stedelijk
    //18950    Stedelijkheid: matig stedelijk
    //19000    Stedelijkheid: weinig stedelijk
    //19050    Stedelijkheid: niet stedelijk

    //Gezondheid:
    //999      Totaal gezondheid
    //123      Gezondheid: zeer goed
    //456      Gezondheid: goed
    //789      Gezondheid: minder dan goed
    //1234     Roken: niet
    //5678     Roken: wel
    //12       Alcoholgebruik: drinkt niet
    //34       Alcoholgebruik: matige drinker <6 glazen per week
    //56       Alcoholgebruik: zware drinker, 6>= glazen per week
    //901      Gewicht: ondergewicht
    //234      Gewicht: gezond gewicht
    //567      Gewicht: overgewicht
    //890      Gewicht: ernstig overgewicht
    //23       Beweging: voldoet niet aan norm
    //45       Beweging: voldoet aan norm

    //Economisch risico:
    //3456     Dienstverband: flexibel
    //9012     Dienstverband: deeltijd
    //5678     Dienstverband: voltijd
    //1234     Financieelonafhankelijk: wel
    //789      Financieel onafhankelijk: niet
    //456      Economisch zelfstandig: wel
    //123      Economisch zelfstandig: niet
    //999      Totaal risico's (totaal van alle bij elkaar)
    
    //Persoonskenmerken:
    //10001    Totaal personen
    //15400    Mannen
    //15450    Vrouwen
    //53110    18 tot 35 jaar
    //53705    35 tot 50 jaar
    //53850    50 tot 65 jaar
    //15700    65 jaar of ouder
    //12600    Herkomst: autochtoon
    //12650    Herkomst: westerse allochtoon
    //13000    Herkomst: niet-westerse allochtoon
?>
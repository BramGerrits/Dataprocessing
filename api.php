<?php    
    include 'databaseFunctions.php';
    include 'formatFunctions.php';
    include 'validation/draft07validate.php';
    include 'validation/XSDvalidate.php';
    include 'requestdata.php';
    
    $codeGetal = [];
    $codeGetal["persoonskenmerken"]["10001"] = "Totaal";
    $codeGetal["persoonskenmerken"]["15400"] = "Mannen";
    $codeGetal["persoonskenmerken"]["15450"] = "Vrouwen";
    $codeGetal["persoonskenmerken"]["53110"] = "18 tot 35 jaar";
    $codeGetal["persoonskenmerken"]["53705"] = "35 tot 50 jaar";
    $codeGetal["persoonskenmerken"]["53850"] = "50 tot 65 jaar";
    $codeGetal["persoonskenmerken"]["15700"] = "65 jaar of ouder";
    $codeGetal["persoonskenmerken"]["12600"] = "Herkomst: autochtoon";
    $codeGetal["persoonskenmerken"]["12650"] = "Herkomst: westerse allochtoon";
    $codeGetal["persoonskenmerken"]["13000"] = "Herkomst: niet-westerse allochtoon";
         
    $codeGetal["economischerisicos"]["3456"] = "Dienstverband: flexibel";
    $codeGetal["economischerisicos"]["9012"] = "Dienstverband: deeltijd";
    $codeGetal["economischerisicos"]["5678"] = "Dienstverband: voltijd";
    $codeGetal["economischerisicos"]["1234"] = "Financieelonafhankelijk: wel";
    $codeGetal["economischerisicos"]["789"] = "Financieel onafhankelijk: niet";
    $codeGetal["economischerisicos"]["456"] = "Economisch zelfstandig: wel";
    $codeGetal["economischerisicos"]["123"] = "Economisch zelfstandig: niet";
    $codeGetal["economischerisicos"]["999"] = "Totaal risico's";
 
    $codeGetal["gezondheid"]["999"] = "Totaal gezondheid";
    $codeGetal["gezondheid"]["123"] = "Gezondheid: zeer goed";
    $codeGetal["gezondheid"]["456"] = "Gezondheid: goed";
    $codeGetal["gezondheid"]["789"] = "Gezondheid: minder dan goed";
    $codeGetal["gezondheid"]["1234"] = "Roken: niet";
    $codeGetal["gezondheid"]["5678"] = "Roken: wel";
    $codeGetal["gezondheid"]["12"] = "Alcoholgebruik: drinkt niet";
    $codeGetal["gezondheid"]["34"] = "Alcoholgebruik: matige drinker <6 glazen per week";
    $codeGetal["gezondheid"]["56"] = "Alcoholgebruik: zware drinker, 6>= glazen per week";
    $codeGetal["gezondheid"]["901"] = "Gewicht: ondergewicht";
    $codeGetal["gezondheid"]["234"] = "Gewicht: gezond gewicht";
    $codeGetal["gezondheid"]["567"] = "Gewicht: overgewicht";
    $codeGetal["gezondheid"]["890"] = "Gewicht: ernstig overgewicht"; 
    $codeGetal["gezondheid"]["23"] = "Beweging: voldoet niet aan norm";
    $codeGetal["gezondheid"]["45"] = "Beweging: voldoet aan norm";
    
    
    $codeGetal["leefomgeving"]["999"] = "Totaal leefomgeving";
    $codeGetal["leefomgeving"]["18550"] = "Stedelijkheid: zeer sterk stedelijk";
    $codeGetal["leefomgeving"]["18900"] = "Stedelijkheid: sterk stedelijk";
    $codeGetal["leefomgeving"]["18950"] = "Stedelijkheid: matig stedelijk";
    $codeGetal["leefomgeving"]["19000"] = "Stedelijkheid: weinig stedelijk";
    $codeGetal["leefomgeving"]["19050"] = "Stedelijkheid: niet stedelijk";
    
    
    
    function decode($code, $key)
    {
        global $codeGetal;
        return $codeGetal[$key][$code];
    }
    
    function decodeValues($array, $table)
    {
        $group = [];
        $group[$table."_naam"] = decode($array[$table], $table);
        $group[$table] = $array[$table];
        $group["persoonskenmerken_naam"] = decode($array["persoonskenmerken"], "persoonskenmerken");
        $group["persoonskenmerken"] = $array["persoonskenmerken"];
        
        unset($array[$table]);
        unset($array["persoonskenmerken"]);
        
        $result = array_merge($group, $array);
                
        return $result;
    }
 
    function decodeValueArray($array, $table)
    {
        foreach($array as $key => $value)
        {
            $array[$key] = decodeValues($value, $table); 
        }
        return $array;
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
                $array = getAll($table);
                $array = decodeValueArray($array, $table);
                $json = JSON($array);
                
                //if(JSON_validate($json, $table)){
                    $result = $json;
                //}
            } 
            else
            {
                $array = getAll($table);
                $array = decodeValueArray($array, $table);
                //var_export($array);
                $xml = createXMLbyArray($table, $array);
                
                //if(XML_validate($xml, $table)){
                    $result = $xml;
                //}
            }
        }
        else
        {
            if($format == "json")
            {              
                $array = getValue($table, $id);
                $array = decodeValues($array, $table);
                $json = JSON($array);
                
                //if(JSON_validate($json, $table)){
                    $result = $json;
                //}   
            } 
            else
            {
                $array = getValue($table, $id);
                $array = decodeValues($array, $table);
                $xml = createXmlByValue($array, $table);
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
        if(isset($data["data"]))
        {
            if($format == "json")
            {
                if(is_array($data) && isset($data))
                {   
                    $json = $data["data"];
                    $json = str_replace ("_", " ", $json); //Lage strepen weghalen die kunnen voorkomen in een postman request

                    if(JSON_validate($json, $table))
                    {
                        $values = json_decode($json);
                        foreach($values->ratings as $values)
                        {
                            addTo($table, json_decode(json_encode($values), true));
                        }
                    }   
                }
            }
            else
            {
                $xml = $data["data"];
                $xml = str_replace ("_", " ", $xml); //Lage strepen weghalen die kunnen voorkomen in een postman request
                $xml = str_replace ("<ratings>", '<ratings xmlns="https://bramgerrits.com/"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                    xsi:noNamespaceSchemaLocation="XMLschema.xsd">', $xml);

                if(XML_validate($xml, $table))
                {
                    $obj = XMLtoValues($xml);
                    foreach($obj as $values)
                    {
                        addTo($table, $values);
                    }
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
            if($format == "json")
            {
                $json = $data["data"];
                $json = str_replace ("_", " ", $json); //Verwijderd lage strepen die kunnen voorkomen in een verzoek

                if(JSON_validate($json, $table))
                {
                    $jsonArr = json_decode($json);                    
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
                $xml = $data["data"];
                $xml = str_replace ("_", " ", $xml);
                $xml = str_replace ("<ratings>", '<ratings xmlns="https://bramgerrits.com/"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                    xsi:noNamespaceSchemaLocation="XMLschema.xsd">', $xml);

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
    $uriData = uriData(ROOT); //Ontvang Uri data
    
    //Headers
    $contentType = $uriData[1] == "xml" ? "text/xml" : "application/json";
    header('Content-type: '.$contentType);
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    error_reporting(E_ALL ^ E_WARNING); //Removes error ouput, prevents unpredicted ouput for the user
    
    //var_export($uriData);
    $table = validUriParam($uriData[0], TABLES);
    $format = validUriParam($uriData[1], FILE_TYPES);
    $id = isset($uriData[2]) ? $uriData[2] : null;    
    $data = bodyData(); //Datafield uit de body van de request. 
    
    //var_export($data);
    
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
    
    //  https://www.liquid-technologies.com/online-json-schema-validator
    //  https://www.freeformatter.com/xml-validator-xsd.html
    
    
    
        //economische zelfstandigheid, financiële onafhankelijkheid en het dienstverband
    //JJ in periode betekend dat het kwartaal niet bekend is.
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
    
        
    
    
    
//    $persKenCodes = array();
//    $persKenCodes[10001] = "Totaal/Gemiddelde";
//    $persKenCodes[15400] = "Mannen";
//    $persKenCodes[15450] = "Vrouwen";
//    $persKenCodes[53110] = "18 tot 35 jaar";
//    $persKenCodes[53705] = "35 tot 50 jaar";
//    $persKenCodes[53850] = "50 tot 65 jaar";
//    $persKenCodes[15700] = "65 jaar of ouder";
//    $persKenCodes[15700] = "65 jaar of ouder";
//    
//    
//    
//    $ecoRiCodes = array();
//    $ecoRiCodes[3456] = "Dienstverband: flexibel";
//    $ecoRiCodes[9012] = "Dienstverband: deeltijd";
//    $ecoRiCodes[5678] = "Dienstverband: voltijd";
//    $ecoRiCodes[1234] = "Financieel onafhankelijk: wel";
//    $ecoRiCodes[789] = "Financieel onafhankelijk: niet";
//    $ecoRiCodes[456] = "Economisch zelfstandig: wel";
//    $ecoRiCodes[123] = "Economisch zelfstandig: niet";
//    $ecoRiCodes[999] = "Totaal/Gemiddelde";
    
    
?>
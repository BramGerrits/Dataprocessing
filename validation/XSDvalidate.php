<?php
    /**
    * Checks if the given xml document is valid compared to the xsd schema.
    * 
    * @param $xmlString  A xml string 
    * @param $schemaName A xsd schema as a string
    * 
    * @author Bram Gerrits
    * @return boolean
    */ 
    function XML_validate($xmlString, $schemaName)
    {
        $xmlDoc = new DOMDocument();
        try
        {
            $xmlDoc->loadXML($xmlString);
            $result = $xmlDoc->schemaValidate("../Dataprocessing/xsd/".$schemaName.".xsd");
        }
        catch(Exception $e)
        {
            $result = false;
        }
        return $result;
    }

//    function XML_validate($xml, $schema)
//    {
//        $xmlDoc = new DOMDocument();
//        try
//        {
//            $xmlDoc->loadXML($xml);
//            $result = $xmlDoc->schemaValidate("../Dataprocessing/xsd/".$schemaName.".xsd");
//        }
//        catch(Exception $e)
//        {
//            $result = false;
//        }
//        return $result;
//    }
?>
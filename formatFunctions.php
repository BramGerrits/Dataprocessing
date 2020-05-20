<?php
    const MAIN_TAG = "<rating/>";
    
    /**
    * Puts xml code between root tags to make it a valid xml document
    *
    * @param $xml        the xml code that will be wrapped with root tags
    * @param $schemaName the name of the xml schema which validates the xml document
    * 
    * @author Bram Gerrits
    * @return A valid xml document
    */ 
    function XML_wrapRoot($xml, $schemaName)
    {
        return '<ratings xmlns="https://bramgerrits.com/"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                    xsi:noNamespaceSchemaLocation="'.$schemaName.'.xsd">'.$xml."</ratings>";
    }
    
    /**
    * removes the xml version tag.
    *
    * @param $xml xml code that will have its version tag stripped.
    * 
    * @author Bram Gerrits
    * @return A xml string
    */ 
    function XML_removeVersion($xml)
    {
        return substr($xml, strpos($xml, '?'.'>') + 2); //verwijderd version tag
    }
   
    /**
    * Puts an array of values into a xml format.
    *
    * @param $array An array with values that will be parsed to an xml format
    * 
    * @author Bram Gerrits
    * @return A xml string
    */ 
    function XML($array)
    {
        $output = null;
        if($array != null)
        {
            $xml = new SimpleXMLElement(MAIN_TAG);

            foreach($array as $key => $data){
                $data = $data == null ? "null" : $data;

                if($key == "id"){
                    $xml->addAttribute("id",$data);
                    unset($array["id"]);
                } else{
                    $array[$key] = array ($data => $key);
                }
            }

            array_walk_recursive($array, array($xml, 'addChild'));
            $output = $xml->asXML();
        } 
        else
        {
            $output = null;
        }
        return $output;    
    }
    
    /**
    * Creates an xml document from a database table row
    *
    * @param $table The database table which will be used to fill the xml document with data
    * @param $id    The identifier which of which the data in the xml document will be based on 
    * 
    * @author Bram Gerrits
    * @return A valid xml document
    */ 
    function createXMLbyId($table, $id)
    {
        $result = XML(getValue($table, $id));
        $result = XML_removeVersion($result);
        $result = XML_wrapRoot($result, $table);
        return $result;
    } 
    
    /**
    * Creates an xml document from a database table
    *
    * @param $table The database table where the data came from
    * @param $array A multidimentional array which contains values
    * 
    * @author Bram Gerrits
    * @return A valid xml document
    */ 
    function createXMLbyArray($table, $array)
    {
        $xml = '';
        foreach($array as $value){
            $newxml = XML($value); //Haalt nieuwe xml op
            $newxml = XML_removeVersion($newxml);
            $xml = $xml.$newxml; //Voegt nieuwe xml toe
        }
        $xml = XML_wrapRoot($xml, $table);
        
        return '<!--?xml version="1.0"?-->'.$xml;
    }
    
    /**
    * Converts a xml string to an array
    *
    * @param $xml A xml stting
    * 
    * @author Bram Gerrits
    * @return array
    */ 
    function XMLtoValues($xml)
    {
        $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        unset($array["rating"]["@attributes"]);
        $values = $array["rating"];
        return $values;
    }

    /**
    * Converts a array to a valid json document
    *
    * @param $array the array which will be parsed to a json object
    * 
    * @author Bram Gerrits
    * @return A valid json string
    */ 
    function JSON($array)
    { 
        $json = "";
        $json = $json.'{';
        $json = $json.'"ratings": ';
        
        if(!isset($array[0]))
        {
            $json = $json.'[';
        }
        
        $json = $json.json_encode($array);
        
        if(!isset($array[0]))
        {
            $json = $json.']';
        }
        
        $json = $json.'}';     
        return $json;
    }
?>

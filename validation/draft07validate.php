<?php
    /**
    * Checks if the given json document is valid compared to the draft v7 schema.
    * 
    * @param $json   A json string 
    * @param $schema A draft v7 schema as a string
    * 
    * @author Bram Gerrits
    * @return boolean
    */ 
    function DRAFT07_validate($json, $schema)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://assertible.com/json',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => '{
                "schema": '.$schema.',
                "json": '.$json.'
              }'
        ]);
  
        ob_start();
        curl_exec($curl);
        $result = ob_get_contents();
        ob_end_clean();
        
        curl_close($curl); //Sluit request.

        $result = get_object_vars(json_decode($result));
        
        
        $result = isset($result["valid"]) && $result["valid"] == TRUE ? 1 : 0;
        
        
        return $result;
    }
    
    /**
    * Checks if the given json document is valid compared to the draft v7 schema.
    * 
    * @param $json A json string 
    * @param $name Name of the draft v7 schema s
    * 
    * @author Bram Gerrits
    * @return boolean
    */ 
    function JSON_validate($json, $name)
    {
        $schema = file_get_contents("../api/draft07/".$name.".json");

        $result = DRAFT07_validate($json, $schema);     
        
        return $result;
    }
?>
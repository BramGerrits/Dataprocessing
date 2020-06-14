<?php

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
    
    
    /**
    * Translates a code and a key to a readable string
    *
    * @param $code        the code that will be translated
    * @param $key         the key which contains the code
    * 
    * @author Bram Gerrits
    * @return A readable string
    */ 
    function translate($code, $key)
    {
        global $codeGetal;
        return $codeGetal[$key][$code];
    }
    
    /**
    * Adds readable strings to a collection of element
    *
    * @param $array        the collection
    * @param $table        the table, which will act as a key
    * 
    * @author Bram Gerrits
    * @return A collection of readable string
    */ 
    
    function translateValues($array, $table)
    {
        $group = [];
        $group[$table."_naam"] = translate($array[$table], $table);
        $group[$table] = $array[$table];
        $group["persoonskenmerken_naam"] = translate($array["persoonskenmerken"], "persoonskenmerken");
        $group["persoonskenmerken"] = $array["persoonskenmerken"];
        
        unset($array[$table]);
        unset($array["persoonskenmerken"]);
        
        $result = array_merge($group, $array);
                
        return $result;
    }
 
    /**
    * Adds readable strings to multiple collections of elements
    *
    * @param $array        the collections
    * @param $table        the table, which will act as a key
    * 
    * @author Bram Gerrits
    * @return A collection of arrays containing readable string
    */ 
    function translateValueArray($array, $table)
    {
        foreach($array as $key => $value)
        {
            $array[$key] = translateValues($value, $table); 
        }
        return $array;
    }

?>
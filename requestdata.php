<?php

    class RequestReader {
        public $aMemberVar = 'aMemberVar Member Variable';
        public $aFuncName = 'aMemberFunc';

        private $uri = null;

        function __construct($root) 
        {
            $this->uri = str_replace($root, "", $_SERVER['REQUEST_URI']);
        }
    
        function getURIData($root, $parameterNames) 
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
    }

?>
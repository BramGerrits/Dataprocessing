<?php
    $conn = null;
 
    function createDatabase()
    {
        $sql = "CREATE DATABASE myDB";
        $conn->query($sql);
    }
    
    /**
    * Opens a connection to a database
    *
    * @param $server   the adress of the database's server
    * @param $username login username of the database's server
    * @param $password login password of the database's server
    * @param $database name of the database
    * 
    * @author Bram Gerrits
    * @return none
    */ 
    function connect($server, $username, $password, $database)
    {
        global $conn;
        $conn = new PDO("mysql:host=$server;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
    * Adds values to a database table
    *
    * @param $table    the table which will consume the data
    * @param $array    a list of data which will be put into the table
    * 
    * @author Bram Gerrits
    * @return none
    */ 
    function addTo($table, $array)
    {
        global $conn;
        
        $sql = "INSERT INTO $table "
        . "VALUES (null, :value, :persoonsKenmerken, :perioden, :ongelukkig, :gelukkig, :ontevreden, :tevreden)";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':value', $array[$table]);
        $stmt->bindParam(':persoonsKenmerken', $array["persoonskenmerken"]);
        $stmt->bindParam(':perioden', $array["perioden"]);
        $stmt->bindParam(':ongelukkig', $array["ongelukkig"]);
        $stmt->bindParam(':gelukkig', $array["gelukkig"]);
        $stmt->bindParam(':ontevreden', $array["ontevreden"]);
        $stmt->bindParam(':tevreden', $array["tevreden"]);
        
        $stmt->execute();        
    }
     
    /**
    * Collects a specific piece of data from a specified table
    *
    * @param $table the table which data will be collected from
    * @param $id    the unique identifier of the data
    * 
    * @author Bram Gerrits
    * @return $info an array of values
    */ 
    function getValue($table, $id)
    {
        global $conn;
        $sql = "SELECT * FROM $table WHERE id = :id";        
        $stmt = $conn->prepare($sql);        
        $stmt->bindParam(':id', $id);        
        $stmt->execute();           
        $info = $stmt->fetch();
        
        
        if($info != null) 
        {
            foreach($info as $key => $data){
                if(is_numeric($key)){
                    unset($info[$key]);
                }
            }
        } 
        else 
        {
            $info = null;
        }
        
        if(isset($info["id"]))
        {
            $info["id"] = intval($info["id"]);
        }
        
        return $info;
    }
    
    /**
    * Collects all data form a specified table
    *
    * @param $table the table which data will be collected from
    * 
    * @author Bram Gerrits
    * @return $info an multidimentional array of values
    */ 
    function getAll($table)
    {
        global $conn;
        
        $sql = "SELECT * FROM $table";        
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $dbdata = $stmt->fetchAll();
     
        foreach($dbdata as $key => $array){
            foreach($array as $tag => $value){
                if(isset($dbdata[$key]["id"]))
                {
                    $dbdata[$key]["id"] = intval($array["id"]);
                }
                
                if(is_integer($tag)){
                    unset($dbdata[$key][$tag]);
                }
            }
        }
        
        return $dbdata;
    }

    /**
    * Deletes a row of the specified table
    *
    * @param $id the identifier of the row that will be removed
    * @param $table the table which will have a row removed
    * 
    * @author Bram Gerrits
    * @return none
    */ 
    function deleteValue($id, $table)
    {
        global $conn;
        
        $sql = "DELETE from $table WHERE id = :id;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();    
    }
    
    /**
    * Replaces the values of a row from a table with new values
    *
    * @param $table the table which will have its content changed
    * @param $array a list of values which will replace the old values
    * @param $id    the identifier of the row that will have its values replaced
    * 
    * @author Bram Gerrits
    * @return none
    */ 
    function updateValue($table, $array, $id)
    {
        global $conn;
        
        $sql = "UPDATE $table 
                SET 
                    $table = :value,
                    persoonskenmerken = :persoonsKenmerken,
                    perioden = :perioden,
                    ongelukkig = :ongelukkig,
                    gelukkig = :gelukkig,
                    ontevreden = :ontevreden,
                    tevreden = :tevreden
                WHERE
                    id = :id";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':value', $array[$table]);
        $stmt->bindParam(':persoonsKenmerken', $array["persoonskenmerken"]);
        $stmt->bindParam(':perioden', $array["perioden"]);
        $stmt->bindParam(':ongelukkig', $array["ongelukkig"]);
        $stmt->bindParam(':gelukkig', $array["gelukkig"]);
        $stmt->bindParam(':ontevreden', $array["ontevreden"]);
        $stmt->bindParam(':tevreden', $array["tevreden"]);
        $stmt->bindParam(':id', $id);
        
        //var_export($array[$table]);
        
        $stmt->execute();  
    }
    
        
    /**
    * Reads data from a csv file and puts it in a database table
    *
    * @param $table the table which the data will be put in
    * @param $csv   the csv dataset which data will be read from
    * 
    * @author Bram Gerrits
    * @return none
    */ 
    function fillTable($table, $csv){
        global $conn;
        
        $sql = "INSERT INTO $table VALUES (null, :gezondheid, :persoonsKenmerken, :perioden, :ongelukkig, :gelukkig, :ontevreden, :tevreden)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':gezondheid', $gezondheid);
        $stmt->bindParam(':persoonsKenmerken', $persoonsKenmerken);
        $stmt->bindParam(':perioden', $perioden);
        $stmt->bindParam(':ongelukkig', $ongelukkig);
        $stmt->bindParam(':gelukkig', $gelukkig);
        $stmt->bindParam(':ontevreden', $ontevreden);
        $stmt->bindParam(':tevreden', $tevreden);

        if (($h = fopen("{$csv}", "r")) !== FALSE) 
        {
            while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
            {
                $info = str_replace('"',"","$data[0]");
                $array = explode(";", $info);

                if(is_numeric($array[0])){
                    $gezondheid = $array[1];
                    $persoonsKenmerken = $array[2];
                    $perioden = $array[3];

                    $ongelukkig = is_numeric($array[4]) ? $array[4] : null;
                    $gelukkig = is_numeric($array[5]) ? $array[5] : null;
                    $ontevreden = is_numeric($array[6]) ? $array[6] : null;
                    $tevreden = is_numeric($array[7]) ? $array[7] : null;

                    $stmt->execute();
                    
                    
                    
                }
            }

            fclose($h);
        }
    }
    
//    connect("127.0.0.1", "root", "", "dataprocessing");
//    fillTable("gezondheid", "datasets/Gezondheid.csv");
//    fillTable("leefomgeving", "datasets/Leefomgeving.csv");
//    fillTable("economischerisicos", "datasets/EconomischeRisicos.csv");
?>
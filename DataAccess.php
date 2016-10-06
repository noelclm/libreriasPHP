<?php

/**
 * @file DataAccess.php
 * @version 1.0
 * @author noelclm (https://github.com/noelclm)
 * @date   24-Septiembre-2016
 * @url    https://github.com/noelclm/libreriasPHP/DataAccess.php
 * @description Conectar a una BBDD con PHP
 */

//-------------------------------------------------------------------
// Definitions
//-------------------------------------------------------------------

define("SERVER_CONNECTION", "server");
define("USER_CONNECTION", "user");
define("PSW_CONNECTION", "password");
define("BBDD_CONNECTION", "bbdd");
define("BBDD_COLLATION", "utf8");

//-------------------------------------------------------------------
// Functions to interact with the database
//-------------------------------------------------------------------

// Query the database and returns an array with the results
function consultSQL($sql){
    
    $result = array();
    $bd = new DataAccess();
    $bd->run($sql);
    
    while(($fila = $bd->row()) !== false ){
        $result[] = $fila;
    }
 
    $bd->close();
    unset($bd);
    
    return $result;
    
}

// Modify an entry in the database and returns true or false if fails
function modifySQL($sql){
    
    $bd = new DataAccess();
    $result = $bd->run($sql);
    $bd->close();
    unset($bd);
    
    return $result;

}

// Inserts a new entry into the database and returns the id of the input or false if fails
function insertSQL($sql){
    
    $bd = new DataAccess();
    if($bd->run($sql)){
        $result = $bd->lastId();
    }
    else{
        $result = false;
    }
    $bd->close();
    unset($bd);
    
    return $result;

}

// Deletes an entry in the database and returns true or false if fails
function removeSQL($sql){
    
    $bd = new DataAccess();
    $result = $bd->run($sql);
    $bd->close();
    unset($bd);
    
    return $result;

}

//-------------------------------------------------------------------
// Class access database
//-------------------------------------------------------------------

class DataAccess {

    var $bd;
    var $query;
    
    // Create connection
    function DataAccess($server = SERVER_CONNECTION, $user = USER_CONNECTION, $psw = PSW_CONNECTION, $bd = BBDD_CONNECTION, $collation = BBDD_COLLATION){

        $this->bd = new mysqli($server, $user, $psw, $bd);
        $this->bd->set_charset($collation);

    }

    // Execute query
    function run($sql){

        if( ($this->query = $this->bd->query($sql)) === false ) {
            return false;
        } else {
            return true;
        }

    }

    // Passes to the next result
    function row(){  

        if ( $fila = $this->query->fetch_assoc())
            return $fila;
        else
            return false;

    }  
    
    // Last inserted id
    function lastId(){
        
        return mysqli_insert_id($this->bd);
        
    }
    
    // Closes the connection
    function close(){
        
        mysqli_close($this->bd);
        
    }

}

?>

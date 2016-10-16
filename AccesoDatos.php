<?php

/**
 * @file AccesoDatos.php
 * @version 1.0
 * @author noelclm (https://github.com/noelclm)
 * @date   24-Septiembre-2016
 * @url    https://github.com/noelclm/libreriasPHP/AccesoDatos.php
 * @description Conectar a una BBDD con PHP
 */

//-------------------------------------------------------------------
// Definiciones
//-------------------------------------------------------------------

define("SERVIDOR", "servidor");
define("USUARIO", "usuario");
define("CLAVE", "clave");
define("BASEDEDATOS", "bdd");
define("CODIFICACION", "utf8");

//-------------------------------------------------------------------
// Funciones para interacturar con la base de datos
//-------------------------------------------------------------------

// Consulta en la base de datos y devuelve un array con los resultados
function consultaSQL ($sql){
    
    $resultado = array();
    $bd = new AccesoDatos();
    $bd->ejecutar($sql);
    
    while(($fila = $bd->sigFila()) !== false )
        $resultado[] = $fila;
 
    $bd->cerrar();
    unset($bd);
    
    return $resultado;
    
} // function consultSQL

// Modifica una entrada en la base de datos y devuelve true o false si da error
function modificarSQL ($sql){
    
    $bd = new AccesoDatos();
    $resultado = $bd->ejecutar($sql);
    
    $bd->cerrar();
    unset($bd);
    
    return $resultado;

} // function modificarSQL

// Inserta una nueva entrada en la base de datos y devuelve el id de la entrada o false si da error
function insertarSQL ($sql){
    
    $bd = new AccesoDatos();
    
    if($bd->ejecutar($sql))
        $resultado = $bd->ultimoId();
    else
        $resultado = false;
 
    $bd->cerrar();
    unset($bd);
    
    return $resultado;

} // function insertarSQL

// Elimina una entrada en la base de datos y devuelve true o false si da error
function borrarSQL ($sql){
    
    $bd = new AccesoDatos();
    $resultado = $bd->ejecutar($sql);
    
    $bd->cerrar();
    unset($bd);
    
    return $resultado;

} // function borrarSQL

//-------------------------------------------------------------------
// Clase de acceso a la base de datos
//-------------------------------------------------------------------

class AccesoDatos {

    var $bd;
    var $query;
    
    // Crea una conexion a la base de datos
    function AccesoDatos ($servidor = SERVIDOR, $usuario = USUARIO, $clave = CLAVE, $bd = BASEDEDATOS, $codificacion = CODIFICACION){

        $this->bd = new mysqli($servidor, $usuario, $clave, $bd);
        $this->bd->set_charset($codificacion);

    } // function AccesoDatos

    // Ejecuta una consulta
    function ejecutar ($sql){

        if( ($this->query = $this->bd->query($sql)) === false )
            return false;
        else 
            return true;
        

    } // function ejecutar

    // Devuelve el siguiente resultado de la consulta
    function sigFila (){  

        if ( $fila = $this->query->fetch_assoc())
            return $fila;
        else
            return false;

    } // function sigFila
    
    // Devuelve el ultimo id insertado
    function ultimoId (){
        
        return mysqli_insert_id($this->bd);
        
    } // function ultimoId
    
    // Cierra la conexion
    function cerrar (){
        
        mysqli_close($this->bd);
        
    } // function cerrar

}

?>

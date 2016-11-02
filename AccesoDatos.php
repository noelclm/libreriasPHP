<?php

/**
 * Conectar a una BBDD con PHP
 *
 * @version 1.0
 * @author noelclm (https://github.com/noelclm)
 * @link https://github.com/noelclm/libreriasPHP/AccesoDatos.php
 */

//-------------------------------------------------------------------
// Definiciones (Datos para conectarse al servidor)
//-------------------------------------------------------------------

define("SERVIDOR", "servidor");
define("USUARIO", "usuario");
define("CLAVE", "clave");
define("BASEDEDATOS", "bdd");
define("CODIFICACION", "utf8");

//-------------------------------------------------------------------
// Funciones para interacturar con la base de datos
//-------------------------------------------------------------------

/**
 * Consulta en la base de datos y devuelve un array con los resultados
 *
 * @param string $sql Select que se quiere lanzar
 * @return array Array con las lineas devueltas
 */
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

/**
 * Modifica una entrada en la base de datos
 *
 * @param string $sql Update que se quiere lanzar
 * @return boolean True si se ha realizado, false en caso contrario
 */
function modificarSQL ($sql){
    
    $bd = new AccesoDatos();
    $resultado = $bd->ejecutar($sql);
    
    $bd->cerrar();
    unset($bd);
    
    return $resultado;

} // function modificarSQL

/**
 * Inserta una nueva entrada en la base de datos
 *
 * @param string $sql Insert que se quiere lanzar
 * @return mixe ID del dato insertado si se ha realizado, false en caso contrario
 */
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

/**
 * Elimina una entrada en la base de dato
 *
 * @param string $sql Delete que se quiere lanzar
 * @return boolean True si se ha realizado, false en caso contrario
 */
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

    /**
     * Objeto de la conexión
     */
    var $bd;
    /**
     * Objeto devuelto tras realizar la ejecución de una query
     */
    var $query;
    
    /**
     * Constructor
     *
     * Establece una conexión con la base de datos mediante mysqli
     *
     * @param string $servidor Dirección del servidor
     * @param string $usuario Usuario del servidor
     * @param string $clave Contraseña del servidor
     * @param string $bd Base de datos del servidor
     * @param string $codificacion Tipo de codificación del servidor
     * @global object $bd
     * @global object $query
     */
    function __construct ($servidor = SERVIDOR, $usuario = USUARIO, $clave = CLAVE, $bd = BASEDEDATOS, $codificacion = CODIFICACION){

        $this->bd = new mysqli($servidor, $usuario, $clave, $bd);
        $this->bd->set_charset($codificacion);

    } // function __construct

    /**
     * Ejecuta una consulta
     *
     * @param string $sql Query a lanzar
     * @global object $bd
     * @global object $query
     * @return boolean True si se ha podido lanzar, false en caso contrario
     */
    function ejecutar ($sql){

        if( ($this->query = $this->bd->query($sql)) === false )
            return false;
        else 
            return true;
        

    } // function ejecutar

    /**
     * Devuelve el siguiente resultado de la consulta
     *
     * @global object $query
     * @return mixe Array con los valores de la siguiente fila si quedan filas, false en caso contrario
     */
    function sigFila (){  

        if ( $fila = $this->query->fetch_assoc())
            return $fila;
        else
            return false;

    } // function sigFila
    
    /**
     * Devuelve el ultimo id insertado
     *
     * @global object $bd
     * @return int ID del ultimo insert que se realizó
     */
    function ultimoId (){
        
        return mysqli_insert_id($this->bd);
        
    } // function ultimoId
    
    /**
     * Cierra la conexión con la base de datos
     *
     * @global object $bd
     */
    function cerrar (){
        
        mysqli_close($this->bd);
        
    } // function cerrar

}

?>

<?php

/**
 * Funciones utiles en PHP
 *
 * @version 1.0
 * @author noelclm (https://github.com/noelclm)
 * @url https://github.com/noelclm/libreriasPHP/Funciones.php
 */

/**
 * Recorta un texto sin recortar palabras
 *
 * @param string $texto Texto que se quiere recortar
 * @param int $numMaxCaract Numero maximo de caracteres
 * @return string Texto recortado
 */
function cortarTexto ($texto, $numMaxCaract){
    
    // Si el tamaño del texto no excede el numero maximo de caracteres
    if(strlen($texto) <  $numMaxCaract)
        $textoCortado = $texto;
    
    else{
        // Recortamos el texto
        $textoCortado = substr($texto, 0, $numMaxCaract);
        // Buscamos la ultima vez que aparece un espacio
        $ultimoEspacio = strripos($textoCortado, " ");
 
        // Si hay espacio recortamos el texto hasta el ultimo espacio
        if($ultimoEspacio !== false){
            
            $textoCortadoTmp = substr($textoCortado, 0, $ultimoEspacio);
            
            if (substr($textoCortado, $ultimoEspacio))
                $textoCortadoTmp .= '...';

            $textoCortado = $textoCortadoTmp;
            
        } // if($ultimoEspacio !== false)
        elseif(substr($texto, $numMaxCaract))
            $textoCortado .= '...';  
        
    } // else
 
    return $textoCortado;
    
} // function cortarTexto

/**
 * Genera una cadena aleatorea de alfanumerica
 *
 * @param int $longitud Longitud de la cadena, es opcional, por defecto es de 10
 * @return string Cadena aleatorea
 */
function cadenaAleatorea ($longitud = 10) {

    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $cantidadCaracteres = strlen($caracteres);
    $cadena = '';
    
    for ($i = 0; $i < $longitud; $i++) 
        $cadena .= $caracteres[rand(0, $cantidadCaracteres - 1)]; 

    return $cadena; 
    
} // function cadenaAleatorea

?>

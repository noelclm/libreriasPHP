<?php

/**
 * @file Funciones.php
 * @version 1.0
 * @author noelclm (https://github.com/noelclm)
 * @date   29-Septiembre-2016
 * @url    https://github.com/noelclm/libreriasPHP/Funciones.php
 * @description Funciones utiles en PHP
 */

// Recorta un texto sin recortar palabras
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

// Genera una cadena aleatorea, por defecto de 10
function cadenaAleatorea ($longitud = 10) {

    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $cantidadCaracteres = strlen($caracteres);
    $cadena = '';
    
    for ($i = 0; $i < $longitud; $i++) 
        $cadena .= $caracteres[rand(0, $cantidadCaracteres - 1)]; 

    return $cadena; 
    
} // function cadenaAleatorea

?>

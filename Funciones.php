<?php

/**
 * Funciones utiles en PHP
 *
 * @version 1.2
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
function cortarTexto ($texto, $numMaxCaract) {
    
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
    // Iniciamos la semilla de numeros aleatorios y la key
    srand((double)microtime()*1000000);
    $cadena = "";

    // Creamos la cadena de N caracteres;
    for($x=0;$x<$longitud;$x++){

        $aleatorio = rand(0,35);
        $cadena .=  substr($caracteres, $aleatorio, 1);

    } // for($x=0;$x<$longitud;$x++)

    return $cadena; 
    
} // function cadenaAleatorea

/**
 * Convierte <br> en nl
 *
 * @param string Cadena a convertir
 * @return string Cadena transformada
 */
function br2nl ($cadena) {
    
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $cadena);
    
} // function br2nl

/**
 * Busca los ficheros de un directorio de forma recursiva
 *
 * @param string Directorio
 * @return array Datos de los ficheros
 */
function directorio2Array ($directorio) { 
   
    $resultado = array(); 

    // Si existe el directorio
    if(file_exists($directorio)){

        // Buscamos todos los archivos del directorio y los recorremos
        $archivosdirectorio = scandir($directorio); 

        foreach ($archivosdirectorio as $clave => $valor){ 

            if(!in_array($valor,array(".",".."))){ 

                // Si es otro directorio lo recorre de forma recursiva
                if(is_dir($directorio . "/" . $valor)){ 
                    $resultado2 = directorio2Array($directorio . "/" . $valor);
                    // Metemos cada fichero con su ruta en el array
                    foreach ($resultado2 as $clave => $archivo) {
                        $resultado[] = $archivo;
                    } 
                } // if(is_dir($directorio . "/" . $valor))
                else{ 
                    // Si es un archivo guardamos los datos
                    $tamanyo = filesize($directorio. "/".$valor);
                    $tipo = tipo_mime($valor); // Funcion declarada en este fichero
                    $resultado[] = array("ruta" => $directorio,
                                         "nombre" => $valor,
                                         "tipo" => $tipo,
                                         "tamanyo" => $tamanyo); 
                } // else

            } // if(!in_array($valor,array(".","..")))

        } // foreach ($archivosdirectorio as $clave => $valor)

    } // if(file_exists($directorio))
     
    return $resultado; 

} // function directorio2Array

/**
 * Saca el Mime-Content-Type de un fichero
 *
 * @param string Nombre del archivo
 * @return string Content-type
 */
function tipo_mime($nombreArchivo) {

    $tipo_mime = array(

        // textos
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // imagenes
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archivos
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        
    ); // array

    $ext = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
    
    if(array_key_exists($ext, $tipo_mime)) 
        return $tipo_mime[$ext];
    else 
        return 'application/octet-stream';
    
} // function tipo_mime

?>

/**
 * @file Functions.php
 * @version 1.0
 * @author noelclm (https://github.com/noelclm)
 * @date   29-Septiembre-2016
 * @url    https://github.com/noelclm/libreriasPHP/Functions.php
 * @description Funciones utiles en PHP
 */

// Cut the text characters but not cut words
function cutText($text, $numMaxCaract){
    if (strlen($text) <  $numMaxCaract){
        $textCut = $text;
    }else{
        $textCut = substr($text, 0, $numMaxCaract);
        $lastSpave = strripos($textCut, " ");
 
        if ($lastSpave !== false){
            $textoCortadoTmp = substr($textCut, 0, $lastSpave);
            if (substr($textCut, $lastSpave)){
                $textoCortadoTmp .= '...';
            }
            $textCut = $textoCortadoTmp;
        }elseif (substr($text, $numMaxCaract)){
            $textCut .= '...';
        }
    }
 
    return $textCut;
}

// Generates a random string, default 10
function generateRandomString($length = 10) {

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) { 
        $randomString .= $characters[rand(0, $charactersLength - 1)]; 
    } 

    return $randomString; 

} 

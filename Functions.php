/*
	Functions.php
    Copyright (C) 2015  Noel Clemente
    This code is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

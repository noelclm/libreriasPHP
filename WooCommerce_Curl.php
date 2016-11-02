<?php

/**
 * @file WooCommerce_Curl.php
 * @version 1.0
 * @author noelclm (https://github.com/noelclm)
 * @date   01-Noviembre-2016
 * @url    https://github.com/noelclm/libreriasPHP/WooCommerce_Curl.php
 * @description Funciones para conectarse a un WooCommerce mediante CURL
 */

/*
// Ejemplos

$categorias = obtenerWooCommerce("products/categories");

productos = obtenerWooCommerce("products");

$data = array( "product_category" => array("name"=>$nombre));
actualizarWooCommerce("products/categories",$id,$datos);

borrarWooCommerce("products/categories",$id);

$data = array( "product" => array( 'title' => "Titulo", 'description' => "Descripcion", 'sku' => Referencia , 'categories' => array( 'id' => $idCategoria)));
createWooCommerce("products",$data);

*/

// Datos para conectarse al servidor
define("URL", "https://www.tupagina.com"); // URL donde esta la pagina de WooCommerce
define("CK", "CONSUMER_KEY");
define("CS", "CONSUMER_SECRET");
define("SSL_VERIFYPEER", false);
define("SSL_VERIFYHOST", false);
define("RETURNTRANSFER", true);

/**
 * @param string $ruta Ruta del WooCommerce
 * @return object Objeto con la lista de los datos solicitados
 * @description Obtiene los datos de la ruta del WooCommerce que indique
 */
function obtenerWooCommerce($ruta){

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, SSL_VERIFYPEER);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, SSL_VERIFYHOST);
    curl_setopt($curl, CURLOPT_URL, URL."/wc-api/v3/".$ruta);
    curl_setopt($curl, CURLOPT_USERPWD, CK.":".CS);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json;"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, RETURNTRANSFER);

    $resultados = curl_exec($curl); 

    if (curl_errno($curl)) 
            print_r("Error: " . curl_error($curl)); 
    
    curl_close($curl); 
    
    $resultados = json_decode($resultados);
        
    return $resultados;
    
} // obtenerWooCommerce($ruta)

/**
 * @param string $ruta Ruta del WooCommerce
 * @param array $datos Datos a insertar
 * @return object El objeto creado
 * @description Crea una nueva entrada en la ruta que le indiques con los datos pasados
 */
function crearWooCommerce($ruta,$datos){

    $datos = json_encode($datos);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, SSL_VERIFYPEER);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, SSL_VERIFYHOST);
    curl_setopt($curl, CURLOPT_POST, true); 
    curl_setopt($curl, CURLOPT_URL, URL."/wc-api/v3/".$ruta);
    curl_setopt($curl, CURLOPT_USERPWD, CK.":".CS);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json;"));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $datos);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, RETURNTRANSFER);

    $resultados = curl_exec($curl); 

    if (curl_errno($curl)) 
            print_r("Error: " . curl_error($curl)); 
    
    curl_close($curl); 

    $resultados = json_decode($resultados);
    
    return $resultados;
    
} // crearWooCommerce($ruta,$datos)

/**
 * @param string $ruta Ruta del WooCommerce
 * @param int $id ID del dato a borrar
 * @return object El objeto eliminado
 * @description Borra la entrada en la ruta indicada 
 */
function borrarWooCommerce($ruta,$id){

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, SSL_VERIFYPEER);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, SSL_VERIFYHOST);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
    curl_setopt($curl, CURLOPT_URL, URL."/wc-api/v3/".$ruta."/".$id);
    curl_setopt($curl, CURLOPT_USERPWD, CK.":".CS);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, RETURNTRANSFER);

    $resultados = curl_exec($curl); 

    if (curl_errno($curl)) 
            print_r("Error: " . curl_error($curl)); 
    
    curl_close($curl); 

    $resultados = json_decode($resultados);
    
    return $resultados;
    
} // borrarWooCommerce($ruta,$id)

/**
 * @param string $ruta Ruta del WooCommerce
 * @param int $id ID del dato a actualizar
 * @param array $datos Datos a modificar
 * @return object El objeto modificado
 * @description Actualiza la entrada en la ruta indicada
 */
function actualizarWooCommerce($ruta,$id,$datos){

    $datos = json_encode($datos);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, SSL_VERIFYPEER);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, SSL_VERIFYHOST);
    curl_setopt($curl, CURLOPT_POST, true); 
    curl_setopt($curl, CURLOPT_URL, URL."/wc-api/v3/".$ruta."/".$id);
    curl_setopt($curl, CURLOPT_USERPWD, CK.":".CS);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json;"));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $datos);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, RETURNTRANSFER);

    $resultados = curl_exec($curl); 

    if (curl_errno($curl)) 
            print_r("Error: " . curl_error($curl)); 
    
    curl_close($curl); 

    $resultados = json_decode($resultados);
    
    return $resultados;
    
} // actualizarWooCommerce($ruta,$id,$datos)

?>

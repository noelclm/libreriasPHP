<?php

/**
 * Funciones para conectarse a un WooCommerce mediante CURL
 *
 * @version 1.5
 * @author noelclm (https://github.com/noelclm)
 * @link https://github.com/noelclm/libreriasPHP/WooCommerce_Curl.php
 */

/*

// Ejemplos
$num = contarWooCommerce("products");

$categorias = obtenerWooCommerce("products/categories");

$productos = obtenerWooCommerce("products");

$datos = array( "product_category" => array("name"=>$nombre));
actualizarWooCommerce("products/categories",$id,$datos);

borrarWooCommerce("products/categories",$id);

$datos = array( "product" => array( 'title' => "Titulo", 'description' => "Descripcion", 'sku' => Referencia , 'categories' => array( 'id' => $idCategoria)));
createWooCommerce("products",$datos);

*/

// Datos para conectarse al servidor
define("URL", "https://www.tupagina.com"); // URL donde esta la pagina de WooCommerce
define("CK", "CONSUMER_KEY");
define("CS", "CONSUMER_SECRET");
define("SSL_VERIFYPEER", false);
define("SSL_VERIFYHOST", false);
define("RETURNTRANSFER", true);
define("VERSION", "v3");

/**
 * Obtiene el numero de elementos de la ruta del WooCommerce que indique
 *
 * @param string $ruta Ruta del WooCommerce
 * @return int Número de elementos
 */
function contarWooCommerce($ruta){

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, SSL_VERIFYPEER);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, SSL_VERIFYHOST);
    curl_setopt($curl, CURLOPT_URL, PAGINA_WOOCOMERCE_SSL."/wc-api/".VERSION."/".$ruta."/count");
    curl_setopt($curl, CURLOPT_USERPWD, CK.":".CS);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json;"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, RETURNTRANSFER);

    $resultados = curl_exec($curl); 

    if (curl_errno($curl)) 
            print_r("Error: " . curl_error($curl)); 
    
    curl_close($curl); 

    $resultados = json_decode($resultados);

    return $resultados->count;
}

/**
 * Obtiene los datos de la ruta del WooCommerce que indique
 *
 * @param string $ruta Ruta del WooCommerce
 * @return object Objeto con la lista de los datos solicitados
 */
function obtenerWooCommerce($ruta){

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, SSL_VERIFYPEER);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, SSL_VERIFYHOST);
    curl_setopt($curl, CURLOPT_URL, URL."/wc-api/".VERSION."/".$ruta);
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
 * Crea una nueva entrada en la ruta que le indiques con los datos pasados
 *
 * @param string $ruta Ruta del WooCommerce
 * @param array $datos Datos a insertar
 * @return object El objeto creado
 */
function crearWooCommerce($ruta,$datos){

    $datos = json_encode($datos);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, SSL_VERIFYPEER);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, SSL_VERIFYHOST);
    curl_setopt($curl, CURLOPT_POST, true); 
    curl_setopt($curl, CURLOPT_URL, URL."/wc-api/".VERSION."/".$ruta);
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
 * Borra la entrada en la ruta indicada 
 *
 * @param string $ruta Ruta del WooCommerce
 * @param int $id ID del dato a borrar
 * @return string Mensage del servidor
 */
function borrarWooCommerce($ruta,$id){

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, SSL_VERIFYPEER);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, SSL_VERIFYHOST);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
    curl_setopt($curl, CURLOPT_URL, URL."/wc-api/".VERSION."/".$ruta."/".$id);
    curl_setopt($curl, CURLOPT_USERPWD, CK.":".CS);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, RETURNTRANSFER);

    $resultados = curl_exec($curl); 

    if (curl_errno($curl)) 
            print_r("Error: " . curl_error($curl)); 
    
    curl_close($curl); 
    
    $resultados = json_decode($resultados);

    return $resultados->message;
    
} // borrarWooCommerce($ruta,$id)

/**
 * Actualiza la entrada en la ruta indicada
 *
 * @param string $ruta Ruta del WooCommerce
 * @param int $id ID del dato a actualizar
 * @param array $datos Datos a modificar
 * @return object El objeto modificado
 */
function actualizarWooCommerce($ruta,$id,$datos){

    $datos = json_encode($datos);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, SSL_VERIFYPEER);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, SSL_VERIFYHOST);
    curl_setopt($curl, CURLOPT_POST, true); 
    curl_setopt($curl, CURLOPT_URL, URL."/wc-api/".VERSION."/".$ruta."/".$id);
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

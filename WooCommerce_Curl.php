<?php

// Datos para conectarse al servidor
define("URL", ""); // URL donde esta la pagina de WooCommerce
define("CK", ""); // CONSUMER_KEY
define("CS", ""); // CONSUMER_SECRET
define("SSL_VERIFYPEER", false);
define("SSL_VERIFYHOST", false);
define("RETURNTRANSFER", true);

// Obtiene los datos de la ruta del WooCommerce que indique (Ej.: 'products', 'products/orders')
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

    return $resultados;
    
} // obtenerWooCommerce($ruta)

// Crea una nueva entrada en la ruta que le indiques con los datos pasados
function crearWooCommerce($ruta,$datos){

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

    return $resultados;
    
} // crearWooCommerce($ruta,$datos)

// Borra la entrada en la ruta indicada 
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

    return $resultados;
    
} // borrarWooCommerce($ruta,$id)

// Actualiza la entrada en la ruta indicada
function actualizarWooCommerce($ruta,$id,$datos){

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

    return $resultados;
    
} // actualizarWooCommerce($ruta,$id,$datos)

?>
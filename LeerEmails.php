<?php

/**
 * Descargar emails y sus adjuntos mediante IMAP y PHP
 *
 * @version 1.0
 * @author noelclm (https://github.com/noelclm)
 * @link https://github.com/noelclm/libreriasPHP/LeerEmails.php
 */

/*

// Ejemplo

$imap = new LeerEmails("{imap.gmail.com:993/imap/ssl}INBOX", "usuario", "clave");

if($imap){

    $emails = $imap->traer_emails();

    foreach ($emails as $clave => $valor) {
        $imap->marcar_como_leido( $valor['numero_email']);
        $imap->borrar_email( $valor['numero_email']);
    }

}

$imap->cerrar(); // cierra la conexion

*/

class LeerEmails {
    
    /**
     * Objeto de la conexión
     * @var object
     * @access protected
     */
    protected $inbox;
    /**
     * Direccion del servidor
     * @var string
     * @access protected
     */
    protected $servidor;
    /**
     * Usuario de la cuenta
     * @var string
     * @access protected
     */
    protected $usuario;
    /**
     * Contraseña de la cuenta
     * @var string
     * @access protected
     */
    protected $clave;
    /**
     * Criterio para la busqueda de correo
     * @var string
     * @access protected
     */
    protected $criterioBusqueda;
    /**
     * Carpeta temporal donde almacenar los adjuntos
     * @var string
     * @access protected
     */
    protected $carpetaTemporal;
    /**
     * Limite del tamaño de los ficheros a coger
     * @var int
     * @access protected
     */
    protected $limiteFicheros;

    /**
     * Constructor
     *
     * Establece una conexión con el servidor
     *
     * @param string $servidor Dirección del servidor
     * @param string $usuario Usuario del servidor
     * @param string $clave Contraseña del servidor
     * @global string $servidor
     * @global string $usuario
     * @global string $clave
     * @global string $criterioBusqueda
     * @global string $carpetaTemporal
     * @global int $limiteFicheros
     * @global objet $inbox
     * @return mixe True si se ha realizado la conexion correctamente, el error en caso contrario
     */
    function __construct ($servidor, $usuario, $clave) {

        $this->servidor = $servidor;
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->criterioBusqueda = 'UNSEEN';
        $this->carpetaTemporal = 'tmp/';
        $this->limiteFicheros = 10485760; // en bytes
        
        // Conectamos al servidor de correo (Si falla hay que cambiar en php.ini ";extension=php_imap.dll" por "extension=php_imap.dll")
        if(!($this->inbox = imap_open($servidor,$usuario,$clave))){
            return imap_last_error();
        }else{
            return true;
        }
        
        
    } // function __construct

    /**
     * Devuelve un array con los datos de los emails
     *
     * @param array $opciones Opciones de la busqueda
     * @param string $usuario Usuario del servidor
     * @param string $clave Contraseña del servidor
     * @global string $criterioBusqueda
     * @global string $carpetaTemporal
     * @global int $limiteFicheros
     * @global objet $inbox
     * @return array Emails que se ha traido del servidor
     */
    function traer_emails ($opciones = '') {

        $arrayEmails = array();

        if($opciones != '' && is_array($opciones)){

            if(isset($opciones['criterioBusqueda'])){ $this->criterioBusqueda = $opciones['criterioBusqueda']; }
            if(isset($opciones['carpetaTemporal'])){ $this->carpetaTemporal = $opciones['carpetaTemporal']; }
            if(isset($opciones['limiteFicheros'])){ $this->limiteFicheros = $opciones['limiteFicheros'];}

        } // if($opciones != '' && is_array($opciones))

        // Busca correos
        $emails = imap_search($this->inbox ,$this->criterioBusqueda);

        // Recorremos los correos
        foreach($emails as $numero_email){

            // Traemos la cabecera 
            $cabecera = imap_headerinfo($this->inbox,$numero_email,0);

            // Miramos el asunto, de quien es y a quien va dirigido   
            $asunto = imap_utf8($cabecera->subject);
            $para = $cabecera->to[0]->mailbox."@".$cabecera->to[0]->host;
            $de = $cabecera->from[0]->mailbox."@".$cabecera->from[0]->host;

            // Miramos el tipo de escructura y la codificacion del correo
            $estructura = imap_fetchstructure($this->inbox,$numero_email);
            $tipo = $estructura->type;
            $codificacion = $estructura->encoding ;

            // Cogemos los ficheros adjuntos
            $adjuntos = $this->traer_adjuntos($estructura,$numero_email);

            // Cogemos el cuerpo del mensaje sin marcarlo como leido
            if($estructura->parts[0]->type == 0){ // Si no tiene ficheros adjuntos
                $cuerpo = imap_utf8(imap_fetchbody($this->inbox,$numero_email,$tipo,FT_PEEK)); 
            }else{ // Si tiene ficheros adjuntos
                $cuerpo = imap_utf8(imap_fetchbody($this->inbox,$numero_email,$tipo.'.1',FT_PEEK)); 
            }
            
            // Decodificamos el cuerpo y el asunto
            $cuerpo = $this->decodificar($codificacion,$cuerpo);
            $asunto = $this->decodificar($codificacion,$asunto);

            $arrayEmails[] = array( 'para' => $para, 
                                    'de' => $de, 
                                    'asunto' => $asunto, 
                                    'cuerpo' => $cuerpo, 
                                    'numero_email' => $numero_email, 
                                    'adjuntos' => $adjuntos );
        
        } // foreach($emails as $numero_email)

        return $arrayEmails;

    } // function traer_emails

    /**
     * Marca como leido el mensaje indicado
     *
     * @param int $numero_email Numero del email
     * @global objet $inbox
     * @return boolean 
     */
    function marcar_como_leido ($numero_email){

        return imap_setflag_full($this->inbox, $numero_email, '\Seen');

    } // function marcar_como_leido

    /**
     * Borra el mensaje indicado
     *
     * @param int $numero_email Numero del email
     * @global objet $inbox
     * @return boolean 
     */
    function borrar_email ($numero_email){

        return imap_delete($this->inbox, $numero_email);

    } // function borrar_email

    // Cerrar conexion
    /**
     * Cerrar conexion
     *
     * @global objet $inbox
     */
    function cerrar (){

        imap_close($this->inbox);

    } // function cerrar

    //---------------------------------------------------------------------------------------------------------
    //                                 Funciones para usar solo en la clase
    //---------------------------------------------------------------------------------------------------------

    /**
     * Se trae los documentos adjuntos
     *
     * @access protected
     * @param object $estructura Estructura del email
     * @param int $numero_email Numero del email
     * @return array Informacion de los adjuntos
     */
    protected function traer_adjuntos ($estructura, $numero_email){

        // Miramos si tiene adjuntos
        $numFicheros = 0;
        $numAdjunto = 2; // Empieza en 2 porque el 1 es el cuerpo del mensaje
        $adjuntos = array();

        foreach ($estructura->parts as $clave => $valor){

            if($clave != 0 && $valor->ifdparameters){

                // Cogemos los datos del fichero
                $nombreFichero = imap_utf8($valor->dparameters[0]->value);
                $subtipoFichero = $valor->subtype;
                $tamanyoFichero = $valor->bytes;
                $codificacionFichero = $valor->encoding;
                $tipoFichero = $this->nombreTipo($valor->type);
                $nombreTemporal = $this->cadenaAleatoria(10);
                
                // Si el fichero es mas pequeño del limite
                if($tamanyoFichero < $this->limiteFicheros){

                    // Cogemos el contenido codificado del fichero adjunto, sin marcar como leido el mail
                    $contenidoFichero = imap_fetchbody($this->inbox,$numero_email,$numAdjunto,FT_PEEK);
                    // Decodificamos el fichero
                    $contenidoFichero = $this->decodificar($codificacionFichero,$contenidoFichero);

                    // Lo creamos en la carpeta temporal
                    $temporal = $this->carpetaTemporal.$nombreTemporal;
                    file_put_contents($temporal,$contenidoFichero);  
                    
                    // Guardamos los datos del fichero para tratarlo al crear la tarea
                    $adjuntos[$numFicheros]['tipo'] = $tipoFichero."/".$subtipoFichero;
                    $adjuntos[$numFicheros]['nombre'] = $nombreFichero;
                    $adjuntos[$numFicheros]['nombreTemporal'] = $temporal;
                    $adjuntos[$numFicheros]['tamanyo'] = strlen($contenidoFichero);

                    $numFicheros++;
                    $numAdjunto++;

                } // if($tamanyoFichero < $this->limiteFicheros)

            } // if($clave != 0 && $valor->ifdparameters)

        } // foreach ($estructura->parts as $clave => $valor)

        return $adjuntos;

    } // function traer_adjuntos

    /**
     * Decofica una cadena
     *
     * @access protected
     * @param int $encoding Tipo de codificación
     * @param string $contenido Cadena de texto
     * @return string Texto decodificado
     */
    protected function decodificar ($encoding, $contenido){

        if ($encoding == 0){ 
            return quoted_printable_decode($contenido);
        }elseif ($encoding == 1){
            return imap_8bit($contenido);
        }elseif ($encoding == 2){
            return imap_binary($contenido);
        }elseif ($encoding == 3){
            return imap_base64($contenido);
        }elseif ($encoding == 4){
            return imap_qprint($contenido);
        }else{
            return $contenidoFichero;
        }

    }

    /**
     * Devuelve en texto el tipo 
     *
     * @access protected
     * @param int $tipo Tipo de correo
     * @return string Nombre del tipo
     */
    protected function nombreTipo ($tipo){

        if($tipo == 0){
            return 'text';
        }elseif($tipo == 1){
            return 'multipart';
        }elseif($tipo == 2){
            return 'message';
        }elseif($tipo == 3){
            return 'application';
        }elseif($tipo == 4){
            return 'audio';
        }elseif($tipo == 5){
            return 'image';
        }elseif($tipo == 6){
            return 'video';
        }elseif($tipo == 7){
            return 'model';
        }else{
            return 'other';
        }

    } // function nombreTipo

    /**
     * Genera una cadena aleatorea de alfanumerica
     *
     * @access protected
     * @param int $longitud Longitud de la cadena, es opcional, por defecto es de 10
     * @return string Cadena aleatorea
     */
    protected function cadenaAleatoria ($longitud = 10){

        $caracteres  = "0123456789abcdefghijklmnopqrstuvwxyz";

        // Iniciamos la semilla de numeros aleatorios y la key
        srand((double)microtime()*1000000);
        $cadena = "";

        // Creamos la cadena de N caracteres;
        for($x=0;$x<$longitud;$x++){

            $aleatorio = rand(0,35);
            $cadena .=  substr($caracteres, $aleatorio, 1);

        } // for($x=0;$x<$longitud;$x++)

        return $cadena;

    } // function cadenaAleatoria

} // class LeerEmails

?>

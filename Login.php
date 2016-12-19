<?php

/**
 * Comprobacion de login
 *
 * @version 1.0
 * @author noelclm (https://github.com/noelclm)
 * @url https://github.com/noelclm/libreriasPHP/Login.php
 */

//-------------------------------------------------------------------
// Definiciones (Opciones)
//-------------------------------------------------------------------

define("VARIAS_SESIONES", true); // Deja o no que puedas estar logueado en varios sitios
define("TIEMPO_LOGIN", 604800); // En segundos (Un dia = 86400, Una semana = 604800)

/*

Contraseñas tienen que estar guardadas con password_hash()

// Tablas necesaria

# -------------------------------------------
# Estructura de `sesion`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `sesion` (
    `idsesion`          INT(11) NOT NULL AUTO_INCREMENT,
    `idusuario`         INT(11) NOT NULL,
    `key`               VARCHAR(32) NOT NULL,
    `activodesde`       INT(11) NOT NULL,
    `ultimaactividad`   INT(11) NOT NULL,
    `ip`                VARCHAR(15) NOT NULL,
    PRIMARY KEY  (`idsesion`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

# -------------------------------------------
# Estructura de `usuario`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `usuario` (
    `IDUsuario`         INT(11) NOT NULL AUTO_INCREMENT,
    `nombre`            VARCHAR(75) NOT NULL,
    `usuario`           VARCHAR(75) NOT NULL,
    `clave`             VARCHAR(255) NOT NULL,
    `email`             VARCHAR(255) NOT NULL,
    `rol`               INT(11) NOT NULL,
    PRIMARY KEY  (`IDUsuario`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;


Ejemplo del formulario login

<form action="index.php" method="post">
    <label for="Usuario">Nombre:</label><input type="text" name="Usuario" required ><br><br>
    <label for="clave">Contraseña:</label><input type="password" name="clave" required ><br><br>
    <label for="guardarSesion">Guardar</label><input type="checkbox" value="true" name="guardarSesion" ><br><br>
    <input type="hidden" name="login" value=true>
    <button type="submit">Entrar</button>
</form>

Ejemplo de uso

$login = new Login();
$codigo = $login->comprobarLogin();

*/

// Fichero necesario https://github.com/noelclm/libreriasPHP/AccesoDatos.php
require_once('AccesoDatos.php');
// Fichero necesario https://github.com/noelclm/libreriasPHP/Funciones.php
require_once('Funciones.php');

class Login {

    /**
     * Constructor
     */
    function __construct (){

        session_start();
        session_regenerate_id(true);

    } // function __construct

    /**
     * Hace todas las comprobaciones al cargar la pagina
     * Devuelve:
     *  1000 = Esta logueado
     *  1001 = Ha hecho login  
     *  1002 = Usuario o contraseña incorrecta 
     *  1003 = Ha hecho logout 
     *  1004 = Sesion expirada
     *  1005 = No esta logueado
     *
     * @return int Codigo mensaje 
     */
    function comprobarLogin (){

        if(!isset($_SESSION['Login'])) $_SESSION['Login'] = false;

        // Borra las sesiones que han caducado
        borrarSesionesAntiguas();

        // Si ha pulsado en logout
        if(((isset($_GET['logOut']) && $_GET['logOut'] === true) || (isset($_POST['logOut']) && $_POST['logOut'] === true)) && (isset($_SESSION['Login']) && $_SESSION['Login'] === true)){
            
            unset($_POST);
            unset($_GET);
            logOut();
            return 1003;

        }

        // Si no esta logeado comprueba las cookies
        if(!$_SESSION['Login'])
            comprobarCookie();

        // Si esta logueado mira si ha expirado la sesion
        if($_SESSION['Login']){
            if(!comprobarSesion())
                return 1004;
        }else{
            return 1005
        }
        

        // Si se acaba de loguear
        if(isset($_POST['login']) && $_POST['login'] === true && isset($_POST['Usuario']) && isset($_POST['clave'])){

            // Si no esta marcado para que se guarde la sesion
            if(!isset($_POST['guardarSesion'])) 
                $_POST['guardarSesion'] = false;

            // Revisa si va codigo en los datos de entrada
            $usuario = escaparCodigo($_POST['Usuario']);
            $clave = escaparCodigo($_POST['clave']);

            if(login($usuario,$clave,$_POST['guardarSesion'])){
                unset($_POST);
                return 1001;
            }else{
                unset($_POST);
                return 1002;
            }

        }

        // Sigue logueado
        return 1000;

    } // function comprobarLogin

    /**
     * Borra de la base de datos las sesiones inactivas
     *
     * @access protected
     */
    protected function borrarSesionesAntiguas (){
        
        if(isset($_SESSION['Key']))
            borrarSQL("DELETE FROM sesion WHERE ultimaactividad + ". TIEMPO_LOGIN ." < " . time() . ";"); 

    } // function borrarSesionesAntiguas

    /**
     * Comprueba las cookies con los datos de la base de datos
     *
     * @access protected
     */
    protected function comprobarCookie (){

        if(isset($_COOKIE["IDUsuario"]) && isset($_COOKIE["Key"]) && isset($_COOKIE["Login"]) && isset($_COOKIE["DireccionIP"]) && isset($_COOKIE["Rol"])&& isset($_COOKIE["Usuario"]) && isset($_COOKIE["Nombre"])){
           
            $resultado = consultaSQL("SELECT * FROM `sesion` WHERE `ip` = '".$_COOKIE['DireccionIP']."' AND `IDUsuario` = ".$_COOKIE['IDUsuario']." AND `key` = '".$_COOKIE['Key']."';");

            if(count($resultado)){

                $_SESSION['Nombre'] = $_COOKIE['Nombre'];
                $_SESSION['Usuario'] = $_COOKIE['Usuario'];
                $_SESSION['IDUsuario'] = $_COOKIE['IDUsuario'];
                $_SESSION['Key'] = $_COOKIE['Key'];
                $_SESSION['DireccionIP'] = $_COOKIE['DireccionIP'];
                $_SESSION['LastActivity'] = $_SERVER['REQUEST_TIME'];
                $_SESSION['Login'] = $_COOKIE['Login'];
                $_SESSION['Rol'] = $_COOKIE['Rol'];

            } // if(count($resultado))

        }  // if(isset($_COOKIE["IDUsuario"])...

    } // function comprobarCookie

    /**
     * Comprueba los datos de la variable $_SESSION con los datos de la base de datos y guarda el ultimo acceso
     *
     * @access protected
     * @return boolean True si el login es valido
     */
    protected function comprobarSesion (){

        $resultado = consultaSQL("SELECT * FROM `session` WHERE `key` = '".$_SESSION['Key']."';");

        if(count($resultado)){

            $resultado = $resultado[0];

            if($resultado['idusuario'] == $_SESSION['IDUsuario'] && $resultado['ip'] == $_SESSION['DireccionIP']){

                modificarSQL("UPDATE sesion SET `ultimaactividad` = ".$_SERVER['REQUEST_TIME']." WHERE `key` = '".$_SESSION['Key']."';");
                return true;

            } // if($resultado['IDUsuario'] == $_SESSION['IDUsuario']...
            
        } // if(count($resultado))

        logOut();
        return false;

    } // function comprobarSesion

    /**
     * Comprueba si el usuario y contraseña introducidos son correctos
     *
     * @access protected
     * @param string $nombre Nombre del usuario
     * @param string $psw Contraseña del usuario
     * @param boolean $save Si esta marcada guardar la sesion
     * @return boolean Devuelve true si se ha registrado el logueo
     */
    protected function logIn ($nombre, $psw, $save){

        $resultado = consultaSQL("SELECT * FROM usuario WHERE usuario = '".$nombre."' ;");

        if(count($resultado) == 1){

            if(password_verify($psw, $resultado[0]['psw'])){

                $_SESSION['Nombre'] = $resultado[0]['nombre'];
                $_SESSION['Usuario'] = $resultado[0]['usuario'];
                $_SESSION['IDUsuario'] = $resultado[0]['IDUsuario'];
                $_SESSION['Key'] = session_id();
                $_SESSION['DireccionIP'] = userDireccionIP();
                $_SESSION['UltimaActividad'] = $_SERVER['REQUEST_TIME'];
                $_SESSION['Login'] = true;
                $_SESSION['Rol'] = $resultado[0]['rol']; 

                if($save){

                    setcookie("IDUsuario", $_SESSION['IDUsuario'] , time()+(TIEMPO_LOGIN));
                    setcookie("Key", $_SESSION['Key'], time()+(TIEMPO_LOGIN));
                    setcookie("Login", $_SESSION['Login'], time()+(TIEMPO_LOGIN));
                    setcookie("DireccionIP", $_SESSION['DireccionIP'], time()+(TIEMPO_LOGIN));
                    setcookie("Rol", $_SESSION['Rol'], time()+(TIEMPO_LOGIN));
                    setcookie("Usuario", $_SESSION['Usuario'], time()+(TIEMPO_LOGIN));
                    setcookie("Nombre", $_SESSION['Nombre'], time()+(TIEMPO_LOGIN));

                } // if($save)
                
                // Si no deja varias sesiones a la vez por usuario borra las anteriores
                if(!VARIAS_SESIONES)
                    borrarSQL("DELETE FROM `sesion` WHERE `idusuario` = '".$_SESSION['IDUsuario']."';");

                insertarSQL("INSERT INTO `sesion` (`idusuario`,`key`,`activodesde`,`ultimaactividad`,`ip`) VALUES (".$_SESSION['IDUsuario'].",'".$_SESSION['Key']."',".$_SESSION['ActivoDesde'].",".$_SESSION['UltimaActividad'].",'".$_SESSION['DireccionIP']."');");
                return true;
                
            } // if(password_verify($psw, $resultado[0]['psw']))

        } // if(count($resultado) == 1)

        return false;
        
    } // function logIn

    /**
     * Borra los datos de logueo
     *
     * @access protected
     */
    protected function logOut (){
        
        if(isset($_SESSION['Key']))
            borrarSQL("DELETE FROM `sesion` WHERE `key` = '".$_SESSION['Key']."';");

        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
        $_SESSION['Login'] = false;

    } // function logOut

    /**
     * Mira la ip desde donde se accede a la web
     *
     * @access protected
     * @return string IP del cliente
     */
    protected function ipCliente (){

        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];

    } // function ipCliente()

} // class Seguridad

?>
<?php

/**
 * Comprobacion de login
 *
 * @version 2.0
 * @author noelclm (https://github.com/noelclm)
 * @url https://github.com/noelclm/libreriasPHP/Login.php
 */

//-------------------------------------------------------------------
// Definiciones (Opciones)
//-------------------------------------------------------------------

define("IDLE_TIME", 604800); // Seconds (1 dia = 86400, 1 semana = 604800, infinito = 0)
define("MULTIPLE_SESSIONS", false);

/*

Contraseñas tienen que estar guardadas con password_hash()

// Tablas necesaria

# -------------------------------------------
# Estructura de `session`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `session` (
    `session_id`        INT(11) NOT NULL AUTO_INCREMENT,
    `user_id`           INT(11) NOT NULL,
    `key`               VARCHAR(32) NOT NULL,
    `last_active`       INT(11) NOT NULL,
    `ip`                VARCHAR(15) NOT NULL,
    PRIMARY KEY  (`session_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

# -------------------------------------------
# Estructura de `user`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `user` (
    `user_id`           INT(11) NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(75) NOT NULL,
    `user`              VARCHAR(75) NOT NULL,
    `password`          VARCHAR(255) NOT NULL,
    `email`             VARCHAR(255) NOT NULL,
    `role`              INT(11) NOT NULL,
    PRIMARY KEY  (`user_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;


Ejemplo del formulario login

<form action="" method="post">
    <label for="user">Nombre:</label><input type="text" name="user" required ><br><br>
    <label for="password">Contraseña:</label><input type="password" name="password" required ><br><br>
    <label for="save">Guardar</label><input type="checkbox" value="true" name="save" ><br><br>
    <input type="hidden" name="login" value=true>
    <button type="submit">Entrar</button>
</form>

Ejemplo de uso

$login = new Login();
$codigo = $login->check();

Las contraseñas tienen que estar guardadas con password_hash()

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

    } 

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
    public function check (){

        if(!isset($_SESSION['login'])){ 
            $_SESSION['login'] = false; 
            return 1005;
        }

        // Borra las sesiones que han caducado
        $this->deleteOldSession();

        // Si ha pulsado en logout
        if(((isset($_GET['logout']) && $_GET['logout'] == true) || (isset($_POST['logout']) && $_POST['logout'] == true)) && (isset($_SESSION['login']) && $_SESSION['login'] == true)){
            $this->logOut();
            return 1003;
        }

        // Si no esta logeado comprueba las cookies
        if (!$_SESSION['login']) { $this->checkCookie(); } 
        else { // Si esta logueado mira si ha expirado la sesion
            if(!$this->chekSesion()){ return 1004; } 
        }
        
        // Si se acaba de loguear
        if(isset($_POST['login']) && $_POST['login'] == true && isset($_POST['user']) && isset($_POST['password'])){
            
            // Si no esta marcado para que se guarde la sesion
            if(!isset($_POST['save'])){ $_POST['save'] = false; }

            if($this->login(escaparCodigo($_POST['user']),escaparCodigo($_POST['password']),$_POST['save'])){ return 1001; }
            else{ return 1002; }
            
            unset($_POST);

        }

        return 1000;

    } 

    /**
     * Borra de la base de datos las sesiones inactivas
     *
     */
    private function deleteOldSession (){
        
        if(IDLE_TIME > 0 && isset($_COOKIE["save"])){
            $bd = new AccesoDatos();
            $bd->ejecutar("DELETE FROM session WHERE last_active + ". IDLE_TIME ." < " . time() . ";");
            $bd->cerrar();
            unset($bd);
        }

    } 

    /**
     * Comprueba las cookies con los datos de la base de datos
     *
     */
    private function checkCookie (){

        if(isset($_COOKIE["user_id"]) && isset($_COOKIE["key"]) && isset($_COOKIE["login"]) && isset($_COOKIE["ip"]) && isset($_COOKIE["user"]) && isset($_COOKIE["name"])){
            
            $bd = new AccesoDatos();
            $execute = $bd->ejecutar("SELECT * FROM `session` WHERE `ip` = '".$_COOKIE['ip']."' AND `IDUsuario` = ".$_COOKIE['user_id']." AND `key` = '".$_COOKIE['key']."';");
            
            if ($execute) {
                while(($row = $bd->nextRow()) !== false){
                    $result[] = $row;
                }
            }
            
            if(count($result)){

                $_SESSION['name'] = $_COOKIE['name'];
                $_SESSION['user'] = $_COOKIE['user'];
                $_SESSION['user_id'] = $_COOKIE['user_id'];
                $_SESSION['key'] = $_COOKIE['key'];
                $_SESSION['ip'] = $_COOKIE['ip'];
                $_SESSION['last_activity'] = $_SERVER['REQUEST_TIME'];
                $_SESSION['login'] = $_COOKIE['login'];

            }
            
            $bd->cerrar();
            unset($bd);

        }

    }

    /**
     * Comprueba los datos de la variable $_SESSION con los datos de la base de datos y guarda el ultimo acceso
     *
     * @return boolean True si el login es valido
     */
    private function chekSesion (){

        $bd = new AccesoDatos();
        $execute = $bd->ejecutar("SELECT * FROM `session` WHERE `key` = '".$_SESSION['key']."';");

        if ($execute) {
            while(($row = $bd->nextRow()) !== false){
                $result[] = $row;
            }
        }

        if(count($result)){

            $result = $result[0];

            if($result['user_id'] == $_SESSION['user_id'] && $result['ip'] == $_SESSION['ip']){

                $bd->ejecutar("UPDATE session SET `last_active` = ".$_SERVER['REQUEST_TIME']." WHERE `key` = '".$_SESSION['key']."';");
                $bd->cerrar();
                unset($bd);
                return true;

            }
            
        } 
        
        $bd->cerrar();
        unset($bd);
        logOut();
        return false;

    } 

    /**
     * Comprueba si el usuario y contraseña introducidos son correctos
     *
     * @param string $name Nombre del usuario
     * @param string $psw Contraseña del usuario
     * @param boolean $save Si esta marcada guardar la sesion
     * @return boolean Devuelve true si se ha registrado el logueo
     */
    private function logIn ($name, $psw, $save){

        $bd = new AccesoDatos();
        $execute = $bd->ejecutar("SELECT * FROM user WHERE user = '".$name."' ;");
        
        if ($execute) {
            while(($row = $bd->nextRow()) !== false){
                $result[] = $row;
            }
        }
                
        if(count($result) == 1){

            if(password_verify($psw, $result[0]['password'])){

                $_SESSION['name'] = $result[0]['name'];
                $_SESSION['user'] = $result[0]['user'];
                $_SESSION['user_id'] = $result[0]['user_id'];
                $_SESSION['key'] = session_id();
                $_SESSION['ip'] = $this->clientIp();
                $_SESSION['last_active'] = $_SERVER['REQUEST_TIME'];
                $_SESSION['login'] = true;

                if($save){
                    setcookie("name", $_SESSION['name'], time()+(TIEMPO_LOGIN));
                    setcookie("user", $_SESSION['user'], time()+(TIEMPO_LOGIN));
                    setcookie("user_id", $_SESSION['user_id'] , time()+(TIEMPO_LOGIN));
                    setcookie("key", $_SESSION['key'], time()+(TIEMPO_LOGIN));
                    setcookie("ip", $_SESSION['ip'], time()+(TIEMPO_LOGIN));
                    setcookie("login", $_SESSION['login'], time()+(TIEMPO_LOGIN));
                } 
                
                // Si no deja varias sesiones a la vez por usuario borra las anteriores
                if(!MULTIPLE_SESSIONS){
                    $bd->ejecutar("DELETE FROM `session` WHERE `user_id` = '".$_SESSION['user_id']."';");
                }

                $bd->ejecutar("INSERT INTO `session` (`user_id`,`key`,`last_active`,`ip`) VALUES (".$_SESSION['user_id'].",'".$_SESSION['key']."',".$_SESSION['last_active'].",'".$_SESSION['ip']."');");
                $bd->cerrar();
                unset($bd); 
                return true;
                
            } 

        } 

        $bd->cerrar();
        unset($bd);
        return false;
        
    } 

    /**
     * Borra los datos de logueo
     * 
     */
    private function logOut (){
        
        unset($_POST);
        unset($_GET);
            
        if(isset($_SESSION['key'])){
            $bd = new AccesoDatos();
            $bd->ejecutar("DELETE FROM `sesion` WHERE `key` = '".$_SESSION['key']."';");
            $bd->cerrar();
            unset($bd); 
        }

        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
        $_SESSION['login'] = false;

    }

    /**
     * Mira la ip desde donde se accede a la web
     *
     * @return string IP del cliente
     */
    private function clientIp (){

        if (!empty($_SERVER['HTTP_CLIENT_IP'])){
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];

    } 

} 
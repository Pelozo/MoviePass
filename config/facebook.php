<?php

/*Autoload facebook */
require_once 'sdk-facebook/autoload.php';
 
/**************Facebook API****************/
// librerias facebook
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
 
//llaves de facebook verifique en https://developers.facebook.com/
$appId         = ''; //Identificador de la Aplicación
$appSecret     = ''; //Clave secreta de la aplicación
$redirectURL   = ''; //Callback URL
$fbPermissions = array('');  //Permisos opcionales
 
//creamos var fb y almacenamos el array de credenciales
$fb = new Facebook(array(
    'app_id' => $appId,
    'app_secret' => $appSecret,
    'default_graph_version' => 'v2.9',
));
 
// Obtener el apoyo de logueo
$helper = $fb->getRedirectLoginHelper();
 
// obtener el token
try {
    if(isset($_SESSION['facebook_access_token'])){
        $accessToken = $_SESSION['facebook_access_token'];
    }else{
          $accessToken = $helper->getAccessToken();
    }
} catch(FacebookResponseException $e) {
     echo 'Graph returned an error: ' . $e->getMessage();
      exit;
} catch(FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
}
/************end facebook api***************/
//Utilizamos la clase ORM de idiorm class
ORM::configure("mysql:host=".DB_HOST.";dbname=".DB_NAME);
ORM::configure('username', DB_USER);
ORM::configure('password', DB_PASS);
// cambiamos el juego de caracteres
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.DB_CHARSET));


?>

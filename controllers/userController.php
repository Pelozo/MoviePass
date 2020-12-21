<?php
namespace controllers;

use models\user as User;
use models\userProfile as Profile;
use daos\userDaos as UserDaos;
use daos\userProfileDaos as UserProfileDaos;
use daos\TicketDaos as TicketDaos;

//fb stuff
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
//Autoload facebook
require_once 'facebook/autoload.php';

class UserController{
    private $daos;
    private $userProfileDaos;
    private $movieController;

    const FB_ERR = "Ocurrió un error al conectar con facebook";

    public function __construct(){
        $this->daos = new UserDaos();
        $this->userProfileDaos = new UserProfileDaos();
        $this->movieController = new MovieController();
        $this->ticketDaos = new TicketDaos();
    }

    public function signup($email = null, $password = null, $firstName = null, $lastName = null, $dni = null){
        
        if(isset($email, $password, $firstName, $lastName, $dni)){        
            
            try{
                if($this->daos->exists($email)){
                    $err = 'Ya hay un usuario registrado con ese email';
                    require_once(VIEWS_PATH . "signup.php");
                }else{
                    $user = new User($email,$password,2);
                    $this->daos->add($user); 
                    $_user = $this->daos->getByEmail($email);
                    $profile = new Profile($firstName, $lastName, $dni);
                    $profile->setIdUser($_user->getId());
                    $this->userProfileDaos->add($profile);
                    require_once(VIEWS_PATH . "login.php");
                } 
            } catch(\Exception $err){
                $err = DATABASE_ERR;
                require_once(VIEWS_PATH . "signup.php");
            }
        } else {
            require_once(VIEWS_PATH . "signup.php");
        }
    }

    public function login($email = null, $password = null){

        if(isset($email, $password)){
            try{
                $user = $this->daos->getByEmail($email);
                $err = null;
                if($user != null){
                    if ($user->getPassword() == $password){
                        $_SESSION['user'] = $user;
                        $profile = $this->userProfileDaos->getById($user->getId());
                        $_SESSION['profile'] = $profile;
                        if(isset($_COOKIE['redirect'])){
                            $redirect = $_COOKIE['redirect'];
                            //remove cookie
                            setcookie("redirect", "$redirect", 1, "/");
                            header("Location: " . FRONT_ROOT . "$redirect"); //justificado
                        }else{
                            $this->movieController->index();
                        }
                        return;                        
                    }else {
                        $err = 'Contraseña incorrecta';
                    }
                } else {
                    $err = 'Usuario no encontrado';
                }
            }catch(\Exception $err){
                $err = DATABASE_ERR;
            }
        }
        require_once(VIEWS_PATH . "login.php");
    }

    public function checkFbPermission(){

        $fb = new Facebook(array(
            'app_id' => FB_APP_ID,
            'app_secret' => FB_APP_SECRET,
            'default_graph_version' => 'v2.9',
        ));

        try {
            
            $helper = $fb->getRedirectLoginHelper();
            $accessToken = $helper->getAccessToken();
            $fb->setDefaultAccessToken($accessToken);
            $response = $fb->get('me?fields=email');
          } catch(FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
          } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
          }
          $response = $response->getGraphNode()->asArray();
          if(!isset($response['email'])){
                $err = "Se necesita el email para poder registrase";
                require_once(VIEWS_PATH . "signup.php");
                exit;
          }

        if(isset($accessToken)){            
            if(isset($_SESSION['facebook_access_token'])){
                $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            }else{
                // Token de acceso de corta duración en sesión
                $_SESSION['facebook_access_token'] = (string) $accessToken;
           
                  // Controlador de cliente OAuth 2.0 ayuda a administrar tokens de acceso
                $oAuth2Client = $fb->getOAuth2Client();
           
                // Intercambia una ficha de acceso de corta duración para una persona de larga vida
                $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
                $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
           
                // Establecer token de acceso predeterminado para ser utilizado en el script
                $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            }

            // Obtener información sobre el perfil de usuario facebook
            try {
                $profileRequest = $fb->get('/me?fields=name,first_name,last_name,email,picture');
                $fbUserProfile = $profileRequest->getGraphNode()->asArray();
            } catch(FacebookResponseException $e) {
                $err =  SELF::FB_ERR . $e->getMessage();
                require_once(VIEWS_PATH . "signup.php");
                return;
            } catch(FacebookSDKException $e) {
                $err =  SELF::FB_ERR . $e->getMessage();
                require_once(VIEWS_PATH . "signup.php");
                return;
            }

           
            try{
                 //register in db
                if(!$this->daos->exists($fbUserProfile['email'])){
                    $user = new User($fbUserProfile['email'],null,2);
                    $this->daos->add($user); 
                    $_user = $this->daos->getByEmail($user->getEmail());
                    $profile = new Profile($fbUserProfile['first_name'], $fbUserProfile['last_name'], null);
                    $profile->setIdUser($_user->getId());
                    $this->userProfileDaos->add($profile);
                    
                    $_SESSION['user'] = $_user;
                    $_SESSION['profile'] = $profile;
                }else{//just login
                    $user = $this->daos->getByEmail($fbUserProfile['email']);
                    $userProfile = $this->userProfileDaos->getById($user->getId());
                    $_SESSION['user'] = $user;
                    $_SESSION['profile'] = $userProfile;
                }
            } catch(\Exception $err){
                $err = DATABASE_ERR;
                require_once(VIEWS_PATH . "signup.php");
                return;;
            }

        }

        if(isset($_COOKIE['redirect'])){
            $redirect = $_COOKIE['redirect'];
            //remove cookie
            setcookie("redirect", "$redirect", 1, "/");
            header("Location: " . FRONT_ROOT . "$redirect"); //justificado
        }else{
            $this->movieController->index();
        }
    }



    public function signupFacebook(){
        
        $fb = new Facebook(array(
            'app_id' => FB_APP_ID,
            'app_secret' => FB_APP_SECRET,
            'default_graph_version' => 'v2.9',
        ));

        //permission needed
        $fbPermissions = array('email');

        $helper = $fb->getRedirectLoginHelper(); 

        //get token
        try {
            if(isset($_SESSION['facebook_access_token'])){
                $accessToken = $_SESSION['facebook_access_token'];
            }else{
                $accessToken = $helper->getAccessToken();
            }
        } catch(FacebookResponseException $e) {
            $err= 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            $err = 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        } catch(\Exception $e){
            $err = SELF::FB_ERR;
            exit;           
        }

 

        //ask for user permission
        $loginURL = $helper->getLoginUrl(FB_REDIRECTION, $fbPermissions);
        header('Location: '.$loginURL);        
        
    }

    public function loginFacebook(){

        $this->signupFacebook();

        echo "asd";
        return;


        if(isset($_COOKIE['redirect'])){
            $redirect = $_COOKIE['redirect'];
            //remove cookie
            setcookie("redirect", "$redirect", 1, "/");
            header("Location: " . FRONT_ROOT . "$redirect"); //justificado
        }else{
            $this->movieController->index();
        }
    }

    public function logout(){
        $_SESSION['user'] = null;
        $_SESSION['profile'] = null;
        $this->login();
    }

    public function index(){

        if(!isset($_SESSION['user'])){
            $redirect = "user/profile";
            require_once(VIEWS_PATH . "login.php");
            return;
        }

        try{
            $profile = $this->userProfileDaos->getById($_SESSION['user']->getId());            

        }catch(\Exception $err){
            $err = DATABASE_ERR;
        }


        //get tickets
        $ticketController = new TicketController();
        $tickets = $ticketController->ticketByUser($_SESSION['user']->getId());

        require_once(VIEWS_PATH . "profile.php");       
    }


    public function profile($firstName = null, $lastName = null, $dni = null){

        if(!isset($_SESSION['user'])){
            $redirect = "user/profile";
            require_once(VIEWS_PATH . "login.php");
            return;
        }

        if(isset($firstName, $lastName, $dni)){

            $profile = new Profile($firstName, $lastName, $dni);
            $profile->setIdUser($_SESSION['user']->getId());
            try{
                $this->userProfileDaos->modify($profile);
            }catch(\Exception $err){
                $err = DATABASE_ERR;
            }

            $message = 'Cambios realizados con exito!';             
            
        }


        $this->index();      
    }
}

?>
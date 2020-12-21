<?php
namespace controllers;

use models\user as User;
use models\userProfile as Profile;
use daos\userDaos as UserDaos;
use daos\userProfileDaos as UserProfileDaos;

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

    public function __construct(){
        $this->daos = new UserDaos();
        $this->userProfileDaos = new UserProfileDaos();
        $this->movieController = new MovieController();
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
                    //$this->login();
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
                        $this->movieController->index();
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
                echo 'Graph returned an error: ' . $e->getMessage();
                session_destroy();
                exit;
            } catch(FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            //register in db
            try{
                if(!$this->daos->exists($fbUserProfile['email'])){
                    $user = new User($fbUserProfile['email'],null,2);
                    $this->daos->add($user); 
                    $_user = $this->daos->getByEmail($user->getEmail());
                    $profile = new Profile($fbUserProfile['first_name'], $fbUserProfile['last_name'], null);
                    $profile->setIdUser($_user->getId());
                    $this->userProfileDaos->add($profile);
                    
                    $_SESSION['user'] = $_user;
                    $_SESSION['profile'] = $profile;
                    require_once(VIEWS_PATH . "login.php");
                }else{
                    $user = $this->daos->getByEmail($fbUserProfile['email']);
                    $userProfile = $this->userProfileDaos->getById($user->getId());
                    $_SESSION['user'] = $user;
                    $_SESSION['profile'] = $userProfile;
                    //TODO move to profile
                    $this->profile();
                }
            } catch(\Exception $err){
                throw $err;
                $err = DATABASE_ERR;
                require_once(VIEWS_PATH . "signup.php");
            }
        }
    }

    public function registerFacebook(){
        
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
             echo 'Graph returned an error: ' . $e->getMessage();
              exit;
        } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
              exit;
        } catch(\Exception $e){
            echo "AAAAAAAAH";
            throw $e;           
        }

        //ask for user permission
        $loginURL = $helper->getLoginUrl(FB_REDIRECTION, $fbPermissions);
        header('Location: '.$loginURL);        
        
    }

    public function loginFacebook(){

        $this->registerFacebook();
    }

    public function logout(){
        $_SESSION['user'] = null;
        $_SESSION['profile'] = null;
        $this->login();
    }

    public function index(){
        try{
            $profile = $this->userProfileDaos->getById($_SESSION['user']->getId());
        }catch(\Exception $err){
            $err = DATABASE_ERR;
        }

        require_once(VIEWS_PATH . "modifyProfile.php");
    }


    public function profile($firstName = null, $lastName = null, $dni = null){
        if(isset($firstName, $lastName, $dni)){

            $profile = new Profile($firstName, $lastName, $dni);
            $profile->setIdUser($_SESSION['user']->getId());
            try{
                $this->userProfileDaos->modify($profile);
            }catch(\Exception $err){
                $err = DATABASE_ERR;
            }

            $message = 'Cambios realizados con exito!'; 
            
            require_once(VIEWS_PATH . "modifyProfile.php");
        }else{
            $this->index();       
        }
    }
}

?>
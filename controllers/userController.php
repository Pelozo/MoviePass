<?php
namespace controllers;

use models\user as User;
use models\userProfile as Profile;
use daos\userDaos as UserDaos;
use daos\userProfileDaos as UserProfileDaos;

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
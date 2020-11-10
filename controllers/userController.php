<?php
namespace controllers;

use models\user as User;
use models\userProfile as Profile;
use daos\userDaos as UserDaos;
use daos\userProfileDaos as UserProfileDaos;

class UserController{
    private $daos;
    private $userProfileDaos;

    public function __construct(){
        $this->daos = new UserDaos();
        $this->userProfileDaos = new UserProfileDaos();
    }

    public function signup($email = null, $password = null, $firstName = null, $lastName = null, $dni = null){
        
        if(isset($email, $password, $firstName, $lastName, $dni)){        
            
            if($this->daos->exists($email)){
                echo 'Ya hay un usuario registrado con ese email';
                require_once(VIEWS_PATH . "header.php");
                require_once(VIEWS_PATH . "signup.php");
                require_once(VIEWS_PATH . "footer.php");
            }else{
                $user = new User($email,$password,2);
                $this->daos->add($user); 
                $_user = $this->daos->getByEmail($email);
                $profile = new Profile($firstName, $lastName, $dni);
                $profile->setIdUser($_user->getId());
                $this->userProfileDaos->add($profile);
                require_once(VIEWS_PATH . "header.php");
                require_once(VIEWS_PATH . "login.php");
                require_once(VIEWS_PATH . "footer.php");          
            } 
        } else {
            require_once(VIEWS_PATH . "header.php");
            require_once(VIEWS_PATH . "signup.php");
            require_once(VIEWS_PATH . "footer.php");
        }
    }

    public function login($email, $password){

        if(isset($email, $password)){
            $user = $this->daos->getByEmail($email);
            if($user != null){
                if ($user->getPassword() == $password){
                    $_SESSION['user'] = $user;
                    $profile = $this->userProfileDaos->getById($user->getId());
                    $_SESSION['profile'] = $profile;
                    require_once(VIEWS_PATH . "header.php");
                    header('location:' . FRONT_ROOT); //acá tendría que ir otra vista, o llamar o movieController->show() o algo así, no sé.
                    require_once(VIEWS_PATH . "footer.php");
                    return;

                }else {
                    echo 'contraseña incorrecta';
                }
            } else {
                echo 'usuario no encontrado';
            }
        }
        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "login.php");
        require_once(VIEWS_PATH . "footer.php");

       
    }

    public function logout(){
        $_SESSION['user'] = null;
        $_SESSION['profile'] = null;
        $this->login();
    }

    public function index(){
        $profile = $this->userProfileDaos->getById($_SESSION['user']->getId());
        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "modifyProfile.php");
        require_once(VIEWS_PATH . "footer.php");  
    }


    public function profile($firstName = null, $lastName = null, $dni = null){
        if(isset($firstName, $lastName, $dni)){

            $profile = new Profile($firstName, $lastName, $dni);
            $profile->setIdUser($_SESSION['user']->getId());

            $this->userProfileDaos->modify($profile);

            $message = 'Cambios realizados con exito!'; 
            
            require_once(VIEWS_PATH . "header.php");
            require_once(VIEWS_PATH . "modifyProfile.php");
            require_once(VIEWS_PATH . "footer.php");  

        }else{
            $this->index();       
        }
    }
}

?>
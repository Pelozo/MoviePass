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

    public function signup(){
        
        if($_POST){            
            $email = $_POST['email'];            
            $password = $_POST['password'];
            $firstName = $_POST['firstName'];            
            $lastName = $_POST['lastName'];  
            $dni = $_POST['dni'];            
            
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
                echo '<pre>';
                var_dump($profile);
                echo '</pre>';
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

    public function login(){
        if($_POST){
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = $this->daos->getByEmail($email);
            if($user != null){
                if ($user->getPassword() == $password){
                    $_SESSION['user'] = $user;
                    $profile = $this->userProfileDaos->getById($user->getId());
                    $_SESSION['profile'] = $profile;
                    require_once(VIEWS_PATH . "header.php");
                    require_once(VIEWS_PATH . "login.php"); //acá tendría que ir otra vista, o llamar o movieController->show() o algo así, no sé.
                    require_once(VIEWS_PATH . "footer.php");
                    return;

                }else {
                    //TODO
                    echo 'contraseña incorrecta';
                }
            } else {
                //TODO
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


    public function profile(){
        if(isset($_POST['firstName'], $_POST['lastName'],$_POST['dni'])){

            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $dni = $_POST['dni'];

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
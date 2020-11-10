<?php
namespace controllers;
use daos\cinemaDaos as CinemaDaos;
use models\cinema as Cinema;

class CinemaController{
    private $cinemaDaos;
    const ERR = 'Hubo un error al querer acceder a la base de datos';

    public function __construct(){
        $this->cinemaDaos = new CinemaDaos();
    }

    public function index(){

        //check if user is logged and has admin privileges
        if($_SESSION['user'] == null || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        try{
            $cinemas = $this->cinemaDaos->getAll();
        } catch(\Exception $ex){
            $ex = self::ERR;
        }

        $modalView = 'login.php';
        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "cinemaTable.php");
        require_once(VIEWS_PATH . "footer.php");

    }

    public function getAll(){
        try{
            $cinemas = $this->cinemaDaos->getAll(); 
        } catch(\Exception $err){
            $err = self::ERR;
        }
    }


    public function add(){
        if($_SESSION['user'] == null || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");
            //or redirect to login, idk
            //$c = new UserController();
            //$c->login();            
            return;
        }


        
        if(isset($_POST['name'], $_POST['address'],$_POST['city'], $_POST['province'],$_POST['postal'])){ //se pasa como parámetro

            $name = $_POST['name'];
            $address = $_POST['address'];
            $city = $_POST['city'];
            $province = $_POST['province'];
            $postal = $_POST['postal'];

            $cinema = new Cinema($name, $address, $city, $postal, $province);

            //check for empty fields
            $required = array('name' => 'nombre',  'address' => 'dirección', 'city' => 'ciudad', 'province' => 'provincia', 'postal' => 'código postal');
            foreach($required as $field => $name) {
                if (empty($_POST[$field])) {
                  $error = ucfirst($required[$field]) . " no puede estar vacio";
                  require_once(VIEWS_PATH . "header.php");
                  require_once(VIEWS_PATH . "addCinema.php");
                  require_once(VIEWS_PATH . "footer.php");
                  return;
                }
            }
            try{
                //add cinema to db
                $this->cinemaDaos->add($cinema);
                //back to index
                $this->index();
            }
            catch(\Exception $ex){
                $ex = self::ERR;
            }
            } else {
                require_once(VIEWS_PATH . "header.php");
                require_once(VIEWS_PATH . "addCinema.php");
                require_once(VIEWS_PATH . "footer.php");        
            }
    }


    public function modify($id){
 
        //check if user is logged and has admin privileges
        if($_SESSION['user'] == null || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        //check if form was sent
        if(isset($_POST['id'], $_POST['name'], $_POST['address'], $_POST['city'], $_POST['province'],$_POST['postal'])){

            $id = $_POST['id'];
            $name = $_POST['name'];
            $address = $_POST['address'];
            $city = $_POST['city'];
            $province = $_POST['province'];
            $postal = $_POST['postal'];

            $cinema = new Cinema($name, $address, $city, $postal, $province);
            //replace new id with old id
            $cinema->setId($_POST['id']);

            //check for empty fields
            $required = array('name' => 'nombre', 'address' => 'dirección', 'city' => 'ciudad', 'province' => 'provincia', 'postal' => 'código postal');
            foreach($required as $field => $name) {
                if (empty($_POST[$field])) {
                  $error = ucfirst($required[$field]) . " no puede estar vacio";
                  require_once(VIEWS_PATH . "header.php");
                  require_once(VIEWS_PATH . "addCinema.php");
                  require_once(VIEWS_PATH . "footer.php");
                  return;
                }
            }
            try{
                //modify cinema in db
                $this->cinemaDaos->modify($cinema);
                //back to index
                $this->index();
            } catch(\Exception $err){
                $err = self::ERR;
            }

        }else{
        try{
            //get cinema from id
            $cinema = $this->cinemaDaos->getById($id);
            //cinema not found
            if(empty($cinema)){
                $this->index();
                return;
            }
        } catch(\Exception $err){
            $err = self::ERR;
        }
        
        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "addCinema.php");
        require_once(VIEWS_PATH . "footer.php");
        }
    }

    public function remove($id){
        //check if user is logged and has admin privileges
        if($_SESSION['user'] == null || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        try{
            $this->cinemaDaos->remove($id);
            $this->index();
        } catch(\Exception $err){
            $err = self::ERR;
        }
    }

}


?>
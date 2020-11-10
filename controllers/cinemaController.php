<?php
namespace controllers;
use daos\cinemaDaos as CinemaDaos;
use models\cinema as Cinema;

class CinemaController{
    private $cinemaDaos;

    public function __construct(){
        $this->cinemaDaos = new CinemaDaos();
    }

    public function index(){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        $cinemas = $this->cinemaDaos->getAll();
        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "cinemaTable.php");
        require_once(VIEWS_PATH . "footer.php");

    }

    public function getAll(){
        $cinemas = $this->cinemaDaos->getAll(); 
    }


    public function add($name = null, $address= null, $city = null, $province= null, $postal=null){

        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        
        if(isset($name, $address, $city, $province, $postal)){

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

            //add cinema to db
            $this->cinemaDaos->add($cinema);
            //back to index
            $this->index();
        }else{
            require_once(VIEWS_PATH . "header.php");
            require_once(VIEWS_PATH . "addCinema.php");
            require_once(VIEWS_PATH . "footer.php");        
        }
        
    }


    public function modify($id, $name = null, $address= null, $city = null, $province= null, $postal=null){
 
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        //check if form was sent
        if(isset($id, $name, $address, $city, $province, $postal)){

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

            //modify cinema in db
            $this->cinemaDaos->modify($cinema);
            //back to index
            $this->index();
        }else{
            
        //get cinema from id
        $cinema = $this->cinemaDaos->getById($id);

        //cinema not found
        if(empty($cinema)){
            $this->index();
            return;
        }
        
        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "addCinema.php");
        require_once(VIEWS_PATH . "footer.php");
        }
    }

    public function remove($id){
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        
        $this->cinemaDaos->remove($id);
        $this->index();
    }

}


?>
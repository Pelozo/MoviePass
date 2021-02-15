<?php
namespace controllers;
use daos\CinemaDaos as CinemaDaos;
use models\Cinema as Cinema;
use models\Address as Address;
use models\Province as Province;

class CinemaController{
    private $cinemaDaos;
    private $provinceController;

    public function __construct(){
        $this->cinemaDaos = new CinemaDaos();
        $this->provinceController = new ProvinceController();
    }

    public function index($err = null){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        try{
            $cinemas = $this->cinemaDaos->getAll();
        } catch(\Exception $err){
            throw $err;
            $err = DATABASE_ERR;
        }
        require_once(VIEWS_PATH . "cinemaTable.php");
    }


    public function add($name = null, $address= null, $city = null, $postal=null, $province= null){
         if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");
            return;
        }
        
        $provinces = $this->provinceController->getAll();
        if(isset($name, $address, $city, $province, $postal)){

            $cinema = new Cinema($name, new Address($address, $city, $postal, new Province($province)));

            //check for empty fields
            $required = array('name' => 'nombre',  'address' => 'dirección', 'city' => 'ciudad', 'province' => 'provincia', 'postal' => 'código postal');
            foreach($required as $field => $value) {
                if (empty($$field)) { 
                    $error = ucfirst($required[$field]) . " no puede estar vacio";

                    require_once(VIEWS_PATH . "addCinema.php");
                    return;
                }
            }

            try{
                //add cinema to db
                $this->cinemaDaos->add($cinema);
                //back to index
                $this->index();
            }
            catch(\Exception $err){
                if($err->getCode()){
                    $error = "Ya hay un cine en esa dirección";
                }else{
                    $error = DATABASE_ERR;
                }
                require_once(VIEWS_PATH . "addCinema.php");
            }
        } else {
            require_once(VIEWS_PATH . "addCinema.php");
        }

    }


    public function modify($id, $name = null, $address= null, $city = null, $postal=null, $province= null){
 
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        $provinces = $this->provinceController->getAll();

        //check if form was sent
        if(isset($id, $name, $address, $city, $province, $postal)){

            $cinema = new Cinema($name, new Address($address, $city, $postal, new Province($province)));
            
            //replace new id with old id
            $cinema->setId($id);

            //check for empty fields
            $required = array('name' => 'nombre', 'address' => 'dirección', 'city' => 'ciudad', 'province' => 'provincia', 'postal' => 'código postal');
            foreach($required as $field => $value) {
                if (empty($$field)) {                  
                  $error = ucfirst($required[$field]) . " no puede estar vacio";

                  require_once(VIEWS_PATH . "addCinema.php");
                  return;
                }
            }
            try{
                //modify cinema in db
                $this->cinemaDaos->modify($cinema);
                //back to index
                $this->index();
            } catch(\Exception $err){
                $error = DATABASE_ERR;
                require_once(VIEWS_PATH . "addCinema.php");
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
                $error = DATABASE_ERR;
            }

        require_once(VIEWS_PATH . "addCinema.php");
        }
    }

    public function remove($id){
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        try{
            $this->cinemaDaos->remove($id);
        } catch(\Exception $err){
            if($err->getCode() == 23000){
                $err = "Para eliminar un cine se deben eliminar sus salas antes.";
            }else{
                $err = DATABASE_ERR;
            }
            $this->index($err);            
        }
        $this->index();

    }

    //this is used on ajax calls
    public function getAllWithRooms(){
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        try{
            echo json_encode($this->cinemaDaos->getAllWithRooms());
        }catch(\Exception $err){
            echo '[]';
        }



    }

}


?>
<?php
namespace controllers;
use daos\roomDaos as RoomDaos;
use daos\cinemaDaos as CinemaDaos;
use models\room as Room;

class RoomController{
    private $roomDaos;

    public function __construct(){
        $this->roomDaos = new RoomDaos();
        $this->cinemaDaos = new CinemaDaos();
    }

    public function show($id){
        //check if user is logged and has admin privileges
        if($_SESSION['user'] == null || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        $cinema = $this->cinemaDaos->getById($id);
        $rooms = $this->roomDaos->getByCinema($id);

        require_once(VIEWS_PATH . "roomTable.php");
    }

    public function getByCinema($idCinema){
        echo json_encode($this->roomDaos->getByCinema($idCinema));
    }

    public function add(){
        if($_SESSION['user'] == null || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");
            //or redirect to login, idk
            //$c = new UserController();
            //$c->login();            
            return;
        }
        $idCinema = $_POST['idCinema'];
        
        if(isset($_POST['name'], $_POST['capacity'], $_POST['ticket'])){

            $name = $_POST['name'];
            $capacity = $_POST['capacity'];
            $ticketPrice = $_POST['ticket'];
            $idCinema = $_POST['idCinema'];

            $room = new Room($name, $capacity, $ticketPrice, $idCinema);

            //check for empty fields
            $required = array('name' => 'nombre', 'capacity' => 'capacidad', 'ticket' => 'precio de entrada');
            foreach($required as $field => $name) {
                if (empty($_POST[$field])) {
                  $error = ucfirst($required[$field]) . " no puede estar vacio";
                  require_once(VIEWS_PATH . "addRoom.php");
                  return;
                }
            }
            //add room to db
            $this->roomDaos->add($room);
            //back to index
            $this->show($idCinema);
        }else{
            require_once(VIEWS_PATH . "addRoom.php");    
        }       
    
    }

    public function modify($id){
 
        //check if user is logged and has admin privileges
        if($_SESSION['user'] == null || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        $idCinema = $_POST['idCinema'];
        //check if form was sent
        if(isset($_POST['id'], $_POST['name'], $_POST['capacity'], $_POST['ticket'], $_POST['idCinema'])){

            $id = $_POST['id'];
            $name = $_POST['name'];
            $capacity = $_POST['capacity'];
            $idCinema = $_POST['idCinema'];
            $ticketPrice = $_POST['ticket'];

            $room = new Room($name, $capacity, $ticketPrice);
            //replace null id with id
            $room->setId($id);
            //add cinema id
            $room->setIdCinema($idCinema);

            //check for empty fields
            $required = array('name' => 'nombre', 'capacity' => 'capacidad', 'ticket' => 'precio de entrada');
            foreach($required as $field => $name) {
                if (empty($_POST[$field])) {
                  $error = ucfirst($required[$field]) . " no puede estar vacio";
                  require_once(VIEWS_PATH . "addRoom.php");
                  return;
                }
            }
            //modify cinema in db
            $this->roomDaos->modify($room);
            //back to index
            $this->show($idCinema);
        }else{
            
        //get cinema from id
        $room = $this->roomDaos->getById($id);

        //cinema not found
        if(empty($room)){
            //$this->show();
            //return;
        }
        
        require_once(VIEWS_PATH . "addRoom.php");
        }
    }

    public function remove($id){
        $this->roomDaos->remove($id);
        $this->show($_POST['idCinema']);
    }


}


?>
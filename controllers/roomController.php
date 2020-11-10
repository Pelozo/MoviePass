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

        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "roomTable.php");
        require_once(VIEWS_PATH . "footer.php");

    }

    //this function returns json becuase it'll be called using ajax in the body of views/addShow.php
    public function getByCinema($idCinema){
        echo json_encode($this->roomDaos->getByCinema($idCinema));
    }

    public function add($idCinema = null, $id = null, $name = null, $capacity = null, $ticket = null){
        
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");;            
            return;
        }
        
        if(isset($name, $capacity, $ticket)){

            $room = new Room($name, $capacity, $ticket, $idCinema);

            //check for empty fields
            $required = array('name' => 'nombre', 'capacity' => 'capacidad', 'ticket' => 'precio de entrada');
            foreach($required as $field => $name) {
                if (empty($_POST[$field])) {
                  $error = ucfirst($required[$field]) . " no puede estar vacio";
                  require_once(VIEWS_PATH . "header.php");
                  require_once(VIEWS_PATH . "addRoom.php");
                  require_once(VIEWS_PATH . "footer.php");
                  return;
                }
            }
            //add room to db
            $this->roomDaos->add($room);
            //back to index
            $this->show($idCinema);
        }else{
            require_once(VIEWS_PATH . "header.php");
            require_once(VIEWS_PATH . "addRoom.php");
            require_once(VIEWS_PATH . "footer.php");        
        }       
    
    }

    public function modify($idCinema, $id, $name = null, $capacity = null, $ticket = null){
 
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        
        //check if form was sent
        if(isset($idCinema, $name, $capacity, $ticket, $id)){

            $room = new Room($name, $capacity, $ticket);
            //replace null id with id
            $room->setId($id);
            //add cinema id
            $room->setIdCinema($idCinema);

            //check for empty fields
            $required = array('name' => 'nombre', 'capacity' => 'capacidad', 'ticket' => 'precio de entrada');
            foreach($required as $field => $name) {
                if (empty($_POST[$field])) {
                  $error = ucfirst($required[$field]) . " no puede estar vacio";
                  require_once(VIEWS_PATH . "header.php");
                  require_once(VIEWS_PATH . "addRoom.php");
                  require_once(VIEWS_PATH . "footer.php");
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
            
            require_once(VIEWS_PATH . "header.php");
            require_once(VIEWS_PATH . "addRoom.php");
            require_once(VIEWS_PATH . "footer.php");
        }
    }

    public function remove($id){
        $this->roomDaos->remove($id);
        $this->show($_POST['idCinema']);
    }


}


?>
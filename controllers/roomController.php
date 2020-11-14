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
        try{
            $cinema = $this->cinemaDaos->getById($id);
            $rooms = $this->roomDaos->getByCinema($id);
        }catch(\Exception $err){
            throw $err;
            $err = DATABASE_ERR;
            
        }

        require_once(VIEWS_PATH . "roomTable.php");
    }

    //this function returns json becuase it'll be called using ajax in the body of views/addShow.php
    public function getByCinema($idCinema){
        try{
            echo json_encode($this->roomDaos->getByCinema($idCinema));
        }catch(\Exception $err){
            echo '[]';
        }
    }


    public function add($idCinema = null, $id = null, $name = null, $capacity = null, $ticket = null){
        
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");
            return;
        }
        
        if(isset($name, $capacity, $ticket)){

            $room = new Room($name, $ticket, $capacity, $idCinema);

            //check for empty fields
            $required = array('name' => 'nombre', 'capacity' => 'capacidad', 'ticket' => 'precio de entrada');
            foreach($required as $field => $name) {
                if (empty($_POST[$field])) {

                  $err = ucfirst($required[$field]) . " no puede estar vacio";
                  require_once(VIEWS_PATH . "addRoom.php");
                  return;
                }
            }
            try{
                //add room to db
                $this->roomDaos->add($room);
                //back to index
                $this->show($idCinema);
            }catch(\Exception $err){
                $err = DATABASE_ERR;
                require_once(VIEWS_PATH . "addRoom.php");
            }
        }else{
            require_once(VIEWS_PATH . "addRoom.php");
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

            $room = new Room($name, $ticket, $capacity);
            //replace null id with id
            $room->setId($id);
            //add cinema id
            $room->setCinema($idCinema);

            //check for empty fields
            $required = array('name' => 'nombre', 'capacity' => 'capacidad', 'ticket' => 'precio de entrada');
            foreach($required as $field => $name) {
                if (empty($_POST[$field])) {
                  $err = ucfirst($required[$field]) . " no puede estar vacio";
                  require_once(VIEWS_PATH . "addRoom.php");
                  return;
                }
            }
            try{
                //modify cinema in db
                $this->roomDaos->modify($room);
                //back to index
                $this->show($idCinema);
            }catch(\Exception $err){
                throw $err;
                $err = DATABASE_ERR;
                require_once(VIEWS_PATH . "addRoom.php");
            }
        } else {
        
            try{
                //get room from id
                $room = $this->roomDaos->getById($id);
                //room not found
                if(empty($room)){
                    $err = 'No se encontro la sala';
                }
            }catch(\Exception $err){
                $err = DATABASE_ERR;
                require_once(VIEWS_PATH . "addRoom.php");

            }

            require_once(VIEWS_PATH . "addRoom.php");

        }
    }

    public function remove($id){
        try{
            $this->roomDaos->remove($id);
            $this->show($_POST['idCinema']);
        }catch(\Exception $err){
            $err = DATABASE_ERR;
        }
    }


}


?>
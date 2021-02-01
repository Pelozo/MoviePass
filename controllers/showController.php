<?php
namespace controllers;
use daos\ShowDaos as ShowDaos;
use daos\CinemaDaos as CinemaDaos;
use daos\GenreDaos as GenreDaos;
use daos\MovieDaos as MovieDaos;
use daos\RoomDaos as RoomDaos;
use daos\PurchaseDaos as PurchaseDaos;
use models\Show as Show;


class ShowController{
    private $showDaos;
    private $cinemaDaos;
    private $genreDaos;
    private $movieDaos;
    private $roomDaos;
    private $purchaseDaos;

    public function __construct(){
        $this->showDaos = new ShowDaos();       
        $this->cinemaDaos = new CinemaDaos();  
        $this->genreDaos = GenreDaos::getInstance();  
        $this->movieDaos = new MovieDaos();  
        $this->roomDaos = new RoomDaos();  
        $this->purchaseDaos = new PurchaseDaos();  
    }

    public function index($err = null){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        try{
            $shows = $this->showDaos->getAll();
            
        }catch(\Exception $err){
            $err = DATABASE_ERR;
        }
        require_once(VIEWS_PATH . "showTable.php");
    }

    public function add($idMovie = null, $idCinema = null, $idRoom = null, $date = null){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        try{
            //this is used later in the view to display dropdowns
            $genres = $this->genreDaos->getAll(); 
            $years = array_column($this->movieDaos->getMoviesYear(),'year');
            $cinemas = $this->cinemaDaos->getAllWithRooms();
        }catch(\Exception $err){
            $err = DATABASE_ERR;
            require_once(VIEWS_PATH . "addShow.php");
            return;
        }


        if (isset($idMovie, $date, $idRoom, $idCinema)){      
            $err = null;

            if ($idMovie == null){
                $err = 'Por favor, seleccione una película';

                require_once(VIEWS_PATH . "addShow.php");
                
            } else {
                try{
                    //create new show
                    $movie = $this->movieDaos->getById($idMovie);
                    $show = new Show($movie, $this->roomDaos->getById($idRoom), $date);
                    $result = $this->showDaos->verifyShowDay($show);
                    
                    //this function returns an !empty array if the show's movie is playing the same day
                    if(!empty($result)){
                        foreach($result as $res){
                            if($res['id_cinema'] != $idCinema){
                                $err = 'No se puede agregar la misma película un mismo día a distintos cines.';
                            }else{
                                if($res['idRoom_show'] != $idRoom){
                                    $err = 'No se puede agregar la misma película un mismo día en diferentes salas del mismo cine.';
                                }
                            }
                        }
                    } 

                    $shows3Days = $this->showDaos->verifyShowDatetimeOverlap($show);
                    $valid = $this->verify15Minutes($shows3Days, $show);
                    
                    if(!$valid){
                        $err = 'Ya hay una funcion a esa hora.';
                    }
                    if($err == null){
                        $this->showDaos->add($show);
                        $this->index();
                    } else {
                        $show = null;
                        require_once(VIEWS_PATH . "addShow.php");
                    }
                }catch(\Exception $err){
                    $err = DATABASE_ERR;

                    require_once(VIEWS_PATH . "addShow.php");
                }
            }
        } else {
            require_once(VIEWS_PATH . "addShow.php");
        }
    }

    public function modify($id, $idMovie = null, $idCinema = null, $idRoom = null, $date = null){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        try{
            $genres = $this->genreDaos->getAll(); 
            $years = array_column($this->movieDaos->getMoviesYear(),'year');
            $cinemas = $this->cinemaDaos->getAllWithRooms(); 
        } catch(\Exception $err){

            $err = DATABASE_ERR;
            require_once(VIEWS_PATH . "addShow.php");
        }
        
        
        //check if form was sent

        if(isset($idMovie, $idCinema, $idRoom, $date)){

            try{

                $room = $this->roomDaos->getById($idRoom);
                $movie = $this->movieDaos->getById($idMovie);

                $show = new Show($movie, $room, $date);
                $show->setId($id);
                $cinemaShow = $this->cinemaDaos->getById($idCinema);

                $err = null;


                $result = $this->showDaos->verifyShowDay($show);
                
                //this function returns an !empty array if the show's movie is playing the same day
                if(!empty($result)){

                    foreach($result as $res){
                        if($res['id_cinema'] != $idCinema){
                            $err = 'No se puede agregar la misma película un mismo día a distintos cines.';
                        }else{
                            if($res['idRoom_show'] != $idRoom){
                                $err = 'No se puede agregar la misma película un mismo día en diferentes salas del mismo cine.';
                            }
                        }
                    }
                }                
    
                $shows3Days = $this->showDaos->verifyShowDatetimeOverlap($show);
                
                $valid = $this->verify15Minutes($shows3Days, $show);
                
                if(!$valid){
                    $err = 'Ya hay una funcion a esa hora.';
                }

                if($err == null){
                    $this->showDaos->modify($show);
                    $this->index();
                } else {
                    require_once(VIEWS_PATH . "addShow.php");
                }
                
            }catch(\Exception $err){
                $err = DATABASE_ERR;

                require_once(VIEWS_PATH . "addShow.php");
            }
        } else {
            try{

                //get show, cinema and room from id
                $show = $this->showDaos->getById($id);
                $room = $show->getRoom();
                $cinemaShow = $this->cinemaDaos->getById($room->getCinema()->getId());
                $movie = $show->getMovie();
                
                $date = $show->getDatetime();
                
                //php to html date format
                $date = str_replace(' ', 'T', $date);
                
                //show not found
                if(empty($show)){
                    $this->index();
                    return;
                }
            }catch(\Exception $err){
                $err = DATABASE_ERR;
            }
            require_once(VIEWS_PATH . "addShow.php");
        }

    }

    public function remove($id){
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        try{
            $this->showDaos->remove($id);
        }catch(\Exception $err){
            if($err->getCode() == 23000){
                $err = "No se puede eliminar una funcion que tenga tickets vendidos";
            }else{
                $err = DATABASE_ERR;
            }
            $this->index($err);
        }
        $this->index();
    }

    //https://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap 
    private function verify15Minutes($shows3Days, $_show){
        $durationSeconds = ($_show->getMovie()->getDuration() + 15) * 60;

        $showTime = strtotime($_show->getDatetime());

        $endShow = strtotime($_show->getDatetime()) + $durationSeconds;

        foreach($shows3Days as $show){

            $dbShowStart = strtotime($show->getDateTime());
            $dbShowMovieDuration = ($show->getMovie()->getDuration() + 15) * 60;
            $dbShowEnd = $dbShowStart + $dbShowMovieDuration;

            if(($showTime <= $dbShowEnd) && ($dbShowStart <= $endShow) && $_show->getId() != $show->getId()){
                return false;
            }
        }
        return true;
    }

 

    public function showDetails($id){

        //verify movie exists and has shows
        try{
            $shows = $this->showDaos->getByIdMovieFuture($id);
            if(sizeof($shows) < 1) throw new \Exception;
        }catch(\Exception $ex){
            $homeController = new HomeController();
            $homeController->index();
            return;
        }

        try{
            $movie = $shows[0]->getMovie();

            $availableShows = array();
            $notAvailableShows = array();
            
            //verify if theres available tickets in the room of the show
            foreach($shows as $show){
                $ticketsSold = $this->purchaseDaos->getSoldTicketsByShow($show->getId());
                $roomCapacity = $show->getRoom()->getCapacity();
                if(($roomCapacity - $ticketsSold) > 0){
                    array_push($availableShows, $show);
                }else{
                    array_push($notAvailableShows, $show);
                }
            }

        }catch(\Exception $err){
            $err = DATABASE_ERR;
            
        }
        require_once(VIEWS_PATH . "selectedShow.php");
    } 

}


?>


<?php
namespace controllers;
use daos\showDaos as ShowDaos;
use daos\cinemaDaos as CinemaDaos;
use daos\GenreDaos as GenreDaos;
use daos\MovieDaos as MovieDaos;
use daos\RoomDaos as RoomDaos;
use models\show as Show;
use controllers\movieController as MovieController;


class ShowController{
    private $showDaos;
    private $cinemaDaos;
    private $genreDaos;
    private $movieDaos;

    public function __construct(){
        $this->showDaos = new ShowDaos();       
        $this->cinemaDaos = new CinemaDaos();  
        $this->genreDaos = GenreDaos::getInstance();  
        $this->movieDaos = new MovieDaos();  
        $this->roomDaos = new RoomDaos();  
    }

    public function index(){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        $shows = $this->showDaos->getAll();

        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "showTable.php");
        require_once(VIEWS_PATH . "footer.php");
    }

    public function add(){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        //this is used later in the view to display dropdowns
        $genres = $this->genreDaos->getAll(); 
        $years = array_column($this->movieDaos->getMoviesYear(),'year');

        $cinemas = $this->cinemaDaos->getAllWithRooms();

        if ($_POST){
            
            //get params
            $idMovie = $_POST['movieId'];
            $date = $_POST['time'];
            $idRoom = $_POST['roomId'];
            $idCinema = $_POST['cinemaId'];

            //create new show
            $show = new Show($this->movieDaos->getById($idMovie), $this->roomDaos->getById($idRoom), $date);

            echo $show->getDatetime();

            //do the verification (SQL, PHP?)
            $result = $this->showDaos->verifyShowDay($show, $idCinema);
            $error = null;
            if(!empty($result)){
                foreach($result as $res){
                    if($res['id_cinema'] != $idCinema){
                        $error = 'No se puede agregar la misma película un mismo día a distintos cines';
                    }
                }
            }

            echo "<pre>";
            $shows3Days = $this->showDaos->verifyShowDatetimeOverlap($show);
            echo "</pre>";
            
            echo $valid = $this->verify15Minutes($shows3Days, $show);
            
            if(!$valid){
                $error = 'Ya hay una funcion a esa hora.';
            }
            if($error == null){
                $this->showDaos->add($show);
                $this->index();
            } else {
                require_once(VIEWS_PATH . "header.php");
                require_once(VIEWS_PATH . "addShow.php");
                require_once(VIEWS_PATH . "footer.php");
            }
            
        } else {
            require_once(VIEWS_PATH . "header.php");
            require_once(VIEWS_PATH . "addShow.php");
            require_once(VIEWS_PATH . "footer.php");
        }
    }

    public function remove($id){
        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        
        $this->showDaos->remove($id);
        $this->index();
    }

    //fuck my life https://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap 
    private function verify15Minutes($shows3Days, $_show){

        //echo "Duration movie to add: {$_show->getMovie()->getDuration()} <br>";
        $durationSeconds = ($_show->getMovie()->getDuration() + 15) * 60;
        //echo "Duration + 15 to seconds $durationSeconds <br>";

        $showTime = strtotime($_show->getDatetime());

        //echo "Date start show to add " . date('Y-m-d H:i:s', $showTime) . " ($showTime)<br>";

        $endShow = strtotime($_show->getDatetime()) + $durationSeconds;

        //echo "Date end show to add " . date('Y-m-d H:i:s', $endShow) . "($endShow)<br>";

        //echo "----loop---<br>";
        foreach($shows3Days as $show){

            $dbShowStart = strtotime($show->getDateTime());

            $dbShowMovieDuration = ($show->getMovie()->getDuration() + 15) * 60;
            $dbShowEnd = $dbShowStart + $dbShowMovieDuration;

            //echo "Date start show in db " . date('Y-m-d H:i:s', $dbShowStart) . " ($dbShowStart)<br>";    
            //echo "Date end show in db " . date('Y-m-d H:i:s', $dbShowEnd) . "($dbShowEnd)<br>";

            if(($showTime <= $dbShowEnd) && ($dbShowStart <= $endShow)){
                return false;
            }
        }
        return true;
    }
}


?>


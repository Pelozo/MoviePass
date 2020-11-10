<?php
namespace controllers;
use daos\MovieDaos as MovieDaos;
use daos\GenreDaos as GenreDaos;
use models\movie as Movie;

class MovieController{

    private $movieDaos;
    private $genreDaos;

    public function __construct(){
        $this->movieDaos = new MovieDaos();
        $this->genreDaos = GenreDaos::getInstance();
    }
    
    public function index(){
        $this->displayBillboard();
    }

    public function displayBillboard(){
        try{
            $genres = $this->genreDaos->getAll(); //this is used later in the view to display a dropdown
        }catch(\Exception $err){
            $err = DATABASE_ERR;
        }
        require_once(VIEWS_PATH . "movieShows.php");
    }

    public function update(){

        if($_SESSION['user'] == null || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");
            //or redirect to login, idk
            //$c = new UserController();
            //$c->login();            
            return;
        }

        try{
            $this->genreDaos->update();
            $this->movieDaos->update();
        }catch(\Exception $err){
            $err = DATABASE_ERR;
        }
        $this->index();
    }
    

    public function getMovies($genreRequired = "all", $yearRequired = "all", $name = "all", $page = 1){
        if($name == "all") $name = null;
        try{
            $movies = $this->movieDaos->getMoviesFiltered($genreRequired, $yearRequired, $name, $page);
            echo json_encode($movies);
        } catch(\Exception $err){
            echo '[]';
        }
    }


    public function details($id){
        try{
            $movie = $this->movieDaos->getById($id);
        }catch(\Exception $err){
            $err = DATABASE_ERR;
        }
        echo "<pre>";
        var_dump($movie);
        echo "</pre>";
    }

    
    
    public function getShows($genre = 'all', $date = 'all'){

        if($genre == 'all')$genre = null;
        if($date == 'all')$date = null;
        
        try{
            $movies = $this->movieDaos->getAllMoviesInBillboardTest($genre, $date);
            echo json_encode($movies);
        }catch(\Exception $err){
            echo '[]';
        }
        
    }

}
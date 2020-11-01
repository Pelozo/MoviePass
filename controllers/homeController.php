<?php
namespace controllers;

use models\cinema as Cinema;
use daos\genreDaos as GenreDaos;
use daos\cinemaDaos as CinemaDaos;

use daos\BaseDaos as BaseDaos;

use daos\Connection as Connection;

class HomeController{

    private $genreDaos;

    public function __construct(){

        $this->genreDaos = GenreDaos::getInstance();
        
    }

    public function index(){

        header("Location: movie/displayBillboard");

  

        //$movieController->displayBillboard();

        /*

        $genres = $this->genreDaos->getAll();        
        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "movieShows.php");
        require_once(VIEWS_PATH . "footer.php");
        */

    }
}


?>
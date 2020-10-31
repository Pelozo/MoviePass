<?php
namespace controllers;

use models\cinema as Cinema;
use models\genre as Genre;
use daos\cinemaDaos as CinemaDaos;

use daos\BaseDaos as BaseDaos;

use daos\Connection as Connection;

class HomeController{

    public function index(){

        //header("Location: movie/displayBillboard");

  

        //$movieController->displayBillboard();
        require_once(VIEWS_PATH . "header.php");
        require_once(VIEWS_PATH . "movieShows.php");
        require_once(VIEWS_PATH . "footer.php");

    }
}


?>
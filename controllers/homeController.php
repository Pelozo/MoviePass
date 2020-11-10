<?php
namespace controllers;

use controllers\MovieController as MovieController;

class HomeController{



    public function __construct(){
    }

    public function index(){
        $movieController  = new MovieController();
        $movieController->displayBillboard();
    }
}


?>
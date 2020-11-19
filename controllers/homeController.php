<?php
namespace controllers;

use controllers\MovieController as MovieController;

class HomeController{

    public function index(){
        $movieController  = new MovieController();
        $movieController->displayBillboard();
    }
}


?>
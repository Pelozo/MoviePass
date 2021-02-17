<?php
namespace controllers;

use controllers\movieController as MovieController;

class HomeController{

    public function index(){
        $movieController  = new MovieController();
        $movieController->displayBillboard();
    }

    public function invalid(){
        header("HTTP/1.1 404");
        //should be replaced with a good looking 404 if possible
    }
}


?>
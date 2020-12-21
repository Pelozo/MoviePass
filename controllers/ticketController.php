<?php
namespace controllers;

use daos\TicketDaos as TicketDaos;

class TicketController{
    private $ticketDaos;

    public function __construct(){
        $this->ticketDaos = new TicketDaos();
    }


    //and this is where I'd put my "only usable from same package" access modifier IF I HAD ONE.
    public function ticketByUser($idUser){

        //check if user is logged and has privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getId() != $idUser){
            header("HTTP/1.1 403");           
            return;
        }

        try{
            return $this->ticketDaos->getByUser($_SESSION['user']->getId());
        }catch(\Exception $ex){{}
            return array();
        }
        

    }



}
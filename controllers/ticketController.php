<?php
namespace controllers;

use daos\TicketDaos as TicketDaos;

class TicketController{
    private $ticketDaos;

    public function __construct(){
        $this->ticketDaos = new TicketDaos();
    }


    public function ticketByUser($idUser){

        //check if user is logged and has privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getId() != $idUser){
            header("HTTP/1.1 403");           
            return;
        }


        $tickets = $this->ticketDaos->getByUser($_SESSION['user']->getId());

        if(isset($tickets) && sizeof($tickets) > 0){
            require_once(VIEWS_PATH . "ticketTable.php");
        }
    }



}
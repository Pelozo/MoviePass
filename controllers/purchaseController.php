<?php
namespace controllers;

use models\Purchase as Purchase;
use models\Show as Show;
use models\UserProfile as Profile;
use models\Ticket as Ticket;

use daos\PurchaseDaos as PurchaseDaos;
use daos\ShowDaos as ShowDaos;
use daos\UserProfileDaos as UserProfileDaos;
use daos\TicketDaos as TicketDaos;

use controllers\userController as UserController;

use util\mailer as Mailer;


class PurchaseController{
    private $purchaseDaos;
    private $ticketDaos;
    private $showDaos;
    private $userProfileDaos;

    public function __construct(){
        $this->purchaseDaos = new PurchaseDaos();
        $this->ticketDaos = new TicketDaos();
        $this->showDaos = new ShowDaos();
        $this->userProfileDaos = new UserProfileDaos();
    }

    public function purchaseDetails($id){

        //if user is not logged redirect to login
        if(!isset($_SESSION['user'])){            
            setcookie("redirect", "purchase/purchaseDetails/$id", time() + 6000, "/");
            (new UserController())->login();
            return;
        }

        //if show doesn't exists rediret to home
        try{
            $show = $this->showDaos->getById($id);
            if($show == null) throw new \Exception;
        }catch(\Exception $err){
            $homeController = new HomeController();
            $homeController->index();
            return;
        }

        
        try{
            $ticketsSold = $this->purchaseDaos->getSoldTicketsByShow($id);
            $roomCapacity = $show->getRoom()->getCapacity();
            $availableCapacity = $roomCapacity - $ticketsSold;
            //$profile = $this->userProfileDaos->getById($_SESSION['user']->getId());            
   
        }catch(\Exception $err){
            $err = DATABASE_ERR;
        }
        
        //verify discount
        $discount = 0;
        if((date("N") == 2 || date("N") == 3)){
            $discount = 25;
            $discountMessage = "Los días martes y miercoles comprando dos o más entradas tenés un 25% de descuento.";
        }

        require_once(VIEWS_PATH . "purchaseForm.php");
    }

    public function makePurchase($idShow, $ticketsQuantity = null, $cardNumber = null, $cardCode = null, $cardExp = null, $cardName = null, $cardDni = null, $discount = null){


        //if user is not logged redirect to login
        if(!isset($_SESSION['user'])){            
            setcookie("redirect", "purchase/purchaseDetails/$idShow", time() + 6000, "/");
            (new UserController())->login();
            return;
        }

        
        $user = $_SESSION['user'];

        //form was sent
        if(isset($ticketsQuantity, $cardNumber, $cardCode, $cardExp, $cardName, $cardDni, $discount)){
            $message = null;

            //if show doesn't exists redirect to home
            try{
                $show = $this->showDaos->getById($idShow);
                if($show == null)throw new \Exception;
            }catch(\Exception $ex){
                $homeController = new HomeController();
                $homeController->index();
                return;
            }

            //verify correct number of tickets
            try{
                $ticketsSold = $this->purchaseDaos->getSoldTicketsByShow($idShow);
                $roomCapacity = $show->getRoom()->getCapacity();
                $availableCapacity = $roomCapacity - $ticketsSold;

                if($availableCapacity < 0) throw new \Exception();

            }catch(\Exception $ex){
                $message = "Ocurrio un error al realizar la compra.";
                require_once(VIEWS_PATH . "purchaseForm.php");
                return;
            }

            
            //verify discount
            $discount = 0;
            if($ticketsQuantity > 1 && (date("N") == 2 || date("N") == 3)){
                $discount = 25;
            }

            $date = date("Y-m-d H:i:s");

            $purchase = new Purchase($user, $show, $ticketsQuantity, $discount, $date);

            //TODO test this
            $purchase->setTotal((($show->getRoom()->getPrice()) * $ticketsQuantity) * (1 - ($discount/100)));

            try{
                $this->purchaseDaos->add($purchase);

                $idPurchase = $this->purchaseDaos->getLastInsertId();

                $purchase->setId($idPurchase);
                
                //generate tickets
                $tickets = array();
                for($i = 0; $i< $ticketsQuantity; $i++){
                    $ticket = new Ticket($purchase, rand());
                    $this->ticketDaos->add($ticket);
                    $tickets[] = $ticket;
                }

                $message = 'Compra realizada con exito! Te mandamos un mail con tus entradas.';
                $this->generateEmail($purchase, $tickets);
            }catch(\Exception $ex){
                $message = "Ocurrio un error al realizar la compra.";
            }
            
        }
        require_once(VIEWS_PATH . "purchaseForm.php");
        
    }

    //returns json cuz it'll be called from ajax
    public function getStats($idMovie = "all", $idCinema = "all", $idRoom = "all"){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }


        if($idMovie == "all") $idMovie = null;
        if($idCinema == "all") $idCinema = null;
        if($idRoom == "all") $idRoom = null;

        $stats = $this->purchaseDaos->getSoldTickets($idMovie, $idCinema, $idRoom);

        echo json_encode($stats);

    }

    //returns json cuz it'll be called from ajax
    public function getStatsTotal($idMovie = "all", $idCinema = "all",  $dates = "all"){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        if($idMovie == "all") $idMovie = null;
        if($idCinema == "all") $idCinema = null;
        if($dates == "all"){
            $startDate = null;
            $endDate = null;
        }else{
            $startDate = explode("a", $dates)[0];
            $endDate = explode("a", $dates)[1];
        }

        $stats = $this->purchaseDaos->getEarnings($idMovie, $idCinema, $startDate, $endDate);

        echo json_encode($stats); 

    }



    public function stats(){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        require_once(VIEWS_PATH . "stats.php");
    }

    public function statsTickets(){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }

        require_once(VIEWS_PATH . "statsTickets.php");
    }

    public function statsTotal(){

        //check if user is logged and has admin privileges
        if(!isset($_SESSION['user']) || $_SESSION['user']->getIdRol() != 1){
            header("HTTP/1.1 403");           
            return;
        }
        
        require_once(VIEWS_PATH . "statsTotal.php");
    }


    private function generateEmail($purchase, $tickets){

        $emailTemplate = ROOT.'/views/mail/purchase.php';
        $mailer = Mailer::getInstance();

        $movie = $purchase->getShow()->getMovie()->getTitle();
        $email = $purchase->getUser()->getEmail();

        ob_start();
        include($emailTemplate);
        $content = ob_get_contents();

        $mailer->sendEmail($email, "Tu compra en MoviePass", $content);
        ob_end_clean();



    }
}

?>
<?php
namespace controllers;

use models\purchase as Purchase;
use daos\purchaseDaos as PurchaseDaos;
use models\show as Show;
use daos\showDaos as ShowDaos;
use models\ticket as Ticket;
use models\userProfile as Profile;
use daos\userProfileDaos as UserProfileDaos;
use daos\ticketDaos as TicketDaos;

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
        try{
            if(isset($_SESSION['user'])){
                $show = $this->showDaos->getById($id);
                $ticketsSold = $this->purchaseDaos->getSoldTicketsByShow($id);
                $roomCapacity = $show->getRoom()->getCapacity();
                $availableCapacity = $roomCapacity - $ticketsSold;
                $profile = $this->userProfileDaos->getById($_SESSION['user']->getId());

                require_once(VIEWS_PATH . "purchaseForm.php");
            }else{
                require_once(VIEWS_PATH . "login.php");
            }
        }catch(\Exception $err){
            throw $err;
            $err = DATABASE_ERR;
        }
    }

    public function makePurchase($idShow, $ticketsQuantity = null, $cardNumber = null, $cardExp = null, $cardName = null, $cardDni = null, $discount = null){
        if(isset($ticketsQuantity, $cardNumber, $cardExp, $cardName, $cardDni, $discount)){
            $message = null;

            $show = $this->showDaos->getById($idShow);
            $user = $_SESSION['user'];

            date_default_timezone_set ("America/Argentina/Buenos_Aires");
            $date = date("Y-m-d H:i:s");

            $purchase = new Purchase($user, $show, $ticketsQuantity, 0, $date);
            $purchase->setTotal(($show->getRoom()->getPrice())*$ticketsQuantity);

            $this->purchaseDaos->add($purchase);

            $idPurchase = $this->purchaseDaos->getLastInsertId();

            $purchase->setId($idPurchase);
            
            //generate tickets
            for($i = 0; $i< $ticketsQuantity; $i++){
                $ticket = new Ticket($purchase, rand());
                $this->ticketDaos->add($ticket);
            }

            $message = 'Compra realizada con exito!';

            require_once(VIEWS_PATH . "purchaseForm.php");
        }else{
            require_once(VIEWS_PATH . "purchaseForm.php");
        }
    }
}

?>
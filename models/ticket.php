<?php
namespace models;

class Ticket{
    private $purchase;
    private $ticket_number;
    private $qr;

    public function __construct($purchase = '', $ticket_number = '', $qr = ''){
        $this->purchase = $purchase;
        $this->ticket_number = $ticket_number;
        $this->qr = $qr;
    }

    public function getPurchase(){
        return $this->purchase;
    }

    public function setPurchase($purchase){
        $this->purchase = $purchase;
    }

    public function getTicket_number(){
		return $this->ticket_number;
	}

	public function setTicket_number($ticket_number){
		$this->ticket_number = $ticket_number;
	}

	public function getQr(){
		return $this->qr;
	}

	public function setQr($qr){
		$this->qr = $qr;
	}
}
?>